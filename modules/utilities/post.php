<?php

/**
 * File: Post Template Helpers
 *
 * Provides additional functions to retrieve
 * content for the current post in the Loop.
 *
 * @package McAskill\Substrate\Utilities
 */

namespace McAskill\Substrate\Utilities;

/**
 * Retrieve the current post title for a URL.
 *
 * It is encoded the same way that the posted data from a WWW form is encoded,
 * that is the same way as in `application/x-www-form-urlencoded` media type
 * (in that for historical reasons, spaces are encoded as plus (+) signs).
 *
 * @see urlencode()
 *
 * @return string  Current post title.
 */
function get_the_title_url()
{
	$title = get_the_title();

	/**
	 * Filter the post title for use in a URL.
	 *
	 * @param string  $title  The current post title.
	 */
	return apply_filters( 'the_title_url', $title );
}

/**
 * Display the post title for a URL.
 */
function the_title_url()
{
	echo get_the_title_url();
}

// URL filters
add_filter( 'the_title_url', 'html_entity_decode' );
add_filter( 'the_title_url', 'strip_tags'         );
add_filter( 'the_title_url', 'urlencode'          );

/**
 * Retrieve the current post title for a URL according to RFC 3986.
 *
 * This is the encoding described in {@link http://www.faqs.org/rfcs/rfc3986 RFC 3986}
 * for protecting literal characters from being interpreted as special URL delimiters,
 * and for protecting URLs from being mangled by transmission media with
 * character conversions (like some email systems).
 *
 * @see rawurlencode()
 *
 * @return string  Current post title.
 */
function get_the_title_url_raw()
{
	$title = get_the_title();

	/**
	 * Filter the post title for use in a URL.
	 *
	 * @param string  $title  The current post title.
	 */
	return apply_filters( 'the_title_url_raw', $title );
}

/**
 * Display the post title for a URL according to RFC 3986.
 */
function the_title_url_raw()
{
	echo get_the_title_url_raw();
}

// Raw URL filters
add_filter( 'the_title_url_raw', 'html_entity_decode' );
add_filter( 'the_title_url_raw', 'strip_tags'         );
add_filter( 'the_title_url_raw', 'rawurlencode'       );
