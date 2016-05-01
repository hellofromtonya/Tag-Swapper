<?php
/**
 * Description
 *
 * @package     Tag_Swapper\Foundation
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Tag_Swapper\Foundation;


class DB_Handler {

	protected $config = array();

	protected $post_type = 'post';

	protected $record_count = 0;

	protected $update_sql = array();

	public function __construct( array $config ) {
		$this->config         = $config;
		$this->post_type      = isset( $config['post_type'] ) ? $this->config['post_type'] : 'post';
	}

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

	public function getProcessedCount() {
		if ( ! $this->update_sql ) {
			return 0;
		}

		if ( ! array_key_exists( 'ids', $this->update_sql ) ) {
			return 0;
		}
		
		return count( $this->update_sql['ids'] );
	}

	public function getRecords() {
		global $wpdb;

		$sql_query = $wpdb->prepare( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_content <> '' AND post_type IN ( %s )", $this->post_type );

		$results = $wpdb->get_results( $sql_query );

		return $results ?: array();
	}

	public function setUpdate( $record_id, $content ) {
		global $wpdb;

		$this->update_sql['set_case'][] = $wpdb->prepare( 'WHEN ID = %d THEN %s', $record_id, $content );
		$this->update_sql['ids'][]      = $record_id;
	}

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