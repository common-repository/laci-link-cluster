<?php
defined( 'ABSPATH' ) || exit;

use LACI_InternalLinks\Controllers\CreatePostListTableController;
use LACI_InternalLinks\Controllers\InternalLinksController;

if ( empty( $result_same_cat ) ) {
    return;
}
$posts_data   = $result_same_cat['posts_data'];
$post_title   = $result_same_cat['post_title'];
$post_id_main = $result_same_cat['post_id'];
?>

<?php
foreach ( $posts_data as $post_data ) :
    $post_id_data    = isset( $post_data['post_id'] ) ? $post_data['post_id'] : '';
    $post_title_data = isset( $post_data['title'] ) ? $post_data['title'] : '';
    $contents        = isset( $post_data['excerpts'] ) ? $post_data['excerpts'] : '';
    $edit_post_link  = get_edit_post_link( $post_id_data );
    $total_results   = isset( $post_data['total_keywords_fined'] ) ? $post_data['total_keywords_fined'] : '0';

    if ( empty( $contents ) ) {
        continue;
    }
    ?>
    <div class="laci-container-same-cat-item laci-container-cat-item" data-id="<?php echo esc_html( $post_id_data ); ?>">
        <div class="laci-left-section">
            <div class="laci-category">
               <h3 style="margin-top:0"><?php echo esc_html( $taxonomy_data->label ); ?></h3>
               <span><?php laci_kses_post_e( CreatePostListTableController::get_hierarchical_categories( $post_id_data ) ); ?></span>
            </div>
        </div>
        <div class="laci-mid-section" data-current-excerpt="0">
            <div class="laci-name-post" data-title="<?php echo esc_html( $post_title_data ); ?>">
                <a href="<?php echo esc_url( $edit_post_link ); ?>" target="_blank"><?php echo esc_html( $post_title_data ); ?></a>
            </div>
            <div class="laci-post-content" >
                <?php
                foreach ( $contents as $key => $content ) :
                    $style = ( 0 === $key ) ? 'display: block;' : 'display: none;';
                    ?>
                    <div class="laci-post-content__excerpt" data-num="<?php echo esc_html( $key ); ?>" style="<?php echo esc_html( $style ); ?>">
                        <?php laci_kses_post_e( $content ); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="laci-same-cat-action">
                <span class="laci-same-cat-num-results"><?php echo ( esc_html( $total_results ) . esc_html__( ' - Search Results', 'laci-link-cluster' ) ); ?></span>
                <button class="button laci-same-cat-content-previous" disabled><?php esc_html_e( '<< Previous', 'laci-link-cluster' ); ?></button>
                <button class="button laci-same-cat-content-next" <?php echo esc_attr( $total_results < 2 ? 'disabled' : '' ); ?>><?php esc_html_e( 'Next >>', 'laci-link-cluster' ); ?></button>
                <button class="button laci-same-cat-content-edit"><?php esc_html_e( 'Edit & Place Link', 'laci-link-cluster' ); ?></button>
                <button class="button laci-add-related-box"><?php esc_html_e( 'Add Related Box', 'laci-link-cluster' ); ?></button>
            </div>
        </div>
        <div class="laci-right-section">
            <div><?php echo esc_html__( 'Outgoing Links to', 'laci-link-cluster' ) . esc_html( $post_title ); ?>: <?php echo esc_html( InternalLinksController::count_outbound_links_between_posts( $post_id_data, $post_id_main ) ); ?></div>
            <div><?php echo esc_html__( 'Outbound Internal Links:', 'laci-link-cluster' ) . esc_html( count( InternalLinksController::get_outbound_internal_links( $post_id_data ) ) ); ?></div>
            <div><?php echo esc_html__( 'Inbound Internal Links:', 'laci-link-cluster' ) . esc_html( count( InternalLinksController::get_inbound_internal_links( $post_id_data ) ) ); ?></div>
        </div>
    </div>
<?php endforeach; ?>
