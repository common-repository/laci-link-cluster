<?php
namespace LACI_InternalLinks\Controllers;

use LACI_InternalLinks\Utils\SingletonTrait;

/**
 *
 * @method static MetaBoxAddKeyWordController get_instance()
 */
class MetaBoxAddKeyWordController {

    use SingletonTrait;

    protected function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_key_word_for_post' ] );
        add_action( 'save_post', [ $this, 'save_key_word_for_post' ] );
    }

    public function add_key_word_for_post() {
        add_meta_box(
            'laci_add_key_word_meta_box',
            '<p>Main Key words</p>',
            [ $this, 'render_key_word_for_post' ],
            [ 'page', 'post' ],
            'side',
            'default'
        );
    }

    public function render_key_word_for_post( $post ) {
        wp_nonce_field( 'laci_add_key_word_nonce', 'laci_add_key_word_nonce' );
        $key_words         = get_post_meta( $post->ID, 'laci_list_key_word', true );
        $key_words_implode = is_array( $key_words ) ? implode( ',', $key_words ) : '';
        ?>
            <div class="laci-update-keywords">
                <input type="text" name="laci_add_key_word" style="width: 100%;margin-bottom:10px">
                <button type="button" class="laci-add-keyword-button button" style="margin-bottom:10px"><?php esc_html_e( 'Add Keyword', 'laci-link-cluster' ); ?></button>
                <div>
                    <label> <?php esc_html_e( 'Configured keywords:', 'laci-link-cluster' ); ?></label>
                    <ul class='laci-list-key-word'>
                        <?php
                        if ( is_array( $key_words ) ) {
                            foreach ( $key_words as $key_word ) {
                                if ( empty( $key_word ) ) {
                                    continue;
                                }
                                ?>
                                <li class="laci-list-key-word__item" data-key="<?php echo esc_html( $key_word ); ?>">
                                    <a class="dashicons dashicons-dismiss remove laci-remove-key-word"></a>
                                    <?php echo esc_html( $key_word ); ?>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <input type="hidden" name="laci_list_key_word" value="<?php echo esc_attr( $key_words_implode ); ?>" style="width: 100%">
            </div>
        <?php
    }

    public function save_key_word_for_post( $post_id ) {
        if ( ! isset( $_POST['laci_add_key_word_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['laci_add_key_word_nonce'] ) ), 'laci_add_key_word_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        $list_key_word = isset( $_POST['laci_list_key_word'] ) ? sanitize_text_field( wp_unslash( $_POST['laci_list_key_word'] ) ) : '';
        $list_key_word = explode( ',', trim( $list_key_word ) );
        update_post_meta( $post_id, 'laci_list_key_word', $list_key_word );
    }
}
