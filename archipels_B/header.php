<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
		
		<!-- dns prefetch -->
		<link href="//www.google-analytics.com" rel="dns-prefetch">
		
		<!-- meta -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>">
		
		<!-- icons -->
		<link href="<?php echo get_template_directory_uri(); ?>/img/icons/favicon.ico" rel="shortcut icon">
		<link href="<?php echo get_template_directory_uri(); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed">
			
		<!-- css + javascript -->
		<?php wp_head(); ?>
		<script>
			!function(){
				// configure legacy, retina, touch requirements @ conditionizr.com
				conditionizr()
			}()
			var admin_url = "<?php echo admin_url(); ?>";
			var home_url = "<?php echo home_url(); ?>";
			function have_modification_right(){
				return <?php echo have_modification_right(); ?>;
			}
		</script>
	</head>
	<body <?php body_class(); ?>>
	
	<div id="warning">
	<?php
	if (is_user_logged_in() && !is_search() ){
		$user_info = get_userdata( get_option("user_control") );
		$username = $user_info->user_login;
		echo "<div class='reduit'>";
		if ( get_option("user_control") == "online" ){
			//Archipels est libre
			echo "<img src='".get_template_directory_uri()."/img/icons/unlocked.png' title='Archipels est libre' class='tips-left'>";
		} else {
			if (get_option("user_control") != get_current_user_id()){
				echo "<img src='".get_template_directory_uri()."/img/icons/locked.png' title='Verrouillé par ".$username."' class='tips-left'>";
			} else {
				echo "<img src='".get_template_directory_uri()."/img/icons/locked.png' title='Vous avez verrouillé Archipels' class='tips-left'>";
			}
		}
		if (substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1') {
			echo '<img src="'.get_template_directory_uri().'/img/icons/local.png" title="Vous êtes sur la copie locale d\'Archipels" class="tips-left">';			
		} else {
			echo "<img src='".get_template_directory_uri()."/img/icons/internet.png' title='Vous êtes sur internet.' class='tips-left'>";	
		}
		if ( have_modification_right() ) {
			echo '<img src="'.get_template_directory_uri().'/img/icons/writable.png" title="Vous pouvez modifier Archipels" class="tips-left">';
			
		} else {
			echo "<img src='".get_template_directory_uri()."/img/icons/protected.png' title='Vous ne pouvez pas modifier Archipels' class='tips-left'>";	
		}
		echo "<hr>";
		if ( get_option("user_control") == "online" ){
			echo '<a href="http://localhost/www.archipels.org/atlas/wp-admin/tools.php?page=dbs_options&dbs_action=sync&url=http%3A%2F%2Fwww.archipels.org%2Fatlas" class="non-souligne"><img src="'.get_template_directory_uri().'/img/icons/sync.png" title="Verrouiller et travailler hors ligne" class="tips-left"></a>';			
		} else {
			echo '<a href="http://localhost/www.archipels.org/atlas/wp-admin/tools.php?page=dbs_options&dbs_action=sync&url=http%3A%2F%2Fwww.archipels.org%2Fatlas" class="non-souligne"><img src="'.get_template_directory_uri().'/img/icons/sync.png" title="Envoyer les modifications et déverrouiller" class="tips-left"></a>';
		}
		echo "<div class='alert hidden tips-left' title='Information importante'>!<br></div>";

		echo "</div>";
		if ( get_option("user_control") == "online" ){
			//Archipels est libre
			if ( !is_connected() ) {
				echo "<p class='detail message'>Pour modifier Archipels sur votre ordinateur verrouillez-le ( 
				<a href='http://localhost/www.archipels.org/atlas/wp-admin/tools.php?page=dbs_options&dbs_action=sync&url=http%3A%2F%2Fwww.archipels.org%2Fatlas' class='non-souligne'><img src='".get_template_directory_uri()."/img/icons/sync.png' title='Verrouiller et travailler hors ligne' class='tips-left'></a> 
				 ). Sinon travaillez en ligne sur <a href='http://www.archipels.org'>www.archipels.org</a>.</p>";
			}
		} else if (get_option("user_control") == get_current_user_id() ) {
			if ( !is_connected() ) {
				echo "<p class='detail message'>Les modifications sont enregistrés sur votre ordinateur. Pensez à <a href='http://localhost/www.archipels.org/atlas/wp-admin/tools.php?page=dbs_options&dbs_action=sync&url=http%3A%2F%2Fwww.archipels.org%2Fatlas'>synchroniser</a> avec le serveur lorsque vous aurez accés à internet. <span class='close'>×</span></p>";
			} else {
				$localhostURL = "http://localhost/www.archipels.org" . $_SERVER['REQUEST_URI'] ;
				echo "<p class='detail message'>Vous avez verrouillé Archipels, pour le modifier ouvrez-le <a href=".$localhostURL.">sur votre ordinateur</a>.</p>";
			}
		}
	}
	?>
	</div>
	
	<div id="fullscreenView"></div>
	