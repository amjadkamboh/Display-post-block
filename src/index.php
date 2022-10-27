<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_block_type( 'wpm-block/category-block-list', [ 'render_callback' => 'cat_block' ] );

  function cat_block($attributes) {

    $block_title = esc_html($attributes['title'] );
    $block_cat = $attributes['categoryList'];
	$block_number = $attributes['numberPost'];
	$postLists = $attributes['postList'];
	
	$integerIDs = array_map(
		function($value) { return (int)$value; },
		$postLists
	);	

	$per_bge ="";
	if($block_number) {
		$per_bge = $block_number;
	}else{
		$per_bge = '-1';
	}

    $args = array(  
		'post_type' => 'post',
		'posts_per_page' => $per_bge,
		'post__not_in' => $integerIDs,
		'post_status'    => 'publish',
        'tax_query' => array(
			array(
				'taxonomy' => 'category',
				'terms' => $block_cat,
			)
		)
	);
	$loop = new WP_Query( $args ); 
	$dotml = '';
	$dotml .= '<div class="blog-category-wpm-section">';
	


	if($block_title) {
		$dotml .= '<h2>' . esc_html( $block_title ) . '</h2>';
	}else{
		$dotml .= '<h2>Read more about '; 
		if( $block_cat ){
			foreach( $block_cat as $cd ){
				$dotml .= '<span>' . get_cat_name( $cd).'</span>';
			}
		}
		$dotml .= '</h2>';
	}
	$dotml .= '<ol class="category-wpm-List">';
	if( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post(); 
		$dotml .= '<li><h3><a href="'. get_the_permalink() .'">'. get_the_title() .'</a></h3></li>';
		endwhile;

		wp_reset_postdata(); 
	}
	$dotml .= '</ol>';
	$dotml .= '</div>';


 return $dotml ;

  } 