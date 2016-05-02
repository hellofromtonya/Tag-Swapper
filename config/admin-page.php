<?php
/**
 * Admin Page runtime configuration parameters
 *
 * @package     Tag_Swapper\Admin
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Tag_Swapper\Admin;

return array(
	'view' => '/views/menu-page.php',

	'tags'           => array(
		'p',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'div',
		'section',
		'main',
		'article',
		'aside',
		'main',
		'header',
		'footer',
	),
	'yes_no_labels'   => array(
		__( 'No', 'tag_swapper' ),
		__( 'Yes', 'tag_swapper' ),
	),
);