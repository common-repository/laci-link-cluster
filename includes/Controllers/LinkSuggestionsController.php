<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 *
 * @method static LinkSuggestionsController get_instance()
 */
class LinkSuggestionsController {

    use SingletonTrait;

    protected function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_custom_meta_box' ] );
    }

    public function add_custom_meta_box() {
        add_meta_box(
            'custom_meta_box',
            'Suggested Outbound Links',
            [ $this, 'display_internal_link_suggestions_meta_box' ],
            [ 'post', 'page' ],
            'normal',
            'high'
        );
    }

    public function display_internal_link_suggestions_meta_box( $post ) {

        $current_post_id      = $post->ID;
        $current_post_content = get_post_field( 'post_content', $current_post_id );

        $suggested_link_text = '';
        $suggested_link_url  = '';

        $args = [
            'post_type'      => 'post',
            'post__not_in'   => [ $current_post_id ],
            'posts_per_page' => -1,
            'publish_status' => 'published',
        ];

        $all_posts   = get_posts( $args );
        $suggestions = [];

        foreach ( $all_posts as $single_post ) {
            $single_post_title         = get_the_title( $single_post->ID );
            $array_text_common_phrases = $this->detect_common_phrases( $current_post_content, $single_post_title );
            if ( ! empty( $array_text_common_phrases ) ) {
                foreach ( $array_text_common_phrases as $text_common_phrases ) {
                    $suggested_link_text = $text_common_phrases;
                    $title_post_outbound = $single_post->post_title;
                    $url_post_outbound   = get_permalink( $single_post->ID );

                    $suggestions[] = [
                        'anchor_text'         => $suggested_link_text,
                        'title_post_outbound' => $title_post_outbound,
                        'url_post_outbound'   => $url_post_outbound,
                        'post_outbound'       => $single_post,
                    ];

                }
            }
        }

        if ( ! empty( $suggestions ) ) {
            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/meta-box/suggestion-table.php';
            $html = ob_get_contents();
            ob_end_clean();
            laci_kses_post_e( $html );
        } else {
            esc_html_e( 'No internal link suggestions available.', 'laci-link-cluster' );
        }
    }

    public function longest_common_substring( $text1, $text2 ) {
        $pattern     = '/[^\p{L}\p{N}\s]+/u';
        $clean_text1 = preg_replace( $pattern, ' ', $text1 );
        $clean_text2 = preg_replace( $pattern, ' ', $text2 );

        $words1 = preg_split( '/\s+/', $clean_text1 );
        $words2 = preg_split( '/\s+/', $clean_text2 );

        $length1        = count( $words1 );
        $length2        = count( $words2 );
        $common_phrases = [];

        $excluded_words = [ 'a', 'an', 'is', 'the', 'for', 'by', 'and', 'or', 'of the', 'for a', 'with', 'your' ];

        for ( $i = 0; $i < $length1; $i++ ) {
            for ( $j = 0; $j < $length2; $j++ ) {
                $phrase = '';
                $k      = 0;
                while ( $i + $k < $length1 && $j + $k < $length2 && $words1[ $i + $k ] == $words2[ $j + $k ] ) {
                    if ( $phrase !== '' ) {
                        $phrase .= ' ';
                    }
                    $phrase .= $words1[ $i + $k ];
                    $k++;
                }
                if ( $phrase !== '' && strlen( $phrase ) > 2 && ! in_array( strtolower( $phrase ), $excluded_words ) ) {
                    $common_phrases[] = $phrase;
                }
            }
        }

        return array_unique( $common_phrases );
    }

    public function detect_common_phrases( $post_content, $post_title ) {
        $common_phrases = $this->longest_common_substring( $post_content, $post_title );
        return $this->filter_array( $common_phrases );
    }

    public function filter_array( $input_array ) {
        $filtered_array = [];

        foreach ( $input_array as $item ) {
            $keep_item = true;

            foreach ( $filtered_array as $filtered_item ) {
                if ( strpos( $item, $filtered_item ) !== false || strpos( $filtered_item, $item ) !== false ) {
                    $keep_item = false;
                    break;
                }
            }

            if ( $keep_item ) {
                $filtered_array[] = $item;
            }
        }

        return $filtered_array;
    }

}

