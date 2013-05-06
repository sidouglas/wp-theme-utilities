( function($) {
   
   $(function (){ 
      
      var ajaxQueue = $({});

      $.ajaxQueue = function(ajaxOpts) {

         var oldComplete = ajaxOpts.complete;

         ajaxQueue.queue(function(next) {

            ajaxOpts.complete = function() {
               if (oldComplete) oldComplete.apply(this, arguments);

               next();
            };

            $.ajax(ajaxOpts);
         });
      };

      $.ajaxQueue({
        success: function(data) {
            add_page_ids();
        }
    });

    $('body').bind( 'click', add_page_ids );

     function add_page_ids(){
        
         $('.cms_tpv_container li > a').each(function(){
            $this = $(this);
            
            if( !$this.hasClass('wptu-plugin-add-ids') ){
               $this.addClass( 'wptu-plugin-add-ids' );
               var the_text = $this.html();
               if( the_text.length > 0  ){
                  
                  var the_id = $this.attr('href').match(/(?!\=)([0-9]+)/)[1];
                  $this.html( the_text + '&nbsp;<span style="color:red; font-size:90%;">id:' + the_id + '</span>' );
               }
            }
            
         });
      }
      
   });
} ) ( jQuery );
