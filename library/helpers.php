<?php

namespace McAskill\Substrate\Support;

/**
 * Action: Enqueue a Substrate module's CSS stylesheet.
 *
 * Registers the style if source provided and enqueues using {@see WordPress\wp_enqueue_style()}.
 *
 * @param string          $handle  Name of the stylesheet.
 * @param string|boolean  $src     Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array           $deps    An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|boolean  $ver     String specifying the stylesheet version number, if it has one. This parameter is used
 *                                 to ensure that the correct version is sent to the client regardless of caching, and so
 *                                 should be included if a version number is available and makes sense for the stylesheet.
 * @param string          $media   Optional. The media for which this stylesheet has been defined.
 *                                 Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                                 'screen', 'tty', or 'tv'.
 */
function enqueue_style( $handle, $src, $deps = [], $ver = false, $media = 'all' )
{
	global $wp_styles;

	$_handle = explode( '?', $handle );

	$suffix = ( SCRIPT_DEBUG ? '' : '.min' );

	$dir_abs  = wp_normalize_path( trailingslashit( dirname( __DIR__ ) ) );
	$dir_rel  = str_replace( dirname( ABSPATH ), '', str_replace( ABSPATH, '', $dir_abs ) );

	$file_abs = $dir_abs . $src;
	$file_rel = $dir_rel . $src;
	$file_uri = plugin_dir_url( dirname( __DIR__ ) ) . $src;

	# if ( ! in_array( $dir_rel, $wp_styles->default_dirs ) ) {
	#	$wp_styles->default_dirs[] = $dir_rel;
	# }

	if ( is_readable( $file_abs ) ) {
		# $wp_styles->add( "substrate-{$_handle[0]}", $src, $deps, $ver, $media );
		# wp_enqueue_style( "substrate-{$_handle[0]}", $file_rel, $deps, $ver, $media );
		wp_add_inline_style( 'wp-admin', trim( file_get_contents( $file_abs ) ) );
	}
}
