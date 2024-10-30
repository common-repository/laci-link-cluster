<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 *
 * @method static GoToProController get_instance()
 */
class GoToProController {

    use SingletonTrait;

    protected function __construct() {
        add_filter( 'plugin_action_links_' . LACI_INTERNAL_LINKS_PLUGIN_BASENAME, [ $this, 'plugin_action_links' ] );
    }

    public function plugin_action_links( $links ) {
        $links[] = '<a href="' . admin_url( 'admin.php?page=laci_internal_links_dashboard' ) . '" aria-label="' . esc_attr__( 'Go Dashboard', 'laci-link-cluster' ) . '">' . esc_html__( 'Go Dashboard', 'laci-link-cluster' ) . '</a>';
        $links[] = '<a target="_blank" href="https://linkandcluster.com/" style="color: #43B854; font-weight: bold">' . __( 'Go Pro', 'laci-link-cluster' ) . '</a>';
        return $links;
    }
}
