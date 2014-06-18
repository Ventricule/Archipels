<div id="interface" class="agrandit">
	<div id="menuwrap">
	<?php if ( is_user_logged_in() || $maintenance == false ) { ?>
		<?php if ( is_page('atlas') ) { $page = 'atlas'; } else { $page='table'; } ?>
		<div id="menu" data-page="<?php echo $page; ?>" data-id="<?php the_ID(); ?>" >
			<div id="open-menu" class="open-button"></div>
			<div id="index"></div>
			<div class="module module-atlas">
				<div class="label button" data-page="atlas"><a href="/atlas">Atlas</a></div>
			</div>
			<div class="module module-table">    
				<div class="label">Table <span class="current-name"></span></div>
				<div id="open-tables" class="open-button"></div>
				<ul class="list">
					<?php echo tableList(); ?>
				</ul>
			</div>
		</div>
	<?php } ?>
	</div>
		
	<div id="panneau" class="agrandit">
		<div id="close" class="icone">×</div>
		<div id="reduire" class="icone"></div>
		<?php 
		if ( is_user_logged_in() ) {
			echo '<div id="edit_element"><a href="'.admin_url().'" class="noir modifier tips-ns" target="_blank" title="Modifier l\'élément"><img src="'.get_bloginfo("template_url").'/img/icons/edit.svg" width="12" /></a></div>';
		}
		?>
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
	<div id="zoomButtons"><div id="zoomPlus">+</div><div id="zoomMoins">-</div></div>
</div>