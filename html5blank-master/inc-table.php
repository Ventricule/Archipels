<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
	<!-- section -->
	<section role="main">
	
		<div id="ecran">
			<div id="table">
				<?php $table_pos = get_post_meta( get_the_ID(),'table_position', true ); ?>
				<script>
					var table_id = <?php the_ID(); ?>;
					<?php 
					if( !$table_pos ) { ?>
						var table_top = 0;
						var table_left = 0;
						var table_scale = 1;
					<?php } else { ?>
						var table_top = <?php echo $table_pos[0]; ?>;
						var table_left = <?php echo $table_pos[1]; ?>;
						var table_scale = <?php echo $table_pos[2] ; ?>;
					<?php 
					} 
					?>
				</script>
				<div id="centre">
					<?php
						$elements = get_post_meta( get_the_ID(),'elements', true );
						$notes = get_post_meta( get_the_ID(),'notes', true );
						$lignes = get_post_meta( get_the_ID(),'lignes', true );
						if ($elements){
							foreach ($elements as $element){
								echo createElement(	$element['id'], 
													$element['instance'], 
													$element['boiteTop'], 
													$element['boiteLeft'], 
													$element['boiteWidth'], 
													$element['boiteHeight'], 
													$element['capsuleTop'], 
													$element['capsuleLeft'], 
													$element['capsuleWidth'], 
													$element['capsuleHeight'], 
													$element['fontSize'], 
													$element['lineHeight'], 
													$element['ombre']
								);
							}
						}
						if ($notes){
							foreach ($notes as $note){
								echo createNote(	$note['left'], 
													$note['top'], 
													$note['width'], 
													$note['height'], 
													$note['couleur'], 
													$note['val'],
													$note['zindex']
								);
							}
						}
						if ($lignes){
							foreach ($lignes as $ligne){
								echo createLigne(	$ligne['left'], 
													$ligne['top'], 
													$ligne['width'], 
													$ligne['height'], 
													$ligne['couleur'], 
													$ligne['pathString'],
													$ligne['zindex'],
													$ligne['epaisseur']
								);
							}
						}
					?>
				</div>
			</div>
		</div>
		
		
		
		<div id="infos"></div>
	
	</section>
	<!-- /section -->
	
	
	<?php include('inc-panneau.php'); ?>



<?php endwhile; ?>

<?php else: ?>

	<!-- article -->
	<article>
		<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
	</article>
	<!-- /article -->

<?php endif; ?>