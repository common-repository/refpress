<?php
/**
 * RefPress Account page
 *
 * This template can be overridden by copying it to yourtheme/refpress/accounts.php.
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


get_header();

do_action('refpress/account/wrap/before'); ?>


<div class="refpress-container">

    <?php do_action( 'refpress/account_page/before' ); ?>
    <?php echo refpress_render_account_page(); ?>
    <?php do_action( 'refpress/account_page/after' ); ?>

</div>

<?php
do_action('refpress/account/wrap/after');

get_footer();