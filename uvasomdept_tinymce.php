<?php 
class UVASOMFacultyShortcode
{
    function __construct() {
    add_action( 'admin_init', array( $this, 'action_admin_init' ) );
}
 
function action_admin_init() {
    // only hook up these filters if we're in the admin panel, and the current user has permission
    // to edit posts and pages
    if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
        add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
        add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
    }
}
 
function filter_mce_button( $buttons ) {
    // add a separation before our button, here our button's id is &quot;uvasomdeptrfd_button&quot;
    array_push( $buttons, '|', 'uvasomdeptrfd_button' );
    return $buttons;
}
 
function filter_mce_plugin( $plugins ) {
    // this plugin file will work the magic of our button
    $plugins['uvasomdeptrfd'] = plugin_dir_url( __FILE__ ) . 'uvasomdeptrfd-plugin.js';
    return $plugins;
}
 
}
 
$uvasomdeptrfd = new UVASOMFacultyShortcode();


?>