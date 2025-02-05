<?php

namespace MEMBERSHIPADDON\INCLUDES;

class InitPluginClass extends \Indeed\Ihc\RegisterForm
{
    public $map;

    public function __construct()
    {
        $this->map = [];
        add_shortcode("testdata", [$this, "test"]);
    }

    public function test($attr = [])
    {

        $form =  $this->form(['level' => $attr['level']]);
        echo $form;
        echo "
        <style>
            .hidden {
                display: none !important;
            }
            .nothidden {
                display: block !important;
            }

        </style>
        ";
        $this->create_logic();
        wp_enqueue_script('dynamic-select-script', MADN_URI . '/assets/main.js', array('jquery'), null, true);
        wp_localize_script('dynamic-select-script', 'phpData', $this->map);
    }

    public function create_logic()
    {

        $fields = ihc_get_user_reg_fields();
        $map = [];
        foreach($fields as $key => $field)
        {
            if(isset ($field['conditional_logic_corresp_field']) && $field['conditional_logic_corresp_field'] != '-1' && $field["display_public_reg"]=='1' && $field["type"] == "select")
            {
                if(!array_key_exists($field['conditional_logic_corresp_field'],$map))
                {
                    $map[$field['conditional_logic_corresp_field']];
                }
                $map[$field['conditional_logic_corresp_field']][] = [
                    'name' => $field["name"],
                    'value' => $field['conditional_logic_corresp_field_value'],
                ];
            }
        }
        $this->map = $map;
    }

    public function buildJS()
    {
        global $wp_version;

        if ( !isset( $GLOBALS['wp_scripts']->registered['ihc-public-dynamic'] ) ){
            wp_register_script( 'ihc-public-dynamic', IHC_URL . '/assets/js/public.js', ['jquery'], '12.8' );
        }
        if ( !isset( $GLOBALS['wp_scripts']->registered['ihc-public-register-form'] ) ){
            wp_register_script( 'ihc-public-register-form', IHC_URL . '/assets/js/IhcRegisterForm.js', ['jquery'], '12.8' );
        }
        wp_enqueue_script( 'ihc-public-register-form' );
    }


}