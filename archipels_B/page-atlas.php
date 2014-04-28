<?php get_header(); ?> 
<div id="fondSymboles">
					
</div>
	<div id="ecranAtlas">
		<div id="atlas">
			<div id="centre">
				
				<?php 
				$args = array (
					'post_type'              => 'element',
					'posts_per_page'         => '-1',
				);

				// The Query
				$query = new WP_Query( $args );

				// The Loop
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						
						$id = $post->ID;
						$coord = explode(",",get_post_meta( $post->ID,'coord', true ));
						$date = get_field('annee', $id);
						
						$nom = get_the_title($id);
						$legende = fabriqueLegende($id);
						$abrege = get_field('abrege', $id);
						
						$classesA = get_field('classification_a', $id);
						$classesB = get_field('classification_b', $id);
						if ( is_array($classesB) ){
						 $classesB_str = implode(" ", $classesB);
						} else {
							$classesB_str = $classesB;
						}
						
						$sortie =		"<div class='hide'>";
						$sortie .=		"<div class='nom'>".$nom."</div>";
						$sortie .=		"<div class='abrege'>".$abrege."</div>";
						$sortie .=		"<div class='legende'>".$legende."</div>";
						$sortie .=		"</div>";
						?>
						<div id="<?php echo $id ; ?>" class="ui-widget-content elementSymbole <?php echo $classesA." ".$classesB_str; ?>" data-date="<?php echo $date ?>" style="left:<?php echo $coord[0]; ?>px;top:<?php echo $coord[1]; ?>px;">
							
							<svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="anneaux">
								<?php
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
								$query2 = new WP_Query( $args );
								// The Loop
								$j=0;
								if ( $query2->have_posts() ) {
									while ( $query2->have_posts() ) {
										$query2->the_post();
											$rgb = substr( get_field('couleur'), 1 );
											$r = hexdec(substr($rgb,0,2));
											$g = hexdec(substr($rgb,2,2));
											$b = hexdec(substr($rgb,4,2));
											$hsl = rgbToHsl($r, $g, $b);
											//$sortValue = $hsl[h] * 5 + $hsl[s] * 2 + $hsl[l];
											$rotateVal = $hsl[h];
										?>
											<line x1="250" y1="250" x2="400" y2="250" style="stroke:<?php echo '#'.$rgb; ?>;" transform="rotate(<?php echo $rotateVal*360; ?>, 250, 250)" />
											  <!--<circle cx="250" cy="250" r="<?php //echo 40+ $j*4; ?>" stroke="<?php //echo get_field('couleur'); ?>"
											  stroke-width="4" fill="none"/>-->
										<?php
										$j++;
									}
								} else {
									// no posts found
								}
								wp_reset_postdata();
								?>
							</svg>
							<div class="classification" title="<?php echo $nom; ?>">
								<?php
								if ($classesA){
									echo ('<div class="'.$classeA.'"><img src="'.get_bloginfo('template_url').'/img/'.$classesA.'.svg" width="20" height="20" /></div>');
								}
								?>
								<svg viewBox="0 0 80 80" width="80" height="80" xml:lang="fr"
								xmlns="http://www.w3.org/2000/svg"
								xmlns:xlink="http://www.w3.org/1999/xlink">

								<?php
								if ($classesA){
									$path[immateriel]='<path class="cache" d="M26.874,5.136c2.75,0,3.409,4.407,5.538,5.43
											s6.146-2.023,7.804,0s-0.855,5.498,0.026,7.677s5.5,2.762,5.5,5.429s-4.299,3.356-5.5,5.429s1.676,5.977-0.026,7.678
											s-5.351-0.646-7.804-0.001c-2.455,0.646-2.955,5.43-5.538,5.43c-2.583,0-3.575-3.991-5.538-5.43s-5.646,1.772-7.804,0.001
											c-2.158-1.773,0.522-5.082-0.026-7.678s-5.5-2.68-5.5-5.429c0-2.75,4.716-3.191,5.5-5.429s-1.591-6.059,0.026-7.677
											s5.85,0.812,7.804,0S24.124,5.136,26.874,5.136z" transform="scale(2.1) translate(-7.7,-4.5)" />';
									$path[volume] = '<circle class="cache" cx="40" cy="40" r="40" />';
									$path[plan] = '<rect class="cache" x="0" y="0" width="80" height="80" />';
									
									?>
									<defs>
										<!-- définition de la découpe -->
										<clipPath id="decoupe_<?php echo $classesA ?>">
											<?php 
											echo $path[$classesA]; 
											?>
										</clipPath>
									</defs>
									<?php
								}
								echo $path[$classesA]; 
								if ($classesB){
									foreach($classesB as $classe){
										echo ('<image xlink:href="'.get_bloginfo('template_url').'/img/'.$classe.'.svg" width="80" height="80" style="clip-path: url(#decoupe_'.$classesA.');" />');
									}
								}

								?>
								</svg>
							</div>
							
							<?php 
							echo $sortie;
							?>
						</div>
						<?php
					}
				} else {
					// no posts found
				}

				// Restore original Post Data
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
	
	<div id="map">
	
		<?php echo GeoMashup::map('map_content=global'); ?>
	
	</div>
	
	
	<?php include('inc-panneau.php'); ?>
	
	<div id="outils">
		
		<div id="recherche">
			<div id="formule">
				<div class="symboles">
					<div class="bouton" title="plan" ><img src="<?php echo get_bloginfo('template_url') ?>/img/plan.svg" alt="plan" width="20" height="20" /></div><span class="detail">Plan</span><br>
					<div class="bouton" title="espace"><img src="<?php echo get_bloginfo('template_url') ?>/img/espace.svg" alt="espace" width="20" height="20" /></div><span class="detail">Espace</span><br>
					<div class="bouton" title="immateriel"><img src="<?php echo get_bloginfo('template_url') ?>/img/immateriel.svg" alt="immateriel" width="20" height="20" /></div><span class="detail">Immateriel</span><br>
					<div class="bouton" title="volume"><img src="<?php echo get_bloginfo('template_url') ?>/img/volume.svg" alt="volume" width="20" height="20" /></div><span class="detail">Volume</span><br>
					<div class="bouton" title="structure_humaine"><img src="<?php echo get_bloginfo('template_url') ?>/img/structure_humaine.svg" alt="structure_humaine" width="20" height="20" /></div><span class="detail">S<sup>truct</sup> Humaine</span><br>
					<div class="bouton" title="structure_naturelle"><img src="<?php echo get_bloginfo('template_url') ?>/img/structure_naturelle.svg" alt="structure_naturelle" width="20" height="20" /></div><span class="detail">S<sup>truct</sup> Naturelle</span><br>
					<div class="bouton" title="sonore"><img src="<?php echo get_bloginfo('template_url') ?>/img/sonore.svg" alt="sonore" width="20" height="20" /></div><span class="detail">Sonore</span><br>
					<div class="bouton" title="temporel"><img src="<?php echo get_bloginfo('template_url') ?>/img/temporel.svg" alt="temporel" width="20" height="20" /></div><span class="detail">Temporel</span><br>
					<div class="bouton" title="representation"><img src="<?php echo get_bloginfo('template_url') ?>/img/representation.svg" alt="representation" width="20" height="20" /></div><span class="detail">Représentation</span><br>
					<div class="bouton enter" title="ou" >⋃</div>« ou »
				</div>
				<div class="champ"></div>
			</div>
		</div>
		
		<label class="bouton_classification"><input type="checkbox" id="show_classification">Classification</label>
		<label class="bouton_periode"><input type="checkbox" id="show_historique">Période</label>
		<label class="bouton_geographie"><input type="checkbox" id="show_carte">Atlas géographique</label>
		
		<div id="champ-recherche">
			<?php get_template_part('searchform'); ?>
		</div>
		
		<div id="historique">
			<p>
			  <p class="detail date" id="annee1"></p>
			  <p class="detail date" id="annee2"></p>
			</p>
			<div id="selecteur_annee"></div>
		</div>

		
	</div>


<!-- /ecranAtlas -->

<?php get_footer(); ?>