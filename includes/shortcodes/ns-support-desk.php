<?php
/**
 * Shortcode: Support Desk
 *
 * Showing the common ticket center of all the support tickets to the respective privileges.
 * Show all the tickets at the front end using shortcode [nanosupport_desk]
 *
 * @author  	nanodesigns
 * @category 	Shortcode
 * @package 	NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ns_support_desk_page() {
	ob_start();

	if( is_user_logged_in() ) :
		//User is Logged in

		global $post, $current_user;

		if( isset($_GET['success']) && $_GET['success'] == 1 ) {
			echo '<div class="alert alert-success" role="alert">';
				_e( "<strong>Success!</strong> Your ticket is submitted successfully! It will be reviewed shortly and replied as early as possible.", 'nanosupport' );
		    echo '</div>';		
		}
		?>

		<div class="well well-sm">
			<div class="row">
				<div class="col-sm-8 text-muted">
					<small><?php _e( 'Only Public tickets here. Private tickets are visible to the admins and to the ticket owner only.', 'nanodesigns-ns' ); ?></small>
				</div>
				<div class="col-sm-4 text-right">
					<a class="btn btn-sm btn-danger btn-submit-new-ticket" href="<?php echo esc_url( get_permalink( get_page_by_path('submit-ticket') ) ); ?>">
						<span class="ns-icon-tag"></span> <?php _e( 'Submit a Ticket', 'nanodesigns-ns' ); ?>
					</a>
				</div>
			</div>
		</div>
		
		<?php
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

			<?php
			while( $support_ticket_query->have_posts() ) : $support_ticket_query->the_post();

				//Get ticket information
				$ticket_control = get_post_meta( get_the_ID(), 'ns_control', true );

				$ticket_status = $ticket_control['status'];

				if( $ticket_status && 'solved' === $ticket_status )
					$status_class = 'status-solved';
				elseif( $ticket_status && 'inspection' === $ticket_status )
					$status_class = 'status-inspection';
				elseif( $ticket_status && 'open' === $ticket_status )
					$status_class = 'status-open';
				?>
				<div class="ticket-cards ns-cards <?php echo esc_attr($status_class); ?>">
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<h3 class="ticket-head">
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
									<small class="ticket-id"><?php printf( '#%s', get_the_ID() ); ?></small> &mdash; <?php the_title(); ?>
								</a>
							</h3>
							<div class="ticket-author">
								<?php
								$author = get_user_by( 'id', $post->post_author );
								echo '<span class="ns-icon-lock"></span> '. $author->display_name;
								?>
							</div>
						</div>
						<div class="col-sm-3 col-xs-4 ticket-meta">
							<div class="text-blocks">
								<strong><?php _e('Department:', 'nanosupport'); ?></strong><br>
								<?php echo ns_get_ticket_departments(); ?>
							</div>
							<div class="text-blocks">
								<strong><?php _e('Created & Updated:', 'nanosupport'); ?></strong><br>
								<?php echo date( 'd M Y h:i A', strtotime( $post->post_date ) ); ?><br>
								<?php echo date( 'd M Y h:i A', strtotime( $post->post_modified ) ); ?>
							</div>
						</div>
						<div class="col-sm-3 col-xs-4 ticket-meta">
							<div class="text-blocks">
								<strong><?php _e('Responses:', 'nanosupport'); ?></strong><br>
								<?php
								$response_count = wp_count_comments( get_the_ID() );
								echo '<span class="responses-count">'. $response_count->approved .'</span>';
								?>
							</div>
							<div class="text-blocks">
								<strong><?php _e('Last Replied by:', 'nanosupport'); ?></strong><br>
								<?php
								$last_response = ns_get_last_response();
					            $last_responder = get_userdata( $last_response['user_id'] );
					            if ( $last_responder ) {
					                echo $last_responder->display_name, '<br>';
					                echo ns_time_elapsed($last_response['comment_date']), ' ago';
					            } else {
					                echo '-';
					            }
					            ?>
							</div>
						</div>
						<div class="col-sm-2 col-xs-4 ticket-meta">
							<div class="text-blocks">
								<strong><?php _e('Priority:', 'nanosupport'); ?></strong><br>
								<?php
								$ticket_priority = $ticket_control['priority'];
								if( 'low' === $ticket_priority ) {
									_e( 'Low', 'nanosupport' );
								} else if( 'medium' === $ticket_priority ) {
									echo '<span class="text-info">' , __( 'Medium', 'nanosupport' ) , '</span>';
								} else if( 'high' === $ticket_priority ) {
									echo '<span class="text-warning">' , __( 'High', 'nanosupport' ) , '</span>';
								} else if( 'critical' === $ticket_priority ) {
									echo '<span class="text-danger">' , __( 'Critical', 'nanosupport' ) , '</span>';
								}
								?>
							</div>
							<div class="text-blocks">
								<strong><?php _e('Ticket Status:', 'nanosupport'); ?></strong><br>
								<?php
								if( $ticket_status ) {
									if( 'solved' === $ticket_status ) {
										$status = '<span class="label label-success">'. __( 'Solved', 'nanosupport' ) .'</span>';
									} else if( 'inspection' === $ticket_status ) {
										$status = '<span class="label label-primary">'. __( 'Under Inspection', 'nanosupport' ) .'</span>';
									} else {
										$status = '<span class="label label-warning">'. __( 'Open', 'nanosupport' ) .'</span>';
									}
								} else {
									$status = '';
								}

								echo $status;
								?>
							</div>
						</div>
					</div>
				</div> <!-- /.ticket-cards -->

			<?php
			endwhile;


			/**
			 * Pagination
			 * @see  includes/helper-functions.php
			 */
			ns_bootstrap_pagination( $support_ticket_query );

		else :
			echo '<div class="alert alert-success" role="alert">';
				_e( '<strong>Nice!</strong> You do not have any support ticket to display.', 'nanosupport' );
			echo '</div>';
		endif;
		wp_reset_postdata();

	else :
		//User is not logged in
		printf( __( 'Sorry, you cannot see your tickets without being logged in.<br><a class="btn btn-default btn-sm" href="%1s" title="Site Login"><span class="ns-icon-lock"></span> Login</a> or <a class="btn btn-default btn-sm" href="%2s" title="Site Registration"><span class="ns-icon-lock"></span> Create an account</a>', 'nanosupport' ), wp_login_url(), wp_registration_url() );
		
	endif; //if( is_user_logged_in() )
	
	return ob_get_clean();
}
add_shortcode( 'nanosupport_desk', 'ns_support_desk_page' );