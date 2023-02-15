<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_block_type( 'wpm-block/category-block-list', [ 'render_callback' => 'cat_block' ] );

  function cat_block($attributes) {

	( !empty( $attributes['title'] ) ) ? $block_title = $attributes['title'] : $block_title = ''; 
	( !empty( $attributes['categoryList'] ) ) ? $block_cat = $attributes['categoryList'] : $block_cat = '';
	( !empty( $attributes['numberPost'] ) ) ? $block_number = $attributes['numberPost'] : $block_number = -1;
	( !empty( $attributes['postList'] ) ) ? $postLists = $attributes['postList'] : $postLists = [];
	( !empty( $attributes['authorList'] ) ) ? $authorlist = $attributes['authorList'] : $authorlist = '';
	
	//var_dump($authorlist);
	// Maping Post list ID's
	$integerIDs = array_map( function($value) { return (int)$value; }, $postLists );	

    $args = array(  
		'post_type' => 'post',
		'posts_per_page' => $block_number,
		'post__not_in' => $integerIDs,
		'post_status'    => 'publish',
		'tax_query' => '',
		'author__in' => -1,

	);

	if ( !empty( $block_cat ) ) {
		$args['tax_query'] =[
			array(
				'taxonomy' => 'category',
				'terms' => $block_cat,
			),
		];
	}

	if ( empty( $authorlist[0] ) ) {
		$args['author__in'] = '';
	}else{
		$args['author__in'] = $authorlist;
	}
	var_dump(  $args );
	
	


	$loop = new WP_Query( $args ); 
	$dotml = '';
	$dotml .= '<div class="blog-category-wpm-section">';
	


	if($block_title) {
		$dotml .= '<h2>' . esc_html( $block_title ) . '</h2>';
	}else{
		$dotml .= '<h2>Read more about '; 
		if( $block_cat !== -1 ){
			foreach( $block_cat as $cd ){
				$dotml .= '<span>' . get_cat_name( $cd).'</span>, ';
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