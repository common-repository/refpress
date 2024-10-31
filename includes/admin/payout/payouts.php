<?php
$accountList = new \RefPress\Includes\Admin\PayoutList();
$accountList->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
		<?php _e('Payouts', 'refpress'); ?>
    </h1>

    <hr class="wp-header-end">

    <p> <?php _e( 'Search payout history with Name, Email', 'refpress' ); ?> </p>

    <form id="students-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
		<?php
		$accountList->search_box(__('Search', 'refpress'), 'refpress_payouts_search');
		$accountList->display(); ?>
    </form>
</div>
