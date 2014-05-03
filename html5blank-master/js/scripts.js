if (typeof console != "object") {
	var console = {
		'log':function(){}
	};
}
function afficherPanneau(objet, ID){
	if (objet== 'element'){
		$("#interface .modifier").show().attr({href: admin_url + "post.php?post="+ID+"&action=edit", title : "Modifier l'élément" });
		$("#panneau .titre h2").html( $('#'+ID).children('.hide').children('.nom').html() );
		$("#panneau .titre p").html( $('#'+ID).children('.hide').children('.abrege').html() );
		$("#panneau .legende p").html( $('#'+ID).children('.hide').children('.legende').html() );
		$("#panneau .content>div:not(.en-tete), #panneau2 .content").css("opacity", .2);
	} else if (objet=='table'){
		$("#interface .modifier").show().attr({href: admin_url + "post.php?post="+ID+"&action=edit", title : "Modifier la table" });
	}
	$.post(admin_url+"admin-ajax.php", { action: 'afficherPanneau', objet: objet, id: ID },  function(data) {
		$('#panneau .content').html( data );
		if ( $('#panneau2').css('display') != 'none' ){
			$('#panneau2 .content').html('<div class="texte bloc clear" style="opacity: 1;"></div>');
			deployerPanneau2();
			/*$('#panneau .meta').prependTo('#panneau2 .content');
			$('.meta .classification').addClass('bloc');
			$('#panneau .texte-element').appendTo('#panneau2 .texte');*/
		}
		$("#panneau div, #panneau2 div").css("opacity", 1);
	}, 'html')
	.success(function() { 
		$(".nano").nanoScroller(); 
	})
}




// DOM Ready
$(function() {

	// SVG fallback
	// toddmotto.com/mastering-svg-use-for-a-retina-web-fallbacks-with-png-script#update
	if (!Modernizr.svg) {
		var imgs = document.getElementsByTagName('img');
		var dotSVG = /.*\.svg$/;
		for (var i = 0; i != imgs.length; ++i) {
			if(imgs[i].src.match(dotSVG)) {
				imgs[i].src = imgs[i].src.slice(0, -3) + "png";
			}
		}
	}
	
	$('.tips').tipsy({live: true, gravity: $.fn.tipsy.autoWE, opacity: 0.9, html: true});
	$('.tips-left').tipsy({live: true, gravity: 'e', opacity: 0.9, html: true});
	$('.tips-top').tipsy({live: true, gravity: 's', opacity: 0.9, html: true});
	$('.tips-bottom').tipsy({live: true, gravity: 'n', opacity: 0.9, html: true});
	$('.tips-ns').tipsy({live: true, gravity: $.fn.tipsy.autoNS, opacity: 0.9, html: true});
	
	$(document).on('click', '#warning .close', function(){
		$("#warning .message").hide();
		$("#warning .alert").show();
		setCookie('message', 0 );
	});
	$(document).on('click', '#warning .alert', function(){
		$("#warning .message").show();
		$("#warning .alert").hide();
		setCookie('message', 1 );
	});
	if( getCookie('message' )==0){
		$("#warning .message").hide();
		$("#warning .alert").show();
	}
	
	/*------------------------------------------------------------------------------------------------------------*\
				PANNEAU
	\*------------------------------------------------------------------------------------------------------------*/
	
	if ( have_modification_right() ) {
			
				$.contextMenu({
					selector: '#interface .liste .couleur', 
					zIndex:9999,
					callback: function(key, options) {
						if (key == 'editer'){
							window.open( admin_url + "post.php?post="+$(this).attr('id')+"&action=edit" ) ;
						}
					},
					items: {
						"editer" : {name: "Éditer"},
					}
				});
				$.contextMenu({
					selector: '#interface .liste-sequences .sequence', 
					zIndex:9999,
					callback: function(key, options) {
						if (key == 'editer'){
							window.open( admin_url + "post.php?post="+$(this).attr('id')+"&action=edit" ) ;
						}
					},
					items: {
						"editer" : {name: "Éditer"},
					}
				});
			}	
	// Remplir le panneau
	$(document).on( 'click', ".elementSymbole .classification", function (e){
		if ( !$('#panneau').is(":visible") ) { 
			$('#panneau').show(); 
			agrandirPanneau();
		}
		afficherPanneau( 'element', $(e.currentTarget).parent().attr('id') );
	});
	$(document).on( 'click', ".element", function (e){
		if ( !$('#panneau').is(":visible") ) { 
			$('#panneau').show(); 
			agrandirPanneau(false);
		}
		afficherPanneau( 'element', $(e.currentTarget).attr('id') );
	});
	
	
	// Afficher le panneau
	$(document).on( 'click', "#ouvrirPanneau", function (e){
		$("#interface .modifier").show();
		$('#panneau').show();
		$(this).hide();
		if ( $('#panneau').hasClass('reduit')){
			setCookie('panneau', 1);
			$('#interface').removeClass('agrandit').addClass('reduit');
		} else {
			setCookie('panneau', 2);
			$('#interface').removeClass('reduit').addClass('agrandit');
		}
		if ( $('#interface').hasClass('deploye')){
			setCookie('panneau', 3);
			$('#panneau2').show();
		} 
		if ( $('#panneau .content').children().length == 0 ) {
			if ( $('body').hasClass('single-table') ) {
				afficherPanneau( 'table', table_id);
			} else if ($('body').hasClass('atlas')) {
			
			}
		}
	});
	
	// Fermer le panneau
	$(document).on( 'click', "#panneau #close", function(){ fermerPanneau() } );
	
	// Agrandire/réduire le panneau
	$(document).on( 'click', "#reduire", function (e){ 
		if ( $('#panneau').hasClass('reduit')){
			agrandirPanneau();
		} else {
			reduirePanneau();
		}
	});
	function fermerPanneau(){
		$("#interface .modifier").hide();
		$('#panneau').hide();
		$('#panneau2').hide();
		$('#interface').removeClass('agrandit').addClass('reduit');
		$("#ouvrirPanneau").show();
		setCookie('panneau', 0);
	}
	function agrandirPanneau(autofill){			autofill = (typeof autofill === "undefined") ? true : autofill;
		$("#ouvrirPanneau").hide();
		$('#panneau').removeClass('reduit').addClass('agrandit');
		$('#interface').removeClass('reduit');
		$('#interface').removeClass('deploye');
		$('#interface').addClass('agrandit');
		$(".nano").nanoScroller(); 
		setCookie('panneau', 2);
		if ( $('#panneau .content').children().length == 0 ) {
			if ( $('body').hasClass('single-table') && autofill) {
				afficherPanneau( 'table', table_id);
			} else if ($('body').hasClass('atlas')) {
			
			}
		}
	}
	function reduirePanneau(){
		$("#ouvrirPanneau").hide();
		$('#panneau').removeClass('agrandit').addClass('reduit');
		$('#interface').removeClass('agrandit').addClass('reduit');
		$('#interface').removeClass('deploye');
		$('#enlarge').html("↔");
		$('#enlarge').prependTo('#panneau');
		$('#panneau2').hide();
		$('#panneau .pane').remove();
		setCookie('panneau', 1);
		if ( $('#panneau .content').children().length == 0 ) {
			if ( $('body').hasClass('single-table') ) {
				afficherPanneau( 'table', table_id);
			} else if ($('body').hasClass('atlas')) {
			
			}
		}
	}
	function deployerPanneau2(){
			$('#panneau2').show();
			$('#enlarge').html("←");
			//$('#panneau #enlarge').appendTo('#panneau2');
			$('#panneau .meta, #interface').addClass('deploye');
			//$('.meta .classification').addClass('bloc');
			$('#panneau .texte-element').appendTo('#panneau2 .texte');
			$(".nano").nanoScroller();
			setCookie('panneau', 3);
	}
	function replierPanneau2(){
			$('#panneau .meta, #interface').removeClass('deploye');
			//$('.meta .classification').removeClass('bloc');
			//$('#panneau2 .meta').appendTo('#panneau .texte');
			$('#panneau2 .texte-element').appendTo('#panneau .texte');
			$('#enlarge').html("↔");
			//$('#panneau2 #enlarge').prependTo('#panneau');
			$('#panneau2').hide();
			$(".nano").nanoScroller();
			setCookie('panneau', 2);
	}
	
	// Déployer le panneau
	$(document).on( 'click', "#enlarge", function (e){
		if ( $('#panneau2').css('display') == 'none' ){
			deployerPanneau2()
		} else {
			replierPanneau2()
		}
		
	});
	
	// Déplier le panneau dans l'état enregistré
	if ($('body').hasClass('home') || $('body').hasClass('atlas') ){
		fermerPanneau();
	} else {
		var panneau = getCookie('panneau');
		switch (panneau) {
			case '1' : 
				$('#panneau').show(); 
				reduirePanneau();
			break;
			case '2' :
				$('#panneau').show(); 
				agrandirPanneau();
			break;
			case '3' :
				$('#panneau').show(); 
				agrandirPanneau();
			break;
		}
	}


	// Scroll du panneau
	$(".nano").nanoScroller();
	
	// Classification
	$(document).on({
		mouseenter: function() {
			$('#panneau .classification .boite').animate({ left : -16 },500) ;
			var largeur = parseInt( $('#panneau .classification .fond').html() );
			$('#panneau .classification .fond').animate({ width : 16*largeur+16 },500) ;
		},
		mouseleave: function(){
			$('#panneau .classification .boite').animate({ left : 0 },200) ;
			$('#panneau .classification .fond').animate({ width : 16 },200) ;
		}
	}, '#panneau .classification');
	$(document).on({
		click: function() {
			var classe = $(this).parent().attr('class') ;
			classe = classe.split(' ');
			classe = classe[1];
			surligner(classe);
		}
	}, '.classification .boite img, .classification .boite span');
	function surligner(classe){
		if (!classe) {
			$('.elementSymbole').removeClass('non-surligne');
			$('.elementSymbole').removeClass('surligne');
		} else {
			$('.elementSymbole').addClass('non-surligne');
			$('.elementSymbole').removeClass('surligne');
			if (classe instanceof Array){
				classe.forEach( function(value){
					$('.elementSymbole.'+value).removeClass('non-surligne');
					$('.elementSymbole.'+value).addClass('surligne');
				} );
			} else {
				$('.elementSymbole.'+classe).removeClass('non-surligne');
				$('.elementSymbole.'+classe).addClass('surligne');
			}
		}
	}
	function focus(id){
		
		var element = $('.elementSymbole#'+id);
		element.children('.classification').trigger('click');
		$('.elementSymbole').addClass('non-surligne');
		$('.elementSymbole').removeClass('surligne');
		element.removeClass('non-surligne');
		element.addClass('surligne');
		
		//var top = ( $('#atlas').height() * .5 ) + parseInt( element.css('top') );
		//var left = ( $('#atlas').width() * .5 ) + parseInt( element.css('left') );
		//var top = ( $('#centre').position().top ) / echelle ;
		//var left = ( $('#centre').position().left ) / echelle ;
		var element_top = element.offset().top;
		var element_left = element.offset().left;
		var atlas_top = parseInt( $('#atlas').css('top') );
		var atlas_left = parseInt( $('#atlas').css('left') ) ;
		var new_top = atlas_top - ( element_top - $('#ecranAtlas').height()/2 );
		var new_left = atlas_left - ( element_left - $('#ecranAtlas').width()/2 );
		$('#atlas').animate({ top: new_top, left: new_left },500);
	}
	
	
	// miniature > fullscreen
	$(document).on({
		click: function() {
			var img = $(this).attr('alt');
			
			$('#fullscreenView').css({'background-image' : 'url("'+img+'")' });
			
			$('#fullscreenView').show();
		}
	}, '.miniature:not(.table) img');
	$(document).on({
		click: function() {
			$('.miniature iframe').clone().appendTo('#fullscreenView');
			$('#fullscreenView').css({'background-image' : 'none' });
			$('#fullscreenView').append('<div class="zoomCarte"></div>');
			$('#fullscreenView').show();
		}
	}, '.miniature .zoomCarte');
	$('#fullscreenView').click(function(){
		$('#fullscreenView').hide();
		$('#fullscreenView').html('');
	});
	
	// Liste Tables
	var fond = '';
	$(document).on(
	{
		mouseenter: function() 
		{
			$(this).children().show();
			fond = $(this).css('background');
			//$(this).css('background', 'none');
		},
		mouseleave: function()
		{
			$(this).children().hide();
			//$(this).css('background', fond);
		}
	}
	, '#menu_table .liste .couleur');
	$(document).on( 'click', "a.liste_tables", function (e){
		$('#menu_table .liste-sequences').slideUp(50);
		$('#menu_table .liste').slideToggle(50);
	});
	$(document).on( 'click', "a.liste_sequences", function (e){
		$('#menu_table .liste').slideUp(50);
		$('#menu_table .liste-sequences').slideToggle(50);
	});
	$(document).on( 'click', "#menu_table .arbre .couleur, #menu_table .arbre .nom", function (e){
		$('#panneau').show();				agrandirPanneau();
		afficherPanneau( 'table', table_id);
	});
	
	
	/*------------------------------------------------------------------------------------------------------------*\
				IF TABLE
	\*------------------------------------------------------------------------------------------------------------*/
	
	if ( $('body').hasClass('single-table') ) {
		var echelle = 1,
			coef=.4;
			
		var drawLine = false;
			
		// Menu Contextuel
		$(function(){
			if ( have_modification_right() ) {
			
				$.contextMenu({
					selector: '#table', 
					zIndex:9999,
					callback: function(key, options) {
						if (key == 'coller'){
							var top = ( $('.context-menu-list:visible').offset().top - $('#centre').offset().top ) / echelle ,
								left = ( $('.context-menu-list:visible').offset().left - $('#centre').offset().left ) / echelle ,
								ID = getCookie('pressepapier');
							if (! ID) { 
								alert ('Le presse-papier est vide'); 
							} else {
								ajouterElement(ID, top, left); 
							}
						} else if (key == 'dessiner'){
							dessiner( true );
						} else if (key == 'annoter'){
							var top = ( $('.context-menu-list:visible').offset().top - $('#centre').offset().top ) / echelle ,
								left = ( $('.context-menu-list:visible').offset().left - $('#centre').offset().left ) / echelle;
							$('#centre').append('<div class="note" style="position:absolute;top:'+top+'px;left:'+left+'px;width:100px;height:50px;"><textarea></textarea></div>');
							initNote();
						}
					},
					items: {
						"coller": {name: "Coller", icon: "paste"},
						"ID" : {
							name: "Ajouter par ID", 
							type: 'text', 
							value: "",
							events : {
								keyup : function(e) {
									if ( e.keyCode == 13){
										var top = ( $('.context-menu-list:visible').offset().top - $('#centre').offset().top ) / echelle ;
											left = ( $('.context-menu-list:visible').offset().left - $('#centre').offset().left ) / echelle;
										// export states to data store
										var infos = $.contextMenu.getInputValues(e.data);
										ID = infos.ID;
										// this basically dumps the input commands' values to an object
										// like {name: "foo", yesno: true, radio: "3", …}
										ajouterElement(ID, top, left);
										$("#table").contextMenu("hide");
									}
								}
							}
						},
						"annoter" : {name: "Annoter"},
						"dessiner": {name: "Dessiner"},
					}
				});
			}
			
			if ( have_modification_right() ) {
				$.contextMenu({
					selector: '.element', 
					callback: function(key, options) {
						if ( key == 'supprimer' ) {
							$(this).remove();
							enregistrer();
						} else if ( key == '0' || key == '1' ||key == '2' ||key == '3' ||key == '4' ) {
							$(this).removeClass('ombre-0');
							$(this).removeClass('ombre-1');
							$(this).removeClass('ombre-2');
							$(this).removeClass('ombre-3');
							$(this).removeClass('ombre-4');
							$(this).addClass('ombre-'+key);
						} else if (key == 'copier'){
							enregistrer()
							setCookie('pressepapier', $(this).attr('id') );
						}if (key == 'editer'){
							enregistrer();
							window.open( admin_url + "post.php?post="+$(this).attr('id')+"&action=edit" );
						}
					},
					items: {
						"copier": {name: "Copier"},
						"editer": {name: "Éditer"},
						"supprimer": {name: "Supprimer"},
						"fold1" : {
							"name": "Calque",
							"items" : {
								"4": {name: "/////"},
								"3": {name: "////"},
								"2": {name: "///"},
								"1": {name: "//"},
								"0": {name: "/"},
							}
						},
					}
				});
			}
			
			if ( have_modification_right() ) {
				$.contextMenu({
					selector: '.ligne', 
					callback: function(key, options) {
						if ( key == 'supprimer' ) {
							$(this).remove();
							enregistrer();
						} else if (key == 'fleche'){
							if ( $(this).children('path').css("marker-end") != "none" ){
								$(this).children('path').css("marker-end", "none");
							} else {
								$(this).children('path').css("marker-end", "url(#arrow)");
							}
							enregistrer()
						} else if ( key.substring(0,7) == 'couleur'){
							var couleur = new Array();
							couleur['couleur0'] = "#000";
							couleur['couleur1'] = "#fff";
							couleur['couleur2'] = "#ffb5bc";
							couleur['couleur3'] = "#ff4040";
							couleur['couleur4'] = "#ff7200";
							couleur['couleur5'] = "#FFF500";
							couleur['couleur6'] = "#4ed27d";
							couleur['couleur7'] = "#536fff";
							$(this).children('path').css("stroke", couleur[key] );
							$(this).find('marker polyline').attr("fill", couleur[key] );
							enregistrer()
						}
					},
					items: {
						"fleche": {name: "Ligne/Flèche"},
						"supprimer": {name: "Supprimer", icon: "delete"},
						"fold1" : {
							"name": "Couleur",
							"items" : {
								"couleur0": {name: "Noir"},
								"couleur1": {name: "Blanc"},
								"couleur2": {name: "Rose"},
								"couleur3": {name: "Rouge"},
								"couleur4": {name: "Orange"},
								"couleur5": {name: "Jaune"},
								"couleur6": {name: "Vert"},
								"couleur7": {name: "Bleu"},
							}
						},
					}
				});
				$.contextMenu({
					selector: '.note', 
					callback: function(key, options) {
						if ( key == 'supprimer' ) {
							$(this).remove();
							enregistrer();
						} else if ( key.substring(0,7) == 'couleur'){
							var couleur = new Array();
							couleur['couleur0'] = "#fff";
							couleur['couleur1'] = "#ffc7cc";
							couleur['couleur2'] = "#F7523B";
							couleur['couleur3'] = "#FFC164";
							couleur['couleur4'] = "#FFF8A6";
							couleur['couleur5'] = "#4ed27d";
							couleur['couleur6'] = "#9CBDF8";
							$(this).children('textarea').css("background-color", couleur[key] );
							enregistrer()
						}
					},
					items: {
						"supprimer": {name: "Supprimer", icon: "delete"},
						"fold1" : {
							"name": "Couleur",
							"items" : {
								"couleur0": {name: "Blanc"},
								"couleur1": {name: "Rose"},
								"couleur2": {name: "Rouge"},
								"couleur3": {name: "Orange"},
								"couleur4": {name: "Jaune"},
								"couleur5": {name: "Vert"},
								"couleur6": {name: "Bleu"},
							}
						},
					}
				});
			}


		});
		
		// Ajouter un élément 
		function ajouterElement(ID, top, left){
			$.post(admin_url+"admin-ajax.php",{
					action: 'addElement',
					id: ID, 
					top: top, 
					left: left
				}, function(data){
					if (data) {
						$('#centre').append( data );
						initElement( ".element.id-"+ID );
						enregistrer();
					}
				})
		}

		// Bouton enregistrer
		$('#bouton_save').hover( function(){
			$(this).children('img').hide();
			$(this).children('span').show();
		}, function(){
			$(this).children('img').show();
			$(this).children('span').hide();
		});
		$('#bouton_save').click( function(){
			enregistrer();
		} );
			
		
		// Enregistrer
		function enregistrer(){
			if ( have_modification_right() ) {
				$('#enregistrement').show();
				var notes = [];
				$('.note').each( function(){
					notes.push({
						left		: parseInt( $(this).css('left') 												),
						top 		: parseInt( $(this).css('top')													),
						height 		: parseInt( $(this).css('height')												),
						width 		: parseInt( $(this).css('width')												),
						couleur		: $(this).children('textarea').css('background')								,
						val 		: $(this).children('textarea').val()
					});
				});
				
				var lignes = [];
				$('svg.ligne').each( function(){
					lignes.push({
						left		: parseInt( $(this).css('left') 												),
						top 		: parseInt( $(this).css('top')													),
						height 		: parseInt( $(this).css('height')												),
						width 		: parseInt( $(this).css('width')												),
						pathString 	: $(this).children('path').attr('d')											,
						couleur 	: $(this).children('path').css('stroke')
					});
				});
				
				var elements = [];
				var elementsList = [];
				$('.element').each( function(){
					var id = $(this).attr('id');
					elementsList.push(id);
					var instance = $(this).attr('title');
					var classes = $(this).attr("class").split(" ");
					var ombre;
					$.each(classes, function(i, item) {
					  if(item.match(/^ombre-/))
						ombre = item ;
					});
					ombre = ombre.split("-")[1];
					

					elements.push({ 
						boiteLeft		: parseInt( $(this).css('left') 												),
						boiteTop 		: parseInt( $(this).css('top')													),
						boiteHeight 	: parseInt( $(this).css('height')												),
						boiteWidth 		: parseInt( $(this).css('width')												),
						capsuleWidth 	: parseInt( $(this).children('.capsule').css('width')							),
						capsuleHeight 	: parseInt( $(this).children('.capsule').css('height')							),
						capsuleLeft 	: parseInt( $(this).children('.capsule').css('left')							),
						capsuleTop 		: parseInt( $(this).children('.capsule').css('top')								),
						id				: parseInt( $(this).attr('id')													),
						fontSize 		: parseInt( $(this).children('.capsule').children('p').css('font-size')			),
						lineHeight 		: parseInt( $(this).children('.capsule').children('p').css('line-height')		),
						ombre			: ombre																			,
						instance 		: $(this).attr('title')															
					});
				});
				var position =[ parseFloat( $('#table').css('top') ), parseFloat( $('#table').css('left') ), $('#table').css('scale') ] ;
				$.post(admin_url+"admin-ajax.php",{
						action: 'save',
						elements: elements,
						table_id: table_id,
						table_position: position,
						elementsList: elementsList,
						notes: notes,
						lignes: lignes
					}, function(data){
						$('#enregistrement').hide();
						if ( $('#panneau .miniature').hasClass('table') ){
							afficherPanneau( 'table', table_id);
						}
					},
				'html');
			}
		}
		
		// Positionnement de la table
		if (table_top != 0){
			$("#table").css('top', table_top) ;
			$("#table").css('left', table_left);
			$('#table').css({ transformOrigin: 'top left' }).transition({ scale: table_scale, duration: 0 });
			echelle = table_scale;
		}
		// Drag de la table  
		$("#table").draggable({ 
			scroll:false,			
			stop:function(){
				var top = $(this).css('top');
				var left = $(this).css('left');
				if ( have_modification_right() ) {
					enregistrer();
				} else {
					setCookie('table_pos_top', top );
					setCookie('table_pos_left', left );
					setCookie('table_zoom', echelle );
				}
			},  
		});
		// Zoom de la table
		//Math.pow(echelle * coef, 1.8)
		$("#ecran").mousewheel(function(event, delta) {
			var zoomer = echelle * .2;
			if(delta > 0) {
				var offset = $('#table').offset();
				var pDist = (event.pageX - offset.left);
				var pDistY = (event.pageY - offset.top);
				var pDistReel = pDist * (1/echelle);
				var pDistYReel = pDistY * (1/echelle);
				//echelle = echelle + Math.pow(echelle * coef, 1.8);
				echelle = echelle + zoomer;
				if (echelle>4){ echelle=4; } else if (echelle<.01){ echelle=.01; }
				var nDist = pDistReel * echelle;
				var nDistY = pDistYReel * echelle;
				var dif = nDist - pDist ;
				var difY = nDistY - pDistY ;		
				zoom(echelle, -dif, -difY);
			}
			else{
				var offset = $('#table').offset();
				var pDist = (event.pageX - offset.left);
				var pDistY = (event.pageY - offset.top);
				var pDistReel = pDist*(1/echelle);
				var pDistYReel = pDistY * (1/echelle);
				echelle = echelle - zoomer;
				if (echelle>4){ echelle=4; } else if (echelle<.01){ echelle=.01; }
				var nDist = pDistReel * echelle;
				var nDistY = pDistYReel * echelle;
				var dif = pDist - nDist ;
				var difY = pDistY - nDistY ;
				zoom(echelle, dif, difY);
			}
		});
		function zoom(echelle, dif, difY){
			$('#table').css({ 
				transformOrigin: 'top left',
				left: function(index, value) { 
					return parseFloat( value ) + dif;
				}, 
				top : function(index, value) { 
					return parseFloat( value ) + difY;
				} 				
			})
			.transition({ scale: echelle }, 0);
		}
				
		// Cache des vidéos
		$(document).on( 'click', ".elementVideo .flechePlay", function (e){ 
			$(this).parent('.couvercle').hide();
			var html = $(this).parent('.couvercle').siblings('.videoBox').attr('title');
			console.log(html);
			if ( have_modification_right() ) {
				$(this).parent('.couvercle').parent('.capsule').parent('.element').resizable('disable');
			}
			$(this).parent('.couvercle').siblings('.videoBox').html(html);
			$(this).parent('.couvercle').siblings('.videoBox').children('iframe').width( $(this).parent('.couvercle').parent('.capsule').parent('.element').width() );
			$(this).parent('.couvercle').siblings('.videoBox').children('iframe').height( $(this).parent('.couvercle').parent('.capsule').parent('.element').height() );
		});
		
		// Cache des cartes
		$(document).on( 'click', ".elementCarte .couvercle", function (e){ 
			$(this).hide();
		});
		
		// Action click sur table
		$("#table").click( function() {
			$('.elementCarte .couvercle').show();
			$('.elementVideo .couvercle').show();
			$('.elementVideo').children('.capsule').children('.videoBox').html('');
			if ( have_modification_right() ) {
				$('.elementVideo').resizable('enable');
			}
		});
		
		
		// Init notes
		function initNote(){
			if ( have_modification_right() ) {
				$('.note').draggable({ scroll:false, cursor:"move",	
					drag: function(evt,ui){
						// zoom fix
						ui.position.top = Math.round(ui.position.top / echelle);
						ui.position.left = Math.round(ui.position.left / echelle);
					},
					stop: function(){
						enregistrer();
					}
				});
				//$('.note textarea').width('100%').height('100%');
				$('.note').resizable({
					start : function (event, ui ) {
						ui.position.top = ui.originalPosition.top/echelle ;
						ui.position.left = ui.originalPosition.left/echelle;
						ui.originalPosition.top = ui.originalPosition.top /echelle;
						ui.originalPosition.left = ui.originalPosition.left /echelle;					
					},
				
					resize : function(event, ui){
						// FIX ZOOM
						var changeWidth = ui.size.width - ui.originalSize.width; // find change in width
						var newWidth = ui.originalSize.width + changeWidth / echelle; // adjust new width by our zoomScale

						var changeHeight = ui.size.height - ui.originalSize.height; // find change in height
						var newHeight = ui.originalSize.height + changeHeight / echelle; // adjust new height by our zoomScale

						ui.size.width = newWidth;
						
						var ratio = ui.originalSize.width / ui.originalSize.height;

						ui.size.height = newHeight;
						$(this).children('.capsule').css( 'width', ui.size.width );

					},
					
					stop: function(){
						enregistrer();
					}
				});
			}
		}
		initNote();
		
		// init elements
		function initElement(element){
			if ( have_modification_right() ) {
				// Drag des éléments
				$(element).draggable({ 
					scroll:false,
					cursor:"move",
					drag: function(evt,ui)
						{
							// zoom fix
							ui.position.top = Math.round(ui.position.top / echelle);
							ui.position.left = Math.round(ui.position.left / echelle);
						},
					stop: function(){
						enregistrer();
					}
				});
				var imgOriginalSize=0;
				var imgOriginalSizeHeight=0;
				var imgOriginalTop=0;
				var imgOriginalLeft=0;
				var rapportCrop=1;	
				
				// Mise à l'échelle des image
				if ( $( element ).hasClass('elementImage') ) {
					$( element ).resizable({
						handles: 'all',
						minWidth: -(800) * 10,
						minHeight: -(800) * 10,

						start : function (event, ui ) {
							imgOriginalSize = $(this).children('.capsule').width() ;
							imgOriginalSizeHeight = $(this).children('.capsule').height() ;
							
							rapportCrop = imgOriginalSize / ui.size.width; 
							imgOriginalTop = parseFloat( $(this).children('.capsule').css( 'top' ))  ;
							imgOriginalLeft = parseFloat( $(this).children('.capsule').css( 'left' ))  ;
							ui.position.top = ui.originalPosition.top/echelle ;
							ui.position.left = ui.originalPosition.left/echelle;
							ui.originalPosition.top = ui.originalPosition.top /echelle;
							ui.originalPosition.left = ui.originalPosition.left /echelle;

						},
						resize: function(  event, ui ) {
							var changeWidth = ui.size.width - ui.originalSize.width; // find change in width
							var newWidth = ui.originalSize.width + changeWidth / echelle; // adjust new width by our zoomScale

							var changeHeight = ui.size.height - ui.originalSize.height; // find change in height
							var newHeight = ui.originalSize.height + changeHeight / echelle; // adjust new height by our zoomScale

							ui.size.width = newWidth;
							
							var ratio = ui.originalSize.width / ui.originalSize.height;
							
							if ( event.shiftKey) {
								ui.size.height = newHeight;
								var changeTop = ui.position.top - ui.originalPosition.top;
								var newTop = ui.originalPosition.top + changeTop/echelle;
								ui.position.top =  newTop ;
								var changeLeft = ui.position.left - ui.originalPosition.left;
								var newLeft = ui.originalPosition.left + changeLeft/echelle;
								ui.position.left =  newLeft ;
								
								ui.size.width = Math.min(ui.size.width , imgOriginalSize + parseFloat( $(this).children('.capsule').css( 'left' ))  );
								ui.size.height = Math.min(ui.size.height , imgOriginalSizeHeight + parseFloat( $(this).children('.capsule').css( 'top' ))  );
								ui.position.left =  Math.max(ui.position.left, ui.originalPosition.left + imgOriginalLeft) ;
								ui.position.top =  Math.max(ui.position.top, ui.originalPosition.top + imgOriginalTop) ;
								
								$(this).children('.capsule').css( 'top', imgOriginalTop + (ui.originalPosition.top - ui.position.top) ) ;				
								$(this).children('.capsule').css( 'left', imgOriginalLeft + (ui.originalPosition.left - ui.position.left) );
								

							} else {
								ui.size.height = ui.size.width / ratio;
												
								$(this).children('.capsule').css( 'top', imgOriginalTop + (ui.originalPosition.top - ui.position.top) ) ;				
								$(this).children('.capsule').css( 'left', imgOriginalLeft + (ui.originalPosition.left - ui.position.left) );
							
								var changeWidth = (ui.size.width - ui.originalSize.width) * rapportCrop;
								
								$(this).children('.capsule').css( 'width', imgOriginalSize + (changeWidth ) );
								$(this).children('.capsule').css( 'min-width', $(this).width() );
														
								var nouvelleTailleImg = imgOriginalSize * ui.size.width / ui.originalSize.width ;
								var proportionDepasseGauche = imgOriginalLeft / imgOriginalSize;
								var nouveauLeft = nouvelleTailleImg * proportionDepasseGauche;
								$(this).children('.capsule').css( 'left', nouveauLeft ) ;				
								
								var proportionDepasseHaut = imgOriginalTop / imgOriginalSizeHeight;
								var nouveauTop = $(this).children('.capsule').height() * proportionDepasseHaut;
								$(this).children('.capsule').css( 'top', nouveauTop ) ;
								
								var scale100 = parseInt( $(this).children('.capsule').children('img').width() / $(this).children('.capsule').children('img').naturalWidth() * 100) ;
								$(this).children('.infos').html( "<p>" + scale100 + "% </p>");
							}
						},
						stop : function(){
							$(this).children('.infos').html("");
							enregistrer();
						}
					});
				}else if ( $( element ).hasClass('elementVideo') ) {
					// Mise à l'échelle des vidéos
					var imgOriginalSize=0;
					var imgOriginalSizeHeight=0;
					var imgOriginalTop=0;
					var imgOriginalLeft=0;
					var rapportCrop=1;
					
					$( element ).resizable({
						handles: 'all',

						start : function (event, ui ) {
							imgOriginalSize = $(this).children('.capsule').width() ;
							imgOriginalSizeHeight = $(this).children('.capsule').height() ;
							
							rapportCrop = imgOriginalSize / ui.size.width; 
							imgOriginalTop = parseFloat( $(this).children('.capsule').css( 'top' ))  ;
							imgOriginalLeft = parseFloat( $(this).children('.capsule').css( 'left' ))  ;
							ui.position.top = ui.originalPosition.top/echelle ;
							ui.position.left = ui.originalPosition.left/echelle;
							ui.originalPosition.top = ui.originalPosition.top /echelle;
							ui.originalPosition.left = ui.originalPosition.left /echelle;

						},
						resize: function(  event, ui ) {
							var changeWidth = ui.size.width - ui.originalSize.width; // find change in width
							var newWidth = ui.originalSize.width + changeWidth / echelle; // adjust new width by our zoomScale

							var changeHeight = ui.size.height - ui.originalSize.height; // find change in height
							var newHeight = ui.originalSize.height + changeHeight / echelle; // adjust new height by our zoomScale

							ui.size.width = newWidth;
							
							var ratio = ui.originalSize.width / ui.originalSize.height;
							
							if ( event.shiftKey) {
								ui.size.height = newHeight;
							} else {
								ui.size.height = ui.size.width / ratio;
							}
						
							var changeWidth = (ui.size.width - ui.originalSize.width) * rapportCrop;
							
							$(this).children('.capsule').css( 'width', imgOriginalSize + (changeWidth ) );
							$(this).children('.capsule').css( 'height', ui.size.height );
							
						},
						stop : function(){
							$(this).children('.infos').html("");
							enregistrer();
						}
					});
				}else if ( $( element ).hasClass('elementSon') ) {
					
				} else if ( $( element ).hasClass('elementCitation') || $( element ).hasClass('elementHyperlien') ) {
				
					// Mise à l'échelle des textes
					var imgOriginalSize=0;
					var imgOriginalSizeHeight=0;
					var imgOriginalTop=0;
					var imgOriginalLeft=0;
					var rapportCrop=1;
					
					$( element ).resizable({
						handles: 'all',
						aspectRatio: false,
						
						start : function (event, ui ) {
							originalFontSize = parseInt ( $(this).children('.capsule').children('p').css('font-size') ) ;
							originalLineHeight = parseInt ( $(this).children('.capsule').children('p').css('line-height') ) ;
							ui.position.top = ui.originalPosition.top/echelle ;
							ui.position.left = ui.originalPosition.left/echelle;
							ui.originalPosition.top = ui.originalPosition.top /echelle;
							ui.originalPosition.left = ui.originalPosition.left /echelle;					
						},
					
						resize : function(event, ui){
							if ( event.shiftKey) {
								var fontSize = parseInt ( originalFontSize * ui.size.width / ui.originalSize.width ) ;
								var lineHeight = parseInt ( fontSize * 1.3 ) ;
								$(this).children('.capsule').children('p').css( "font-size", fontSize ) ;
								$(this).children('.capsule').children('p').css( "line-height", lineHeight+"px" ) ;
								$(this).children('.capsule').css( 'width', ui.size.width );
							} else {
								$(this).resizable( "option", "aspectRatio", false );
								$(this).children('.capsule').css( 'width', ui.size.width );
								$(this).children('.capsule').css( 'height', ui.size.height );
							}
							// FIX ZOOM
							var changeWidth = ui.size.width - ui.originalSize.width; // find change in width
							var newWidth = ui.originalSize.width + changeWidth / echelle; // adjust new width by our zoomScale

							var changeHeight = ui.size.height - ui.originalSize.height; // find change in height
							var newHeight = ui.originalSize.height + changeHeight / echelle; // adjust new height by our zoomScale

							ui.size.width = newWidth;
							
							var ratio = ui.originalSize.width / ui.originalSize.height;
						},
						
						stop: function(){
						 enregistrer();
						}
					});
				} else if ( $( element ).hasClass('elementCarte') ) {
				
					// Mise à l'échelle des cartes
					var imgOriginalSize=0;
					var imgOriginalSizeHeight=0;
					var imgOriginalTop=0;
					var imgOriginalLeft=0;
					var rapportCrop=1;
					
					$( element ).resizable({
						handles: 'all',
						
						start : function (event, ui ) {
							originalFontSize = parseInt ( $(this).children('.capsule').children('p').css('font-size') ) ;
							originalLineHeight = parseInt ( $(this).children('.capsule').children('p').css('line-height') ) ;
							ui.position.top = ui.originalPosition.top/echelle ;
							ui.position.left = ui.originalPosition.left/echelle;
							ui.originalPosition.top = ui.originalPosition.top /echelle;
							ui.originalPosition.left = ui.originalPosition.left /echelle;
							$('.elementCarte .couvercle').show();
						},
					
						resize : function(event, ui){
							// FIX ZOOM
							var changeWidth = ui.size.width - ui.originalSize.width; // find change in width
							var newWidth = ui.originalSize.width + changeWidth / echelle; // adjust new width by our zoomScale

							var changeHeight = ui.size.height - ui.originalSize.height; // find change in height
							var newHeight = ui.originalSize.height + changeHeight / echelle; // adjust new height by our zoomScale

							ui.size.width = newWidth;
							ui.size.height = newHeight;
							$(this).children('.capsule').css( 'width', ui.size.width );
						},
						
						stop: function(){
						 enregistrer();
						}
					});
				}
			}
		}
		initElement( ".elementVideo" );
		initElement( ".elementSon" );
		initElement( ".elementCitation" );
		initElement( ".elementImage" );
		initElement( ".elementHyperlien" );
		initElement( ".elementCarte" );
		
		// Dessiner
		function dessiner ( actif ){
			if (actif){
				drawLine=true;
				$('#table').addClass('crosshair');
			} else {
				drawLine=false;
				$('#table').removeClass('crosshair');
			}
		}
		$('#table').click( function(e){
			if (drawLine) {
				var y = ( e.pageY - $('#centre').offset().top ) / echelle ,
					x = ( e.pageX - $('#centre').offset().left ) / echelle ;
				addpoint(x, y);
			}
		});
		
		var points = Array();
		var newline = 1;
		function addpoint(x, y){
			x = parseInt(x);
			y = parseInt(y);
			$('#centre').prepend('<div class="pointsTemp" style="position:absolute;top:'+y+'px;left:'+x+'px;">●</div>');
			points.push({ 'x':x, 'y':y });
			if (points.length==3){
				var marge = 10;
				var Xmax = Math.max.apply(Math, points.map(function(o){return o.x;}));
				var Xmin = Math.min.apply(Math, points.map(function(o){return o.x;}));
				var Ymax = Math.max.apply(Math, points.map(function(o){return o.y;}));
				var Ymin = Math.min.apply(Math, points.map(function(o){return o.y;}));
				var canvasW = Xmax-Xmin;
				var canvasH = Ymax-Ymin;
				Xmax -= marge;
				Xmin -= marge;
				Ymax -= marge;
				Ymin -= marge;
				
				var pathString = 'M'+( points[0]['x'] - Xmin )+','+( points[0]['y']  - Ymin )+' Q'+( points[1]['x'] - Xmin )+','+( points[1]['y'] - Ymin )+' '+( points[2]['x'] - Xmin )+','+( points[2]['y'] - Ymin);
				var svg  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="ligne" style="position:absolute;width:'+( (Xmax-Xmin) + 2*marge)+'px;height:'+( (Ymax-Ymin) + 2*marge)+'px;left:'+(Xmin)+'px;top:'+(Ymin)+'px;">';
				svg		+=  '<defs><marker id="arrow" viewBox="0 0 10 10" refX="1" refY="5" markerUnits="userSpaceOnUse" orient="auto" markerWidth="8" markerHeight="8" ><polyline points="0,0 10,5 0,10 1,5" fill="context-stroke" /></marker></defs>';
				svg 	+= '<path d="'+pathString+'" style="stroke: #000; fill:none;" marker-end="url(#arrow)"/>';
				svg 	+= '</svg>';
				$('#centre').prepend(svg);
				$('.pointsTemp').remove();
				points = Array();
				dessiner(false);
			}
			
		}
		/*
		function draw() {
		  var canvas = document.getElementById('canvas');
		  if (canvas.getContext) {
			var ctx = canvas.getContext('2d');

			// Quadratric curves example
			ctx.beginPath();
			ctx.moveTo(75,25);
			ctx.quadraticCurveTo(25,25,25,62.5);
			ctx.quadraticCurveTo(25,100,50,100);
			ctx.quadraticCurveTo(50,120,30,125);
			ctx.quadraticCurveTo(60,120,65,100);
			ctx.quadraticCurveTo(125,100,125,62.5);
			ctx.quadraticCurveTo(125,25,75,25);
			ctx.stroke();
		  }
		}
		$('#table').click( function(e){
		var y = ( e.pageY - $('#centre').offset().top ) / echelle ,
			x = ( e.pageX - $('#centre').offset().left ) / echelle ;
			addpoint(x, y);
		});
		var points = Array();
		var newline = 1;
		
		function addpoint(x, y){
			x = parseInt(x);
			y = parseInt(y);
			points.push({ 'x':x, 'y':y });
			var Xmax = Math.max.apply(Math, points.map(function(o){return o.x;}))
			var Xmin = Math.min.apply(Math, points.map(function(o){return o.x;}))
			var Ymax = Math.max.apply(Math, points.map(function(o){return o.y;}))
			var Ymin = Math.min.apply(Math, points.map(function(o){return o.y;}))
			var canvasW = Xmax-Xmin;
			var canvasH = Ymax-Ymin;
			var marge = Math.max(canvasW, canvasH);
			
			if (newline){
				$('#centre').prepend('<canvas id="ligne" width="'+(canvasW+2*marge)+'" height="'+(canvasH+2*marge)+'" style="position:absolute;top:'+ (Ymin-marge) +'px;left:'+ (Xmin-marge) +'px;border:5px solid red;"></canvas>');
				//$('#table').prepend('<canvas id="ligne" width="1000" height="1000" style="position:absolute;top:50000px;left:50000px;border:5px solid red;"></canvas>');
				newline = false;
			}
			
			$('#ligne').attr('width', canvasW + 2*marge ).attr('height',canvasH + 2*marge).css({ 'top':Ymin-marge, 'left':Xmin-marge });
			
			var canvas = document.getElementById('ligne');
			var pointsXY = [];
			for (var i=0; i < points.length ; i++){
				pointsXY.push( points[i]['x'] + marge );
				pointsXY.push( points[i]['y'] + marge );
			}
			console.log(pointsXY);
			console.log(pointsXY.length);
			if (pointsXY.length > 6){
			    var e=document.getElementById("ligne");
				var ctx=e.getContext('2d');
				if(!ctx){return}
				//   Drawing a spline takes one call.  The points are an array [x0,y0,x1,y1,...],
				//   the tension is t (typically 0.33 to 0.5), and true/false tells whether to
				//   connect the endpoints of the data to make a closed curve.
				drawSpline(ctx,pointsXY,.5,false);
			}
		}
		Array.max = function( array ){
			return Math.max.apply( Math, array );
		};
		Array.min = function( array ){
			return Math.min.apply( Math, array );
		};
		*/
		
	}
	/*	end if single-table		*/
	
	/*-----------------------------------------*\
					ATLAS
	\*-----------------------------------------*/
	
	if ( $('body').hasClass('atlas') ) {
	
		var echelle = 1;
		var coef=.4;
		var top = parseInt ( $('#atlas').offset().top ) *echelle;
		var left = parseInt ( $('#atlas').offset().left )*echelle;

		$("#atlas").draggable({ 
			scroll:false,			
			stop:function(){
				var top = $(this).css('top');
				var left = $(this).css('left');
				setCookie('atlas_pos_top', top );
				setCookie('atlas_pos_left', left );
				setCookie('atlas_zoom', parseInt(echelle*10000) );
			}
		});
		$(document).keydown(function(e) {
			if(e.shiftKey && !$("#atlas").draggable("option", "disabled") ) {
				$( "#atlas" ).draggable( "disable" );
				$( ".elementSymbole" ).draggable( "enable" );
			}
			
		});
		$(document).keyup(function(e) {
			if( $("#atlas").draggable("option", "disabled") ) {
				$( "#atlas" ).draggable( "enable" );
				$( ".elementSymbole" ).draggable( "disable" );
			}
		});
		if ( getCookie('atlas_pos_top') ){
			var atlas_top = getCookie('atlas_pos_top');
			var atlas_left = getCookie('atlas_pos_left');
			var atlas_scale = getCookie('atlas_zoom')/10000;
			$("#atlas").css('top', atlas_top) ;
			$("#atlas").css('left', atlas_left);
			$('#atlas').css({ transformOrigin: 'top left' }).transition({ scale: atlas_scale, duration: 0 });
			echelle = atlas_scale;
		} else {
			var atlas_top = 42000;
			var atlas_left = 42000;
			var atlas_scale = 0.17;
			$("#atlas").css('top', atlas_top) ;
			$("#atlas").css('left', atlas_left);
			$('#atlas').css({ transformOrigin: 'top left' }).transition({ scale: atlas_scale, duration: 0 });
			echelle = atlas_scale;
		}
		
		// Fond des symboles
		var i =0;
		$('.elementSymbole').each(function(){
			var left = $(this).offset().left - $(window).scrollLeft();
			var top = $(this).offset().top - $(window).scrollLeft();
			var num= "trame-"+i
			//$('#fondSymboles').append("<div class='trameRonde "+num+"'> </div>").children("."+num).offset({ top: top, left: left });
			i++
		});
		
		// Zoom de l'atlas
		$("#ecranAtlas").mousewheel(function(event, delta) {
			if(delta > 0) {
				var offset = $('#atlas').offset();
				var pDist = (event.pageX - offset.left);
				var pDistY = (event.pageY - offset.top);
				var pDistReel = pDist * (1/echelle);
				var pDistYReel = pDistY * (1/echelle);
				echelle = echelle + Math.pow(echelle * coef, 1.8);
				if (echelle>4){ echelle=4; }
				var nDist = pDistReel * echelle;
				var nDistY = pDistYReel * echelle;
				var dif = nDist - pDist ;
				var difY = nDistY - pDistY ;
				$('#atlas').css({ 
					left: function(index, value) { 
						return parseFloat( value ) - dif;
					}, 
					top : function(index, value) { 
						return parseFloat( value ) - difY;
					} 
				} );					
				atlasZoom(echelle);
			}
			else{
				var offset = $('#atlas').offset();
				
				var pDist = (event.pageX - offset.left);
				var pDistY = (event.pageY - offset.top);
				var pDistReel = pDist*(1/echelle);
				var pDistYReel = pDistY * (1/echelle);
				echelle = echelle - Math.pow(echelle * coef, 1.8);
				if (echelle>4){ echelle=4; }
				var nDist = pDistReel * echelle;
				var nDistY = pDistYReel * echelle;
				var dif = pDist - nDist ;
				var difY = pDistY - nDistY ;
				$('#atlas').css({ 
					left: function(index, value) { 
						return parseFloat( value ) + dif;
					}, 
					top : function(index, value) { 
						return parseFloat( value ) + difY;
					} 
				} );
				atlasZoom(echelle);
			}
		});
		function atlasZoom(echelle){
			$('#atlas').css({ transformOrigin: 'top left' }).transition({ scale: echelle, duration: 0 });
		}

		// DRAG DES ELEMENTS
	
		if ( have_modification_right() ) {
			// this creates the selected variable
			// we are going to store the selected objects in here
			var selected = $([]), offset = {top:0, left:0}; 
			
			$('.elementSymbole').draggable({ 
				scroll:false,
				cursor:"move",
				handle: ".classification",
				start: function(ev, ui) {
					if ($(this).hasClass("ui-selected")){
						selected = $(".ui-selected").each(function() {
						   var el = $(this);
						   el.data("offset", el.position()  );
						});
					}
					else {
						selected = $([]);
						$(".elementSymbole").removeClass("ui-selected");
					}
					// zoom fix
					//ui.position.top = Math.round(ui.position.top / echelle);
					//ui.position.left = Math.round(ui.position.left / echelle);
					//ui.originalposition = {top: ui.position.top, left: ui.position.left};
				},
				drag: function(ev, ui) {
					// zoom fix
					ui.position.top = Math.round(ui.position.top / echelle);
					ui.position.left = Math.round(ui.position.left / echelle);
					var dt = ui.position.top - ui.originalPosition.top/echelle, dl = ui.position.left - ui.originalPosition.left/echelle;
					
					// take all the elements that are selected expect $("this"), which is the element being dragged and loop through each.
					selected.not(this).each(function() {
						 // create the variable for we don't need to keep calling $("this")
						 // el = current element we are on
						 // off = what position was this element at when it was selected, before drag
						var el = $(this), off = { top: parseFloat( el.data("offset").top )/echelle, left: parseFloat( el.data("offset").left )/echelle } ;
						el.css({top: off.top + dt  , left: off.left + dl   });
					});
				},
				stop: function(){
					var coord = parseInt( $(this).css('left') )+","+parseInt( $(this).css('top') );
					$('#enregistrement').show();
									
					$.post(admin_url+"admin-ajax.php", { action: 'saveCoord', element: $(this).attr('id'), coord: coord },  function(data) {
						$('#enregistrement').hide();
					})
				}
			});
			
			$( ".elementSymbole" ).draggable( "disable" );
			$( "#atlas" ).selectable({ filter: ".elementSymbole" });
		
			// manually trigger the "select" of clicked elements
			$( ".elementSymbole" ).click( function(e){
				if (e.metaKey == false) {
					// if command key is pressed don't deselect existing elements
					$( ".elementSymbole" ).removeClass("ui-selected");
					$(this).addClass("ui-selecting");
				}
				else {
					if ($(this).hasClass("ui-selected")) {
						// remove selected class from element if already selected
						$(this).removeClass("ui-selected");
					}
					else {
						// add selecting class if not
						$(this).addClass("ui-selecting");
					}
				}
				
				$( "#atlas" ).data("selectable")._mouseStop(null);
			});
		}
		

		// MOUVEMENT DES PASTILLES
		setInterval( function(){
			$('.elementSymbole.surligne .classification')
			.animate({
				width: '120',
				height: '120',
				marginLeft:-20,
				marginTop:-20,
			}, 2200, "easeInOutCubic")
			.animate({
				width: '80',
				height: '80',
				marginLeft:0,
				marginTop:0,
			}, 1800, "easeInOutCubic");
		}, 4000);
	
		//	MENU CONTEXTUEL
		if ( have_modification_right() ) {
		 
			$(function(){
				$.contextMenu({
					selector: '.elementSymbole .classification', 
					callback: function(key, options) {
						if ( key == 'copier' ) {
							setCookie('pressepapier', $(this).parent().attr('id') );
						} else if ( key == 'editer' ) {
							window.open( admin_url + "post.php?post="+$(this).parent().attr('id')+"&action=edit" );
						}
					},
					items: {
						"copier": {name: "Copier", icon: "copy"},
						"editer": {name: "Éditer"},
					}
				});
				$.contextMenu({
					selector: '.elementListed', 
					callback: function(key, options) {
						if ( key == 'copier' ) {
							setCookie('pressepapier', $(this).attr('data-id') );
						} else if ( key == 'editer' ) {
							window.open( admin_url + "post.php?post=" + $(this).attr('data-id') + "&action=edit" );
						}
					},
					items: {
						"copier": {name: "Copier", icon: "copy"},
						"editer": {name: "Éditer"},
					}
				});
				$.contextMenu({
					selector: '#atlas', 
					callback: function(key, options) {
						if ( key == 'nouveau' ) {
							var top = ( $('.context-menu-list:visible').offset().top - $('#centre').offset().top ) / echelle;
							var left = ( $('.context-menu-list:visible').offset().left - $('#centre').offset().left ) / echelle;
							document.location.href= admin_url + "post-new.php?post_type=element&xy=" + left +","+top ;
						}
					},
					items: {
						"nouveau": {name: "Nouveau", icon: "new"},
					}
				});
			});
		}
		
		//infosbulles
		$(document).on({
			mouseenter: function() {
				var nom = $(this).attr('title');
				var offset = $(this).offset();
				var right = $('#ecranAtlas').width() - offset.left ;
				var bottom = $('#ecranAtlas').height() - offset.top ;
				var marginTop = 80*echelle / 2 - 17;
				var infobulle= "<div class='infobulle tipsy tipsy-e' style='position:absolute;top:"+offset.top+"px;right:"+right+"px;margin-top:"+marginTop+"px;'>";
				infobulle += "<div class='tipsy-arrow tipsy-arrow-e'></div>";
				infobulle += "<div class='tipsy-inner'>"+nom+"</div>";
				infobulle += "</div>";
				$('body').append(infobulle);
			},
			mouseleave: function() {
				$('.infobulle').remove();
			}
		}, '.elementSymbole .classification');
		
		// RECHERCHE PAR FORMULE
		var emplacement;
		$("#recherche #formule .symboles .bouton").click(function(){
			if (emplacement){
				if ( $(this).hasClass('enter') ){
					$(emplacement).after('<div class="espace">&nbsp;</div>');
					$(this).clone().insertAfter(emplacement);
					var newEmplacement = $(emplacement).next('.bouton').next('.espace');
					curseur(newEmplacement);
				} else {
					if ( !$(emplacement).prev().hasClass('enter') && $(emplacement).prev() && $("#recherche #formule .champ .bouton").size() > 0 ){
						//$(emplacement).before('<span title="et" class="tips-bottom">⋂</span>');
						$(emplacement).before('<span title="et">⋂</span>');
					}
					$(emplacement).after('<div class="espace">&nbsp;</div>');
					//$(this).clone().addClass('tips-bottom').insertAfter(emplacement);
					$(this).clone().insertAfter(emplacement);
					var newEmplacement = $(emplacement).next('.bouton').next('.espace');
					$(emplacement).remove();
					curseur(newEmplacement);
				}
			}else{
				if ( $("#recherche #formule .champ").html() != '' && !$(this).hasClass('enter') ){
					$("#recherche #formule .champ div").last('.espace').remove();
					if ( !$("#recherche #formule .champ .bouton").last().hasClass('enter') && $("#recherche #formule .champ .bouton").size() > 0 ){
						//$("#recherche #formule .champ").append('<span title="et" class="tips-bottom">⋂</span>');
						$("#recherche #formule .champ").append('<span title="et">⋂</span>');
					}
				}
				//$(this).clone().addClass('tips-bottom').appendTo("#recherche #formule .champ");
				$(this).clone().appendTo("#recherche #formule .champ");
				$("#recherche #formule .champ").append('<div class="espace">&nbsp;</div>');
			}
			select();
		});
		$(document).on( 'click', "#recherche #formule .champ .espace", function (){
			curseur(this);
		});
		$(document).on( 'click', "#recherche #formule .champ .bouton", function (){
			$(this).next('span').remove();
			if ($(this).hasClass('enter') ){
				$(this).nextUntil(".enter").andSelf().remove();
			}
			if ($(this).next().hasClass('espace') ){
				$(this).prev('span').remove();
			}
			$(this).remove();
			select();
		});
		function curseur(espace){
			emplacement = espace;
			$('.espace').html('&nbsp;');
			$(emplacement).html('<div id="cursor">⎜</div>');
		}
		function select(){
			var classes=[];
			var i=0;
			$("#recherche #formule .champ .bouton").each(function(){
				if ($(this).hasClass('enter')){
					i++;
				} else {
					if ( !classes[i] ) {
						classes[i] =  $(this).children('img').attr('alt');
					}else{
						classes[i] += "." + $(this).children('img').attr('alt');
					}
				}
			});
			surligner(classes);
		}
		setInterval ( function cursorAnimation() {
			$('#cursor').css('opacity', 0).delay(500).animate({ 'opacity': 1}, 0);
		}, 1000);
		
		// SELECTION PAR ANNEE
		var start = -1000000;
		var end = 2100;
		var debut, fin;
		$( "#selecteur_annee" ).slider({
			range: true,
			min: start,
			max: end,
			values: [ start, end ],
			create: function() {
				$( "p#annee1" ).html( start ).css( 'left', $('.ui-slider-handle').first().css('left'));
				$( "p#annee2" ).html( end ).css( 'left', $('.ui-slider-handle').last().css('left'));
			},
			slide: function( event, ui ) {
				function nonLinear(val, start, end){
					val = val-start;
					var width = end - start;
					var linear = val / width ;
					var nonLinear = Math.pow(linear, 1/300);
					return Math.round( nonLinear*width + start );
				}
				debut = nonLinear (ui.values[ 0 ], start, end);
				fin = nonLinear (ui.values[ 1 ], start, end);
				
				$( "p#annee1" ).html( debut ).css( 'left', $('.ui-slider-handle').first().css('left') );
				$( "p#annee2" ).html( fin ).css( 'left', $('.ui-slider-handle').last().css('left') );
				if (debut == start) { $( "p#annee1" ).html( '- ∞' ) }
				if (fin == end) { $( "p#annee2" ).html( '+ ∞' ) }
				
				var filtered = elementsAffiches.filter(function(x) { if ( x >= debut && x <= fin ){ return x; } });
				$('.elementSymbole').removeClass('surligne');
				$('.elementSymbole').addClass('non-surligne');
				$('.elementSymbole').filter( function() {  return $(this).attr( 'data-date' ) > debut && $(this).attr( 'data-date' ) < fin }).removeClass('non-surligne').addClass('surligne');
			},
			stop: function(e, ui){
				var filtered = elementsAffiches.filter(function(x) { if ( x >= debut && x <= fin ){ return x; } });
				$('.elementSymbole').removeClass('surligne');
				$('.elementSymbole').addClass('non-surligne');
				$('.elementSymbole').filter( function() {  return $(this).attr( 'data-date' ) > debut && $(this).attr( 'data-date' ) < fin }).removeClass('non-surligne').addClass('surligne');
			}
		});
		$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ) );
		
		//méthode de selection
		var selection;	
		
		// Liste
		$('#show_laliste').change(function(){
			if ( this.checked ) {
				var liste = $('<div/>');
				liste.attr('id', 'liste_elements');
				$('.elementSymbole').each(function() {
					var abrege = $(this).children('.hide').children('.abrege').html();
					var legende = $(this).children('.hide').children('.legende').html();
					var miniature = $(this).children('.hide').children('.miniature').html();
					var id = $(this).attr('id');
					var element = $('<div/>');
					var image = $('<img/>').attr({ 'src' : miniature , 'width' : 150, 'height' : 150 });
					abrege = $('<p/>').attr('class','detail').html(abrege);
					legende = $('<p/>').attr('class','detail').html(legende);
					element.append(image, abrege, legende);
					element.attr('data-id', id);
					element.attr('class', 'elementListed');
					liste.append(element);
				});
				liste.children('div').hover( function() {
					surligner('id-' + $(this).attr('data-id') );
				} );
				liste.children('div').click( function() {
					var element = $( '.elementSymbole#' + $(this).attr('data-id') );
					element.children('.classification').trigger('click');
				});
				liste.mouseleave( function() { surligner(0); } );
				$('body').append(liste);
			} else {
				$("#liste_elements").remove();
				surligner(0);
			}
		});
		
		
		// Période
		$('#show_historique').change(function(){
			if ( this.checked ) {
				$("#outils input").not(this).prop("checked", false); 
				$("#map").css("top","-99999px");
				$("#recherche").hide();
				$("#historique").show();
				surligner(0);
				selection='historique';
			} else {
				$("#historique").hide();
				surligner(0);
			}
		});							
		
		// Vue géographique
		$('#show_carte').change(function(){
			if ( this.checked ) {
				$("#outils input").not(this).prop("checked", false); 
				$("#recherche").hide();
				$("#historique").hide();
				$("#map").css('top',0);
				surligner(0);
				selection='carte';
			} else {
				$("#map").css("top","-99999px");
				surligner(0);
			}
		});
		
		// Formule classification
		$('#show_classification').change(function(){
			if ( this.checked ) {
				$("#outils input").not(this).prop("checked", false); 
				$("#historique").hide();
				$("#map").css("top","-99999px");
				$("#recherche").show();
				surligner(0);
				selection='classification';
			} else {
				$("#recherche").hide();
				surligner(0);
			}
		});
		
		// Recherche par mots clefs
		$('#champ-recherche input[name="s"]').liveSearch({url: admin_url+"admin-ajax.php" + '?action=fm_search&s='});
		$(document).on( 'click', "#jquery-live-search a, #interface .avatars a", function (){
			$('#outils').find('input').each( function(){
				if ( $(this).prop('checked') ) {
					$(this).prop('checked', false)
					$(this).change();
				}
			});

			var num = $(this).attr('href').substring(1);
			focus(num);
			selection='keyword';
		});
		
		
		//désactiver enter=send formulaire de recherche
		$('#champ-recherche input').keypress(function (e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#jquery-live-search a:first').trigger('click');
				return false;
			}
			
		});
		
		// Tout deselectionner
		$('#atlas').click(function(){
			if( selection=='keyword' ){
				$(".elementSymbole").removeClass('surligne');
				$(".elementSymbole").removeClass('non-surligne');
				selection='';
			}
		});
		
	}
	/* end if "atlas" */
		
});

// cookies
function setCookie(sName, sValue) {
	var today = new Date(), expires = new Date();
	expires.setTime(today.getTime() + (365*24*60*60*1000));
	document.cookie = sName + "=" + encodeURIComponent(sValue) + ";expires=" + expires.toGMTString() + "; path=/";
}
function getCookie(sName) {
	var cookContent = document.cookie, cookEnd, i, j;
	var sName = sName + "=";

	for (i=0, c=cookContent.length; i<c; i++) {
			j = i + sName.length;
			if (cookContent.substring(i, j) == sName) {
					cookEnd = cookContent.indexOf(";", j);
					if (cookEnd == -1) {
							cookEnd = cookContent.length;
					}
					return decodeURIComponent(cookContent.substring(j, cookEnd));
			}
	}       
	return null;
}
	
var elementsAffiches = new Array();
 
(function($) {
  function img(url) {
	var i = new Image;
	i.src = url;
	return i;
  }
 
  if ('naturalWidth' in (new Image)) {
	$.fn.naturalWidth  = function() { return this[0].naturalWidth; };
	$.fn.naturalHeight = function() { return this[0].naturalHeight; };
	return;
  }
  $.fn.naturalWidth  = function() { return img(this.src).width; };
  $.fn.naturalHeight = function() { return img(this.src).height; };
})(jQuery);
