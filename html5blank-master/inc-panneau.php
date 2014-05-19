<div id="interface" class="agrandit">
<div id="menuwrap">
<?php if ( is_user_logged_in() || $maintenance == false ) { ?>
	<div id="display" class="button close">
		<div class="plus"></div><span class="displayTitle"><a href="http://www.archipels.org/atlas/">Atlas</a></span>
	</div>
	<div id="menu">
		<div class="subMenu">
			<div id="menu_atlas" class="titleBlock block level1" data-num="0">
				<div class="title level1"><a href="http://www.archipels.org/atlas/">Atlas</a></div>
			</div>
			<div id="menu_tables" class="titleBlock block level2 close" data-num="1">
				<div class="title button level2">Table</div>
				<div class="plus button level2"></div>
				<div id="list" class="subMenu block inline">
					<div id="listWarp">
						<?php echo tableList(); ?>
					</div>
				</div>
			</div>
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
</div>