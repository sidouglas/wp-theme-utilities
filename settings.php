<?php
class WPTU_options {
	
	private $sections;
	private $checkboxes;
	private $settings;
	
	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
		
    //include the theme's functions file
     $theme = trailingslashit( get_stylesheet_directory() );
     
     if( file_exists( $theme . 'wptu-core-options.php' ) ){
        require_once $theme . 'wptu-core-options.php'; 
     }

     if( file_exists( $theme . 'theme-utilities-settings.php' ) ){
        require_once $theme . 'theme-utilities-settings.php'; 
     }
     
	// This will keep track of the checkbox options for the validate_settings function.
    
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();
		
		$this->sections['general']      = __( 'General Settings' );
		$this->sections['social']   = __( 'Social Media' );

		//$this->sections['about']        = __( 'About' );
    
    $args = $this->wptu_core_section_options();
    
    if( is_array( $args ) ){
       foreach( $args as $key => $value ){
          $this->sections[ $key ] = $value;
       }
    }
       $this->sections['reset']        = __( 'Reset to Defaults' );	
       
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'wptu_plugin_options' ) )
			$this->initialize_settings();
		
	}
	
	/**
	 * Add options page
	 *
	 * @since 1.0
	 */
	public function add_pages() {
		
		$admin_page = add_options_page( __( 'WPTU Options' ), __( 'WPTU Options' ), 'manage_options', 'wptu-plugin-options', array( &$this, 'display_page' ) );

		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );
		
	}
	
	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'      => 'default_field',
			'title'   => __( 'Default Field' ),
			'desc'    => __( 'This is a default description.' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'wptu-plugin-options', $section, $field_args );
	}
	
	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {
		
		echo '<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2>' . __( 'Theme Utility Options' ) . '</h2>';
	
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';
		
		echo '<form action="options.php" method="post">';
	
		settings_fields( 'wptu_plugin_options' );
		echo '<div class="ui-tabs">
			<ul class="ui-tabs-nav">';
		
		foreach ( $this->sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		
		echo '</ul>';
		do_settings_sections( $_GET['page'] );
		
		echo '</div>';
    
    if( is_admin() ):
       echo '<p>Developers: <strong>You can additional fields by adding a theme-utilities-settings.php file to your installed theme.</strong></p>';
    endif;
    
		echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>
		
	</form>';
	
	echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
			var sections = [];';
			
			foreach ( $this->sections as $section_slug => $section )
				echo "sections['$section'] = '$section_slug';";
			
			echo 'var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
			wrapped.each(function() {
				$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
			});
			$(".ui-tabs-panel").each(function(index) {
				$(this).attr("id", sections[$(this).children("h3").text()]);
				if (index > 0)
					$(this).addClass("ui-tabs-hide");
			});
			$(".ui-tabs").tabs({
				fx: { opacity: "toggle", duration: "fast" }
			});
			
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});
			
			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});
			
			$(".wrap h3, .wrap table").show();
			
			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
			
			// Browser compatibility
			if ($.browser.mozilla) 
			         $("form").attr("autocomplete", "off");
		});
	</script>
</div>';
		
	}
	
	/**
	 * Description for section
	 *
	 * @since 1.0
	 */
	public function display_section() {
		// code
	}
	
	/**
	 * Description for About section
	 *
	 * @since 1.0
	 */
	public function display_about_section() {
		
		// This displays on the "About" tab. Echo regular HTML here, like so:
		// echo '<p>Copyright 2011 me@example.com</p>';
		
	}
	
	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_setting( $args = array() ) {
		
     
		extract( $args );
		
		$options = get_option( 'wptu_plugin_options' );
		
		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
			
			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
				break;
			
			case 'checkbox':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="wptu_plugin_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
				echo $this->admin_show_parameter( $args['label_for'] );
        
				break;
			
			case 'select':
				echo '<select class="select' . $field_class . '" name="wptu_plugin_options[' . $id . ']">';
				
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' ){
             echo '<br /><span class="description">' . $desc . '</span>';
        }
				  echo $this->admin_show_parameter( $args['label_for'] );
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="wptu_plugin_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' ){
					echo '<br /><span class="description">' . $desc . '</span>';
        }
        echo $this->admin_show_parameter( $args['label_for'] );
        
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="wptu_plugin_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . strip_tags( $options[$id] ) . '</textarea>';
				
				if ( $desc != '' ){
					echo '<br /><span class="description">' . $desc . '</span>';
        }
         echo $this->admin_show_parameter( $args['label_for'] );
        
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="wptu_plugin_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				
				if ( $desc != '' ) {
					echo '<br /><span class="description">' . $desc . '</span>';
        }
        echo $this->admin_show_parameter( $args['label_for'] );
				
				break;
			
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="wptu_plugin_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		
		 		if ( $desc != '' ) {
		 			echo '<br /><span class="description">' . $desc . '</span>';
        }
        
        echo $this->admin_show_parameter( $args['label_for'] );
        
		 		break;
		 	
		}
		
	}
	
	/**
	 * Settings and defaults
	 * 
	 * @since 1.0
	 */
	public function get_settings() {
		
		/* General Settings
		===========================================*/
		$jversion = '<strong>' . wptu_plugin_option('jquery_version') . '</strong>'; 
				
		$this->settings['jquery_version'] = array(
			'title'   => __( 'jQuery Version' ),
			'desc'    => __( "Using: $jversion" ),
			'std'     => 'http://code.jquery.com/jquery-latest.min.js',
			'type'    => 'text',
			'section' => 'general'
		);
		
		
		 $this->settings['use_modernizer'] = array(
			'section' => 'general',
			'title'   => __( 'Use Modernizer' ),
			'desc'    => __( "Path to Modernizer" ),
			'type'    => 'text',
			'std'     => 'http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js'
		);
		
		$this->settings['sniff_browser'] = array(
			'section' => 'general',
			'title'   => __( 'Add extra browser body classes' ),
			'desc'    => __( "This will detect the user agent and add classes for the body ( can slow the site down / caching issues too )" ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
	    $this->settings['google_analytics'] = array(
			'title'   => __( 'Google Analytics Code' ),
			'desc'    => __( 'This adds google analytics to the header' ),
			'std'     => "",
			'type'    => 'textarea',
			'section' => 'general'
		);

		$this->settings['tinymce_styles'] = array(
			'title'   => __( 'TinyMCE extra styles' ),
			'desc'    => __( 'Adds styles to the editor. Note the convention &mdash; rule per line' ),
			'std'     => "Align Left=alignleft\nAlign Right=alignright\nClear Content=clear",
			'type'    => 'textarea',
			'section' => 'general'
		);
    
 

    $htaccess_revving = '';
    
    if( wptu_plugin_option( 'use_asset_revving' ) ){
    $htaccess_revving = "<br />Add this to .htaccess:
<pre># ------------------------------------------------------------------------------
# | Filename-based cache busting                                               |
# ------------------------------------------------------------------------------

# To understand why this is important and a better idea than `*.css?v231`, read:
# http://stevesouders.com/blog/2008/08/23/revving-filenames-dont-use-querystring

&lt;IfModule mod_rewrite.c&gt;
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.+)\.(\d+)\.(js|css|png|jpg|gif)$ $1.$3 [L]
&lt;/IfModule&gt;</pre>";
    }

    $this->settings['use_asset_revving'] = array(
			'section' => 'general',
			'title'   => __( 'Enable CDN Asset Revving' ),
			'desc'    => __( "This will be friendly to CDNs.{$htaccess_revving}" ),
			'std'     => 0,
			'type'    => 'checkbox'
		);

		
    $this->settings['twitter_link'] = array(
			'title'   => __( 'Twitter link' ),
			'desc'    => __( "The full url to the client's Twitter account" ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'social'
		);
    
    $this->settings['facebook_link'] = array(
			'title'   => __( 'Facebook link' ),
			'desc'    => __( "The full url to the client's Facebook account" ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'social'
		);
    
    $this->settings['youtube_link'] = array(
			'title'   => __( 'YouTube link' ),
			'desc'    => __( "The full url to the client's YouTube account" ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'social'
		);
    
    $this->settings['linkedin_link'] = array(
			'title'   => __( 'Linked In link' ),
			'desc'    => __( "The full url to the client's Linked In account" ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'social'
		);
    
    $params = $this->wptu_core_section_settings();
    
   if( is_array($params) ) {
         foreach ($params as $key => $value) {

            $props = array();

            foreach ($value as $prop => $propval) {
               $props[$prop] = $propval;
            }
            $this->settings[$key] = $props;
         }
      }

    /*
		$this->settings['example_checkbox'] = array(
			'section' => 'general',
			'title'   => __( 'Example Checkbox' ),
			'desc'    => __( 'This is a description for the checkbox.' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
     * 
		
		$this->settings['example_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'    => 'Example Heading',
			'type'    => 'heading'
		);
		
		$this->settings['example_radio'] = array(
			'section' => 'general',
			'title'   => __( 'Example Radio' ),
			'desc'    => __( 'This is a description for the radio buttons.' ),
			'type'    => 'radio',
			'std'     => '',
			'choices' => array(
				'choice1' => 'Choice 1',
				'choice2' => 'Choice 2',
				'choice3' => 'Choice 3'
			)
		);
		
		$this->settings['example_select'] = array(
			'section' => 'general',
			'title'   => __( 'Example Select' ),
			'desc'    => __( 'This is a description for the drop-down.' ),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => 'Other Choice 1',
				'choice2' => 'Other Choice 2',
				'choice3' => 'Other Choice 3'
			)
		);
		
		*/					
		/* Reset
		===========================================*/
		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset WordPress Theme Utilities Settings' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset the plugin options to their defaults.' )
		);
		
	}
	
	/**
	 * Initialize settings to their default values
	 * 
	 * @since 1.0
	 */
	public function initialize_settings() {
		
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
		//disable wpautop filter
		remove_filter ('the_content',  'wpautop');
		update_option( 'wptu_plugin_options', $default_settings );
		add_filter ('the_content',  'wpautop');
	}
	
	/**
	* Register settings
	*
	* @since 1.0
	*/
	public function register_settings() {
		
		register_setting( 'wptu_plugin_options', 'wptu_plugin_options', array ( &$this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( &$this, 'display_about_section' ), 'wptu-plugin-options' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'wptu-plugin-options' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
		
	}
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function scripts() {
		
		wp_print_scripts( 'jquery-ui-tabs' );
		
	}
	
	/**
	* Styling for the theme options page
	*
	* @since 1.0
	*/
	public function styles() {
		
		$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		
		wp_register_style( 'mytheme-admin', WPTU_CORE_PATH . 'css/wptu-settings.css' );
		wp_enqueue_style( 'mytheme-admin' );
		
	}
	
	/**
	* Validate settings
	*
	* @since 1.0
	*/
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'wptu_plugin_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		return false;
		
	}
  
/*
 *  Adds a helpful reference to the option for theming
 * 
 *  @since 1.1
 */
  public function admin_show_parameter( $array_label ) {
     
      if( current_user_can('administrator') ){
        return "<br/> <span style='color:red; font-size:80%'>wptu_plugin_option( '{$array_label}' )</span>";
      }
      
  }
  
 
  /*
   * Pluggable Function for the installed theme to add more features
   * 
   */
  
 public function wptu_core_section_options() {
     
     if( function_exists('wptu_core_section_options') ){
        $args = wptu_core_section_options();
        return $args;
     } 
     return false;
  }
  
  /*
   * Pluggable Function for the installed theme to add more features
   * 
   */
  
  public function wptu_core_section_settings() {
     
     if( function_exists('wptu_core_section_settings') ){
        $args = wptu_core_section_settings();
        return $args;
     } 
     return false;
  }
  
	
}

$theme_options = new WPTU_options();

function wptu_plugin_option( $option ) {
	$options = get_option( 'wptu_plugin_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}
?>