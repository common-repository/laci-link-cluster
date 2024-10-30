<?php
namespace LACI_InternalLinks\Enqueue;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 * @method static AdminEnqueue get_instance()
 */
class AdminEnqueue {

    use SingletonTrait;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_tinymce' ] );
        add_filter( 'mce_buttons', [ $this, 'custom_tinymce_buttons' ] );
        add_filter( 'mce_external_plugins', [ $this, 'my_custom_tinymce_plugin' ] );

        add_filter( 'safe_style_css', [ $this, 'filter_safe_style_css' ], 10, 1 );

        add_filter( 'posts_search', [ $this, 'search_multiple_keywords_in_content' ], 100, 2 );

    }

    public function search_multiple_keywords_in_content( $where, $query ) {
        global $wpdb;

        if ( is_admin() && ! empty( $query->get( 'keywords_array' ) ) ) {
            $keywords_array = $query->get( 'keywords_array' );

            if ( ! empty( $keywords_array ) ) {
                $search_terms = array_map(
                    function( $keyword ) use ( $wpdb ) {
                        return $wpdb->prepare( 'post_content LIKE %s', '%' . $wpdb->esc_like( $keyword ) . '%' );
                    },
                    $keywords_array
                );

                $search_query = implode( ' OR ', $search_terms );

                $where .= ' AND (' . $search_query . ')';
            }
        }

        return $where;
    }

    public function filter_safe_style_css( $default_array ) {
        $additional_allowed_css_attributes = [ 'display' ];
        return array_merge( $default_array, $additional_allowed_css_attributes );
    }

    public function custom_tinymce_buttons( $buttons ) {
        array_push( $buttons, 'custom_button_add_link' );
        return $buttons;
    }

    public function my_custom_tinymce_plugin( $plugin_array ) {
        $plugin_array['custom_button_add_link'] = LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/link-assistant.js';
        return $plugin_array;
    }

    public function enqueue_tinymce( $plugin_array ) {
        wp_enqueue_script( 'tinymce' );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'quicktags' );
        wp_enqueue_script( 'wp-tinymce' );
        wp_enqueue_style( 'editor-buttons' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'jquery' );

        if ( get_current_screen()->id === 'post' || get_current_screen()->id === 'linkcluster_page_laci_internal_links_dashboard' || get_current_screen()->id === 'admin_page_laci-internal-links-assistant' ) {
            wp_register_script( 'laci-internal-links-admin-select2', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/lib/select2/select2.min.js', [ 'jquery' ], LACI_INTERNAL_LINKS_VERSION, true );
            wp_enqueue_script( 'laci-internal-links-admin-select2' );
            wp_register_style( 'laci-internal-links-admin-select2', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/lib/select2/select2.min.css', [], LACI_INTERNAL_LINKS_VERSION );
            wp_enqueue_style( 'laci-internal-links-admin-select2' );

            wp_enqueue_script( 'laci-internal-links-admin-select2-js', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/select2.js', [ 'jquery' ], LACI_INTERNAL_LINKS_VERSION, true );
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_style( 'jquery-ui-css', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/lib/css/jquery-ui.css', [], LACI_INTERNAL_LINKS_VERSION );

            wp_enqueue_editor();

            wp_enqueue_style( 'laci-internal-links-admin-link-assistant', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/css/link-assistant.css', [], LACI_INTERNAL_LINKS_VERSION );
            wp_enqueue_script( 'laci-internal-links-admin-link-assistant', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/link-assistant.js', [ 'jquery', 'jquery-ui-dialog' ], LACI_INTERNAL_LINKS_VERSION, true );

        }

        if ( get_current_screen()->id === 'linkcluster_page_laci-internal-links-settings' ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'laci-settings', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/css/settings.css', [], LACI_INTERNAL_LINKS_VERSION );
            wp_enqueue_script( 'laci-settings', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/settings.js', [ 'wp-color-picker' ], LACI_INTERNAL_LINKS_VERSION, true );
        }

        //CSS for free version
        wp_enqueue_style( 'laci-free-ver', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/css/free-ver.css', [], LACI_INTERNAL_LINKS_VERSION );

        wp_enqueue_script( 'laci-internal-links-notification', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/notification.js', [ 'jquery' ], LACI_INTERNAL_LINKS_VERSION, true );
        wp_enqueue_style( 'laci-internal-links-admin', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/css/admin.css', [], LACI_INTERNAL_LINKS_VERSION );
        wp_enqueue_script( 'laci-internal-links-admin', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/admin.js', [ 'jquery', 'jquery-ui-dialog' ], LACI_INTERNAL_LINKS_VERSION, true );
        wp_localize_script(
            'laci-internal-links-admin',
            'laci_internal_links',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'laci-internal-links-nonce' ),
            ]
        );

        // Enqueue the script for update database
        wp_enqueue_script( 'laci-internal-links-update-database', LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/js/update-database.js', [ 'jquery' ], LACI_INTERNAL_LINKS_VERSION, true );
        wp_localize_script(
            'laci-internal-links-update-database',
            'laci_internal_links_update_database',
            [
                'ajax_url'            => admin_url( 'admin-ajax.php' ),
                'nonce'               => wp_create_nonce( 'laci-internal-links-update-database-nonce' ),
                'cron_job_status'     => get_option( 'laci_cron_job_status' ),
                'is_update_with_cron' => true,
            ]
        );
    }
}
