<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap laci-dashboard-page">
    <div class="logo">
        <img src="<?php echo esc_url( LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/images/logo.png' ); ?>" alt="WP Internal Links">
        <h2><?php esc_html_e( 'Report & Cluster Cockpit', 'laci-link-cluster' ); ?></h2>
    </div>

    <div> <?php echo do_shortcode( '[laci_internal_links_post_list]' ); ?></div>
</div>
