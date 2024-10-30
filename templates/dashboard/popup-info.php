<?php
defined( 'ABSPATH' ) || exit;
$taxonomy_data = get_option( 'laci_internallinks_taxonomy', 'category' );
$taxonomy_data = get_taxonomy( $taxonomy_data );

$terms = get_the_term_list( $post_id, $taxonomy_data->name, '', ', ', '' );
?>

<h3><?php echo esc_html__( 'Information for: ', 'laci-link-cluster' ) . esc_html( $post_title ); ?></h3>

<?php
if ( 'link_back_to_category' === $link_type ) {
    ?>
    <table>
        <thead>
            <tr>
                <th><?php echo esc_html( $taxonomy_data->label ); ?></th>
                <th><?php esc_html_e( 'Linked', 'laci-link-cluster' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $links as $item ) : ?>
                <tr>
                    <td><?php echo wp_kses_post( $terms ); ?></td>
                    <td><?php echo wp_kses_post( $item['linked'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
} else {
    ?>
    <table>
        <thead>
            <tr>
                <th><?php esc_html_e( 'Title', 'laci-link-cluster' ); ?></th>
                <th><?php esc_html_e( 'Type', 'laci-link-cluster' ); ?></th>
                <th><?php echo esc_html( $taxonomy_data->label ); ?></th>
                <th><?php esc_html_e( 'Anchor Text', 'laci-link-cluster' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $links as $item ) : ?>
                <tr>
                    <td><a href="<?php echo esc_url( get_edit_post_link( $item['ID'] ) ); ?>"><?php echo esc_html( $item['title'] ); ?></a></td>
                    <td><?php echo esc_html( ucfirst( $item['type'] ) ); ?></td>
                    <td><?php echo wp_kses_post( $terms ); ?></td>
                    <td><?php echo wp_kses_post( $item['anchor_text'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}
