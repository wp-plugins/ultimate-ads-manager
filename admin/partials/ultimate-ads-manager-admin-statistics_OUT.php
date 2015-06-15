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


    <div id="modal-window-id" style="display:none;">
        <h3>Trusted and used by hundreds of website owners around the globe.</h3>

        <p>You need the full version of the Ultimate Ads Manager plugin for unlimited usage.</p>

        <p>It costs only <strong>9 USD per month</strong>.
        </p>
        <p>The subscription permits you to use the Ultimate Ads Manager on one wordpress instance
            and can be <strong>canceled any time</strong>. </p>
        <p>
            <small>Support: contact@codeneric.com</small>
        </p>
        <div style="float: none; vertical-align: text-bottom;" id="cc-premium-spinner" class="spinner"></div>
        <script async="async" src="https://www.paypalobjects.com/js/external/paypal-button.min.js?merchant=elance-facilitator@codeneric.com"
                data-button="subscribe"
                data-name="Ultimate Ads Manager"
                data-amount="9"
                data-recurrence="1"
                data-period="M"
                data-callback="http://wp-plugins-server.codeneric.com/pay"
                data-env="sandbox"
                data-src="1"
                data-no_shipping="1"
                data-custom="<?php echo get_option('codeneric_uam_uuid'); ?>"
            <?php
            function isSecure()
            {
                return
                    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || $_SERVER['SERVER_PORT'] == 443;
            }

            $return_url = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $return_url = isSecure() ? "https://$return_url" : "http://$return_url";
            $return_url = add_query_arg('paid', 'yes', $return_url);

            ?>
                data-return="<?php echo $return_url; ?>"
            ></script>
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
        //Expired! Ask server if user has paid for the next month...
        $uuid = get_option('codeneric_uam_uuid');
        $res = wp_remote_get( "http://wp-plugins-server.codeneric.com/paid/subscription/ultimate-ads-manager/$uuid" );
        print_r($res['response']);
        if(empty($res))return false;//server down
        if($res['response']['code'] !== 200){
            echo "<script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('modal-win-link').click();});</script>";
            return true;
        }else{
            update_option( '_site_transint_timeout_browser_a7cef1c8465454dd4238b5bc2f2e819', time() + 60 * 60 * 24 * 33);
        }

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

    ?>

    <div class="wrap">
        <h2><?php echo __('Statistics'); ?></h2>


        <div id="uam_statistics<?php echo $a; ?>">
            <?php echo $a ? '<h3>Do you like the Ultimate Ads Manager?</h3><h3>Then please purchase the full version <a href="javascript:history.go(0)">here.</a></h3>' : ''; ?>
        </div>




    </div>




    <?php


}