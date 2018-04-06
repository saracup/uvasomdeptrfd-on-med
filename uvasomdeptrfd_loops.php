<?php
////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCTION TO RETRIEVE A LISTING OF FACULTY WITHIN A GIVEN TAXONOMY
////////////////////////////////////////////////////////////////////////////////////////////////
function uvasomdeptrfd_do_loop($atts) {
	//set shortcode attributes
	extract(shortcode_atts(array(
      'primary' => genesis_get_option( 'primary', 'UVASOMDEPTRFD_SETTINGS_FIELD'),
   ), $atts));
   //begin loop
	global $post;
	// arguments, adjust as needed
	$args = array(
		'post_type'      => 'faculty-listing',
		'posts_per_page' => 100,
		//'primary' 		=> 'pharmacology',
		'primary' => genesis_get_option( 'primary', 'UVASOMDEPTRFD_SETTINGS_FIELD'),
		'post_status'    => 'publish',
		'orderby'	=> 'title',
		'order'	=> 'ASC',
		'paged'          => get_query_var( 'paged' )
	);
	//begin query	
	global $wp_query;
	//switch to the main faculty directory site for data
	global $switched;
	switch_to_blog( 45 );
	$wp_query = new WP_Query( $args );
 	
	if ( have_posts() ) : 
		
		while ( have_posts() ) : the_post(); 
 			$post_id = get_the_ID();
			$thumb_id = get_post_thumbnail_id($post_id);
			//Each individual listing is contained within a div
			echo '<div class="facultylist">'."\n";
			$facultylisting = '<a href="?id='.get_the_ID().'">';
			//Faculty listing starts off with display of the featured image. If there is no featured image, a generic is displayed.
			if(!empty($thumb_id)){
				$thumb_raw_url = wp_get_attachment_image_src($thumb_id,'thumbnail', true);
				$thumb_url = site_url().'/wp-content/uploads/sites/45/'.substr(strstr($thumb_raw_url[0], '/uploads/'),18);
				$facultylisting .= '<img src="'.$thumb_url.'" alt="'.get_post_meta($post_id, 'wpcf-first-name', true).' ';			
				$facultylisting .= get_post_meta($post_id,'wpcf-last-name',true ).'" class="attachment-thumbnail wp-post-image" />';
				}
			else {
			  $facultylisting .= '<img class="nofacimage" src="/sharedassets/images/blankavatar.jpg" alt="No Photo Available"/>';
				}
			$facultylisting .= '</a>'."\n";
			//Faculty member's full name is displayed
			$facultylisting .= '<h2><a href="?id='.get_the_ID().'">'.get_the_title().'</a></h2>'."\n";
			//List the faculty member's statement about their research area, known as "research interest title" in Curvita
				$restitle = get_post_meta($post_id,'wpcf-research-interest-title',true );
				if (!empty($restitle)) {
					$facultylisting .= '<p>'.$restitle.'</p>'."\n";
				}
			echo $facultylisting;
			echo '</div>';
			//close listing div
	 
		endwhile;
	endif;
 
	wp_reset_query();
	restore_current_blog();

}
add_action( 'genesis_loop', 'uvasomdeptrfd_do_loop' );
remove_action( 'genesis_loop', 'genesis_do_loop' );
////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCTION TO RETRIEVE SINGLE FACULTY LISTING
////////////////////////////////////////////////////////////////////////////////////////////////
function uvasomrfd_single_faclisting() {
	global $post;
	//Get the original post ID from the url parameter
	$post_id = $_GET['id'];
	//run the query within the faculty directory site database
	global $switched;
	switch_to_blog( 45 );
	$middle = get_post_meta( $post_id,'wpcf-middle-name',true );
	$degrees = get_post_meta($post_id,'wpcf-degrees-earned',true );
	$rank = get_post_meta($post_id,'wpcf-rank',true );
	$primary  = get_the_terms($post_id, 'primary' );
	$restitle = get_post_meta($post_id,'wpcf-research-interest-title',true );
	$resdescription = the_content('More...');
	//begin the facultylisting variable
	$facultylisting = '';
	//structure the link to the post thumbnail
	$thumb_id = get_post_thumbnail_id($post_id);
	//if there is no thumbnail, don't display anything
	if(!empty($thumb_id)){
		$thumb_raw_url = wp_get_attachment_image_src($thumb_id,'thumbnail', true);
		$thumb_url = site_url().'/wp-content/uploads/sites/45/'.substr(strstr($thumb_raw_url[0], '/uploads/'),8);
		$facultylisting .= '<img src="'.$thumb_url.'" alt="'.get_post_meta($post_id, 'wpcf-first-name', true).' '.get_post_meta($post_id,'wpcf-last-name',true ).'" class="attachment-thumbnail wp-post-image" />';
		}
	//Display the faculty member's full name
	$facultylisting .= '<h2>'.get_post_meta($post_id, 'wpcf-first-name', true);
	if (!empty($middle)) {
		$facultylisting .= ' '.$middle;
	}
	$facultylisting .= ' '.get_post_meta($post_id,'wpcf-last-name',true ).'</h2>'."\n";
	//Display the faculty member's rank/title and primary appointment
	$facultylisting .= '<h4 class="faculty">Primary Appointment</h4>';
		if (!empty($rank)) {
		$facultylisting .= $rank.', ';
		}
		$terms = get_the_terms( $post_id, 'primary' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$primary_links = array();
			foreach ( $terms as $term ) {
				$primary_links[] = $term->name;
				$primary_slug[] = $term->slug;
			}
			$primary = join( ", ", $primary_links );
			$facultylisting .= '<a href="'.home_url().'/primary/'.$primary_slug[0].'">'.$primary_links[0].'</a>';
			}
	//Display the faculty member's degrees/education 
	if (!empty($degrees)) {
		$facultylisting .= '<h4 class="faculty">Education</h4>'."\n";
		$facultylisting .= '<ul>'."\n";
		$facultylisting .= html_entity_decode(get_post_meta($post_id,'wpcf-degrees-earned',true ))."\n";
		$facultylisting .= '</ul>'."\n";
	}
	//Display the faculty member's email address
	$facultylisting .= '<h4 class="faculty">Contact</h4>'."\n";
	$facultylisting .= '<p>Email: <a href="mailto:'.get_post_meta($post_id,'wpcf-email',true ).'">'.get_post_meta($post_id,'wpcf-email',true ).'</a></p>'."\n";
	//Display the faculty member's brief statement on their research area, if available
		if (!empty($restitle)) {
			$facultylisting .= '<h4 class="faculty">Research Interests</h4>'."\n";
			$facultylisting .= '<p>'.$restitle.'</p>'."\n";
		}
		//Display the full text narrative of the faculty member's research, if available
		if (!empty($resdescription)) {
			$facultylisting .= '<h4 class="faculty">Research Description</h4>'."\n";
			$facultylisting .= '<div class="researchdesc">'.$resdescription.'</div>'."\n";
		}
	//Call up Curvita webservices to display the faculty member's publications via ajax
	$facultylisting .= '<h4 class="publications" id="'.get_post_meta($post_id,'wpcf-curv_id',true ).'">Selected Publications</h4>'."\n";
	$facultylisting .= '<ul class="publications" id="publications-'.get_post_meta($post_id,'wpcf-curv_id',true ).'">'."\n";
	$facultylisting .= '</ul>'."\n";
	echo $facultylisting;
	//Return to this blog with the query is over.
	restore_current_blog();

}
function uvasomdeptrfd_register_shortcodes(){
   add_shortcode('faculty-listing', 'uvasomdeptrfd_do_loop');
}
add_action( 'init', 'uvasomdeptrfd_register_shortcodes');
?>