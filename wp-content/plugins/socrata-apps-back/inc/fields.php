<?

$prefix = 'socrata_apps_';

$fields = array(	
	array( // Single checkbox
		'label'	=> 'Socrata Certified', // <label>
		'desc'	=> 'Yes, this app meets Socrata Certification standards.', // description
		'id'	=> $prefix.'certified', // field id and name
		'type'	=> 'checkbox' // type of field
	),		
	array( // Text Input
		'label'	=> 'Company Name', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'company_name', // field id and name
		'type'	=> 'text' // type of field
	),		
	array( // Text Input
		'label'	=> 'Company Website', // <label>
		'desc'	=> 'Full URL including http:// or https:// (Required)', // description
		'id'	=> $prefix.'company_website', // field id and name
		'type'	=> 'text' // type of field
	),	
	array ( // Checkbox group
		'label'	=> 'App Type', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'type', // field id and name
		'type'	=> 'checkbox_group', // type of field
		'options' => array ( // array of options
			'one' => array ( // array key needs to be the same as the option value
				'label' => 'Web App', // text displayed as the option
				'value'	=> 'Web App' // value stored for the option
			),
			'two' => array (
				'label' => 'Mobile App',
				'value'	=> 'Mobile App'
			),
			'three' => array (
				'label' => 'Desktop App',
				'value'	=> 'Desktop App'
			)
		)
	),
	array ( // Checkbox group
		'label'	=> 'Platform', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'platform', // field id and name
		'type'	=> 'checkbox_group', // type of field
		'options' => array ( // array of options
			'one' => array ( // array key needs to be the same as the option value
				'label' => 'Web', // text displayed as the option
				'value'	=> 'Web' // value stored for the option
			),
			'two' => array (
				'label' => 'iOS',
				'value'	=> 'iOS'
			),
			'three' => array (
				'label' => 'Android',
				'value'	=> 'Android'
			),
			'four' => array (
				'label' => 'Windows Phone',
				'value'	=> 'Windows Phone'
			),
			'five' => array (
				'label' => 'Mac OS',
				'value'	=> 'Mac OS'
			),
			'six' => array (
				'label' => 'Linux',
				'value'	=> 'Linux'
			),
			'seven' => array (
				'label' => 'Windows',
				'value'	=> 'Windows'
			)
		)
	),
	array ( // Radio group
		'label'	=> 'Cost', // <label>
		'desc'	=> 'Is there a cost for this app?', // description
		'id'	=> $prefix.'cost', // field id and name
		'type'	=> 'radio', // type of field
		'options' => array ( // array of options
			'one' => array ( // array key needs to be the same as the option value
				'label' => 'Free App', // text displayed as the option
				'value'	=> 'Free App' // value stored for the option
			),
			'two' => array (
				'label' => 'Paid App',
				'value'	=> 'Paid App'
			)
		)
	),	
	array( // Text Input
		'label'	=> 'App Website', // <label>
		'desc'	=> 'Site where the user can get the app or more information. Include the full URL including http:// or https://', // description
		'id'	=> $prefix.'app_website', // field id and name
		'type'	=> 'text' // type of field
	),
	array( // Text Input
		'label'	=> 'Demo Website', // <label>
		'desc'	=> 'Full URL including http:// or https:// (Required)', // description
		'id'	=> $prefix.'demo_website', // field id and name
		'type'	=> 'text' // type of field
	),	
	array( // Textarea
		'label'	=> 'Short Description', // <label>
		'desc'	=> 'A brief description of the app.', // description
		'id'	=> $prefix.'short_description', // field id and name
		'type'	=> 'textarea' // type of field
	),
	array(
	    'label' => 'App Details',
	    'desc'  => 'Long description of the app. Features and benefits. Special instructions.',
	    'id'    => 'editorField',
	    'type'  => 'editor',
	    'sanitizer' => 'wp_kses_post',
	    'settings' => array(
	        'textarea_name' => 'editorField'
	    )
	),
	array( // Image ID field
		'label'	=> 'App Icon', // <label>
		'desc'	=> 'SIZE: 500px X 500px. (Required)', // description
		'id'	=> $prefix.'app_icon', // field id and name
		'type'	=> 'image' // type of field
	),	
	array( // Image ID field
		'label'	=> 'Splash Image', // <label>
		'desc'	=> 'SIZE: 1024px X 652px. (Required)', // description
		'id'	=> $prefix.'splash_image', // field id and name
		'type'	=> 'image' // type of field
	),	
	array( // Image ID field
		'label'	=> 'Screen Shot One', // <label>
		'desc'	=> 'SIZE: 1024px X 652px. Used in the slider. (Required)', // description
		'id'	=> $prefix.'screen_shot_one', // field id and name
		'type'	=> 'image' // type of field
	),	
	array( // Image ID field
		'label'	=> 'Screen Shot Two', // <label>
		'desc'	=> 'SIZE: 1024px X 652px. Used in the slider. (Optional)', // description
		'id'	=> $prefix.'screen_shot_two', // field id and name
		'type'	=> 'image' // type of field
	),	
	array( // Image ID field
		'label'	=> 'Screen Shot Three', // <label>
		'desc'	=> 'SIZE: 1024px X 652px. Used in the slider. (Optional)', // description
		'id'	=> $prefix.'screen_shot_three', // field id and name
		'type'	=> 'image' // type of field
	),	
	array( // Text Input
		'label'	=> 'Developers Name', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'developer_name', // field id and name
		'type'	=> 'text' // type of field
	),	
	array( // Text Input
		'label'	=> 'Developer Website', // <label>
		'desc'	=> 'Full URL including http:// or https://', // description
		'id'	=> $prefix.'developer_website', // field id and name
		'type'	=> 'text' // type of field
	),
	array( // Text Input
		'label'	=> 'Support Website', // <label>
		'desc'	=> 'Full URL including http:// or https://', // description
		'id'	=> $prefix.'support_website', // field id and name
		'type'	=> 'text' // type of field
	),	
	array( // Text Input
		'label'	=> 'Version Number', // <label>
		'desc'	=> 'Example: v1.0', // description
		'id'	=> $prefix.'version_number', // field id and name
		'type'	=> 'text' // type of field
	),
	array( // jQuery UI Date input
		'label'	=> 'Last Updated', // <label>
		'desc'	=> 'Date the app was last updated.', // description
		'id'	=> $prefix.'last_updated', // field id and name
		'type'	=> 'date' // type of field
	),
	array( // Text Input
		'label'	=> 'File Size', // <label>
		'desc'	=> 'Example: 5MB, or Varies, etc.', // description
		'id'	=> $prefix.'file_size', // field id and name
		'type'	=> 'text' // type of field
	),	
	array( // Text Input
		'label'	=> 'Data Schema Link', // <label>
		'desc'	=> 'Full URL including http:// or https://', // description
		'id'	=> $prefix.'schema', // field id and name
		'type'	=> 'text' // type of field
	),
	array( // Single checkbox
		'label'	=> 'LeadGen Form', // <label>
		'desc'	=> 'Include Lead Generation Form', // description
		'id'	=> $prefix.'leadgen', // field id and name
		'type'	=> 'checkbox' // type of field
	),		
);

// Get and return the values for the URL and description
function get_socrata_apps_meta() {
  global $post;
  $socrata_apps_app_website = get_post_meta($post->ID, 'socrata_apps_app_website', true); // 0
  $socrata_apps_last_updated = get_post_meta($post->ID, 'socrata_apps_last_updated', true); // 1
  $socrata_apps_version_number = get_post_meta($post->ID, 'socrata_apps_version_number', true); // 2
  $socrata_apps_file_size = get_post_meta($post->ID, 'socrata_apps_file_size', true); // 3
  $socrata_apps_app_icon = get_post_meta($post->ID, 'socrata_apps_app_icon', true); // 4
  $socrata_apps_splash_image = get_post_meta($post->ID, 'socrata_apps_splash_image', true); // 5 
  $socrata_apps_screen_shot_one = get_post_meta($post->ID, 'socrata_apps_screen_shot_one', true); // 6
  $socrata_apps_screen_shot_two = get_post_meta($post->ID, 'socrata_apps_screen_shot_two', true); // 7 
  $socrata_apps_screen_shot_three = get_post_meta($post->ID, 'socrata_apps_screen_shot_three', true); // 8
  $socrata_apps_company_name = get_post_meta($post->ID, 'socrata_apps_company_name', true); // 9
  $socrata_apps_developer_name = get_post_meta($post->ID, 'socrata_apps_developer_name', true); // 10
  $socrata_apps_company_website = get_post_meta($post->ID, 'socrata_apps_company_website', true); // 11
  $socrata_apps_developer_website = get_post_meta($post->ID, 'socrata_apps_developer_website', true); // 12
  $socrata_apps_support_website = get_post_meta($post->ID, 'socrata_apps_support_website', true); // 13
  $socrata_apps_short_description = get_post_meta($post->ID, 'socrata_apps_short_description', true); // 14
  $editorField = get_post_meta($post->ID, 'editorField', true); // 15
  $socrata_apps_certified = get_post_meta($post->ID, 'socrata_apps_certified', true); // 16
  $socrata_apps_platform = get_post_meta($post->ID, 'socrata_apps_platform', true); // 17
  $socrata_apps_type = get_post_meta($post->ID, 'socrata_apps_type', true); // 18
  $socrata_apps_cost = get_post_meta($post->ID, 'socrata_apps_cost', true); // 19  
  $socrata_apps_schema = get_post_meta($post->ID, 'socrata_apps_schema', true); // 20  
  $socrata_apps_leadgen = get_post_meta($post->ID, 'socrata_apps_leadgen', true); // 21  
  $socrata_apps_demo_website = get_post_meta($post->ID, 'socrata_apps_demo_website', true); // 22

  return array(
  $socrata_apps_app_website,
  $socrata_apps_last_updated,
  $socrata_apps_version_number,
  $socrata_apps_file_size,
  $socrata_apps_app_icon,
  $socrata_apps_splash_image,
  $socrata_apps_screen_shot_one,
  $socrata_apps_screen_shot_two,
  $socrata_apps_screen_shot_three,
  $socrata_apps_company_name,
  $socrata_apps_developer_name,
  $socrata_apps_company_website,
  $socrata_apps_developer_website,
  $socrata_apps_support_website,
  $socrata_apps_short_description,
  $editorField,
  $socrata_apps_certified,
  $socrata_apps_platform,
  $socrata_apps_type,
  $socrata_apps_cost,
  $socrata_apps_schema,
  $socrata_apps_leadgen,
  $socrata_apps_demo_website
  );
}

/**
 * Instantiate the class with all variables to create a meta box
 * var $id string meta box id
 * var $title string title
 * var $fields array fields
 * var $page string|array post type to add meta box to
 * var $js bool including javascript or not
 */
$socrata_apps_box = new socrata_apps_custom_add_meta_box( 'socrata_apps_box', 'APP SPECIFICATIONS', $fields, 'socrata_apps', true );


