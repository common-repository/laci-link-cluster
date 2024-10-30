<?php
namespace LACI_InternalLinks\Utils;

use LACI_InternalLinks\Controllers\WPILCustomTableManager;

/**
 * @method static ActivePlugin get_instance()
 */
class ActivePlugin {

    public static function laci_internal_links_data_init() {
        // Create custom table
        WPILCustomTableManager::get_instance();
        self::created_data_main_post_for_categories();
    }

    public static function created_data_main_post_for_categories() {
        $categories = get_terms(
            [
                'taxonomy'   => 'category',
                'hide_empty' => false,
            ]
        );

        if ( is_wp_error( $categories ) || empty( $categories ) ) {
            return;
        }

        foreach ( $categories as $category ) {
            $main_post_id_category = get_term_meta( $category->term_id, 'laci_main_post_for_category', true );

            if ( empty( $main_post_id_category ) ) {
                $posts_in_category = get_posts(
                    [
                        'category'       => $category->term_id,
                        'posts_per_page' => 1,
                        'fields'         => 'ids',
                    ]
                );

                if ( ! empty( $posts_in_category ) ) {
                    $main_post_id = $posts_in_category[0];
                } else {
                    $all_posts = get_posts(
                        [
                            'posts_per_page' => 1,
                            'fields'         => 'ids',
                        ]
                    );

                    if ( ! empty( $all_posts ) ) {
                        $main_post_id = $all_posts[0];
                    } else {
                        continue; // Skip updating if no posts are found
                    }
                }

                update_term_meta( $category->term_id, 'laci_main_post_for_category', $main_post_id );
            }
        }
    }

    public static function set_default_setting_value() {
        if ( get_option( 'laci_related_box__title_color' ) === false ) {
            update_option( 'laci_related_box__title_color', '#000000' );
        }
        if ( get_option( 'laci_related_box__content_color' ) === false ) {
            update_option( 'laci_related_box__content_color', '#1e73be' );
        }
        if ( get_option( 'laci_related_box__bg_color' ) === false ) {
            update_option( 'laci_related_box__bg_color', '#dcdcde' );
        }
        if ( get_option( 'laci_related_box__bd_color' ) === false ) {
            update_option( 'laci_related_box__bd_color', '#dcdcde' );
        }
        if ( get_option( 'laci_related_box__bd_radius' ) === false ) {
            update_option( 'laci_related_box__bd_radius', '5' );
        }
        if ( get_option( 'laci_related_box__padding_top' ) === false ) {
            update_option( 'laci_related_box__padding_top', '20' );
        }
        if ( get_option( 'laci_related_box__padding_right' ) === false ) {
            update_option( 'laci_related_box__padding_right', '20' );
        }
        if ( get_option( 'laci_related_box__padding_bottom' ) === false ) {
            update_option( 'laci_related_box__padding_bottom', '20' );
        }
        if ( get_option( 'laci_related_box__padding_left' ) === false ) {
            update_option( 'laci_related_box__padding_left', '20' );
        }
        if ( get_option( 'laci_related_box__margin_top' ) === false ) {
            update_option( 'laci_related_box__margin_top', '0' );
        }
        if ( get_option( 'laci_related_box__margin_right' ) === false ) {
            update_option( 'laci_related_box__margin_right', '0' );
        }
        if ( get_option( 'laci_related_box__margin_bottom' ) === false ) {
            update_option( 'laci_related_box__margin_bottom', '0' );
        }
        if ( get_option( 'laci_related_box__margin_left' ) === false ) {
            update_option( 'laci_related_box__margin_left', '0' );
        }
        if ( get_option( 'laci_related_box__title' ) === false ) {
            update_option( 'laci_related_box__title', '<span>Related Box: </span>' );
        }
        if ( get_option( 'laci_related_box__content' ) === false ) {
            update_option( 'laci_related_box__content', '<span>[laci_post_title_link]</span>' );
        }

        if ( get_option( 'laci_num_item_dash' ) === false ) {
            update_option( 'laci_num_item_dash', '50' );
        }

        if ( get_option( 'laci_num_item_la' ) === false ) {
            update_option( 'laci_num_item_la', '50' );
        }

        if ( get_option( 'laci_delete_shortcode_in_deactivate' ) === false ) {
            update_option( 'laci_delete_shortcode_in_deactivate', 0 );
        }

        if ( get_option( 'laci_delete_shortcode_in_delete' ) === false ) {
            update_option( 'laci_delete_shortcode_in_delete', 0 );
        }

        if ( get_option( 'laci_internallinks_taxonomy' ) === false ) {
            update_option( 'laci_internallinks_taxonomy', 'category' );
        }
    }
}
