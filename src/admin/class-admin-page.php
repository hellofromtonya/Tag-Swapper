<?php
/**
 * Admin Page Handler - which is responsible for rendering out the Tools > Tag Swapper admin page.
 *
 * @package     Tag_Swapper\Admin
 * @since       1.0.2
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Tag_Swapper\Admin;

use Tag_Swapper\Controller;

class Admin_Page {

	/**
	 * Configuration array
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Processed settings values
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Records Count
	 *
	 * @var int
	 */
	protected $records_count = 0;

	/**
	 * Menu ID
	 *
	 * @var int
	 */
	protected $menu_id;

	/**
	 * Flag indicated that the tag swap or count process is complete.
	 *
	 * @var bool
	 */
	protected $process_is_complete = false;

	/**
	 * Internal process start time
	 *
	 * @var int
	 */
	protected $process_start_time = 0;

	/**
	 * Total processing time in seconds
	 *
	 * @var int
	 */
	public $processing_time_in_seconds = 0;

	/**
	 * Number of Tags Swapped
	 *
	 * @var int
	 */
	protected $number_tag_swaps = 0;

	/**
	 * Form validation error
	 *
	 * @var bool
	 */
	protected $validation_error = false;

	/*******************
	 * Setters
	 ******************/

	/**
	 * Sets the number of processed counts.
	 *
	 * @since 1.0.0
	 *
	 * @param int $count
	 *
	 * @return void
	 */
	public function setProcessedCount( $count ) {
		$this->records_count = $count;
	}

	/**
	 * Sets the state of Process is Complete flag
	 *
	 * @since 1.0.0
	 *
	 * @param bool $state
	 *
	 * @return void
	 */
	public function setProcessIsComplete( $state, $number_tag_swaps = 0 ) {
		$this->process_is_complete = $state;
		$this->number_tag_swaps    = $number_tag_swaps;

		if ( $state ) {
			$this->processing_time_in_seconds = microtime( true ) - $this->process_start_time;
		} else {
			$this->process_start_time         = microtime( true );
			$this->processing_time_in_seconds = 0;
		}
	}

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the object
	 *
	 * @since 1.0.3
	 *
	 * @param array $config Runtime configuration parameters
	 * @param array $selected_settings Form settings
	 * @param bool $is_processing_submit Flag if the form was submitted.
	 */
	public function __construct( array $config, array $selected_settings, $is_processing_submit ) {
		$this->config           = $config;
		$this->current_values   = $selected_settings;
		$this->validation_error = $is_processing_submit && ! $selected_settings['attribute_value'];

		$this->init_events();
	}

	/**
	 * Initialize the events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add the menu page to the Tools Menu
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_submenu_page() {
		$this->menu_id = add_submenu_page(
			'tools.php',
			__( 'Tag Swapper', 'tag_swapper' ),
			__( 'Tag Swapper', 'tag_swapper' ),
			'manage_options',
			'tag_swapper',
			array( $this, 'render_menu_page' )
		);
	}

	/**
	 * Enqueue assets
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Current page
	 *
	 * @return null
	 */
	public function enqueue_assets( $hook ) {
		if ( $this->menu_id !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'tag_swapper_css',
			TAG_SWAPPER_PLUGIN_URL . 'assets/css/tag-swapper.css',
			array(),
			Controller::VERSION
		);
	}

	/**
	 * Render the Utility Page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_menu_page() {
		$view = __DIR__ . $this->config['view'];

		if ( ! is_readable( $view ) ) {
			return;
		}

		$message_class         = $this->records_count < 1 ? ' tag-swapper-no-records-processed' : '';
		$attribute_value_class = $this->validation_error ? ' class="validation-error"' : '';

		require( $view );
	}
}