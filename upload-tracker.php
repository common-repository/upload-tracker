<?php
/**
 * Plugin Name: Upload Tracker
 * Description: Easy access your uploaded file web links.
 * Version: 1.3.2
 * Requires at least: 5.0
 * Requires PHP: 5.4
 * Author: BytesUnited
 * Author URI: https://www.bytesunited.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Copyright 2021-2023 Rolandas Dundulis (email: rolandas.dundulis@gmail.com)
 * 
 * Upload Tracker is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *  
 * Upload Tracker is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Upload Tracker. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Uptk;

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define( 'UPTK_VER', '1.3.2' );

function passingFileName($post_ID) {

    if (get_option( 'uutracker_active', false ) != true) {
        return;
    }

    global $table_prefix, $wpdb;
	
    $feUrl = wp_get_attachment_url( $post_ID );

    $tblname = 'ut_linkk';
    $wp_track_table = $table_prefix . "$tblname";

	$wpdb->insert($wp_track_table, array(
        'link' => $feUrl
    ));
}

add_action('add_attachment', '\Uptk\passingFileName');

add_action( 'admin_bar_menu', function( $wp_admin_bar )
{

    $wp_admin_bar->add_menu( array(
        'id'     => 'uptkk',
        'parent' => null,
        'group'  => null,
        'title'  => '<span id="utb_icon" class="ab-icon '.(get_option('uutracker_active', false) ? 'uut-green' : '').'"></span>'.__( 'Upload Tracker' ),
        'href'   => false
    ) );

    $wp_admin_bar->add_node( array(
        'id'        => 'uut-enable',
        'title' => (get_option('uutracker_active', false) ? 'Disable' : 'Enable'),
        'href' => false,
        'parent' => 'uptkk',
        'meta'  => array(
            'onclick'  => 'uwwEnableAjax()'
        )
    ) );

    $wp_admin_bar->add_node( array(
        'id'        => 'uut-show',
        'title' => 'Show Links',
        'href' => false,
        'parent' => 'uptkk',
        'meta'  => array(
            'onclick'  => 'uwwShowAjax()'
        )
    ) );

    $wp_admin_bar->add_node( array(
        'id'        => 'uut-clear',
        'title' => 'Clear Links',
        'href' => false,
        'parent' => 'uptkk',
        'meta'  => array(
            'onclick'  => 'uwwClearAjax()'
        )
    ) );

}, 100 );

function register_all_scripts() 
{
    wp_register_style(
        'uplinkk-css',
        plugin_dir_url( __FILE__ ) . 'styles.css',
        [],
        UPTK_VER
    );

    wp_register_script(
        'uplinkk-js',
        plugin_dir_url( __FILE__ ) . 'uscript.js',
        ['wp-util'],
        UPTK_VER,
        true // Enqueue script in the footer.
    );
}

function load_frontend_scripts(){
    wp_enqueue_style( 'uplinkk-css' );
    wp_enqueue_script( 'uplinkk-js' );
}

add_action( 'wp_loaded', '\Uptk\register_all_scripts' );
add_action( 'admin_enqueue_scripts', '\Uptk\load_frontend_scripts' );

function uut_callback() {
    if (isset($_POST['is_green'])) {
        $_POST['is_green'] === 'true' ? update_option('uutracker_active', false) : update_option('uutracker_active', true);
        wp_send_json_success(get_option('uutracker_active', false));
    }
}

function usww_callback(){
    if (isset($_POST['usww_trigger'])) {
        if ($_POST['usww_trigger'] === 'true') {

            global $table_prefix, $wpdb;

            $tblname = 'ut_linkk';
            $wp_track_table = $table_prefix . "$tblname";

            $wres = $wpdb->get_results("SELECT * FROM $wp_track_table", ARRAY_A);

            wp_send_json_success($wres);
        }
    }
}

function usrr_callback(){
    if (isset($_POST['usrr_clr'])) {
        if ($_POST['usrr_clr'] === 'true'){

            global $table_prefix, $wpdb;

            $tblname = 'ut_linkk';
            $wp_track_table = $table_prefix . "$tblname";

            $delete = $wpdb->query("TRUNCATE TABLE `$wp_track_table`");
            wp_send_json_success($delete);
        }
    }
}

//add_action('wp_ajax_nopriv_uut_callback', '\Uptk\uut_callback');
add_action('wp_ajax_uut_callback', '\Uptk\uut_callback');
add_action('wp_ajax_usww_callback', '\Uptk\usww_callback');
add_action('wp_ajax_usrr_callback', '\Uptk\usrr_callback');

function create_plugin_database_table()
{
    global $table_prefix, $wpdb;

    $tblname = 'ut_linkk';
    $wp_track_table = $table_prefix . "$tblname";

    #Check to see if the table exists already, if not, then create it

    if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
    {
        $sql = "CREATE TABLE `". $wp_track_table . "` ( ";
        $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
        $sql .= "  `link`  text   NOT NULL, ";
        $sql .= "  PRIMARY KEY `order_id` (`id`) "; 
        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ; ";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
}

register_activation_hook( __FILE__, '\Uptk\create_plugin_database_table' );

// Delete table when deactivate
function my_plugin_remove_database() {

        global $table_prefix, $wpdb;

        $tblname = 'ut_linkk';
        $wp_track_table = $table_prefix . "$tblname";

        $sql = "DROP TABLE IF EXISTS $wp_track_table;";
        $wpdb->query($sql);
}    
register_deactivation_hook( __FILE__, '\Uptk\my_plugin_remove_database' );