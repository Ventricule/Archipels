<?php get_header(); ?>

<?php
$type = $_GET['type'];
if($type == 'ajax') {
	include('/ajax-search.php');
} else {
	include('/normal-search.php');
}
?>

<?php get_footer(); ?>