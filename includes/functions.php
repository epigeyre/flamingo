<?php

function flamingo_plugin_url( $path = '' ) {
	$url = plugins_url( $path, FLAMINGO_PLUGIN );

	if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}

function flamingo_array_flatten( $input ) {
	if ( ! is_array( $input ) ) {
		return array( $input );
	}

	$output = array();

	foreach ( $input as $value ) {
		$output = array_merge( $output, flamingo_array_flatten( $value ) );
	}

	return $output;
}

/**
 * Move a spam to the Trash
 *
 * @since 2.1
 *
 * @see wp_trash_post()
 *
 */
function flamingo_schedule_move_trash() {

	// abort if FLAMINGO_MOVE_TRASH_DAYS is set to zero or in minus
	if ( (int) FLAMINGO_MOVE_TRASH_DAYS <= 0 ) {
		return true;
	}

	global $wpdb;
	$move_timestamp = time() - ( DAY_IN_SECONDS * FLAMINGO_MOVE_TRASH_DAYS );

	// get posts ids Array to move to the trash
	$posts_to_move = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_spam_meta_time' AND meta_value < %d", $move_timestamp ) );

	// post id's loop
	foreach ( (array) $posts_to_move as $post_id ) {
		$post_id = (int) $post_id;

		if ( ! $post_id ) {
			continue;
		}

		// trash it now
		wp_trash_post( $post_id );
	}
}
