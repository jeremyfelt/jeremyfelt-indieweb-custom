<?php

namespace JIC\Common;

add_action( 'plugins_loaded', __NAMESPACE__ . '\remove_indieweb_styles', 15 );
add_action( 'init', __NAMESPACE__ . '\remove_indieweb_added_on_init', 15 );

function remove_indieweb_styles() {
	remove_action( 'wp_enqueue_scripts', array( 'IndieWeb_Plugin', 'enqueue_style' ) );
	remove_action( 'wp_enqueue_scripts', array( 'Semantic_Linkbacks_Plugin', 'enqueue_scripts' ) );
}

function remove_indieweb_added_on_init() {

	// Remove the injection of the Semantic_Linkbacks_Walker_Comment walker when displaying
	// comments. This is added on the init action by the Semantic Linkbacks plugin.
	remove_filter( 'wp_list_comments_args', array( 'Linkbacks_Handler', 'filter_comment_args' ) );
}
