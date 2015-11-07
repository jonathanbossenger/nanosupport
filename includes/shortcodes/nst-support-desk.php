<?php
/**
 * Shortcode: Support Desk
 *
 * Showing the common ticket center of all the support tickets to the respective privileges.
 * Show all the tickets at the front end using shortcode [nst_support_desk]
 *
 * @author  	nanodesigns
 * @category 	Shortcode
 * @package 	Nano Support Ticket
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nst_support_desk_page() {
	ob_start();

	if( is_user_logged_in() ) :
		//User is Logged in

		global $post, $current_user;

		if( isset($_GET['success']) && $_GET['success'] == 1 ) {
			echo '<div class="alert alert-success" role="alert">';
				_e( "<strong>Success!</strong> Your ticket is submitted successfully! It will be reviewed shortly and replied as early as possible.", 'nano-support-ticket' );
		    echo '</div>';		
		}

		/*if( is_user_logged_in() ) {

			if( isset( $_GET['my-tickets'] ) ) {
				$u_id = (int) $_GET['my-tickets'];
				if( $u_id === $current_user->ID ) {
					//only my tickets
					$author_id		= $current_user->ID;
					$ticket_status 	= array( 'publish', 'private' );					
				} else {
					$ticket_status 	= 'publish';
					$author_id		= $u_id;				
				}
			} else {
				if( current_user_can('administrator') || current_user_can('editor') ) {
					//site admins
					$ticket_status 	= array( 'publish', 'private' );
					$author_id 		= '';
				} else {
					//general logged in users
					$ticket_status 	= 'publish';
					$author_id		= '';	
				}				
			}

		} else {
			//for visitors
			$ticket_status 		= 'publish';
			$author_id			= '';
		}*/
		if( current_user_can('administrator') || current_user_can('editor') ) {
			//Admin users
			$author_id 		= '';
			$ticket_status 	= array('publish', 'private');
		} else {
			//General users
			$author_id		= $current_user->ID;
			$ticket_status 	= array('private');
		}

		$posts_per_page = get_option( 'posts_per_page' );
		$paged 			= ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

		$support_ticket_query = new WP_Query( array(
				'post_type'			=> 'nanosupport',
				'post_status'		=> $ticket_status,
				'posts_per_page'	=> $posts_per_page,
				'author'			=> $author_id,
				'paged'				=> $paged
			) );

		if( $support_ticket_query->have_posts() ) : ?>
			<div class="table-responsive">
				<table id="nst-support-tickets" class="table table-striped">
					<thead>
						<tr>
							<th><?php _e( 'ID', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Subject', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Priority', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Department', 'nano-support-ticket' ); ?></th>
							<th><span class="nst-icon-responses" title="<?php esc_attr_e( 'Responses', 'nano-support-ticket' ); ?>"></span></th>
							<th><?php _e( 'Ticket Status', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Author', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Modified', 'nano-support-ticket' ); ?></th>
						</tr>
					</thead>
					<tbody>
			<?php
			while( $support_ticket_query->have_posts() ) : $support_ticket_query->the_post();
			?>
				<tr>
					<td>
						<a href="<?php the_permalink(); ?>"><?php echo '#', get_the_ID(); ?></a>
					</td>
					<td>
						<a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a><br>
						<small class="text-muted"><?php echo __( 'Originally Posted: ', 'nano-support-ticket' ), date( 'd F Y h:i A', strtotime( $post->post_date ) ); ?></small>
					</td>
					<?php $ticket_control = get_post_meta( get_the_ID(), 'nst_control', true ); ?>
					<td>
						<?php
						$ticket_priority = $ticket_control['priority'];
						if( 'low' === $ticket_priority ) {
							_e( 'Low', 'nano-support-ticket' );
						} else if( 'medium' === $ticket_priority ) {
							echo '<span class="text-info">' , __( 'Medium', 'nano-support-ticket' ) , '</span>';
						} else if( 'high' === $ticket_priority ) {
							echo '<span class="text-warning">' , __( 'High', 'nano-support-ticket' ) , '</span>';
						} else if( 'critical' === $ticket_priority ) {
							echo '<span class="text-danger">' , __( 'Critical', 'nano-support-ticket' ) , '</span>';
						}
						?>
					</td>
					<td>
						<?php
						$departments = get_the_terms( get_the_ID(), 'nanosupport_departments' );
						if ( $departments && ! is_wp_error( $departments ) ) :
							foreach ( $departments as $department ) {
								echo $department->name;
							}
						endif;
						?>
					</td>
					<td class="text-center">
						<?php
						$response_count = wp_count_comments( get_the_ID() );
						echo $response_count->approved;
						?>
					</td>
					<td>
						<?php
						$ticket_status = $ticket_control['status'];
						if( $ticket_status ) {
							if( 'solved' === $ticket_status ) {
								$status = '<span class="label label-success">'. __( 'Solved', 'nano-support-ticket' ) .'</span>';
							} else if( 'inspection' === $ticket_status ) {
								$status = '<span class="label label-primary">'. __( 'Under Inspection', 'nano-support-ticket' ) .'</span>';
							} else {
								$status = '<span class="label label-warning">'. __( 'Open', 'nano-support-ticket' ) .'</span>';
							}
						} else {
							$status = '';
						}

						echo $status;
						?>
					</td>
					<td>
						<?php
						$author = get_user_by( 'id', $post->post_author );
						echo $author->display_name;
						?>
					</td>
					<td>
						<?php echo date( 'd F Y h:ia', strtotime( $post->post_modified ) ); ?>
					</td>
				</tr>
			<?php
			endwhile;
			?>
					</tbody>
					<tfoot>
						<tr>
							<th><?php _e( 'ID', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Subject', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Priority', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Department', 'nano-support-ticket' ); ?></th>
							<th><span class="nst-icon-responses"></span></th>
							<th><?php _e( 'Ticket Status', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Author', 'nano-support-ticket' ); ?></th>
							<th><?php _e( 'Modified', 'nano-support-ticket' ); ?></th>
						</tr>
					</tfoot>
				</table> <!-- #nst-support-tickets -->

			</div> <!-- .table-responsive -->			

			<?php
			/**
			 * Pagination
			 * @see  includes/helper-functions.php
			 */
			nst_bootstrap_pagination( $support_ticket_query );

		else :
			echo '<div class="alert alert-success" role="alert">';
				_e( '<strong>Nice!</strong> You do not have any support ticket to display.', 'nano-support-ticket' );
			echo '</div>';
		endif;
		wp_reset_postdata();

	else :
		//User is not logged in
		printf( __( 'Sorry, you cannot see your tickets without being logged in.<br><a class="btn btn-default btn-sm" href="%1s" title="Site Login"><span class="nst-icon-lock"></span> Login</a> or <a class="btn btn-default btn-sm" href="%2s" title="Site Registration"><span class="nst-icon-lock"></span> Create an account</a>', 'nano-support-ticket' ), wp_login_url(), wp_registration_url() );
		
	endif; //if( is_user_logged_in() )
	
	return ob_get_clean();
}
add_shortcode( 'nst_support_desk', 'nst_support_desk_page' );