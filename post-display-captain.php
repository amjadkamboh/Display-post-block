<?php
/**
 * Plugin Name:       Post Listig block
 * Description:       
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            WP Minds
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-display-captain
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_post_display_captain_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_post_display_captain_block_init' );


require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
require_once plugin_dir_path( __FILE__ ) . 'src/index.php';


/* change amount of posts returned by REST API to 400 */
function display_captain_block_rest_posts_per_page( $args, $request ) {
    $max = max( (int)$request->get_param( 'per_page' ), 400 );
    $args['posts_per_page'] = $max;
    return $args;
}
add_filter( 'rest_post_query', 'display_captain_block_rest_posts_per_page', 10, 2 );