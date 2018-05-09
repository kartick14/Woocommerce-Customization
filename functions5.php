<?php
function process_product_meta_custom_tab( $post_id ) {    
    $location_array = explode('|', get_option( 'wcaddinfo_location' ));
    if(!empty($location_array)){        
        update_post_meta( $post_id, 'menu_location', $_POST['menu_location']); 
        foreach ($location_array as $key => $location) {
            $location_trim = strtolower(str_replace(' ', '_', $location));            
            update_post_meta( $post_id, 'available_start_time_'.$location_trim, $_POST['available_start_time_'.$location_trim]); 
            update_post_meta( $post_id, 'available_range_'.$location_trim, $_POST['available_range_'.$location_trim]); 
        }
    }   
}
add_action('woocommerce_process_product_meta', 'process_product_meta_custom_tab', 10, 2);


function custom_tab_options_tab_spec() {
?>
    <li class="custom_tab3"><a href="#custom_tab_data3"><?php _e(' Additional Info', 'woothemes'); ?></a></li>
<?php
}
add_action('woocommerce_product_write_panel_tabs', 'custom_tab_options_tab_spec');


/**
 * Custom Tab Options
 *
 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
 */
function custom_tab_options_spec() {
    global $post;
    $location_array = explode('|', get_option( 'wcaddinfo_location' ));
    if(!empty($location_array)){
        $custom_tab_options_spec = array(
        'location' => get_post_meta($post->ID, 'menu_location', true)
        );

        foreach ($location_array as $key => $location) {
            $location_trim = strtolower(str_replace(' ', '_', $location));
            $custom_tab_options_spec['available_start_time_'.$location_trim] = get_post_meta($post->ID, 'available_start_time_'.$location_trim, true);
            $custom_tab_options_spec['available_range_'.$location_trim] = get_post_meta($post->ID, 'available_range_'.$location_trim, true);
        }
    }
?>
    <div id="custom_tab_data3" class="panel woocommerce_options_panel">               
        <div class="options_group custom_tab_options">           
            <?php
            if($custom_tab_options_spec['location']){
                $sel_loc = @$custom_tab_options_spec['location']; 
            }
            $location_array = explode('|', get_option( 'wcaddinfo_location' ));
            if(!empty($location_array)){
                foreach ($location_array as $key => $location) {  ?>
                <p class="form-field">
                    <label><?php _e('Select location:', 'woothemes'); ?></label>
                    <input type="checkbox" name="menu_location[]" value="<?php echo $location; ?>" <?php if(in_array($location,$sel_loc)){ ?> checked="" <?php } ?>> <?php echo $location; ?>
                </p>
                <?php $location_trim = strtolower(str_replace(' ', '_', $location)); ?>
                <p class="form-field">
                    <label><?php _e('Select available days:', 'woothemes'); ?></label>
                    <?php
                    $sel = array();
                    if($custom_tab_options_spec['available_range_'.$location_trim]){
                        $sel = @$custom_tab_options_spec['available_range_'.$location_trim]; 
                    }
                    $flagday = get_option( 'wcaddinfo_day_start' );
                    if(empty($flagday)){
                      $flagday = 'sunday';
                    }
                    $sunday = strtotime("last ".$flagday);
                    $sunday = date('w', $sunday)==date('w') ? $sunday+7*86400 : $sunday;       
                    $current_day = date('l');
                    $current_date = date('d');
                
                    //for ($i=0; $i < 7; $i++) {                 

                    $day_range = get_option( 'wcaddinfo_day_range' );
                    if(empty($day_range)){
                        $day_range = 7;
                    }
                    for ($i=0; $i < $day_range; $i++) { 
                      $day = date("D",strtotime(date("Y-m-d",$sunday)." +".$i." days"));
                      $date = date("d",strtotime(date("Y-m-d",$sunday)." +".$i." days"));
                      $full_date = date("Y-m-d",strtotime(date("Y-m-d",$sunday)." +".$i." days"));
                      ?>
                      <input type="checkbox" name="available_range_<?php echo $location_trim; ?>[]" value="<?php echo $full_date; ?>" <?php if(in_array($full_date,$sel)){ ?> checked="" <?php } ?>> <?php echo $day.'('.$date.')'; ?>
                      <?php
                    } ?>
                </p>
                <p class="form-field">
                <label><?php _e('Select available time range:', 'woothemes'); ?></label>
                <?php $sel = @$custom_tab_options_spec['available_start_time_'.$location_trim]; ?>                
                 <?php 
                    $time_array = explode('|', get_option( 'wcaddinfo_timing' ));
                    if(!empty($time_array)){
                    foreach ($time_array as $key => $time) {
                    ?>  
                        &nbsp;<input type="checkbox" name="available_start_time_<?php echo $location_trim; ?>[]" value="<?php echo $time; ?>" <?php if(in_array($time,$sel)){ ?> checked="" <?php } ?>> <?php echo $time; ?>
                    <?php } } ?>
                </p>
                <hr>
            <?php    
                }
            }
            ?>                            
		</div> 
    </div>
<?php
}
add_action('woocommerce_product_write_panels', 'custom_tab_options_spec');
