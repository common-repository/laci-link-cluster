<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Controllers\InternalLinksController;

use LACI_InternalLinks\Controllers\WPILCustomTableManager;

// Include the WP_List_Table class
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * @method static LinkAssistantController get_instance()
 */
class LinkAssistantController extends CreatePostListTableController {

    use SingletonTrait;

    public function prepare_items() {
        if ( isset( $_REQUEST['nonce'] ) && ! empty( $_REQUEST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'laci-internal-links-nonce' ) ) {
            wp_die();
        }

        $post_id = isset( $_REQUEST['post_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_id'] ) ) : '';
        $post    = get_post( $post_id );

        if ( empty( $post ) ) {
            wp_die();
        }

        $this->items = [ $post ];

        $columns               = $this->get_columns();
        $this->_column_headers = [ $columns ];
    }

}
