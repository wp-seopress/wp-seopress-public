<?php
/**
 * Print the notifications center.
 *
 * @package SEOPress
 * @subpackage Blocks
 */

defined( 'ABSPATH' ) || exit( ' Please don&rsquo;t call the plugin directly. Thanks :)' );

if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && SEOPRESS_WL_ADMIN_HEADER === false ) {
	return;
}

$args  = seopress_get_service( 'Notifications' )->generateAllNotifications();
$args  = seopress_get_service( 'Notifications' )->orderByImpact( $args );
$total = seopress_get_service( 'Notifications' )->getSeverityNotification( 'all' );
?>

<div id="seopress-notifications-center" class="seopress-notifications-center">
	<div class="seopress-notifications-list-content seopress-notifications-active">
	<?php
	if ( ! empty( $args ) ) {
		foreach ( $args as $arg ) {
			if ( isset( $arg['status'] ) && true === $arg['status'] ) {
				echo wp_kses_post( seopress_get_service( 'Notifications' )->renderNotification( $arg ) );
			}
		}
	}

	if ( 0 === $total ) {
		?>
		<div class="seopress-notifications-none">
			<img src="<?php echo esc_url( SEOPRESS_ASSETS_DIR . '/img/ico-notifications.svg' ); ?>" width="56" height="56" alt=""/>
			<h3>
				<?php esc_attr_e( 'You don‘t have any notifications yet!', 'wp-seopress' ); ?>
			</h3>
		</div>
		<?php
	}
	?>
	</div>
	<details class="seopress-notifications-hidden">
		<summary class="seopress-notifications-list-title">
			<img src="<?php echo esc_url( SEOPRESS_ASSETS_DIR . '/img/ico-notifications-hidden.svg' ); ?>" alt='' width='32' height='32' />
			<h3 id="seopress-hidden-notifications"><?php esc_attr_e( 'Hidden notifications', 'wp-seopress' ); ?></h3>
		</summary>
		<div class="seopress-notifications-list-content">
			<?php
			if ( empty( $args ) ) {
				return;
			}

			$hidden_notifications = array_filter(
				$args,
				function ( $arg ) {
					return isset( $arg['status'] ) && false === $arg['status'];
				}
			);

			if ( empty( $hidden_notifications ) ) {
				echo '<p>' . esc_html__( 'You currently have no hidden notifications.', 'wp-seopress' ) . '</p>';
				return;
			}

			$notification_service = seopress_get_service( 'Notifications' );
			foreach ( $hidden_notifications as $notification ) {
				echo wp_kses_post( $notification_service->renderNotification( $notification ) );
			}
			?>
		</div>
	</details>
</div>
<!--#seopress-notifications-center-->
