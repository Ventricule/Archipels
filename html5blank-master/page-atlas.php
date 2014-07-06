<?php get_header(); ?> 
<div id="fondSymboles">
					
</div>
	<div id="ecranAtlas">
		<div id="atlas">
			<div id="centre">
				
				<?php 
				
				$template_url = get_bloginfo('template_url');
				$elementsTable = array();
				
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
						$abrege = get_field('abrege', $id);												$miniature = get_field('image', $id);						$miniature = $miniature['sizes'][ 'thumbnail' ];
						
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
						$sortie .=		"<div class='legende'>".$legende."</div>";												$sortie .=		"<div class='miniature'>".$miniature."</div>";
						$sortie .=		"</div>";
						?>
						<div id="<?php echo $id ; ?>" class="ui-widget-content elementSymbole <?php echo $classesA." ".$classesB_str; ?> id-<?php echo $id ; ?>" data-date="<?php echo $date ?>" style="left:<?php echo $coord[0]; ?>px;top:<?php echo $coord[1]; ?>px;">
							
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
								$tables = array();
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
											$tables[] = get_the_ID();
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
								
								$thisElement['x'] 		= $coord[0];
								$thisElement['y']		= $coord[1];
								$thisElement['date'] 	= $date;
								$thisElement['class_a'] = $classesA;
								$thisElement['class_b'] = $classesB;
								$thisElement['tables'] 	= $tables;
								$thisElement['nom'] 	=  $nom;
								$thisElement['abrege'] 	=  $abrege;
								$thisElement['legende'] =  $legende;
								$thisElement['id'] 		=  $id;
								
								$elementsTable[] = $thisElement;
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
	
	<script type='text/javascript'>
		var elements_list_json = <?php echo(json_encode($elementsTable)); ?>;
	</script>
	
	<div id="map">
	
		<?php echo GeoMashup::map('map_content=global'); ?>
	
	</div>
	
	
	<?php include('inc-panneau.php'); ?>
	
	<div id="outils">
		
		<div id="recherche" class="noSelection">
			<div id="formule">
				<div class="symboles">
					<div class="bouton class1" title="immateriel"><img src="<?php echo $template_url ?>/img/immateriel.png" alt="immateriel" width="20" height="20" /></div><span class="detail">Immateriel</span><br>
					<div class="bouton class1" title="plan" ><img src="<?php echo $template_url ?>/img/plan.png" alt="plan" width="20" height="20" /></div><span class="detail">Plan</span><br>
					<div class="bouton class1" title="volume"><img src="<?php echo $template_url ?>/img/volume.png" alt="volume" width="20" height="20" /></div><span class="detail">Volume</span><br>
					<div class="bouton class2" title="structure_humaine"><img src="<?php echo $template_url ?>/img/structure_humaine.svg" alt="structure_humaine" width="20" height="20" /></div><span class="detail">S<sup>truct</sup> Humaine</span><br>
					<div class="bouton class2" title="structure_naturelle"><img src="<?php echo $template_url ?>/img/structure_naturelle.svg" alt="structure_naturelle" width="20" height="20" /></div><span class="detail">S<sup>truct</sup> Naturelle</span><br>
					<div class="bouton class2" title="sonore"><img src="<?php echo $template_url ?>/img/sonore.svg" alt="sonore" width="20" height="20" /></div><span class="detail">Sonore</span><br>
					<div class="bouton class2" title="temporel"><img src="<?php echo $template_url ?>/img/temporel.svg" alt="temporel" width="20" height="20" /></div><span class="detail">Temporel</span><br>
					<div class="bouton class2" title="espace"><img src="<?php echo $template_url ?>/img/espace.svg" alt="espace" width="20" height="20" /></div><span class="detail">Espace</span><br>
					<div class="bouton class2" title="representation"><img src="<?php echo $template_url ?>/img/representation.svg" alt="representation" width="20" height="20" /></div><span class="detail">Représentation</span><br>
					<div class="bouton enter" title="ou" >⋃</div>« ou »
				</div>
				<div class="champ"></div>
			</div>
		</div>
		<div id="boutons" class="noSelection">
			<input type="checkbox" id="show_classification" class="tips-bottom" title="Classification" >
			<label class="bouton_classification" for="show_classification" onclick>
				<img src="<?php echo $template_url ?>/img/classification.png" width="23" height="13" />
			</label>
			<input type="checkbox" id="show_historique" class="tips-bottom" title="Période">
			<label class="bouton_periode" for="show_historique" onclick>
				<img src="<?php echo $template_url ?>/img/periode.png" width="23" height="13" />
			</label>
			<input type="checkbox" id="show_carte" class="tips-bottom"  title="Atlas géographique">
			<label class="bouton_geographie" for="show_carte" onclick>
				<img src="<?php echo $template_url ?>/img/map.png" width="23" height="13" />
			</label>				
			<?php // if ( is_user_logged_in() ) { ?>
			<input type="checkbox" id="show_laliste" class="tips-bottom"  title="Liste">
			<label class="bouton_laliste" for="show_laliste" onclick>
				<img src="<?php echo $template_url ?>/img/liste.png" width="23" height="13" />
			</label>
		
			<?php // } ?>
		
			<div id="champ-recherche">
				<?php get_template_part('searchform'); ?>
			</div>
		</div>
		
		<div id="historique" class="noSelection">
			<p>
			  <p class="detail date" id="annee1"></p>
			  <p class="detail date" id="annee2"></p>
			</p>
			<div id="selecteur_annee"></div>
		</div>

		
	</div>


<!-- /ecranAtlas -->

<?php get_footer(); ?>