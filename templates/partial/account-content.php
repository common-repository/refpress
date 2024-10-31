<?php
/**
 * Main Account page, only approved account holders can see this page contents.
 *
 * This template can be overridden by copying it to yourtheme/refpress/partial/account-content.php.
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$current_account_page = get_query_var( 'account_page', 'overview' );

?>

<div class="refpress-container">

    <div class="refpress-account-wrap margin-y-5">

        <div class="refpress-account-menu-wrap">
            <?php refpress_account_menu_items_generate(); ?>
        </div>

        <div class="refpress-account-content">

            <?php
            refpress_locate_template( "accounts/{$current_account_page}", true );
            ?>

        </div>

    </div>

</div>