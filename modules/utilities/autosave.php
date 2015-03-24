<?php

namespace McAskill\Substrate\Utilities;

/**
 * Disable revision auto-saving.
 *
 * Out of the box, WordPress doesn't allow you to disable the feature.
 * You can only delay it by increasing the delay. The default is 60 seconds.
 *
 * With Substrate, you can disable it by setting the value of the
 * {@see AUTOSAVE_INTERVAL} constant to `false`. If `false`, the 'autosave'
 * script is dequeued and unregistered.
 *
 * Unregistering it will prevent third-parties from attempting to enqueue it.
 * This will also prevent all scripts dependant on 'autosave' from enqueueing.
 *
 * @used-by Action: "wp/admin_enqueue_scripts"
 * @link https://codex.wordpress.org/Editing_wp-config.php#Modify_AutoSave_Interval
 */
function remove_autosave_script()
{
	wp_dequeue_script('autosave');
	wp_deregister_script('autosave');
}

if ( defined('AUTOSAVE_INTERVAL') && false === AUTOSAVE_INTERVAL ) {
	add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\remove_autosave_script');
}
