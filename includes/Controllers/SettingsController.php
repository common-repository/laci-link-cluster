<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 * @method static SettingsController get_instance()
 */
class SettingsController extends CreatePostListTableController {

    use SingletonTrait;

    protected function __construct() {
        add_action( 'wp_ajax_laci_import_key_words_rank_math', [ $this, 'laci_import_key_words_rank_math' ] );
        add_action( 'wp_ajax_laci_import_key_words_yoast', [ $this, 'laci_import_key_words_yoast' ] );
    }

    public static function set_default_value() {
        update_option( 'laci_related_box__title_color', '#000000' );
        update_option( 'laci_related_box__content_color', '#1e73be' );
        update_option( 'laci_related_box__bg_color', '#dcdcde' );
        update_option( 'laci_related_box__bd_color', '#dcdcde' );
        update_option( 'laci_related_box__bd_radius', '5' );

        update_option( 'laci_related_box__padding_top', '20' );
        update_option( 'laci_related_box__padding_right', '20' );
        update_option( 'laci_related_box__padding_bottom', '20' );
        update_option( 'laci_related_box__padding_left', '20' );

        update_option( 'laci_related_box__margin_top', '0' );
        update_option( 'laci_related_box__margin_right', '0' );
        update_option( 'laci_related_box__margin_bottom', '0' );
        update_option( 'laci_related_box__margin_left', '0' );

        update_option( 'laci_related_box__image', '' );
        update_option( 'laci_related_box__image_width', '100' );
        update_option( 'laci_related_box__image_height', '100' );

        update_option( 'laci_related_box__title', '<span>Related Box: </span>' );
        update_option( 'laci_related_box__content', '<span>[laci_post_title_link]</span>' );
    }

    public static function update_option_related_box( $data ) {
        update_option( 'laci_related_box__title_color', sanitize_text_field( $data['laci_title_color'] ) );
        update_option( 'laci_related_box__content_color', sanitize_text_field( $data['laci_content_color'] ) );
        update_option( 'laci_related_box__bg_color', sanitize_text_field( $data['laci_bg_color'] ) );
        update_option( 'laci_related_box__bd_color', sanitize_text_field( $data['laci_bd_color'] ) );
        update_option( 'laci_related_box__bd_radius', sanitize_text_field( $data['laci_bd_radius'] ) );

        update_option( 'laci_related_box__padding_top', sanitize_text_field( $data['laci_pd_top'] ) );
        update_option( 'laci_related_box__padding_right', sanitize_text_field( $data['laci_pd_right'] ) );
        update_option( 'laci_related_box__padding_bottom', sanitize_text_field( $data['laci_pd_bottom'] ) );
        update_option( 'laci_related_box__padding_left', sanitize_text_field( $data['laci_pd_left'] ) );

        update_option( 'laci_related_box__image', sanitize_text_field( $data['laci_related_box_image'] ) );
        update_option( 'laci_related_box__image_width', sanitize_text_field( $data['laci_related_box_image_width'] ) );
        update_option( 'laci_related_box__image_height', sanitize_text_field( $data['laci_related_box_image_height'] ) );

        update_option( 'laci_related_box__margin_top', sanitize_text_field( $data['laci_mg_top'] ) );
        update_option( 'laci_related_box__margin_right', sanitize_text_field( $data['laci_mg_right'] ) );
        update_option( 'laci_related_box__margin_bottom', sanitize_text_field( $data['laci_mg_bottom'] ) );
        update_option( 'laci_related_box__margin_left', sanitize_text_field( $data['laci_mg_left'] ) );

        update_option( 'laci_related_box__title', wp_kses_post( $data['custom-related-box-title-editor'] ) );
        update_option( 'laci_related_box__content', wp_kses_post( $data['custom-related-box-content-editor'] ) );
    }

    public function laci_import_key_words_yoast() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
            $limit  = 100; // Number of posts to update per request

            $args = [
                'post_type'      => [ 'post', 'page' ],
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'offset'         => $offset,
                'fields'         => 'ids',
            ];

            $query        = new \WP_Query( $args );
            $all_post_ids = $query->posts;

            foreach ( $all_post_ids as $post_id ) {
                $primary_keyphrase = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );

                $related_keyphrases = get_post_meta( $post_id, '_yoast_wpseo_focuskeywords', true );

                $arr_keywords = [];

                if ( ! empty( $related_keyphrases ) ) {
                    $related_keyphrases_decode = json_decode( $related_keyphrases, true );

                    $arr_keywords = array_column( $related_keyphrases_decode, 'keyword' );
                }

                if ( ! empty( $primary_keyphrase ) ) {
                    array_push( $arr_keywords, $primary_keyphrase );
                }

                $merge_key_work = [];

                $laci_list_key_word = get_post_meta( $post_id, 'laci_list_key_word', true );

                if ( is_array( $laci_list_key_word ) && is_array( $arr_keywords ) ) {
                    $merge_key_work = array_merge( $arr_keywords, $laci_list_key_word );
                }

                update_post_meta( $post_id, 'laci_list_key_word', array_unique( $merge_key_work ) );
            }

            $total_posts = $query->found_posts;
            $next_offset = $offset + $limit;

            wp_send_json_success(
                [
                    'message'    => __( 'Key words updated successfully', 'laci-link-cluster' ),
                    'nextOffset' => $next_offset < $total_posts ? $next_offset : null,
                    'totalPosts' => $total_posts,
                    'offset'     => $next_offset,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function laci_import_key_words_rank_math() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
            $limit  = 100; // Number of posts to update per request

            $args = [
                'post_type'      => [ 'post', 'page' ],
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'offset'         => $offset,
                'fields'         => 'ids',
            ];

            $query        = new \WP_Query( $args );
            $all_post_ids = $query->posts;

            foreach ( $all_post_ids as $post_id ) {
                $rank_math_focus_keyword = explode( ',', get_post_meta( $post_id, 'rank_math_focus_keyword', true ) );
                $laci_list_key_word      = get_post_meta( $post_id, 'laci_list_key_word', true );

                $merge_key_work = [];

                if ( is_array( $rank_math_focus_keyword ) && is_array( $laci_list_key_word ) ) {
                    $merge_key_work = array_merge( $rank_math_focus_keyword, $laci_list_key_word );
                }

                update_post_meta( $post_id, 'laci_list_key_word', array_unique( $merge_key_work ) );
            }

            $total_posts = $query->found_posts;
            $next_offset = $offset + $limit;

            wp_send_json_success(
                [
                    'message'    => __( 'Key words updated successfully', 'laci-link-cluster' ),
                    'nextOffset' => $next_offset < $total_posts ? $next_offset : null,
                    'totalPosts' => $total_posts,
                    'offset'     => $next_offset,
                ]
            );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }


}
