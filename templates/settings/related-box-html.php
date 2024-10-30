<?php
defined( 'ABSPATH' ) || exit;

$laci_related_box__title_color   = get_option( 'laci_related_box__title_color', '#ffffff' );
$laci_related_box__content_color = get_option( 'laci_related_box__content_color', '#ffffff' );
$laci_related_box__bg_color      = get_option( 'laci_related_box__bg_color', '#ffffff' );
$laci_related_box__bd_color      = get_option( 'laci_related_box__bd_color', '#ffffff' );
$laci_related_box__bd_radius     = get_option( 'laci_related_box__bd_radius', '5' );

$laci_related_box__padding_top    = get_option( 'laci_related_box__padding_top', '0' );
$laci_related_box__padding_right  = get_option( 'laci_related_box__padding_right', '0' );
$laci_related_box__padding_bottom = get_option( 'laci_related_box__padding_bottom', '0' );
$laci_related_box__padding_left   = get_option( 'laci_related_box__padding_left', '0' );

$laci_related_box__margin_top    = get_option( 'laci_related_box__margin_top', '0' );
$laci_related_box__margin_right  = get_option( 'laci_related_box__margin_right', '0' );
$laci_related_box__margin_bottom = get_option( 'laci_related_box__margin_bottom', '0' );
$laci_related_box__margin_left   = get_option( 'laci_related_box__margin_left', '0' );

$laci_related_box__image        = get_option( 'laci_related_box__image', '' );
$laci_related_box__image_width  = get_option( 'laci_related_box__image_width', '100' );
$laci_related_box__image_height = get_option( 'laci_related_box__image_height', '100' );
?>
<style>
    .laci-related-box-container {
        padding: <?php echo esc_html( $laci_related_box__padding_top ); ?>px <?php echo esc_html( $laci_related_box__padding_right ); ?>px <?php echo esc_html( $laci_related_box__padding_bottom ); ?>px <?php echo esc_html( $laci_related_box__padding_left ); ?>px;
        background: <?php echo esc_html( $laci_related_box__bg_color ); ?>;
        border-radius: <?php echo esc_html( $laci_related_box__bd_radius ); ?>px;
        color: <?php echo esc_html( $laci_related_box__title_color ); ?>;
        border: 1px solid <?php echo esc_html( $laci_related_box__bd_color ); ?>;
    }
    .laci-related-box-text {
        color: <?php echo esc_html( $laci_related_box__content_color ); ?>;
    }

    .laci-related-box-container {
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .laci-related-box-image {
        flex-shrink: 0;
        padding-right: 20px;
    }

    .laci-related-box-image img {
        display: block;
        max-width: 100%;
    }

    .laci-related-box-content {
        flex: 1;
    }
</style>

<div class="laci-related-box-container">
    <?php if ( ! empty( $laci_related_box__image ) ) : ?>
        <div class="laci-related-box-image">
            <img width="<?php echo esc_attr( $laci_related_box__image_width ); ?>" height="<?php echo esc_attr( $laci_related_box__image_height ); ?>" src="<?php echo esc_url( $laci_related_box__image ); ?>" alt="<?php esc_html_e( 'Related Box Image', 'laci-link-cluster' ); ?>" style="max-width: 100%;" />
        </div>
    <?php endif; ?>
    <div class="laci-related-box-content">
        <?php laci_kses_post_e( $laci_related_box__title ); ?>
        <span class="laci-related-box-text">
            <?php echo do_shortcode( $laci_related_box__content ); ?>
        </span>
    </div>
</div>
