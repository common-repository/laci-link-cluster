<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;
use LACI_InternalLinks\Utils\CleanContent;


/**
 * @method static PlacementAssistantController get_instance()
 */
class PlacementAssistantController {

    use SingletonTrait;

    public function __construct() {
        add_action( 'wp_ajax_laci_search_keyword_same_cate', [ $this, 'laci_search_keyword_same_cate_callback' ] );
        add_action( 'wp_ajax_laci_save_internal_links_for_post', [ $this, 'laci_save_internal_links_for_post_callback' ] );
        add_action( 'wp_ajax_laci_load_more_post_same_cate', [ $this, 'laci_load_more_post_same_cate_callback' ] );
        add_action( 'wp_ajax_laci_load_more_post_diff_cate', [ $this, 'laci_load_more_post_diff_cate_callback' ] );
        add_action( 'wp_ajax_laci_get_content_post', [ $this, 'laci_get_content_post_callback' ] );
        add_action( 'wp_ajax_laci_save_related_post', [ $this, 'laci_save_related_post_callback' ] );
        add_action( 'wp_ajax_laci_insert_main_keywords', [ $this, 'laci_insert_main_keywords_callback' ] );

    }

    public function laci_insert_main_keywords_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

            if ( empty( $post_id ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID or Key Word is empty', 'laci-link-cluster' ) ] );
            }

            $key_words = get_post_meta( $post_id, 'laci_list_key_word', true );
            $key_word  = is_array( $key_words ) ? implode( ',', $key_words ) : '';
            $key_word  = rtrim( $key_word, ',' );
            $key_word  = preg_replace( '/,+/', ',', $key_word );

            wp_send_json_success(
                [
                    'mess'     => __( 'Saved successfully', 'laci-link-cluster' ),
                    'key_word' => $key_word,
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'mess' => $e->getMessage() ] );
        }
    }

    public function laci_load_more_post_diff_cate_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id  = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
            $key_word = isset( $_POST['key_word'] ) ? sanitize_text_field( wp_unslash( $_POST['key_word'] ) ) : '';
            $page     = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

            if ( empty( $post_id ) || empty( $key_word ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID or Key Word is empty', 'laci-link-cluster' ) ] );
            }

            $result_diff_cat = self::get_posts_with_keyword_in_diff_taxonomy( $post_id, $key_word, $page );

            $result_diff_cat ['post_id'] = $post_id;

            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/placement-assistant/diff-category.php';
            $html_diff_cate = ob_get_contents();
            ob_end_clean();

            wp_send_json_success(
                [
                    'mess_diff_cate'         => isset( $result_diff_cat['error'] ) ? $result_diff_cat['error'] : __( 'Found posts with the keyword', 'laci-link-cluster' ),
                    'html_diff_cate'         => $html_diff_cate,
                    'max_pages_diff_cate'    => $result_diff_cat['max_pages'],
                    'current_page_diff_cate' => $result_diff_cat['current_page'],
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'mess' => $e->getMessage() ] );
        }
    }

    public function laci_save_related_post_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id         = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
            $content         = isset( $_POST['content'] ) ? stripslashes( trim( $_POST['content'] ) ) : '';
            $related_post_id = isset( $_POST['related_post_id'] ) ? intval( $_POST['related_post_id'] ) : '';

            if ( empty( $post_id ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID is empty', 'laci-link-cluster' ) ] );
            }

            $post_content = CleanContent::clean_content_to_update_related_post( $content, $related_post_id );

            $post_update_status = wp_update_post(
                [
                    'ID'           => $post_id,
                    'post_content' => $post_content,
                ]
            );

            if ( is_wp_error( $post_update_status ) ) {
                wp_send_json_error( [ 'mess' => $post_update_status->get_error_message() ] );
            }

            wp_send_json_success(
                [
                    'mess' => __( 'Get content post successfully', 'laci-link-cluster' ),
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'mess' => $e->getMessage() ] );
        }
    }

    public function laci_get_content_post_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id            = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
            $related_post_title = isset( $_POST['related_post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['related_post_title'] ) ) : '';
            $key_words          = isset( $_POST['key_words'] ) ? sanitize_text_field( wp_unslash( $_POST['key_words'] ) ) : '';

            if ( empty( $post_id ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID is empty', 'laci-link-cluster' ) ] );
            }

            $post         = get_post( $post_id );
            $post_content = $post->post_content;
            $post_title   = $post->post_title;

            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/placement-assistant/box-content.php';
            $html = ob_get_contents();
            ob_end_clean();

            wp_send_json_success(
                [
                    'mess'  => __( 'Get content post successfully', 'laci-link-cluster' ),
                    'html'  => $html,
                    'title' => $post_title,
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'mess' => $e->getMessage() ] );
        }
    }

    public function laci_load_more_post_same_cate_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id  = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
            $key_word = isset( $_POST['key_word'] ) ? sanitize_text_field( wp_unslash( $_POST['key_word'] ) ) : '';
            $page     = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

            if ( empty( $post_id ) || empty( $key_word ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID or Key Word is empty', 'laci-link-cluster' ) ] );
            }

            $result_same_cat = self::get_posts_with_keyword_in_same_taxonomies( $post_id, $key_word, $page );

            $result_same_cat ['post_id'] = $post_id;

            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/placement-assistant/same-category.php';
            $html_same_cate = ob_get_contents();
            ob_end_clean();

            wp_send_json_success(
                [
                    'mess_same_cate'         => isset( $result_same_cat['error'] ) ? $result_same_cat['error'] : __( 'Found posts with the keyword', 'laci-link-cluster' ),
                    'html_same_cate'         => $html_same_cate,
                    'max_pages_same_cate'    => $result_same_cat['max_pages'],
                    'current_page_same_cate' => $result_same_cat['current_page'],
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'mess' => $e->getMessage() ] );
        }
    }

    public function laci_save_internal_links_for_post_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id        = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
            $content_before = isset( $_POST['content_before'] ) ? $_POST['content_before'] : '';
            $content_after  = isset( $_POST['content_after'] ) ? $_POST['content_after'] : '';

            if ( empty( $post_id ) || empty( $content_before ) || empty( $content_after ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID or Content is empty', 'laci-link-cluster' ) ] );
            }

            $current_content = CleanContent::replace_html_entity( get_post_field( 'post_content', $post_id ) );

            $cleaned_content_before = CleanContent::clean_content( $content_before );
            $cleaned_content_after  = CleanContent::clean_content( $content_after );

            if ( strpos( $current_content, $cleaned_content_before ) !== false ) {
                $updated_content = str_replace( $cleaned_content_before, $cleaned_content_after, $current_content );
            } else {
                wp_send_json_error( [ 'mess' => __( 'Content before not found in current content', 'laci-link-cluster' ) ] );
            }

            $post_update_status = wp_update_post(
                [
                    'ID'           => $post_id,
                    'post_content' => $updated_content,
                ]
            );

            if ( is_wp_error( $post_update_status ) ) {
                wp_send_json_error( [ 'mess' => $post_update_status->get_error_message() ] );
            }

            wp_send_json_success( [ 'mess' => __( 'Saved successfully', 'laci-link-cluster' ) ] );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'mess' => $e->getMessage() ] );
        }
    }

    public function laci_search_keyword_same_cate_callback() {
        try {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if ( ! wp_verify_nonce( $nonce, 'laci-internal-links-nonce' ) ) {
                wp_send_json_error( [ 'mess' => __( 'Nonce is invalid', 'laci-link-cluster' ) ] );
            }

            $post_id     = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
            $key_word    = isset( $_POST['key_word'] ) ? sanitize_text_field( wp_unslash( $_POST['key_word'] ) ) : '';
            $current_url = isset( $_POST['current_url'] ) ? sanitize_text_field( wp_unslash( $_POST['current_url'] ) ) : '';

            if ( empty( $post_id ) || empty( $key_word ) ) {
                wp_send_json_error( [ 'mess' => __( 'Post ID or Key Word is empty', 'laci-link-cluster' ) ] );
            }

            $key_word = rtrim( $key_word, ',' );
            $key_word = preg_replace( '/,+/', ',', $key_word );

            $result_same_cat = self::get_posts_with_keyword_in_same_taxonomies( $post_id, $key_word );
            $result_diff_cat = self::get_posts_with_keyword_in_diff_taxonomy( $post_id, $key_word );

            $admin_url = get_admin_url() . 'admin.php?';
            $page      = 'page=laci-internal-links-assistant';

            $current_url = $admin_url . $page . '&post_id=' . $post_id . '&key_word=' . $key_word;

            $result_same_cat ['post_id'] = $post_id;
            $result_diff_cat ['post_id'] = $post_id;

            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/placement-assistant/same-category.php';
            $html_same_cate = ob_get_contents();
            ob_end_clean();

            ob_start();
            include LACI_INTERNAL_LINKS_PLUGIN_PATH . 'templates/placement-assistant/diff-category.php';
            $html_diff_cate = ob_get_contents();
            ob_end_clean();

            wp_send_json_success(
                [
                    'same_cate_error'        => isset( $result_same_cat['error'] ) ? $result_same_cat['error'] : '',
                    'diff_cate_error'        => isset( $result_diff_cat['error'] ) ? $result_diff_cat['error'] : '',

                    'html_same_cate'         => $html_same_cate,
                    'max_pages_same_cate'    => $result_same_cat['max_pages'],
                    'current_page_same_cate' => $result_same_cat['current_page'],

                    'html_diff_cate'         => $html_diff_cate,
                    'max_pages_diff_cate'    => $result_diff_cat['max_pages'],
                    'current_page_diff_cate' => $result_diff_cat['current_page'],

                    'current_url'            => $current_url,
                ]
            );

        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public static function get_posts_with_keyword_in_same_taxonomies( $post_id, $key_words, $page = 1, $posts_per_page = LACI_INTERNAL_LINKS_CUSTOM_NUM_ITEM_LA ) {
        $filtered_posts = [];
        $posts_to_check = 100;

        $taxonomy   = get_option( 'laci_internallinks_taxonomy', 'category' ); // Lấy taxonomy từ cài đặt
        $post_title = get_the_title( $post_id );

        // Lấy danh sách terms của bài viết từ taxonomy hiện tại
        $terms = get_the_terms( $post_id, $taxonomy );
        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            return [ 'error' => __( 'No terms found for this post in the selected taxonomy', 'laci-link-cluster' ) ];
        }

        // Chuyển terms thành mảng ID
        $term_ids = array_map(
            function( $term ) {
                return $term->term_id;
            },
            $terms
        );

        $keywords_array = array_map( 'trim', explode( ',', $key_words ) );

        $found_posts = 0;
        $offset      = 0;

        while ( true ) {
            $args = [
                'tax_query'      => [
                    [
                        'taxonomy' => $taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $term_ids,
                    ],
                ],
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $posts_to_check,
                'offset'         => $offset,
                'fields'         => 'ids',
            ];

            if ( count( $keywords_array ) === 1 ) {
                $args['s'] = $keywords_array[0];
            }

            $query = new \WP_Query( $args );

            if ( $query->have_posts() ) {
                $post_ids = $query->posts;

                foreach ( $post_ids as $item ) {
                    if ( $item == $post_id ) {
                        continue;
                    }

                    $post    = get_post( $item );
                    $content = $post->post_content;

                    // Kiểm tra nếu bài viết có chứa chu���i 'laci_related_post_content'
                    if ( stripos( $content, 'laci_related_post_content' ) !== false ) {
                        continue; // Bỏ qua bài viết này
                    }

                    // Kiểm tra nếu nội dung bài viết đã có liên kết đến bài viết hiện tại
                    $permalink = get_permalink( $post_id );
                    if ( stripos( $content, $permalink ) !== false ) {
                        continue; // Bỏ qua bài viết này nếu đã có liên kết
                    }

                    $excerpts = CleanContent::get_highlighted_excerpts( $content, $keywords_array );

                    if ( ! empty( $excerpts ) && $excerpts['total_keywords_fined'] > 0 ) {
                        $post_term_ids = array_map(
                            function( $term ) {
                                return $term->term_id;
                            },
                            get_the_terms( $post->ID, $taxonomy )
                        );

                        $filtered_posts[] = [
                            'post_id'              => $post->ID,
                            'title'                => $post->post_title,
                            'excerpts'             => $excerpts['excerpts'], // list content
                            'total_keywords_fined' => $excerpts['total_keywords_fined'],
                            'term_ids'             => $post_term_ids,
                        ];

                        $found_posts++;
                    }
                }

                if ( $found_posts >= $posts_per_page || count( $post_ids ) < $posts_to_check ) {
                    break;
                }
            } else {
                break;
            }

            $offset += $posts_to_check;
            wp_reset_postdata();
        }

        if ( empty( $filtered_posts ) ) {
            return [ 'error' => __( 'No posts found with the keyword(s) in the same terms', 'laci-link-cluster' ) ];
        }

        // Pagination
        $total_posts   = count( $filtered_posts );
        $max_pages     = ceil( $total_posts / $posts_per_page );
        $offset        = ( $page - 1 ) * $posts_per_page;
        $paged_results = array_slice( $filtered_posts, $offset, $posts_per_page );

        return [
            'post_title'   => $post_title,
            'posts_data'   => $paged_results,
            'max_pages'    => $max_pages,
            'current_page' => $page,
        ];
    }

    public static function get_posts_with_keyword_in_diff_taxonomy( $post_id, $key_words, $page = 1, $posts_per_page = LACI_INTERNAL_LINKS_CUSTOM_NUM_ITEM_LA ) {
        $filtered_posts = [];
        $posts_to_check = 100;

        // Lấy tiêu đề bài viết hiện tại
        $post_title = get_the_title( $post_id );

        // Lấy taxonomy từ cài đặt
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' ); // Mặc định là 'category'
        $terms    = wp_get_post_terms( $post_id, $taxonomy ); // Lấy terms theo taxonomy

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return [ 'error' => __( 'No terms found for this post in the specified taxonomy', 'laci-link-cluster' ) ];
        }

        $term_ids = array_map(
            function( $term ) {
                return $term->term_id;
            },
            $terms
        );

        // Xử lý từ khóa
        $keywords_array = array_map( 'trim', explode( ',', $key_words ) );

        $found_posts = 0;
        $offset      = 0;

        // Vòng lặp để truy vấn bài viết không thuộc taxonomy hiện tại
        while ( true ) {
            $args = [
                'tax_query'      => [
                    [
                        'taxonomy' => $taxonomy, // Sử dụng taxonomy từ cài đặt
                        'field'    => 'term_id',
                        'terms'    => $term_ids,
                        'operator' => 'NOT IN', // Lọc các bài viết không thuộc taxonomy hiện tại
                    ],
                ],
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $posts_to_check,
                'offset'         => $offset,
                'fields'         => 'ids',
            ];

            // Nếu chỉ có một từ khóa
            if ( count( $keywords_array ) === 1 ) {
                $args['s'] = $keywords_array[0];
            } else {
                $args['keywords_array'] = $keywords_array;
            }

            $query = new \WP_Query( $args );

            if ( $query->have_posts() ) {
                $post_ids = $query->posts;

                foreach ( $post_ids as $item ) {
                    if ( $item == $post_id ) {
                        continue;
                    }
                    $post    = get_post( $item );
                    $content = $post->post_content;

                    // Kiểm tra nếu nội dung bài viết đã có liên kết đến bài viết hiện tại
                    $permalink = get_permalink( $post_id );
                    if ( stripos( $content, $permalink ) !== false ) {
                        continue; // Bỏ qua bài viết này nếu đã có liên kết
                    }

                    // Kiểm tra nếu nội dung chứa laci_related_post_content
                    if ( strpos( $content, 'laci_related_post_content' ) !== false ) {
                        continue; // Bỏ qua bài viết nếu có chứa text này
                    }

                    $excerpts = CleanContent::get_highlighted_excerpts( $content, $keywords_array );

                    if ( ! empty( $excerpts ) && $excerpts['total_keywords_fined'] > 0 ) {
                        $post_term_ids = array_map(
                            function( $term ) {
                                return $term->term_id;
                            },
                            wp_get_post_terms( $post->ID, $taxonomy )
                        );

                        $filtered_posts[] = [
                            'post_id'              => $post->ID,
                            'title'                => $post->post_title,
                            'excerpts'             => $excerpts['excerpts'],
                            'total_keywords_fined' => $excerpts['total_keywords_fined'],
                            'term_ids'             => $post_term_ids,
                        ];

                        $found_posts++;
                    }
                }

                if ( $found_posts >= $posts_per_page || count( $post_ids ) < $posts_to_check ) {
                    break;
                }
            } else {
                break;
            }

            $offset += $posts_to_check;
            wp_reset_postdata();
        }

        if ( empty( $filtered_posts ) ) {
            return [ 'error' => __( 'No posts found with the keyword(s) not in the same taxonomy terms', 'laci-link-cluster' ) ];
        }

        // Pagination
        $total_posts   = count( $filtered_posts );
        $max_pages     = ceil( $total_posts / $posts_per_page );
        $offset        = ( $page - 1 ) * $posts_per_page;
        $paged_results = array_slice( $filtered_posts, $offset, $posts_per_page );

        return [
            'post_title'   => $post_title,
            'posts_data'   => $paged_results,
            'max_pages'    => $max_pages,
            'current_page' => $page,
        ];
    }

}
