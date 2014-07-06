	<?php if ( is_user_logged_in() || $maintenance == false ) { ?>
		<?php if ( is_page('atlas') ) { $page = 'atlas'; } else { $page='table'; } ?>
	<nav id="menu" class="noSelection" data-page="<?php echo $page; ?>" data-id="<?php the_ID(); ?>">
		<div id="info" class="menubox" title="informations">i</div><!-- 
	--><div id="atlasButton" class="menubox"><a href="/atlas">Atlas</a><!--
	--></div><!--
    --><div id="closeButton" class="menubox close">x</div><!--
	--><div id="tables" class="menubox close"><!--
	--><div id="display">Tables</div><div id="tableButton">+</div></div>
		<ul id="tableList" class="close">
			<?php echo tableList(); ?>
		</ul>
	</nav>
	<?php } ?>
<div id="hoverCall"></div>
<div id="interface" class="agrandit close">
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
	<div id="zoomButtons" class="noSelection"><div id="zoomPlus">+</div><div id="zoomMoins">-</div></div>
</div>