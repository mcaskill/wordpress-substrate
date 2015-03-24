<?php

/**
 * File: Outdated Navigator Notice
 *
 * Adds a customizable notification into the {@see "get_header"} action.
 *
 * @package McAskill\Substrate\UpgradeNavigator
 */

namespace McAskill\Substrate\UpgradeNavigator;

if ( is_admin() ) {
	return;
}

/**
 * Action: Display the notification.
 *
 * By default, the notice will only display for visitors using a version of
 * Microsoft Internet Explorer inferior to 9; this can be customized through
 * filters to alter the version or even be downlevel-free.
 *
 * @used-by Action: "wp/get_header"
 *
 * @param string  $header  Name of the specific header file to use.
 */
function notice( $header )
{
	$conditional = [
		'statement' => 'lt IE 9',
		'downlevel' => 'hidden'
	];

	$destination = add_query_arg( [ 'locale' => substr( get_locale(), 0, 2 ) ], 'https://browsehappy.com/' );

	/**
	 * Filter the conditional comment to wrap around the notice.
	 *
	 * Returning an empty value for $conditional will preclude any conditional
	 * comments and reveal the notice to all browsers.
	 *
	 * @param array|null  $conditional {
	 *     An array representing the opening and closing conditional comments.
	 *
	 *     @type  string          $statement  The conditional statement {@link http://www.sitepoint.com/web-foundations/internet-explorer-conditional-comments/}.
	 *     @type  string|boolean  $downlevel  Either "reveal" (false) or "hidden" (true). By default, "hidden". Using "reveal" will append a hack, `<!-->`,
	 *                                        to the opening comment to reveal the content inside the statement to browsers that don't
	 *                                        support conditional comments.
	 * }
	 * @param string  $header  Name of the specific header file to use.
	 */
	$conditional = apply_filters( 'substrate/outdated_navigator/conditional_statement', $conditional, $header );

	/**
	 * Filter the destination.
	 *
	 * Returning an empty value for $conditional will preclude any conditional
	 * comments and reveal the notice to all browsers.
	 *
	 * @param string      $destination  The suggested URL about upgrading one's navigator.
	 * @param array|null  $conditional  The conditional comment.
	 * @param string      $header       Name of the specific header file to use.
	 */
	$destination = apply_filters( 'substrate/outdated_navigator/link', $destination, $conditional, $header );

	/**
	 * Filter the message.
	 *
	 * Returning an empty value for $conditional will preclude any conditional
	 * comments and reveal the notice to all browsers.
	 *
	 * @param string      $message      The notification about their predicament.
	 * @param array|null  $conditional  The conditional comment.
	 * @param string      $header       Name of the specific header file to use.
	 */
	$message = apply_filters(
		'substrate/outdated_navigator/message',
		sprintf(
			__( 'You are using an <strong>outdated</strong> browser.', 'substrate' ) . ' ' .
			__( 'Please <a href="%s">upgrade your browser</a> to improve your experience.', 'substrate' ),
			esc_url( $destination )
		),
		$conditional,
		$header
	);

	/**
	 * Filter the CSS classes applied to the notice's HTML container.
	 *
	 * @param string      $message      The notification about their predicament.
	 * @param array|null  $conditional  The conditional comment.
	 * @param string      $header       Name of the specific header file to use.
	 */
	$classes = apply_filters( 'substrate/outdated_navigator/classes', [ 'alert', 'alert-warning' ], $conditional, $header );

	if ( ! empty( $classes ) ) {
		$classes = ' class="' . ( is_array( $classes ) ? implode( ' ', $classes ) : (string) $classes ) . '"';
	}

	if ( empty( $message ) ) {
		return;
	}

	if ( ! empty( $conditional['statement'] ) && isset( $conditional['downlevel'] ) ) {
		echo '<!--[if ' . $conditional['statement'] . ']>';
		if ( 'reveal' === $conditional['downlevel'] || ! $conditional['downlevel'] ) {
			echo '<!-->';
		}
		echo "\n";
	}

		?>
		<div<?php echo $classes; ?> role="alert"><?php
			echo $message;
		?></div>
		<?php

	if ( ! empty( $conditional['statement'] ) && ! empty( $conditional['downlevel'] ) ) {
		echo ( 'reveal' === $conditional['downlevel'] || ! $conditional['downlevel'] ? '<!--<![endif]-->' : '<![endif]-->' ) . "\n";
	}
}

add_action( 'get_header', __NAMESPACE__ . '\\notice', 5 );
