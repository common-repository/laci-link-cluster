<?php
    defined( 'ABSPATH' ) || exit;
?>

<form method="POST" class="wrap-filters laci-wrap-from">
    <div class="laci-license-card">
        <div class="laci-license-card-header">
            <div class="laci-license-card-title-wrapper">
                <h3 class="laci-license-card-title laci-license-card-header-item">
                    <?php esc_html_e( 'Link and Cluster', 'laci-link-cluster' ); ?>
                    <span class="laci-license-badge success"><?php esc_html_e( 'Active', 'laci-link-cluster' ); ?></span>
                </h3>
            </div>
        </div>
        <div class="laci-license-card-body">
            <label><?php echo esc_html( 'Your license key:' ); ?></label>
            <div class="laci-license-input-row">
                <?php wp_nonce_field( 'laci-licenses-nonce', 'laci-licenses-nonce' ); ?>
                <input type="password" disabled value="<?php echo esc_attr( get_option( 'laci_license_key' ) ); ?>">
                <input type="hidden" name="laci-license-input" value="">
                <input type="hidden" name="laci-license-deactivate-input" value="">
                <button class="laci-license-button laci-remove-license">
                    <span><?php esc_html_e( 'Deactivate', 'laci-link-cluster' ); ?></span>
                    <span class="activate-loading sync-loading">
                        <svg
                        data-v-7957300f=""
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        >
                            <path
                            data-v-7957300f=""
                            d="M21.66 10.37a.62.62 0 00.07-.19l.75-4a1 1 0 00-2-.36l-.37 2a9.22 9.22 0 00-16.58.84 1 1 0 00.55 1.3 1 1 0 001.31-.55A7.08 7.08 0 0112.07 5a7.17 7.17 0 016.24 3.58l-1.65-.27a1 1 0 10-.32 2l4.25.71h.16a.93.93 0 00.34-.06.33.33 0 00.1-.06.78.78 0 00.2-.11l.08-.1a1.07 1.07 0 00.14-.16.58.58 0 00.05-.16zM19.88 14.07a1 1 0 00-1.31.56A7.08 7.08 0 0111.93 19a7.17 7.17 0 01-6.24-3.58l1.65.27h.16a1 1 0 00.16-2L3.41 13a.91.91 0 00-.33 0H3a1.15 1.15 0 00-.32.14 1 1 0 00-.18.18l-.09.1a.84.84 0 00-.07.19.44.44 0 00-.07.17l-.75 4a1 1 0 00.8 1.22h.18a1 1 0 001-.82l.37-2a9.22 9.22 0 0016.58-.83 1 1 0 00-.57-1.28z"
                            ></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</form>
