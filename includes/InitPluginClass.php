<?php

namespace MEMBERSHIPADDON\INCLUDES;

class InitPluginClass extends \Indeed\Ihc\RegisterForm
{
    public $map;

    public $member_id;

    public $fields;


    public function __construct()
    {
        $this->map = [];
        add_shortcode("testdata", [$this, "test"]);
    }


    public function test($attr = [])
    {

        $this->member_id = $_GET['mid'];
        $form =  $this->form(['level' => $attr['level'] , 'mid' => $this->member_id]);
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

    public function __get($name)
    {
        if($name === 'fields')
        {
            return $this->fields;
        }
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

    public function setFields()
    {
        $this->fields = ihc_get_user_reg_fields();// get fields from db
        // remove payment_select from fields
        $key = ihc_array_value_exists( $this->fields, 'payment_select', 'name' );
        if ( $key !== false ){
            unset( $this->fields[$key] );
        }
        // remove dynamic price from fields
        $key = ihc_array_value_exists( $this->fields, 'ihc_dynamic_price', 'name' );
        if ( $key !== false ){
            unset( $this->fields[$key] );
        }
        // remove ihc coupons from fields
        $key = ihc_array_value_exists( $this->fields, 'ihc_coupon', 'name' );
        if ( $key !== false ){
            unset( $this->fields[$key] );
        }

        // extra check for recaptcha
        $key = ihc_array_value_exists( $this->fields, 'recaptcha', 'name' );
        if ( $key !== false ){
            $recaptchaType = get_option( 'ihc_recaptcha_version' );
            if ( $recaptchaType !== false && $recaptchaType == 'v3' ){
                $recaptchaKey = get_option('ihc_recaptcha_public_v3');
            } else {
                $recaptchaKey = get_option('ihc_recaptcha_public');
            }
            if ( empty( $recaptchaKey ) ){
                // in case we dont have keys for recaptcha . unset it
                unset( $this->fields[$key] );
            }
        }
        // sort the fields
        ksort( $this->fields );

        // show only the fields that are selected on backend
        $keyToSearch = ( isset( $this->shortcodeAttr['is_modal'] ) && (int)$this->shortcodeAttr === 1 ) ? 'display_on_modal' : 'display_public_reg';
        if ( $this->isModal ){
            $keyToSearch = 'display_on_modal';
        }

        // loop through form fields, and decide what to show.
        foreach ( $this->fields as $fieldKey => $fieldArray ){
            if ( (int)$fieldArray[$keyToSearch] === 0 ){
                unset( $this->fields[$fieldKey] );
            } else {
                // Targeting Memberships
                if ( isset( $fieldArray['target_levels'] ) && $fieldArray['target_levels'] !== '' ){
                    $targetMemberships = explode( ',', $fieldArray['target_levels'] );
                    if ( count( $targetMemberships ) > 0 ){
                        $showField = false;
                        foreach ( $targetMemberships as $targetMembership ){
                            if ( $targetMembership === $this->member_id ){
                                $showField = true;
                            }
                        }
                        if ( !$showField ){
                            unset( $this->fields[$fieldKey] );
                            continue;
                        }
                    }
                }
                // end of Targeting Memberships

                // set the field parent id & class, required, inside label, multiple values
                $this->fields[$fieldKey]['parent_field_class']    = 'iump-form-' . $fieldArray['name'];
                $this->fields[$fieldKey]['parent_field_id']       = 'ihc_reg_' . $fieldArray['name'] . '_' . rand(1,10000);
                $this->fields[$fieldKey]['multiple_values']       = isset( $fieldArray['values'] ) && $fieldArray['values'] ? ihc_from_simple_array_to_k_v( $fieldArray['values'] ) : false;
                $this->fields[$fieldKey]['label_inside']          = isset( $fieldArray['native_wp'] ) && $fieldArray['native_wp'] ? esc_html__( $fieldArray['label'], 'ihc') : ihc_correct_text( $fieldArray['label'] );
                $this->fields[$fieldKey]['required_field']        = isset( $fieldArray['req'] ) && $fieldArray['req'] ? $fieldArray['req'] : false;
                $this->fields[$fieldKey]['disabled_field']        = false;
                if ( isset( $fieldArray['plain_text_value'] ) && $fieldArray['plain_text_value'] !== '' ){
                    $this->fields[$fieldKey]['value_to_print'] = $fieldArray['plain_text_value'];
                }

                // value from post or value from cookie if its case
                if ( isset( $_POST[ $fieldArray['name'] ] ) && $_POST[ $fieldArray['name'] ] !== '' ){
                    $this->fields[$fieldKey]['value_to_print'] = sanitize_text_field($_POST[ $fieldArray['name'] ]);
                } else if ( isset( self::$dataFromCookie[ $fieldArray['name'] ] ) && self::$dataFromCookie[ $fieldArray['name'] ] !== '' ){
                    $this->fields[$fieldKey]['value_to_print'] = self::$dataFromCookie[ $fieldArray['name'] ];
                }

                // is this field required, this array will go into js. we exclude the pass1 and pass2
                if ( $this->fields[$fieldKey]['required_field'] !== false ){
                    $this->requiredFields[] = $fieldArray['name'];
                }

                // add conditional_text && unique_value_text  into js
                switch ( $this->fields[$fieldKey]['type'] ){
                    case 'conditional_text':
                        $this->conditionalTextFields[] = $fieldArray['name'];
                        break;
                    case 'unique_value_text':
                        $this->uniqueFields[] = $fieldArray['name'];
                        break;
                }

                // special settings for special fields.
                switch ( $this->fields[$fieldKey]['name'] ){
                    case 'ihc_social_media':
                    case 'recaptcha':
                    case 'tos':
                        $this->fields[$fieldKey]['hide_outside_label'] = true;
                        $this->fields[$fieldKey]['label_inside'] = '';
                        break;
                    case 'ihc_memberlist_accept':
                        $this->fields[$fieldKey]['hide_outside_label'] = true;
                        $this->fields[$fieldKey]['value_to_print'] = isset( $this->fields[$fieldKey]['ihc_memberlist_accept_checked'] ) ? (int)$this->fields[$fieldKey]['ihc_memberlist_accept_checked'] : null;
                        break;
                    case 'ihc_optin_accept':
                        $this->fields[$fieldKey]['hide_outside_label'] = true;
                        $this->fields[$fieldKey]['value_to_print'] = isset( $this->fields[$fieldKey]['ihc_optin_accept_checked'] ) ? (int)$this->fields[$fieldKey]['ihc_optin_accept_checked'] : null;
                        break;
                }
            }
        }

        // switch the type of tos field from checkbox to 'tos'
        $key = ihc_array_value_exists( $this->fields, 'tos', 'name' );
        if ( $key !== false ){
            $this->fields[$key]['type'] = 'tos';
        }
        // switch the type of state field if its available
        $key = ihc_array_value_exists( $this->fields, 'ihc_state', 'name' );
        if ( $key !== false ){
            // switch the type of text field from checkbox to 'ihc_state'
            $this->fields[$key]['type'] = 'ihc_state';
        }

        // create an array with all fields name
        foreach ( $this->fields as $fieldKey => $fieldArray ){
            $this->fieldsNames[] = $fieldArray['name'];
        }

        // Use Reflection to override the private $fields property in the parent class
        $reflection = new \ReflectionClass($this);
        $property = $reflection->getParentClass()->getProperty('fields');
        $property->setAccessible(true); // Allow access to private property
        $property->setValue($this, $this->fields); // Replace private $fields with public one

        return $this;
    }


}