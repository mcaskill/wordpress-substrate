<?php

/**
 * File: Media Helpers
 *
 * Provides additional functions for working
 * with attachments, mediums, and image sizes.
 *
 * @package McAskill\Substrate\Utilities
 */

namespace McAskill\Substrate\Utilities;

/**
 * Retrieve the image sizes or the the requested size, if available.
 *
 * @uses get_intermediate_image_sizes()
 * @link https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
 *
 * @global $_wp_additional_image_sizes
 *
 * @param  string|array  $sizes  Optional, image size or list of sizes. Default is to return all sizes.
 * @return array         Returns an array of image size data
 */
function get_image_sizes( $sizes = [] )
{
	global $_wp_additional_image_sizes;

	$single_size = ( ! is_array( $sizes ) || 1 === count( $sizes ) );

	if ( ! is_array( $sizes ) ) {
		if ( ! empty( $sizes ) ) {
			$sizes = [ $sizes ];
		}
		else {
			$sizes = [];
		}
	}

	$image_sizes = [];
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	// Create the full array with sizes and crop info
	foreach( $get_intermediate_image_sizes as $_size ) {
		if ( in_array( $_size, [ 'thumbnail', 'medium', 'large' ] ) ) {
			$image_sizes[ $_size ] = [
				'width'  => get_option( $_size . '_size_w' ),
				'height' => get_option( $_size . '_size_h' ),
				'crop'   => (bool) get_option( $_size . '_crop' )
			];
		}
		elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$image_sizes[ $_size ] = [
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop']
			];
		}
	}

	if ( $single_size ) {
		if ( isset( $image_sizes[ reset( $sizes ) ] ) ) {
			return $image_sizes[ reset( $sizes ) ];
		} else {
			return false;
		}
	}
	else if ( count( $sizes ) ) {
		return array_filter(
			$image_sizes,
			function ( $_size ) use ( $sizes ) {
				return in_array( $_size, $sizes );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	return $image_sizes;
}

/**
 * Retrieve the image sizes or the the requested size, if available.
 *
 * Alias of {@uses get_image_sizes()}.
 *
 * @param  string  $size  Required, image size. If an array is passed, the first element is used.
 * @return array   Returns an array of image size data
 */
function get_image_size( $size )
{
	if ( is_array( $size ) ) {
		$size = reset( $size );
	}

	return get_image_sizes( [ $size ] );
}
