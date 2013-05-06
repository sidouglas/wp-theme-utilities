<?php 
// path for this plugin
$path =  trailingslashit( plugins_url()  . '/' . basename ( dirname(__FILE__) ) );

define('WPTU_CORE_PATH',$path);

/* SECURITY (Recommended by Smashing Magazine 2010)
----------------------------------------------------------------------------------*/
function _wptu_comment_post( $incoming_comment ) {
  $incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);
  $incoming_comment['commenvt_content'] = str_replace( "'", '&apos;', $incoming_comment['comment_content'] );
  return( $incoming_comment );
}

function _wptu_comment_display( $comment_to_display ) {
  $comment_to_display = str_replace( '&apos;', "'", $comment_to_display );
  return $comment_to_display;
}

function _wptu_clean_header(){
remove_action('wp_head', 'rsd_link'); // kill the RSD link
remove_action('wp_head', 'wlwmanifest_link'); // kill the WLW link
remove_action('wp_head', 'index_rel_link'); // kill the index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // kill the prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // kill the start link
remove_action('wp_head', 'feed_links', 2); // kill post and comment feeds
remove_action('wp_head', 'feed_links_extra', 3); // kill category, author, and other extra feeds
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // kill adjacent post links
remove_action('wp_head', 'wp_generator'); // kill the wordpress version number
remove_action('wp_head', 'rel_canonical');

add_filter('preprocess_comment', '_wptu_comment_post', '', 1);
add_filter('comment_text', '_wptu_comment_display', '', 1);
add_filter('comment_text_rss', '_wptu_comment_display', '', 1);
add_filter('comment_excerpt', '_wptu_comment_display', '', 1);

remove_action('wp_head','woo_version'); //remove woo generator information if it exists

//added in 0.9.3 we don't need these loaded from Hybrid Core:
remove_action( 'wp_head', 'hybrid_meta_robots', 1 );
remove_action( 'wp_head', 'hybrid_meta_author', 1 );
remove_action( 'wp_head', 'hybrid_meta_copyright', 1 );
remove_action( 'wp_head', 'hybrid_meta_revised', 1 );
remove_action( 'wp_head', 'hybrid_meta_description', 1 );
remove_action( 'wp_head', 'hybrid_meta_keywords', 1 );

}

function _wptu_check_referrer() {
    if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '')
        wp_die( __('Please enable referrers in your browser, or, if you\'re a spammer, go away!') );

}


add_action('wp_head', '_wptu_clean_header', 1 );
add_action('check_comment_flood', '_wptu_check_referrer');

/* FILTERS
----------------------------------------------------------------------------------*/
if( wptu_plugin_option('sniff_browser') == 1 ) {
  add_filter( 'body_class', '_wptu_browser_class_names' );
}

// Note that this global can be used anywhere.
global $wptu_browser_classes;
$wptu_browser_classes = array();

function _wptu_browser_class_names ( $classes ) {
   global $wptu_browser_classes;
   // add 'class-name' to the $classes array
   // $classes[] = 'class-name';
   $browser = $_SERVER[ 'HTTP_USER_AGENT' ];

   // Mac, PC ...or Linux
   if ( preg_match( "/Mac/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'mac';

   } elseif ( preg_match( "/Windows/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'windows';

   } elseif ( preg_match( "/Linux/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'linux';

   } else {
      $wptu_browser_classes[] = $classes[] = 'unknown-os';
   }

   // Checks browsers in this order: Chrome, Safari, Opera, MSIE, FF
   if ( preg_match( "/Chrome/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'chrome';

      preg_match( "/Chrome\/(\d{1,})/si", $browser, $matches );
      if ( count( $matches ) > 0 ) {
         $ch_version = 'ch' . str_replace( '.', '-', $matches[1] );
         $wptu_browser_classes[] = $classes[] = $ch_version;
      }

   } elseif ( preg_match( "/Safari/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'safari';

      preg_match( "/Version\/(\d{1,})/si", $browser, $matches );
      if ( count( $matches ) > 0 ) {
         $sf_version = 'sf' . str_replace( '.', '-', $matches[1] );
         $wptu_browser_classes[] = $classes[] = $sf_version;
      }
   } elseif ( preg_match( "/Opera/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'opera';

      preg_match( "/Opera\/(\d{1,})/si", $browser, $matches );
      if ( count( $matches ) > 0 ) {
         $op_version = 'op' . str_replace( '.', '-', $matches[1] );
         $wptu_browser_classes[] = $classes[] = $op_version;
      }

   } elseif ( preg_match( "/MSIE/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'msie';

      if ( preg_match( "/MSIE 6.0/", $browser ) ) {
         $wptu_browser_classes[] = $classes[] = 'ie6';
      } elseif ( preg_match( "/MSIE 7.0/", $browser ) ) {
         $wptu_browser_classes[] = $classes[] = 'ie7';
      } elseif ( preg_match( "/MSIE 8.0/", $browser ) ) {
         $wptu_browser_classes[] = $classes[] = 'ie8';
      } elseif ( preg_match( "/MSIE 9.0/", $browser ) ) {
         $wptu_browser_classes[] = $classes[] = 'ie9';
      } elseif ( preg_match( "/MSIE 10.0/", $browser ) ) {
         $wptu_browser_classes[] = $classes[] = 'ie10';
      }

   } elseif ( preg_match( "/Firefox/", $browser ) && preg_match( "/Gecko/", $browser ) ) {
      $wptu_browser_classes[] = $classes[] = 'firefox';

      preg_match( "/Firefox\/(\d{1,})/si", $browser, $matches );
      if ( count( $matches ) > 0 ) {
         $ff_version = 'ff' . str_replace( '.', '-', $matches[1] );
         $wptu_browser_classes[] = $classes[] = $ff_version;
      }
   }

   else {
      $wptu_browser_classes[] = $classes[] = 'unknown-browser';
   }

   if ( strstr( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) ) {
      $wptu_browser_classes[] = $classes[] = 'iphone';
   }
   if ( strstr( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) {
      $wptu_browser_classes[] = $classes[] = 'ipad';
   }
   if ( strstr( $_SERVER['HTTP_USER_AGENT'], 'Android' ) ) {
      $wptu_browser_classes[] = $classes[] = 'android';
   }

   if ( preg_match( '/iPad|iPhone|Android|Blackberry/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
      $wptu_browser_classes[] = $classes[] = 'mobile';
   }
   // return the $classes array
   return $classes;
}


/**
 * Add "first" and "last" CSS classes to dynamic sidebar widgets. Also adds numeric index class for each widget (widget-1, widget-2, etc.)
 */
add_filter( 'dynamic_sidebar_params','_wptu_widget_first_last_classes' );

function _wptu_widget_first_last_classes($params) {

  global $my_widget_num; // Global a counter array
  $this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
  $arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets  

  if(!$my_widget_num) {// If the counter array doesn't exist, create it
    $my_widget_num = array();
  }

  if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
    return $params; // No widgets in this sidebar... bail early.
  }

  if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
    $my_widget_num[$this_id] ++;
  } else { // If not, create it starting with 1
    $my_widget_num[$this_id] = 1;
  }

  $class = 'class="widget-' . $my_widget_num[$this_id] . ' '; // Add a widget number class for additional styling options

  if($my_widget_num[$this_id] == 1) { // If this is the first widget
    $class .= 'widget-first ';
  } elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
    $class .= 'widget-last ';
  }

  $params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']); // Insert our new classes into "before widget"

  return $params;

}



/**
 * These 4 functions cause WordPress to correctly navigate between posts in the same category.
 * It was first needed in allontherapeutics.com
 */
function _wptu_filter_next_post_sort($sort) {
  $sort = "ORDER BY p.post_title ASC LIMIT 1";
  return $sort;
}
function _wptu_filter_next_post_where($where) {
  global $post, $wpdb;

  return $wpdb->prepare("WHERE p.post_title > '%s' AND p.post_type = 'post' AND p.post_status = 'publish'",$post->post_title);
}

function _wptu_filter_previous_post_sort($sort) {
  $sort = "ORDER BY p.post_title DESC LIMIT 1";
  return $sort;
}
function _wptu_filter_previous_post_where($where) {
  global $post, $wpdb;

  return $wpdb->prepare("WHERE p.post_title < '%s' AND p.post_type = 'post' AND p.post_status = 'publish'",$post->post_title);
}

add_filter('get_next_post_sort',   '_wptu_filter_next_post_sort');
add_filter('get_next_post_where',  '_wptu_filter_next_post_where');

add_filter('get_previous_post_sort',  '_wptu_filter_previous_post_sort');
add_filter('get_previous_post_where', '_wptu_filter_previous_post_where');



/* ACTIONS
----------------------------------------------------------------------------------*/

add_action('wp_head','_wptu_warn_if_local');

// tells the developer if the site is local or live
function _wptu_warn_if_local(){
  
  $address = $_SERVER['SERVER_ADDR'];
  if( strstr($address, '192.168') || strstr($address, '127.0.0.1') ) {
    
  ?>
    <style type="text/css">
    #developer-warning-bar {
  position:fixed;
  background:#676767;
  color:yellow;
  width:100%;
  text-align:center;
  height:20px;
  line-height:20px;
  font-size:120%;
  }
    </style>
  <script type="text/javascript">
  
  function show_warning_bar(){
  
    var site_name ='<?php bloginfo('site_name');?>';    
    var warning_div = document.createElement("div");
    warning_div.id = 'developer-warning-bar';
    var warning_text = document.createTextNode("You are viewing "+site_name+" locally");
    
    if( document.getElementById('wpadminbar') ){  
        var wpadminbar = document.getElementById('wpadminbar');
        wpadminbar.appendChild(warning_text);
    }
    else {
      warning_div.appendChild( warning_text );
      document.body.appendChild( warning_div );
      warning_div.style.top = 0;
    }
  }

  
  addOnload(show_warning_bar);
  
  function addOnload(newFunction) {
  var oldOnload = window.onload;
  

  if (typeof oldOnload == "function") {
    window.onload = function() {
      if (oldOnload) {
        oldOnload();
      }
      newFunction();
    }
  }
  else {
    window.onload = newFunction;
  } 
}
    </script>
    
  <?php 
  }
  

}

add_action( 'wp_head' , '_wptu_add_google_code');

function _wptu_add_google_code(){
   // if the signals plugin is installed
   if( function_exists( 'wptu_plugin_option') ) {
   
      $google_code = wptu_plugin_option( 'google_analytics' );
      
      if(  $google_code != '' ) {
            echo '<script type="text/javascript">';
            echo  "\r\n\t" . $google_code ."\r\n\t\r\n";
            echo "</script>\n\r";
      }
   }
}


add_action( 'wp','_wptu_load_additional_scripts' );

function _wptu_load_additional_scripts() {

  $japi = wptu_plugin_option('jquery_version');
  
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', ( $japi ), false, $jversion );
    wp_enqueue_script( 'jquery' );

   if( !is_admin() ):
    
         if( wptu_plugin_option( 'use_modernizer' ) != '' ) {
            wp_register_script('modinizr', ( wptu_plugin_option( 'use_modernizer' ) ), false, '2.0.6' );
            wp_enqueue_script('modinizr');
         }

   endif;
}

// rip out crap from the backend.
if( is_admin() ):
   // load backend styles:
   wp_enqueue_style( 'wp-admin-overide', WPTU_CORE_PATH.'css/wptu-admin.css', false, 1, 'all' );
endif;


// add ids to the image gallery via javascript
add_action('admin_enqueue_scripts', '_wptu_add_id_to_gallery_action');

function _wptu_add_id_to_gallery_action($where) {
  if ($where == 'media-upload-popup')
    wp_enqueue_script('add_id_to_gallery', WPTU_CORE_PATH.'js/add_id_to_gallery.js', array('jquery', 'media-upload', 'utils'), 1.0, true);
}

// the next 3 functions tidy up breaks.
add_filter('publish_post', '_wptu_publish_fix_the_content');

function _wptu_publish_fix_the_content($the_editor_content) {
  $the_editor_content = str_replace('<p><br class="spacer_" /></p>', '', $the_editor_content);
  return $the_editor_content;
}



add_filter('the_content', '_wptu_fix_the_content', 15);

function _wptu_fix_the_content($content) {
  $content = str_replace('<p><br class="spacer_" /></p>', '', $content);
  $content = str_replace('<p></p>', '', $content);
  $content = str_replace('<p>&nbsp;</p>', '', $content);
  return $content;
}


add_filter('the_editor_content', '_wptu_my_editor_content');

function _wptu_my_editor_content( $content ) {
  $content = str_replace('<p><br class="spacer_" /></p>', '', $content);
  return $content;
}


// this prevents stupid ' appearing in the url.
add_action( 'title_save_pre', '_wptu_do_replace_dashes' );

function _wptu_do_replace_dashes($string_to_clean) {
    # The html entities (&#8211;  and &#8212;) don’t actually work but I include them for kicks and giggles. 
    $string_to_clean = str_replace( array('&#8212;', '—', '&#8211;', '–', '‚', '„', '“', '”', '’', '‘', '…'), array('-','-', '-','-', '-', '', '', '', "", "", ''), $string_to_clean );
    return $string_to_clean;
}



//this sets up the signals plugin settings for the tinymce 
// added 1.0
// updated in 1.0.7 - bugfix 
add_filter( 'tiny_mce_before_init', '_wptu_tiny_mce_before_init' );

function _wptu_tiny_mce_before_init( $init ){
  
  $styles_raw = wptu_plugin_option('tinymce_styles');
  
  if( $styles_raw != '' ){
     if( !is_plugin_active( 'tinymce-advanced/tinymce-advanced.php' ) ){
        $init['theme_advanced_buttons2_add'] = 'styleselect';
     }
     $styles = str_replace( "\r\n",';', $styles_raw );
     $init['theme_advanced_styles'] = $styles;
     
  }
  
  
 return $init;
}

if( wptu_plugin_option( 'use_asset_revving' ) ){

add_filter( 'style_loader_src', '_wptu_remove_wp_ver_css_js', 20, 1 );

add_filter( 'script_loader_src', '_wptu_remove_wp_ver_css_js', 20, 1 );
}

/**
 * [wptu_remove_wp_ver_css_js]
 * http://www.stevesouders.com/blog/2008/08/23/revving-filenames-dont-use-querystring/
 * Removes the version number on the assets, which can cause CDN issues
 * Routes the resource to the original asset.
 * @param  [string] $src  [description]
 * @return [string]  $src [description]
 */
function _wptu_remove_wp_ver_css_js( $src ){
  
    if( is_admin() ){
      return $src;
    }

    if( is_string( $src ) ){

      if ( strstr( $src, 'ver=' ) ){

      // external resources must be skipped.
      $basename =  basename( get_site_url() ); 
      $host = $_SERVER['HTTP_HOST'];

        // if url starts with a '/' it should be parsed, because it's an absolute path.
        // or if the host name appears in the url.
        if( strpos( $src ,'/' ) == 0 || strstr( $src , $host ) ){

          preg_match('/(?!\?ver=)(\d*)$/', $src , $matches );

            if( $matches[0] > 0 ){
              $version = $matches[0];
              $src = remove_query_arg( 'ver', $src );
              $file = pathinfo( basename($src));
              $src = str_replace( '.' . $file['extension'], '.' . $version . '.' . $file['extension'], $src );
            }
          } 
      }
    }
  return $src;
}

// remove the id atttribute from style/js tags
add_filter( 'style_loader_tag' , '_wptu_style_loader_tag');

function _wptu_style_loader_tag( $tagstring ){

  if( is_admin()){
    return $tagstring;
  }
  $tagstring = preg_replace('/id=(\'|\").*?(\'|\")/', '', $tagstring );
  return $tagstring;
}

/* USEFUL FUNCTIONS
----------------------------------------------------------------------------------*/

/**
 *
 * Creates a deep multi-dimmension array of image properties which you use directly
 * in you template
 *
 * @todo $caption to be configured
 * @since 0.0.1
 * @param int $post_id (optional) if $attachment_id is not set
 * @param int $attachment_id (optional) if $post_id is not set
 * @param bool $cf_image if the @attachment_id is being sent via a Custom Field by the CFT plugin. The
 * format is value,image name e.g. 14,wptu-header.jpg
 * @param string $link sets the href property
 * @param string $size sets the image_tag property with this size.
 * @param bool $fancybox sets whether you want to use fancybox sets the rel property of the a tag
 * @param string $rel sets the rel property of the a tag
 * @param string $class sets the class property of the a tag or image depending on situation
 * @param string $title sets the title property of the a tag
 * @param string $caption nothing yet
 * @param int $width sets width on output image tag - useful for forcing a size
 * @param int $height sets width on output image tag - useful for forcing a size
 * @param string @alt sets alt tag on the image itself
 * @return array Everything about the image is returned as a multi dimmensional array
 * 
 * @todo Image tag needs to return a smaller image if the one asked for is not available.
 */
function wptu_get_attachment($params){
  global $post;
  global $wpdb;
  //parameters
  // for post images
  $post_id =false;
  $attachment_id=false;
  $cf_image=false;
  $link=false;
  $size='thumbnail';
  $fancybox=false;
  $rel=false;
  $class=false;
  $title=false;
  $caption=false;
  $width=false;
  $height=false;
  $alt=false;
  $before=false;
  $after=false;

  
  if( is_string( $params) ){
        parse_str( $params );
  }
  if( is_array( $params ) ){
        extract( shortcode_atts( $params , array() ) );
  }

  //setup array with attachment information

  $attachment = array();
  $attachment['post_id'] = $post_id ? $post_id : $post->ID;

  if($attachment_id){
    $attachment['attachment_id'] = intval($attachment_id);
  }
  else {
  // no image provided so well have to use the one from the post image
    $thumb_id = get_post_meta($attachment['post_id'],'_thumbnail_id',false );

    if( is_array($thumb_id) ){
      $thumb_id = $thumb_id[0];
      $attachment['attachment_id'] = intval($thumb_id);
    }
    else {
      $attachment['attachment_id'] = intval($thumb_id);
    }
  }
  // if coming from a custom field
  // the format expected is "id,filename.extension" This will overwrite
  if($cf_image!=''){
    $parts = explode(',',$cf_image);
    $image = array();
    $attachment['attachment_id'] = intval($parts[0]);
  }
  
  // get height and widths of image
  $attachment['link'] = $link? $link : wp_get_attachment_url($attachment['attachment_id']);
  $attachment_src = wp_get_attachment_image_src($attachment['attachment_id'], $size);

  // the fullsize image is retrieved here.
  $attachment['source']  = $attachment_src[0];
  $attachment['width'] = $attachment_src[1];
  $attachment['height'] = $attachment_src[2];
  $attachment['image_size'] = $size;
  $attachment['meta_data'] = wp_get_attachment_metadata($attachment['attachment_id']);
  $attachment['mime'] = get_post_mime_type($attachment['attachment_id']);
  $attachment['before'] = $before ? $before : '';
  $attachment['after'] = $after ? $after : '';

  wp_get_attachment_image_src($attachment_id, $size);
  if($attachment['mime'] =='image/jpg' || $attachment['mime'] =='image/jpeg' || $attachment['mime'] =='image/gif' || $attachment['mime'] =='image/png'){

    // get attachments from a post
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts WHERE {$wpdb->prefix}posts.ID = %d" ,  $attachment['attachment_id'] );

    $properties = $wpdb->get_results( $query );

    foreach($properties as $property) {

          if($property->ID == $attachment['attachment_id']){

                 $attachment['title'] = $title ? $title : $property->post_title;
                 $attachment['caption'] = $caption ? $caption : $property->post_excerpt;
                 $attachment['alt'] =  $alt ? $alt : get_post_meta($attachment['attachment_id'], '_wp_attachment_image_alt', true);
                 $attachment['description'] = $property->post_content;
                 $attachment['date'] = $property->post_date;

                 $attachment['width'] = $width ? $width : $attachment['width'];
                 $attachment['height'] = $height ? $height : $attachment['height'];
                 $attachment['link'] = $link ? $link : get_permalink( $attachment['post_id'] );
                 $attachment['class'] = $class ? str_replace(',',' ',$class) :'';
                 $attachment['filename'] = basename($attachment['source']);


                 $a_title = ( $attachment['title'] != '') ? 'title="' . $attachment['title']  .'"' : '';

                 if( $fancybox  ) {
                     // get the large image and use it as the popup image

                    $large_image_link = wp_get_attachment_image_src( $attachment['attachment_id'], 'large' );
                    $large_image_link = $large_image_link[0];


                    $attachment['image_tag'] = $attachment['before'] .  '<a href="'. $large_image_link  .'" class="fancybox ' . $attachment['class']  . '"' . $a_title . '>';
                    $attachment['image_tag'] .= '<img src="'.$attachment['source'].'" width="'.$attachment['width'].'" height="'.$attachment['height'].'" alt="'.$attachment['alt'] .'">';
                    $attachment['image_tag'] .= '</a>' . $attachment['after'];
                 }

                 elseif( $link && !$fancybox ){
                    $attachment['image_tag'] = $attachment['before'] .'<a href="' . $attachment['link'] . '"'. $a_title .' class="'. $attachment['class'] .'">';
                    $attachment['image_tag'] .= '<img src="'.$attachment['source'].'" width="'.$attachment['width'].'" height="'.$attachment['height'].'" alt="'.$attachment['alt'] .'" class="'.$attachment['class'] .'">';
                    $attachment['image_tag'] .= '</a>'.$attachment['after'];
                 }
                 
                 else {
                    $attachment['image_tag'] = $attachment['before'] . '<img src="'.$attachment['source'].'" width="'.$attachment['width'].'" height="'.$attachment['height'].'" alt="'.$attachment['alt'] .'" class="'. $attachment['class'] .'">' . $attachment['after'];
                 }

              }
          }

  }
  return $attachment;

}

/*
 *  This is used to add images to posts. This is the way to overide
 *  the default wptu_get_attachment behaviour.
 * 
 */
function wptu_add_post_image(){
  global $post;

  /* Allow plugins/themes to filter the arguments. */
  $args = apply_filters( 'wptu_add_post_image_args', $args );
  
  /* Merge the input arguments and the defaults. */
  $args = wp_parse_args( $args, $defaults );
  
  /*set attributes*/
  $title  =    $args['title'] ? $args['title'] : $post->post_title;
  $before    =    $args['before']   ? $args['before'] : '';
  $after    =    $args['after']   ? $args['after'] : '';

   if( function_exists( 'wptu_get_attachment' ) ){

      $output ='';
      if( is_page() || is_single() ){
         
         $size   =      $args['size']     ? $args['size'] : 'medium';
         $class  =      $args['class']    ? $args['class']: "alignleft";
         $alt  =        $args['alt']      ? $args['alt'] : $post->post_title;
         $fancybox  =   $args['fancybox'] ? true : false;
         
         $image = wptu_get_attachment("before={$before}&after={$after}&size={$size}&class={$class}&link={$link}&fancybox={$fancybox}");
         
         $output .= $image['image_tag'];
         echo $output;
         return true;
      }
      else {
         $size   =    $args['size']  ? $args['size']  : 'thumbnail';
         $class  =    $args['class'] ? $args['class'] : "alignleft";
        
         $link  =     $args['link']  ?   urldecode( $args['link'] )  : get_permalink( $args['post_id']);

         $fancybox  = $args['fancybox'] ? $args['fancybox'] : false;

         $image = wptu_get_attachment("before={$before}&after={$after}&size={$size}&class={$class}&link={$link}&fancybox={$fancybox}");

         $output .= $image['image_tag'];

         echo $output;
         return true;
      }
   }
}


/**
 * Scans a directory - useful for the cft plugin
 *
 * @param string $dir full path to the directory
 * @param bool $list_directories  include directories in the return array 
 * @param bool $skip_dots whether you want to skip dots or not
 * @return array  $array array of files found in that directory 
 */ 
function wptu_scandir( $dir , $list_directories = false , $skip_dots = true) {
    $dirArray = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (($file != "." && $file != ".." ) || $skip_dots == true) {
                if( $list_directories == false ) {
                                        if( is_dir( $dir. DIRECTORY_SEPARATOR . $file ) ) {
                                            continue;  
                                        }
    }
                array_push($dirArray,basename($file));
            }
        }
        closedir($handle);
    }
    sort( $dirArray );
    return $dirArray;
}

/**
 * Determines if the page passed is in a child of another
 *
 * @param int or bool $page  the page in question.
 * @param int $ancestor is the page in question a descendant of this ancestor
 * @return bool
 */
function wptu_is_descendant( $page, $ancestor = false ) {
    if( !is_object( $page ) ) {
        $page = intval( $page );
        $page = get_post( $page );
    }
    if( is_object( $page ) ) {
        if( isset( $page->ancestors ) && !empty( $page->ancestors ) ) {
            if( !$ancestor )
                return true;
            else if ( in_array( $ancestor, $page->ancestors ) )
                return true;
        }
    }
    return false;
}


/**
*
* Determines if link passed is of the same domain as the current site or not.
* This is quite basic and probably needs to be beeded later.
*
* @todo beef up the checks for wptu_is_external_link
* @param string | int $link in query_posts
* @return bool
*/

function wptu_is_internal_link( $link ){
  
  // if link is int then it must be a post id. Check if it exists then return true
  if( is_int( $link ) )
      return true;

  // if the link is text then
  if( is_string( $link ) ) {

     $wp_site_url = get_site_url();
     $basename =  basename( $wp_site_url );
     
     if( strstr( $link, $basename ) ) {
         return true;
     }

     if( preg_match('/^#.*$/', $link ) ){
        return true;
     }

   }
   return false;
}

function wptu_str_replace_once($needle , $replace , $haystack){
    // Looks for the first occurence of $needle in $haystack
    // and replaces it with $replace.
    $pos = strpos($haystack, $needle);
    if ($pos === false) {
        // Nothing found
    return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

// @since  0.9.8.8
// @improvement 1.0.6
// @ String
function wptu_smart_truncate( $string, $your_desired_width=300, $trailing='...' ){

     //remove div tags
     $string  = preg_replace('/<div.*?>/','', $string );
     $string = str_replace('</div>','',$string);
    
     //remove new lines so substr can work.
     $string = str_replace("\n",'',$string);
     
     $string = apply_filters( 'wptu_smart_truncate_filtering', $string );
      
     
    if(strlen($string) > $your_desired_width) {
    $string = wordwrap($string, $your_desired_width);
    $string = substr($string, 0, strpos($string, "\n"));
    $string = $string . $trailing;
    }
    
    $string = apply_filters( 'wptu_smart_truncate_overide', $string );
    
    return $string;

}

/** 
 * Detects if the current page has children or not
 * Added 1.0.3
 * Updated 1.1.1
 */
function wptu_post_has_children( $post = false, $post_type = 'page' ){
   
   if( !$post ){
      global $post;
   }
   
   $children = wp_list_pages('&child_of='.$post->ID.'&echo=0&title_li=&post_type='. $post_type );
   if( $children ){
      return $children;
   }
}

/*
 * function wptu_get_parents
 * A way to get around the BS wp_get_post_ancestors issue that has
 * been in Trac for 15+ months
 * @param int $the_page_id
 * @return array $all_parents
 * 
 */
function wptu_get_parents( $the_page_id ){
   
   global $wpdb;
   
   static $parents = array();
   
   array_push( $parents , $the_page_id );
   
   $sql = "SELECT `post_parent` FROM {$wpdb->posts} WHERE `ID` = {$the_page_id}";
   
   $this_post =  $wpdb->get_results( $sql, OBJECT );
   
   if( $this_post[0]->post_parent == 0 ) {
      $all_parents = array_reverse( $parents );
   } 
   else {
      return wptu_get_parents(  $this_post[0]->post_parent  );
   }
   return $all_parents;
}