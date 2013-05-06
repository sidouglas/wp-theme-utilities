( function($) {
   
   $(function (){ 
   
      $('.media-item').each(function(){
		var self = $(this);
		var attachment_id = self.attr('id').match(/([0-9]+)/);
		self.find('.filename')
				.find('.title')
					.after('<span style="color:#D54E21; padding:0 3px 0 8px;">' + 'id:' + attachment_id[1] + '</span>');
   });

} ) ( jQuery );