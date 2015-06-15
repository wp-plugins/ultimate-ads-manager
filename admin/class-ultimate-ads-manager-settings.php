<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://codeneric.com
 * @since      1.0.0
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ultimate_Ads_Manager
 * @subpackage Ultimate_Ads_Manager/admin
 * @author     Codeneric <contact@codeneric.com>
 */

class Ultimate_Ads_Manager_Settings  {
     /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    private $display_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */

    private $plugin_options_key = 'codeneric_ad_options';
    private $general_settings_key = 'codeneric_ad_general_settings';
    public $general_settings;


    private $default_settings = array(
        'block_adblock' => 0
    );

    public function __construct($plugin_name,$version, $display_name ) {
        $this->plugin_name = $plugin_name;
        $this->display_name = $display_name;
        $this->version = $version;
        $temp = get_option('timezone_string');
        $this->default_settings['block_timezone'] = empty($temp) ? 'America/New_York' : $temp;
        //echo "TZZzzzzzzzzzzzzzzzzzooooooooooooooooon:" . $this->default_settings['block_timezone'];
    }


    public function register_general_settings() {

        register_setting( $this->general_settings_key, $this->general_settings_key );
        $section_key = 'section_general';

        add_settings_section( $section_key, __('General Settings'), array( &$this, 'section_general_desc' ), $this->general_settings_key );


        add_settings_field( 'block_adblock', __('Block AdBlock'), array( &$this, 'field_block_adbock' ), $this->general_settings_key, $section_key );

        add_settings_field( 'block_timezone', __('Your Timezone'), array( &$this, 'field_block_timezone' ), $this->general_settings_key, $section_key );

    }
    public function section_general_desc() { echo 'General section description goes here.'; }
    public function field_block_adbock() {
        ?>
        <label title="Block AdBlock">
            <input type="checkbox" name="<?php echo $this->general_settings_key; ?>[block_adblock]"  <?php checked( $this->general_settings['block_adblock'], 1 ); ?>  value="1" />
            <small><?php echo __('Success not guaranteed.'); ?></small>
        </label>



        <?php
    }

    public function field_block_timezone(){
        $tz = DateTimeZone::listIdentifiers();

        ?>
        <label title="Your Timezone">
            <select name="<?php echo $this->general_settings_key; ?>[block_timezone]"
                    value="" >
                <?php foreach($tz as $key=>$value): ?>
                    <option value="<?php echo $value; ?>" <?php
                    if(isset($this->general_settings['block_timezone']) && $this->general_settings['block_timezone'] === $value)
                        echo 'selected';
                    ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
            <button class="button" id="guess-tz">guess timezone</button>
            <small><?php echo __('Click this button if you want to automatically guess your timezone by your local time.') ?></small>

        </label>

        <?php
    }
    public function load_settings() {
        $this->general_settings = (array)  get_option( $this->general_settings_key );
        $this->general_settings = array_merge( $this->default_settings, $this->general_settings );

    }
}

