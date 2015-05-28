<?php
/*
	Plugin Name: SPD Walldorf - Kandidatenplugin 2015
	Author URI: http://www.vimaster.de/
	Description: Auflistung aller Kandidaten inklusive Teilbereich und Foto
	Version: 1.0
	Author: Vincent Mahnke
	
	Freepik (http://www.freepik.com) from www.flaticon.com is licensed under CC BY 3.0 (http://creativecommons.org/licenses/by/3.0/)
*/

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

function generateCandidatePage() {
	if (!current_user_can('editor'))  {
		wp_die( __("Sie besitzen nicht ausreichend Rechte, um diese Seite zu besuchen.") );
	} 
}

?>