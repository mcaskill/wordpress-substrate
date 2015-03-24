<?php

/*
 * Plugin Name:  Substrate
 * Plugin URI:   https://github.com/mcaskill/wordpress-substrate
 * Description:  A collection of handy features, fixes, and helpers which make your life with WordPress easier.
 * Version:      0.0.0
 *
 * Author:       Chauncey McAskill
 * Author URI:   https://mcaskill.ca
 *
 * License:      MIT License
 * License URI:  http://opensource.org/licenses/MIT
 *
 * Text Domain:  substrate
 * Domain Path:  /assets/languages
 */

namespace McAskill\Substrate;

require __DIR__ . '/library/ModuleRepository.php';
require __DIR__ . '/library/Module.php';
require __DIR__ . '/library/helpers.php';

$repository = new ModuleRepository(__DIR__);
$repository::setInstance($repository);

add_action( 'after_setup_theme', [ $repository, 'loadModules' ], 100 );
