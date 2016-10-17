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

	public $slugs =  array(
			'slug_guide' => array(
				'title' => 'Explosives Engineers\' Guide', 
				'default' => 'guide',
				),
			'slug_library' => array(
				'title' => 'Library', 
				'default' => 'library',
				),
			'slug_more' => array(
				'title' => 'More section', 
				'default' => 'more',
				),
			'slug_explosives_per_blasthole' => array(
				'title' => 'Explosives per Blasthole Formula', 
				'default' => 'formula-for-explosive-per-blasthole',
				),
			'slug_rock_per_blasthole' => array(
				'title' => 'Rock per Blasthole Formula', 
				'default' => 'formula-for-rock-per-blasthole',
				),
			'slug_powder_factor' => array(
				'title' => 'Powder Factor Formula', 
				'default' => 'formula-for-powder-factor',
				),
			'slug_wet_hole' => array(
				'title' => 'Wet Hole Formula', 
				'default' => 'formula-for-wet-hole',
				),
			'slug_ground_vibration_prediction' => array(
				'title' => 'Ground Vibration Prediction', 
				'default' => 'formula-for-ground-vibration-prediction',
				),
			'slug_explosive_charge_per_blasthole' => array(
				'title' => 'Explosive Charge Per Blasthole', 
				'default' => 'formula-for-explosive-charge-per-blasthole',
				),
			'slug_scaled_distance' => array(
				'title' => 'Scaled Distance', 
				'default' => 'formula-for-scaled-distance',
				),
			'slug_distance_to_nearest_structure' => array(
				'title' => 'Distance To Nearest Structure',
				'default' => 'formula-for-distance-to-nearest-structure'
				),
			'slug_airblast_prediction' => array(
				'title' => 'Airblast Prediction',
				'default' => 'formula-for-airblast-prediction'
				),
		);

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
		$this->mobile_app_options = get_option( 'mobile_app_options' ); ?>

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

		add_settings_section(
			'mobile_app_setting_section', // id
			'Mobile App Settings', // title
			array( $this, 'mobile_app_section_info' ), // callback
			'mobile-app-admin' // page
		);

		add_settings_field(
			'region_name', // id
			'Region Name', // title
			array( $this, 'region_name_callback' ), // callback
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

		add_settings_field(
			'countries', // id
			'Countries', // title
			array( $this, 'countries_callback' ), // callback
			'mobile-app-admin', // page
			'mobile_app_setting_section' // section
		);


		foreach ($this->slugs as $slug => $parameters) {
			add_settings_field(
						$slug, // id
						$parameters['title'], // title
						array( $this, 'slug_callback' ), // callback
						'mobile-app-admin', // page
						'mobile_app_setting_section', // section
						$args = array( $slug, $parameters['default'] ) // arguments for callback
					);
		}

	}

	public function mobile_app_sanitize($input) {
		$sanitary_values = array();
		
		foreach ($slugs as $slug => $parameters) {
			if ( isset( $input[$slug] ) ) {
				$sanitary_values[$slug] = sanitize_text_field( $input[$slug] );
			}
		}

		if ( isset( $input['units'] ) ) {
			$sanitary_values['units'] = $input['units'];
		}

		if ( isset( $input['region_name'] ) ) {
			$sanitary_values['region_name'] = sanitize_text_field( $input['region_name'] );
		}

		if ( isset( $input['countries'] ) ) {
			$sanitary_values['countries'] = sanitize_text_field( $input['countries'] );
		}

		return $sanitary_values;
	}

	public function mobile_app_section_info() {
		echo "Use this page to customize the Mobile App. Enter the page slug for each of the main app sections, the Region Name and the Default Units value for this region.";
		
	}

	public function slug_callback($args) {
		$slug = $args[0];
		$default = $args[1];

		echo '<input class="regular-text" type="text" name="mobile_app_options['.$slug.']" id="'.$slug.'"';
		printf(
			'value="%s">',
			isset( $this->mobile_app_options[$slug] ) ? esc_attr( $this->mobile_app_options[$slug]) : ''
		);
		echo ' default slug: '.$default;
	}

	public function units_callback() {
		?> <select name="mobile_app_options[units]" id="units">
			<?php $selected = (isset( $this->mobile_app_options['units'] ) && $this->mobile_app_options['units'] === 'imperial') ? 'selected' : '' ; ?>
			<option value="imperial" <?php echo $selected; ?>>Imperial</option>
			<?php $selected = (isset( $this->mobile_app_options['units'] ) && $this->mobile_app_options['units'] === 'metric') ? 'selected' : '' ; ?>
			<option value="metric" <?php echo $selected; ?>>Metric</option>
		</select> <?php
	}

	public function region_name_callback() {
		printf(
			'<input class="regular-text" type="text" name="mobile_app_options[region_name]" id="region_name" value="%s">',
			isset( $this->mobile_app_options['region_name'] ) ? esc_attr( $this->mobile_app_options['region_name']) : ''
		);
		echo ' default: site name (currently: '.get_bloginfo( 'name' ).')';
	}

	public function countries_callback() {
		printf(
			'<input class="regular-text" type="text" name="mobile_app_options[countries]" id="countries" value="%s">',
			isset( $this->mobile_app_options['countries'] ) ? esc_attr( $this->mobile_app_options['countries']) : ''
		);
		echo '<br/><br/>A list of 2 letters ISO country codes within this region, separated by comma.<br/>I.e.: Europe would be "AL, AD, AT, BY, BE, BA, BG, etc.".<br/><a href="http://www.countrycallingcodes.com/iso-country-codes/">This site</a> has a thorough list.<hr/>';
	}

}
if ( is_admin() ) $mobile_app_options_page = new DynoMobileAppOptions();

class DynoMobileAppCustomApiCalls {
	public function __construct() {
			add_action( 'rest_api_init', array($this, 'register_routes') );
	}

	public function register_routes() {
		//register_rest_route( 'mobile-app/v1/', '/options/', array(
		//    'methods' => 'GET',
		//    'callback' => array($this, 'get_mobile_app_options'),
		//) );
		register_rest_route( 'mobile-app/v1/', '/sites/', array(
		    'methods' => 'GET',
		    'callback' => array($this, 'get_sites'),
		) );
		register_rest_route( 'mobile-app/v1/', '/regions/', array(
		    'methods' => 'GET',
		    'callback' => array($this, 'get_sites'),
		) );
		register_rest_route( 'mobile-app/v1/', '/contents/', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_all_contents'),
			
			'args'	=> array(
				'after' =>	array(
							'description'        => __( 'Limit response to resources modified after a given ISO8601 compliant date.' ),
							'type'               => 'string',
							'format'             => 'date-time',
							'validate_callback'  => 'rest_validate_request_arg',
							),
				'deleted' => array(
							'description'		=> __('Get a list of IDs for the deleted items'),
							'type'				=> 'bool',
							),
			),

		) );
	}

	private function get_mobile_app_options($blog_id = NULL) {
		if ($blog_id) switch_to_blog($blog_id);
		$options = get_option( 'mobile_app_options' );

		$dyno = new DynoMobileAppOptions();

		foreach ($dyno->slugs as $slug => $parameters) {
			if (!$options[$slug]) $options[$slug] = $parameters['default'];	
		}

		if (!$options['units']) $options['units'] = 'imperial';
		if (!$options['region_name']) $options['region_name'] = get_bloginfo( 'name' );

		if ($blog_id) restore_current_blog();

		return $options;
	}

	public function get_sites() {
		$sites = wp_get_sites();
        $sites_details = array();
        foreach ($sites as $site) {
            $details = get_blog_details($site['blog_id']);
            $options = $this->get_mobile_app_options($site['blog_id']);
            foreach ($options as $key => $value) {
            	$details->$key = $value;
            }
            array_push($sites_details, $details);
        }
        return $sites_details;
	}

	public function get_all_contents( $request ) {
		if ($request['deleted']) {
			return $this->get_all_deleted_contents($request);
		}
		$posts = $this->get_all_items( 'post', $request );
		$pages = $this->get_all_items( 'page', $request );
		$contents = array_merge((array) $posts, (array) $pages);
		$response = rest_ensure_response( $contents );
		return $response;
	}

	private function get_all_deleted_contents($request) {
		$query_args = array(
    	'post_type' => 'any',
    	'post_status' => array('pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')    
		);
		if ($request['after']) {
			// Get all posts that were modified after the date
			$query_args['date_query'] = array(
									array(
									'column' => 'post_modified_gmt',
									'after' => $request['after'],
									),
								);
		}
		$posts_query = new WP_Query();
		$query_result = $posts_query->query( $query_args );
		$posts = array();
		foreach ( $query_result as $post ) {
			$posts[] = $post->ID;
		}
		return $posts;
	}

	// Example call to get all contents:
	// http://dynonobel.wpengine.com/wp-json/mobile-app/v1/contents
	// Example call to get all contents modified after Oct 6 2016
	// http://dynonobel.wpengine.com/wp-json/mobile-app/v1/contents?after=2016-10-06T00:00:01

	private function get_all_items($post_type, $request) {
		$rest_posts_controller = new WP_REST_Posts_Controller($post_type);
		$query_args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'post_status' => 'publish',
			);
		if ($request['after']) {
			// Get all posts that were modified after the date
			$query_args['date_query'] = array(
									array(
									'column' => 'post_modified_gmt',
									'after' => $request['after'],
									),
								);
		}
		$posts_query = new WP_Query();
		$query_result = $posts_query->query( $query_args );
		$posts = array();
		foreach ( $query_result as $post ) {
			if ( ! $rest_posts_controller->check_read_permission( $post ) ) {
				continue;
			}

			$data = $rest_posts_controller->prepare_item_for_response( $post, $request );
			$posts[] = $rest_posts_controller->prepare_response_for_collection( $data );
		}
		return $posts;
	}
}
$mobile_app_custom_api_calls = new DynoMobileAppCustomApiCalls();

/* 
 * Retrieve this value with:
 * $mobile_app_options = get_option( 'mobile_app_options' ); // Array of All Options
 * $slug_guide = $mobile_app_options['slug_guide']; // Guide Slug
 * $slug_library = $mobile_app_options['slug_library']; // Library Slug
 * $slug_more = $mobile_app_options['slug_more']; // More Slug
 * $units = $mobile_app_options['units']; // Units
 * $region_name = $mobile_app_options['region_name']; // Region Name
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

