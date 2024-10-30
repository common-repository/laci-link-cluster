<?php
defined( 'ABSPATH' ) || exit;

use  LACI_InternalLinks\Controllers\SettingsController;

$setting = SettingsController::get_instance();

if ( isset( $_POST['laci-settings-security-token'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['laci-settings-security-token'] ) ), 'laci-settings-security-token' ) ) {
    update_option( 'laci_num_item_dash', sanitize_text_field( isset( $_POST['num_item_dash'] ) ? wp_unslash( $_POST['num_item_dash'] ) : '' ) );
    update_option( 'laci_num_item_la', sanitize_text_field( isset( $_POST['num_item_la'] ) ? wp_unslash( $_POST['num_item_la'] ) : '' ) );
    update_option( 'laci_delete_shortcode_in_deactivate', isset( $_POST['delete_shortcode_in_deactivate'] ) && sanitize_text_field( wp_unslash( $_POST['delete_shortcode_in_deactivate'] ) ) === 'on' ? 1 : 0 );
    update_option( 'laci_delete_shortcode_in_delete', isset( $_POST['delete_shortcode_in_delete'] ) && sanitize_text_field( wp_unslash( $_POST['delete_shortcode_in_delete'] ) ) === 'on' ? 1 : 0 );
    update_option( 'laci_internallinks_taxonomy', sanitize_text_field( isset( $_POST['laci_internallinks_taxonomy'] ) ? wp_unslash( $_POST['laci_internallinks_taxonomy'] ) : '' ) );
}

$laci_num_item_dash                  = get_option( 'laci_num_item_dash', '50' );
$laci_num_item_la                    = get_option( 'laci_num_item_la', '50' );
$laci_delete_shortcode_in_deactivate = get_option( 'laci_delete_shortcode_in_deactivate', 0 );
$laci_delete_shortcode_in_delete     = get_option( 'laci_delete_shortcode_in_delete', 0 );
$taxonomy_data                       = get_option( 'laci_internallinks_taxonomy', 'category' );
$taxonomies                          = get_taxonomies( [ 'public' => true ], 'objects' );

$updated_for_taxonomy = get_option( 'laci_internallinks_updated_for_taxonomy', 'category' );

if ( $taxonomy_data !== $updated_for_taxonomy ) {
    ?>
<div class="notice notice-warning laci-notice" style="display: block;">
    <p><?php esc_html_e( 'Please note that changing the applicable taxonomy will require', 'laci-link-cluster' ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=laci_internal_links_dashboard' ) ); ?>"><?php esc_html_e( 'updating the link counts', 'laci-link-cluster' ); ?></a> <?php esc_html_e( 'to ensure internal links are properly counted within their respective clusters.', 'laci-link-cluster' ); ?></p>
</div>
    <?php
}
?>
<form class="laci-settings-form" method="POST">
    <input type='hidden' name='laci-settings-security-token' value='<?php echo esc_attr( wp_create_nonce( 'laci-settings-security-token' ) ); ?>'>  
    <table class="form-table">
        <tr class="laci-setting-taxonomies">
            <th><?php esc_html_e( 'Taxonomies', 'laci-link-cluster' ); ?></th>
            <td>
                <select name="laci_internallinks_taxonomy">
                    <?php foreach ( $taxonomies as $tax_item ) : ?>
                        <option value="<?php echo esc_attr( $tax_item->name ); ?>" <?php selected( $taxonomy_data, $tax_item->name ); ?>>
                            <?php echo esc_html( $tax_item->labels->singular_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Number of items per page in the dashboard', 'laci-link-cluster' ); ?></th>
            <td>
                <input name="num_item_dash" type="number" id="num_item_dash" value="<?php echo esc_attr( $laci_num_item_dash ); ?>" min="1" max="900">
            </td>
        </tr>
        <tr class="laci-setting-item-per-page">
            <th><?php esc_html_e( 'Number of items per page in Link Assistant', 'laci-link-cluster' ); ?></th>
            <td>
                <input name="num_item_la" type="number" id="num_item_la" value="<?php echo esc_attr( $laci_num_item_la ); ?>" min="1" max="100">
                <p style="margin-top: 5px;"> <?php esc_html_e( 'Note: ', 'laci-link-cluster' ); ?><code><?php esc_html_e( 'Max is 100', 'laci-link-cluster' ); ?></code></p>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Delete shortcode when deactivate the plugin', 'laci-link-cluster' ); ?></th>
            <td>
                <label class="laci-switch" for="delete_shortcode_in_deactivate">
                    <input name="delete_shortcode_in_deactivate" type="checkbox" id="delete_shortcode_in_deactivate" <?php echo $laci_delete_shortcode_in_deactivate ? 'checked' : ''; ?>>
                    <div class="slider round"></div>
                </label>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Delete shortcode when delete the plugin', 'laci-link-cluster' ); ?></th>
            <td>
                <label class="laci-switch" for="delete_shortcode_in_delete">
                    <input name="delete_shortcode_in_delete" type="checkbox" id="delete_shortcode_in_delete" <?php echo $laci_delete_shortcode_in_delete ? 'checked' : ''; ?>>
                    <div class="slider round"></div>
                </label>
            </td>
        </tr>
    </table> 
    <?php
    submit_button();
    ?>
</form>
