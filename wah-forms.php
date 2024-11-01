<?php
/**
 * WAH Forms
 *
 * @package           WAHForms
 * @author            Alex Volkov
 * @copyright         2022 Alex Volkov
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WAH Forms
 * Plugin URI:        https://example.com/plugin-name
 * Description:       Accessible forms by WAH.
 * Version:           1.0
 * Requires at least: 5.8
 * Requires PHP:      5.6
 * Author:            Alex Volkov
 * Author URI:        https://volkov.co.il
 * Text Domain:       wah-forms
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WAHFORMS_VERSION' ) ) {
	define( 'WAHFORMS_VERSION', '1.0' );
}

require_once __DIR__ . '/global.php';

if ( is_admin() ) {
	require_once __DIR__ . '/admin/init.php';
	require_once __DIR__ . '/admin/helpers.php';
	require_once __DIR__ . '/admin/enqueue.php';
	require_once __DIR__ . '/admin/ajax.php';
}

require_once __DIR__ . '/public/hooks.php';
require_once __DIR__ . '/public/init.php';
require_once __DIR__ . '/public/enqueue.php';
require_once __DIR__ . '/public/ajax.php';
