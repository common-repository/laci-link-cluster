<?php
defined( 'ABSPATH' ) || exit;

use LACI_InternalLinks\Controllers\CreatePostListTableController;

$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
    wp_die( 'Security check' );
}

$post_list_table = CreatePostListTableController::get_instance();

$post_list_table->prepare_items();

$all_count    = $post_list_table->get_post_count_by_type();
$page_count   = $post_list_table->get_post_count_by_type( 'page' );
$post_count   = $post_list_table->get_post_count_by_type( 'post' );
$other_count  = $post_list_table->get_post_count_by_type( '' ); // Other post type
$orphan_count = $post_list_table->count_orphan_pages();

$inbound_link_entire_site_quantity_from = isset( $_GET['inbound_link_entire_site_quantity_from'] ) ? sanitize_text_field( wp_unslash( $_GET['inbound_link_entire_site_quantity_from'] ) ) : '';
$inbound_link_entire_site_quantity_to   = isset( $_GET['inbound_link_entire_site_quantity_to'] ) ? sanitize_text_field( wp_unslash( $_GET['inbound_link_entire_site_quantity_to'] ) ) : '';

$outbound_link_entire_site_quantity_from = isset( $_GET['outbound_link_entire_site_quantity_from'] ) ? sanitize_text_field( wp_unslash( $_GET['outbound_link_entire_site_quantity_from'] ) ) : '';
$outbound_link_entire_site_quantity_to   = isset( $_GET['outbound_link_entire_site_quantity_to'] ) ? sanitize_text_field( wp_unslash( $_GET['outbound_link_entire_site_quantity_to'] ) ) : '';

$inbound_links_in_category_quantity_from = isset( $_GET['inbound_links_in_category_quantity_from'] ) ? sanitize_text_field( wp_unslash( $_GET['inbound_links_in_category_quantity_from'] ) ) : '';
$inbound_links_in_category_quantity_to   = isset( $_GET['inbound_links_in_category_quantity_to'] ) ? sanitize_text_field( wp_unslash( $_GET['inbound_links_in_category_quantity_to'] ) ) : '';

$outbound_links_in_category_quantity_from = isset( $_GET['outbound_links_in_category_quantity_from'] ) ? sanitize_text_field( wp_unslash( $_GET['outbound_links_in_category_quantity_from'] ) ) : '';
$outbound_links_in_category_quantity_to   = isset( $_GET['outbound_links_in_category_quantity_to'] ) ? sanitize_text_field( wp_unslash( $_GET['outbound_links_in_category_quantity_to'] ) ) : '';

$search_value = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

$get_post_type = isset( $_GET['get_post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['get_post_type'] ) ) : 'all';
$only_orphan   = isset( $_GET['only_orphan'] ) ? sanitize_text_field( wp_unslash( $_GET['only_orphan'] ) ) : '';
?>

<div class="wrap">
    <div class="wrap-top-nav">
        <ul class="subsubsub" style="margin-bottom:10px">
            <?php
            $links = [
                'all'    => [ esc_html__( 'All', 'laci-link-cluster' ), $all_count ],
                'page'   => [ esc_html__( 'Only Page', 'laci-link-cluster' ), $page_count ],
                'post'   => [ esc_html__( 'Only Post', 'laci-link-cluster' ), $post_count ],
                'other'  => [ esc_html__( 'Other Post Type', 'laci-link-cluster' ), $other_count ],
                'orphan' => [ esc_html__( 'Only Orphan Page', 'laci-link-cluster' ), $orphan_count ],
            ];

            foreach ( $links as $key => $label_count ) {
                $class = ( $get_post_type === $key || ( $only_orphan && 'orphan' === $key ) ) ? 'class="current"' : '';
                $href  = add_query_arg(
                    array_merge(
                        $_REQUEST,
                        [
                            'get_post_type' => 'orphan' !== $key ? $key : '',
                            'only_orphan'   => 'orphan' === $key ? '1' : '',
                            'page'          => 'laci_internal_links_dashboard',
                        ]
                    )
                );
                // $href  = remove_query_arg(
                //     [
                //         'category',
                //         's',
                //         'inbound_link_entire_site_quantity_from',
                //         'inbound_link_entire_site_quantity_to',
                //         'outbound_link_entire_site_quantity_from',
                //         'outbound_link_entire_site_quantity_to',
                //         'inbound_links_in_category_quantity_from',
                //         'inbound_links_in_category_quantity_to',
                //         'outbound_links_in_category_quantity_from',
                //         'outbound_links_in_category_quantity_to',
                //     ],
                //     $href
                // );
                echo '<li><a href="' . esc_url( $href ) . '" ' . wp_kses_post( $class ) . '>' . esc_attr( $label_count[0] ) . ' <span class="count">(' . esc_attr( $label_count[1] ) . ')</span></a> | </li>';
            }
            ?>
        </ul>
       
    </div>
    <div class="clear"></div>
    <div class="wrap-search-and-filters">
        <form method="get" class="wrap-filters laci-wrap-from">
            <input type="hidden" name="get_post_type" value="<?php echo esc_html( $get_post_type ); ?>">
            <input type="hidden" name="only_orphan" value="<?php echo esc_html( $only_orphan ); ?>">
            <input type="hidden" name="page" value="laci_internal_links_dashboard">
            <input type="hidden" name="nonce" value="<?php echo wp_kses_post( wp_create_nonce( 'laci-internal-links-nonce' ) ); ?>">
            <input type="search" id="search_id-search-input" name="s" value="<?php echo esc_html( $search_value ); ?>">
            <input type="submit" id="search-submit" class="button" value="Search">
            <?php
            $taxonomy_val = get_option( 'laci_internallinks_taxonomy', 'category' );

            wp_dropdown_categories(
                [
                    'taxonomy'        => $taxonomy_val,
                    'hide_empty'      => false,
                    'name'            => 'category',
                    'id'              => 'category',
                    'show_option_all' => __( 'All Terms', 'laci-link-cluster' ),
                    'selected'        => isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0,
                    'hierarchical'    => true,
                    'value_field'     => 'term_id',
                ]
            );
            ?>
            <input type="submit" value="Filter" class="button">
            <div class="clear"></div>
            <input type="hidden" name="page" value="laci_internal_links_dashboard">
            <input type="hidden" name="nonce" value="<?php echo wp_kses_post( wp_create_nonce( 'laci-internal-links-nonce' ) ); ?>">
            <div class="laci-filter-link-group">
                <div>
                    <div class="laci-filter-link-item">
                        <label class="laci-label-filter-number" for="quantity"><?php esc_html_e( 'INBOUND LINKS ENTIRE SITE', 'laci-link-cluster' ); ?></label>
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="inbound_link_entire_site_quantity_from" min="0" max="9000" value="<?php echo esc_attr( $inbound_link_entire_site_quantity_from ); ?>" placeholder="from">
                        <input class="laci-filter-link"  style="width: 70px" type="number" id="quantity" name="inbound_link_entire_site_quantity_to" min="0" max="9000" value="<?php echo esc_attr( $inbound_link_entire_site_quantity_to ); ?>"  placeholder="to">
    
                    </div>
                    <div class="laci-filter-link-item">
                        <label class="laci-label-filter-number" for="quantity"><?php esc_html_e( 'OUTBOUND LINKS ENTIRE SITE', 'laci-link-cluster' ); ?></label>
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="outbound_link_entire_site_quantity_from" min="0" max="9000" value="<?php echo esc_attr( $outbound_link_entire_site_quantity_from ); ?>" placeholder="from">
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="outbound_link_entire_site_quantity_to" min="0" max="9000" value="<?php echo esc_attr( $outbound_link_entire_site_quantity_to ); ?>"  placeholder="to">
                    </div>
                </div>
                <div>
                    <div class="laci-filter-link-item">
                        <label class="laci-label-filter-number" for="quantity"><?php esc_html_e( 'INBOUND LINKS SAME CLUSTER', 'laci-link-cluster' ); ?></label>
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="inbound_links_in_category_quantity_from" min="0" max="9000" value="<?php echo esc_attr( $inbound_links_in_category_quantity_from ); ?>"  placeholder="from">
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="inbound_links_in_category_quantity_to" min="0" max="9000" value="<?php echo esc_attr( $inbound_links_in_category_quantity_to ); ?>"  placeholder="to">
                    </div>
                    <div class="laci-filter-link-item">
                        <label class="laci-label-filter-number" for="quantity"><?php esc_html_e( 'OUTBOUND LINKS SAME CLUSTER', 'laci-link-cluster' ); ?></label>
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="outbound_links_in_category_quantity_from" min="0" max="9000" value="<?php echo esc_attr( $outbound_links_in_category_quantity_from ); ?>" placeholder="from">
                        <input class="laci-filter-link" style="width: 70px" type="number" id="quantity" name="outbound_links_in_category_quantity_to" min="0" max="9000" value="<?php echo esc_attr( $outbound_links_in_category_quantity_to ); ?>" placeholder="to">
                    </div>
                </div>
            </div>
            <div>
                <input type="submit" value="Filter Link" class="button laci-filter-link-group">
                <input type="submit" value="Clear ALL" class="button laci-filter-link-group-clear-all">
            </div>
        </form>
    </div>
    <div class="clear"></div>
  
    <div class="laci-update-post-container">
            <span class="laci-action-update-post-to-db">
                <span class="dashicons dashicons-update laci-icon-update-post"></span>
                <?php esc_html_e( 'Update Link Count for the Entire Site', 'laci-link-cluster' ); ?>
            </span>
            <span class="laci-percent-updated"><span class="laci-percent-number">0</span><span>% Updated</span></span>
            <?php if ( ! empty( get_option( 'laci_last_updated_date' ) ) && ! empty( get_option( 'laci_last_updated_time' ) ) ) { ?>
            <span class="laci-time-updated">[<?php esc_html_e( 'Last Update:', 'laci-link-cluster' ); ?> <?php echo esc_html( get_option( 'laci_last_updated_time' ) ); ?> on <?php echo esc_html( get_option( 'laci_last_updated_date' ) ); ?>]</span>
            <?php } ?> 
    </div>
   
    <div>
        <?php $post_list_table->display(); ?>
    </div>
</div>
