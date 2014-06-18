<?php get_header(); ?>
<style>
body{
background:none;
}
#warning{
	display:none;
}
</style>

<div style="width:100%;">
	<?php
	// VARIABLES
	$son = $_GET['src'];
	echo do_shortcode('[audio src="'.$son.'"]');
	
	 ?>
 </div>

<?php get_footer(); ?>