//add additional columns to the users.php admin page
add_filter('manage_users_columns', 'project_add_user_id_column');
function project_add_user_id_column($columns) {
    $columns['user_id'] = 'ID';
   // unset($columns['pmpro_membership_level']);
    return $columns;
}

//add content to your new custom column
add_action('manage_users_custom_column',  'project_show_user_id_column_content', 10, 3);
function project_show_user_id_column_content($value, $column_name, $user_id) {
    $user = get_userdata( $user_id );
	if ( 'user_id' == $column_name )
		return $user_id;
    return $value;
}

//make the new column sortable
function user_sortable_columns( $columns ) {
    $columns['user_id'] = 'ID';
    return $columns;
}
add_filter( 'manage_users_sortable_columns', 'user_sortable_columns' );

//set instructions on how to sort the new column
if(is_admin()) {//prolly not necessary, but I do want to be sure this only runs within the admin
    add_action('pre_user_query', 'my_user_query');
}
function my_user_query($userquery){ 
    if('user_id'==$userquery->query_vars['orderby']) {//check if church is the column being sorted
        global $wpdb;
        $userquery->query_orderby = " ORDER BY ID ".($userquery->query_vars["order"] == "ASC" ? "asc " : "desc ");//set sort order
    }
}
