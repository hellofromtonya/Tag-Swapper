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

use Tag_Swapper\Foundation\Tag_Swapper;

class Manager {

	protected $config = array();

	/**
	 * Instance of the tag swapper
	 *
	 * @var Foundation\Tag_Swapper
	 */
	protected $tag_swapper;

	public function __construct( Tag_Swapper $tag_swapper, array $config ) {
		$this->config      = $config;
		$this->tag_swapper = $tag_swapper;
	}
	
	public function run() {
		$records = $this->fetch_records();

		foreach( $records as $record ) {
			d( $record );
			$new_html = $this->swap( $record );
			
			d( $new_html );
		}

		die();
	}

	protected function fetch_records() {
		return $this->config['dummy_records'];
	}
	
	protected function swap( $html ) {
		return $this->tag_swapper->swap( $html );
	}
}