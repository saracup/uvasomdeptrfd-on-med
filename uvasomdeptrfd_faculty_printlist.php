<?php
function uvasomdeptrfd_faculty_printlist() {

		if ( have_posts() ) : 
		echo '<div id="facultylistcontainer" class="">';
		while ( have_posts() ) : the_post(); 
 			$post_id = get_the_ID();
			$thumb_id = get_post_thumbnail_id($post_id);
			echo '<div class="facultylist">'."\n";
			$facultylisting = '<a href="?id='.get_the_ID().'">';
			if(!empty($thumb_id)){
				$thumb_raw_url = wp_get_attachment_image_src($thumb_id,'thumbnail', true);
				$thumb_url = site_url().'/wp-content/uploads/sites/45/'.substr(strstr($thumb_raw_url[0], '/uploads/'),18);
				//////////////////
				$facultylisting .= '<img src="'.$thumb_url.'" alt="'.get_post_meta($post_id, 'wpcf-first-name', true).' ';			
				$facultylisting .= get_post_meta($post_id,'wpcf-last-name',true ).'" class="attachment-thumbnail wp-post-image" />';
				}
			else {
			  $facultylisting .= '<img class="nofacimage" src="/sharedassets/images/blankavatar.jpg" alt="No Photo Available"/>';
				}
			$facultylisting .= '</a>'."\n";
			$facultylisting .= '<h2><a href="?id='.get_the_ID().'">'.get_the_title().'</a></h2>'."\n";
				$restitle = get_post_meta($post_id,'wpcf-research-interest-title',true );
				if (!empty($restitle)) {
					$facultylisting .= '<p>'.$restitle.'</p>'."\n";
				}
			echo $facultylisting;
			echo '</div>'."\n";
	 
		endwhile;
		
		//do_action( 'genesis_after_endwhile' );
		echo '</div>'."\n";
	endif;
}

?>
