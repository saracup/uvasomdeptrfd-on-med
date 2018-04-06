<?php
/*
Plugin Name: UVA SOM Research Faculty Directory (for departments on MED.Virginia.edu)
Plugin URI: http://technology.med.virginia.edu/digitalcommunications
Description: For sites on med.virginia.edu - and NOT sub directories.  This aggregates research faculty data from the main faculty directory for use on department websites.
Version: 0.2.1
Author: Ray Nedzel, Cathy Finn-Derecki
Author URI: http://med.virginia.edu
Copyright 2015  Ray Nedzel  (email : ran2n@virginia.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//include widget
//require_once(dirname( __FILE__ ). '/uvasomdeptrfd_search_widget.php');
require_once( trailingslashit( get_template_directory() ) . 'lib/classes/class-genesis-admin.php');
require_once( trailingslashit( get_template_directory() ) . 'lib/classes/class-genesis-admin-boxes.php');
require_once(dirname( __FILE__ ). '/uvasomdeptrfd_settings_page.php');
require_once(dirname( __FILE__ ). '/uvasomdeptrfd_faculty_printlist.php');
//require_once(dirname( __FILE__ ). '/uvasomdept_tinymce.php');
/**************************************************************************************************/
//REGISTER THE FACULTY LISTING CONTENT TYPE FOR RETRIEVAL FROM FACULTY DIRECTORY//////////////////////////////
/**************************************************************************************************/
// hook into the init action and call uvasomdeptrfd_faclisting when it fires
add_action( 'init', 'uvasomdeptrfd_faclisting', 0 );

// create taxonomies for the post type "faculty-listing"
function uvasomdeptrfd_faclisting() {
// Primary
	$labels = array(
		'name'              => _x( 'Primary', 'taxonomy general name' ),
		'singular_name'     => _x( 'Primary', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Primary Department' ),
		'all_items'         => __( 'All Departments' )
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'primary' ),
	);

register_taxonomy( 'primary', array( 'faculty-listing' ), $args );
//Training
$labels = array(
		'name'              => _x( 'Training', 'taxonomy general name' ),
		'singular_name'     => _x( 'Training', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Training Faculty' ),
		'all_items'         => __( 'All Training Grants' )
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'training-grant' ),
	);
register_taxonomy( 'training-grant', array( 'faculty-listing' ), $args );
//Joint Affiliation
$labels = array(
		'name'              => _x( 'Joint Affiliation', 'taxonomy general name' ),
		'singular_name'     => _x( 'Joint Affiliation', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Joint Affiliation Faculty' ),
		'all_items'         => __( 'All Joint Affiliations' )
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'otheraff' ),
	);

register_taxonomy( 'otheraff', array( 'faculty-listing' ), $args );
//Research Discipline
$labels = array(
		'name'              => _x( 'Research Discipline', 'taxonomy general name' ),
		'singular_name'     => _x( 'Research Discipline', 'taxonomy singular name' ),
		'search_items'      => __( 'Faculty Research Areas' ),
		'all_items'         => __( 'All Research Areas' )
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'research-discipline' ),
	);

register_taxonomy( 'research-discipline', array( 'faculty-listing' ), $args );

}
/*********add css for plugin **************/
function uvasomdeptrfd_styles() {
	wp_enqueue_style( 'uvasomdeptrfd', plugins_url(). '/uvasomdeptrfd/uvasomdeptrfd.css');
}
add_action('wp_enqueue_scripts', 'uvasomdeptrfd_styles');
//********Function to redirect to faculty search results template***********//
add_action("template_redirect", 'uvasomdeptrfd_listing_redirect');
function uvasomdeptrfd_listing_redirect() {
	global $post;
	$plugindir = dirname( __FILE__ );
	$archivetemplate = 'uvasomdeptrfd_archive.php';
	$singletemplate = 'uvasomdeptrfd_listing_single.php';
	if (strpos($_SERVER["REQUEST_URI"], '?primary')||strpos($_SERVER["REQUEST_URI"], '?training-grant')||strpos($_SERVER["REQUEST_URI"], '?otheraff'))
		{
			include($plugindir . '/' . $archivetemplate);
		}
	if (strpos($_SERVER["REQUEST_URI"], '?id='))
		{
			include($plugindir . '/' . $singletemplate);
		}

}
/*********shortcodes **********/
/***********automatic faculty listings based on primary department, research discipline, or training grant participation***********/
add_shortcode('uvasomfaculty', 'uvasomdeptrfd_do_loop');
function uvasomdeptrfd_do_loop( $atts ) {
	extract( shortcode_atts( array(
		'listing' => 'primary',
		'name' => '',
		'exclude'=> '',
	), $atts ) );
	global $post;
	$excludeID = array(esc_attr($exclude));
	//$excludeFac = var_dump($excludeID[0]);
	$excludeFac = implode(',',$excludeID);
	if ((esc_attr($listing) == '')||(esc_attr($listing) == 'primary')||(esc_attr($listing) == 'training-grant')||(esc_attr($listing) == 'research-discipline')){
	// arguments, adjust as needed
	$args = array(
		'post_type'      => 'faculty-listing',
		'posts_per_page' => 100,
		'tax_query' => array(
				array(
				'taxonomy' => esc_attr($listing),
				'field' => 'slug',
				'terms' => esc_attr($name),
				)
		),
		'meta_query' => 	array(
			array (
			'key'     => 'wpcf-user-name',
			'value'   => $excludeFac,
			'compare' => 'NOT IN',
				),
			),
		'post_status'    => 'publish',
		'orderby'	=> 'title',
		'order'	=> 'ASC',
		'paged'          => get_query_var( 'paged' )
	);
	// Do something
	}
	/*query baby!*/
	global $wp_query;
	global $switched;
	switch_to_blog( 45 );
	$wp_query = new WP_Query( $args );
	uvasomdeptrfd_faculty_printlist();
	wp_reset_query();
	restore_current_blog();


}
/*****manually create faculty listings that link to bios **********/
function uvasom_faculty_bio($atts)
{
	extract( shortcode_atts( array(
		'uvauserid' => '',
	), $atts ) );
	global $post;
	$args = array(
		'post_type'      => 'faculty-listing',
		'post_status'    => 'publish',
		'meta_key'	=> 'wpcf-user-name',
		'meta_value' => $uvauserid
	);

    $result = '';
    switch_to_blog( 45 );
	$query = new WP_Query( $args );
    // The Loop
    while ( $query->have_posts() ) : $query->the_post();
		$post_id = get_the_ID();
		$thumb_id = get_post_thumbnail_id($post_id);
        $result.= '<div class="facultylist">'."\n";
		//$result = '<a href="?id='.get_the_ID().'">'.get_the_title().'</a>';
		if(!empty($thumb_id)){
				$thumb_raw_url = wp_get_attachment_image_src($thumb_id,'thumbnail', true);
				$thumb_url = site_url().'/wp-content/uploads/sites/45/'.substr(strstr($thumb_raw_url[0], '/uploads/'),18);
				$result .= '<img src="'.$thumb_url.'" alt="'.get_post_meta($post_id, 'wpcf-first-name', true).' ';
				$result .= get_post_meta($post_id,'wpcf-last-name',true ).'" class="attachment-thumbnail wp-post-image" />';
				}
			else {
			  $result .= '<img class="nofacimage" src="/sharedassets/images/blankavatar.jpg" alt="No Photo Available"/>';
				}
			$result .= '</a>'."\n";
		$result .= '<h2><a href="?id='.get_the_ID().'">'.get_the_title().'</a></h2>'."\n";
				$restitle = get_post_meta($post_id,'wpcf-research-interest-title',true );
				if (!empty($restitle)) {
					$result .= '<p>'.$restitle.'</p>'."\n";
				}
        $result.= '</div>';
    endwhile;

    // Reset Post Data
	wp_reset_query();
    wp_reset_postdata();
	restore_current_blog();
    //return the result
    return $result;
}
function uvasom_faculty_pubs($atts)
{
	wp_enqueue_script( 'load_pubs', plugins_url(). '/uvasomdeptrfd/loadpubs.js', array('jquery'), '', true );
	wp_enqueue_style( 'uvasomdeptrfd', plugins_url(). '/uvasomdeptrfd/uvasomdeptrfd.css');
	extract( shortcode_atts( array(
		'uvauserid' => '',
	), $atts ) );
	global $post;
	$args = array(
		'post_type'      => 'faculty-listing',
		'post_status'    => 'publish',
		'meta_key'	=> 'wpcf-user-name',
		'meta_value' => $uvauserid
	);

    $facpubs = '';
    switch_to_blog( 45 );
	$query = new WP_Query( $args );
    // The Loop
    while ( $query->have_posts() ) : $query->the_post();
		$post_id = get_the_ID();
		$middle = get_post_meta( $post_id,'wpcf-middle-name',true );
		$facpubs .= '<h4 class="publications faculty" id="'.get_post_meta($post_id,'wpcf-curv_id',true );
		$facpubs .=	'">Selected Publications by '.get_post_meta($post_id, 'wpcf-first-name', true);
		if (!empty($middle)) {
			$facpubs .= ' '.$middle;
		}
		$facpubs .= ' '.get_post_meta($post_id,'wpcf-last-name',true ).'</h4>'."\n";
		$facpubs .= '<div class="publications_container" id="'.get_post_meta(get_the_ID(),'wpcf-curv_id',true ).'"  style="background-image:url(/sharedassets/images/ajax-loader_large.gif);background-repeat:no-repeat;background-position:center 30px;overflow:hidden;min-height:150px;">'."\n";
		$facpubs .= ' <div class="publications" id="publications-'.get_post_meta($post_id,'wpcf-curv_id',true ).'">'."\n";
		$facpubs .= ' </div>'."\n".'</div>'."\n";
    endwhile;

    // Reset Post Data
	wp_reset_query();
    wp_reset_postdata();
	restore_current_blog();
    //return the resulting list of publications
    return $facpubs;
}
function register_shortcodes(){
  add_shortcode( 'uvasomfacbio', 'uvasom_faculty_bio' );
  add_shortcode('uvasomfacpubs','uvasom_faculty_pubs');
  add_shortcode('uvasomfaculty', 'uvasomdeptrfd_do_loop');
}

add_action( 'init', 'register_shortcodes');
?>
