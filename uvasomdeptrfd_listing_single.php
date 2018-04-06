<?php
/**
Template for single faculty lising. Requires Genesis Framework.
 */
//keep these lines for debugging
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
//////////////////////////////////////////////////////////////////////////////////
//*******************************PREPARE HEADER/SCRIPTS**************************
//////////////////////////////////////////////////////////////////////////////////

/*********add jquery for publications load **************/
function uvasomdeptrfd_pubs() {
	wp_enqueue_script( 'load_pubs', plugins_url(). '/uvasomdeptrfd/loadpubs.js', array('jquery'), '', true );
	wp_enqueue_style( 'uvasomdeptrfd', plugins_url(). '/uvasomdeptrfd/uvasomdeptrfd.css');
}    
add_action('wp_enqueue_scripts', 'uvasomdeptrfd_pubs');
// Add specific CSS class by filter
add_filter('body_class','uvasomdeptrfd_class');
function uvasomdeptrfd_class($classes) {
	// add 'class-name' to the $classes array
	$classes[] = 'single-faculty-listing';
	// return the $classes array
	return $classes;
}
/*********Make it force a sidebar content layout.**************/
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_sidebar_content');

//////////////////////////////////////////////////////////////////////////////////
//********************CUSTOM TITLE************************************************
//////////////////////////////////////////////////////////////////////////////////
add_filter('genesis_post_title_output', 'uvasomdeptrfd_alter_post_title');
function uvasomdeptrfd_alter_post_title( $title ) {
		$post_id = $_GET['id'];
    	return sprintf( '<h1 class="entry-title">'.get_the_title($post_id).'</h1>');
}
remove_action( 'genesis_post_title','genesis_do_post_title' );
remove_action('genesis_after_header', 'uvasom_do_post_title');
add_action('genesis_after_header', 'uvasomdeptrfd_do_post_title');

/*add_filter('genesis_page_crumb','uvasomdeptrfd_page_crumb');
function uvasomdeptrfd_page_crumb(){
$crumbs[] = get_permalink( $post->ID );
}*/
function uvasomdeptrfd_do_post_title()
{
	echo '<div class="clearfix"></div>';
	echo '<div id="uvasom_page_title">';
	genesis_do_breadcrumbs();
	
	//switch to the faculty directory site to get the title and the remainder of the post
	global $switched;
	switch_to_blog( 45 );
	genesis_do_post_title();
	echo '</div>';
}

//////////////////////////////////////////////////////////////////////////////////
//********************CUSTOM CONTENT LAYOUT***************************************
//////////////////////////////////////////////////////////////////////////////////
//REMOVE POST INFO AND META DISPLAY
remove_action( 'genesis_before_post_content', 'genesis_post_info' );
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
//REMOVE STANDARD POST CONTENT
remove_action( 'genesis_post_content', 'genesis_do_post_content' );
//REMOVE THE ARCHIVE LOOP
remove_action( 'genesis_loop', 'genesis_do_loop' );
remove_action( 'genesis_loop', 'uvasomdeptrfd_do_loop' );
//ADD FACULTY LISTING POST TYPE CONTENT
add_action( 'genesis_loop', 'uvasomrfd_single_faclisting' );
/** Custom loop to get post from the main faculty directory site**/
function uvasomrfd_single_faclisting() {
	global $post;
	//Get the original post ID from the url parameter
	$post_id = $_GET['id'];
	$post_object = get_post( $post_id );
	$post_object->post_content;
	//run the query within the faculty directory site database
	$middle = get_post_meta( $post_id,'wpcf-middle-name',true );
	$degrees = get_post_meta($post_id,'wpcf-degrees-earned',true );
	$rank = get_post_meta($post_id,'wpcf-rank',true );
	$primary  = get_the_terms($post_id, 'primary' );
	$restitle = get_post_meta($post_id,'wpcf-research-interest-title',true );
	$address1 = get_post_meta($post_id,'wpcf-address1',true );
	$address2 = get_post_meta($post_id,'wpcf-address2',true );
	$city = get_post_meta($post_id,'wpcf-city',true );
	$state = get_post_meta($post_id,'wpcf-state',true );
	$zip = get_post_meta($post_id,'wpcf-zip',true );
	$tel = get_post_meta($post_id,'wpcf-campus-phone',true );
	$fax = get_post_meta($post_id,'wpcf-fax',true );
	$personalurl = get_post_meta($post_id,'wpcf-personal-website-url',true );
	$resdescription = $post_object->post_content;
	//begin the facultylisting variable
	$facultylisting = '';
	//structure the link to the post thumbnail
	$thumb_id = get_post_thumbnail_id($post_id);
	if(!empty($thumb_id)){
		$thumb_raw_url = wp_get_attachment_image_src($thumb_id,'thumbnail', true);
		$thumb_url = site_url().'/wp-content/uploads/sites/45/'.substr(strstr($thumb_raw_url[0], '/uploads/'),18);
		$facultylisting .= '<img src="'.$thumb_url.'" alt="'.get_post_meta($post_id, 'wpcf-first-name', true).' '.get_post_meta($post_id,'wpcf-last-name',true ).'" class="attachment-thumbnail wp-post-image" />';
		}
	$facultylisting .= '<h2>'.get_post_meta($post_id, 'wpcf-first-name', true);
	if (!empty($middle)) {
		$facultylisting .= ' '.$middle;
	}
	$facultylisting .= ' '.get_post_meta($post_id,'wpcf-last-name',true ).'</h2>'."\n";
	$facultylisting .= '<h4 class="faculty">Primary Appointment</h4>'."\n".'<p>';
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
			$facultylisting .= $primary_links[0].'</p>'."\n";
			}

	if (!empty($degrees)) {
		$facultylisting .= '<h4 class="faculty">Education</h4>'."\n";
		$facultylisting .= '<ul>'."\n";
		$facultylisting .= html_entity_decode(get_post_meta($post_id,'wpcf-degrees-earned',true ))."\n";
		$facultylisting .= '</ul>'."\n";
	}
	$facultylisting .= '<h4 class="faculty">Contact Information</h4>'."\n".'<p>'."\n";
		if (!empty($address1)) {
			$facultylisting .= $address1;
			if (!empty($address2)) {
			$facultylisting .= '<br />'."\n".$address2;
			}
			if (!empty($city)) {
			$facultylisting .= '<br />'."\n".$city.', ';
			}
			if (!empty($state)) {
			$facultylisting .= $state;
			}
			if (!empty($zip)) {
			$facultylisting .= ' '.$zip."\n";
			}
		}
		if (!empty($tel)) {
			$facultylisting .= '<br />'."\n".'<strong>Telephone: </strong><a href="tel:'.$tel.'">'.$tel.'</a>'."\n";
		}
		if (!empty($fax)) {
			$facultylisting .= '<br />'."\n".'<strong>Fax: </strong>'.$fax."\n";
		}
	$facultylisting .= '<br />'."\n".'<strong>Email: </strong><a href="mailto:'.get_post_meta($post_id,'wpcf-email',true ).'">'.get_post_meta($post_id,'wpcf-email',true ).'</a>'."\n";
	   if (!empty($personalurl)) {
			$facultylisting .= '<br />'."\n".'<strong>Website: </strong><a href="'.$personalurl.'">'.$personalurl.'</a>'."\n";
		}
			$facultylisting .= '</p>';
		if (!empty($restitle)) {
			$facultylisting .= '<h4 class="faculty">Research Interests</h4>'."\n";
			$facultylisting .= '<p>'.$restitle.'</p>'."\n";
		}
		if (!empty($resdescription)) {
			$facultylisting .= '<h4 class="faculty">Research Description</h4>'."\n";
			$facultylisting .= '<div class="researchdesc">'.$resdescription.'</div>'."\n";
		}
	$facultylisting .= '<h4 class="publications faculty" id="'.get_post_meta($post_id,'wpcf-curv_id',true ).'">Selected Publications</h4>'."\n";
	$facultylisting .= '<div class="publications_container" id="'.get_post_meta($post_id,'wpcf-curv_id',true ).'"  style="background-image:url(/sharedassets/images/ajax-loader_large.gif);background-repeat:no-repeat;background-position:center 30px;overflow:hidden;min-height:150px;">'."\n";
	$facultylisting .= ' <div class="publications" id="publications-'.get_post_meta($post_id,'wpcf-curv_id',true ).'">'."\n";
	$facultylisting .= ' </div>'."\n".'</div>'."\n";

	echo '<div class="entry-content">'."\n";
	echo $facultylisting;
	echo '</div>'."\n";
	//go back to the current blog for the remainder of the page
	restore_current_blog();

}

?>
