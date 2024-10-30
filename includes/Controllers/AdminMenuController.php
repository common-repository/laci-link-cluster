<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 * LACI_InternalLinks Plugin Initializer
 *
 * @method static AdminMenuController get_instance()
 */
class AdminMenuController {

    use SingletonTrait;

    public $is_active = false;

    protected function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );

        add_action( 'admin_init', [ $this, 'setting_taxonomy' ] );

    }

    public function setting_taxonomy() {
        register_setting( 'laci_internallinks_settings', 'laci_internallinks_taxonomy' );
        add_settings_section( 'laci_internallinks_settings_section', 'Internal Links Settings', null, 'internal-links-settings' );
        add_settings_field( 'laci_internallinks_taxonomy', 'Taxonomy', [ $this, 'laci_internallinks_taxonomy_field' ], 'internal-links-settings', 'laci_internallinks_settings_section' );
    }

    public function laci_internallinks_taxonomy_field() {
        $taxonomy   = get_option( 'laci_internallinks_taxonomy', 'category' );
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        ?>
        <select name="laci_internallinks_taxonomy">
            <?php foreach ( $taxonomies as $tax ) : ?>
                <option value="<?php echo esc_attr( $tax->name ); ?>" <?php selected( $taxonomy, $tax->name ); ?>>
                    <?php echo esc_html( $tax->labels->singular_name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function add_admin_menu() {
        add_menu_page(
            'laci-internal-links',
            __( 'Link&Cluster', 'laci-link-cluster' ),
            'manage_options',
            'laci-internal-links',
            null,
            'dashicons-admin-links',
            10
        );

        add_submenu_page(
            'laci-internal-links',
            __( 'Dashboard', 'laci-link-cluster' ),
            __( 'Dashboard', 'laci-link-cluster' ),
            'manage_options',
            'laci_internal_links_dashboard',
            [ $this, 'menu_dashboard' ]
        );

        add_submenu_page(
            null,
            __( 'Placement Assistant', 'laci-link-cluster' ),
            __( 'Placement Assistant', 'laci-link-cluster' ),
            'manage_options',
            'laci-internal-links-assistant',
            [ $this, 'menu_placement_assistant' ]
        );

        add_submenu_page(
            'laci-internal-links',
            __( 'Settings', 'laci-link-cluster' ),
            __( 'Settings', 'laci-link-cluster' ),
            'manage_options',
            'laci-internal-links-settings',
            [ $this, 'menu_settings' ]
        );

        add_submenu_page(
            'laci-internal-links',
            __( 'Go To Pro', 'laci-link-cluster' ),
            __( 'Go To Pro', 'laci-link-cluster' ),
            'manage_options',
            'laci-internal-links-go-to-pro',
            [ $this, 'menu_go_to_pro' ]
        );

        remove_submenu_page( 'laci-internal-links', 'laci-internal-links' );
    }

    public function menu_dashboard() {
        $path = LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/dashboard/dashboard.php';
        if ( file_exists( $path ) ) {
            require $path;
        }
    }

    public static function menu_placement_assistant() {
        $path = LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/placement-assistant/placement-assistant.php';
        if ( file_exists( $path ) ) {
            require $path;
        }
    }

    public static function menu_settings() {
        $path = LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/settings/nav-tabs.php';
        if ( file_exists( $path ) ) {
            require $path;
        }
    }

    public static function menu_go_to_pro() {
        ?>
        <script type="text/javascript">
            window.location.href = "https://linkandcluster.com/";
        </script>
        <?php
        exit;
    }
}
