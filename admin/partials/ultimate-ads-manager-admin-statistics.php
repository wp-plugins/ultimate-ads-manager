<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */


function uam_codeneric_modal()
{
    add_thickbox();
    ?>


    <a href="#TB_inline?height=521&width=783&inlineId=modal-window-id" id="modal-win-link" class="thickbox hidden"
       title="Go Premium"></a>


    <div  id="modal-window-id" style="display:none;">
        <h3>Get the Premium Version for Free</h3>
        <p>You need the premium Ultimate Ads Manager plugin for unlimited usage. You can also get it for free if you leave a short review on <a href="https://wordpress.org/support/view/plugin-reviews/ultimate-ads-manager" target="_blank">wordpress.org</a>:</p>
        <p>1. Register <a href="https://wordpress.org/support/register.php" target="_blank">here</a>
            if you don't already have an account <small>(and remember your username for the 2. step).</small>
        </p>
        <p>2. Type in your <strong>wordpress.org username</strong>: <input type="text" id="cc-username" /> </p>
        <p>3. Click on this <a href="https://wordpress.org/support/view/plugin-reviews/ultimate-ads-manager" target="_blank">link</a> and write a review (you need to be logged in). </p>
        <p><small>Support: contact@codeneric.com</small></p>
        <input id="cc-go-premium" type="button" value="Check my review and unlock premium!" class="button-primary" onclick="checkReview()" />
        <div style="float: none; vertical-align: text-bottom;" id="cc-premium-spinner" class="spinner"></div>
    </div>

    <?php
    /*
                Hello, fellow developer!

				Apparently, you really want to see this part of our plugin.
				This is where we set the expiration date for the payment.
				If you can not afford the plugin any longer, feel free to hack this part.
				However, we are working hard to deliver this awesome product, so please consider to subscribe as soon as you can.

				Best,
				Ultimate Ads Plugin Team
    */

    if(get_option( '_site_transint_timeout_browser_a7cef1c8465454dd4238b5bc2f2e819') < time() ){



            echo "<script>document.addEventListener('DOMContentLoaded',function(){

                        document.getElementById('modal-win-link').click();});


                  </script>";
        return true;

    }

    return false;
}


function ultimate_ads_manager_statistics_page() {

    //require_once plugin_dir_path( __FILE__ ) . '../../includes/config.php';
    //require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/class-ultimate-ads-manager-statistics-calculator.php';
    //$Stats_Calc = new Statistics_Calculator();
    // echo "Total clicks:". $Stats_Calc->get_total_events(6, 'click', null, current_time('mysql'), date("F j, Y", time() - 60 * 60 * 24));
    // echo "Unique views:". $Stats_Calc->get_unique_events(6, 'view');
    //echo "Last seven days: ".json_encode($Stats_Calc->get_last_7_days(6,'click'));
    //echo "Last 24 hours: ".json_encode($Stats_Calc->get_last_24_hours(43,'click','total'));

    //require_once "subscription-modal.php";
    $a = uam_codeneric_modal();

    $uuid = get_option('codeneric_uam_uuid');

    ?>

    <div class="wrap">
        <h2><?php echo __('Statistics'); ?></h2>


        <div id="uam_statistics<?php echo $a; ?>">
            <?php echo $a ? '<h3>Do you like the Ultimate Ads Manager?</h3><h3>Then please get the full version <a href="javascript:history.go(0)">here.</a></h3>' : ''; ?>
        </div>




    </div>




    <?php


}