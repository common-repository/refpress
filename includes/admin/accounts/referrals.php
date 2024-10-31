<?php
$accountList = new \RefPress\Includes\Admin\CommissionsList();
$accountList->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Commissions', 'refpress'); ?>
    </h1>

    <hr class="wp-header-end">

    <?php
    if ( ! refpress_has_pro() ) {
        ?>
        <div class="refpress-get-pro-text-wrap">

            <?php
            $is_edit_action = refpress_get_input_text( 'action' );
            if ( $is_edit_action === 'edit' ) {
                echo "<p class='info-text'> ℹ️ " . __( 'To edit or view the commission, you will need the RefPress Pro', 'refpress' ) . " </p>";
            }
            ?>

            <h4> <?php _e( 'Get the pro version to have the following features', 'refpress' ); ?> </h4>

            <ul>
                <li> ✅ <?php _e( 'Edit commission', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Approve commission', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Reject commission', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Delete commission', 'refpress' ); ?> </li>
            </ul>
        </div>
    <?php
    }
    ?>

    <form id="students-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
        <?php
        $accountList->search_box(__('Search', 'refpress'), 'commissions_search');
        $accountList->display(); ?>
    </form>
</div>
