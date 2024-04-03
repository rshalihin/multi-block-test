<?php
/**
 * Plugin Name:       Demo Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       demo-block
 *
 * @package           create-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function demo_block_demo_block_block_init() {
	$blocks = array(
		'first-block',
		'second-block',
		'zero-block',
	);
	foreach ( $blocks as $block ) {
		register_block_type( __DIR__ . "/build/{$block}" );
	}
	add_action( 'admin_enqueue_scripts', 'demo_block_admin_enqueue_scripts' );

}
add_action( 'init', 'demo_block_demo_block_block_init' );

function demo_block_admin_enqueue_scripts() {
	$handle = 'create-block-zero-block-editor-script';

	$data       = array();
	$test_args  = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);
	$test_query = new WP_Query( $test_args );
	while ( $test_query->have_posts() ) {
		$test_query->the_post();
		$data[] = array(
			'title'   => get_the_title(),
			'content' => get_the_content(),
			'excerpt' => get_the_excerpt(),
			'url'     => get_the_permalink(),
		);

		// Restore original Post Data.
		wp_reset_postdata();
	}

	// wp_localize_script( $handle, 'demoBlockData', $data );

	$handler   = 'create-block-first-block-editor-script';
	$user_data = get_transient( 'demoUser' );

	if ( ! $user_data ) {
		$response  = wp_remote_get( 'https://jsonplaceholder.typicode.com/users' );
		$user_data = json_decode( wp_remote_retrieve_body( $response ) );

		set_transient( 'demoUser', $user_data, 7 * DAY_IN_SECONDS );
	}
	wp_localize_script( $handler, 'jsonUser', $user_data );

}


