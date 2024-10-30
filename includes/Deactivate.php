<?php
namespace LACI_InternalLinks;

/**
 * LACI_InternalLinks Plugin Deactivater
 *
 * @method static Deactivate get_instance()
 */
class Deactivate {

    public static function delete_shortcode() {
        $laci_delete_shortcode_in_deactivate = get_option( 'laci_delete_shortcode_in_deactivate', 0 );
        if ( $laci_delete_shortcode_in_deactivate ) {
            laci_delete_shortcode();
        }
    }
}
