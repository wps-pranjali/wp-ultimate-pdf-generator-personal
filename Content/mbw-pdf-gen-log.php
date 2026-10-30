<?php
/**
 * Pdf gen log file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Mwb_log_list extents wp_list_table.
 */
class mbw_log_list extends WP_List_Table {


	/** Class constructor */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Record', 'wp-ultimate-pdf' ),
				'plural'   => __( 'Records', 'wp-ultimate-pdf' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Gets record from db.
	 *
	 * @name get_records
	 *
	 * @param string $per_page per_page.
	 * @param string $page_number page_numbre.
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public static function get_records( $per_page = 5, $page_number = 1 ) {
		global $wpdb;
		$sql = 'SELECT id,title, emailid,userName,type,exportdate from ' . $wpdb->prefix . 'ExporttoPDFRecord';
		$sql .= ' order by exportdate desc';
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}
	/**
	 * Delete record from db.
	 *
	 * @name delete_record
	 * @param string $id id .
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public static function delete_record( $id ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'ExporttoPDFRecord', array( 'id' => $id ), array( '%d' ) );
	}
	/**
	 * Count records.
	 *
	 * @name record_count
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public static function record_count() {
		 global $wpdb;
		$sql = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'ExporttoPDFRecord';
		return $wpdb->get_var( $sql );
	}
	/**
	 * Message when no records available.
	 *
	 * @name no_items
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function no_items() {
		esc_html_e( 'No records available.', 'wp-ultimate-pdf' );
	}

	/**
	 * Delete record column.
	 *
	 * @param string $item item .
	 */
	public function column_name( $item ) {
		$title = '<strong>' . $item['name'] . '</strong>';
		return $title;
	}
	/**
	 * Delete record from column.
	 *
	 * @name column_default
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'emailid':
			case 'userName':
			case 'exportdate':
			case 'useripaddr':
			case 'title':
			case 'type':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}
	/**
	 * Add checkbox to each row.
	 *
	 * @name column_cb.
	 * @param string $item item .
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id'] );
	}
	/**
	 * Removelog .
	 *
	 * @param string $item item .
	 */
	public function column_removelog( $item ) {
		$id = $item['id'];
		return sprintf( '<input type="button" value="Remove Log" class="removelg" onclick="removelog(' . $id . ')" />' );
	}

	/**
	 * Get columns to display table headers.
	 *
	 * @name get_columns
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function get_columns() {
		 $columns = array(
			 'cb'      => '<input type="checkbox" />',
			 'title'    => __( 'Post Name', 'wp-ultimate-pdf' ),
			 'emailid' => __( 'User Email', 'wp-ultimate-pdf' ),
			 'userName'    => __( 'User Name', 'wp-ultimate-pdf' ),
			 'type' => __( 'Post Type', 'wp-ultimate-pdf' ),
			 'exportdate' => __( 'Export Date', 'wp-ultimate-pdf' ),
			 'removelog' => __( 'Remove Log', 'wp-ultimate-pdf' ),
		 );
		 return $columns;
	}
	/**
	 * Get bulk action to be performed
	 *
	 * @name get_bulk_actions
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function get_bulk_actions() {
		$nonce = wp_create_nonce( 'wp_pdf_nonce' );
		echo '<input type="hidden" id="wp_pdf_nonce_field" name="wp_pdf_nonce_field" value="' . $nonce . '">';
		$actions = array( 'bulk-delete' => 'Delete' );
		return $actions;
	}
	/**
	 * Generates table records
	 *
	 * @name prepare_items
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		/** Process bulk action */
		$this->process_bulk_action();
		$per_page     = $this->get_items_per_page( 'records_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$this->items = self::get_records( $per_page, $current_page );
	}
	/**
	 * Process the chosen bulk action
	 *
	 * @name process_bulk_action
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function process_bulk_action() {
		$value_check = isset( $_POST['wp_pdf_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_pdf_nonce_field'] ) ) : '';
		if ( wp_verify_nonce( $value_check, 'wp_pdf_nonce' ) ) {
			if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {
				if ( isset( $_POST['bulk-delete'] ) ) {
					$delete_ids = esc_sql( map_deep( wp_unslash( $_POST['bulk-delete'] ), 'sanitize_text_field' ) );
				}
				if ( ! empty( $delete_ids ) ) {
					foreach ( $delete_ids as $id ) {
						self::delete_record( $id );
					}
				}
			}
		}
	}
}
