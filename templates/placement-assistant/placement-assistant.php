<?php
defined( 'ABSPATH' ) || exit;
use LACI_InternalLinks\Controllers\LinkAssistantController;

$post_table = LinkAssistantController::get_instance();
$post_table->prepare_items();

$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
    wp_die( 'Security check' );
}

$post_id_data = isset( $_REQUEST['post_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_id'] ) ) : '';
$post_title   = get_the_title( $post_id_data );
$post_link    = get_permalink( $post_id_data );

$key_words         = get_post_meta( $post_id_data, 'laci_list_key_word', true );
$key_words_implode = is_array( $key_words ) ? implode( ',', $key_words ) : '';

$taxonomy_data = get_option( 'laci_internallinks_taxonomy', 'category' );
$taxonomy_data = get_taxonomy( $taxonomy_data );

?>
<div class="laci-laci-placement-assistant-container">
    <div class="laci-laci-placement-assistant-update-pro">
        <div class="laci-update-pro-text">
            <a class="button button-primary laci-buy-pro" href="https://linkandcluster.com/" target="_blank">
                <?php esc_html_e( 'Note: This is just a demo. To get it please buy our pro version', 'laci-link-cluster' ); ?>
            </a>
        </div>
    </div>

    <div class="laci-placement-assistant">
        <div class="wrap">
            <div class="laci-placement-assistant-title" data-link="<?php echo esc_attr( $post_link ); ?>" data-title="<?php echo esc_attr( $post_title ); ?>" data-id="<?php echo esc_attr( $post_id_data ); ?>" data-keywords="<?php echo esc_attr( $key_words_implode ); ?>">
                <h1><?php echo ( esc_html__( 'Link Assistant to find Link Opportunities for: ', 'laci-link-cluster' ) . esc_html( $post_title ) ); ?></h1>
            </div>
            <div>
                <?php $post_table->display(); ?>
            </div>
            <div class="laci-search-group">
                <div class="laci-search-control">
                    <input type="text" class="laci-search-input-control" placeholder="key_word1, key_word2,..." style="width:100%;height:50px" data-id="<?php echo esc_html( $post_id_data ); ?>">
                    <p class="laci-insert-main-key-word"><?php esc_html_e( '[ INSERT MAIN KEYWORDS FOR THE POST ]', 'laci-link-cluster' ); ?></p>
                </div>
                <button class="button laci-search-button"><?php esc_html_e( 'Search', 'laci-link-cluster' ); ?></button>
            </div>
            <div class="laci-search-text"><?php esc_html_e( 'Search Results:', 'laci-link-cluster' ); ?></div>
            <div class="laci-search-results-group">
                <div class="laci-search-results__same-category">
                    <div class="laci-search-results__same-category__title">
                        <p class="laci-text-title">
                            <?php
                            // translators: %s: taxonomy label
                            echo sprintf( esc_html__( 'SAME %s / CLUSTER: IN-TEXT LINK OPPORTUNITIES', 'laci-link-cluster' ), esc_html( strtoupper( $taxonomy_data->label ) ) );
                            ?>
                        </p>
                    </div>
                    <div class="laci-search-results__same-category__content">

                    </div>
                    <div class="laci-loading laci-updating-message" style="display: none;"></div>
                    <div class="laci-search-results__same-category__load-more <?php echo esc_attr( $class_name ); ?>">
                        <button class="button laci-load-more-same-category" data-post-id="" data-key-word="" data-max-pages=""><?php esc_html_e( 'Load More', 'laci-link-cluster' ); ?></button>
                    </div>
                </div>

                <div class="laci-search-results__diff-category">
                    <div class="laci-search-results__diff-category__title">
                        <p class="laci-text-title">
                            <?php
                            // translators: %s: taxonomy label
                            echo sprintf( esc_html__( 'OTHER %s / CLUSTER: IN-TEXT LINK OPPORTUNITIES', 'laci-link-cluster' ), esc_html( strtoupper( $taxonomy_data->label ) ) );
                            ?>
                        </p>
                    </div>
                    <div class="laci-search-results__diff-category__content">

                    </div>
                    <div class="laci-loading laci-updating-message" style="display: none;"></div>
                    <div class="laci-search-results__diff-category__load-more <?php echo esc_attr( $class_name_diff ); ?>">
                        <button class="button laci-load-more-diff-category" data-post-id="" data-key-word="" data-max-pages=""><?php esc_html_e( 'Load More', 'laci-link-cluster' ); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
