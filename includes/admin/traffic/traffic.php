<?php
$traffic = new \RefPress\Includes\Admin\TrafficList();
$traffic->prepare_items();
?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php _e('All Traffics', 'refpress'); ?>
	</h1>

	<hr class="wp-header-end">

	<form id="students-filter" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
		<?php
		$traffic->search_box( __('Search', 'refpress'), 'traffics_search');
		$traffic->display(); ?>
	</form>
</div>
