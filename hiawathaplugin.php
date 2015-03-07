<?php

/**
 * Class HiawathaPlugin
 * This is just a vague wrapper for procedural code, it's not OO, just a class that holds static functions.
 *
 * I'm not hugely experienced with WP so I just got this to work.
 *
 * So many static functions... and globals. Ack.
 */
class HiawathaPlugin {

    /**
     *
     */
    public static function runHiawathaCheck() {
        global $wpdb; // wordpress Database
        $tableName = $wpdb->prefix . 'hiawatha_login_protection_entries';


        $options = get_option('hiawatha-protection-settings');

        $triesAllowed = $options['hiawatha-protection-login-attempts'];
        $timePeriod = $options['hiawatha-protection-time-period'];
        $banTime = $options['hiawatha-protection-ban-duration'];

        // insert new failed login attempt into DB for the IP address
        $userIp = self::getUserIp();

        $timeAttempted = date("Y-m-d H:i:s", time());

        $query = "INSERT INTO {$tableName} (ip_address, attempted_at)
                  VALUES('{$userIp}', '{$timeAttempted}');";

        $wpdb->query($query); // run query to insert failed attempt

        $numberOfPreviousTries = self::getNumberOfLoginAttemptsForIPInTimePeriod($userIp, $timePeriod);

        if($numberOfPreviousTries >= $triesAllowed) {
            header( "X-Hiawatha-Ban: {$banTime}" );
        }
    }


    /**
     * @param $ipAddress
     * @param $timePeriod
     * @return int
     */
    public static function getNumberOfLoginAttemptsForIPInTimePeriod($ipAddress, $timePeriod) {
        global $wpdb; // wordpress Database
        $tableName = $wpdb->prefix . 'hiawatha_login_protection_entries';
        $timeAttemptedFrom = date("Y-m-d H:i:s", time() - $timePeriod);

        $query = "SELECT * FROM {$tableName}
                      WHERE ip_address = '{$ipAddress}'
                      AND attempted_at > '{$timeAttemptedFrom}'";

        $results = $wpdb->get_results($query);

        return count($results);
    }

    /**
     * @return mixed
     */
    public static function getUserIp() {
        if ( ! empty($_SERVER['HTTP_CLIENT_IP']))
        {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            // is the IP from a proxy?
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    /**
    *
    */
    public static function activate() {
        global $wpdb; // wordpress Database
        $tableName = $wpdb->prefix . 'hiawatha_login_protection_entries';

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        add_option(
            'hiawatha-protection-settings',
                array(
                    'hiawatha-protection-login-attempts'    =>	5,
                    'hiawatha-protection-time-period'  		=>	300,
                    'hiawatha-protection-ban-duration' 		=>	300
                )
        );

        $tableSql = "
            CREATE TABLE {$tableName} (
            entry_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(255) NOT NULL,
            attempted_at DATETIME NOT NULL,
            PRIMARY KEY (entry_id)
            ) ENGINE = InnoDB;
        ";
        dbDelta($tableSql);
    }

    /**
     *
     */
    public static function deactivate() {
        global $wpdb;
        $tableName = $wpdb->prefix . "hiawatha_login_protection_entries";

        delete_option('hiawatha-protection-settings');

        $wpdb->query("DROP TABLE IF EXISTS $tableName");
    }

}