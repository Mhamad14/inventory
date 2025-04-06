<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */


Events::on('pre_system', static function (): void {

    if (! function_exists('Config\\check_for_installer'))  // checking the function in whole namespace of Config
    {
        function check_for_installer()
        {
            if (file_exists('install/index.php')) {
                header("location:install/");
                die();
            }
        }
    }
  
    
    check_for_installer();

    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        Services::toolbar()->respond();
        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            Services::routes()->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }

    
});

Events::on('post_controller_constructor', function () {

    if (! function_exists('Config\\set_system_timezone')) // checking the function in whole namespace of Config
    {
        function set_system_timezone()
        {
            $db  = \Config\Database::connect();
            $settings = get_settings('general_settings', true);
            // $settings = fetch_details('settings');
            // ($settings['select_time_zone']);
            // $settings = json_decode($settings[0]['value'], true);
            // 
            /* Set database timezone */
            if (isset($settings['mysql_timezone'])) {
                $db->query("SET time_zone='" . $settings['mysql_timezone'] . "'");
            } else {
                $db->query("SET time_zone='+05:30'");
            }
            /* Set PHP server timezone */
            // if (is_numeric($settings['select_time_zone'])) {
    
            //         date_default_timezone_set('Asia/Kolkata');
    
            // }
            // else{
            if (isset($settings['select_time_zone']) || !empty($settings['select_time_zone'])) {
                date_default_timezone_set($settings['select_time_zone']);
            } else {
                date_default_timezone_set('Asia/Kolkata');
            }
            // }
        }
    }
    
    set_system_timezone();
});

