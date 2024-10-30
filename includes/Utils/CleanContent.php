<?php
namespace LACI_InternalLinks\Utils;

/**
 *
 * @method static CleanContent get_instance()
 */
class CleanContent {


    /**
     * Cleans the content to update the related post.
     *
     * @param string $content The content to be cleaned.
     * @param int $related_post_id The ID of the related post.
     * @return string The cleaned content.
     */
    public static function clean_content_to_update_related_post( $content, $related_post_id ) {
        $title           = get_the_title( $related_post_id );
        $related_post_id = 'lac_current_' . $related_post_id;
        $shortcode       = '[laci_related_post_content id="' . $related_post_id . '" title="' . $title . '"]';

        $post_content = preg_replace(
            '/<div class="laci-related-box-container">.*?<\/div>/is',
            $shortcode,
            $content
        );

        $post_content = self::move_shortcode_outside_blocks( $post_content );

        // $post_content = self::replace_shortcode( $post_content, $related_post_id, $title );

        $shortcode_command = '<!-- wp:shortcode -->' . $shortcode . '<!-- /wp:shortcode -->';

        $post_content = str_replace( $shortcode, $shortcode_command, $post_content );

        $post_content = preg_replace( '/<p class="laci-shortcode">\s*(\[[^\]]+\])\s*<\/p>/is', '$1', $post_content );

        $post_content = str_replace( 'lac_current_', '', $post_content );

        return $post_content;
    }

    /**
     * Moves the shortcode outside of Gutenberg blocks in the post content.
     *
     * @param string $post_content The content of the post.
     * @return string The modified post content.
     */

    public static function move_shortcode_outside_blocks( $post_content ) {
        // Regular expression to find Gutenberg blocks
        $block_pattern = '/<!-- wp:(?!shortcode)[\w-]+.*?-->.*?<!-- \/wp:(?!shortcode)[\w-]+ -->/s';

        // Find all Gutenberg blocks in the content
        preg_match_all( $block_pattern, $post_content, $blocks );

        // Iterate over each block to check and process shortcode
        foreach ( $blocks[0] as $block ) {
            // Check if the block contains the shortcode
            if ( strpos( $block, '[laci_related_post_content' ) !== false ) {
                // If it's inside a Gutenberg block, move shortcode out
                $post_content = str_replace(
                    $block,
                    preg_replace(
                        '/(<!-- wp:(?!shortcode)[\w-]+.*?-->)(.*?)(\[laci_related_post_content [^\]]+\])(.*?)(<!-- \/wp:(?!shortcode)[\w-]+ -->)/s',
                        '$1$2$4$5' . "\n" . '$3',
                        $block
                    ),
                    $post_content
                );
            }
        }

        return $post_content;
    }

    /**
     * Replaces the specified shortcode with a default shortcode.
     *
     * @param string $input The input string.
     * @return string The modified input string.
     */
    public static function replace_shortcode( $input, $post_id, $title ) {
        $input = preg_replace( '/<!-- wp:shortcode -->.*?\[laci_related_post_content [^\]]+\].*?<!-- \/wp:shortcode -->/s', '[laci_related_post_content id="' . $post_id . '" title="' . $title . '"]', $input );

        return $input;
    }

    /**
     * Replaces the HTML entity for non-breaking space with a regular space.
     *
     * @param string $content The content to be modified.
     * @return string The modified content.
     */
    public static function replace_html_entity( $content ) {
        $content = htmlentities( $content, ENT_QUOTES, 'UTF-8' ); // Convert special characters to HTML entities
        $content = str_replace( '&nbsp;', ' ', $content ); // Replace HTML entity for non-breaking space with a regular space
        $content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' ); // Convert HTML entities back to their original characters
        $content = str_replace( '&nbsp;', ' ', $content );
        return $content;
    }

    /**
     * Cleans the content by removing unwanted elements and formatting.
     *
     * @param string $content The content to be cleaned.
     * @return string The cleaned content.
     */
    public static function clean_content( $content ) {
        $content = stripslashes( $content );
        $content = preg_replace( '/<span class="laci-text-highlight">|<\/span>/', '', $content );
        $content = preg_replace( '/<span class="laci-text-highlight active-highlight">|<\/span>/', '', $content );
        $content = str_replace( [ '<p>', '</p>' ], '', $content );
        $content = str_replace( '&nbsp;', ' ', $content );

        return trim( $content );
    }

    /**
     * Retrieves the highlighted excerpts from the content based on an array of keywords.
     *
     * @param string $content The content to extract excerpts from.
     * @param array $keywords_array An array of keywords to search for.
     * @return array An array containing the extracted excerpts and the total number of keywords found.
     */
    public static function get_highlighted_excerpts( $content, $keywords_array ) {
        $excerpts             = [];
        $total_keywords_fined = 0;

        // Extract content between <!-- wp:paragraph --> and <!-- /wp:paragraph --> tags
        preg_match_all( '/<!-- wp:paragraph -->(.*?)<!-- \/wp:paragraph -->/is', $content, $matches );

        $found_first_keyword = false;

        foreach ( $matches[1] as $paragraph ) {
            $highlighted_paragraph = $paragraph;

            // Find all <a> tags and their href attributes
            preg_match_all( '/<a\b[^>]*href=[\'"]([^\'"]+)[\'"][^>]*>(.*?)<\/a>/is', $highlighted_paragraph, $link_matches, PREG_OFFSET_CAPTURE );

            foreach ( $keywords_array as $key_word ) {
                // Normalize keyword for case-insensitive matching
                $normalized_key_word = strtolower( $key_word );

                // Find all matches of the keyword in the paragraph
                if ( preg_match_all( '/(' . preg_quote( $normalized_key_word, '/' ) . ')/i', $highlighted_paragraph, $all_matches, PREG_OFFSET_CAPTURE ) ) {
                    // Reverse loop through all matches to avoid issues with string replacement offsets
                    for ( $i = count( $all_matches[0] ) - 1; $i >= 0; $i-- ) {
                        $exact_match = $all_matches[0][ $i ][0];
                        $start_pos   = $all_matches[0][ $i ][1];

                        // Check if the match is inside an <a> tag's href attribute
                        $is_inside_href = false;
                        foreach ( $link_matches[0] as $link ) {
                            $full_link      = $link[0];
                            $href_start_pos = $link[1]; // Position of <a> tag
                            $href_value     = $link_matches[1][0][0]; // Href value of the anchor tag

                            // Check if the exact match is inside the href
                            if ( ! empty( $exact_match ) && strpos( $full_link, $exact_match ) !== false && strpos( $full_link, $exact_match ) < $start_pos && ( $start_pos >= $href_start_pos && $start_pos < $href_start_pos + strlen( $full_link ) ) ) {
                                $is_inside_href = true;
                                break;
                            }
                        }

                        // Highlight the match if it's not inside an href
                        if ( ! $is_inside_href ) {
                            $total_keywords_fined++;
                            $highlighted_paragraph = substr_replace(
                                $highlighted_paragraph,
                                '<span class="laci-text-highlight">' . $exact_match . '</span>',
                                $start_pos,
                                strlen( $exact_match )
                            );
                        }
                    }
                }
            }

            if ( $highlighted_paragraph !== $paragraph ) {
                if ( ! $found_first_keyword ) {
                    $highlighted_paragraph = preg_replace(
                        '/<span class="laci-text-highlight">(.+?)<\/span>/',
                        '<span class="laci-text-highlight active-highlight">$1</span>',
                        $highlighted_paragraph,
                        1
                    );
                    $found_first_keyword   = true;
                }

                $excerpts[] = trim( $highlighted_paragraph );
            }
        }

        return [
            'excerpts'             => $excerpts,
            'total_keywords_fined' => $total_keywords_fined,
        ];
    }
}
