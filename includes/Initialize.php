<?php
namespace LACI_InternalLinks;

use LACI_InternalLinks\Utils\ActivePlugin;
use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Enqueue\AdminEnqueue;
use LACI_InternalLinks\Shortcode\AddShortcode;
use LACI_InternalLinks\Controllers\MetaBoxMainCategoryController;
use LACI_InternalLinks\Controllers\MetaBoxAddKeyWordController;
use LACI_InternalLinks\Controllers\MetaBoxInternalLinkInfoController;
use LACI_InternalLinks\Controllers\PlacementAssistantController;
use LACI_InternalLinks\Controllers\SettingsController;
use LACI_InternalLinks\Controllers\GoToProController;
/**
 * LACI_InternalLinks Plugin Initializer
 *
 * @method static Initialize get_instance()
 */
class Initialize {

    use SingletonTrait;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        add_action( 'init', [ $this, 'laci_internal_links_init' ] );
    }

    public static function laci_internal_links_init() {

        require_once LACI_INTERNAL_LINKS_PLUGIN_PATH . 'includes/Functions.php';

        $current_ver = LACI_INTERNAL_LINKS_VERSION;
        $old_ver     = get_option( 'laci_version' );
        if ( strval( $current_ver ) !== $old_ver ) {
            ActivePlugin::laci_internal_links_data_init();
            ActivePlugin::set_default_setting_value();
        }

        update_option( 'laci_internallinks_taxonomy', 'category' );
        update_option( 'laci_version', $current_ver );

        AdminEnqueue::get_instance();
        PlacementAssistantController::get_instance();
        Ajax::get_instance();
        UpdateDatabase::get_instance();
        AddShortcode::get_instance();
        MetaBoxMainCategoryController::get_instance();
        MetaBoxAddKeyWordController::get_instance();
        MetaBoxInternalLinkInfoController::get_instance();
        SettingsController::get_instance();
        GoToProController::get_instance();

    }
}
