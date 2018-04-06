<?php

/**
 *
 * This file registers all of this plugin's 
 * specific Theme Settings, accessible from
 * Genesis > Site Contact Info.
 *
 * @package      WPS_Starter_Genesis_Child
 * @author       Travis Smith <travis@wpsmith.net>
 * @copyright    Copyright (c) 2012, Travis Smith
 * @license      <a href="http://opensource.org/licenses/gpl-2.0.php" onclick="javascript:_gaq.push(['_trackEvent','outbound-article','http://opensource.org']);" rel="nofollow">http://opensource.org/licenses/gpl-2.0.php</a> GNU Public License
 * @since        1.0
 * @alter        1.1.2012
 *
 */
 
 
/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the Child Theme Settings page.
 *
 * @package      WPS_Starter_Genesis_Child
 * @subpackage   Admin
 *
 * @since 1.0.0
 */
class UVASOMDEPTRFD_Settings extends Genesis_Admin_Boxes {
	/**
	 * Create an admin menu item and settings page.
	 * 
	 * @since 1.0.0
	 */
	function __construct() {
		
		// Specify a unique page ID. 
		$page_id = 'uvasomdeptrfd';
		
		// Set it as a child to genesis, and define the menu and page titles
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'genesis',
				'page_title'  => 'Faculty Listing Settings',
				'menu_title'  => 'Faculty Listing',
				'capability' => 'manage_options',
			)
		);
		
		// Set up page options. These are optional, so only uncomment if you want to change the defaults
		$page_ops = array(
		//	'screen_icon'       => array( 'custom' => WPS_ADMIN_IMAGES . '/staff_32x32.png' ),
			'screen_icon'       => 'options-general',
		//	'save_button_text'  => 'Save Settings',
		//	'reset_button_text' => 'Reset Settings',
		//	'save_notice_text'  => 'Settings saved.',
		//	'reset_notice_text' => 'Settings reset.',
		);		
		
		// Give it a unique settings field. 
		// You'll access them from genesis_get_option( 'option_name', CHILD_SETTINGS_FIELD );
		$settings_field = 'UVASOMDEPTRFD_SETTINGS_FIELD';
		
		// Set the default values
		$default_settings = array(
			'primary' => '',
			'training-grant' => '',
			'research-discipline' => ''
		);
		
		// Create the Admin Page
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );

		// Initialize the Sanitization Filter
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitization_filters' ) );
			
	}

	/** 
	 * Set up Sanitization Filters
	 *
	 * See /lib/classes/sanitization.php for all available filters.
	 *
	 * @since 1.0.0
	 */	
	function sanitization_filters() {
		genesis_add_option_filter( 'no_html', $this->settings_field, array(
			'primary',
			'training-grant',
			'research-discipline'
		) );
	}
	
	/**
	 * Register metaboxes on Child Theme Settings page
	 *
	 * @since 1.0.0
	 *
	 * @see Child_Theme_Settings::contact_information() Callback for contact information
	 */
	function metaboxes() {
		
		add_meta_box('uvasomdeptrfd-settings', 'Faculty Listing Settings', array( $this, 'uvasomdeptrfd_meta_box' ), $this->pagehook, 'main', 'high');
		
	}
	
	/**
	 * Register contextual help on Child Theme Settings page
	 *
	 * @since 1.0.0
	 *
	 */
	function help( ) {	
		global $my_admin_page;
		$screen = get_current_screen();
		
		if ( $screen->id != $this->pagehook )
			return;
		
		$tab1_help = 
			'<h3>' . __( 'Primary Appointment' , '' ) . '</h3>' .
			'<p>' . __( 'Select the name of your department from the list. This will ensure that the list is searching for anyone with a primary affiliation with your department. This will also be used in the search for joint affiliations with your department (those who are affiliated with other than primary appointments or training grants).' , '' ) . '</p>';
		
		$tab3_help = 
			'<h3>' . __( 'Research Discipline' , '' ) . '</h3>' .
			'<p>' . __( 'Select the area of research you would like to display by default. This will pull in a list of faculty engaged in that research discipline.' , '' ) . '</p>';
			
		$tab2_help = 
			'<h3>' . __( 'Training Grant' , '' ) . '</h3>' .
			'<p>' . __( 'In the Curvita system, there is no way to align a training grant with a primary department. This option allows you to select the training grant associated faculty that have primary appointments elsewhere, but participate in your department\'s training grant.' , '' ) . '</p>'.
		
		$screen->add_help_tab( 
			array(
				'id'	=> $this->pagehook . '-primary',
				'title'	=> __( 'Primary Appointment' , '' ),
				'content'	=> $tab1_help,
			) );
		$screen->add_help_tab( 
			array(
				'id'	=> $this->pagehook . '-research-discipline',
				'title'	=> __( 'Research Discipline' , '' ),
				'content'	=> $tab1_help,
			) );
		$screen->add_help_tab( 
			array(
				'id'	=> $this->pagehook . '-training-grant',
				'title'	=> __( 'Training Grant' , '' ),
				'content'	=> $tab2_help,
			) );
		
		
		// Add Genesis Sidebar
		$screen->set_help_sidebar(
                '<p><strong>' . __( 'For more information:', '' ) . '</strong></p>'.
                '<p><a href="' . __( 'http://www.studiopress.com/support', '' ) . '" target="_blank" title="' . __( 'Support Forums', '' ) . '">' . __( 'Support Forums', '' ) . '</a></p>'.
                '<p><a href="' . __( 'http://www.studiopress.com/tutorials', '' ) . '" target="_blank" title="' . __( 'Genesis Tutorials', '' ) . '">' . __( 'Genesis Tutorials', '' ) . '</a></p>'.
                '<p><a href="' . __( 'http://dev.studiopress.com/', '' ) . '" target="_blank" title="' . __( 'Genesis Developer Docs', '' ) . '">' . __( 'Genesis Developer Docs', '' ) . '</a></p>'
        );
	}
	
	/**
	 * Callback for Contact Information metabox
	 *
	 * @since 1.0.0
	 *
	 * @see Child_Theme_Settings::metaboxes()
	 */
	function uvasomdeptrfd_meta_box() {
		
//Display the form
//Faculty listing Selection
?>
    <!--<p><strong>Select Page for Faculty Listing:</strong><br />
	<?php /*$args = array(
    'echo'             => 1,
    'selected' => genesis_get_option( 'listingpage', 'UVASOMDEPTRFD_SETTINGS_FIELD'),
    'name'  => 'UVASOMDEPTRFD_SETTINGS_FIELD[listingpage]');
     wp_dropdown_pages($args);*/
//Primary Department Selection
	?>
	</p>-->
	<p><strong>Primary Department:</strong><br />
    <?php custom_taxonomy_dropdown( 'primary','Primary Department' ); ?>
	</p>
<?php 
//Researh Discipline Selection 
	?>
	<p><strong>Research Discipline:</strong><br />
    <?php custom_taxonomy_dropdown( 'research-discipline','Research Discipline' ); ?>
	</p>
<?php 
//Training Grant Selection 
	?>
    <p><strong>Training Grant:</strong><br />
	<?php custom_taxonomy_dropdown( 'training-grant','Training Grant' ); ?>
	</p>
    
<?php
	}
}
//function to search the main faculty listing site for the data to populate the pull-down menus
function custom_taxonomy_dropdown( $taxonomy, $title ) {
	//switch to the main faculty blog before running the query
	global $switched;
	switch_to_blog( 45 );
	$terms = get_terms( $taxonomy );
	if ( $terms ) {
		printf( '<select name="UVASOMDEPTRFD_SETTINGS_FIELD[%s]">', esc_attr( $taxonomy ) );
		$value = genesis_get_option( $taxonomy, 'UVASOMDEPTRFD_SETTINGS_FIELD');
		if ($value ===''){
		echo '<option value="" selected="selected">Select '.$title.'</option>';
		}
		if ($value >''){
		echo '<option value="">Select '.$title.'</option>';
		}
		foreach ( $terms as $term ) {
				if ($value=== ($term->slug )):$selected = ' selected="selected"'; 
				else:$selected = '';
				endif;
			printf( '<option value="%s"'.$selected.'>%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
		print( '</select>');
	//return to the current blog
	restore_current_blog();
	}
}
add_action( 'genesis_admin_menu', 'uvasomdeptrfd_settings_menu' );
/**
 * Instantiate the class to create the menu.
 *
 * @since 1.8.0
 */
function uvasomdeptrfd_settings_menu() {

	new UVASOMDEPTRFD_Settings;

}
