<?php
defined( 'ABSPATH' ) || exit;
use LACI_InternalLinks\Controllers\SettingsController;

if ( isset( $_POST['laci-settings-security-token'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['laci-settings-security-token'] ) ), 'laci-settings-security-token' ) ) {
    if ( isset( $_POST['reset_to_default'] ) ) {
        SettingsController::set_default_value();
    } elseif ( isset( $_POST['submit'] ) ) {
        SettingsController::update_option_related_box( $_POST );
    }
}

$laci_related_box__title    = wp_unslash( get_option( 'laci_related_box__title', '0' ) );
$laci_related_box__title_id = 'laci_related_box__title_id';

$laci_related_box__content    = wp_unslash( get_option( 'laci_related_box__content', '' ) );
$laci_related_box__content_id = 'laci_related_box__content_id';
$settings                     = [
    'textarea_name' => 'custom-related-box-content-editor',
    'media_buttons' => true,
    'teeny'         => false,
    'quicktags'     => true,
    'tinymce'       => [
        'plugins'          => 'wordpress, wplink, wpeditimage',
        'toolbar1'         => 'undo redo | styleselect | bold italic fontsizeselect hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | mediaImageLibrary  | mediaimagelibrary fullscreen shortcodeList',
        'height'           => 180,
        'fontsize_formats' => '10px 12px 14px 16px 18px 20px 24px 28px 32px 36px 48px 60px 72px 96px',
        'content_css'      => LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/css/laci-editor-style.css',
    ],
];

$settings_related_box__title = [
    'textarea_name' => 'custom-related-box-title-editor',
    'media_buttons' => true,
    'teeny'         => false,
    'quicktags'     => true,
    'tinymce'       => [
        'plugins'          => 'wordpress, wplink, wpeditimage',
        'toolbar1'         => 'undo redo | styleselect | bold italic fontsizeselect hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | mediaImageLibrary  | mediaimagelibrary fullscreen shortcodeList',
        'height'           => 180,
        'fontsize_formats' => '10px 12px 14px 16px 18px 20px 24px 28px 32px 36px 48px 60px 72px 96px',
        'content_css'      => LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/css/laci-editor-style.css',
    ],
];
?>

<div class="laci-custom-related-box">
    <form class="laci-custom-related-box-form" method="POST">
        <input type='hidden' name='laci-settings-security-token' value='<?php echo esc_attr( wp_create_nonce( 'laci-settings-security-token' ) ); ?>'>  
        <h2><?php esc_html_e( 'Custom related box', 'laci-link-cluster' ); ?></h2>

        <table class="form-table">
            <tr>
                <th><?php esc_html_e( 'Title color', 'laci-link-cluster' ); ?></th>
                <td>
                    <input type="text" id="laci-title-color" name="laci_title_color" value="<?php echo esc_attr( get_option( 'laci_related_box__title_color', '#ffffff' ) ); ?>" class="laci-title-color__related-box" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Content color', 'laci-link-cluster' ); ?></th>
                <td>
                    <input type="text" id="laci-content-color" name="laci_content_color" value="<?php echo esc_attr( get_option( 'laci_related_box__content_color', '#ffffff' ) ); ?>" class="laci-content-color__related-box" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Background color', 'laci-link-cluster' ); ?></th>
                <td>
                    <input type="text" id="laci-bg-color" name="laci_bg_color" value="<?php echo esc_attr( get_option( 'laci_related_box__bg_color', '#ffffff' ) ); ?>" class="laci-bg-color__related-box" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Border color', 'laci-link-cluster' ); ?></th>
                <td>
                    <input type="text" id="laci-bd-color" name="laci_bd_color" value="<?php echo esc_attr( get_option( 'laci_related_box__bd_color', '#ffffff' ) ); ?>" class="laci-bd-color__related-box" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Border radius (px)', 'laci-link-cluster' ); ?></th>
                <td>
                    <input  style="width: 50px;" type="number" id="laci-bd-radius" name="laci_bd_radius" value="<?php echo esc_attr( get_option( 'laci_related_box__bd_radius', '5' ) ); ?>" class="laci-bd-radius__related-box" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Padding (px)', 'laci-link-cluster' ); ?></th>
                <td>
                    <div> 
                        <label for="laci-padding-box" class="laci-related-box-label">Top</label>
                        <input style="width: 50px;"  type="number" id="laci-padding-box" name="laci_pd_top" value="<?php echo esc_attr( get_option( 'laci_related_box__padding_top', '0' ) ); ?>" class="laci-pd-top__related-box" />
                        <label for="laci-padding-box" class="laci-related-box-label">Right</label>
                        <input style="width: 50px;"  type="number" id="laci-padding-box" name="laci_pd_right" value="<?php echo esc_attr( get_option( 'laci_related_box__padding_right', '0' ) ); ?>" class="laci-pd-right__related-box" />
                    </div>
                    <div style="margin-top: 10px"> 
                        <label for="laci-padding-box" class="laci-related-box-label">Bottom</label>
                        <input style="width: 50px;" type="number" id="laci-padding-box" name="laci_pd_bottom" value="<?php echo esc_attr( get_option( 'laci_related_box__padding_bottom', '0' ) ); ?>" class="laci-pd-bottom__related-box" />
                        <label for="laci-padding-box" class="laci-related-box-label">Left</label>
                        <input style="width: 50px;" type="number" id="laci-padding-box" name="laci_pd_left" value="<?php echo esc_attr( get_option( 'laci_related_box__padding_left', '0' ) ); ?>" class="laci-pd-left__related-box" />
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Margin (px)', 'laci-link-cluster' ); ?></th>
                <td>
                    <div>
                        <label for="laci-margin-box" class="laci-related-box-label">Top</label>
                        <input style="width: 50px;" type="number" id="laci-margin-box" name="laci_mg_top" value="<?php echo esc_attr( get_option( 'laci_related_box__margin_top', '0' ) ); ?>" class="laci-mg-top__related-box" />
                        <label for="laci-margin-box" class="laci-related-box-label">Right</label>
                        <input style="width: 50px;" type="number" id="laci-margin-box" name="laci_mg_right" value="<?php echo esc_attr( get_option( 'laci_related_box__margin_right', '0' ) ); ?>" class="laci-mg-right__related-box" />
                    </div>
                    <div style="margin-top: 10px">
                        <label for="laci-margin-box" class="laci-related-box-label">Bottom</label>
                        <input style="width: 50px;" type="number" id="laci-margin-box" name="laci_mg_bottom" value="<?php echo esc_attr( get_option( 'laci_related_box__margin_bottom', '0' ) ); ?>" class="laci-mg-bottom__related-box" />
                        <label for="laci-margin-box" class="laci-related-box-label">Left</label>
                        <input style="width: 50px;" type="number" id="laci-margin-box" name="laci_mg_left" value="<?php echo esc_attr( get_option( 'laci_related_box__margin_left', '0' ) ); ?>" class="laci-mg-left__related-box" />
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Related box image', 'laci-link-cluster' ); ?></th>
                <td>
                    <input type="text" id="laci-related-box-image" name="laci_related_box_image" value="<?php echo esc_attr( get_option( 'laci_related_box__image', '' ) ); ?>" style="width: 70%;" />
                    <button type="button" class="button" id="laci-related-box-image-upload"><?php esc_html_e( 'Select Image', 'laci-link-cluster' ); ?></button>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Image width(px)', 'laci-link-cluster' ); ?></th>
                <td>
                    <input  style="width: 100px;" type="number" id="laci-related-box-image-width" name="laci_related_box_image_width" value="<?php echo esc_attr( get_option( 'laci_related_box__image_width', '100' ) ); ?>" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Image height(px)', 'laci-link-cluster' ); ?></th>
                <td>
                    <input  style="width: 100px;" type="number" id="laci-related-box-image-height" name="laci_related_box_image_height" value="<?php echo esc_attr( get_option( 'laci_related_box__image_height', '100' ) ); ?>" />
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Title related box', 'laci-link-cluster' ); ?></th>
                <td><?php wp_editor( $laci_related_box__title, $laci_related_box__title_id, $settings_related_box__title ); ?>
                </td>
            </tr>
            <tr>   
                <th><?php esc_html_e( 'Content related box', 'laci-link-cluster' ); ?></th>
                <td>
                    <?php wp_editor( $laci_related_box__content, $laci_related_box__content_id, $settings ); ?>
                    <p style="margin-top: 5px;"> <?php esc_html_e( 'Note: ', 'laci-link-cluster' ); ?><code><?php esc_html_e( 'You can use the shortcode [laci_post_title_link] to display the title of the post.', 'laci-link-cluster' ); ?></code></p>
                </td>
            </tr>
            
        </table>
        <span class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></span>
        <span class="submit"><input type="submit" name="reset_to_default" id="reset_to_default" class="button button-primary" value="<?php esc_html_e( 'Reset to default', 'laci-link-cluster' ); ?>"></span>
        <script>
        jQuery(document).ready(function($) {
            $('#reset_to_default').on('click', function(e) {
                var confirmed = confirm('<?php esc_html_e( 'Are you sure you want to reset to default?', 'laci-link-cluster' ); ?>');
                if (!confirmed) {
                    e.preventDefault(); // Prevent form submission if the user cancels
                }
            });
        });
        </script>
    </form>

    <div class="laci-custom-related-box-review">
        <h2>Review box:</h2>
        <div class="laci-custom-related-box-container">
            <?php
            ob_start();
            require LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/settings/related-box-html.php';
            $related_box = ob_get_clean();
            laci_kses_post_e( $related_box );
            ?>
        </div>
    </div>
</div>
