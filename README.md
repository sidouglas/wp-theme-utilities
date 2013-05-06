# A guide to WordPress Theme Utilites

Version _1.0_

_WordPress Theme Utilites_ was written to take care of some of the numerous
required plugins and hacks that tend to build up in template function files.

This plugin was written by [Simon Douglas](http://simondouglas.com) 2012 --
2013

### Public

  * **wptu_get_attachment**: gets everything you want for an image.   
For example:

    
    $image_properties = wptu_get_attachment("post_id={$post_id}&attachment;_id={$image_id}&size;=thumbnail&alt;={$title}&width;={$width}&height;={$height}");

  
Where `$post_id = $post->ID` and `$attachment_id is the id of the image`

The `$size` parameter will build up the `image_tag` value, you can just echo
that out to your template.

You will get a deep array of image properties thus:

    
    
        
        [post_id] => 262
        [attachment_id] => 263
        [link] => http://www.vancouverfoundationawards.ca/wp-content/uploads/2011/01/13/allison-b/SmallGrantProject.jpg
        [source] => http://www.vancouverfoundationawards.ca/wp-content/uploads/2011/01/13/allison-b/SmallGrantProject-138x108.jpg
        [width] => 138
        [height] => 108
        [image_size] => thumbnail
        [meta_data] => Array
            (
                [width] => 4224
                [height] => 3168
                [hwstring_small] => height='96' width='128'
                [file] => 2011/01/13/allison-b/SmallGrantProject.jpg
                [sizes] => Array
                    (
                        [thumbnail] => Array
                            (
                                [file] => SmallGrantProject-138x108.jpg
                                [width] => 138
                                [height] => 108
                            )
    
                        [medium] => Array
                            (
                                [file] => SmallGrantProject-300x225.jpg
                                [width] => 300
                                [height] => 225
                            )
    
                        [large] => Array
                            (
                                [file] => SmallGrantProject-400x300.jpg
                                [width] => 400
                                [height] => 300
                            )
    
                        [news-image] => Array
                            (
                                [file] => SmallGrantProject-185x100.jpg
                                [width] => 185
                                [height] => 100
                            )
    
                    )
    
                [image_meta] => Array
                    (
                        [aperture] => 2.9
                        [credit] => 
                        [camera] => COOLPIX P6000
                        [caption] => 
                        [created_timestamp] => 
                        [copyright] => 
                        [focal_length] => 6
                        [iso] => 64
                        [shutter_speed] => 0.00215238
                        [title] => 
                    )
    
            )
    
        [mime] => image/jpeg
        [title] => SmallGrantProject
        [caption] => 
        [alt] => A green transformation
        [description] => 
        [date] => 2011-01-13 00:14:05
        [image_tag] => ![A green transformation](http://www.vancouverfoundationawards.ca/wp-content/uploads/2011/01/13/allison-b/SmallGrantProject-138x108.jpg)
        [filename] => SmallGrantProject-138x108.jpg
        [class] => 
        
        

Therefore to get the camera used for example we could go
`$image_properties['meta_data']['image_meta']['camera']` which would retrieve
COOLPIX P6000.

  * **wptu_add_post_image**: This attaches an image via a hook to the post. It is coupled with _wptu_get_attachment_ as a means to add post images. Parameters must be added through the filter _wptu_add_post_image_args_
  * **wptu_add_post_image_args: **Filter used to add parameters to _wptu_add_post_image _function. 
  * <del>**wptu_get_cft_id**: retrieves a value between known delimeters. Useful with the CFT plugin.</del>
  * <del>**wptu_get_cft_repeating_data**: Used with the CFT in side of repeating field sets to get repeating data. Note: Repeating fields in the CFT plugin are useless, so use the [Magic Fields Plugin](http://magicfields.org/) instead.</del>
  * **wptu_get_the_browser**: gets useful properties of the user browser. This is called automatically and added to the `body_class` hook. You probably shouldn't have to call it again as it can add considerable time to file execution.
  * **wptu_is_descedant**: determines if a page is a child(n) of another.
  * $**wptu_browser_classes**: global registed that contains browser specific classes.

## WPTU Admin Panel

_Added in version 1.0_

**WordPress Menu -> Settings -> WordPress Theme Utilites**

This is a customisable panel that can be used to store data in the Options
Table for your website.

Data is stored as a serialised array so it can be access easily.

Using `wptu_plugin_option('field_name')` will retrieve the value of the field
for you.

##### General Settings

  * jQuery Version
  * Use Modernizer or not
  * Add body classes to the `body_class()` function
  * Add extra TinyMCE styles. This still works without TinyMCE Advanced
  * Google Analytics code for the header

##### Social Media

Twitter, Facebook, YouTube, LinkedIn for your client site

##### Adding Extra Settings

Add `wptu-core-options.php` to your theme, then use the provided hooks to add
more fields.

An example from [WoodWorks](http://woodworks.org)

    
    
    /*Adds A heading to the WordPress Theme Utilites Options Panel - take a look at wptu-settings.php for info...*/
    function wptu_core_section_options(){
        $args =  array( 'woodworks' => 'Woodworks Custom Post Types' );
        return $args;
    }
    /* Adds a field to the section woodworks */
    function wptu_core_section_settings(){
    
       $args = array('ww_staff_slug' => array( 
                                        'title'=>'Staff Archive Slug',
                                        'desc'=>'This is for breadcrumb functionality. Define the with_front property of the slug',
                                        'std'=>'',
                                        'type'=>'text', // text,textarea,radio,select
                                        'section'=>'woodworks' )
                   );
       return $args;
    }
    

## Shortcodes

  * [bloginfo] Returns the shortcodes for bloginfo e.g. [bloginfo key="name"] returns the sitename.   
The WP [Codex has
values](http://codex.wordpress.org/Function_Reference/bloginfo).

  * [admin_email] Returns the admin email in the format [bloginfo('name')](admin_email). No parameters.
  * [email] e.g. [email mailbox="simon" url="wptu.ca" subject="Enquiry From Site" display_text="display text!"].   
This outputs literally:

    
    <script type="text/javascript">eE=('simon' + '%40' + 'wptu.ca?subject=Enquiry%20From%20Site');eD=('<span class="display-text">display text!</span>');document.write('<a href="&#109&#97&#105&#108&#116;&#111;&#58;' + eE + '">' + eD + '</a>');</script>
    

  * [include]. Does a simple php include. [include file="path_to_file"]. Put your includes in your template directory.
  * [loop]. This is a useful way to drop in post data into a page.  

    
    [loop category='slug_name_or_id' query='anything_you_want_eg_orderby' pagination='true']

_Improved in 1.0.13_. Now accepts parameters such as taxonomy (
`tax="tax_name"` ), `term="term_name"` and `loop` template.

    
    
        // example from woodworks.org
        [loop query="posts_per_page=-1&post;_type=resources&orderby;=date&order;=ASC" tax="resource_type" terms="information-sheets" loop="excerpt-information"]
        // result:
        // Get all posts from the resource post type order by date ascending that have 
        // the term information-sheets ( like a tag )
        // Output the data to template "loop-excerpt-information.php" which is responsible for formatting.
        

If wp_pagenavi is present, `pagination=true` will enable it.

_Improvements in 1.1_

WP PageNavi has slightly changed so this plugin had to be updated. Compatible
with WP PageNavi 2.83

Implementation is the same, but there could be bugs with `$paged`.

## Private

This stuff all turns up for free.

  * WPTU Developer Notes added to the dashboard so you can leave notes to you and other developers. Added ()
  * Added warning bar if you are viewing the site locally ( 0.9.8.2 )
  * Added [Modinzer](http://www.modernizr.com/) support for html5 sites and detecting CSS3.
  * You can place your shortcodes in **text widgets** and they evalute.
  * Removing WordPress version and fluff in the `meta` header.
  * jQuery is added automatically from Google.
  * ID's are added to the Media browser, for your convenience. IDs are also added to all Admin tables.
  * ID's are added to the CMS Tree plugin if it is available ( usually is on most WPTU Sites )
  * Various Tinymce clean up filters which run to remove empty spaces and line breaks (the latter could be a problem depending on your setup.)

2013 [simondouglas.com](http://www.simondouglas.com).