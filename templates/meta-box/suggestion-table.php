<?php
defined( 'ABSPATH' ) || exit;
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th {
        background-color: #f1f1f1;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    table td.copy {
        text-align: center;
    }
</style>

<table class="laci-table-suggested">
    <thead>
        <tr>
            <th><b><?php esc_html_e( 'Phrases In This Post To Link From', 'laci-link-cluster' ); ?></b></th>
            <th><b><?php esc_html_e( 'Suggested Posts To Link To', 'laci-link-cluster' ); ?></b></th>
            <th><b><?php esc_html_e( 'Action', 'laci-link-cluster' ); ?></b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $suggestions as $suggestion ) { ?>
            <tr>
                <td>                 
                    <?php echo esc_html( $suggestion['anchor_text'] ); ?>
                </td>
                <td>
                    <div>
                        <strong><?php esc_html_e( 'Title', 'laci-link-cluster' ); ?></strong> 
                        <span><i><strong><?php echo esc_html( $suggestion['title_post_outbound'] ); ?></strong></i></span>
                    </div>
                    <div>
                        <strong><?php esc_html_e( 'Url:', 'laci-link-cluster' ); ?></strong> 
                        <span><a href="<?php echo esc_url( $suggestion['url_post_outbound'] ); ?>"><?php echo esc_url( $suggestion['url_post_outbound'] ); ?></a></span>
                    </div>
                </td>
                <td> 
                    <button class="button button-primary laci-table-suggested-button-copy"><?php esc_html_e( 'Copy', 'laci-link-cluster' ); ?></button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </tbody>
</table>
