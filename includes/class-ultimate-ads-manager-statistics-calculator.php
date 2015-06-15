<?php
/**
 * Created by PhpStorm.
 * User: denis_000
 * Date: 21.05.2015
 * Time: 23:53
 */

class Statistics_Calculator {
    public $wpdb;
    public $db_map;
    public $time_map;
    public $settings_mgr;
    public $tz;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;

        require_once plugin_dir_path( __FILE__ ) . 'config.php';
        UAM_Config::init();
        $this->time_map = array(
            'day' => (60 * 60 *24),
            'hour' => (60 * 60),
            'week' => (60 * 60 * 24 * 7),
            'hour/10' => 60 * 6
        );

        require_once UAM_Config::$plugin_root_path . '/admin/class-ultimate-ads-manager-settings.php';
        $this->settings_mgr = new Ultimate_Ads_Manager_Settings(UAM_Config::$plugin_name,
            UAM_Config::$plugin_version, UAM_Config::$plugin_display_name);
        $this->settings_mgr->load_settings();
        $this->tz = date_default_timezone_get();
        $this->enter_client_timezone();

    }




    public function get_unique_events($ad_id, $event_type,$ad_slide_id=null, $time_a=null, $time_b=null){
        $unique_events = $this->get_events($ad_id, $event_type,'unique', $ad_slide_id, $time_a, $time_b);

        return $unique_events;
    }

    public function get_total_events($ad_id, $event_type, $ad_slide_id=null, $time_a=null, $time_b=null){
        $total_views = $this->get_events($ad_id, $event_type,'total', $ad_slide_id, $time_a, $time_b);

        return $total_views;
    }

    private function get_events($ad_id, $event_type,$metric='unique', $ad_slide_id=null, $time_a=null, $time_b=null){
        $table_name = UAM_Config::$table_name_events;
        $event_type  = UAM_Config::$db_map[$event_type];
        $count_param = $metric === 'unique' ? 'DISTINCT uuid' : '*';


        $query = "SELECT COUNT($count_param) FROM $table_name WHERE type = $event_type AND ad_id = %d";

        if(isset($ad_slide_id)){
            $query .=  $this->wpdb->prepare(" AND ad_slide_id = %d", $ad_slide_id);
        }

        if(isset($time_a) && isset($time_b)){
            $f_c = $this->int_to_date($time_a);
            $a = $this->int_to_date( $this->convert_time_to_server_tz($time_a));
            $b = $this->int_to_date($this->convert_time_to_server_tz($time_b));

            date_default_timezone_set($this->tz);

            //exit("from-client:$f_c\n from: $a to: $b\n time: ".current_time( 'mysql' ));
            $query .= $this->wpdb->prepare(" AND time BETWEEN '%s' AND '%s' ", $a, $b);
        }

        $unique_events = $this->wpdb->get_var( $this->wpdb->prepare($query, $ad_id)  );

        return $unique_events;
    }



    private function get_periods($ad_id, $event_type, $metric, $ad_slide_id, $time_a, $time_b, $step='day'){
        $res = array();
        $intervals = $this->make_intervals($step,$time_a, $time_b);//ceil($diff_time/$step_sec);

        if($time_a > $time_b)
            throw new Exception('time_a must be smaller than time_b');

        /*$tz = date_default_timezone_get();
        $client_tz = isset($this->settings_mgr->general_settings['block_timezone']) ?
            $this->settings_mgr->general_settings['block_timezone'] : 'America/Los_Angeles';
        date_default_timezone_set($client_tz);*/ //TODO: get users timezone from settings

        for($i =0; $i < count($intervals)-1; $i++){
            $from = $intervals[$i];
            $to = $intervals[$i+1];
            $events = $this->get_events($ad_id,$event_type,$metric,$ad_slide_id, $from, $to );



            /*$e_obj = new stdClass();
            $e_obj->from = $from; //date('r',$from);//$from;
            $e_obj->to = $to;
            $e_obj->data = $events;
            array_push($res, $e_obj);*/



            $e_obj = new stdClass();
            $e_obj->from = $from; //$this->convert_time_to_client_tz($from); //strtotime(date('r',$from));//$from;
            $e_obj->to = $to; //$this->convert_time_to_client_tz($to); //strtotime(date('r',$to));
            $e_obj->data = $events;
            array_push($res, $e_obj);
        }
        //date_default_timezone_set($tz);

        return $res;


    }

    private function make_intervals($step,$from, $to){


        $intervals = array();


        if(isset($this->time_map[$step])){
            $intermediate = $from;
            while($intermediate < $to){
                array_push($intervals, $intermediate);
                $intermediate += $this->time_map[$step];
            }

            array_push($intervals, $to);

        }elseif($step === 'month'){

            $client_tz_from = $from;// $this->convert_time_to_client_tz($from);
            $client_tz_to = $to;// $this->convert_time_to_client_tz($to);

            //exit("from: $client_tz_from ; to: $client_tz_to");

            //$this->enter_client_timezone();
            $from_month = date('n',$client_tz_from);
            $from_year = date('Y',$client_tz_from);
            $intermediate = strtotime("1-$from_month-$from_year 00:00:00");

            while($intermediate < $client_tz_to){

                //array_push($intervals, $this->convert_time_to_server_tz($intermediate)-1 );
                array_push($intervals, $intermediate-1 );
                if($from_month < 12)$from_month++;
                else{
                    $from_month = 1;
                    $from_year++;
                }
                $intermediate = strtotime("1-$from_month-$from_year 00:00:00");

            }

            //$this->leave_client_timezone();

            //array_push($intervals, $this->convert_time_to_server_tz($to));
            array_push($intervals, $to);

        }elseif($step === 'infinite'){
            $intervals = array($from,$to);
        }


            //date_default_timezone_set($tz);


        return $intervals;

    }

    private function int_to_date($int){
        return date("Y-m-d G:i:s", $int);
    }

    private function enter_client_timezone(){
        $client_tz = isset($this->settings_mgr->general_settings['block_timezone']) ?
            $this->settings_mgr->general_settings['block_timezone'] : 'America/Los_Angeles';
        date_default_timezone_set($client_tz);
    }

    private function leave_client_timezone(){
        date_default_timezone_set($this->tz);
    }

    private function convert_time_to_client_tz($t){
        $def_tz = date_default_timezone_get();

        $this->leave_client_timezone();
        $server_tz_time = date('r',$t);
        $this->enter_client_timezone();
        $client_tz_time = strtotime($server_tz_time);
        //$this->leave_client_timezone();

        date_default_timezone_set($def_tz);

        return $client_tz_time;
    }

    private function convert_time_to_server_tz($t){
        $def_tz = date_default_timezone_get();

        $this->enter_client_timezone();
        $client_tz_time = date('r',$t);
        $this->leave_client_timezone();
        $server_tz_time = strtotime($client_tz_time);

        date_default_timezone_set($def_tz);

        return $server_tz_time;
    }
/*
    public function get_today_passed_time(){
        return time() - strtotime(date("Y-m-d 00:00:00",time()));
    }*/


    ////////////////// Public API /////////////////

    public function get_last_7_days($query){
        $ad_id = $query['ad_id'];
        $event_type = $query['type'];
        $metric = $query['metric'];
        $ad_slide_id = $query['ad_slide_id'];

        $seven_days_ago = $query['from'] - ($this->time_map['week']);
        $periods = $this->get_periods($ad_id, $event_type,$metric, $ad_slide_id,
            $seven_days_ago, $query['from'],'day' );


        return $periods;
    }

    public function get_last_24_hours($query){
        $ad_id = $query['ad_id'];
        $event_type = $query['type'];
        $metric = $query['metric'];
        $ad_slide_id = $query['ad_slide_id'];

        $_24_hours_ago = $query['from'] - $this->time_map['hour'] *24;
        $periods = $this->get_periods($ad_id, $event_type,$metric, $ad_slide_id,
            $_24_hours_ago, $query['from'],'hour');

        return $periods;
    }

    public function get_last_hour($query){
        $ad_id = $query['ad_id'];
        $event_type = $query['type'];
        //echo "eho";
        $metric = $query['metric'];
        $ad_slide_id = $query['ad_slide_id'];

        $_1_hour_ago = $query['from'] - $this->time_map['hour'];
        //echo "technology";exit;
        $periods = $this->get_periods($ad_id, $event_type,$metric, $ad_slide_id,
            $_1_hour_ago, $query['from'],'hour/10');

        return $periods;
    }

    public function get_last_12_months($query){
        $ad_id = $query['ad_id'];
        $event_type = $query['type'];
        $metric = $query['metric'];
        $ad_slide_id = $query['ad_slide_id'];



        //$this->enter_client_timezone();

        $from_month = date('n',$query['from']) === 12 ? 1: date('n',$query['from'])+1;
        $from_year = date('n',$query['from']) === 12 ? date('Y',$query['from'])+1 : date('Y',$query['from']);
        $end_of_month = strtotime("1-$from_month-$from_year 00:00:00");
        $prev_year = $from_year -1;
        $_12_months_ago = strtotime("1-$from_month-$prev_year 00:00:00");

        $server_tz_12_months_ago = date('r',$_12_months_ago);
        $server_tz_end_of_month = date('r',$end_of_month);

        //$this->leave_client_timezone();
        //exit("from:$server_tz_12_months_ago ; to: $server_tz_end_of_month");
        $server_tz_12_months_ago = strtotime($server_tz_12_months_ago);
        $server_tz_end_of_month = strtotime($server_tz_end_of_month)-1;


        //$periods = $this->get_periods($ad_id, $event_type,$metric, $ad_slide_id,
        //    $_12_months_ago, $end_of_month,'month');
        $periods = $this->get_periods($ad_id, $event_type,$metric, $ad_slide_id,
            $server_tz_12_months_ago, $server_tz_end_of_month,'month');



        return $periods;
    }

    public function get_all_time($query){
        $ad_id = $query['ad_id'];
        $event_type = $query['type'];
        $metric = $query['metric'];
        $ad_slide_id = $query['ad_slide_id'];

        $periods = $this->get_periods($ad_id, $event_type,$metric, $ad_slide_id,
            0, time()+$this->time_map['day'] ,'infinite');

        return $periods;
    }


}