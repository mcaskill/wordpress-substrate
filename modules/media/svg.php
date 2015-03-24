<?php

/**
 * File: SVG Media Extensions
 *
 * Provides support for SVG media.
 *
 * Acknowledgments:
 * - {@link https://gist.github.com/Lewiscowles1986/44f059876ec205dd4d27 Lewis Cowles’ SVG Media Plugin}
 * - {@link https://github.com/jonathantneal/wp-svg-spritemap Jonathan Neal’s SVG Spritemap Manager}
 *
 * @package McAskill\Substrate\Media
 */

namespace McAskill\Substrate\Media\Svg;

use McAskill\Substrate\Support;

/**
 * Filter file type based on additional extension names.
 *
 * @see WordPress\wp_ext2type()
 * @used-by Filter: "wp/ext2type"
 *
 * @param array  $ext2type  Multi-dimensional array with extensions for a default set of file types.
 */
function ext2type( $ext2type = [] )
{
	if ( isset( $ext2type['image'] ) && ! in_array( 'svg', $ext2type['image'] ) ) {
		$ext2type['image'][] = 'svg';
	}

	return $ext2type;
}

add_filter( 'ext2type', __NAMESPACE__ . '\\ext2type' );

/**
 * Filter the list of mime types and file extensions.
 *
 * This filter should be used to add, not remove, mime types. To remove
 * mime types, use the 'upload_mimes' filter.
 *
 * @see WordPress\wp_get_mime_types()
 * @used-by Filter: "wp/mime_types"
 *
 * @param array  $mime_types  Mime types keyed by the file extension regex
 *                            corresponding to those types.
 */
function mime_types( $mime_types = [] )
{
	if ( ! array_key_exists( 'svg', $mime_types ) ) {
		$mime_types['svg'] = 'image/svg+xml';
	}

	return $mime_types;
}

add_filter( 'mime_types', __NAMESPACE__ . '\\mime_types' );

/**
 * Enqueue assets for all admin pages.
 *
 * @uses Substrate\enqueue_asset()
 * @used-by Action: "wp/admin_enqueue_scripts"
 */
function admin_enqueue_assets()
{
	$handle = basename( __FILE__, '.php' );

	Support\enqueue_style( $handle, "assets/styles/media-svg.css" );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_enqueue_assets' );

/**
 * Fires as an admin screen or script is being initialized.
 *
 * Output buffering is initialized and content is captured on shutdown
 * to be filter the media templates to insert SVG support.
 *
 * This @hack will be used until {@link https://core.trac.wordpress.org/ticket/31258 Ticket #31258}
 * is officially applied to WordPress.
 *
 * @used-by Action: "wp/admin_init"
 * @todo Look into using "print_media_templates" action.
 * @todo Look into replacing "shutdown" hook in favor of $output_callback of ob_start();
 */
function capture_media_templates()
{
	ob_start();

	add_action( 'shutdown', function () {
		$output = '';
		$ob_levels = count( ob_get_level() );

		for ( $i = 0; $i < $ob_levels; $i++ ) {
			$output .= ob_get_clean();
		}

		echo apply_filters( 'substrate/shutdown/output', $output );
	}, 0 );

	add_filter( 'substrate/shutdown/output', function ( $output ) {
		$output = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
					<img class="details-image" src="{{ data.url }}" draggable="false" />
				<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			$output
		);

		$output = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
					<div class="centered">
						<img src="{{ data.url }}" class="thumbnail" draggable="false" />
					</div>
				<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			$output
		);

		return $output;
	});
}

add_action( 'admin_init', __NAMESPACE__ . '\\capture_media_templates' );
