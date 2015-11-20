<?php
/**
 * NanoSupport Settings SubMenu Page Callback function
 *
 * @package NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nanosupport_settings_options_init(){

	/**
     * Tab: Basic Settings
     * 	- Bootstrap?
     * 	- Support Desk Page
     * 	- Submit Ticket Page
     * ----------------------------------
     */
    add_settings_section(
        'ns_general',									// ID/Slug*
        __( 'General Settings', 'nanosupport' ),		// Name*
        'ns_general_settings_section_callback',			// Callback*
        'nanosupport_settings'							// Page/Tab where to add the section of options*
    );
    register_setting(
        'nanosupport_settings',							// Option group*
        'nanosupport_settings',							// Option Name (db)*
        'ns_general_settings_validate'					// Sanitize Callback Function
    );
        add_settings_field(
            'support_desk_page',						// ID*
            __( 'Support Desk Page', 'nanosupport' ),	// Title*
            'ns_support_desk_field',					// Callback Function*
            'nanosupport_settings',						// Page (Plugin)*
            'ns_general'								// Section
        );
        add_settings_field(
            'submit_ticket_page',						// ID*
            __( 'Ticket Submission Page', 'nanosupport' ),  // Title*
            'ns_submit_ticket_field',					// Callback Function*
            'nanosupport_settings',						// Page (Plugin)*
            'ns_general'								// Section
        );
        add_settings_field(
            'bootstrap',								// ID*
            __( 'Load Bootstrap CSS?', 'nanosupport' ),	// Title*
            'ns_bootstrap_field',						// Callback Function*
            'nanosupport_settings',						// Page (Plugin)*
            'ns_general'								// Section
        );

    
    /**
     * Tab: Email Settings
     * 	- Enable email notifications
     * ----------------------------------
     */
    add_settings_section(
        'nanosupport_email',							// ID/Slug*
        __( 'Email Settings', 'nanosupport' ),			// Name*
        'ns_email_settings_section_callback',			// Callback*
        'nanosupport_email_settings'					// Page/Tab where to add the section of options*
    );
    register_setting(
        'nanosupport_email_settings',					// Option group*
        'nanosupport_email_settings',					// Option Name*
        'ns_email_settings_validate'                    // Sanitize Callback Function
    );
	    add_settings_field(
	        'email',									// ID*
	        __( 'Email', 'nanosupport' ),				// Title*
	        'ns_email_field',							// Callback Function*
	        'nanosupport_email_settings',				// Page (Plugin)*
	        'nanosupport_email'							// Section
	    );


    /**
     * Tab: Knowledgebase Settings
     *  - Enable email notifications
     * ----------------------------------
     */
    add_settings_section(
        'nanosupport_knowledgebase',                            // ID/Slug*
        __( 'Knowledgebase Settings', 'nanosupport' ),          // Name*
        'ns_knowledgebase_settings_section_callback',           // Callback*
        'nanosupport_knowledgebase_settings'                    // Page/Tab where to add the section of options*
    );
    register_setting(
        'nanosupport_knowledgebase_settings',                   // Option group*
        'nanosupport_knowledgebase_settings',                   // Option Name*
        'ns_knowledgebase_settings_validate'                    // Sanitize Callback Function
    );
        add_settings_field(
            'knowledgebase',                                    // ID*
            __( 'Knowledgebase', 'nanosupport' ),               // Title*
            'ns_doc_terms_field',                           // Callback Function*
            'nanosupport_knowledgebase_settings',               // Page (Plugin)*
            'nanosupport_knowledgebase'                         // Section
        );
}
add_action( 'admin_init', 'nanosupport_settings_options_init' );


//General Settings Fields
require_once 'ns-settings-general-fields.php';
//Email Settings Fields
require_once 'ns-settings-emails-fields.php';
//Knowledgebase Settings Fields
require_once 'ns-settings-knowledgebase-fields.php';


/**
 * THE SETTINGS PAGE
 * Showing the complete Settings page.
 */
function nanosupport_settings_page_callback() {
	global $plugin_page; ?>

    <div class="wrap">
        <h1><span class="ns-icon-nanosupport"></span> <?php _e( 'NanoSupport Settings', 'nanosupport' ); ?></h1>

        <?php
        //tabs
        $tabs = array(
            'general_settings'          => __( 'General', 'nanosupport' ),
            'email_settings'            => __( 'Emails', 'nanosupport' ),
            'knowledgebase_settings'	=> __( 'Knowledgebase', 'nanosupport' ),
		);
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';

    	foreach( $tabs as $tab => $name ) :
			$active_class = $active_tab == $tab ? 'nav-tab-active' : '';
			$tab_links[] = '<a class="nav-tab '. esc_attr($active_class) .'" href="?page='. $plugin_page .'&tab='. $tab .'">'. $name .'</a>';
		endforeach;
		?>
		
        <h2 class="nav-tab-wrapper">
        	<?php
			foreach ( $tab_links as $tab_link )
				echo $tab_link;
			?> 
        </h2>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">

        	<?php if( 'general_settings' === $active_tab ) { ?>

        		<div class="nanosupport-left-column">

					<?php settings_fields('nanosupport_settings'); ?>
					<?php do_settings_sections('nanosupport_settings'); ?>

				</div> <!-- /.nanosupport-left-column -->
				<div class="nanosupport-right-column">

                    <?php printf( __( '<strong>NanoSupport</strong> is a complete package for a front-end Support Ticketing System in a complete WordPress\' way. It has a rich back end for ticket maintenance and management.<hr><a href="%s"><strong>nano</strong>designs</a>', 'nanosupport' ), 'http://nanodesignsbd.com/' ); ?>

                </div> <!-- /.nanosupport-right-column -->
                <div class="clearfix"></div>

			<?php } else if( 'email_settings' === $active_tab ) { ?>

				<div class="nanosupport-left-column">

					<?php settings_fields('nanosupport_email_settings'); ?>
					<?php do_settings_sections('nanosupport_email_settings'); ?>

				</div> <!-- /.nanosupport-left-column -->
				<div class="nanosupport-right-column">
					
					<?php printf( __( '<strong>NanoSupport</strong> is a complete package for a front-end Support Ticketing System in a complete WordPress\' way. It has a rich back end for ticket maintenance and management.<hr><a href="%s"><strong>nano</strong>designs</a>', 'nanosupport' ), 'http://nanodesignsbd.com/' ); ?>

				</div> <!-- /.nanosupport-right-column -->
                <div class="clearfix"></div>

            <?php } else if( 'knowledgebase_settings' === $active_tab ) { ?>

                <div class="nanosupport-left-column">

                    <?php settings_fields('nanosupport_knowledgebase_settings'); ?>
                    <?php do_settings_sections('nanosupport_knowledgebase_settings'); ?>

                </div> <!-- /.nanosupport-left-column -->
                <div class="nanosupport-right-column">
                    
                    <?php printf( __( '<strong>NanoSupport</strong> is a complete package for a front-end Support Ticketing System in a complete WordPress\' way. It has a rich back end for ticket maintenance and management.<hr><a href="%s"><strong>nano</strong>designs</a>', 'nanosupport' ), 'http://nanodesignsbd.com/' ); ?>

                </div> <!-- /.nanosupport-right-column -->
                <div class="clearfix"></div>

			<?php } //endif ?>

			<?php submit_button(); ?>

        </form>

    </div> <!-- /.wrap -->
<?php
}