<?php

/**
 * File: Translation Helpers
 *
 * Provides additional functions for working
 * with localization, internationalization,
 * and multilingual installations.
 *
 * @package McAskill\Substrate\Utilities
 */

namespace McAskill\Substrate\Utilities;

/**
 * Get the language from the current, or passed, locale
 *
 * @uses    WordPress\get_locale()
 * @param   string|null  $locale
 * @return  string
 */

function get_locale_language( $locale = null )
{
	if ( ! $locale ) {
		$locale = get_locale();
	}

	return substr( $locale, 0, 2 );
}

/**
 * Get the territory from the current, or passed, locale
 *
 * @uses    WordPress\get_locale()
 * @param   string|null  $locale
 * @return  string
 */

function get_locale_territory( $locale = null )
{
	if ( ! $locale ) {
		$locale = get_locale();
	}

	return substr( $locale, 2, 2 );
}
