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

function codeneric_ad_manager_settings_page() {

    ?>
    <form action='options.php' method='post'>
        <div class="wrap">
            <h2><?php echo __('Settings'); ?></h2>
            <div class="postbox">
                <div class="inside">
                    <?php settings_fields( 'codeneric_ad_general_settings' ); ?>
                    <?php do_settings_sections( 'codeneric_ad_general_settings' ); ?>
                    <?php submit_button(); ?>
                </div>
            </div>
        </div>
    </form>

    <?php
}