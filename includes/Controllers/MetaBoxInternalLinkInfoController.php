<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 *
 * @method static MetaBoxInternalLinkInfoController get_instance()
 */
class MetaBoxInternalLinkInfoController {

    use SingletonTrait;

    protected function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'internal_link_info' ] );
    }

    public function internal_link_info() {
        add_meta_box(
            'laci_internal_link_info_meta_box',
            'Internal Link Information',
            [ $this, 'render_internal_link_info' ],
            [ 'page', 'post' ],
            'normal',
            'high'
        );
    }

    public function render_internal_link_info( $post ) {
        $post_table = TableInternalLinkInfoController::get_instance();
        $post_table->prepare_items();
        ?>
        <div class="laci-internal-link-info-container laci-dashboard-page">
        <?php $post_table->display(); ?>
        </div>
        <?php
    }
}

