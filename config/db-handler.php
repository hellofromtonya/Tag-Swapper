<?php
/**
 * Db Handler runtime configuration parameters
 *
 * @package     Tag_Swapper\Foundation
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Tag_Swapper\Foundation;

$config = $_POST['tag_swapper'];

$config['security_nonce'] = '_tag_swapper';

return $config;