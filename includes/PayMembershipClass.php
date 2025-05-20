<?php

namespace MEMBERSHIPADDON\INCLUDES;

class PayMembershipClass
{
    public function __construct()
    {
        add_shortcode("pay_membership", [$this, "pay_membership"]);
        add_action('redirect_to_pass' , [$this,"pay_membership_action"]);
    }

    public function pay_membership_action($lid = null)
    {
        if (is_admin()) {
            return '';
        }

        if ( $lid )
        {
            $lid = sanitize_text_field( $lid );
        }

        if ($lid)
        {
            $productId = \Ihc_Db::get_woo_product_id_for_lid($lid);
            if ($productId) {
                if (!wc_get_product($productId)) {
                    echo 'Product not found.';
                }
                WC()->cart->empty_cart();
                WC()->cart->add_to_cart((int)$productId);
                $redirect_url = wc_get_checkout_url();
                $script = "
                    <script type='text/javascript'>
                        window.onload = function() {
                            window.location.href = '$redirect_url';
                        };
                    </script>
                    ";
                echo $script;
                wp_redirect($redirect_url);
                die();
            }
        }
    }
    public function pay_membership($atts)
    {
        if (is_admin()) {
            return '';
        }
        $lid = null;
        if (isset($atts["lid"])) {
            $lid = $atts["lid"];
        }
        elseif ( isset( $_GET['lid'] ) && $_GET['lid'] !== '' ){
            $lid = sanitize_text_field( $_GET['lid'] );
        }

        if ($lid)
        {
            $productId = \Ihc_Db::get_woo_product_id_for_lid($lid);
            if ($productId)
            {
                if (!wc_get_product($productId)) {
                    echo 'Product not found.';
                }
                WC()->cart->empty_cart();
                WC()->cart->add_to_cart($productId);
                $redirect_url = wc_get_checkout_url();
                $script = "
                    <script type='text/javascript'>
                        window.onload = function() {
                            window.location.href = '$redirect_url';
                        };
                    </script>
                    ";
                return $script;

            }
            else
            {
                echo "No product found for lid : $lid";
            }
        }
        else
        {
            echo "No product id specified";
        }
    }

}