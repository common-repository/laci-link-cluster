<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

// Include the WP_List_Table class
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * @method static TableInternalLinkInfoController get_instance()
 */
class TableInternalLinkInfoController extends CreatePostListTableController {

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

    public function get_columns() {
        $columns = [
            'title'                      => esc_html__( 'ACTIONS', 'laci-link-cluster' ),
            'inbound_links'              => $this->get_title( 'INBOUND LINKS ENTIRE SITE' ),
            'inbound_links_in_category'  => $this->get_title( 'INBOUND LINKS SAME CLUSTER' ),
            'outbound_links'             => $this->get_title( 'OUTBOUND LINKS ENTIRE SITE' ),
            'outbound_links_in_category' => $this->get_title( 'OUTBOUND LINKS SAME CLUSTER' ),
            'link_back_to_category'      => $this->get_title( 'LINK BACK TO CATEGORY /PARENT' ),
        ];
        return $columns;
    }

    public function display() {
        $nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
        if ( isset( $_REQUEST['nonce'] ) && ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Invalid nonce.', 'laci-link-cluster' ) . '</p></div>';
            return;
        }

        $post_id = isset( $_REQUEST['post_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_id'] ) ) : '';
        $post    = get_post( $post_id );

        if ( empty( $post ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Post not found.', 'laci-link-cluster' ) . '</p></div>';
            return;
        }

        $this->prepare_items(); // Prepare items for display

        echo '<form id="internal-links-filter" method="get">';
        echo '<table class="wp-list-table ' . esc_attr( implode( ' ', ( $this->get_table_classes() ) ) ) . '">';
        $this->print_column_headers();
        echo '<tbody id="the-list">';

        foreach ( $this->items as $item ) {
            echo '<tr>';
            $this->single_row( $item );
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</form>';
        echo '</div>';
    }
}
