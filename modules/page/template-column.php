<?php

/**
 * File: Custom Page Template Column
 *
 * Adds a custom column to the list table for Pages. The column displays
 * the Page's custom template, if there is one. The column will also
 * display a notice if the Page is used as a static page.
 */

namespace McAskill\Substrate\Page\TemplateColumn;

use McAskill\Substrate\Support;

use InvalidArgumentException;
use WP_Post;

if ( ! is_admin() ) {
	return;
}

/**
 * Register the "Page Template" post column.
 *
 * @used-by Filter: wp/manage_pages_columns"
 *
 * @param  array  $post_columns  An array of column names.
 * @return array
 */
function add_template_column( $post_columns )
{
	$columns = [
		'template' => __( 'Template' ) /* __( 'Page Template' ) */
	];

	if ( function_exists( 'array_insert' ) ) {
		$post_columns = array_insert( $post_columns, $columns, 'title' );
	} else {
		$post_columns = array_merge( $post_columns, $columns );
	}

	/**
	 * Fires when the page template column is added.
	 */
	do_action( 'substrate/page/template_column/column_added' );

	return $post_columns;
}

add_filter( 'manage_pages_columns', __NAMESPACE__ . '\\add_template_column' );

/**
 * Display cell value for Page Template
 *
 * @used-by Action: "wp/manage_pages_custom_column"
 *
 * @param string   $column_name  The name of the column to display.
 * @param integer  $post_id      The current post ID.
 */
function manage_template_cell( $column_name, $post_id )
{
	if ( 'template' === $column_name ) {
		$template_name = get_page_template_name( $post_id );
		$template_path = get_post_meta( $post_id, '_wp_page_template', true );

		if ( ! $template_name ) {
			$template_name = sprintf( '<span aria-hidden="true">—</span><span class="screen-reader-text">%s</span>',
				__( 'Default Template' )
			);
		}

		/**
		 * Filter the page template cell value of the page.
		 *
		 * @param  string   $output    The value of the cell to display.
		 * @param  string   $template  The current post's assigned page template.
		 * @param  integer  $post_id   The current post ID.
		 * @return string
		 */
		echo apply_filters( 'substrate/page/template_column/value', $template_name, $template_path, $post_id );
	}
}

add_action( 'manage_pages_custom_column', __NAMESPACE__ . '\\manage_template_cell', 10, 2 );

/**
 * Register Page Template column as sortable
 *
 * @used-by Filter: "wp/manage_edit-page_sortable_columns"
 *
 * @param    array  $sortable_columns  An array of sortable columns.
 * @return   array  $sortable_columns
 */

function register_sortable_template_column( $sortable_columns )
{
	$sortable_columns['template'] = 'template';

	return $sortable_columns;
}

add_filter( 'manage_edit-page_sortable_columns', __NAMESPACE__ . '\\register_sortable_template_column' );


/**
 * Sort Pages by the Page Template column
 *
 * @used-by Filter: "wp/request"
 *
 * @param    array  $query_vars  The array of requested query variables.
 * @return   array  $query_vars
 */

function order_template_column( $query_vars )
{
	$vars = [];

	if ( isset( $query_vars['orderby'] ) && $query_vars['orderby'] === 'template' ) {
		$vars = [
			  'meta_key' => '_wp_page_template'
			, 'orderby'  => 'meta_value'
		];
	}

	return array_merge( $query_vars, $vars );
}

add_filter( 'request', __NAMESPACE__ . '\\order_template_column' );

/**
 * Enqueue assets for all admin pages.
 *
 * @used-by Action: "wp/admin_enqueue_scripts"
 */

function admin_enqueue_assets()
{
	$handle = basename( __FILE__, '.php' );

	Support\enqueue_style( $handle, 'assets/styles/page-template-column.css' );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_enqueue_assets' );

/**
 * Retrieve the page template name for the given post ID or post object..
 *
 * @param  WP_Post|integer  $post  Post ID or post object.
 * @return string|null      The name of the page template.
 *
 * @throws InvalidArgumentException If the post is invalid.
 * @throws Exception                If the template name was not found.
 */
function get_page_template_name( $post )
{
	$post = get_post($post);

	if ( ! $post ) {
		throw new InvalidArgumentException('Invalid post. Must be a post ID or an instance of WP_Post.');
	}

	$current_template = $post->page_template;

	if ( 'default' === $current_template ) {
		return null;
	}

	/**
	 * Filter the collection of Page Templates available in this theme.
	 *
	 * @param  string[]  $templates  Key is the template name, value is the filename of the template.
	 * @param  string    $template   The current post's assigned page template.
	 * @param  integer   $post_id    The current post ID.
	 * @return array
	 */
	$page_templates = apply_filters(
		'substrate/page/template_column/theme_page_templates',
		get_page_templates(),
		$current_template,
		$post->ID
	);

	ksort( $page_templates );
	foreach ( $page_templates as $template_name => $template_path ) {
		if ( $current_template === $template_path ) {
			return $template_name;
		}
	}

	return null;
}
