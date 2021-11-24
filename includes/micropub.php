<?php

namespace JIC\Micropub;

add_filter( 'micropub_post_type', __NAMESPACE__ . '\filter_micropub_post_type', 10, 2 );
add_filter( 'micropub_post_content', __NAMESPACE__ . '\filter_micropub_post_content', 0, 2 );
add_filter( 'get_post_metadata', __NAMESPACE__ . '\filter_reply_to_metadata', 10, 4 );
add_filter( 'shortnotes_reply_to_name', __NAMESPACE__ . '\filter_shortnotes_reply_to_name', 10, 3 );

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

	if ( isset( $props['in-reply-to'] ) ) {
		return 'shortnote';
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

/**
 * Filter Shortnotes metadata to include the stored micropub
 * data as a reply to URL if one is available.
 *
 * @param null   $value No data by default.
 * @param int    $post_id  The post ID.
 * @param string $meta_key The meta key.
 * @param bool   $single   Whether a single value was requested.
 * @return string The reply-to URL.
 */
function filter_reply_to_metadata( $value, $post_id, $meta_key, $single ) {
	if ( 'shortnotes_reply_to_url' !== $meta_key ) {
		return $value;
	}

	$reply_to = get_post_meta( $post_id, 'mf2_in-reply-to', $single );

	if ( ! $reply_to ) {
		return $value;
	}

	if ( is_string( $reply_to ) ) {
		return $reply_to;
	}

	if ( wp_is_numeric_array( $reply_to ) ) {
		return $reply_to[0];
	}

	if ( is_array( $reply_to ) && isset( $reply_to['url'] ) ) {
		return $reply_to['url'];
	}

	return $value;
}

/**
 * Filters the text used for the reply-to name.
 *
 * @param string   $reply_to_name The current text.
 * @param \WP_Post $post          A shortnote's post object.
 * @param string   $reply_to_url  The reply-to URL.
 * @return string The modified reply-to name.
 */
function filter_shortnotes_reply_to_name( $reply_to_name, $post, $reply_to_url ) {
	if ( 'this post' === $reply_to_name ) {
		return $reply_to_url;
	}

	return $reply_to_name;
}
