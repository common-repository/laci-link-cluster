<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 * @method static WPILCustomTableManager get_instance()
 */
class WPILCustomTableManager {

    use SingletonTrait;

    public function __construct() {
        $this->create_table();
    }
    /**
     * Creates a custom table in the WordPress database for storing internal links metadata.
     *
     * @global wpdb $wpdb The WordPress database object.
     */
    public function create_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME;

        $charset_collate = $wpdb->get_charset_collate();

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {
            $sql = "CREATE TABLE {$table_name} (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                post_id bigint(20) NOT NULL,
                meta_key varchar(255) NOT NULL,
                meta_value longtext NOT NULL,
                PRIMARY KEY (id),
                KEY post_id (post_id)
            ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }

    /**
     * Inserts or updates data in the custom table.
     *
     * @param int    $post_id    The ID of the post.
     * @param string $meta_key   The meta key for the data.
     * @param mixed  $meta_value The meta value to be inserted, can be string or array.
     * @return void
     */
    public static function insert_data( $post_id, $meta_key, $meta_value ) {
        global $wpdb;
        $table_name = $wpdb->prefix . LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME;

        // Convert meta_value to JSON if it is an array
        if ( is_array( $meta_value ) ) {
            $meta_value = wp_json_encode( $meta_value );
        }

        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        if ( $exists ) {
            $wpdb->update(
                $table_name,
                [ 'meta_value' => $meta_value ],
                [
                    'post_id'  => $post_id,
                    'meta_key' => $meta_key,
                ]
            );
        } else {
            $wpdb->insert(
                $table_name,
                [
                    'post_id'    => $post_id,
                    'meta_key'   => $meta_key,
                    'meta_value' => $meta_value,
                ]
            );
        }
    }

    /**
     * Deletes data from the custom table based on the given post ID.
     *
     * @param int $post_id The ID of the post to delete data for.
     * @return void
     */
    public function delete_data( $post_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME;
        $wpdb->delete( $table_name, [ 'post_id' => $post_id ] );
    }

    /**
     * Retrieves data from the custom table based on the given post ID.
     *
     * @param int $post_id The ID of the post.
     * @return array|null The retrieved data from the custom table, or null if no data found.
     */
    public static function get_data( $post_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE post_id = %d",
                $post_id
            )
        );
    }

    /**
     * Retrieves the meta value for a given post ID and meta key from the custom table.
     *
     * @param int    $post_id   The ID of the post.
     * @param string $meta_key  The meta key to retrieve the value for.
     *
     * @return mixed|null The meta value if found, null otherwise.
     */
    public static function get_meta_value( $post_id, $meta_key ) {
        global $wpdb;
        $table_name = $wpdb->prefix . LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME;
        $meta_value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM {$table_name} WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        // Try to decode JSON to return array if possible
        $decoded_value = json_decode( $meta_value, true );
        return ( json_last_error() === JSON_ERROR_NONE ) ? $decoded_value : $meta_value;
    }

    /**
     * Handles the insertion of data into the custom table for all posts.
     *
     * @return void
     */
    public static function handle_insert_data( $batch_size = 100, $offset = 0 ) {
        global $wpdb;

        // Get total post count
        $total_posts = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_type IN ('post', 'page')"
        );

        // Execute SQL query
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_type IN ('post', 'page') LIMIT %d OFFSET %d",
                $batch_size,
                $offset
            )
        );

        foreach ( $results as $result ) {
            $post = get_post( $result->ID );
            if ( $post ) {
                self::handle_inserts_data_for_single_post( $post->ID, $post );
            }
        }

        return $total_posts;

    }

    /**
     * Handles the insertion of data with a cron job.
     *
     * This function retrieves a batch of posts from the WordPress database and inserts them into a custom table.
     * It uses a cron job to process the insertion in smaller batches to avoid memory issues.
     *
     * @param int $batch_size The number of posts to process in each batch. Default is 20.
     * @param int $offset     The offset to start retrieving posts from. Default is 0.
     *
     * @return int The total number of posts processed.
     */
    public static function handle_insert_data_with_cron_job( $batch_size = 20, $offset = 0 ) {
        global $wpdb;

        // Get total post count
        $total_posts = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_type IN ('post', 'page')"
        );

        // Execute SQL query
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_type IN ('post', 'page') LIMIT %d OFFSET %d",
                $batch_size,
                $offset
            )
        );

        update_option( 'laci_handle_insert_data_all_total', $total_posts );

        foreach ( $results as $result ) {
            $post = get_post( $result->ID );
            if ( $post ) {
                self::handle_inserts_data_for_single_post( $post->ID, $post );
            }
        }

        return $total_posts;
    }


    /**
     * Handles inserting data for a single post into the custom table.
     *
     * @param int $post_id The ID of the post.
     * @param object|null $post_data Optional. The post data object. If not provided, it will be fetched using the post ID.
     * @return void
     */
    public static function handle_inserts_data_for_single_post( $post_id, $post_data = null ) {
        $post = ! empty( $post_data ) ? $post_data : get_post( $post_id );
        if ( $post ) {
            self::insert_data( $post_id, 'post_id', $post_id );
            self::insert_data( $post_id, 'post_title', $post->post_title );
            self::insert_data( $post_id, 'post_content', $post->post_content );
            self::insert_data( $post_id, 'type', $post->post_type );
            self::insert_data( $post_id, 'status', $post->post_status );
            self::insert_data( $post_id, 'author', $post->post_author );
            self::insert_data( $post_id, 'categories', wp_get_post_categories( $post_id ) );
            self::insert_data( $post_id, 'inbound_links', InternalLinksController::get_inbound_internal_links( $post_id ) );
            self::insert_data( $post_id, 'outbound_links', InternalLinksController::get_outbound_internal_links( $post_id ) );
            self::insert_data( $post_id, 'inbound_links_in_category', InternalLinksController::get_inbound_internal_links_in_taxonomy( $post_id ) );
            self::insert_data( $post_id, 'outbound_links_in_category', InternalLinksController::get_outbound_internal_links_in_taxonomy( $post_id ) );
            self::insert_data( $post_id, 'link_back_to_category', InternalLinksController::get_outbound_link_back_to_taxonomy( $post_id ) );
            self::insert_data( $post_id, 'is_orphan_page', InternalLinksController::is_orphan_page( $post_id ) );
        }
    }
}
