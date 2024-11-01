<?php 

add_action('wp_ajax_update_redirect', 'wtf_update_redirect');
add_action('wp_ajax_nopriv_update_redirect', 'wtf_update_redirect');

function wtf_update_redirect(){
	global $current_user, $wpdb;
	 	
		$table_name = 'wtf_404_log';
		$table_name =  $wpdb->prefix.$table_name;
		
		$res = $wpdb->update(
			$table_name,
			array(
				'redirect_url' => sanitize_text_field( $_POST['redirect_url'] )
			),
			array(
				'id' => sanitize_text_field( $_POST['id'] )
			),
			array(
				'%s'
			)
		);
		
		if( $res ){
			echo json_encode( array( 'result' => 'success' ) );
		}else{
			echo json_encode( array( 'result' => 'error' ) );
		}
		
	 
	die();
}



add_action('wp_ajax_delete_redirect', 'wtf_delete_redirect');
add_action('wp_ajax_nopriv_delete_redirect', 'wtf_delete_redirect');

function wtf_delete_redirect(){
	global $current_user, $wpdb;
	 	
		$table_name = 'wtf_404_log';
		$table_name =  $wpdb->prefix.$table_name;
		
		$res = $wpdb->delete(
			$table_name,
			array(
				'id' => (int)$_POST['id']
			) 
			
		);
	 
		if( $res ){
			echo json_encode( array( 'result' => 'success' ) );
		}else{
			echo json_encode( array( 'result' => 'error' ) );
		}
		
	 
	die();
}

 add_action('wp_ajax_archive_redirect', 'wtf_archive_redirect');
add_action('wp_ajax_nopriv_archive_redirect', 'wtf_archive_redirect');

function wtf_archive_redirect(){
	global $current_user, $wpdb;
	 	
		$table_name = 'wtf_404_log';
		$table_name =  $wpdb->prefix.$table_name;
		
		$res = $wpdb->update(
			$table_name,
			array(
				'is_archive' => "1"
			),
			array(
				'id' => sanitize_text_field( $_POST['id'] )
			),
			array(
				'%d'
			)
		);
	 
		if( $res ){
			echo json_encode( array( 'result' => 'success' ) );
		}else{
			echo json_encode( array( 'result' => 'error' ) );
		}
		
	 
	die();
}

?>