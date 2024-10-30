<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

if ( get_option( 'laci_delete_shortcode_in_delete', 0 ) ) {
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
