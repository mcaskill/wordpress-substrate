<?php

/**
 * File: Formatting Helpers
 *
 * Provides additional functions and
 * filters for formatting output.
 *
 * @package McAskill\Substrate\Utilities
 */

namespace McAskill\Substrate\Utilities;

/**
 * Filter zero-width characters from a string.
 *
 * The set of special characters was based on {@link https://gitorious.org/mediawiki/mediawiki-trunk-phase3/source/includes/Sanitizer.php#L1677}.
 *
 * @link http://tools.ietf.org/html/3454#section-3.1 Characters that will be ignored in IDNs.
 * @used-by Filter: "wp/sanitize_title"
 *
 * @param string  $text      Sanitized string.
 * @param string  $raw_text  Unused. The string prior to sanitization.
 * @param string  $context   Unused. The context for which the string is being sanitized.
 */
function sanitize_zero_chars( $text )
{
	$strip = "/
		\\s|          # General whitespace
		\xc2\xad|     # 00ad SOFT HYPHEN
		\xe1\xa0\x86| # 1806 MONGOLIAN TODO SOFT HYPHEN
		\xe2\x80\x8b| # 200b ZERO WIDTH SPACE
		\xe2\x81\xa0| # 2060 WORD JOINER
		\xef\xbb\xbf| # feff ZERO WIDTH NO-BREAK SPACE
		\xcd\x8f|     # 034f COMBINING GRAPHEME JOINER
		\xe1\xa0\x8b| # 180b MONGOLIAN FREE VARIATION SELECTOR ONE
		\xe1\xa0\x8c| # 180c MONGOLIAN FREE VARIATION SELECTOR TWO
		\xe1\xa0\x8d| # 180d MONGOLIAN FREE VARIATION SELECTOR THREE
		\xe2\x80\x8c| # 200c ZERO WIDTH NON-JOINER
		\xe2\x80\x8d| # 200d ZERO WIDTH JOINER
		[\xef\xb8\x80-\xef\xb8\x8f] # fe00-fe0f VARIATION SELECTOR-1-16
		/xuD";

	$text = preg_replace( $strip, '', $text );

	return $text;
}

add_filter( 'sanitize_title', __NAMESPACE__ . '\\sanitize_zero_chars' );
