<?php
/**
 * Database Handler - handles all interaction with the database.
 *
 * @package     Tag_Swapper\Foundation
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Tag_Swapper\Foundation;

if ( ! function_exists( 'apply_filters' ) ) {
	die( 'Heya, you silly goose. You can\'t call me directly.' );
}

class DB_Handler {

	/**
	 * Runtime configuration parameters
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Post type for this swap
	 *
	 * @var string
	 */
	protected $post_type = 'post';

	/**
	 * The accumulated number of records that were updated.
	 *
	 * @var int
	 */
	protected $record_count = 0;

	/**
	 * Array of UPDATE SQL
	 *
	 * @var array
	 */
	protected $update_sql = array();

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the Database Handler object
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Runtime configuration parameters
	 */
	public function __construct( array $config ) {
		$this->config         = $config;
		$this->post_type      = isset( $config['post_type'] ) ? $this->config['post_type'] : 'post';
	}

	/**************************
	 * Workers
	 *************************/

	/**
	 * Get a copy of the number of records.
	 *
	 * @since 1.0.0
	 *
	 * @return int|null
	 */
	public function getCount() {
		global $wpdb;

		$sql_query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type == %s", $this->post_type );

		$this->record_count = $wpdb->get_var( $sql_query );

		return $this->record_count;
	}

	/**
	 * Get a count of all of the processed (i.e. meaning these records had tags swapped) records.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function getProcessedCount() {
		if ( ! $this->update_sql ) {
			return 0;
		}

		if ( ! array_key_exists( 'ids', $this->update_sql ) ) {
			return 0;
		}
		
		return count( $this->update_sql['ids'] );
	}

	/**
	 * Fetch records from the database that have content and are of the right post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getRecords() {
		global $wpdb;

		$sql_query = $wpdb->prepare( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_content <> '' AND post_type IN ( %s )", $this->post_type );

		$results = $wpdb->get_results( $sql_query );

		return $results ?: array();
	}

	/**
	 * Set the update, meaning the newly updated records are stored and the update SQL is incrementally built up.
	 * We do this to save time and prepare for the SQL query to update all of the records in one database hit.
	 *
	 * @since 1.0.0
	 *
	 * @param int $record_id Record ID
	 * @param string $content The record's updated content
	 *
	 * @return void
	 */
	public function setUpdate( $record_id, $content ) {
		global $wpdb;

		$this->update_sql['set_case'][] = $wpdb->prepare( 'WHEN ID = %d THEN %s', $record_id, $content );
		$this->update_sql['ids'][]      = $record_id;
	}

	/**
	 * It's time to update all of the updated records.
	 *
	 * @since 1.0.0
	 *
	 * @return false|int|void
	 */
	public function updateRecords() {
		if ( ! $this->update_sql ) {
			return;
		}

		global $wpdb;

		$sql_query = "UPDATE {$wpdb->posts} SET post_content = ( CASE ";
		$sql_query .= join( ' ', $this->update_sql['set_case'] );
		$sql_query .= " END ) WHERE ID IN ( ";
		$sql_query .= join( ', ', $this->update_sql['ids'] );
		$sql_query .= " );";

		return $wpdb->query( $sql_query );
	}
}
