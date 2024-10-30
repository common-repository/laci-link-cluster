<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 *
 * @method static InternalLinksController get_instance()
 */
class InternalLinksController {

    use SingletonTrait;

    public static function has_internal_links( $content ) {
        $home_url = home_url();
        return preg_match( '/<a\s[^>]*href=["\']' . preg_quote( $home_url, '/' ) . '[^"\']*["\'][^>]*>/i', $content );
    }

    public static function is_orphan_page( $post_id ) {
        global $wpdb;

        $cache_key = 'is_orphan_page_' . $post_id;

        // Check cache first
        $cached_result = wp_cache_get( $cache_key, 'orphan_pages' );

        if ( $cached_result === 'no' ) {
            return false;
        }
        if ( $cached_result === 'yes' ) {
            return true;
        }
        if ( false !== $cached_result ) {
            return $cached_result;
        }

        $home_url            = home_url( '/' );
        $post_url            = get_permalink( $post_id );
        $normalized_post_url = str_replace( $home_url, '', $post_url );

        // WP_Query to retrieve all published posts and pages (excluding the current post)
        $args = [
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => 'publish',
            'posts_per_page' => -1, // Retrieve all posts
            'fields'         => 'ids', // Only retrieve post IDs
            'post__not_in'   => [ $post_id ], // Exclude the current post
        ];

        $query        = new \WP_Query( $args );
        $all_post_ids = $query->posts;

        foreach ( $all_post_ids as $id ) {
            $post = get_post( $id );

            if ( stripos( $post->post_content, $post_url ) !== false ||
                stripos( $post->post_content, $normalized_post_url ) !== false ) {
                wp_cache_set( $cache_key, 'no', 'orphan_pages', 12 * HOUR_IN_SECONDS );
                return false;
            }

            if ( has_shortcode( $post->post_content, 'laci_related_post_content' ) ) {
                $pattern = get_shortcode_regex( [ 'laci_related_post_content' ] );
                if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
                    foreach ( $matches as $shortcode ) {
                        if ( $shortcode[2] === 'laci_related_post_content' ) {
                            $shortcode_atts = shortcode_parse_atts( $shortcode[3] );
                            if ( isset( $shortcode_atts['id'] ) && $shortcode_atts['id'] == $post_id ) {
                                wp_cache_set( $cache_key, 'no', 'orphan_pages', 12 * HOUR_IN_SECONDS );
                                return false;
                            }
                        }
                    }
                }
            }
        }

        wp_cache_set( $cache_key, 'yes', 'orphan_pages', 12 * HOUR_IN_SECONDS );
        return true;
    }


    public static function get_inbound_internal_links( $post_id ) {
        global $internal_links_cache;

        $cache_key = 'inbound_links_' . $post_id;

        if ( isset( $internal_links_cache[ $cache_key ] ) ) {
            return $internal_links_cache[ $cache_key ];
        }

        $cached_links = wp_cache_get( $cache_key, 'internal_links' );
        if ( false !== $cached_links ) {
            $internal_links_cache[ $cache_key ] = $cached_links;
            return $cached_links;
        }

        $args = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $query     = new \WP_Query( $args );
        $links     = [];
        $permalink = get_permalink( $post_id );

        $processed_post_ids = [];

        foreach ( $query->posts as $post_id_loop ) {
            if ( $post_id_loop !== $post_id && ! in_array( $post_id_loop, $processed_post_ids ) ) {
                $post         = get_post( $post_id_loop );
                $anchor_texts = [];

                if ( stripos( $post->post_content, $permalink ) !== false ) {
                    if ( preg_match_all( '/<a\s[^>]*href=["\']' . preg_quote( $permalink, '/' ) . '["\'][^>]*>(.*?)<\/a>/i', $post->post_content, $matches ) ) {
                        $anchor_texts = array_merge( $anchor_texts, $matches[1] );
                    }
                }

                if ( has_shortcode( $post->post_content, 'laci_related_post_content' ) ) {
                    if ( preg_match( '/\[laci_related_post_content\s+id=["\']' . preg_quote( $post_id, '/' ) . '["\'].*?\]/i', $post->post_content ) ) {
                        $anchor_texts[] = 'Shortcode Link';
                    }
                }

                if ( ! empty( $anchor_texts ) ) {
                    $links[] = [
                        'ID'          => $post->ID,
                        'title'       => $post->post_title,
                        'type'        => $post->post_type,
                        'categories'  => get_the_category_list( ', ', '', $post->ID ),
                        'anchor_text' => implode( ', ', $anchor_texts ),
                    ];

                    $processed_post_ids[] = $post_id_loop;
                }
            }
        }

        wp_cache_set( $cache_key, $links, 'internal_links', 12 * HOUR_IN_SECONDS );
        $internal_links_cache[ $cache_key ] = $links;

        return $links;
    }

    public static function get_outbound_internal_links( $post_id ) {
        global $internal_links_cache;
        $cache_key = 'outbound_links_' . $post_id;

        if ( isset( $internal_links_cache[ $cache_key ] ) ) {
            return $internal_links_cache[ $cache_key ];
        }

        $cached_links = wp_cache_get( $cache_key, 'internal_links' );
        if ( false !== $cached_links ) {
            $internal_links_cache[ $cache_key ] = $cached_links;
            return $cached_links;
        }

        $home_url        = home_url();
        $post            = get_post( $post_id );
        $content         = $post->post_content;
        $links           = [];
        $processed_posts = [];

        // Match all anchor tags
        if ( preg_match_all( '/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $content, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                $url         = $match[1];
                $anchor_text = $match[2];

                // Check if the link is internal
                if ( strpos( $url, $home_url ) === 0 ) {
                    $linked_post_id = url_to_postid( $url );
                    if ( $linked_post_id && $linked_post_id !== $post_id ) {
                        if ( ! isset( $processed_posts[ $linked_post_id ] ) ) {
                            $linked_post                        = get_post( $linked_post_id );
                            $processed_posts[ $linked_post_id ] = [
                                'ID'          => $linked_post->ID,
                                'title'       => $linked_post->post_title,
                                'type'        => $linked_post->post_type,
                                'categories'  => get_the_category_list( ', ', '', $linked_post->ID ),
                                'anchor_text' => $anchor_text,
                            ];
                        } else {
                            $processed_posts[ $linked_post_id ]['anchor_text'] .= ', ' . $anchor_text;
                        }
                    }
                }
            }
        }

        // Match shortcode links
        if ( preg_match_all( '/\[laci_related_post_content id="(\d+)" title="([^"]+)"\]/', $content, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                $related_post_id = $match[1];
                $shortcode_title = $match[2];

                if ( $related_post_id && $related_post_id !== $post_id ) {
                    if ( ! isset( $processed_posts[ $related_post_id ] ) ) {
                        $linked_post                         = get_post( $related_post_id );
                        $processed_posts[ $related_post_id ] = [
                            'ID'          => $linked_post->ID,
                            'title'       => $linked_post->post_title,
                            'type'        => $linked_post->post_type,
                            'categories'  => get_the_category_list( ', ', '', $linked_post->ID ),
                            'anchor_text' => $shortcode_title,
                        ];
                    } else {
                        $processed_posts[ $related_post_id ]['anchor_text'] .= ', ' . $shortcode_title;
                    }
                }
            }
        }

        // Store the processed posts in links array
        $links = array_values( $processed_posts );

        wp_cache_set( $cache_key, $links, 'internal_links', 12 * HOUR_IN_SECONDS );
        $internal_links_cache[ $cache_key ] = $links;

        return $links;
    }

    public static function get_inbound_internal_links_in_taxonomy( $post_id ) {
        global $internal_links_cache;

        // Dynamically retrieve the taxonomy from the settings, with 'category' as the default.
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

        // Get terms (e.g., categories, tags) associated with the post for the chosen taxonomy.
        $post_terms = wp_get_post_terms( $post_id, $taxonomy );
        $term_ids   = array_map(
            function( $term ) {
                return $term->term_id;
            },
            $post_terms
        );

        // Get all child terms of the selected taxonomy terms.
        $all_term_ids = [];
        foreach ( $term_ids as $term_id ) {
            $all_term_ids = array_merge( $all_term_ids, get_term_children( $term_id, $taxonomy ) );
        }
        // Include the original term IDs in the list.
        $all_term_ids = array_merge( $term_ids, $all_term_ids );

        // Cache key includes both taxonomy and terms.
        $cache_key = 'inbound_links_' . $taxonomy . '_' . $post_id . '_' . implode( '_', $all_term_ids );

        if ( isset( $internal_links_cache[ $cache_key ] ) ) {
            return $internal_links_cache[ $cache_key ];
        }

        $cached_links = wp_cache_get( $cache_key, 'internal_links' );
        if ( false !== $cached_links ) {
            $internal_links_cache[ $cache_key ] = $cached_links;
            return $cached_links;
        }

        // Modify the query to use the selected taxonomy and all term IDs (including child terms).
        $args = [
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $all_term_ids, // Use all term IDs including children
                ],
            ],
            'fields'         => 'ids',
        ];

        $query     = new \WP_Query( $args );
        $links     = [];
        $permalink = get_permalink( $post_id );

        $processed_post_ids = [];

        foreach ( $query->posts as $post_id_loop ) {
            if ( $post_id_loop !== $post_id && ! in_array( $post_id_loop, $processed_post_ids ) ) {
                $post         = get_post( $post_id_loop );
                $anchor_texts = [];

                if ( stripos( $post->post_content, $permalink ) !== false ) {
                    if ( preg_match_all( '/<a\s[^>]*href=["\']' . preg_quote( $permalink, '/' ) . '["\'][^>]*>(.*?)<\/a>/i', $post->post_content, $matches ) ) {
                        $anchor_texts = array_merge( $anchor_texts, $matches[1] );
                    }
                }

                if ( has_shortcode( $post->post_content, 'laci_related_post_content' ) ) {
                    $shortcode_regex = get_shortcode_regex( [ 'laci_related_post_content' ] );
                    if ( preg_match_all( '/' . $shortcode_regex . '/s', $post->post_content, $shortcode_matches, PREG_SET_ORDER ) ) {
                        foreach ( $shortcode_matches as $shortcode_match ) {
                            if ( $shortcode_match[2] === 'laci_related_post_content' ) {
                                $shortcode_atts = shortcode_parse_atts( $shortcode_match[3] );
                                if ( isset( $shortcode_atts['id'] ) && $shortcode_atts['id'] == $post_id ) {
                                    $anchor_texts[] = 'Shortcode Link';
                                }
                            }
                        }
                    }
                }

                if ( ! empty( $anchor_texts ) ) {
                    $links[] = [
                        'ID'          => $post->ID,
                        'title'       => $post->post_title,
                        'type'        => $post->post_type,
                        'terms'       => get_the_term_list( $post->ID, $taxonomy, '', ', ', '' ),
                        'anchor_text' => implode( ', ', $anchor_texts ),
                    ];

                    $processed_post_ids[] = $post_id_loop;
                }
            }
        }

        wp_cache_set( $cache_key, $links, 'internal_links', 12 * HOUR_IN_SECONDS );
        $internal_links_cache[ $cache_key ] = $links;

        return $links;
    }

    public static function get_outbound_internal_links_in_taxonomy( $post_id ) {
        global $internal_links_cache;

        // Lấy taxonomy từ settings, mặc định là 'category'.
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

        $cache_key = 'outbound_links_same_taxonomy_' . $post_id . '_' . $taxonomy;

        // Kiểm tra bộ nhớ cache
        if ( isset( $internal_links_cache[ $cache_key ] ) ) {
            return $internal_links_cache[ $cache_key ];
        }

        $cached_links = wp_cache_get( $cache_key, 'internal_links' );
        if ( $cached_links !== false ) {
            $internal_links_cache[ $cache_key ] = $cached_links;
            return $cached_links;
        }

        $home_url     = home_url();
        $post         = get_post( $post_id );
        $content      = $post->post_content;
        $links        = [];
        $linked_posts = [];

        // Lấy danh mục của bài viết
        $post_terms = get_the_terms( $post_id, $taxonomy );
        if ( ! $post_terms || is_wp_error( $post_terms ) ) {
            return [];
        }

        // Lấy ID của các danh mục mà bài viết thuộc về.
        $term_ids = array_map(
            function( $term ) {
                return $term->term_id;
            },
            $post_terms
        );

        // Lấy tất cả các danh mục con của các danh mục mà bài viết thuộc về.
        $all_term_ids = [];
        foreach ( $term_ids as $term_id ) {
            $all_term_ids = array_merge( $all_term_ids, get_term_children( $term_id, $taxonomy ) );
        }

        // Bao gồm cả danh mục cha và danh mục con trong quá trình kiểm tra.
        $all_term_ids = array_merge( $term_ids, $all_term_ids );

        // Tìm các liên kết trong nội dung bài viết
        if ( preg_match_all( '/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $content, $matches ) ) {
            foreach ( $matches[1] as $index => $url ) {
                if ( strpos( $url, $home_url ) === 0 ) {
                    $linked_post_id = url_to_postid( $url );
                    if ( $linked_post_id && $linked_post_id !== $post_id ) {
                        $linked_post_terms = get_the_terms( $linked_post_id, $taxonomy );
                        if ( $linked_post_terms && ! is_wp_error( $linked_post_terms ) ) {
                            $linked_post_term_ids = array_map(
                                function( $term ) {
                                    return $term->term_id;
                                },
                                $linked_post_terms
                            );

                            // Kiểm tra xem bài viết được liên kết có thuộc vào các danh mục hoặc danh mục con không.
                            if ( array_intersect( $all_term_ids, $linked_post_term_ids ) ) {
                                if ( ! isset( $linked_posts[ $linked_post_id ] ) ) {
                                    $linked_posts[ $linked_post_id ] = [
                                        'ID'           => $linked_post_id,
                                        'title'        => get_the_title( $linked_post_id ),
                                        'type'         => get_post_type( $linked_post_id ),
                                        'terms'        => get_the_term_list( $linked_post_id, $taxonomy, '', ', ' ),
                                        'anchor_texts' => [],
                                    ];
                                }
                                $anchor_text                                       = self::get_anchor_text( $content, $url );
                                $linked_posts[ $linked_post_id ]['anchor_texts'][] = $anchor_text;
                            }
                        }
                    }
                }
            }
        }

        // Xử lý các shortcodes laci_related_post_content
        if ( preg_match_all( '/\[laci_related_post_content id="(\d+)" title="([^"]+)"\]/', $content, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                $related_post_id = $match[1];
                $title           = $match[2];
                if ( $related_post_id && $related_post_id !== $post_id ) {
                    $related_post_terms = get_the_terms( $related_post_id, $taxonomy );
                    if ( $related_post_terms && ! is_wp_error( $related_post_terms ) ) {
                        $related_post_term_ids = array_map(
                            function( $term ) {
                                return $term->term_id;
                            },
                            $related_post_terms
                        );

                        // Kiểm tra xem bài viết được liên kết bằng shortcode có thuộc vào các danh mục hoặc danh mục con không.
                        if ( array_intersect( $all_term_ids, $related_post_term_ids ) ) {
                            if ( ! isset( $linked_posts[ $related_post_id ] ) ) {
                                $linked_posts[ $related_post_id ] = [
                                    'ID'           => $related_post_id,
                                    'title'        => get_the_title( $related_post_id ),
                                    'type'         => get_post_type( $related_post_id ),
                                    'terms'        => get_the_term_list( $related_post_id, $taxonomy, '', ', ' ),
                                    'anchor_texts' => [],
                                ];
                            }
                            $linked_posts[ $related_post_id ]['anchor_texts'][] = $title;
                        }
                    }
                }
            }
        }

        // Tạo danh sách các liên kết outbound
        foreach ( $linked_posts as $linked_post_id => $post_data ) {
            $links[] = [
                'ID'          => $post_data['ID'],
                'title'       => $post_data['title'],
                'type'        => $post_data['type'],
                'terms'       => $post_data['terms'],
                'anchor_text' => implode( ', ', $post_data['anchor_texts'] ),
            ];
        }

        // Lưu vào bộ nhớ cache
        wp_cache_set( $cache_key, $links, 'internal_links', 12 * HOUR_IN_SECONDS );
        $internal_links_cache[ $cache_key ] = $links;

        return $links;
    }

    public static function get_internal_links( $post_id ) {
        $inbound_links  = self::get_inbound_internal_links( $post_id );
        $outbound_links = self::get_outbound_internal_links( $post_id );

        return [
            'inbound_links'  => $inbound_links,
            'outbound_links' => $outbound_links,
        ];
    }

    public static function get_anchor_text( $content, $url ) {
        $pattern = '/<a\s[^>]*href=["\']' . preg_quote( $url, '/' ) . '["\'][^>]*>(.*?)<\/a>/i';
        if ( preg_match( $pattern, $content, $matches ) ) {
            return $matches[1];
        }
        return '';
    }

    public function get_outbound_link_for_main_post( $post_id, $category_id ) {
        $outbound_links              = self::get_outbound_internal_links( $post_id );
        $outbound_links_for_category = array_filter(
            $outbound_links,
            function( $link ) use ( $category_id ) {
                $categories = $link['categories__in'];
                return in_array( $category_id, $categories );
            }
        );
        return $outbound_links_for_category;
    }


    public static function get_outbound_link_back_to_taxonomy( $post_id ) {
        $taxonomy = get_option( 'laci_internallinks_taxonomy', 'category' );

        $terms   = wp_get_post_terms( $post_id, $taxonomy );
        $results = [];

        $post_content = get_post_field( 'post_content', $post_id );

        foreach ( $terms as $term ) {
            $term_link = get_term_link( $term->term_id, $taxonomy );

            if ( strpos( $post_content, $term_link ) !== false ) {
                $results[] = [
                    'term_name' => $term->name,
                    'linked'    => 'Yes!',
                    'term_id'   => $term->term_id,
                    'term_link' => $term_link,
                ];
            } else {
                $results[] = [
                    'term_name' => $term->name,
                    'linked'    => 'No',
                    'term_id'   => $term->term_id,
                    'term_link' => $term_link,
                ];
            }
        }

        $total_terms  = count( $terms );
        $linked_terms = count(
            array_filter(
                $results,
                function( $result ) {
                    return $result['linked'] === 'Yes!';
                }
            )
        );

        return [
            'total_links'              => $linked_terms,
            'total_outbound_main_post' => $total_terms,
            'link_details'             => $results,
        ];
    }


    public static function count_outbound_links_between_posts( $post_id_1, $post_id_2 ) {
        $post_1_content   = get_post_field( 'post_content', $post_id_1 );
        $post_2_permalink = get_permalink( $post_id_2 );

        $link_count = substr_count( $post_1_content, 'href="' . $post_2_permalink . '"' );

        return $link_count;
    }
}
