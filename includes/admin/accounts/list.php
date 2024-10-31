<?php
$accountList = new \RefPress\Includes\Admin\AccountsList();
$accountList->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
		<?php _e('Affiliate Accounts', 'refpress'); ?>
    </h1>

	<?php if ( refpress_has_pro() ) { ?>
        <a href="<?php echo add_query_arg( array( 'sub_page' => 'add_new_account' ) ); ?>" class="page-title-action">
            + <?php _e('Add New Affiliate Account', 'refpress'); ?>
        </a>
	<?php } ?>

    <hr class="wp-header-end">

	<?php
	if ( ! refpress_has_pro() ) {
		?>
        <div class="refpress-get-pro-text-wrap">
			<?php
			$is_edit_action = refpress_get_input_text( 'sub_page' );
			if ( $is_edit_action === 'edit_account' ) {
				echo "<p class='info-text'> ℹ️ " . __( 'To edit affiliate account, you will need the RefPress Pro', 'refpress' ) . " </p>";
			}
			?>

            <h4> <?php _e( 'Get the pro version to have the following features', 'refpress' ); ?> </h4>

            <ul>
                <li> ✅ <?php _e( 'Add new affiliate account', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Create affiliate account directly from the users list', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Edit any affiliate account', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Edit Account specific affiliate commission', 'refpress' ); ?> </li>
                <li> ✅ <?php _e( 'Enable Disable custom commission for specific affiliate account', 'refpress' ); ?> </li>
            </ul>
        </div>
		<?php
	}
	?>

    <form id="students-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
		<?php
		$accountList->search_box(__('Search', 'refpress'), 'affiliate_accounts_search');
		$accountList->display(); ?>
    </form>
</div>
