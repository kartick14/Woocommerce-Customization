<?php
//Step 1: Add Custom Data in WooCommerce Session
function wdm_add_user_custom_data_options_callback()
{
    if(isset($_GET['order_location']) && isset($_GET['order_date_picker'])){        
        session_start();
        $strtotime_format = strtotime($_GET['order_date_picker']);
        $timeformat = date("l jS F Y", $strtotime_format);
        $order_del_dt = date("Y-m-d", $strtotime_format);
        $user_custom_data_values = 'Location: '.$_GET['order_location'].' and Delivery date: '.$timeformat.' and Drop off time: '.$_GET['order_drop_off_time'];
        $_SESSION['wdm_user_custom_data'] = $user_custom_data_values;        
        $_SESSION['user_order_location'] = $_GET['order_location'];
        $_SESSION['user_order_pickup_datetime'] = $timeformat;
        $_SESSION['user_order_pickup_date'] = $order_del_dt;
        $_SESSION['user_order_drop_off_time'] = $_GET['order_drop_off_time'];  
    }
}
add_filter('init','wdm_add_user_custom_data_options_callback',1,2);

//Step 2: Add Custom Data in WooCommerce Session
add_filter('woocommerce_add_cart_item_data','wdm_add_item_data',1,2);
if(!function_exists('wdm_add_item_data'))
{
    function wdm_add_item_data($cart_item_data,$product_id)
    {
        /*Here, We are adding item in WooCommerce session with, wdm_user_custom_data_value name*/
        global $woocommerce;
        session_start();    
        if (isset($_SESSION['wdm_user_custom_data'])) {
            $option = $_SESSION['wdm_user_custom_data'];       
            $pickup_date = $_SESSION['user_order_pickup_date']; 
            $pickup_location = $_SESSION['user_order_location'];
            $drop_off_time = $_SESSION['user_order_drop_off_time'];
            $new_value = array('wdm_user_custom_data_value' => $option, 'user_order_pickup_date_value' => $pickup_date, 'user_order_location_value' => $pickup_location, 'user_order_drop_off_time_value' => $drop_off_time);
        }
        if(empty($option))
            return $cart_item_data;
        else
        {    
            if(empty($cart_item_data))
                return $new_value;
            else
                return array_merge($cart_item_data,$new_value);
        }
        unset($_SESSION['wdm_user_custom_data']); 
        //Unset our custom session variable, as it is no longer needed.
    }
}

//Step 3: Extract Custom Data from WooCommerce Session and Insert it into Cart Object

add_filter('woocommerce_get_cart_item_from_session', 'wdm_get_cart_items_from_session', 1, 3 );
if(!function_exists('wdm_get_cart_items_from_session'))
{
    function wdm_get_cart_items_from_session($item,$values,$key)
    {
        if (array_key_exists( 'wdm_user_custom_data_value', $values ) )
        {
            $item['wdm_user_custom_data_value'] = $values['wdm_user_custom_data_value'];
        }  

        if (array_key_exists( 'user_order_pickup_date_value', $values ) )
        {
            $item['user_order_pickup_date_value'] = $values['user_order_pickup_date_value'];
        } 

        if (array_key_exists( 'user_order_location_value', $values ) ){
            $item['user_order_location_value'] = $values['user_order_location_value'];
        } 

        if (array_key_exists( 'user_order_drop_off_time_value', $values ) ){
            $item['user_order_drop_off_time_value'] = $values['user_order_drop_off_time_value'];
        }     
        return $item;
    }
}



//Step 4: Display User Custom Data on Cart and Checkout page

add_filter('woocommerce_checkout_cart_item_quantity','wdm_add_user_custom_option_from_session_into_cart',1,3);  
add_filter('woocommerce_cart_item_price','wdm_add_user_custom_option_from_session_into_cart',1,3);

if(!function_exists('wdm_add_user_custom_option_from_session_into_cart'))
{
 function wdm_add_user_custom_option_from_session_into_cart($product_name, $values, $cart_item_key )
    {
        /*code to add custom data on Cart & checkout Page*/    
        if(count($values['wdm_user_custom_data_value']) > 0)
        {
            $return_string = $product_name . "</a><dl class='variation'>";
            $return_string .= "<table class='wdm_options_table' id='" . $values['product_id'] . "'>";
            $return_string .= "<tr><td>" . $values['wdm_user_custom_data_value'] ."</td></tr>";
            $return_string .= "</table></dl>"; 
            return $return_string;
        }
        else
        {
            return $product_name;
        }
    }
}

//Step 5: Add Custom Data as Metadata to the Order Items
add_action('woocommerce_add_order_item_meta','wdm_add_values_to_order_item_meta',1,2);
if(!function_exists('wdm_add_values_to_order_item_meta'))
{
    function wdm_add_values_to_order_item_meta($item_id, $values)
    {
        global $woocommerce,$wpdb;

        $user_custom_values = $values['wdm_user_custom_data_value'];
        $user_order_pickup_date_values = $values['user_order_pickup_date_value'];
        $user_order_location_values = $values['user_order_location_value'];
        $user_order_drop_off_time_values = $values['user_order_drop_off_time_value'];

        if(!empty($user_custom_values))
        {
            wc_add_order_item_meta($item_id,'User Order Location, Delivery Date and Drop Off Time ',$user_custom_values);  
        }

        if(!empty($user_order_pickup_date_values))
        {
            wc_add_order_item_meta($item_id,'user_order_pickup_date_value',$user_order_pickup_date_values);  
        }
        if(!empty($user_order_location_values))
        {
            wc_add_order_item_meta($item_id,'user_order_location_value',$user_order_location_values);  
        }
        if(!empty($user_order_drop_off_time_values))
        {
            wc_add_order_item_meta($item_id,'user_order_drop_off_time_value',$user_order_drop_off_time_values);  
        }
    }
}

//Step 6: Remove User Custom Data, if Product is Removed from Cart

add_action('woocommerce_before_cart_item_quantity_zero','wdm_remove_user_custom_data_options_from_cart',1,1);
if(!function_exists('wdm_remove_user_custom_data_options_from_cart'))
{
    function wdm_remove_user_custom_data_options_from_cart($cart_item_key)
    {
        global $woocommerce;
        // Get cart
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach( $cart as $key => $values)
        {
            if ( $values['wdm_user_custom_data_value'] == $cart_item_key )
                unset( $woocommerce->cart->cart_contents[ $key ] );

            if ( $values['user_order_pickup_date_value'] == $cart_item_key )
                unset( $woocommerce->cart->cart_contents[ $key ] );

            if ( $values['user_order_location_value'] == $cart_item_key )
                unset( $woocommerce->cart->cart_contents[ $key ] );

            if ( $values['user_order_drop_off_time_value'] == $cart_item_key )
                unset( $woocommerce->cart->cart_contents[ $key ] );
        }
    }
}
