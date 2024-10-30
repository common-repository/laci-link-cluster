<?php
defined( 'ABSPATH' ) || exit;
if ( isset( $_POST ) && ! empty( $_POST['laci-form-submit'] ) && ! empty( $_POST['laci-settings-security-token'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['laci-settings-security-token'] ) ), 'laci-settings-security-token' ) ) {
    wp_die();
}

$active_tab_settings         = empty( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] != 'cus_related_box' && $_GET['tab'] != 'import_key_words' ) ? 'nav-tab-active' : '';
$active_tab_cus_related_box  = isset( $_GET['tab'] ) && $_GET['tab'] === 'cus_related_box' ? 'nav-tab-active' : '';
$active_tab_import_key_words = isset( $_GET['tab'] ) && $_GET['tab'] === 'import_key_words' ? 'nav-tab-active' : '';
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Settings', 'laci-link-cluster' ); ?></h1>
    <p><?php esc_html_e( 'Welcome to the WP Internal Links plugin settings page.', 'laci-link-cluster' ); ?></p>

    <div class="laci-settings-container">
        <h1 class="nav-tab-wrapper hide-if-no-js">
            <a href="<?php echo esc_attr( admin_url( 'admin.php?page=laci-internal-links-settings&tab=settings' ) ); ?>" class="nav-tab <?php echo esc_attr( $active_tab_settings ); ?>"><?php esc_html_e( 'Settings', 'laci-link-cluster' ); ?></a>
        </h1>
        <div class="laci-settings-content">
            <?php
                require_once LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/settings/settings.php';
            ?>
        </div>
    </div>
</div>
