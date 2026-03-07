<?php
/**
 * SEOPress Titles functions.
 *
 * @package SEOPress
 * @subpackage Admin_Pages
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$this->options = get_option( 'seopress_titles_option_name' );
if ( function_exists( 'seopress_admin_header' ) ) {
	echo seopress_admin_header();
}
?>
<form method="post"
	action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>"
	class="seopress-option">
	<?php
		echo $this->feature_title( 'titles' );
		settings_fields( 'seopress_titles_option_group' );
	?>

	<div id="seopress-tabs" class="wrap">
		<?php
			$current_tab      = '';
		$plugin_settings_tabs = array(
			'tab_seopress_titles_home'     => __( 'Home', 'wp-seopress' ),
			'tab_seopress_titles_single'   => __( 'Post Types', 'wp-seopress' ),
			'tab_seopress_titles_archives' => __( 'Archives', 'wp-seopress' ),
			'tab_seopress_titles_tax'      => __( 'Taxonomies', 'wp-seopress' ),
			'tab_seopress_titles_advanced' => __( 'Advanced', 'wp-seopress' ),
		);

		echo '<div class="nav-tab-wrapper">';
		foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
			echo '<a id="' . esc_attr( $tab_key ) . '-tab" class="nav-tab" href="?page=seopress-titles#tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a>';
		}
		echo '</div>';
		?>
		<div class="seopress-tab 
		<?php
		if ( 'tab_seopress_titles_home' === $current_tab ) {
			echo 'active';
		}
		?>
		" id="tab_seopress_titles_home">
			<?php do_settings_sections( 'seopress-settings-admin-titles-home' ); ?>
			<?php
			if ( function_exists( 'seopress_render_contextual_promotion' ) ) {
				seopress_render_contextual_promotion( 'titles' );
			}
			?>
		</div>
		<div class="seopress-tab
		<?php
		if ( 'tab_seopress_titles_single' === $current_tab ) {
			echo 'active';
		}
		?>
		" id="tab_seopress_titles_single">
			<?php do_settings_sections( 'seopress-settings-admin-titles-single' ); ?>
			<?php
			if ( function_exists( 'seopress_render_contextual_promotion' ) ) {
				seopress_render_contextual_promotion( 'titles' );
			}
			?>
		</div>
		<div class="seopress-tab
		<?php
		if ( 'tab_seopress_titles_archives' === $current_tab ) {
			echo 'active';
		}
		?>
		" id="tab_seopress_titles_archives">
			<?php do_settings_sections( 'seopress-settings-admin-titles-archives' ); ?>
			<?php
			if ( function_exists( 'seopress_render_contextual_promotion' ) ) {
				seopress_render_contextual_promotion( 'titles' );
			}
			?>
		</div>
		<div class="seopress-tab
		<?php
		if ( 'tab_seopress_titles_tax' === $current_tab ) {
			echo 'active';
		}
		?>
		" id="tab_seopress_titles_tax">
			<?php do_settings_sections( 'seopress-settings-admin-titles-tax' ); ?>
			<?php
			if ( function_exists( 'seopress_render_contextual_promotion' ) ) {
				seopress_render_contextual_promotion( 'titles' );
			}
			?>
		</div>
		<div class="seopress-tab
		<?php
		if ( 'tab_seopress_titles_advanced' === $current_tab ) {
			echo 'active';
		}
		?>
		" id="tab_seopress_titles_advanced">
			<?php do_settings_sections( 'seopress-settings-admin-titles-advanced' ); ?>
			<?php
			if ( function_exists( 'seopress_render_contextual_promotion' ) ) {
				seopress_render_contextual_promotion( 'titles' );
			}
			?>
		</div>
	</div>

	<?php sp_submit_button( esc_html__( 'Save changes', 'wp-seopress' ) ); ?>
</form>
