<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Controllers\WPILCustomTableManager;

/**
 *
 * @method static MetaBoxMainCategoryController get_instance()
 */
class MetaBoxMainCategoryController {

    use SingletonTrait;

    protected function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_main_category_for_post' ] );
        add_action( 'save_post', [ $this, 'save_main_category_for_post' ] );

        add_action( 'category_edit_form_fields', [ $this, 'add_custom_field_main_post_for_category' ] );
        add_action( 'edited_category', [ $this, 'save_custom_field_main_post_for_category' ] );

        // add_action( 'category_add_form_fields', [ $this, 'add_custom_field_to_category_form' ] );
        // add_action( 'created_category', [ $this, 'save_custom_field_in_category' ], 10, 2 );

    }

    /*
    public function save_custom_field_in_category( $term_id, $tt_id ) {
        if ( isset( $_POST['custom_field'] ) && $_POST['custom_field'] !== '' ) {
            add_term_meta( $term_id, 'custom_field', sanitize_text_field( $_POST['custom_field'] ), true );
        }
    }

    public function add_custom_field_to_category_form( $taxonomy ) {
        ?>
        <div class="form-field">
            <label for="custom_field">Custom Field</label>
            <input name="custom_field" id="custom_field" type="text" value="" size="40">
            <p class="description">Enter your custom field value here</p>
        </div>
        <?php
    }
    */

    public function save_custom_field_main_post_for_category( $term_id ) {

        if ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'main_category_nonce' ) ) {
            wp_die();
        }

        if ( isset( $_POST['laci_main_post_for_category'] ) ) {
            $main_post_id = sanitize_text_field( wp_unslash( $_POST['laci_main_post_for_category'] ) );

            update_term_meta( $term_id, 'laci_main_post_for_category', $main_post_id );

            // $main_category_list = get_post_meta( $main_post_id, 'laci_main_category_for_post', true );

            // if ( ! is_array( $main_category_list ) ) {
            //     $main_category_list = [];
            // }

            // if ( ! in_array( $term_id, $main_category_list ) ) {
            //     $main_category_list[] = $term_id;
            // }

            // update_post_meta( $main_post_id, 'laci_main_category_for_post', $main_category_list );
        }
    }


    public function add_custom_field_main_post_for_category( $term ) {
        $selected_value = get_term_meta( $term->term_id, 'laci_main_post_for_category', true );

        $args  = [
            'category'    => $term->term_id,
            'post_status' => 'publish',
            'numberposts' => -1,
        ];
        $posts = get_posts( $args );

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="laci_main_post_for_category"> <?php esc_html_e( 'Main Category Post', 'laci-link-cluster' ); ?> </label></th>
            <td>
                <select name="laci_main_post_for_category" id="laci_main_post_for_category" class="postform" style="width: 100%;">
                    <?php
                    echo '<option value="">Select the main post</option>';
                    foreach ( $posts as $post ) {
                        echo '<option value="' . esc_attr( $post->ID ) . '"' . selected( $selected_value, $post->ID, false ) . '>' . esc_html( $post->post_title ) . '</option>';
                    }
                    ?>
                </select>
                <p class="description"><?php esc_html_e( 'Select the main post for this category.', 'laci-link-cluster' ); ?> </p>
            </td>
        </tr>
        <?php
    }

    public function add_main_category_for_post() {
        add_meta_box(
            'laci_main_category_meta_box',
            '<p>Main Category</p>',
            [ $this, 'render_main_category_meta_box' ],
            [ 'post' ],
            'side',
            'default'
        );
    }

    public function render_main_category_meta_box( $post ) {
        wp_nonce_field( 'main_category_nonce', 'main_category_nonce' );
        $main_category          = get_post_meta( $post->ID, 'laci_main_category_id', true );
        $wp_get_post_categories = wp_get_post_categories( $post->ID );

        if ( empty( $main_category ) || in_array( $main_category, $wp_get_post_categories ) === false ) {
            $main_category = get_the_category( $post->ID )[0]->term_id;
            update_post_meta( $post->ID, 'laci_main_category_id', $main_category );
        }

        $categories = get_the_category( $post->ID );

        if ( ! empty( $categories ) ) {
            echo '<select name="main_category" id="main_category">';
            echo '<option value="">Select Main Category</option>';
            foreach ( $categories as $category ) {
                echo '<option value="' . esc_attr( $category->term_id ) . '"' . selected( $main_category, $category->term_id, false ) . '>' . esc_html( $category->name ) . '</option>';
            }
            echo '</select>';
        } else {
            echo 'This post has no categories assigned.';
        }
    }

    public function save_main_category_for_post( $post_id ) {
        if ( ! isset( $_POST['main_category_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['main_category_nonce'] ) ), 'main_category_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        $main_category = isset( $_POST['main_category'] ) ? sanitize_text_field( wp_unslash( $_POST['main_category'] ) ) : '';
        update_post_meta( $post_id, 'laci_main_category_id', $main_category );

        $outbound_links = array_merge( InternalLinksController::get_outbound_internal_links( $post_id ), WPILCustomTableManager::get_meta_value( $post_id, 'outbound_links' ) );

        $unique_outbound_links = array_map(
            function ( $item ) {
                return $item['ID'];
            },
            $outbound_links
        );
        $unique_outbound_links = array_unique( $unique_outbound_links );
        foreach ( $unique_outbound_links as $link_id ) {
            WPILCustomTableManager::handle_inserts_data_for_single_post( $link_id );
        }

        WPILCustomTableManager::handle_inserts_data_for_single_post( $post_id );
    }
}

