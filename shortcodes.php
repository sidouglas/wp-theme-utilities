<?php 
/* SHORTCODES
----------------------------------------------------------------------------------*/

// returns the shortcodes for bloginfo e.g. [bloginfo key="name"] returns the sitename
add_shortcode('bloginfo', '_wptu_bloginfo_shortcode');

function _wptu_bloginfo_shortcode( $atts ) {
    extract(shortcode_atts(array(
        'key' => '',
    ), $atts));
    return get_bloginfo($key);
}



// returns the admin email as the shortcode [admin_email]
// in the format <a href="admin_email">bloginfo('name')</a>
add_shortcode('admin_email','_wptu_admin_wptu_email_shortcode');

function _wptu_admin_wptu_email_shortcode(){
	$link = 'mailto:' . get_bloginfo("admin_email");
     $obfuscatedLink = "";
     for ($i=0; $i<strlen($link); $i++){
         $obfuscatedLink .= "&#" . ord($link[$i]) . ";";
     }
	 return '<a href="'.$obfuscatedLink.'">' . get_bloginfo('name').'</a>';
}



// Usage:  [email mailbox="enquiries" url="siteurl" subject="Enquiry From Site" ]
add_shortcode('email','_wptu_email_shortcode');
function _wptu_email_shortcode($attr,$content) {

	extract(shortcode_atts(array(	'mailbox'=>'',
								 	'url'=>'',
									'subject'=>'',
                  'display_text'=>''
                  ),
                   $attr
								 )
						   );

	if( $subject ) {
     $subject = str_replace( ' ', '%20', $subject );
     $subject = "?subject={$subject}";
  }

	$protected_email  = '<script type="text/javascript">';
	$protected_email .= "eE=('{$mailbox}' + '%40' + '{$url}{$subject}');";

  if( $display_text != '' ):
     $protected_email .= "eD=('<span class=\"display-text\">{$display_text}</span>');";
  else:
     $protected_email .= "eD=('<span class=\"mailbox\">{$mailbox}</span>' + '<span class=\"at-operator\">(at)</span>' + '<span class=\"url\">{$url}</span>');";
  endif;

	$protected_email .= "document.write('<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;' + eE + '\">' + eD + '</a>');</script>";
	return  $protected_email;

}

add_shortcode('include',_wptu_include_file);

function _wptu_include_file($attr) {

	extract(shortcode_atts( array( 'file'=>'' ), $attr ) );
  
  if ( !empty($file) ) {
    $template = trailingslashit(  get_stylesheet_directory()  );
    if( file_exists( $template .$file  ) ){
    	ob_start();
      include( $template .$file );
			$content = ob_get_clean();
			return $content;
		} 
    else {
			return 'File not found. I tried looking for: ' . $template . $file .' but I canne find it captain.';
		}

	} else {
	return 'Pass a $file parameter for the file. Includes must be in the theme directory. Current Theme: ' . $template;
	}
}

add_shortcode( "loop", "wptu_content_loop" );

/**
 *  This uses a shortcode directly in the page where the archive needs to be.
 *  It is useful because content be added before and after the loop without
 *  having to affect template files.
 *
 * @param bool $pagination whether to use standard WordPress pagination
 * @param string $query query for the function to run. Refer to get_posts query
 * @param category $string Use slug or category name
 * @param tax $string for the taxonomy term
 * @param terms for one term name
 *
 * @todo add support for wp_pagenavi so we can use it instead of crappy wordpress
 * pagination.
 */
function wptu_content_loop( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
	"pagination" => 'true',
  "query" => '',
  "loop"=>'',
  'tax' =>'',
  'terms' => '',
  'wrap' => 'inline-loop'
	), $atts));

	global $wp_query,$post,$wp;

	$temp = $wp_query;
  
  $query = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $query);
  $query = preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $query);
  $query = str_replace('&amp;', '&', $query );

	if( $pagination == 'true' ){
    $args = wp_parse_args($wp->matched_query);
  
    if ( !empty ( $args['paged'] ) && 0 == $paged ) {
    $wp_query->set('paged', $args['paged']);
    $paged = $args['paged'];
    $query .= '&paged='.$paged;
    }
    
  }
  
  $query = wp_parse_args( $query );
  
  if( !empty( $tax ) && !empty( $terms ) ){
   
   $terms = explode( ',' , $terms  );
   
     
   $query['tax_query'] = array( 
                              array(
                                 'taxonomy' => $tax,
                                 'field' => 'slug',
                                 'terms' => $terms
                              ) 
                           );
                  
  }
  
  // set some realistic defaults.
  if( empty($query['orderby'] ) ){
     $query['orderby'] = 'menu_order';
  }
  
  if( empty($query['order'] ) ){
     $query['order'] = 'ASC';
  }
  
  $wp_query = new WP_Query( $query );
  
  setup_postdata( $wp_query );
  
  ob_start();
	
  ?>

  <div class="<?php echo $wrap; ?>">
   <?php get_template_part( 'loop', $loop ); ?>
  </div>


<?php 
      // the theme will have to handle the pagination.
      if( $pagination == 'true' ):

          if( function_exists( 'wp_pagenavi' ) ):
                if ( $wp_query->max_num_pages > 1):
                  wp_pagenavi( array( 'query'=>$wp_query) );
                endif;
          else:?>
                 <div class="navigation">
                    <div class="alignleft"><?php previous_posts_link('Previous') ?></div>
                    <div class="alignright"><?php next_posts_link('More') ?></div>
                 </div>
    <?php endif; ?>
<?php endif; ?>


	<?php $wp_query = null; $wp_query = $temp;
  $content = ob_get_contents();
  ob_end_clean();
  wp_reset_query();
  return $content;
}


/*
 * Adds shortcode support for text widgets. Therefore we can run our shortcodes
 * there.
 *
 */
add_filter('widget_text', 'do_shortcode', 11);


/*
 
 *  http://jqueryui.com/demos/accordion/
 */

add_shortcode('acc', 'wptu_accordion');
/**
 *  WordPress Theme Utilities Accordion
 *  You will have to load the javascript file that is included manually.
 *  You can either include it globally or on a per page basis.
 *  
 *  $wptu_ui_path = WP_PLUGIN_URL . '/wp-theme-utilities/';
 *  wp_enqueue_script( 'wptu-accordion', $wptu_ui_path.'js/wptu-ui.js', array('jquery'), 1, true );
 *  
 * @param  [array] $atts    params - null
 * @param  [string] $content 
 * @return [string]
 */
function wptu_accordion( $atts, $content = null ){

   global $post;

   // count the number of shortcodes on the page

   static $count = 1;
   static $the_number_of_acc = 3;

   $temp_content  = explode('[/acc]', $post->post_content ) ;
   $the_number_of_acc = ( count( $temp_content ) - 1 )  ;

    extract( shortcode_atts( array(
      'title' => 'you must supply a title attribute ;)'
      ), $atts ) );

  
  if( $count == 1 ) {
       $output .='<script>';
       $output .= '$(function(){ 	$(".jquery-ui-accordion").accordion({ header: "h3",	animated: "slide", event: "click", collapsible : true, autoHeight: false , active: false });';
       $output .='});</script>' . "\n";
       $output .= '<div class="jquery-ui-accordion">';
  }
  
   $output .= '<div class="accordion-inner">';
      $output .= '<h3 class="accordion-title"><a href="#">' . $title . '</a></h3>';
      $output .= '<div class="accordion-content">' .  $content . '</div>';
   $output .='</div>';
   
   if( $the_number_of_acc == $count )
      $output .='</div>';

   $count++;
   return $output;
}
