<?php
namespace LACI_InternalLinks\Utils;

/**
 *
 * @method static Helpers get_instance()
 */
class Helpers {

    public static function wp_kses_allowed_html( $cus_attr_tags = [] ) {
        $allowed_html_tags = wp_kses_allowed_html( 'post' );

        $allowed_html_tags['style'] = true;

        $allowed_html_tags['select'] = [
            'id'       => true,
            'class'    => true,
            'name'     => true,
            'style'    => true,
            'multiple' => true,
            'data-id'  => true,
        ];

        $allowed_html_tags['option'] = [
            'value'    => true,
            'selected' => true,
            'style'    => true,
            'class'    => true,
            'data-id'  => true,
        ];

        $allowed_html_tags['script'] = true;

        $allowed_html_attr = $cus_attr_tags;

        return array_map(
            function ( $item ) use ( $allowed_html_attr ) {
                return is_array( $item ) ? array_merge( $item, $allowed_html_attr ) : $item;
            },
            $allowed_html_tags
        );
    }
}
