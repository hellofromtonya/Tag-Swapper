<?php
/**
 * HTML Tag Swapper
 *
 * @package         Tag_Swapper
 * @author          hellofromTonya
 * @license         GPL-2.0+
 * @link            https://knowthecode.io
 *
 * @wordpress-plugin
 * Plugin Name:     HTML Tag Swapper
 * Plugin URI:      https://knowthecode.io
 * Description:     HTML Tag Swapper - queries the database, replaces all occurrences of the tag when it's attributes match the specified value, and saves the records back to the database.
 * Version:         1.0.1
 * Author:          hellofromTonya
 * Author URI:      https://knowthecode.io
 * Text Domain:     tag_swapper
 * Requires WP:     3.5
 * Requires PHP:    5.4
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace Tag_Swapper;

use Tag_Swapper\Admin\Admin_Page;
use Tag_Swapper\Foundation\DB_Handler;
use Tag_Swapper\Foundation\Tag_Swapper;

if ( ! function_exists( 'apply_filters' ) ) {
	die( 'Heya, you silly goose. You can\'t call me directly.' );
}

define( 'TAG_SWAPPER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TAG_SWAPPER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	require_once( __DIR__ . '/assets/vendor/autoload.php' );

	$selected_settings = merge_settings_with_defaults();

	$admin_page = new Admin_Page(
		include( TAG_SWAPPER_PLUGIN_DIR . 'config/admin-page.php' ),
		$selected_settings
	);

	if ( is_processing_submit() ) {
		launch_swapper( $selected_settings, $admin_page );
	}
}

/**
 * Launch the swapper processing.
 *
 * @since 1.0.0
 *
 * @param array $selected_settings
 * @param Admin_Page $admin_page
 *
 * @return void
 */
function launch_swapper( array $selected_settings, Admin_Page $admin_page ) {
	@set_time_limit( 60 * 10 );
	@ini_set( 'memory_limit', '1024M' );

	$config  = wp_parse_args( include( TAG_SWAPPER_PLUGIN_DIR . 'config/controller.php' ), $selected_settings );
	$swapper = new Controller(
		new Tag_Swapper( $selected_settings ),
		$admin_page,
		$config
	);

	$swapper->setDbHandler( new DB_Handler( include( TAG_SWAPPER_PLUGIN_DIR . 'config/db-handler.php' ) ) );
	$swapper->run();
}

/**
 * Merge the submitted settings with the defaults (keep it DRY).
 *
 * @since 1.0.0
 *
 * @return array
 */
function merge_settings_with_defaults() {
	$defaults = include( TAG_SWAPPER_PLUGIN_DIR . 'config/defaults.php' );

	if ( ! is_processing_submit() ) {
		return $defaults;
	}

	$settings                    = wp_parse_args( $_POST['tag_swapper'], $defaults );
	$settings['count_records']   = (bool) $settings['count_records'];
	$settings['suppress_errors'] = (bool) $settings['suppress_errors'];

	return $settings;
}

/**
 * Checks to see if this is a submitted tag swap, meaning it's time to process.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function is_processing_submit() {
	static $is_processing = false;

	if ( ! $is_processing ) {
		$is_processing = array_key_exists( 'tag_swapper', $_POST );
	}

	return $is_processing;
}

if ( is_admin() ) {
	launch();
}