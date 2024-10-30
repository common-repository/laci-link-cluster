<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Controllers\InternalLinksController;

use LACI_InternalLinks\Controllers\WPILCustomTableManager;

// Include the WP_List_Table class
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * @method static CreatePostListTableController get_instance()
 */
class CreatePostListTableController extends \WP_List_Table {

    use SingletonTrait;

    protected $total_items;
    protected $per_page;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        parent::__construct(
            [
                'singular' => 'post',
                'plural'   => 'posts',
                'ajax'     => false,
            ]
        );
        $this->per_page = get_option( 'laci_num_item_dash', '50' );
    }

    public function get_columns() {
        $columns = [
            'title'                      => strtoupper( esc_html__( 'Post Title', 'laci-link-cluster' ) ),
            'taxonomies'                 => $this->get_categories_title(),
            'inbound_links'              => $this->get_title( strtoupper( __( 'Inbound Links Entire Site', 'laci-link-cluster' ) ) ),
            'inbound_links_in_category'  => $this->get_title( strtoupper( __( 'Inbound Links Same Cluster', 'laci-link-cluster' ) ) ),
            'outbound_links'             => $this->get_title( strtoupper( __( 'Outbound Links Entire Site', 'laci-link-cluster' ) ) ),
            'outbound_links_in_category' => $this->get_title( strtoupper( __( 'Outbound Links Same Cluster', 'laci-link-cluster' ) ) ),
            'link_back_to_category'      => $this->get_title( strtoupper( __( 'Link Back To Category /Parent', 'laci-link-cluster' ) ) ),
            'date_published'             => strtoupper( esc_html__( 'Date Published', 'laci-link-cluster' ) ),
        ];
        return $columns;
    }

    public static function get_title( $text ) {
        ob_start();
        ?>
            <span>
                <div class="laci-key-words-title laci-row-title-content">
                    <span class="laci-text-label"><?php echo esc_html( $text ); ?></span>
                    <span class="dashicons dashicons-info">
                        <span class="laci-text-tooltip"><?php echo esc_html( strtolower( $text ) ); ?></span>
                    </span>
                </div>
            </span>
        <?php
        return ob_get_clean();
    }

    public static function get_key_words_title() {
        ob_start();
        ?>
            <div class="laci-key-words-title">
                <span class="laci-text-label"><?php esc_html_e( 'FOCUS KEYWORDS', 'laci-link-cluster' ); ?></span>
                <span class="dashicons dashicons-edit laci-key-words-icon-edit"></span>
            </div>
            <div class="laci-key-words-action-change">
                <button type="button" class="button button-primary laci-key-words-save-change"><?php esc_html_e( 'Save change', 'laci-link-cluster' ); ?></button>
                <p style="display:inline" class="delete laci-key-words-cancel-change"><?php esc_html_e( 'Cancel', 'laci-link-cluster' ); ?></p>
            </div>
        <?php
        return ob_get_clean();
    }

    public static function get_categories_title() {
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );
        $taxonomy = get_taxonomy( $taxonomy );
        ob_start();
        ?>
            <div class="laci-categories-title">
                <span class="laci-text-label"><?php echo esc_html( strtoupper( $taxonomy->label ) ); ?></span>
                <span class="dashicons dashicons-edit laci-categories-icon-edit"></span>
            </div>
            <div class="laci-categories-action-change">
                <button type="button" class="button button-primary laci-categories-save-change"><?php esc_html_e( 'Save change', 'laci-link-cluster' ); ?></button>
                <p style="display:inline" class="delete laci-categories-cancel-change"><?php esc_html_e( 'Cancel', 'laci-link-cluster' ); ?></p>
            </div>
        <?php
        return ob_get_clean();
    }

    public static function get_link_back_category_title() {
        ob_start();
        ?>
            <div class="laci-link-back-category-title-title">
                <img width="50" src="<?php echo esc_url( LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/images/logo-back-parent.png' ); ?>" alt="WP Internal Links">
                <p class="laci-text-label"><strong><?php esc_html_e( 'Internal Links', 'laci-link-cluster' ); ?></strong></p>
                <p class="laci-text-des"><strong><?php esc_html_e( '[Back to Parent]', 'laci-link-cluster' ); ?></strong></p>
                <p><?php esc_html_e( 'Link back to Category', 'laci-link-cluster' ); ?></p>
            </div>
        <?php
        return ob_get_clean();
    }


    public static function get_inbound_links_within_category_title() {
        ob_start();
        ?>
            <div class="laci-inbound-links-within-category-title">
                <img width="50" src="<?php echo esc_url( LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/images/logo-within-cat.png' ); ?>" alt="WP Internal Links">
                <p class="laci-text-label"><strong><?php esc_html_e( 'Internal Links', 'laci-link-cluster' ); ?></strong></p>
                <p class="laci-text-des"><strong><?php esc_html_e( '[Within the Category]', 'laci-link-cluster' ); ?></strong></p>
                <p><?php esc_html_e( 'Inbound', 'laci-link-cluster' ); ?></p>
            </div>
        <?php
        return ob_get_clean();
    }

    public static function get_inbound_links_title() {
        ob_start();
        ?>
            <div class="laci-inbound-links-title">
                <img width="50" src="<?php echo esc_url( LACI_INTERNAL_LINKS_PLUGIN_URL . 'assets/admin/images/logo-entrire.png' ); ?>" alt="WP Internal Links">
                <p class="laci-text-label"><strong><?php esc_html_e( 'Internal Links', 'laci-link-cluster' ); ?></strong></p>
                <p class="laci-text-des"><strong><?php esc_html_e( '[Entire WebSite]', 'laci-link-cluster' ); ?></strong></p>
                <p><?php esc_html_e( 'Inbound', 'laci-link-cluster' ); ?></p>
            </div>
        <?php
        return ob_get_clean();
    }

    public function get_sortable_columns() {
        $sortable_columns = [
            'title'                      => [ 'title', true ],
            'taxonomies'                 => [ 'taxonomies', true ],
            //'key_words'                  => [ 'key_words', true ],
            'inbound_links'              => [ 'inbound_links', false ],
            'outbound_links'             => [ 'outbound_links', false ],
            'inbound_links_in_category'  => [ 'inbound_links_in_category', false ],
            'outbound_links_in_category' => [ 'outbound_links_in_category', false ],
            'link_back_to_category'      => [ 'link_back_to_category', false ],
            'date_published'             => [ 'date', true ],
        ];
        return $sortable_columns;
    }

    protected function search_items( $search_value, $term_id, $taxonomy ) {
        $args = [
            'posts_per_page' => -1,
            'post_type'      => 'post',
            's'              => $search_value,
            'post_status'    => 'publish',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ];

        if ( ! empty( $term_id ) && ! empty( $taxonomy ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ],
            ];
        }

        $query = new \WP_Query( $args );
        return $query;
    }


    public function prepare_items() {
        if ( isset( $_REQUEST['nonce'] ) && ! empty( $_REQUEST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'laci-internal-links-nonce' ) ) {
             wp_die( 'Security check' );
        }

        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

        $term_id      = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
        $search_value = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

        $post_type   = isset( $_GET['get_post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['get_post_type'] ) ) : '';
        $only_orphan = isset( $_GET['only_orphan'] ) ? $_GET['only_orphan'] === '1' : false;

        $inbound_link_entire_site_quantity_from = isset( $_GET['inbound_link_entire_site_quantity_from'] ) && '' !== $_GET['inbound_link_entire_site_quantity_from'] ? (int) sanitize_text_field( wp_unslash( $_GET['inbound_link_entire_site_quantity_from'] ) ) : '';
        $inbound_link_entire_site_quantity_to   = isset( $_GET['inbound_link_entire_site_quantity_to'] ) && '' !== $_GET['inbound_link_entire_site_quantity_to'] ? (int) sanitize_text_field( wp_unslash( $_GET['inbound_link_entire_site_quantity_to'] ) ) : '';

        $outbound_link_entire_site_quantity_from = isset( $_GET['outbound_link_entire_site_quantity_from'] ) && '' !== $_GET['outbound_link_entire_site_quantity_from'] ? (int) sanitize_text_field( wp_unslash( $_GET['outbound_link_entire_site_quantity_from'] ) ) : '';
        $outbound_link_entire_site_quantity_to   = isset( $_GET['outbound_link_entire_site_quantity_to'] ) && '' !== $_GET['outbound_link_entire_site_quantity_to'] ? (int) sanitize_text_field( wp_unslash( $_GET['outbound_link_entire_site_quantity_to'] ) ) : '';

        $inbound_links_in_category_quantity_from = isset( $_GET['inbound_links_in_category_quantity_from'] ) && '' !== $_GET['inbound_links_in_category_quantity_from'] ? (int) sanitize_text_field( wp_unslash( $_GET['inbound_links_in_category_quantity_from'] ) ) : '';
        $inbound_links_in_category_quantity_to   = isset( $_GET['inbound_links_in_category_quantity_to'] ) && '' !== $_GET['inbound_links_in_category_quantity_to'] ? (int) sanitize_text_field( wp_unslash( $_GET['inbound_links_in_category_quantity_to'] ) ) : '';

        $outbound_links_in_category_quantity_from = isset( $_GET['outbound_links_in_category_quantity_from'] ) && '' !== $_GET['outbound_links_in_category_quantity_from'] ? (int) sanitize_text_field( wp_unslash( $_GET['outbound_links_in_category_quantity_from'] ) ) : '';
        $outbound_links_in_category_quantity_to   = isset( $_GET['outbound_links_in_category_quantity_to'] ) && '' !== $_GET['outbound_links_in_category_quantity_to'] ? (int) sanitize_text_field( wp_unslash( $_GET['outbound_links_in_category_quantity_to'] ) ) : '';

        add_filter( 'posts_fields', [ $this, 'custom_posts_fields' ] );

        $args = [
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'order'          => 'ASC',
            'fields'         => 'ids',
        ];

        if ( ! empty( $post_type ) && in_array( $post_type, [ 'post', 'page' ] ) ) {
            $args['post_type'] = $post_type;
        }

        if ( ! empty( $term_id ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ],
            ];
        }

        if ( ! empty( $search_value ) ) {
            $query = $this->search_items( $search_value, $term_id, $taxonomy );
        } else {
            $query = new \WP_Query( $args );
        }

        $posts = [];

        foreach ( $query->posts as $item ) {
            $post             = new \stdClass();
            $post->ID         = $item;
            $post->post_title = get_the_title( $item );
            $posts[]          = $post;
        }

        if ( $only_orphan ) {
            $posts = array_filter(
                $posts,
                function ( $post ) {
                    $has_internal_links = WPILCustomTableManager::get_meta_value( $post->ID, 'is_orphan_page' );
                    return $has_internal_links;
                }
            );
        }

        if ( is_numeric( $inbound_link_entire_site_quantity_from ) || is_numeric( $inbound_link_entire_site_quantity_to ) ) {
            $posts = array_filter(
                $posts,
                function ( $post ) use ( $inbound_link_entire_site_quantity_from, $inbound_link_entire_site_quantity_to ) {
                    $inbound_links = count( WPILCustomTableManager::get_meta_value( $post->ID, 'inbound_links' ) );

                    if ( $inbound_links < $inbound_link_entire_site_quantity_from ) {
                        return false;
                    }

                    if ( $inbound_links > $inbound_link_entire_site_quantity_to ) {
                        return false;
                    }

                    return true;
                }
            );
        }

        if ( is_numeric( $outbound_link_entire_site_quantity_from ) || is_numeric( $outbound_link_entire_site_quantity_to ) ) {
            $posts = array_filter(
                $posts,
                function ( $post ) use ( $outbound_link_entire_site_quantity_from, $outbound_link_entire_site_quantity_to ) {
                    $outbound_links = count( WPILCustomTableManager::get_meta_value( $post->ID, 'outbound_links' ) );

                    if ( $outbound_links < $outbound_link_entire_site_quantity_from ) {
                        return false;
                    }

                    if ( $outbound_links > $outbound_link_entire_site_quantity_to ) {
                        return false;
                    }

                    return true;
                }
            );
        }

        if ( is_numeric( $inbound_links_in_category_quantity_from ) || is_numeric( $inbound_links_in_category_quantity_to ) ) {
            $posts = array_filter(
                $posts,
                function ( $post ) use ( $inbound_links_in_category_quantity_from, $inbound_links_in_category_quantity_to ) {
                    $inbound_links_in_category = count( WPILCustomTableManager::get_meta_value( $post->ID, 'inbound_links_in_category' ) );

                    if ( $inbound_links_in_category < $inbound_links_in_category_quantity_from ) {
                        return false;
                    }

                    if ( $inbound_links_in_category > $inbound_links_in_category_quantity_to ) {
                        return false;
                    }

                    return true;
                }
            );
        }

        if ( is_numeric( $outbound_links_in_category_quantity_from ) || is_numeric( $outbound_links_in_category_quantity_to ) ) {
            $posts = array_filter(
                $posts,
                function ( $post ) use ( $outbound_links_in_category_quantity_from, $outbound_links_in_category_quantity_to ) {
                    $outbound_links_in_category = count( WPILCustomTableManager::get_meta_value( $post->ID, 'outbound_links_in_category' ) );

                    if ( $outbound_links_in_category < $outbound_links_in_category_quantity_from ) {
                        return false;
                    }

                    if ( $outbound_links_in_category > $outbound_links_in_category_quantity_to ) {
                        return false;
                    }

                    return true;
                }
            );
        }

        usort( $posts, [ $this, 'sort_items' ] );

        $total_items       = count( $posts );
        $this->total_items = $total_items;
        $this->set_pagination_args(
            [
                'total_items' => $total_items,
                'per_page'    => $this->per_page,
            ]
        );

        $current_page = $this->get_pagenum();
        $offset       = ( $current_page - 1 ) * $this->per_page;
        $this->items  = array_slice( $posts, $offset, $this->per_page );

        $columns               = $this->get_columns();
        $hidden                = [];
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = [ $columns, $hidden, $sortable ];
    }

    public function custom_posts_fields( $fields ) {
        global $wpdb;
        return "{$wpdb->posts}.ID, {$wpdb->posts}.post_title, {$wpdb->posts}.post_content";
    }

    public function title_filter( $search, $wp_query ) {
        global $wpdb;

        if ( empty( $search ) ) {
            return $search; // skip processing - no search term in query
        }

        $q = $wp_query->query_vars;
        $n = ! empty( $q['exact'] ) ? '' : '%';

        $search    = '';
        $searchand = '';

        foreach ( (array) $q['search_terms'] as $term ) {
            $term      = esc_sql( $wpdb->esc_like( $term ) );
            $search   .= "{$searchand}(({$wpdb->posts}.post_title LIKE '{$n}{$term}{$n}') OR ({$wpdb->posts}.post_content LIKE '{$n}{$term}{$n}'))";
            $searchand = ' AND ';
        }

        if ( ! empty( $search ) ) {
            $search = " AND ({$search}) ";
            if ( ! is_user_logged_in() ) {
                $search .= " AND ({$wpdb->posts}.post_password = '') ";
            }

            // Ensure only posts with post_type 'post' or 'page' are included
            $search .= " AND {$wpdb->posts}.post_type IN ('post', 'page') ";

        }

        return $search;
    }

    protected function column_title( $item ) {
        $edit_link = get_edit_post_link( $item->ID );
        $permalink = get_permalink( $item->ID );

        $type = '<span class="laci-post-type"> [' . get_post_type( $item->ID ) . ']</span>';

        $key_words         = get_post_meta( $item->ID, 'laci_list_key_word', true );
        $key_words_implode = is_array( $key_words ) ? implode( ',', $key_words ) : '';
        $link_assistant    = admin_url( 'admin.php?page=laci-internal-links-assistant' ) . '&post_id=' . $item->ID . '&key_word=' . $key_words_implode;

        ob_start();
        include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/dashboard/column-title.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    protected function single_row_columns( $item ) {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        foreach ( $columns as $column_name => $column_display_name ) {
            $class = "class=\"$column_name column-$column_name\"";

            if ( 'title' === $column_name ) {
                $class = "class=\"$column_name column-$column_name title-column\"";
            }

            $style = '';
            if ( in_array( $column_name, $hidden ) ) {
                $style = ' style="display:none;"';
            }

            $additional_class = $this->get_column_class( $item, $column_name );
            if ( $additional_class ) {
                $class = "class=\"$column_name column-$column_name $additional_class\"";
            }

            $attributes = "$class$style";

            if ( 'cb' === $column_name ) {
                laci_kses_post_e( '<th scope="row" class="check-column">' );
                laci_kses_post_e( $this->column_cb( $item ) );
                echo '</th>';
            } elseif ( method_exists( $this, 'column_' . $column_name ) ) {
                laci_kses_post_e( "<td $attributes>" );
                laci_kses_post_e( $this->{'column_' . $column_name}( $item ) );
                echo '</td>';
            } else {
                laci_kses_post_e( "<td $attributes>" );
                laci_kses_post_e( $this->column_default( $item, $column_name ) );
                echo '</td>';
            }
        }
    }

    public function get_column_class( $item, $column_name ) {
        switch ( $column_name ) {
            case 'inbound_links':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'inbound_links' ) ?? [] );
                return $count > 0 ? 'bg-green' : 'bg-gray';
            case 'outbound_links':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'outbound_links' ) ?? [] );
                return $count > 0 ? 'bg-green' : 'bg-gray';
            case 'inbound_links_in_category':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'inbound_links_in_category' ) ?? [] );
                return $count > 0 ? 'bg-green' : 'bg-gray';
            case 'outbound_links_in_category':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'outbound_links_in_category' ) ?? [] );
                return $count > 0 ? 'bg-green' : 'bg-gray';
            case 'link_back_to_category':
                $total_links = ( WPILCustomTableManager::get_meta_value( $item->ID, 'link_back_to_category' )['total_links'] ?? 0 );
                return $total_links > 0 ? 'bg-green' : 'bg-gray';
            default:
                return '';
        }
    }

    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'taxonomies':
                return $this->get_hierarchical_categories( $item->ID );
            case 'inbound_links':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'inbound_links' ) ?? [] );
                return '<a href="#" class="laci-count-link inbound-links-count" data-type="inbound" data-post-id="' . $item->ID . '">' . $count . '</a>';
            case 'outbound_links':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'outbound_links' ) ?? [] );
                return '<a href="#" class="laci-count-link outbound-links-count"  data-type="outbound" data-post-id="' . $item->ID . '">' . $count . '</a>';
            case 'inbound_links_in_category':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'inbound_links_in_category' ) ?? [] );
                return '<a href="#" class="laci-count-link inbound-links-in-category-count"  data-type="inbound_category" data-post-id="' . $item->ID . '">' . $count . '</a>';
            case 'outbound_links_in_category':
                $count = count( WPILCustomTableManager::get_meta_value( $item->ID, 'outbound_links_in_category' ) ?? [] );
                return '<a href="#" class="laci-count-link outbound-links-in-category-count"  data-type="outbound_category" data-post-id="' . $item->ID . '">' . $count . '</a>';
            case 'link_back_to_category':
                $total_links              = ( WPILCustomTableManager::get_meta_value( $item->ID, 'link_back_to_category' )['total_links'] ?? 0 );
                $total_outbound_main_post = ( WPILCustomTableManager::get_meta_value( $item->ID, 'link_back_to_category' )['total_outbound_main_post'] ?? 0 );
                $html                     = $total_links . '/' . $total_outbound_main_post;
                return '<a href="#" class="laci-count-link links-back-to-category-count"  data-type="link_back_to_category" data-post-id="' . $item->ID . '">' . $html . '</a>';
            case 'date_published':
                $date = get_the_date( '', $item->ID );
                $time = get_the_time( '', $item->ID );
                return esc_html__( 'Published: ', 'laci-link-cluster' ) . '<br>' . esc_html( $date ) . esc_html__( ' at ', 'laci-link-cluster' ) . esc_html( $time );
            default:
                return print_r( $item, true );
        }
    }

    public static function get_category_link( $post_id ) {
        $categories = get_the_category( $post_id );

        if ( ! empty( $categories ) ) {
            $main_category = $categories[0];
            $category_link = get_category_link( $main_category->term_id );

            $content    = get_post_field( 'post_content', $post_id );
            $link       = 'href="' . esc_url( $category_link ) . '"';
            $link_count = substr_count( $content, $link );
            $class      = $link_count > 0 ? ' bg-green' : ' bg-gray';

            return '<a  class="laci-count-link' . $class . '"  href="' . esc_url( $category_link ) . '">' . __( 'View Category', 'laci-link-cluster' ) . '</a>';
        }

        return '';
    }

    private function sort_items( $a, $b ) {
        $orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING ) ?? '';
        $order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING ) ?? 'asc';

        switch ( $orderby ) {
            case 'title':
                $result = strcmp( $a->post_title, $b->post_title );
                break;
            case 'taxonomies':
                    $result = strcmp( $this->sort_taxonomy( $a->ID ), $this->sort_taxonomy( $b->ID ) );
                break;
            case 'inbound_links':
                $a_count = count( WPILCustomTableManager::get_meta_value( $a->ID, 'inbound_links' ) );
                $b_count = count( WPILCustomTableManager::get_meta_value( $b->ID, 'inbound_links' ) );
                $result  = $a_count - $b_count;
                break;
            case 'outbound_links':
                $a_count = count( WPILCustomTableManager::get_meta_value( $a->ID, 'outbound_links' ) );
                $b_count = count( WPILCustomTableManager::get_meta_value( $b->ID, 'outbound_links' ) );
                $result  = $a_count - $b_count;
                break;
            case 'inbound_links_in_category':
                $a_count = count( WPILCustomTableManager::get_meta_value( $a->ID, 'inbound_links_in_category' ) );
                $b_count = count( WPILCustomTableManager::get_meta_value( $b->ID, 'inbound_links_in_category' ) );
                $result  = $a_count - $b_count;
                break;
            case 'outbound_links_in_category':
                $a_count = count( WPILCustomTableManager::get_meta_value( $a->ID, 'outbound_links_in_category' ) );
                $b_count = count( WPILCustomTableManager::get_meta_value( $b->ID, 'outbound_links_in_category' ) );
                $result  = $a_count - $b_count;
                break;
            case 'link_back_to_category':
                $a_count = WPILCustomTableManager::get_meta_value( $a->ID, 'link_back_to_category' )['total_links'];
                $b_count = WPILCustomTableManager::get_meta_value( $b->ID, 'link_back_to_category' )['total_links'];
                $result  = $a_count - $b_count;
                break;
            case 'date':
                $date_a = strtotime( get_the_date( '', $a->ID ) );
                $date_b = strtotime( get_the_date( '', $b->ID ) );
                $result = $date_a - $date_b;
                break;
            default:
                $result = 0;
        }

        return ( 'asc' === $order ) ? $result : -$result;
    }

    public function sort_taxonomy( $post_id ) {
        // Lấy giá trị taxonomy từ tùy chọn
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

        // Lấy giá trị term_id của taxonomy chính được lưu trong meta
        $main_term_id = get_post_meta( $post_id, 'laci_main_term_id', true );

        // Nếu chưa có term_id trong meta, lấy term đầu tiên từ taxonomy
        if ( empty( $main_term_id ) ) {
            $terms        = get_the_terms( $post_id, $taxonomy ); // Lấy terms của taxonomy
            $main_term_id = $terms ? $terms[0]->term_id : ''; // Lấy term đầu tiên
        }

        // Nếu không tìm thấy term, trả về chuỗi rỗng
        if ( empty( $main_term_id ) ) {
            return '';
        }

        // Lấy term theo term_id
        $term = get_term( $main_term_id, $taxonomy );

        // Trả về slug của term
        return $term ? $term->slug : '';
    }


    public function get_post_count_by_type( $post_type = '' ) {
        global $wpdb;

        $post_type  = $post_type ? $post_type : [ 'post', 'page' ];
        $post_types = implode( "', '", array_map( 'esc_sql', (array) $post_type ) );

        $cache_key = 'post_count_' . md5( $post_types );

        $cached_count = wp_cache_get( $cache_key, 'post_counts' );
        if ( $cached_count !== false ) {
            return $cached_count;
        }

        $args = [
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $query      = new \WP_Query( $args );
        $post_count = count( $query->posts );
        wp_cache_set( $cache_key, $post_count, 'post_counts', 12 * HOUR_IN_SECONDS );

        return $post_count;
    }

    public function count_orphan_pages() {
        $cache_key = 'orphan_page_count';

        $cached_count = wp_cache_get( $cache_key, 'orphan_pages' );
        if ( false !== $cached_count ) {
            return $cached_count;
        }

        $args = [
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $query        = new \WP_Query( $args );
        $all_post_ids = $query->posts;

        $orphan_count = 0;

        foreach ( $all_post_ids as $post_id ) {
            if ( WPILCustomTableManager::get_meta_value( $post_id, 'is_orphan_page' ) ) {
                $orphan_count++;
            }
        }

        // Save the result to cache
        wp_cache_set( $cache_key, $orphan_count, 'orphan_pages', 12 * HOUR_IN_SECONDS );

        return $orphan_count;
    }

    private function get_key_words_post( $post_id ) {
        $key_words         = get_post_meta( $post_id, 'laci_list_key_word', true );
        $key_words_implode = is_array( $key_words ) ? implode( ',', $key_words ) : '';
        $name_input        = "key-words-change__$post_id";
        $class             = "laci-key-words-text__$post_id";
        ob_start();
        ?>
            <div class="laci-hierarchical-key-words">
                <p class="<?php echo esc_html( "laci-key-words-text $class" ); ?>"><?php echo esc_html( $key_words_implode ); ?></p>    
                <input style="width:100%" type="text" class="laci-key-words-change" data-id="<?php echo esc_html( $post_id ); ?>" name="<?php echo esc_html( $name_input ); ?>" value="<?php echo esc_html( $key_words_implode ); ?>" placeholder="<?php esc_html_e( 'Ex: key_word_1, key_word_2' ); ?>">
            </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    public static function get_hierarchical_categories( $post_id ) {
        $post_type = get_post_type( $post_id );
        if ( ! in_array( $post_type, [ 'post' ] ) ) {
            return '';
        }
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

        $terms         = get_the_terms( $post_id, $taxonomy );
        $name_input    = "category-change__$post_id";
        $class         = "laci-category-text__$post_id";
        $main_category = get_post_meta( $post_id, 'laci_main_category_id', true );

        $post_terms = wp_get_post_terms( $post_id, $taxonomy );

        $all_terms = get_terms(
            [
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
            ]
        );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            ob_start();
            ?>
               <div class="laci-hierarchical-categories">
               <p class=" <?php echo esc_html( "laci-category-text $class" ); ?>" data-id="<?php echo esc_html( $post_id ); ?>"></p>
               <div class="laci-category-for-post-select" style="display: none;">
                    <select style="width:100%" class="laci-category-for-post" data-id="<?php echo esc_html( $post_id ); ?>" name="<?php echo esc_html( $name_input ); ?>" multiple>
                        <?php
                        foreach ( $all_terms as $term ) :
                            $ancestors         = get_ancestors( $term->term_id, $taxonomy );
                            $ancestors         = array_reverse( $ancestors );
                            $ancestors[]       = $term->term_id;
                            $hierarchical_name = '';
                            foreach ( $ancestors as $ancestor_id ) {
                                $ancestor           = get_term( $ancestor_id, $taxonomy );
                                $hierarchical_name .= $ancestor->name . ' / ';
                            }
                            $hierarchical_name = rtrim( $hierarchical_name, ' / ' );
                            ?>
                            <option value="<?php echo esc_attr( $term->term_id ); ?>">
                                <?php echo esc_html( $hierarchical_name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            return $html;
        }

        $terms_hierarchical = [];
        foreach ( $terms as $key => $term ) {
            $ancestors         = get_ancestors( $term->term_id, $taxonomy );
            $ancestors         = array_reverse( $ancestors );
            $ancestors[]       = $term->term_id;
            $hierarchical_name = '';
            foreach ( $ancestors as $ancestor_id ) {
                $ancestor           = get_term( $ancestor_id, $taxonomy );
                $hierarchical_name .= $ancestor->name . ' / ';
            }
            $hierarchical_name = rtrim( $hierarchical_name, ' / ' );

            if ( empty( $main_category ) && $key == 0 || in_array( $main_category, $post_terms ) === false && $key == 0 ) {
                $hierarchical_name = '<strong>' . $hierarchical_name . '</strong>';
            } elseif ( $main_category == strval( $term->term_id ) ) {
                $hierarchical_name = '<strong>' . $hierarchical_name . '</strong>';
            }

            $terms_hierarchical[] = [
                'id'   => $term->term_id,
                'name' => '<a class="laci-category-text-item" href="' . get_edit_term_link( $term->term_id ) . '">' . $hierarchical_name . '</a>',
            ];
        }

        ob_start();
        ?>
            <div class="laci-hierarchical-categories">
                <p class=" <?php echo esc_html( "laci-category-text $class " ); ?>" data-id="<?php echo esc_html( $post_id ); ?>"><?php echo wp_kses_post( implode( ', ', array_column( $terms_hierarchical, 'name' ) ) ); ?></p>
                <div class="laci-category-for-post-select" style="display: none;">
                    <select style="width:100%" class="laci-category-for-post" data-id="<?php echo esc_html( $post_id ); ?>" name="<?php echo esc_html( $name_input ); ?>" multiple>
                        <?php
                        foreach ( $all_terms as $term ) :
                            $ancestors         = get_ancestors( $term->term_id, $taxonomy );
                            $ancestors         = array_reverse( $ancestors );
                            $ancestors[]       = $term->term_id;
                            $hierarchical_name = '';
                            foreach ( $ancestors as $ancestor_id ) {
                                $ancestor           = get_term( $ancestor_id, $taxonomy );
                                $hierarchical_name .= $ancestor->name . ' / ';
                            }
                            $hierarchical_name = rtrim( $hierarchical_name, ' / ' );
                            $selected          = in_array( $term->term_id, array_column( $terms_hierarchical, 'id' ) ) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo esc_attr( $selected ); ?>>
                                <?php echo esc_html( $hierarchical_name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }
}
