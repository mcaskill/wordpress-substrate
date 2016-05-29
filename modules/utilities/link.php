<?php

/**
 * File: Link Template Helpers
 *
 * Provides additional functions to be
 * used within the WordPress Loop.
 *
 * @package McAskill\Substrate\Utilities
 */

namespace McAskill\Substrate\Utilities;

/**
 * Advanced retrieval of adjacent post.
 *
 * Can either be next or previous post with taxonomy and meta exceptions.
 *
 * @uses WP_Tax_Query
 * @uses WP_Meta_Query
 * @link http://codex.wordpress.org/Function_Reference/get_adjacent_post
 *
 * @global $wpdb
 *
 * @param array $args {
 *     Optional. Default post navigation arguments. Default empty array.
 *
 *     @type  boolean     $previous     Optional. Whether to retrieve previous post.
 *     @type  string      $key          Database table column or custom field key. Possible values include parameters from 'orderby'. Defaults to “date”.
 *     @type  string      $type         Type of meta. Database table the key belongs to. Possible values include 'post' and 'meta'. Defaults to “post”.
 *     @type  int|object  $post         Post ID or post object. Optional, default is the current post from the loop.
 *     @type  string      $order_by
 *     @type  string      $order_type
 *     @type  integer     $limit        Number of post to retrieve. Default is 1.
 * }
 * @return mixed $result Post object if successful. Null if $post is not set. Empty string if no corresponding post exists.
 */
function get_adjacent_post( $args = [] )
{
	global $wpdb;

	$defaults = [
		'previous'   => true,
		'key'        => null,
		'type'       => 'post',
		'post'       => null,
		'order_by'   => null,
		'order_type' => 'date',
		'limit'      => 1,
	];

	$r = wp_parse_args( $args, $defaults );

	if ( empty( $r['post'] ) ) {
		$post = get_post();
	}
	else {
		$post = get_post( $r['post'] );
	}

	if ( ! $post ) {
		return null;
	}

	$adjacent = ( $r['previous'] ? 'previous' : 'next' );
	$operator = ( $r['previous'] ? '<' : '>' );
	$order    = ( $r['previous'] ? 'DESC' : 'ASC' );

	$op = $operator;

	// Parse key with WP_Query orderby
	if ( empty( $r['key'] ) ) {
		$key = 'p.post_date';
		$value = $post->post_date;
	} else {
		// Used to filter values
		$allowed_keys = [ 'name', 'author', 'date', 'title', 'modified', 'menu_order', 'parent', 'ID', 'comment_count' ];

		$r['key'] = urldecode($r['key']);
		$r['key'] = addslashes_gpc($r['key']);

		if ( 'meta' == $r['type'] )
		{
			$allowed_keys[] = $r['key'];
			$allowed_keys[] = 'meta_value';
			$allowed_keys[] = 'meta_value_num';

			if ( in_array($r['key'], $allowed_keys) ) {
				switch ( $r['key'] ) {
					case $r['key']:
					case 'meta_value':
						$key   = "$wpdb->postmeta.meta_value";
						$value = false;
						break;
					case 'meta_value_num':
						$key   = "$wpdb->postmeta.meta_value+0";
						$value = false;
						break;
				}
			}

		} else {

			if ( in_array($r['key'], $allowed_keys) ) {
				switch ( $r['key'] ) {
					case 'menu_order':
						$key   = 'p.menu_order';
						$value = $post->menu_order;
						break;
					case 'ID':
						$key   = 'p.ID';
						$value = $post->ID;
						break;
					case 'comment_count':
						$key   = 'p.comment_count';
						$value = $post->comment_count;
						break;
					default:
						if ( isset( $post->{'post_' . $r['key']} ) ) {
							$key   = 'p.post_' . $r['key'];
							$value = $post->{'post_' . $r['key']};
						}
				}
			}
		}

		if ( empty( $key ) ) {
			$key   = 'p.post_date';
			$value = $post->post_date;
		}
	}


	$noop_query_obj = null;
	$noop_sql = [ 'join' => '', 'where' => '' ];

	// Parse taxonomy query
	if ( isset( $r['tax_query'] ) ) {
		$tax_query_obj = new WP_Tax_Query( $r['tax_query'] );
		$tax_sql = $tax_query_obj->get_sql( 'p', 'ID' );
	} else {
		$tax_query_obj = $noop_query_obj;
		$tax_sql = $noop_sql;
	}

	// Parse meta query
	if ( isset( $r['meta_query'] ) ) {
		$meta_query_obj = new WP_Meta_Query( $r['meta_query'] );
		$meta_sql = $meta_query_obj->get_sql( 'post', 'p', 'ID' );

	} else {
		$meta_query_obj = $noop_query_obj;
		$meta_sql = $noop_sql;
	}

	$join = apply_filters( "get_{$adjacent}_post_join", $tax_sql['join'] . $meta_sql['join'], $tax_query_obj, $meta_query_obj );

	if ( empty( $r['order_by'] ) ) {
		$orderby = $key;
	}
	else {
		$orderby = $r['order_by'];
	}

	if ( $r['order_type'] ) {
		if ( $orderby !== 'post_date' && $r['order_type'] === 'date') {
			$orderby = "CAST($orderby AS DATE)";
		}
	}

	$where_date = "";
	if($orderby == 'post_date') {
		$where_date .= $wpdb->prepare(" AND post_date $op %s ", $post->post_date);
	}

	if ( $value === false ) {
		$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_type = %s AND p.post_status = 'publish' AND p.ID != %d", $post->post_type, $post->ID) . $where_date . $tax_sql['where'] . $meta_sql['where'], $tax_query_obj, $meta_query_obj );
	}
	else {
		$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE %s $op %s AND p.post_type = %s AND p.post_status = 'publish' AND p.ID != %d", $key, $value, $post->post_type, $post->ID) . $where_date . $tax_sql['where'] . $meta_sql['where'], $tax_query_obj, $meta_query_obj );
	}


	$sort = apply_filters( "get_{$adjacent}_post_sort", $wpdb->prepare("ORDER BY $orderby $order LIMIT %d", $r['limit']) );

	$query = "SELECT p.ID FROM $wpdb->posts AS p $join $where $sort";

	$query_key = 'adjacent_post_' . md5($query);
	$result = wp_cache_get($query_key, 'counts');
	if ( false !== $result ) {
		if ( $result ) {
			$result = get_post( $result );
		}
		return $result;
	}

	$result = $wpdb->get_var( $query );
	if ( null === $result ) {
		$result = '';
	}

	wp_cache_set( $query_key, $result, 'counts' );

	if ( $result ) {
		$result = get_post( $result );
	}

	return $result;
}
