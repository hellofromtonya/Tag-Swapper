<?php
/**
 * Tag Swapper
 *
 * @package         Tag_Swapper
 * @author          hellofromTonya
 * @license         GPL-2.0+
 * @link            https://knowthecode.io
 *
 * @wordpress-plugin
 * Plugin Name:     Tag Swapper
 * Plugin URI:      https://knowthecode.io
 * Description:     HTML Tag Swapper - queries the database, replaces all occurrences of the tag when it's attributes match the specified value, and saves the records back to the database.
 * Version:         1.0.0
 * Author:          hellofromTonya
 * Author URI:      https://knowthecode.io
 * Text Domain:     fulcrum_site
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

use Tag_Swapper\Foundation\Tag_Swapper;

if ( ! function_exists( 'apply_filters' ) ) {
	die( 'Heya, you silly goose. You can\'t call me directly.' );
}

define( 'TAG_SWAPPER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

add_action( 'admin_init', __NAMESPACE__ . '\launch' );
/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	require_once( __DIR__ . '/assets/vendor/autoload.php' );
	
	$swapper = new Manager(
		new Tag_Swapper( include( TAG_SWAPPER_PLUGIN_DIR . '/config/tag-swapper.php' ) ),
		include( TAG_SWAPPER_PLUGIN_DIR . '/config/plugin.php' )
	);

	$swapper->run();
}