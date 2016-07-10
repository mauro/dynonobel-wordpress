<?php
/**
 * Plugin name:		Dyno Nobel Mobile App Options
 * Description: 	Adds a page in the Administration Area with options for the Mobile App. Adds cusotm JSON call to retrieve those options.
 * Author: Mauro Dalu
 * Version: 1.0
 * Author URI: http://ipassion.it
 * Plugin URI: http://surgeworks.com
**/

class DynoMobileAppOptions {
	private $mobile_app_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'mobile_app_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'mobile_app_page_init' ) );
	}

	public function mobile_app_add_plugin_page() {
		add_menu_page(
			'Mobile App', // page_title
			'Mobile App', // menu_title
			'manage_options', // capability
			'mobile-app', // menu_slug
			array( $this, 'mobile_app_create_admin_page' ), // function
			'dashicons-smartphone', // icon_url
			100 // position
		);
	}

	public function mobile_app_create_admin_page() {
		$this->mobile_app_options = get_option( 'mobile_app_option_name' ); ?>

		<div class="wrap">
			
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'mobile_app_option_group' );
					do_settings_sections( 'mobile-app-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function mobile_app_page_init() {
		register_setting(
			'mobile_app_option_group', // option_group
			'mobile_app_options', // option_name
			array( $this, 'mobile_app_sanitize' ) // sanitize_callback
		);

		add_settings_field(
			'region_name', // id
			'Region Name', // title
			array( $this, 'region_name_callback' ), // callback
			'mobile-app-admin', // page
			'mobile_app_setting_section' // section
		);

		add_settings_section(
			'mobile_app_setting_section', // id
			'Mobile App Settings', // title
			array( $this, 'mobile_app_section_info' ), // callback
			'mobile-app-admin' // page
		);

		add_settings_field(
			'guide_slug', // id
			'Explosives Engineers\' Guide', // title
			array( $this, 'guide_slug_callback' ), // callback
			'mobile-app-admin', // page
			'mobile_app_setting_section' // section
		);

		add_settings_field(
			'library_slug', // id
			'Technical Library', // title
			array( $this, 'library_slug_callback' ), // callback
			'mobile-app-admin', // page
			'mobile_app_setting_section' // section
		);

		add_settings_field(
			'more_slug', // id
			'More', // title
			array( $this, 'more_slug_callback' ), // callback
			'mobile-app-admin', // page
			'mobile_app_setting_section' // section
		);

		add_settings_field(
			'units', // id
			'Units', // title
			array( $this, 'units_callback' ), // callback
			'mobile-app-admin', // page
			'mobile_app_setting_section' // section
		);
	}

	public function mobile_app_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['guide_slug'] ) ) {
			$sanitary_values['guide_slug'] = sanitize_text_field( $input['guide_slug'] );
		}

		if ( isset( $input['library_slug'] ) ) {
			$sanitary_values['library_slug'] = sanitize_text_field( $input['library_slug'] );
		}

		if ( isset( $input['more_slug'] ) ) {
			$sanitary_values['more_slug'] = sanitize_text_field( $input['more_slug'] );
		}

		if ( isset( $input['units'] ) ) {
			$sanitary_values['units'] = $input['units'];
		}

		if ( isset( $input['region_name'] ) ) {
			$sanitary_values['region_name'] = sanitize_text_field( $input['region_name'] );
		}

		return $sanitary_values;
	}

	public function mobile_app_section_info() {
		echo "Use this page to customize the Mobile App. Enter the page slug for each of the main app sections, the Region Name and the Default Units value for this region.";
		
	}

	public function guide_slug_callback() {
		printf(
			'<input class="regular-text" type="text" name="mobile_app_option_name[guide_slug]" id="guide_slug" value="%s"> default: guide',
			isset( $this->mobile_app_options['guide_slug'] ) ? esc_attr( $this->mobile_app_options['guide_slug']) : ''
		);
	}

	public function library_slug_callback() {
		printf(
			'<input class="regular-text" type="text" name="mobile_app_option_name[library_slug]" id="library_slug" value="%s"> default: library',
			isset( $this->mobile_app_options['library_slug'] ) ? esc_attr( $this->mobile_app_options['library_slug']) : ''
		);
	}

	public function more_slug_callback() {
		printf(
			'<input class="regular-text" type="text" name="mobile_app_option_name[more_slug]" id="more_slug" value="%s"> default: more',
			isset( $this->mobile_app_options['more_slug'] ) ? esc_attr( $this->mobile_app_options['more_slug']) : ''
		);
	}

	public function units_callback() {
		?> <select name="mobile_app_option_name[units]" id="units">
			<?php $selected = (isset( $this->mobile_app_options['units'] ) && $this->mobile_app_options['units'] === 'imperial') ? 'selected' : '' ; ?>
			<option value="imperial" <?php echo $selected; ?>>Imperial</option>
			<?php $selected = (isset( $this->mobile_app_options['units'] ) && $this->mobile_app_options['units'] === 'metric') ? 'selected' : '' ; ?>
			<option value="metric" <?php echo $selected; ?>>Metric</option>
		</select> <?php
	}

	public function region_name_callback() {
		printf(
			'<input class="regular-text" type="text" name="mobile_app_option_name[region_name]" id="region_name" value="%s">',
			isset( $this->mobile_app_options['region_name'] ) ? esc_attr( $this->mobile_app_options['region_name']) : ''
		);
		echo ' default: site name (currently: '.get_bloginfo( 'name' ).')';
	}

}
if ( is_admin() ) $mobile_app_options_page = new DynoMobileAppOptions();

class DynoMobileAppCustomApiCalls {
	public function __construct() {
			add_action( 'rest_api_init', array($this, 'register_routes') );
	}

	public function register_routes() {
		register_rest_route( 'mobile-app/v1/', '/options/', array(
		    'methods' => 'GET',
		    'callback' => array($this, 'get_mobile_app_options'),
		) );
		register_rest_route( 'mobile-app/v1/', '/sites/', array(
		    'methods' => 'GET',
		    'callback' => array($this, 'get_sites'),
		) );
	}

	public function get_mobile_app_options() {
		$options = get_option( 'mobile_app_options' );
		if (!$options['guide_slug']) $options['guide_slug'] = 'guide';
		if (!$options['library_slug']) $options['library_sluge'] = 'library';
		if (!$options['more_slug']) $options['more_sluge'] = 'more';
		if (!$options['unites']) $options['units'] = 'imperial';
		if (!$options['region_name']) $options['region_name'] = get_bloginfo( 'name' );
		return $options;
	}

	public function get_sites() {
		$sites = wp_get_sites();
        $sites_details = array();
        foreach ($sites as $site) {
            $details = get_blog_details($site['blog_id']);
            array_push($sites_details, $details);
        }
        return $sites_details;
	}
}
$mobile_app_custom_api_calls = new DynoMobileAppCustomApiCalls();

/* 
 * Retrieve this value with:
 * $mobile_app_options = get_option( 'mobile_app_options' ); // Array of All Options
 * $guide_slug = $mobile_app_options['guide_slug']; // Guide Slug
 * $library_slug = $mobile_app_options['library_slug']; // Library Slug
 * $more_slug = $mobile_app_options['more_slug']; // More Slug
 * $units = $mobile_app_options['units']; // Units
 * $region_name = $mobile_app_options['region_name']; // Region Name
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

