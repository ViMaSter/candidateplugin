<?php
	// Get all candidates that are available...
	$candidateQuery = new WP_Query();
	$candidateQuery->query(array(
		"post_type" => array(
			"candidate"
		),
		"posts_per_page" => -1,
		"post_parent" => 0,

		// ...or are assigned a certain department
		'tax_query' => array(
			array(
				'taxonomy' => 'department',
				'field'    => 'slug',
				'terms'    => 'abteilung-1',
			)
		)
		// Removing the part between this, and the comment above, removes the department-filter
	));

	// Itterate over every candidate
	for($i = 0; $i < count($candidateQuery->posts); $i++) {
		$post = $candidateQuery->posts[$i];
		$imageURL = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), "full", false );

		// Useful values to echo here:
		// $post->ID														(1337)
		// $post->post_title												(Firstname Lastname)
		// $post->post_content												(Description of the candidate)
		// $imageURL														(URL of the full sized image of the candidate)
	
		// Since one candidate can be assigned multiple departments, we itterate again...
		$departments = wp_get_post_terms($post->ID, 'department', array("fields" => "all"));
		for($i = 0; $i < count($departments); $i++) {
			$department = $departments[$i];

			// ...and can echo some useful values here too
			// $department->name	(Display name of department)
			// $department->slug	(ID equivalent )
		}
	}
?>
