<?php
namespace LACI_InternalLinks;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Controllers\WPILCustomTableManager;
use LACI_InternalLinks\Controllers\CreatePostListTableController;

/**
 *
 * @method static UpdateDatabase get_instance()
 */
class UpdateDatabase {

    use SingletonTrait;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        add_action( 'wp_ajax_laci_update_post_to_db', [ $this, 'laci_update_post_to_db_callback' ] );
        add_action( 'wp_ajax_nopriv_laci_update_post_to_db', [ $this, 'laci_update_post_to_db_callback' ] );

        add_action( 'wp_ajax_laci_start_update_post_cron', [ $this, 'laci_start_update_post_cron_callback' ] );
        add_action( 'wp_ajax_nopriv_laci_start_update_post_cron', [ $this, 'laci_start_update_post_cron_callback' ] );

        add_action( 'wp_ajax_laci_check_cron_job_status', [ $this, 'laci_check_cron_job_status_callback' ] );
        add_action( 'wp_ajax_nopriv_laci_check_cron_job_status', [ $this, 'laci_check_cron_job_status_callback' ] );

        add_action( 'laci_update_post_cron_job', [ $this, 'handle_update_post_cron_job' ] );

    }

    public function handle_update_post_cron_job() {
        try {

            $wp_count_posts = wp_count_posts()->publish;

            $batch_size = $wp_count_posts < 100 ? 10 : 40;

            $offset = intval( get_option( 'laci_handle_insert_data_offset' ) );

            $total_posts = WPILCustomTableManager::handle_insert_data_with_cron_job( $batch_size, $offset );

            $offset += $batch_size;

            update_option( 'laci_handle_insert_data_offset', $offset );

            $time_next = $wp_count_posts < 100 ? 2 : 5;

            if ( $offset < $total_posts ) {
                wp_schedule_single_event(
                    time() + $time_next,
                    'laci_update_post_cron_job'
                );
            } else {
                $date_format = get_option( 'date_format' );
                $time_format = get_option( 'time_format' );

                $current_date = date_i18n( $date_format, current_time( 'timestamp' ) );
                $current_time = date_i18n( $time_format, current_time( 'timestamp' ) );

                update_option( 'laci_last_updated_date', $current_date );
                update_option( 'laci_last_updated_time', $current_time );

                update_option( 'laci_cron_job_status', 'completed' );
                update_option( 'laci_handle_insert_data_offset', 0 );
            }
        } catch ( \Exception $e ) {
            error_log( $e->getMessage() );
            update_option( 'laci_cron_job_status', 'failed' );
        }
    }

    public function laci_check_cron_job_status_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-update-database-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $a       = get_option( 'laci_handle_insert_data_all_total' );
            $b       = get_option( 'laci_handle_insert_data_offset' );
            $percent = 1;

            $cron_job_status   = get_option( 'laci_cron_job_status' );
            $is_next_scheduled = wp_next_scheduled( 'laci_update_post_cron_job' );

            if ( $a > 0 ) {
                $percent = number_format( ( $b / $a ) * 100, 1 );
            }

            if ( $percent == 100 ) {
                update_option( 'laci_handle_insert_data_all_total', 0 );
                update_option( 'laci_handle_insert_data_offset', 0 );
            }

            wp_send_json_success(
                [
                    'mess'              => __( 'Cron job started successfully', 'laci-link-cluster' ),
                    'percent'           => $percent > 100 ? 100 : $percent,
                    'current'           => $b,
                    'cron_job_status'   => $cron_job_status,
                    'is_next_scheduled' => $is_next_scheduled,
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function laci_start_update_post_cron_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-update-database-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $cron_job_status = get_option( 'laci_cron_job_status' );

            if ( $cron_job_status != 'running' ) {
                update_option( 'laci_cron_job_status', 'running' );
                $offset = intval( get_option( 'laci_handle_insert_data_offset' ) );
                if ( $offset >= 100 ) {
                    update_option( 'laci_handle_insert_data_offset', 0 );
                }
            }

            if ( ! wp_next_scheduled( 'laci_update_post_cron_job' ) ) {
                $wp_count_posts = wp_count_posts()->publish;

                $time_next = $wp_count_posts < 100 ? 1 : 3;

                wp_schedule_single_event(
                    time() + $time_next,
                    'laci_update_post_cron_job'
                );
            }
            $taxonomy_data = get_option( 'laci_internallinks_taxonomy', 'category' );
            update_option( 'laci_internallinks_updated_for_taxonomy', $taxonomy_data );
            wp_send_json_success( [ 'mess' => __( 'Cron job started successfully', 'laci-link-cluster' ) ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function laci_update_post_to_db_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-update-database-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );

            }

            $batch_size = isset( $_POST['batch_size'] ) ? intval( $_POST['batch_size'] ) : 100;
            $offset     = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;

            $total_posts = WPILCustomTableManager::handle_insert_data( $batch_size, $offset );

            wp_send_json_success(
                [
                    'mess'        => __( 'Updated successfully', 'laci-link-cluster' ),
                    'offset'      => $offset,
                    'batch_size'  => $batch_size,
                    'total_posts' => $total_posts,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }

    }

}
