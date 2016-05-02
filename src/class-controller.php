<?php
/**
 * Tag Swapper Controller
 *
 * @package     Tag_Swapper
 * @since       1.0.1
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Tag_Swapper;

use Tag_Swapper\Admin\Admin_Page;
use Tag_Swapper\Foundation\DB_Handler;
use Tag_Swapper\Foundation\Tag_Swapper;

class Controller {

	/**
	 * The plugin's version
	 *
	 * @var string
	 */
	const VERSION = '1.0.1';

	/**
	 * The plugin's minimum WordPress requirement
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '3.5';

	/**
	 * Configuration parameters
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Instance of the tag swapper
	 *
	 * @var Foundation\Tag_Swapper
	 */
	protected $swapper;

	/**
	 * Instance of the Admin Page
	 *
	 * @var Admin\Admin_Page
	 */
	protected $admin_page;

	/**
	 * Instance of the DB Handler
	 *
	 * @var Foundation\DB_Handler
	 */
	protected $db_handler;

	/*************************
	 * Getters
	 ************************/

	public function version() {
		return self::VERSION;
	}

	public function min_wp_version() {
		return self::MIN_WP_VERSION;
	}

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the plugin
	 *
	 * @since 1.0.0
	 *
	 * @param Foundation\Tag_Swapper $swapper
	 * @param Admin\Admin_Page $admin_Page
	 * @param array $config Runtime configuration parameters
	 */
	public function __construct( Tag_Swapper $swapper, Admin_Page $admin_Page, array $config ) {
		$this->config     = $config;
		$this->admin_page = $admin_Page;
		$this->swapper    = $swapper;
	}

	/**
	 * Set the Db Handler - this is separated out, as we only need it when handling the swapping.
	 *
	 * @since 1.0.0
	 *
	 * @param Foundation\DB_Handler $db_handler
	 *
	 * @return void
	 */
	public function setDbHandler( DB_Handler $db_handler ) {
		$this->db_handler = $db_handler;
	}

	/**
	 * Time to run the tag swapper.  There are two processes:
	 *
	 *   1. Tag swap which updates the records
	 *   2. Count only - no records are updated
	 *
	 * For the count only, we have to process the records in
	 * order to get a count.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function run() {
		$this->admin_page->setProcessIsComplete( false );

		$records = $this->fetch_records();
		$this->swap( $records );

		$count = $this->db_handler->getProcessedCount();
		$this->admin_page->setProcessedCount( $count );

		if ( ! $this->is_count_process() ) {
			$this->update_records();
		}

		$this->admin_page->setProcessIsComplete( true, $this->swapper->number_of_swaps );
	}

	/**
	 * Flag determines if this is a counting process only.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_count_process() {
		return $this->config['count_records'] === true;
	}

	/**
	 * Fetch the records from the database.
	 *
	 * @since 1.0.0
	 *
	 * @return array Returns an array with the `ID` and `post_content` columns
	 */
	protected function fetch_records() {
		return $this->db_handler->getRecords();
	}

	/**
	 * Update the records (updates the `post_content` column).
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function update_records() {
		return $this->db_handler->updateRecords();
	}

	/**
	 * Set the record for the update.
	 *
	 * @since 1.0.0
	 *
	 * @param int $record_id
	 * @param string $content
	 *
	 * @return void
	 */
	protected function set_record( $record_id, $content ) {
		if ( $content ) {
			$this->db_handler->setUpdate( $record_id, $content );
		}
	}

	/**
	 * Process the swap.
	 *
	 * @since 1.0.0
	 *
	 * @param array $records
	 *
	 * @return array
	 */
	protected function swap( array $records ) {
		$updated_records = array();

		foreach ( $records as $record ) {
			$content = $this->swapper->swap( $record->post_content, $record->ID );

			$this->set_record( $record->ID, $content );
		}

		return $updated_records;
	}
}
