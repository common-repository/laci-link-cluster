<?php
$laci_related_box__content = get_option( 'laci_related_box__content', '' );
$laci_related_box__title   = get_option( 'laci_related_box__title', '0' );
$laci_related_box__content = str_replace( '[laci_post_title_link]', $related_post_title, $laci_related_box__content );

ob_start();
require LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/settings/related-box-html.php';
$div_to_insert = ob_get_clean();

$insert_after = '<!-- /wp:paragraph -->';

$list_key_word = explode( ',', $key_words );

$key_word = $list_key_word[0];

$firt_location = stripos( $post_content, $key_word ) ? stripos( $post_content, $key_word ) : strlen( $post_content );

foreach ( $list_key_word as $item ) {
    $location = stripos( $post_content, trim( $item ) );
    if ( $location && ( $location < $firt_location ) ) {
        $key_word = $item;
    }
}

$paragraphs = explode( $insert_after, $post_content );

foreach ( $paragraphs as $index => $paragraph ) {
    if ( stripos( $paragraph, $key_word ) !== false ) {
        $paragraphs[ $index ] .= $div_to_insert;
        break;
    }
}

$post_content = implode( $insert_after, $paragraphs );

$pattern      = '/(<!--\s?wp:shortcode\s?-->)(.*?)(<!--\s?\/wp:shortcode\s?-->)/is';
$replacement  = '$1<p class="laci-shortcode">$2</p>$3';
$post_content = preg_replace( $pattern, $replacement, $post_content );
?>

<div class="laci-post-content-container">
    <?php laci_kses_post_e( $post_content ); ?>
</div>

