<?php
/**
 * SEOPress Admin Header functions.
 *
 * @package SEOPress
 * @subpackage Admin_Bar
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Admin header.
 *
 * @param string $context The context.
 * @return void
 */
function seopress_admin_header( $context = '' ) {
	$docs          = seopress_get_docs_links();
	$notifications = seopress_get_service( 'Notifications' )->getSeverityNotification( 'all' );
	$total         = 0;
	$page          = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

	if ( ! empty( $notifications['total'] ) ) {
		$total = $notifications['total'];
	}
	?>

<div id="seopress-header" class="seopress-option">
	<div id="seopress-navbar">
		<ul>
			<li>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=seopress-option' ) ); ?>">
					<?php esc_html_e( 'Home', 'wp-seopress' ); ?>
				</a>
			</li>
			<?php if ( get_admin_page_title() ) { ?>
			<li>
				<?php echo esc_html( get_admin_page_title() ); ?>
			</li>
			<?php } ?>
		</ul>
	</div>
	<aside id="seopress-activity-panel" class="seopress-activity-panel">
		<div role="tablist" aria-orientation="horizontal" class="seopress-activity-panel-tabs">
			<?php if ( 'seopress-option' === $page ) { ?>
				<button type="button" role="tab" aria-selected="true" aria-label="<?php esc_html_e( 'Display', 'wp-seopress' ); ?>" title="<?php esc_html_e( 'Display', 'wp-seopress' ); ?>" id="activity-panel-tab-display" data-panel="display" class="btn">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M15.75 0.75H2.25C1.85218 0.75 1.47064 0.908035 1.18934 1.18934C0.908035 1.47064 0.75 1.85218 0.75 2.25V15.75C0.75 16.1478 0.908035 16.5294 1.18934 16.8107C1.47064 17.092 1.85218 17.25 2.25 17.25H15.75C16.1478 17.25 16.5294 17.092 16.8107 16.8107C17.092 16.5294 17.25 16.1478 17.25 15.75V2.25C17.25 1.85218 17.092 1.47064 16.8107 1.18934C16.5294 0.908035 16.1478 0.75 15.75 0.75ZM15.75 8.25H9.75V2.25H15.75V8.25ZM8.25 2.25V8.25H2.25V2.25H8.25ZM2.25 9.75H8.25V15.75H2.25V9.75ZM15.75 15.75H9.75V9.75H15.75V15.75Z" fill="#0C082F"/>
					</svg>
					<span class="screen-reader-text"><?php esc_html_e( 'Display', 'wp-seopress' ); ?></span>
				</button>
			<?php } ?>
			<button type="button" role="tab" aria-selected="true" aria-label="<?php esc_html_e( 'Help', 'wp-seopress' ); ?>" title="<?php esc_html_e( 'Help', 'wp-seopress' ); ?>" id="activity-panel-tab-help" data-panel="help" class="btn">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M11.125 14.875C11.125 15.0975 11.059 15.315 10.9354 15.5C10.8118 15.685 10.6361 15.8292 10.4305 15.9144C10.225 15.9995 9.99876 16.0218 9.78053 15.9784C9.5623 15.935 9.36184 15.8278 9.20451 15.6705C9.04718 15.5132 8.94003 15.3127 8.89662 15.0945C8.85321 14.8762 8.87549 14.65 8.96064 14.4445C9.04579 14.2389 9.18998 14.0632 9.37499 13.9396C9.55999 13.816 9.7775 13.75 10 13.75C10.2984 13.75 10.5845 13.8685 10.7955 14.0795C11.0065 14.2905 11.125 14.5766 11.125 14.875ZM10 4.75C7.93188 4.75 6.25 6.26406 6.25 8.125V8.5C6.25 8.69891 6.32902 8.88968 6.46967 9.03033C6.61033 9.17098 6.80109 9.25 7 9.25C7.19892 9.25 7.38968 9.17098 7.53033 9.03033C7.67099 8.88968 7.75 8.69891 7.75 8.5V8.125C7.75 7.09375 8.75969 6.25 10 6.25C11.2403 6.25 12.25 7.09375 12.25 8.125C12.25 9.15625 11.2403 10 10 10C9.80109 10 9.61033 10.079 9.46967 10.2197C9.32902 10.3603 9.25 10.5511 9.25 10.75V11.5C9.25 11.6989 9.32902 11.8897 9.46967 12.0303C9.61033 12.171 9.80109 12.25 10 12.25C10.1989 12.25 10.3897 12.171 10.5303 12.0303C10.671 11.8897 10.75 11.6989 10.75 11.5V11.4325C12.46 11.1184 13.75 9.75437 13.75 8.125C13.75 6.26406 12.0681 4.75 10 4.75ZM19.75 10C19.75 11.9284 19.1782 13.8134 18.1068 15.4168C17.0355 17.0202 15.5127 18.2699 13.7312 19.0078C11.9496 19.7458 9.98919 19.9389 8.09787 19.5627C6.20656 19.1865 4.46928 18.2579 3.10571 16.8943C1.74215 15.5307 0.813554 13.7934 0.437348 11.9021C0.061142 10.0108 0.254225 8.05042 0.992179 6.26884C1.73013 4.48726 2.97982 2.96451 4.58319 1.89317C6.18657 0.821828 8.07164 0.25 10 0.25C12.585 0.25273 15.0634 1.28084 16.8913 3.10872C18.7192 4.93661 19.7473 7.41498 19.75 10ZM18.25 10C18.25 8.3683 17.7661 6.77325 16.8596 5.41655C15.9531 4.05984 14.6646 3.00242 13.1571 2.37799C11.6497 1.75357 9.99085 1.59019 8.39051 1.90852C6.79017 2.22685 5.32016 3.01259 4.16637 4.16637C3.01259 5.32015 2.22685 6.79016 1.90853 8.3905C1.5902 9.99085 1.75358 11.6496 2.378 13.1571C3.00242 14.6646 4.05984 15.9531 5.41655 16.8596C6.77326 17.7661 8.36831 18.25 10 18.25C12.1873 18.2475 14.2843 17.3775 15.8309 15.8309C17.3775 14.2843 18.2475 12.1873 18.25 10Z" fill="#0C082F"/>
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Help', 'wp-seopress' ); ?></span>
			</button>
			<button type="button" role="tab" aria-selected="true" aria-label="<?php esc_html_e( 'Notifications', 'wp-seopress' ); ?>" title="<?php esc_html_e( 'Notifications', 'wp-seopress' ); ?>" id="activity-panel-tab-notifications" data-panel="notifications" class="btn">
			<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M13.6004 0.200195H1.60039C1.28213 0.200195 0.976903 0.326627 0.751863 0.551667C0.526823 0.776707 0.400391 1.08194 0.400391 1.4002V13.4002C0.400391 13.7184 0.526823 14.0237 0.751863 14.2488C0.976903 14.4738 1.28213 14.6002 1.60039 14.6002H13.6004C13.9186 14.6002 14.2239 14.4738 14.449 14.2488C14.674 14.0237 14.8004 13.7184 14.8004 13.4002V1.4002C14.8004 1.08194 14.674 0.776707 14.449 0.551667C14.2239 0.326627 13.9186 0.200195 13.6004 0.200195ZM13.6004 1.4002V9.2002H11.4479C11.2904 9.1998 11.1343 9.23068 10.9889 9.29108C10.8434 9.35148 10.7114 9.44012 10.6004 9.55196L9.15215 11.0002H6.04863L4.60039 9.55196C4.48937 9.44004 4.3572 9.35132 4.21157 9.29092C4.06596 9.23052 3.90978 9.19972 3.75214 9.2002H1.60039V1.4002H13.6004ZM13.6004 13.4002H1.60039V10.4002H3.75214L5.20039 11.8484C5.31141 11.9604 5.44358 12.0491 5.58921 12.1095C5.73482 12.1699 5.891 12.2007 6.04863 12.2002H9.15215C9.30975 12.2007 9.46599 12.1699 9.61159 12.1095C9.75719 12.0491 9.88935 11.9604 10.0004 11.8484L11.4486 10.4002H13.6004V13.4002Z" fill="#0C082F"/>
			</svg>
				<?php
				if ( $total > 0 ) {
					echo '<span class="badge">' . absint( $total ) . '</span>';
				}
				?>
				<span class="screen-reader-text"><?php esc_html_e( 'Notifications', 'wp-seopress' ); ?></span>
			</button>
		</div>
		<div id="seopress-activity-panel-help" class="seopress-activity-panel-wrapper" tabindex="0" role="tabpanel"
			aria-label="Help">
			<div id="activity-panel-true">
				<div class="seopress-activity-panel-header">
					<div class="seopress-inbox-title">
						<h2><?php esc_html_e( 'Documentation', 'wp-seopress' ); ?></h2>
					</div>
				</div>
				<div class="seopress-activity-panel-content">
					<form action="<?php echo esc_url( $docs['website'] ); ?>" method="get" class="seopress-search" target="_blank">
						<label for="seopress-search" class="screen-reader-text"><?php esc_html_e( 'Search', 'wp-seopress' ); ?></label>
						<input class="adminbar-input" id="seopress-search" name="s" type="text" value="" placeholder="<?php esc_html_e( 'Search our documentation', 'wp-seopress' ); ?>" maxlength="150">
						<input type="submit" class="btn btnSecondary" value="<?php esc_html_e( 'Search', 'wp-seopress' ); ?>">
					</form>

					<div class="seopress-panel-section">
						<h2><?php esc_html_e( 'Get started guides', 'wp-seopress' ); ?></h2>
						<ul class="seopress-list-guides" role="menu">
							<?php
							$docs_started = $docs['get_started'];

							foreach ( $docs_started as $key => $value ) {
								?>
								<li class="seopress-item">
									<?php if ( ! empty( $value['link'] ) && $value['title'] && $value['desc'] && $value['ico'] ) { ?>
										<a href="<?php echo esc_url( $value['link'] ); ?>" class="seopress-item-inner has-action" aria-disabled="false" tabindex="0" role="menuitem" target="_blank" data-link-type="external">
											<img src="<?php echo esc_url( SEOPRESS_ASSETS_DIR . '/img/' . $value['ico'] . '.svg' ); ?>" width="48" height="48" alt="" class="seopress-item-ico"/>
											<h3 class="seopress-item-title">
												<?php echo esc_html( $value['title'] ); ?>
											</h3>
											<p class="seopress-item-desc">
												<?php echo esc_html( $value['desc'] ); ?>
											</p>
										</a>
									<?php } ?>
								</li>
								<?php
							}
							?>
						</ul>
					</div>

					<div class="seopress-panel-section">
						<h2><?php esc_html_e( 'By product', 'wp-seopress' ); ?></h2>
						<p><?php esc_html_e( 'Quickly filter our documentation by a specific plugin.', 'wp-seopress' ); ?></p>
						<div class="seopress-products">
							<a href="<?php echo esc_url( $docs['support-free'] ); ?>" title="<?php esc_html_e( 'SEOPress Free documentation (new window)', 'wp-seopress' ); ?>" target="_blank">
								<img src="<?php echo esc_url( SEOPRESS_ASSETS_DIR . '/img/support-seopress-free.svg' ); ?>" width="291" height="81" alt="SEOPress Free"/>
							</a>
							<a href="<?php echo esc_url( $docs['support-pro'] ); ?>" title="<?php esc_html_e( 'SEOPress PRO documentation (new window)', 'wp-seopress' ); ?>" target="_blank">
								<img src="<?php echo esc_url( SEOPRESS_ASSETS_DIR . '/img/support-seopress-pro.svg' ); ?>" width="291" height="81" alt="SEOPress PRO"/>
							</a>
							<a href="<?php echo esc_url( $docs['support-insights'] ); ?>" title="<?php esc_html_e( 'SEOPress Insights documentation (new window)', 'wp-seopress' ); ?>" target="_blank">
								<img src="<?php echo esc_url( SEOPRESS_ASSETS_DIR . '/img/support-seopress-insights.svg' ); ?>" width="291" height="81" alt="SEOPress Insights"/>
							</a>
						</div>
					</div>

					<div class="seopress-panel-section">
						<h2><span class="dashicons dashicons-translation"></span><?php esc_html_e( 'Translations', 'wp-seopress' ); ?></h2>
						<p><?php esc_html_e( 'The plugin is translated into dozens of languages.', 'wp-seopress' ); ?></p>
						<p><?php echo /* translators: %s: admin Updates URL */ wp_kses_post( sprintf( __( 'Go to <a href="%s">Updates</a> and click <strong>Update translations</strong> button to get the latest available translations for your language.', 'wp-seopress' ), esc_url( admin_url( 'update-core.php' ) ) ) ); ?></p>
						<p><?php esc_html_e( 'Your translation is missing? Contribute to SEOPress now!', 'wp-seopress' ); ?></p>
						<a href="<?php echo esc_url( $docs['i18n'] ); ?>" class="btn btnSecondary" title="<?php esc_html_e( 'Learn how to translate the plugin in your own language', 'wp-seopress' ); ?>" target="_blank">
							<?php esc_html_e( 'Translate SEOPress', 'wp-seopress' ); ?>
							<span class="dashicons dashicons-external"></span>
						</a>
					</div>

					<div class="seopress-panel-section">
						<h2><?php esc_html_e( 'Open a support ticket', 'wp-seopress' ); ?></h2>
						<p><?php esc_html_e( 'Can‘t find the info you‘re looking for?', 'wp-seopress' ); ?></p>
						<a href="<?php echo esc_url( $docs['support-tickets'] ); ?>" class="btn btnSecondary" title="<?php esc_html_e( 'Open a support ticket on SEOPress.org website (new window)', 'wp-seopress' ); ?>" target="_blank">
							<?php esc_html_e( 'Open a support ticket', 'wp-seopress' ); ?>
							<span class="dashicons dashicons-external"></span>
						</a>
					</div>
				</div>
			</div>
		</div>

		<?php if ( 'seopress-option' === $page ) { ?>
			<div id="seopress-activity-panel-display" class="seopress-activity-panel-wrapper" tabindex="0" role="popover" aria-label="Display">
				<div id="activity-panel-true">
					<div class="seopress-activity-panel-header">
						<div class="seopress-inbox-title">
							<h2><?php esc_html_e( 'Choose the way it looks', 'wp-seopress' ); ?></h2>
						</div>
					</div>
					<div class="seopress-activity-panel-content seopress-activity-panel-options">
						<h3>
							<?php esc_html_e( 'Show / Hide your dashboard blocks:', 'wp-seopress' ); ?>
						</h3>

						<?php
							$options = get_option( 'seopress_advanced_option_name' ) ? get_option( 'seopress_advanced_option_name' ) : array();
							$check   = seopress_get_service( 'NoticeOption' )->getNoticeGetStarted();
						?>

						<p>
							<input id="notice-get-started" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								data-notice=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="notice-get-started" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="notice-get-started"></label>
							<label for="notice-get-started">
								<?php esc_html_e( 'Hide Get started?', 'wp-seopress' ); ?>
							</label>
						</p>

						<?php $check = seopress_get_service( 'NoticeOption' )->getNoticeTasks(); ?>

						<p>
							<input id="notice-tasks" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								data-notice=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="notice-tasks" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="notice-tasks"></label>
							<label for="notice-tasks">
								<?php esc_html_e( 'Hide SEO Suite?', 'wp-seopress' ); ?>
							</label>
						</p>

						<?php
							$check = isset( $options['seopress_advanced_appearance_seo_tools'] ) ? $options['seopress_advanced_appearance_seo_tools'] : false;
						?>

						<p>
							<input id="seopress_tools" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="seopress_advanced_option_name[seopress_advanced_appearance_seo_tools]" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="seopress_tools"></label>
							<label for="seopress_advanced_option_name[seopress_advanced_appearance_seo_tools]">
								<?php esc_html_e( 'Hide Site Overview?', 'wp-seopress' ); ?>
							</label>
						</p>

						<?php
							$check = isset( $options['seopress_advanced_appearance_notifications'] ) ? $options['seopress_advanced_appearance_notifications'] : false;
						?>

						<p>
							<input id="notifications_center" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="seopress_advanced_option_name[seopress_advanced_appearance_notifications]" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="notifications_center"></label>
							<label for="seopress_advanced_option_name[seopress_advanced_appearance_notifications]">
								<?php esc_html_e( 'Hide Notifications Center?', 'wp-seopress' ); ?>
							</label>
						</p>

						<?php $check = seopress_get_service( 'NoticeOption' )->getNoticeEbooks(); ?>

						<p>
							<input id="notice-ebooks" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								data-notice=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="notice-ebooks" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="notice-ebooks"></label>
							<label for="notice-ebooks">
								<?php esc_html_e( 'Hide SEO Ebooks?', 'wp-seopress' ); ?>
							</label>
						</p>

						<?php $check = seopress_get_service( 'NoticeOption' )->getNoticeIntegrations(); ?>

						<p>
							<input id="notice-integrations" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								data-notice=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="notice-integrations" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="notice-integrations"></label>
							<label for="notice-integrations">
								<?php esc_html_e( 'Hide Integrations?', 'wp-seopress' ); ?>
							</label>
						</p>

						<?php $check = isset( $options['seopress_advanced_appearance_news'] ) ? $options['seopress_advanced_appearance_news'] : false; ?>

						<p>
							<input id="seopress_news" class="toggle" data-toggle=
								<?php
								if ( '1' === $check ) {
									echo '1';
								} else {
									echo '0';
								}
								?>
								name="seopress_advanced_option_name[seopress_advanced_appearance_news]" type="checkbox"
								<?php
								if ( '1' === $check ) {
									echo 'checked="yes"';
								}
								?>
							/>
							<label for="seopress_news"></label>
							<label for="seopress_advanced_option_name[seopress_advanced_appearance_news]">
								<?php esc_html_e( 'Hide SEO News?', 'wp-seopress' ); ?>
							</label>
						</p>
					</div>
				</div>
			</div>
		<?php } ?>

		<div id="seopress-activity-panel-notifications" class="seopress-activity-panel-wrapper" tabindex="0" role="popover" aria-label="Notifications">
			<div id="activity-panel-true">
				<div class="seopress-activity-panel-header">
					<div class="seopress-inbox-title">
						<h2>
						<?php
						/* translators: %d number of notifications */
						echo wp_kses_post( sprintf( __( 'Notifications <span>(%d)</span>', 'wp-seopress' ), absint( $total ) ) );
						?>
						</h2>
						<a href="#seopress-hidden-notifications" class="btnLink"><?php esc_html_e( 'Show hidden', 'wp-seopress' ); ?></a>
					</div>
				</div>
				<div class="seopress-activity-panel-content">
					<?php
						include_once dirname( __DIR__ ) . '/blocks/notifications-center.php';
					?>
				</div>
			</div>
		</div>
	</aside>
</div>
	<?php
}
