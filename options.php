<?php

/**
* register wporg_settings_init to the admin_init action hook
*/
function pbtt_settings_init() {

	register_setting('pbtt', 'pbtt_zone');
	register_setting('pbtt', 'pbtt_posttype');

	add_settings_section(
	'pbtt_options_active',
	__( "Enable", 'pbtt' ),
	'pbtt_options_active_desc',
	'pbtt'
	);


	add_settings_field(
		'pbtt_options_posttypes',
		__( "Post types", 'pbtt' ),
		'pbtt_options_posttypes_fields',
		'pbtt',
		'pbtt_options_active'
	);

	add_settings_field(
		'pbtt_options_zones',
		__( "Postbox zones", 'pbtt' ),
		'pbtt_options_zone_fields',
		'pbtt',
		'pbtt_options_active'
	);
}
add_action('admin_init', 'pbtt_settings_init');

// section content cb
function pbtt_options_active_desc() {
	_e( "Select where the plugin is enabled.", 'pbtt' );
}

function pbtt_options_posttypes_fields() {

	$check = get_option( 'pbtt_posttype' );

	foreach (get_post_types( ['public'=> true], 'object') as  $posttype) {

		$tple  =	'<div>';
		$tple .=		'<input type="checkbox" name="pbtt_posttype[]" id="pbtt_posttype_%s" value="%s"%s/>';
		$tple .=		'<label for="pbtt_posttype_%s">%s</label>';
		$tple .=	'</div>';

		printf($tple,
			$posttype->name,
			$posttype->name,
			( is_array($check) && in_array($posttype->name, $check) ? ' checked' : '' ),
			$posttype->name,
			$posttype->label
		);
	}
}

function pbtt_options_zone_fields() {

	$zones = array(
		'normal'		=> __( "Normal <em>(below post content)</em>.", 'pbtt' ),
		'advanced'	=> __( "Advanced <em>(below normal zone)</em>.", 'pbtt' ),
		'side'			=> __( "Side <em>(next to post content)</em>.", 'pbtt' ),
	);

	$check = get_option( 'pbtt_zone' );

	foreach ( $zones as $zId => $zName ) {


		$tple  =	'<div>';
		$tple .=		'<input type="checkbox" name="pbtt_zone[]" id="pbtt_zone_%s" value="%s"%s/>';
		$tple .=		'<label for="pbtt_zone_%s">%s</label>';
		$tple .=	'</div>';

		printf($tple,
			$zId,
			$zId,
			( is_array($check) && in_array($zId, $check) ? ' checked' : '' ),
			$zId,
			$zName
		);
	}
}

function pbtt_options_page() {
	add_submenu_page(
		'tools.php',
		__( "Postbox to Tab options", 'pbtt' ),
		__( "Postbox to Tab", 'pbtt' ),
		'manage_options',
		'pbtt',
		'pbtt_options_page_html'
	);
}
add_action('admin_menu', 'pbtt_options_page');

function pbtt_options_page_html() {

	if (!current_user_can('manage_options')) {
		return;
	}

	?>
	<div class="wrap">
		<h1><?= esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields('pbtt'); // security fields for the registered setting "pbtt_options"
			do_settings_sections('pbtt'); // setting sections and their fields
			submit_button(); // save settings button
			?>
		</form>
	</div>
	<?php
}
