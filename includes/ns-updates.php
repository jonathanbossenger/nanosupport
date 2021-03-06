<?php
/**
 * Functions related to NanoSupport updates
 *
 * @author   	nanodesigns
 * @category 	Core
 * @package  	NanoSupport/Core
 * @version  	1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Upgrade NanoSupport
 * 
 * Do ground things, make the environment updated during plugin update.
 *
 * @since  1.0.0
 * -----------------------------------------------------------------------
 */
function ns_update() {

	$ns_existing_version = get_option( 'nanosupport_version' );

    /**
     * v0.2.0
     */
    if ( version_compare( $ns_existing_version, '0.2.0', '<' ) ) {
    	ns_update_v020();
    }

    update_option( 'nanosupport_version', NS()->version );

}

add_action( 'plugins_loaded', 'ns_update' );


/**
 * Version 0.2.0 Update
 * Update the general settings options.
 * Update the knowledgebase settings options.
 * ...
 */
function ns_update_v020() {
	//get the default notice texts
	global $ns_submit_ticket_notice, $ns_support_desk_notice, $ns_knowledgebase_notice;

	$ns_general_settings = get_option( 'nanosupport_settings' );
	if( $ns_general_settings !== false ) {	
		$ns_general_settings['submit_ticket_notice'] = esc_attr(strip_tags($ns_submit_ticket_notice));
		$ns_general_settings['support_desk_notice']  = esc_attr(strip_tags($ns_support_desk_notice));
		$ns_general_settings['knowledgebase_notice'] = esc_attr(strip_tags($ns_knowledgebase_notice));

    	update_option( 'nanosupport_settings', $ns_general_settings );
	}
	
	$ns_knowledgebase_settings = get_option( 'nanosupport_knowledgebase_settings' );
	if( $ns_knowledgebase_settings !== false ) {
		$ns_knowledgebase_settings['isactive_kb'] = absint(1);
    	
    	update_option( 'nanosupport_knowledgebase_settings', $ns_knowledgebase_settings );
	}
}
