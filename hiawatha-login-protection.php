<?php
/**
 * Plugin Name: Hiawatha Login Protection
 * Plugin URI: http://mfyu.co.uk/code/hiawatha-login-protection
 * Description: Plugin to ban repeated failed login attempts on <a href="https://www.hiawatha-webserver.org/">Hiawatha</a> servers via the <a href="https://www.hiawatha-webserver.org/manpages/hiawatha">BanByCGI option</a>. You need to make sure you've got the BanByCGI option enabled and setup correctly in your Hiawatha configuration file. <strong>BanByCGI = yes|no[, &lt;max value&gt;]</strong>. If you have any other plugins that alter logins, or interact with the WordPress authentication system, this plugin may not function as expected. Also, if another function implements this function already, this plugin won't do anything.
 * Version: 0.0.1
 * Author: Matt Brunt
 * Author URI: http://mfyu.co.uk
 * License: MIT
 */

/*
http://ottopress.com/2009/wordpress-settings-api-tutorial/
http://codex.wordpress.org/Creating_Options_Pages
http://codex.wordpress.org/Writing_a_Plugin#Saving_Plugin_Data_to_the_Database
http://codex.wordpress.org/Writing_a_Plugin
http://codex.wordpress.org/Creating_Tables_with_Plugins
 */

defined( 'ABSPATH' ) or die();

include_once "hiawathaplugin.php"; // no idea how class autoloading works in WordPress, this'll do for now - only a simple plugin.

register_activation_hook( __FILE__, array('HiawathaPlugin', 'activate') );
register_deactivation_hook( __FILE__, array('HiawathaPlugin', 'deactivate') );

/**
 * Over-writing the standard wordpress login function.
 * @param $username
 * @param $password
 * @return mixed|void|WP_Error
 */

if ( !function_exists('wp_authenticate') ) :
function wp_authenticate($username, $password) {
    $username = sanitize_user($username);
    $password = trim($password);

    //

    /*
        Because this function we're ine runs when the page loads as well as when the form is submitted.
        I've gotta do a check to only run the filters when form is submitted and fields are empty as that's one of the requirements
        WordPress doesn't seem to throw any errors etc if the username and password are empty, it doesn't see them as 'incorrect'
        What the hell...
    */
    $hasBeenChecked = false;
    if(isset($_POST['log']) && empty($_POST['log']) || isset($_POST['pwd']) && empty($_POST['pwd'])) {
        $hasBeenChecked = true;
        HiawathaPlugin::runHiawathaCheck();
    }

    $user = apply_filters( 'authenticate', null, $username, $password );

    if ( $user == null ) {
        // TODO what should the error message be? (Or would these even happen?)
        // Only needed if all authentication handlers fail to return anything.
        $user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));

        // if we've not already run the check, do it here.
        if( ! $hasBeenChecked) {
            $hasBeenChecked = true;
            HiawathaPlugin::runHiawathaCheck();
        }
    }

    $ignore_codes = array('empty_username', 'empty_password');

    if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {

        // if we've not already run the check, do it here.
        if( ! $hasBeenChecked) {
            $hasBeenChecked = true;
            HiawathaPlugin::runHiawathaCheck();
        }
        do_action( 'wp_login_failed', $username );
    }

    return $user;
}
endif;