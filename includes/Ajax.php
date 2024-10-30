<?php
namespace LACI_InternalLinks;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Controllers\InternalLinksController;
use LACI_InternalLinks\Controllers\WPILCustomTableManager;


/**
 *
 * @method static Ajax get_instance()
 */
class Ajax {

    use SingletonTrait;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        add_action( 'wp_ajax_get_internal_links_info', [ $this, 'get_internal_links_info_callback' ] );

        add_action( 'wp_ajax_laci_change_key_words_for_post', [ $this, 'laci_change_key_words_for_post_callback' ] );

        add_action( 'wp_ajax_laci_change_category_for_post', [ $this, 'laci_change_category_for_post_callback' ] );

        add_action( 'wp_ajax_laci_update_single_post_to_db', [ $this, 'laci_update_single_post_to_db_callback' ] );

        add_action( 'wp_ajax_laci_get_keywords_info', [ $this, 'laci_get_keywords_info' ] );
    }

    public function laci_get_keywords_info() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

            if ( ! $post_id ) {
                wp_send_json_error( [ 'mess' => __( 'post id not found', 'laci-link-cluster' ) ] );
            }

            $key_words         = get_post_meta( $post_id, 'laci_list_key_word', true );
            $key_words_implode = is_array( $key_words ) ? implode( ',', $key_words ) : '';

            ob_start();
            ?>
                <div class="laci-update-keywords">
                    <input type="text" name="laci_add_key_word" style="width: 100%;margin-bottom:10px">
                    <button type="button" class="laci-add-keyword-button button" style="margin-bottom:10px"><?php esc_html_e( 'Add Keyword', 'laci-link-cluster' ); ?></button>
                    <div>
                        <label> <?php esc_html_e( 'Configured keywords:', 'laci-link-cluster' ); ?></label>
                        <ul class='laci-list-key-word'>
                            <?php
                            if ( is_array( $key_words ) ) {
                                foreach ( $key_words as $key_word ) {
                                    if ( empty( $key_word ) ) {
                                        continue;
                                    }
                                    ?>
                                    <li class="laci-list-key-word__item" data-key="<?php echo esc_html( $key_word ); ?>">
                                        <a class="dashicons dashicons-dismiss remove laci-remove-key-word"></a>
                                        <?php echo esc_html( $key_word ); ?>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <input type="hidden" name="laci_list_key_word" value="<?php echo esc_attr( $key_words_implode ); ?>" style="width: 100%">
                </div>
               
            <?php
            $html = ob_get_contents();
            ob_end_clean();

            wp_send_json_success(
                [
                    'mess' => __( 'Key words updated successfully', 'laci-link-cluster' ),
                    'html' => $html,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function laci_update_single_post_to_db_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

            WPILCustomTableManager::handle_inserts_data_for_single_post( $post_id );

            $total_links              = ( WPILCustomTableManager::get_meta_value( $post_id, 'link_back_to_category' )['total_links'] ?? 0 );
            $total_outbound_main_post = ( WPILCustomTableManager::get_meta_value( $post_id, 'link_back_to_category' )['total_outbound_main_post'] ?? 0 );

            wp_send_json_success(
                [
                    'mess'                       => __( 'Updated successfully', 'laci-link-cluster' ),
                    'inbound_links'              => count( WPILCustomTableManager::get_meta_value( $post_id, 'inbound_links' ) ?? [] ),
                    'outbound_links'             => count( WPILCustomTableManager::get_meta_value( $post_id, 'outbound_links' ) ?? [] ),
                    'inbound_links_in_category'  => count( WPILCustomTableManager::get_meta_value( $post_id, 'inbound_links_in_category' ) ?? [] ),
                    'outbound_links_in_category' => count( WPILCustomTableManager::get_meta_value( $post_id, 'outbound_links_in_category' ) ?? [] ),
                    'link_back_to_category'      => $total_links . '/' . $total_outbound_main_post,
                    'total_links'                => $total_links,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }

    }

    public function laci_change_category_for_post_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

            $list_category = isset( $_POST['list_category'] ) ? rest_sanitize_array( wp_unslash( $_POST['list_category'] ) ) : [];

            if ( ! $list_category ) {
                wp_send_json_error( [ 'mess' => __( 'Key words not found', 'laci-link-cluster' ) ] );
            }

            foreach ( $list_category as $key => $item ) {
                $post_id = isset( $item['post_id'] ) ? intval( $item['post_id'] ) : 0;
                $terms   = isset( $item['categories'] ) ? array_map( 'intval', $item['categories'] ) : [];

                $list_category[ $key ]['categories_title']     = $this->get_category_titles_from_ids( $terms );
                $list_category[ $key ]['categories_edit_link'] = $this->get_category_categories_edit_link( $terms, $post_id );

                if ( $post_id && ! empty( $terms ) ) {
                    wp_set_post_terms( $post_id, $terms, $taxonomy );
                }
            }

            wp_send_json_success(
                [
                    'mess'          => __( 'Key words updated successfully', 'laci-link-cluster' ),
                    'list_category' => $list_category,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }


    public function get_category_categories_edit_link( $category, $post_id ) {
        $category_edit_link     = [];
        $taxonomy               = get_option( 'laci_internallinks_taxonomy', 'category' );
        $main_category          = get_post_meta( $post_id, 'laci_main_category_id', true );
        $wp_get_post_categories = wp_get_post_categories( $post_id );

        foreach ( $category as $index => $cat_id ) {
            $ancestors         = get_ancestors( $cat_id, $taxonomy );
            $ancestors         = array_reverse( $ancestors );
            $ancestors[]       = $cat_id;
            $hierarchical_name = '';

            foreach ( $ancestors as $ancestor_id ) {
                $ancestor           = get_term( $ancestor_id, $taxonomy );
                $hierarchical_name .= $ancestor->name . ' / ';
            }
            $hierarchical_name = rtrim( $hierarchical_name, ' / ' );

            // If main_category is empty and this is the first category, or if this is the main category
            if ( empty( $main_category ) && $index == 0 || in_array( $main_category, $wp_get_post_categories ) === false && $index == 0 ) {
                $hierarchical_name = '<strong>' . $hierarchical_name . '</strong>';
            } elseif ( $main_category == strval( $cat_id ) ) {
                $hierarchical_name = '<strong>' . $hierarchical_name . '</strong>';
            }

            $hierarchical_name    = rtrim( $hierarchical_name, ' / ' );
            $category_edit_link[] = '<a class="laci-category-text-item" href="' . get_edit_term_link( $cat_id, 'category' ) . '">' . $hierarchical_name . '</a>';
        }

        return $category_edit_link;
    }


    public function get_category_titles_from_ids( $term_ids ) {
        $titles = [];

        foreach ( $term_ids as $term_id ) {
            $term = get_term( $term_id, 'category' );
            if ( ! is_wp_error( $term ) ) {
                $titles[] = $term->name;
            }
        }

        return $titles;
    }

    public function laci_change_key_words_for_post_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $list_key_words = isset( $_POST['list_key_words'] ) ? sanitize_text_field( wp_unslash( $_POST['list_key_words'] ) ) : [];
            $post_id        = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

            if ( ! $list_key_words || ! $post_id ) {
                wp_send_json_error( [ 'mess' => __( 'key words not found', 'laci-link-cluster' ) ] );
            }

            update_post_meta( $post_id, 'laci_list_key_word', explode( ',', trim( $list_key_words ) ) );

            wp_send_json_success( [ 'mess' => __( 'Key words updated successfully', 'laci-link-cluster' ) ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }

    }

    public function get_internal_links_info_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id   = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
            $link_type = isset( $_POST['link_type'] ) ? sanitize_text_field( wp_unslash( $_POST['link_type'] ) ) : 'inbound';

            $get_popup_info = $this->get_popup_info_html( $post_id, $link_type ) ?? '';
            $html           = $get_popup_info['html'] ?? '';
            $title          = $get_popup_info['title'] ?? '';
            wp_send_json_success(
                [
                    'html'  => $html,
                    'title' => $title,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function get_popup_info_html( $post_id, $link_type ) {
        $post       = get_post( $post_id );
        $post_title = $post->post_title;

        if ( 'inbound' === $link_type ) {
            $title = __( 'Internal Links [Entire WebSite] - Inbound', 'laci-link-cluster' );
            $links = WPILCustomTableManager::get_meta_value( $post_id, 'inbound_links' );
        }
        if ( 'outbound' === $link_type ) {
            $title = __( 'Internal Links [Entire WebSite] - Outbound', 'laci-link-cluster' );
            $links = WPILCustomTableManager::get_meta_value( $post_id, 'outbound_links' );
        }
        if ( 'inbound_category' === $link_type ) {
            $title = __( 'Internal Links [Within the Category] - Inbound', 'laci-link-cluster' );
            $links = WPILCustomTableManager::get_meta_value( $post_id, 'inbound_links_in_category' );
        }
        if ( 'outbound_category' === $link_type ) {
            $title = __( 'Internal Links [Within the Category] - Outbound', 'laci-link-cluster' );
            $links = WPILCustomTableManager::get_meta_value( $post_id, 'outbound_links_in_category' );
        }

        if ( 'link_back_to_category' === $link_type ) {
            $title = __( 'Link back to Category [Back to Parent]', 'laci-link-cluster' );
            $links = WPILCustomTableManager::get_meta_value( $post_id, 'link_back_to_category' )['link_details'];
        }

        if ( ! empty( $links ) && ! empty( $post_title ) ) {
            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/dashboard/popup-info.php';
            $html = ob_get_contents();
            ob_end_clean();

            return [
                'html'  => $html,
                'title' => $title,
            ];
        }
        return '';
    }

}
