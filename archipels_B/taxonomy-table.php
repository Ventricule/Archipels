<?php /* Template Name: Table */ get_header(); ?>
	
	<!-- section -->
	<section role="main">
	
		<h1></h1>
		
	<div id="ecran">
		<div id="table" title="<?php $term = $wp_query->queried_object; echo $term->slug; ?>" >
			<div id="centre">
			
				<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
				<?php
				// VARIABLES
				
				$titre = get_field('titre');
				$type = get_field('type');
				$tagID = get_query_var('cat');
				if ($type == 'image'){
					$image = get_field('image');
					$image = $image[sizes][full];
					$contenu =	"<img src=".$image." />";
				} else if ($type == 'video') {
					$videoIframe = get_field('video').'?showinfo=0&modestbranding=1&controls=0&color=white';
					$array = array();
					preg_match( '/src="//www.youtube.com/embed/([^"]*)"/i', $videoIframe, $array ) ;
					$videoRef = $array[1] ;
					$videoRef = explode($videoRef, "/");
					$videoRef = explode($videoRef[0], "?");
					$videoRef = $videoRef[0];
					$videoImg = "http://i1.ytimg.com/vi/".$videoRef."/hqdefault.jpg";
					$contenu = "<div class='couvercle'><div class='flechePlay'></div><img src=".$videoImg."/></div>";
					$contenu .= "<div class='capsule' title=".$videoIframe.">";
					$contenu .= "</div>";
				} else if ($type == 'citation') {
					$citation = get_field('texte');
					$contenu = "<p>".$citation."</p>";
				} else if ($type == 'sons') {
					$son = get_field ('son');
				} else if ($type == 'hyperlien') {
					$hyperlien = get_field('hyperlien');
					$contenu = "<p><a href=".$hyperlien." target='_blank'>".$hyperlien."</a></p>";
				}
				$style = 'element'.ucfirst($type);					
				$texte = get_field('texte');
				
				$tablePosition = get_post_meta( get_the_ID(), 'tablePosition' );
				foreach ( $tablePosition[$tagID] as $instanceID => $instance ) {
					$boiteTop = $instance[boite][top];
					$boiteLeft = $instance[boite][left];
					$boiteWidth = $instance[boite][width];
					$boiteHeight = $instance[boite][height];
					$contenuTop = $instance[contenu][top];
					$contenuLeft = $instance[contenu][left];
					$contenuWidth = $instance[contenu][width];
					$contenuHeight = $instance[contenu][height];

					?>
	
			
					<div class="ombre-1 element <?php echo $style ?>" style="position:absolute;top:<?php echo $boiteTop; ?>px;left:<?php echo $boiteLeft; ?>px;width:<?php echo $boiteWidth; ?>px;height:<?php echo $boiteHeight; ?>px;">
						<div class="capsule">
							<?php echo $contenu; ?>
						</div>
						<div class="infos"></div>
					</div>
					
					
					
					<?php 
				} ?>

				<?php endwhile; ?>
			
				<?php else: ?>
					
				<?php endif; ?>

			</div>
		</div>
	</div>
	
	
	
	<div id="infos"></div>
	
	
	</section>
	<!-- /section -->
	
	
	<div id="panneau">

	</div>


<?php get_footer(); ?>