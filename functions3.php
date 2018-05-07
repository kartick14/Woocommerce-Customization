// Woocommerce disable/enable payment gateway depend on user role
function bbloomer_paypal_disable_manager( $available_gateways ) {
    global $woocommerce;
    if ( isset( $available_gateways['stripe'] ) && current_user_can('administrator') ) {
        unset( $available_gateways['stripe'] );
    }else{
        unset( $available_gateways['cod'] );
    } 
    return $available_gateways;
}
 
add_filter( 'woocommerce_available_payment_gateways', 'bbloomer_paypal_disable_manager' );
