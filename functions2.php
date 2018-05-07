//Woocommerce update data after successful checkout
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');
function custom_checkout_field_update_order_meta( $order_id ) {
  $theorder = new WC_Order( $order_id );
  $user_id = $theorder->get_user_id();
}
