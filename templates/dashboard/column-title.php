<?php
defined( 'ABSPATH' ) || exit;
?>

<span class="laci-row-title-content">
    <a class="laci-row-title row-title" href="<?php echo esc_attr( $edit_link ); ?>"><?php echo esc_attr( $item->post_title ); ?></a>
    <span class="laci-text-tooltip"><?php echo esc_attr( $item->post_title ); ?></span>
</span>
<span class="laci-post-type">[<?php echo esc_attr( get_post_type( $item->ID ) ); ?>]</span>
<div class="laci-title-action-group">
    <span class="laci-title-action-item laci-link-assistant-item">
        <span class="dashicons dashicons-search"></span>
        <a class="link-assistant" target="_blank" href="<?php echo esc_attr( $link_assistant ); ?>"><?php esc_html_e( 'Internal Link Opportunities', 'laci-link-cluster' ); ?></a>
    </span>
    <span class="laci-title-action-item laci-update-internal-link-item">
        <span class="dashicons dashicons-image-rotate"></span>
        <a class="update-internal-link" data-id="<?php echo esc_attr( $item->ID ); ?>"><?php esc_html_e( 'Update Link Count', 'laci-link-cluster' ); ?></a>
    </span>
    <span class="laci-title-action-item laci-update-focus-keywords">
        <span class="dashicons dashicons-edit"></span>
        <a class="laci-action-update-focus-keywords" data-id="<?php echo esc_attr( $item->ID ); ?>"><?php esc_html_e( 'Focus Keywords', 'laci-link-cluster' ); ?></a>
    </span>
</div>

