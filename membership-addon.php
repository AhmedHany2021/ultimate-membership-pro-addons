<?php
/*
Plugin Name: Ultimate Membership Pro Addon
Plugin URI: https://github.com/AhmedHany2021
Description: this plugin add some features to integrate ultimate membership pro with woocommerce
Author: Ahmed Hany
Version: 1.1.0
Author URI: https://github.com/AhmedHany2021
GitHub Plugin URI: https://github.com/AhmedHany2021
*/

namespace MEMBERSHIPADDON;

if (!defined('ABSPATH'))
{
    die();
}

if ( !in_array( 'indeed-membership-pro/indeed-membership-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    die("NO ACCESS From Here Fine");
}

/* Add the main global variables */

if(!defined("MADN_BASEDIR")) { define("MADN_BASEDIR",__DIR__ . '/'); }
if(!defined("MADN_INC")) { define("MADN_INC",MADN_BASEDIR.'includes' . '/'); }
if(!defined("MADN_TEMPLATES")) { define("MADN_TEMPLATES",MADN_BASEDIR.'templates' . '/'); }
if(!defined("MADN_URI")) { define("MADN_URI",plugin_dir_url(__FILE__) ); }
if(!defined("MADN_ASSETS")) { define("MADN_ASSETS", MADN_URI.'assets' . '/'); }
if(!defined("MADN_ORIGINAL_DIR")) { define("MADN_ORIGINAL_DIR", WP_PLUGIN_DIR . '/indeed-membership-pro' . '/'); }
if(!defined("IHC_URL")) { define("IHC_URL", plugins_url(WP_PLUGIN_DIR . '/indeed-membership-pro')); }


require_once MADN_INC . 'autoload.php';
use MEMBERSHIPADDON\INCLUDES\autoload;
use MEMBERSHIPADDON\INCLUDES\InitPluginClass;
use MEMBERSHIPADDON\INCLUDES\PayMembershipClass;
use MEMBERSHIPADDON\INCLUDES\MediaIDToLinkShortcode;

autoload::fire();

add_action('plugins_loaded', function() {
    if (is_plugin_active('indeed-membership-pro/indeed-membership-pro.php')) {
        require_once MADN_ORIGINAL_DIR.'classes/Ihc_Db.class.php';
        require_once MADN_ORIGINAL_DIR.'classes/RegisterForm.php';
        require_once MADN_ORIGINAL_DIR.'classes/ValidateForm.php';

        $PayMembership = new PayMembershipClass();
        $init = new InitPluginClass();
    }
});

new MediaIDToLinkShortcode();

