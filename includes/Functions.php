<?php
use LACI_InternalLinks\Utils\Helpers;

if ( ! function_exists( 'laci_kses_post' ) ) {
    /**
     * The function laci_kses_post sanitizes HTML content using the allowed HTML tags defined in the
     * Helpers class.
     *
     * @param html The  parameter is the input string that you want to sanitize and allow only
     * certain HTML tags and attributes.
     *
     * @return the result of the wp_kses() function, which is the sanitized version of the
     * parameter using the  array as the allowed HTML tags and attributes.
     */
    function laci_kses_post( $html ) {
        $allowed_html = Helpers::wp_kses_allowed_html();
        return wp_kses( $html, $allowed_html );
    }
}


if ( ! function_exists( 'laci_kses_post_e' ) ) {
    /**
     * The function `laci_kses_post_e` echoes the HTML content after sanitizing it using the allowed
     * HTML tags defined in the `Helpers::wp_kses_allowed_html()` method.
     *
     * @param html The  parameter is the content that you want to sanitize and filter using the
     * wp_kses() function. It could be any HTML content that you want to ensure is safe and free from
     * any potentially harmful or malicious code.
     */
    function laci_kses_post_e( $html ) {
        $allowed_html = Helpers::wp_kses_allowed_html();
        echo wp_kses( $html, $allowed_html );
    }
}

if ( ! function_exists( 'laci_delete_shortcode' ) ) {
    /**
     * The function `laci_delete_shortcode` removes the shortcode from the post content.
     *
     */
    function laci_delete_shortcode() {
        global $wpdb;

        // Pattern to match the entire Gutenberg block containing the shortcode
        $pattern = '<!-- wp:shortcode -->\s*\[laci_related_post_content[^\]]*\]\s*<!-- \/wp:shortcode -->';

        // Update the posts' content by removing the matched blocks
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->posts} SET post_content = REGEXP_REPLACE(post_content, %s, '')",
                $pattern
            )
        );
    }
}
