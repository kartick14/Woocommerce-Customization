// WooCommerce Only one product in cart  
 
add_filter( 'woocommerce_add_to_cart_validation', 'project_only_one_in_cart', 99, 2 );
  
function project_only_one_in_cart( $passed, $added_product_id ) {
 
global $woocommerce;
 
// empty cart: new item will replace previous
$woocommerce->cart->empty_cart();
 
// display a message if you like
//wc_add_notice( 'Product added to cart!', 'notice' );
 
return $passed;
}
