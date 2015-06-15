<?php
/**
 * Created by PhpStorm.
 * User: denis_000
 * Date: 01.06.2015
 * Time: 17:43
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

    if(get_option( '_site_transint_timeout_browser_a7cef1c8465454dd4238b5bc2f2e819') < time() ){
        echo "<script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('modal-win-link').click();});</script>";
        return true;
    }

    return false;
}
