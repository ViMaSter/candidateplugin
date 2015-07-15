<?php
/*
	Plugin Name: SPD Pfungstadt - Kandidatenplugin 2015
	Author URI: http://www.vimaster.de/
	Description: Auflistung aller Kandidaten inklusive Teilbereich und Foto
	Version: 1.0
	Author: Vincent Mahnke
	
	Freepik (http://www.freepik.com) from www.flaticon.com is licensed under CC BY 3.0 (http://creativecommons.org/licenses/by/3.0/)
*/

// Register page type
add_action('init', 'registerCandidatePanel');
function registerCandidatePanel() {
	$pluginDir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$args = array(
		'label' => __('Kandidaten'),
		'singular_label' => __('Kandidat'),
		'capability_type' => 'page',
		'public' => true,
		'has_archive' => true,
		'revisions' => true,
		'trackbacks' => true,
		'taxonomies' => array("department"),
		'hierarchical' => true,
		'custom-fields' => false,
		'menu_icon' => $pluginDir.'candidate.png',
		'supports' => array(
			'title',
			'editor',
			'trackbacks',
			'author',
			'thumbnail',
			'page-attributes',
			'has_archive',
			'comments'
		)
	);
	register_post_type( 'candidate' , $args);
}

function generateCandidatePage() {
	if (!current_user_can('editor'))  {
		wp_die( __("Sie besitzen nicht ausreichend Rechte, um diese Seite zu besuchen.") );
	} 
}

// Register taxonomy
add_action( 'init', 'create_department_taxonomy' );
function create_department_taxonomy() {
	register_taxonomy(
		'department',
		'candidate',
		array(
			'label' => __( 'Abteilung' ),
			'rewrite' => array( 'slug' => 'department' )
		)
	);
}

add_action('add_meta_boxes', 'add_candidate_meta_box' );
function add_candidate_meta_box() {
	add_meta_box(
		'candidate_sectionid',
		__('Kandidaten Meta-Daten', 'candidate_textdomain'),
		'candidate_meta_box_callback',
		"candidate"
	);
}

function candidate_meta_box_callback( $post ) {
	wp_nonce_field( 'candidate_save_meta_box_data', 'candidate_meta_box_nonce' );

	$value = get_post_meta($post->ID, 'candidate_hobbies', true);
	echo '<label for="candidate_hobbies">';
	_e('Hobbys:', 'candidate_textdomain');
	echo '</label> ';
	echo '<input type="text" id="candidate_hobbies" name="candidate_hobbies" value="' . esc_attr( $value ) . '" /><br />';

	$value = get_post_meta($post->ID, 'candidate_age', true);
	echo '<label for="candidate_age">';
	_e('Alter:', 'candidate_textdomain');
	echo '</label> ';
	echo '<input type="number" min="0" max="100" id="candidate_age" name="candidate_age" value="' . esc_attr( $value ) . '" /><br />';

	$value = get_post_meta($post->ID, 'candidate_job', true);
	echo '<label for="candidate_job">';
	_e('Job:', 'candidate_textdomain');
	echo '</label> ';
	echo '<input type="text" id="candidate_job" name="candidate_job" value="' . esc_attr( $value ) . '" /><br />';

	$value = get_post_meta($post->ID, 'candidate_listposition', true);
	echo '<label for="candidate_listposition">';
	_e('Listenplatz:', 'candidate_textdomain');
	echo '</label> ';
	echo '<input type="number" id="candidate_listposition" name="candidate_listposition" value="' . esc_attr( $value ) . '" /><br />';
}

add_action( 'save_post', 'candidate_save_meta_box_data' );
function candidate_save_meta_box_data($post_id) {
	// Check if our nonce is set
	if ( !isset($_POST['candidate_meta_box_nonce']) ) {
		return;
	}

	// Verify that the nonce is valid
	if ( !wp_verify_nonce($_POST['candidate_meta_box_nonce'], 'candidate_save_meta_box_data') ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( !current_user_can('edit_page', $post_id) ) {
			return;
		}
	} else {
		if ( !current_user_can('edit_post', $post_id) ) {
			return;
		}
	}


	// Custom fields
	$customFieldPrefix = "candidate_";
	$customFields = array("hobbies", "age", "job", "listposition");

	foreach ($customFields as $customField) {
		$fieldName		= $customFieldPrefix . $customField;

		// Clean values from client...
		$data			= sanitize_text_field( $_POST[$fieldName] );

		// ...and save them
		update_post_meta( $post_id, $fieldName, $data );
	}
}
?>