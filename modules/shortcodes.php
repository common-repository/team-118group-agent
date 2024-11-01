<?php  
 
add_shortcode( 'team118agent', 'wtf_team118agent_link' );
function wtf_team118agent_link( $atts, $content = null ){
	 
	if( isset($atts['url']) ){
		$url = sanitize_url( $atts['url'] );
		$anchortext = sanitize_text_field( $atts['anchortext'] );
		if( is_front_page()   ){
			$out .= '
				<a href="'.$url.'" target="_blank" class="team118Link">'.$anchortext.'</a>
			';
		}
		return $out;
	}
	
	if( $atts['task'] == 'copyright' ){
		$copyname = sanitize_text_field( $atts['copyname'] );
		$copyright_text = str_replace('{{copy}}', '&copy;', $copyname);
		$copyright_text = str_replace('{{year}}', date('Y'), $copyright_text);

		if($copyname !== $copyright_text)
			return '<span class="team-118-agent-copyright">' . $copyright_text . '<span>';
		else
			return '<span class="team-118-agent-copyright">&copy; '.$copyname.' '.date('Y').'<span>';
	}
	 

		
}

 