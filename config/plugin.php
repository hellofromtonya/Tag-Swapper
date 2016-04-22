<?php
/**
 * Description
 *
 * @package     Tag_Swapper
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Tag_Swapper;

return array(
	'dummy_records' => array(
		'<div class="container"><p class="foo">test</p><p id="bar" class="headline1 someOtherClass" style="display: none;">Here is some content</p><p class="anotherClass">some other text</p></div>',
		'<p class="foo">test</p><p id="bar" class="headline1 someOtherClass" style="display: none;">Here is some content</p>',
		'<div class="foo"><p id="bar" class="headline1 someOtherClass" style="display: none;">Here is some content</p></div>',
		'<div class="foobar">no p tags in here</div>',
	),
);