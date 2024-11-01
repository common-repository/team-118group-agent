<?php 
 
add_action( 'admin_menu', 'my_plugin_menu' );

 
function my_plugin_menu() {
	add_menu_page( __('118 Agent', $locale_taro), __('118 Agent', $locale_taro), 'manage_options', 'wtf_118_main', 'wtf_plugin_general', plugins_url('/images/icon_20x20.png', __FILE__ ) );
	add_submenu_page( 'wtf_118_main', __('General', $locale_taro), __('General', $locale_taro), 'manage_options', 'wtf_118_general', 'wtf_plugin_general' );
	add_submenu_page( 'wtf_118_main', __('404', $locale_taro), __('404', $locale_taro), 'manage_options', 'wtf_118_404', 'wtf_118_404' );
	
	remove_submenu_page('wtf_118_main','wtf_118_main');
}

 
function wtf_plugin_general() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap tw-bs4">';
	echo '<div class="data_output">';
	echo '
	<p><b>To request audit log you can use this API endpoint</b></p>
	<p>'.get_option('home').'/wp-json/alog/v1/date/mm-dd-yyyy</p>
	<p><b>To request Gravity Forms  entries you can use this API endpoint</b></p>
	<p>'.get_option('home').'/wp-json/gfdata/v1/date/mm-dd-yyyy</p>
	<p><b>To request JSON Sitemap of site you can use this API endpoint</b></p>
	<p>'.get_option('home').'/wp-json/sitemap/v1/generate</p>
	
	<p><b>To use the link shortcode use this shortcode:</b></p>
	<p>[team118agent url=\'https://www.118group.com\' anchortext=\'118Group Web Design\']</p>

	<p><b>To display copyright and year use this shortcode.</b></p>
	<p>[team118agent task=\'copyright\' copyname=\'118Group Web Design {{copy}} {{year}}\']</p>
	';
	echo '</div>';
	echo '</div>';
} 

function wtf_118_404() {
	global $wpdb;
	
	$table_name = 'wtf_404_log';
	$table_name =  $wpdb->prefix.$table_name;	
	 
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap tw-bs4 table_container">';
	
	// add custom url2redirect
	if( $_POST['url2redirect'] ){
		$res = $wpdb->insert(
				$table_name, 
				array( 
					'request_url' => sanitize_text_field( $_POST['url2redirect'] ),
					'refferal_url' =>   '',
					'timestamp' => time() ,
				 
				), 
				array(  
					'%s', 
					'%s', 
					'%d' 
				) 
			);
		 
			$msg = '<div class="alert alert-info">URL Added</div>';
	}
	
	
    echo $msg.'
	<form action="'.admin_url('admin.php?page=wtf_118_404').'" method="POST" >
        <table class="table" >
          <tbody>
            
			<tr>
       
              <th scope="col"><input placeholder="Add custom URL to redirect" id="custom_redirect_url" name="url2redirect" class="col-12 form-control" value="" /></th>
              <th scope="col"><button class="btn btn-success" >Add</button>&nbsp;
			   
             
            </tr>
			
 
 
          </tbody>
           
        </table>
	</form>
	<form action="'.admin_url('admin.php?page=wtf_118_404').'" method="POST" >
        <table class="table" >
          <tbody>
            
		 
			
			<tr>
       
              <th scope="col"><input placeholder="Enter part of URL to filter" id="table_filter" name="url_search" class="col-12 form-control" value="'.( $_POST['url_search'] ? sanitize_text_field( $_POST['url_search'] ) : '' ).'" /></th>
              <th scope="col"><button class="btn btn-success" >Go</button>&nbsp;
			  <a href="'.admin_url('admin.php?page=wtf_118_404').'" class="btn btn-info" >Clear</a></th>
             
            </tr>
 
          </tbody>
           
        </table>
	</form>
		<table class="table result_data">
			<thead>
				<tr>
					<td>
						<div class="row">
							<div class="col-6 text-center">
								<input class="form-check-input" type="checkbox" value="on" id="display_archived">
								  <label class="form-check-label" for="defaultCheck1">
									Display Archived URLs
								  </label>
							</div>
							<div class="col-6 text-center">
								<input class="form-check-input" type="checkbox" value="on" id="show_empty_redirects">
							  <label class="form-check-label" for="defaultCheck1">
								Show Empty Redirects
							  </label>
							</div>
						</div>
						
					</td>
		 
				</tr>
			</thead>
		  <tbody>
            ';
			if( $_POST['url_search'] ){
			$search_field = '%'.sanitize_text_field( $_POST['url_search'] ).'%';
				$all_results = $wpdb->get_results("SELECT * FROM $table_name WHERE request_url LIKE '$search_field'  ORDER BY timestamp DESC");
			 
			}else{
				$all_results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");
			}
			
			 
			if( count( $all_results ) > 0 ){
			foreach( $all_results as  $isngle_res ){
				echo ' 
				<style>
				.row_container{
					overflow:hidden;
				}
				.row_container .col_33{
					float:left;
					width:33%;
					margin-bottom:10px;
				}
				.row_container .col_100{
					float:left;
					width:100%;
					margin-bottom:10px;
					    font-size: 12px;
    font-style: italic;
				}
				</style>
				<tr  class="single_row row_'.$isngle_res->id.'  '.( $isngle_res->is_archive == '1' ? ' is_archived ' : '' ).'" >
				  <td scope="col">
					<div class="row_container  alert alert-info">
						<div class="col_100">
							<b>URL:</b>&nbsp;'.$isngle_res->request_url.'
						</div>
						<div class="col_100">
							<b>Refferal:</b>&nbsp;'.( $isngle_res->refferal_url ? $isngle_res->refferal_url : 'N/A' ).'
						</div>
						<div class="col_100">
							<b>Time:</b>&nbsp;'.date( 'm/d/Y H:i:s', $isngle_res->timestamp ).'
						</div>
						<div class="col_100">
							<b>Redirect To:</b>
							<div class="row">
								<div class="col-9"><input value="'.$isngle_res->redirect_url.'" class=" form-control col-12 redirect_url_field redirect_url_'.$isngle_res->id.'"/></div>
								<div class="col-3">
								<button data-id="'.$isngle_res->id.'" class="btn btn-success btn-sm save_redirect">Save</button>&nbsp;
								<button data-id="'.$isngle_res->id.'" class="btn btn-info btn-sm archive_redirect">Archive</button>&nbsp;
								<button data-id="'.$isngle_res->id.'" class="btn btn-danger btn-sm delete_redirect">Delete</button>
								</div>
							</div>
							
							
						</div>
					</div>
			 
				  </td>
				  </tr>';
			}
			}else{
			echo '  
				<tr>
				  <td scope="col">
					No data in base.
				  </td>
				   </tr>';
				
			}
            
            echo '
           
 
          </tbody>
           
        </table>
    ';
    echo '</div>';
} 

?>