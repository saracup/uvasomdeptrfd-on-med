<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
/**
 * This file handles the faculty search results page.
*/
/*********add stylesheet **************/
function uvasomdeptrfd_styles() {
	wp_enqueue_style( 'uvasomdeptrfd', plugins_url(). '/uvasomdeptrfd/uvasomdeptrfd.css');
}    
add_action('wp_enqueue_scripts', 'uvasomdeptrfd_styles');

/*********Make it sidebar content layout.**************/
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_sidebar_content');
/*********Don't display the post meta after each post.**************/
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
/*********Add the search class to the page body for optional theme styling**************/
function uvasomdeptrfdsearch_add_classes( $classes ) {
	$classes[] = 'search';
	$classes[] = 'archive';
	return $classes;
}
add_filter( 'body_class', 'uvasomdeptrfdsearch_add_classes' );
/**************************************************************************************************/
//THESE LAYOUT ADJUSTMENTS ARE  SPECIFIC TO THE UVASOM BIMS THEME ONLY//////////////////////////////
/**************************************************************************************************/
/*********Move the page title from its default location, per the BIMS Theme**************/
if (get_stylesheet() =='uvasom_bims') {
add_action( 'genesis_post_title','genesis_do_post_title' );
add_action( 'genesis_after_header', 'uvasomdeptrfd_do_search_title' );
}
if (get_stylesheet() =='uvasom_news') {
add_action( 'genesis_post_title','genesis_do_post_title' );
//add_action( 'genesis_before_loop', 'uvasomdeptrfd_do_search_title' );
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
}
add_filter( 'genesis_breadcrumb_args', 'uvasomdeptrfd_breadcrumb' );
function uvasomdeptrfd_breadcrumb( $args ) {
  $args['home'] = get_bloginfo('name').' Home';
  return $args;
 
}
/**************************************************************************************************/
/*function uvasomdeptrfd_do_search_title() {
	if (strpos($_SERVER["REQUEST_URI"], '?primary')){$preterm='Primary Faculty';$term = '';}
	if (strpos($_SERVER["REQUEST_URI"], 'research-discipline')){$preterm='Faculty Resarch Area: ';$term = $_GET['research-discipline'];}
	if (strpos($_SERVER["REQUEST_URI"], 'training')){$preterm='Other Training Faculty';$term = '';}
	if (strpos($_SERVER["REQUEST_URI"], 'joint')){$preterm='Joint Appointments';$term = '';}
	$title = sprintf( '<div class="clearfix"></div><div id="uvasom_page_title">'.genesis_do_breadcrumbs().'<h1 class="archive-title">%s %s</h1>', apply_filters( 'genesis_search_title_text', __( $preterm, 'genesis' ) ), $term).'</div>';
	echo apply_filters( 'genesis_search_title_output', $title ) . "\n";
}*/
/**************************************************************************************************/
//CUSTOMIZE THE LOOP TO SEARCH THE FACULTY DIRECTORY SITE//////////////////////////////
/**************************************************************************************************/
function uvasomdeptrfd_do_loop() {
	global $post;
 
	// arguments, adjust as needed
	$args = array(
		'post_type'      => 'faculty-listing',
		'posts_per_page' => 100,
		//'primary' 		=> 'pharmacology',
		'tax_query' => array(
				array(
				'taxonomy' => 'primary',
				'field' => 'slug',
				'terms' => genesis_get_option( 'primary', 'UVASOMDEPTRFD_SETTINGS_FIELD')
				),
				array(
				'taxonomy' => 'training-grant',
				'field' => 'slug',
				'terms' => genesis_get_option( 'training-grant', 'UVASOMDEPTRFD_SETTINGS_FIELD')
				),
				array(
				'taxonomy' => 'otheraff',
				'field' => 'slug',
				'terms' => genesis_get_option( 'otheraff', 'UVASOMDEPTRFD_SETTINGS_FIELD')
				)
		),
		
		'post_status'    => 'publish',
		'orderby'	=> 'title',
		'order'	=> 'ASC',
		'paged'          => get_query_var( 'paged' )
	);
	// Do something
 
	/* 
	Overwrite $wp_query with our new query.
	The only reason we're doing this is so the pagination functions work,
	since they use $wp_query. If pagination wasn't an issue, 
	use: https://gist.github.com/3218106
	*/
	
	global $wp_query;
	global $switched;
	switch_to_blog( 45 );
	$wp_query = new WP_Query( $args );
 	
	if ( have_posts() ) : 
		
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
			echo '</div>';
	 
		endwhile;
		
		//do_action( 'genesis_after_endwhile' );
	endif;
 
	wp_reset_query();
	restore_current_blog();

}
add_action( 'genesis_loop', 'uvasomdeptrfd_do_loop' );
remove_action( 'genesis_loop', 'genesis_do_loop' );
?>
