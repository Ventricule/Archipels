<?php
add_action( 'wp_ajax_nopriv_afficherPanneau', 'afficher_panneau' );
add_action( 'wp_ajax_afficherPanneau', 'afficher_panneau' );

//add_action( 'wp_ajax_nopriv_save', 'save' );
add_action( 'wp_ajax_save', 'save' );

//add_action( 'wp_ajax_nopriv_saveCoord', 'saveCoord' );
add_action( 'wp_ajax_saveCoord', 'saveCoord' );

//add_action( 'wp_ajax_nopriv_addElement', 'addElement' );
add_action( 'wp_ajax_addElement', 'addElement' );

add_action('wp_ajax_is_user_logged_in', 'ajax_check_user_logged_in');
add_action('wp_ajax_nopriv_is_user_logged_in', 'ajax_check_user_logged_in');

add_action('wp_ajax_fm_search', 'quicksearch');
add_action('wp_ajax_nopriv_fm_search', 'quicksearch');

function afficher_panneau(){
	// VARIABLES
	$id = $_POST['id'];
	$objet = $_POST['objet'];

	$post = get_post($id);

	if ($objet == "element"){
		$reference = get_the_title($id);
		$legende = fabriqueLegende($id);
		$abrege = get_field('abrege', $id);


		$texte = apply_filters( 'the_content', trim( get_field('texte', $id) ) );
		$texte = str_replace('<p></p>', '', $texte);
		$texte = str_replace('<p> </p>', '', $texte);
		$texte = str_replace('<p>&nbsp;</p>', '', $texte);

		$classesA = get_field('classification_a', $id);
		$classesB = get_field('classification_b', $id);
		$avatars = get_field('avatars', $id);
		$type = get_field('type', $id);
		$tagID = get_query_var('cat', $id);
		
		
		if ($type == 'image'){
			$imageInfos = get_field('image', $id);
			$image = $imageInfos[sizes][large];
			$width = $imageInfos[sizes]['large-width'];
			$height = $imageInfos[sizes]['large-height'];
			$miniature = "<img src='".$image."' width='".$width."' height='".$height."' alt='".$imageInfos[url]."'/>";
		} else if ($type == 'video') {
			/*$videoUrl = get_field('vid_url', $id);
			$video = videoinfo($videoUrl);
			$videoImg = $video["thumb"];
			$videoUrl = $video["iframe"];
			$videoImg = "http://i1.ytimg.com/vi/".$videoRef."/hqdefault.jpg";
			$width = 420;
			$height = 315;
			$miniature = '<iframe width="'.$width.'" height="'.$height.'" src="'.$videoUrl.'" frameborder="0" allowfullscreen></iframe>';*/
			$video_url = get_field('vid_url',$id);
			$video_infos = videoinfo($video_url);
			$miniature = '<iframe width="960" height="720" src="'.$video_infos[iframe].'" frameborder="0" allowfullscreen></iframe>';
		} else if ($type == 'citation') {
			$citation = strip_tags(get_field('citation',$id), '<br><em><strong><b><i>');
			$miniature = "<div class='citation'><p>".$citation."</p></div>";
		} else if ($type == 'hyperlien') {
			$hyperlien = get_field('hyperlien',$id);
			$miniature = "<div class='hyperlien'><p><a href=".$hyperlien." target='_blank'>".$hyperlien."</a></p></div>";
		}
		else if ($type == 'hyperlien') {
			$hyperlien = get_field('hyperlien',$id);
			$miniature = "<div class='hyperlien'><p><a href=".$hyperlien." target='_blank'>".$hyperlien."</a></p></div>";
		}
		else if ($type == 'son') {
			$son = get_field('son',$id);
			$miniature = '<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="audioplayer/?src='.$son[url].'"></iframe>';
		}
		else if ($type == 'carte') {
			$miniature = get_field('carte_iframe',$id);
			$miniature .= '<div class="zoomCarte"></div>';
		}
		
		?>
		<div class="en-tete clear">
			
			<div class="titre bloc une marge">
				<h2><?php echo $reference ?></h2>
				<p class="detail"><?php echo $abrege ?></h2>
			</div>
			
			<div class="legende bloc une">
				<p class="detail"><?php echo $legende ?></p>
			</div>
			
			<div class="clear"></div>
		</div>

		<div class="miniature deux clear type-<?php echo $type ?>">
			<?php echo $miniature ?>
		</div>

		<div class="texte bloc clear">
			
			<div class="meta une">
				<div class="classification une">
					<h4>Classification</h4>
					<?php
					if ($classesA){
							$classe=$classesA;
							echo ('<div class="boite '.$classe.'">
								<img src="'.get_bloginfo('template_url').'/img/'.$classe.'.svg" width="15" height="15" />
								&nbsp;<span class="detail nomClasse"><a href="#">'.$classe.'</a></span>
							</div>');
					}
					if ($classesB){
						foreach($classesB as $classe){
							echo ('<div class="boite '.$classe.'">
								<img src="'.get_bloginfo('template_url').'/img/'.$classe.'.svg" width="15" height="15" />
								&nbsp;<span class="detail nomClasse"><a href="#">'.$classe.'</a></span>
							</div>');
						}
					}					
					?>
				</div>
				<div class="planches bloc une">
					<h4>Tables</h4>
					<?php
						// WP_Query arguments
						$args = array (
							'post_type'              => 'table',
							'meta_query'             => array(
								array(
									'key'       => 'elementsList',
									'value'     => $id,
									'compare'   => 'LIKE',
								),
							),
						);

						// The Query
						$query = new WP_Query( $args );

						// The Loop
						if ( $query->have_posts() ) {
							while ( $query->have_posts() ) {
								$query->the_post();
								echo '<div class="couleur" style="background:'.get_field('couleur').';"><a href="'.get_permalink().'"></a></div><a href="'.get_permalink().'">'.get_field('abrege').'</a><br>';
							}
						} else {
							// no posts found
						}

						// Restore original Post Data
						wp_reset_postdata();
					?>
				</div>
			</div>
			<span class="texte-element"><?php echo $texte ?></span>
		</div>
		<?php
		if ($avatars){
		?>
			<div class="avatars">
				<h4 class="tips" title="Un avatar est un élément similaire mais observé avec un autre point de vu.">Avatars :</h4>
				<?php
				$numItems = count($avatars);
				$i = 0;
				foreach($avatars as $avatar){
					$abrege=get_field('abrege', $avatar);
					echo ('<a href="#'.$avatar.'" name="'.$avatar.'" >'.$abrege.'</a>');
					if(++$i !== $numItems) {
						echo "&thinsp;, ";
					 }
				}
				?>
			</div>
		<?php
		}
		?>



		
		<?php

	} else if ($objet=='table') {
		$reference = get_the_title($id);
		$abrege = get_field('abrege', $id);
		$couleur = get_field('couleur', $id);

		$elements = get_post_meta( $id, 'elements', true );
		$legendes ='';
		
		if ($elements){
			$i=0;
			foreach ($elements as $element){
				$i++;
				$i==1 ? $legendes .= "" : $legendes .= "&nbsp;&nbsp;—&nbsp;&nbsp;";
				$legendes .= $i.".&nbsp;".fabriqueLegende($element['id']);
			}
		}
		$texte = "<p class='detail'>".$legendes."</p>";
		$texte .= get_field('texte', $id);
		$thumb = get_post_meta( $id,'tableThumb', true );
		$miniature = "<img src='".$thumb."' width='420' height='350' >";
		?>
		<div class="en-tete clear">
			
			<div class="titre bloc une marge">
				<h2><?php echo $reference ?></h2>
				<p class="detail"><?php echo $abrege ?></p>
			</div>
			
			<div class="blocCouleur une" style="background:<?php echo $couleur; ?>"></div>
			
			<div class="clear"></div>
		</div>

		<div class="miniature table deux clear">
			<?php echo $miniature ?>
		</div>

		<div class="texte bloc clear">
			<?php echo $texte ?>
		</div>

		<div class="meta clear">
		</div>

		
		<?php
	}
	exit;
}

function save(){
	// VARIABLES
	$elements = $_POST['elements'];
	$table_id = $_POST['table_id'];
	$table_position = $_POST['table_position'];
	$elementsList = $_POST['elementsList'];
	$notes = $_POST['notes'];
	$lignes = $_POST['lignes'];

	update_post_meta($table_id, 'elements', $elements);
	update_post_meta($table_id, 'elementsList', $elementsList);
	update_post_meta($table_id, 'table_position', $table_position);
	update_post_meta($table_id, 'notes', $notes);
	update_post_meta($table_id, 'lignes', $lignes);

	// Fabriquer miniature de la table
	$thumb = createTableThumb($table_id);
	
	update_post_meta($table_id, 'tableThumb', $thumb);
	
	exit;
}

function saveCoord(){
	$element = $_POST['element'];
	$coord = $_POST['coord'];

	update_post_meta($element, 'coord', $coord);
	die();
}

function addElement(){
	$id = $_POST['id'];
	$top = $_POST['top'];
	$left = $_POST['left'];
	echo createElement($id, '', $top, $left, '', '', '', '', '', '', '', '', '');
	die();
}

function quicksearch() {
	global $wpdb;

	if (strlen($_GET['s'])>2) {
	
		global $wp_query;

		$wp_query = new WP_Query();
		$wp_query->query_vars['s'] = $_GET['s'];

		relevanssi_do_query($wp_query);

		if($wp_query->have_posts()) {
			  while ($wp_query->have_posts()) : $wp_query->the_post();
					 ?>
					 <p class="detail"><a href="#<?php echo get_the_ID(); ?>" class="non-souligne" ><?php echo '['.get_field('abrege').'] '.get_the_title();?></a></p>
					 <?php
			  endwhile;
		}

	}
	else echo '';
	die();
}
?>