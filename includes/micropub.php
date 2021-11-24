<?php

namespace JIC\Micropub;

add_filter( 'micropub_post_type', __NAMESPACE__ . '\filter_micropub_post_type', 10, 2 );
add_filter( 'micropub_post_content', __NAMESPACE__ . '\filter_micropub_post_content', 0, 2 );

/**
 * Filter the post type for content published with a micropub client.
 *
 * @param string $post_type The post type.
 * @param array  $input     An array of input args.
 * @return string The modified post type.
 */
function filter_micropub_post_type( $post_type, $input ) {
	$props = mp_get( $input, 'properties' );

	if ( isset( $props['like-of'] ) ) {
		return 'like';
	}

	return $post_type;
}

/**
 * Filter the post content for content publishedd with a micropub client.
 *
 * @param string $content The post content.
 * @param array  $input   An array of input args.
 * @return string The modified post content.
 */
function filter_micropub_post_content( $content, $input ) {
	$props = mp_get( $input, 'properties' );

	// Remove content filtering if this is a reply.
	if ( isset( $props['in-reply-to'] ) ) {
		remove_filter( 'micropub_post_content', array( 'Micropub_Render', 'generate_post_content' ), 1, 2 );
	}

	return $content;
}
