<?php

namespace JIC\Micropub;

add_filter( 'micropub_post_type', __NAMESPACE__ . '\filter_micropub_likes', 10, 2 );

/**
 * Filter the post type for a like published with a micropub client.
 *
 * @param string $post_type The post type.
 * @param array  $input     An array of input args.
 * @return string The modified post type.
 */
function filter_micropub_likes( $post_type, $input ) {
	$props = mp_get( $input, 'properties' );

	if ( isset( $props['like-of'] ) ) {
		return 'like';
	}

	return $post_type;
}
