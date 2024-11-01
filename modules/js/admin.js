jQuery(document).ready(function($){


$('body').on( 'click', '.save_redirect', function( e ){
 
 
		// recaptcha validation
	  
		if( $('.redirect_url_'+$(this).attr('data-id')).val() == '' ){
			return true;
		}
	  
		var data = {
			id  : $(this).attr('data-id'),
			redirect_url  : $('.redirect_url_'+$(this).attr('data-id')).val(),
			action : 'update_redirect'
		}
		jQuery.ajax({url: wtf_local_data.ajaxurl,
				type: 'POST',
				data: data,            
				beforeSend: function(msg){
						jQuery('body').prepend('<div class="loader"></div>');
					},
					success: function(msg){
						$('.loader').replaceWith('');
						
						console.log( msg );
						var obj = jQuery.parseJSON( msg );
						 
						if( obj.result == 'success'){
			 
						
						
						} else{
								alert('Error');
							}
						 
					} , 
					error:  function(msg) {
									
					}          
			});
	 
	})
	
	$('body').on( 'click', '.delete_redirect', function( e ){
 
		if( !confirm("Are you sure?") ){
				return false;
		}
 
 
		// recaptcha validation
	  
	 
		var id = $(this).attr('data-id');
		var pnt = $(this);
		var data = {
			id  : id,

			action : 'delete_redirect'
		}
		jQuery.ajax({url: wtf_local_data.ajaxurl,
				type: 'POST',
				data: data,            
				beforeSend: function(msg){
						jQuery('body').prepend('<div class="loader"></div>');
					},
					success: function(msg){
						$('.loader').replaceWith('');
						
						console.log( msg );
						var obj = jQuery.parseJSON( msg );
						 
						if( obj.result == 'success'){
				 
							pnt.parents('.row_container').fadeOut(function(){
								pnt.parents('.row_container').replaceWith();
							})
						
						
						} else{
								alert('Error');
							}
						 
					} , 
					error:  function(msg) {
									
					}          
			});
	 
	})

	
	$('body').on( 'click', '.archive_redirect', function( e ){
 
		if( !confirm("Are you sure?") ){
				return false;
		}

		// recaptcha validation 
		var id = $(this).attr('data-id');
		var pnt = $(this);
		var data = {
			id  : id,

			action : 'archive_redirect'
		}
		jQuery.ajax({url: wtf_local_data.ajaxurl,
				type: 'POST',
				data: data,            
				beforeSend: function(msg){
						jQuery('body').prepend('<div class="loader"></div>');
					},
					success: function(msg){
						$('.loader').replaceWith('');
						
						console.log( msg );
						var obj = jQuery.parseJSON( msg );
						 
						if( obj.result == 'success'){
				 
							pnt.parents('.row_container').fadeOut(function(){
								pnt.parents('.row_container').replaceWith();
							})
						
						
						} else{
								alert('Error');
							}
						 
					} , 
					error:  function(msg) {
									
					}          
			});
	 
	}) 
	
	
	// filterts
	$('body').on( 'change', '#display_archived', function(){
		if( $(this).attr('checked') == 'checked' ){
			$('.is_archived').addClass('archived_visible');
		}else{
			$('.is_archived').removeClass('archived_visible');
		}
	})
	
	$('body').on( 'change', '#show_empty_redirects', function(){
		$('.single_row').each(function(){
			if( $('.redirect_url_field', this).val() != '' ){
				$(this).fadeOut();
			}else{
				$(this).fadeIn();
			}
		})
	})
	
});