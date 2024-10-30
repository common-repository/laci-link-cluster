<?php
namespace LACI_InternalLinks\Shortcode;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Controllers\CreatePostListTableController;

/**
 * @method static AddShortcode get_instance()
 */
class AddShortcode {

    use SingletonTrait;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        add_shortcode( 'laci_internal_links_post_list', [ $this, 'custom_post_list_shortcode' ] );
        add_shortcode( 'laci_internal_links_breadcrumbs', [ $this, 'custom_breadcrumbs_shortcode' ] );
        add_shortcode( 'laci_related_post_content', [ $this, 'related_post_content' ] );
        add_shortcode( 'laci_post_title_link', [ $this, 'laci_post_title_link' ] );
    }

    public function laci_post_title_link( $args ) {
        if ( isset( $args['id'] ) ) {
            $post_id    = $args['id'];
            $post       = get_post( $post_id );
            $post_title = $post->post_title;
        } else {
            $color      = get_option( 'laci_related_box__content_color', '#ffffff' );
            $post_title = esc_html__( 'Title of the post will be shown here.', 'laci-link-cluster' );
            return wp_kses_post( '<a style="color:' . $color . '" href="#">' . $post_title . '</a>' );
        }

        return wp_kses_post( '<a href="' . get_permalink( $post_id ) . '">' . $post_title . '</a>' );
    }

    public function related_post_content( $args ) {
        $post_id    = $args['id'];
        $post       = get_post( $post_id );
        $post_title = $post->post_title;

        $color = get_option( 'laci_related_box__content_color', '#ffffff' );
        $link  = '<a style="color:' . $color . '"href="' . get_permalink( $post_id ) . '">' . $post_title . '</a>';

        $laci_related_box__title   = get_option( 'laci_related_box__title', '0' );
        $laci_related_box__content = get_option( 'laci_related_box__content', '' );
        $laci_related_box__content = str_replace( '[laci_post_title_link]', $link, $laci_related_box__content );

        ob_start();
        require LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/settings/related-box-html.php';
        $related_box = ob_get_clean();
        return $related_box;
    }

    public function custom_breadcrumbs_shortcode() {
        return $this->custom_breadcrumbs();
    }

    public function custom_breadcrumbs() {
        global $post;
        $home_text = 'Home';
        $separator = ' &raquo; ';
        $home_link = home_url( '/' );

        $breadcrumb = '<a href="' . $home_link . '">' . $home_text . '</a>';

        if ( is_home() || is_front_page() ) {
            return $breadcrumb;
        }

        if ( is_category() ) {
            $category    = get_queried_object();
            $breadcrumb .= $separator . single_cat_title( '', false );
        } elseif ( is_single() ) {
            $main_category = get_post_meta( $post->ID, 'laci_main_category_id', true );

            if ( empty( $main_category ) ) {
                $main_category = get_the_category( $post->ID )[0]->term_id;
            }

            $category = get_category( $main_category );

            if ( $category ) {
                $breadcrumb .= $separator . '<a href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a>';
            }
            $breadcrumb .= $separator . get_the_title();
        } elseif ( is_page() && ! is_front_page() ) {

            if ( $post->post_parent ) {
                $parent_id = $post->post_parent;
                $crumbs    = [];
                while ( $parent_id ) {
                    $page      = get_page( $parent_id );
                    $crumbs[]  = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
                    $parent_id = $page->post_parent;
                }
                $crumbs = array_reverse( $crumbs );
                foreach ( $crumbs as $crumb ) {
                    $breadcrumb .= $separator . $crumb;
                }
            }
            $breadcrumb .= $separator . get_the_title();
        } elseif ( is_tag() ) {
            $breadcrumb .= $separator . single_tag_title( '', false );
        } elseif ( is_author() ) {
            $breadcrumb .= $separator . get_the_author();
        } elseif ( is_search() ) {
            $breadcrumb .= $separator . 'Search results for: ' . get_search_query();
        } elseif ( is_404() ) {
            $breadcrumb .= $separator . 'Error 404';
        }

        return wp_kses_post( '<div class="custom-breadcrumbs">' . $breadcrumb . '</div>' );
    }

    public function custom_post_list_shortcode() {
        ob_start();
        include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/dashboard/table-report.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
