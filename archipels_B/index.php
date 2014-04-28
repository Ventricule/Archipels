<?php get_header();

if (  is_numeric( get_option('page_accueil') )){
	query_posts( array (
		'post_type'              => 'table',
		'p'         => get_option('page_accueil'),
	) );
} else {
	query_posts( array (
		'post_type'              => 'table',
		'posts_per_page'         => '1',
		'orderby'                => 'rand',
	) );
}
include('inc-table.php');

get_footer(); ?>