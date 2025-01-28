<?php

namespace MEMBERSHIPADDON\INCLUDES;

class InitPluginClass
{
    public function __construct()
    {
        add_action( 'init',[$this , 'init']); 
    }

    public function init()
    {
        remove_action( 'wp_ajax_ihc_register_forms_check_one_field', [ '\Indeed\Ihc\RegisterForm', 'ihc_register_forms_check_one_field' ] );
        remove_action( 'wp_ajax_nopriv_ihc_register_forms_check_one_field', [ '\Indeed\Ihc\RegisterForm', 'ihc_register_forms_check_one_field' ] );
    }
}