<?php
/*
Plugin Name: WordPress Theme Utilities
Plugin URI: http://www.simondouglas.com
Description: Adds Common functionality to WordPress sites. Takes care of security, pruning cruft and adding useful helper functions.
Version: 1.0
Author: Simon Douglas
Author URI: https://github.com/sidouglas/wp-theme-utilities
*/

include('shortcodes.php');

/* CREATE SIGNALS ADMIN PANEL
----------------------------------------------------------------------------------*/
require_once 'settings.php';

include('functions.php');

/* ADD ID TO PAGE TREE VIEW 
----------------------------------------------------------------------------------*/

add_filter( 'admin_init','_wptu_add_ids_to_page_tree_view' );
function _wptu_add_ids_to_page_tree_view( $where ){
   global $user;
        
           if( current_user_can('administrator') &&  
               is_plugin_active( 'cms-tree-page-view/index.php' )  ) {
              
              if( strstr( $_GET['page'] , 'cms-tpv-page' ) ){
                  wp_enqueue_script('add_id_to_gallery', WPTU_CORE_PATH.'js/add_id_to_cms-tree.js', array('jquery' ), 1.0, true);
              }
        }
}

/* ADDS ID TO THE WORDPRESS ADMIN TABLES
 * ----------------------------------------------------------------------------------*/

// Prepend the new column to the columns array
function _wptu_column($cols) {
	$cols['ssid'] = 'ID';
	return $cols;
}

// Echo the ID for the new column
function _wptu_value($column_name, $id) {
	if ($column_name == 'ssid')
		echo $id;
}

function _wptu_return_value($value, $column_name, $id) {
	if ($column_name == 'ssid')
		$value = $id;
	return $value;
}

// Output CSS for width of new column
function _wptu_css() {
?>
<style type="text/css">
	#ssid { width: 50px; } /* Simply Show IDs */
</style>
<?php
}

// Actions/Filters for various tables and the css output
function _wptu_add() {
	add_action('admin_head', '_wptu_css');

	add_filter('manage_posts_columns', '_wptu_column');
	add_action('manage_posts_custom_column', '_wptu_value', 10, 2);

	add_filter('manage_pages_columns', '_wptu_column');
	add_action('manage_pages_custom_column', '_wptu_value', 10, 2);

	add_filter('manage_media_columns', '_wptu_column');
	add_action('manage_media_custom_column', '_wptu_value', 10, 2);

	add_filter('manage_link-manager_columns', '_wptu_column');
	add_action('manage_link_custom_column', '_wptu_value', 10, 2);

	add_action('manage_edit-link-categories_columns', '_wptu_column');
	add_filter('manage_link_categories_custom_column', '_wptu_return_value', 10, 3);

	foreach ( get_taxonomies() as $taxonomy ) {
		add_action("manage_edit-${taxonomy}_columns", '_wptu_column');
		add_filter("manage_${taxonomy}_custom_column", '_wptu_return_value', 10, 3);
	}

	add_action('manage_users_columns', '_wptu_column');
	add_filter('manage_users_custom_column', '_wptu_return_value', 10, 3);

	add_action('manage_edit-comments_columns', '_wptu_column');
	add_action('manage_comments_custom_column', '_wptu_value', 10, 2);
}
add_action('admin_init', '_wptu_add');


/* DEVELOPER NOTES
----------------------------------------------------------------------------------*/
/**
 * use hook, to integrate new widget
 */
add_action('wp_dashboard_setup', 'wptu_developer_notes');

/**
 * add Dashboard Widget via function wp_add_dashboard_widget()
 */
function wptu_developer_notes() {
	get_currentuserinfo();
	if ( current_user_can( 'manage_options' ) ) {
		wp_add_dashboard_widget( '', __( 'Developer Notes' ), 'wptu_notes_dashboard' );
	} 
}

/**
 * Content of Dashboard-Widget
 */
function wptu_notes_dashboard() {

	if( $_REQUEST['saved'] == 'Update Notes' ) {
			update_option('wptu_dev_notes', $_REQUEST['wptu_notes'] );
	}

	if( get_option('wptu_dev_notes') == false ) {
		add_option('wptu_dev_notes', 'content to go here!');
	}

	echo '<form method="post" name="wptu_notes_form">';
	echo '<textarea name="wptu_notes" rows="10"  wrap="wrap" style="width:100%;">'.stripslashes(get_option('wptu_dev_notes') ).'</textarea>';
	echo '<input type="submit" class="button" value="Update Notes" name="saved">';
	echo '</form>';

}