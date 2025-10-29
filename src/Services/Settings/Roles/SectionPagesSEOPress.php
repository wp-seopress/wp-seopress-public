<?php // phpcs:ignore

namespace SEOPress\Services\Settings\Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SectionPagesSEOPress
 */
class SectionPagesSEOPress {

	/**
	 * The render function.
	 *
	 * @since 4.6.0
	 *
	 * @param string $key_settings The key settings.
	 *
	 * @return void
	 */
	public function render( $key_settings ) {
		$options = seopress_get_service( 'AdvancedOption' )->getOption();

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // phpcs:ignore
		}

		foreach ( $wp_roles->get_names() as $key => $value ) {
			if ( 'administrator' === $key ) {
				continue;
			}
			$unique_key   = sprintf( '%s_%s', $key_settings, $key );
			$name_key     = \sprintf( '%s_%s', 'seopress_advanced_security_metaboxe', $key_settings );
			$data_options = isset( $options[ $name_key ] ) ? $options[ $name_key ] : array();

			if ( 'titles-metas_editor' === $unique_key ) { ?>
	<p class="description">
				<?php esc_html_e( 'Check a user role to authorized it to edit a specific SEO page.', 'wp-seopress' ); ?>
	</p>
	<?php } ?>

	<p>
		<label
			for="seopress_advanced_security_metaboxe_role_pages_<?php echo sanitize_key( $unique_key ); ?>">
			<input type="checkbox"
				id="seopress_advanced_security_metaboxe_role_pages_<?php echo sanitize_key( $unique_key ); ?>"
				value="1"
				name="seopress_advanced_option_name[<?php echo sanitize_key( $name_key ); ?>][<?php echo sanitize_key( $key ); ?>]"
				<?php
				if ( isset( $data_options[ $key ] ) ) {
					checked( $data_options[ $key ], '1' );
				}
				?>
			/>
			<strong><?php echo esc_html( $value ); ?></strong> (<em><?php echo esc_html( translate_user_role( $value, 'default' ) ); ?></em>)
		</label>
	</p>
			<?php
		}
	}

	/**
	 * The __call function.
	 *
	 * @since 4.6.0
	 *
	 * @param string $name The name.
	 * @param array  $params The params.
	 *
	 * @return void
	 */
	public function __call( $name, $params ) {
		$function_with_key = explode( '_', $name );
		if ( ! isset( $function_with_key[1] ) ) {
			return;
		}

		$this->render( $function_with_key[1] );
	}

	/**
	 * The printSectionPages function.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function printSectionPages() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $submenu;
		if ( ! isset( $submenu['seopress-option'] ) ) {
			return;
		}
		$menus = $submenu['seopress-option'];

		foreach ( $menus as $key => $item ) {
			$key_clean = $item[2];
			if ( in_array(
				$item[2],
				array(
					'seopress-option', // dashboard.
					'seopress-license',
					'edit.php?post_type=seopress_schemas',
					'edit.php?post_type=seopress_404',
					'edit.php?post_type=seopress_bot',
				),
				true
			) ) {
				continue;
			}

			add_settings_field(
				'seopress_advanced_security_metaboxe_' . $key_clean,
				$item[0],
				array( $this, sprintf( 'render_%s', $key_clean ) ),
				'seopress-settings-admin-advanced-security',
				'seopress_setting_section_advanced_security_roles'
			);
		}

		do_action( 'seopress_add_settings_field_advanced_security' );
	}
}
