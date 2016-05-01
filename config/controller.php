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

$real_content = <<<EOT
<div class="soc">
<p class="subjtitle">About us</p>
<p class="title2">The Cliffside Historical Bunch</p>
<div class="rc-content">
<p><span class="drop">T</span>his outfit ought to have a name, I thought. "This outfit" being I and all the enablers, aiders and abettors who are complicit in the development of this web site. So, I came up with "The Cliffside Historical Bunch." Has a certain dignity to it, don't you think? Yet it doesn't sound so official that someone will go running off to Raleigh or Washington to check our tax status.</p>
<p>I was tempted to call it "The Cliffside Historical Bunch and Marching Band," but that would require members to have certain musical talents; and if you've checked the cost of band uniforms lately, you know they're outrageous. Not to mention that some of us would find marching and playing an instrument, while coping with our walkers and oxygen tanks, a bit much.</p>
<p>Anyway, our mission statement (I wrote it myself) is clear and concise: Remember Cliffside!</p>
<p>A member of CHB, as we'll call it, is anyone who contributes to this website: a photo, a fact, a memory, an article, a suggestion, or any gesture of encouragement or support. Members' names will be listed on the contributors page unless anonymity is desired.</p>
<p>The original material on these pages is for your personal, individual use and enjoyment, and is not to be distributed in any form, for profit or otherwise, without our permission. However, you may share our pages on social media like Facebook or Twitter.</p>
<p>The copyrighted newspaper articles herein are reprinted with permission from the publishers.</p>
<p><strong>— Reno Bailey</strong></p>
<p class="editorsnote"><strong>Update:</strong> As of February, 2006, the loosely-formed Cliffside Historical Bunch became an official organization, chartered as a non-profit corporation by the state of North Carolina as the Cliffside Historical Society.</p>
<p><img class="aligncenter logo size-full wp-image-52614" src="http://dev-remember.com/wp-content/uploads/2015/11/symbol1.gif" alt="" width="46" height="13" /></p>
</div>
</div>
EOT;

return array(
	'dummy_records' => array(
		$real_content,
		'<div class="container"><p class="foo">test</p><p id="bar" class="headline1 someOtherClass" style="display: none;">Here is some content</p><p class="anotherClass">some other text</p></div>',
		'<p class="foo">test</p><p id="bar" class="headline1 someOtherClass" style="display: none;">Here is some content</p>',
		'<div class="foo"><p id="bar" class="headline1 someOtherClass" style="display: none;">Here is some content</p></div>',
		'<div class="foobar">no p tags in here</div>',
	),
);