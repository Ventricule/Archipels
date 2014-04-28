<div id="interface" class="reduit">
	<div id="menu_table">
		<div id="bandeau">
			<div id="ouvrirPanneau" class="icone">⊟</div>
			<div class="arbre">
				<p class="detail lien_atlas">
					<?php if ( is_user_logged_in() || $maintenance != true ) { ?>
						<?php if ( is_page('atlas') ) { ?>
							<span class="tips-bottom" title="L'atlas réunit tous les éléments et permet leur recherche et leur organisation."><strong>Atlas</strong></span>
							<div class="arbo">
								Atlas
								<span class="grey"> 
									> 
									<a href="javascript:void(0);" class="liste_tables">Tables</a> 
								</span>
							</div>
						<?php } else if (is_singular('sequence')) { ?>
							<span><strong>Séquence : </strong><span class="nom"><?php echo get_the_title(); ?></span></span>
							<div class="arbo">
								<a href="<?php echo get_bloginfo('url') ; ?>/atlas/">Atlas</a> > 
								<a href="<?php echo get_permalink( $idT = get_post_meta( get_the_ID(), 'tableParente', true) ); ?>">Table : <?php echo get_field('abrege', $idT ); ?> <span class="couleur" style="background:<?php the_field('couleur', $idT); ?>;"></span></a>
								> 
								<a href="javascript:void(0);" class="liste_sequences">Séquences</a>
							</div>
						<?php } else { ?>
							<span><strong>Table : </strong><span class="nom"><?php the_field('abrege'); ?> </span><span class="couleur" style="background:<?php the_field('couleur'); ?>;"></span> </span>
							<div class="arbo">
								<a href="<?php echo get_bloginfo('url') ; ?>/atlas/">Atlas</a> > 
								<a href="javascript:void(0);" class="liste_tables">Tables</a>
								<span class="grey">
									> 
									<a href="javascript:void(0);" class="liste_sequences">Séquences</a>
								</span>
							</div>
						<?php } ?>
					<?php } ?>
				</p>
			</div>
			<div class="log">
				<?php 
				if ( is_user_logged_in() ) {
					?>
					<a href="<?php echo admin_url(); ?>" class="noir modifier tips-ns" target="_blank" title="Modifier l'élément"><img src="<?php echo get_bloginfo('template_url').'/img/icons/edit.svg'; ?>" width="12" /></a>
					<a href="<?php echo admin_url(); ?>" class="noir tips-ns" style="font-size: 15px;" title="Administration" target="_blank">⊛</a>
					<?php
				} else {
					?>
					<a href="<?php echo wp_login_url( get_permalink() ); ?>" class="noir tips-ns" title="Connexion" style="font-size: 24px;">✺</a>
					<?php
				}
				?>
			</div>
		</div>
		
		<!-- LISTE DES TABLES -->
		<div class="liste">
			<?php
			// WP_Query arguments
			$args = array (
				'post_type'              => 'table',
				'posts_per_page'         => '-1',
			);

			// The Query
			$query = new WP_Query( $args );

			// The Loop
			$tables = array();
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					
					$rgb = substr( get_field('couleur'), 1 );
					$r = hexdec(substr($rgb,0,2));
					$g = hexdec(substr($rgb,2,2));
					$b = hexdec(substr($rgb,4,2));
					$hsl = rgbToHsl($r, $g, $b);
					$sortValue = $hsl[h] * 5 + $hsl[s] * 2 + $hsl[l];
					$tables["$sortValue"] = array(
						'couleur' => get_field('couleur'),
						'abrege' => get_field('abrege'),
						'id' => get_the_id(),
						'lien' => get_permalink()
					);
				}
				ksort($tables);
			} else {
				// no posts found
			}
			?>
			<div>
				<p class="detail">Tables : </p>
				<?php
				foreach($tables as $table){
					echo '<div class="couleur" id="'.$table[id].'" style="background:'.$table[couleur].'"><p class="detail noir"><a href="'.$table[lien].'"><span>'.$table[abrege].'</span></a></p></div>';
				}
				// Restore original Post Data
				wp_reset_postdata();
				if (have_modification_right() ){
				?>
					<p class="detail alignRight"><a href="<?php echo admin_url(); ?>post-new.php?post_type=table" target="_blank" class="noir">+ Nouvelle table</a></p>
				<?php
				}
				?>
			</div>
		</div>
		
		<?php 
		if ( is_singular('sequence') ) {
			$tableParente = get_post_meta( get_the_ID(), 'tableParente', true);
		} else {
			$tableParente = get_the_ID();
		}
		?>
		
		<!-- LISTE DES SEQUENCES -->
		<div class="liste-sequences">
			<div>
				<p class="detail">Séquences :</p>
				<?php
				// WP_Query arguments
				$args = array (
					'post_type'              => 'sequence',
					'posts_per_page'         => '-1',
					'order'                  => 'ASC',
					'orderby'                => 'title',
					'meta_query'             => array(
						array(
							'key'       => 'tableParente',
							'value'     => $tableParente,
							'compare'   => 'LIKE',
						),
					),
				);

				// The Query
				$query = new WP_Query( $args );

				// The Loop
				$tables = array();
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						echo '<h2><a href="'.get_permalink().'" id="'.get_the_ID().'" class="non-souligne sequence">'.get_the_title().'</a></h2>';
						// Restore original Post Data
						wp_reset_postdata();
					}
					?>
				<?php
				}
				if (have_modification_right() ){
					?>
						<p class="detail alignRight"><a href="<?php echo admin_url(); ?>post-new.php?post_type=sequence&table=<?php the_ID(); ?>" target="_blank" class="noir">+ Nouvelle séquence</a></p>
					<?php
					}
				?>
			</div>
		</div>
	</div>
		
	<div id="panneau" class="agrandit">
		<div id="close" class="icone">×</div>
		<div id="reduire" class="icone"></div>
		<div id="enlarge" class="icone">↔</div>
		<div id="panneau-container" class="nano">
			<div class="content colonne1">
		</div>
		</div>
	</div>	
	<div id="panneau2">
		<div id="panneau-container" class="nano trois">
			<div class="content colonne2">
				<div class="texte bloc clear" style="opacity: 1;">	</div>
			</div>
		</div>
	</div>
	<div id="enregistrement"><p class="infos">enregistrement</p></div>
</div>