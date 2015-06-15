<?php

/**
 * Created by PhpStorm.
 * User: denis_000
 * Date: 24.05.2015
 * Time: 15:23
 */
class UAM_Fake_Request_Classifier
{
    public function classify_click($event){
        if($this->is_crawler())
            return false;

        global $wpdb;
        $prop = 0; //we ar optimistic, no fake.

        $table_name = UAM_Config::$table_name_events;
        $db_map = UAM_Config::$db_map;

        //get most recent click of this ip
        $query = "SELECT * FROM $table_name WHERE ip = '%s' AND type = %d ORDER BY time DESC LIMIT 1";

        $old_event = $wpdb->get_results( $wpdb->prepare($query, $event['ip'], $db_map['click']),ARRAY_A  );
        if($wpdb->num_rows === 1){
            $old_time  = strtotime($old_event[0]['time']);
            $curr_time = strtotime($event['time']);
            $diff_in_sec = $curr_time - $old_time;

            if($diff_in_sec <= 60 && $old_event[0]['uudi'] !== $event['uuid']){ //somebody wants to produce 2 total events
                $prop = 1;
            }
        }
    }

    public function classify_view($event){
        if($this->is_crawler())
            return false;
    }



    public function is_crawler(){
        $crawlers_agents = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';

        if ( strpos($crawlers_agents , $_SERVER['HTTP_USER_AGENT']) === false )
            return false;
        return true;
    }
}