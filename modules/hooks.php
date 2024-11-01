<?php 
add_Action('init', 'wtf_init');
function wtf_init(){
	global $wpdb;
	
	$table_name = 'wtf_404_log';
	$table_name =  $wpdb->prefix.$table_name;	
	
	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '$table_name' AND column_name = 'is_archive'"  );

	if(empty($row)){
	   $wpdb->query("ALTER TABLE $table_name ADD is_archive mediumint(1) NOT NULL");
	}
}
 
	
// gravity form entries listing
add_action('rest_api_init', function () {
  register_rest_route( 'gfdata/v1', 'date/(?P<date>[a-zA-Z0-9-]+)',array(
                'methods'  => 'GET',
                'callback' => 'wtgaf_get_entries'
      ));
});

function wtgaf_get_entries($request) {
	global $wpdb;
 
    $date = sanitize_text_field( str_replace('-', '/', $request['date'] ) );	
 
	$timestamp = strtotime( $date );
 
	$formated_date = date('Y-m-d', $timestamp );
	
	$all_entries = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}gf_entry WHERE date_created > %s ", $formated_date) );
 
	if( count($all_entries) > 0 )
	foreach( $all_entries as $single_item ){
 
		$gf_entry_meta = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id = %d ", $single_item->id ) );
	 
		if( count($gf_entry_meta) > 0 )
		foreach( $gf_entry_meta as $single_form ){
			$fields = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}gf_form_meta WHERE form_id = %d ", $single_form->form_id ) );
	 
			$all_data = json_decode( $fields->display_meta );
			
			$fields = $all_data->fields;
		}
		
		$out_fields = array();
		if( count($gf_entry_meta) > 0 ){
			foreach( $gf_entry_meta as  $single_entry ){

				foreach( $fields as $single_field ){
					if( $single_field->id == $single_entry->meta_key ){
						$out_fields[] = array( 'meta_key' => $single_field->id, 'label' => $single_field->label, 'value' => $single_entry->meta_value );
					}
				}
				
				
			
			}
		}
	 
	 
		// get form title
		$form_title = $wpdb->get_row( $wpdb->prepare("SELECT title, is_active, is_trash FROM {$wpdb->prefix}gf_form WHERE id= %d", $single_item->form_id) );
	 
		$out_entry[] = array( 
		'submission_id' => $single_item->id,
		'form_id' => $single_item->form_id,
		
		'form_title' => $form_title->title,
		'form_is_active' => $form_title->is_active,
		'form_is_trash' => $form_title->is_trash,
		'date_created' => $single_item->date_created,
		
		'entry_status' => $single_item->status,
		
		'fields' => $out_fields
		);
	}
  
  
	$response = new WP_REST_Response($out_entry);
    $response->set_status(200);

    return $response;
	
}

// Alog plugin log return
add_action('rest_api_init', function () {
  register_rest_route( 'alog/v1', 'date/(?P<date>[a-zA-Z0-9-]+)',array(
                'methods'  => 'GET',
                'callback' => 'wtgaf_get_alog_info'
      ));
});

function wtgaf_get_alog_info($request) {
	global $wpdb;
 
    $date = sanitize_text_field( str_replace('-', '/', $request['date'] ) );	
 
	$timestamp = strtotime( $date );
 
	$date_start = $timestamp;
	$date_end= $timestamp + ( 24*60*60 ) - 1;
 
 
	$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wsal_occurrences WHERE `created_on` BETWEEN %d AND %d", $date_start, $date_end ) );
 
	if( count( $results ) > 0 ){
		$out_data = array();
		foreach( $results as $s_result ){
			$results_meta = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wsal_metadata WHERE  occurrence_id = %d", $s_result->id ) );
			$s_result->meta = $results_meta;
			$out_data[] = (array)$s_result;
		}
	
		$response = new WP_REST_Response( $out_data );
		$response->set_status(200);

		return $response;
		
	}else{
		$out_data = array(
			'status' => 'error',
			'message' => 'No entried found'
		);
		$response = new WP_REST_Response( $out_data );
		$response->set_status(404);

		return $response;
	}
 
	
 
	#########
 
	 
  
  
	
	
}

// sitemap generation
add_action('rest_api_init', function () {
  register_rest_route( 'sitemap/v1', '/generate',array(
                'methods'  => 'GET',
                'callback' => 'wtgaf_generate_sitemap'
      ));
});

function wtgaf_generate_sitemap($request) {
	global $wpdb;
 
   
	$args = array(
		'showposts' => -1,
		'post_type' => 'any'
	);
	$all_posts = get_posts($args);
	
	$json_sitemap = array();
	
	if( count( $all_posts ) > 0 ){
		foreach( $all_posts as $single_post ){
			
			$json_sitemap[] = array(
				'url' => get_permalink( $single_post->ID ),
				'post_type' => $single_post->post_type,
				'last_mod' => $single_post->post_modified
			
			);
			
			}
		}
	
   
	$response = new WP_REST_Response( $json_sitemap );
	$response->set_status(200);

	return $response;
	
	
}


// insertin link to plugin
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wtgaf_add_action_links' );
function wtgaf_add_action_links ( $links ) {
    $mylinks = array(
    '<a href="' . admin_url( 'options-general.php?page=myplugin' ) . '">Usage Info</a>',
    );
    return array_merge( $links, $mylinks );
}


// 404 functionality
add_Action('template_redirect', 'wtf_template_redirect');
function wtf_template_redirect(){
	if( is_404() ){
		global $wp, $wpdb;
		
		$table_name = 'wtf_404_log';
		$table_name =  $wpdb->prefix.$table_name;	
			
 
		
		$current_url = home_url(add_query_arg(array(), $wp->request));
		date_default_timezone_set('US/Eastern');
		
		// check if exists
		
		 
		
		$res = $wpdb->get_results($wpdb->prepare( "SELECT * FROM $table_name WHERE request_url = %s", $current_url) );
 
		if( count($res) == 0 ){
		
			// check if table exists
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
 
			 $table_name = 'wtf_404_log';
			 $table_name =  $wpdb->prefix.$table_name;
			 
			 
			 //$wpdb->query("DROP TABLE ".$table_name );

			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
			  `request_url` longtext NOT NULL ,
			  `refferal_url` longtext NOT NULL,
			  `timestamp` longtext NOT NULL,
			  `redirect_url` longtext NOT NULL,
			 
			  UNIQUE KEY `id` (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			dbDelta($sql);
			/*  finsih */
		
			$res = $wpdb->insert(
				$table_name, 
				array( 
					'request_url' => $current_url ,
					'refferal_url' =>   ( $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '' ),
					'timestamp' => time() ,
				 
				), 
				array(  
					'%s', 
					'%s', 
					'%d' 
				) 
			);	
			 
		}
		
		
		
		// redirect
  
		 $redirect_url = $wpdb->get_var( $wpdb->prepare("SELECT redirect_url FROM {$wpdb->prefix}wtf_404_log WHERE request_url = %s", $current_url));
	 
		 if( $redirect_url && $redirect_url != '' ){
			wp_redirect( $redirect_url, 301 );		 
			die();
		 }
		
	}
}

?>