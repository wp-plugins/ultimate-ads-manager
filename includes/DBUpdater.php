<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 26.03.2015
 * Time: 15:35
 */

namespace cc_ads_manage;


class DBUpdater {
    static $versions = array('1.0.0');
    static $currVersion;// = '1.0.0';




    static function version_to_func_name($newVersion){
        $tempNew = str_replace(".", "_", $newVersion);

        return "update_to_$tempNew";
    }

    static function updateDB(){
        $varTemp = get_plugin_data( dirname(__FILE__).'/../ultimate-ads-manager.php', false, false );
        DBUpdater::$currVersion = $varTemp['Version'];
        $oldVersion = get_option( 'cc_ads_manage_curr_version' );

        if($oldVersion === DBUpdater::$currVersion)return;

        //flush_rewrite_rules();


        update_option( 'cc_photo_manage_curr_version',DBUpdater::$currVersion );
        $oldVersionIndex = array_search($oldVersion, DBUpdater::$versions);

        if($oldVersionIndex === false)return; //first installation

        $funcContainer = new FunctionContainer();

        for($i = $oldVersionIndex+1; $i< count(DBUpdater::$versions); $i++ ){
            $funcName = DBUpdater::version_to_func_name(DBUpdater::$versions[$i]);

            if(method_exists ($funcContainer, $funcName)){ //we have to perform some operations on the database
                $funcContainer->$funcName();
            }
        }



    }


}

class FunctionContainer{
    /*
    function update_to_1_1_0(){ //update from 1.0.1 to 1.1.0
        $options = array('cc_photo_image_box'=> 1, 'cc_photo_download_text'=> 'Download all' );
        update_option( 'cc_photo_settings', $options );

        $posts_array = get_posts( "post_type=client" );
        foreach($posts_array as $client){
            $projects = get_post_meta($client->ID,"projects",true);
            foreach($projects as &$project)
                $project['downloadable'] = true;
            //print_r($projects);
            update_post_meta($client->ID,"projects", $projects);
        }
    }
    */
}