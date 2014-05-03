<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

include('ajax.php');

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('html5blank', get_template_directory() . '/languages');
}


/*------------------------------------*\
	Functions
\*------------------------------------*/

$maintenance = false;

if (is_home()) {
    query_posts('post_type=table&orderby=rand');
    while (have_posts()) {
        the_post();
        wp_redirect( get_permalink() );
    }
}
add_filter( 'body_class', 'add_body_classes');

function add_body_classes( $classes ) {
     if ( is_home() )
          $classes[] = 'single-table';
     return $classes;
}

function is_connected(){
	if (substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1') {
		return false;
	} else {
		return true;
	}
}
function have_modification_right() {
	if (is_user_logged_in()){
		if ( get_option("user_control") == "online" && is_connected() ){
			return true;
		} else if ( get_option("user_control") == get_current_user_id() && !is_connected() ) {
			return true;
		}
	}
}

function site_verrouille() {
	if ( !have_modification_right() && $_GET['page']!= 'dbs_options') {
		echo 	"<div style='
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #fff;
				text-align: center;
				vertical-align: middle;
				z-index: 200;
				' id='verrou'>";
		echo "<div style='top:40%;position: absolute;width: 100%;'>";
		echo "<p>Vous ne pouvez pas modifier Archipels.</p>";
		echo "<p><a href='tools.php?page=dbs_options&dbs_action=sync&url=http%3A%2F%2Fwww.archipels.org%2Fatlaspage=dbs_options&dbs_action=sync&url=http%3A%2F%2Fwww.archipels.org%2Fatlas'>Pourquoi ?</a></p>";
		echo "</div>";
		echo "</div>";
	}
}
add_action('admin_footer', 'site_verrouille');

function videoinfo($url){
	if ( strpos($url , 'youtube.com') ){
		$regex = '/^http(?:s?):\/\/(www\.)?youtube\.com\/watch\?v=([\w-]+).*$/i';  
		$key = preg_replace($regex, '$2', $url);  
		$return['thumb'] = "http://i1.ytimg.com/vi/".$key."/hqdefault.jpg";
		$return['iframe'] = '//www.youtube.com/embed/'.$key.'?showinfo=0&modestbranding=1&color=white';
		return $return;
	} else if ( strpos($url , 'youtu.be') ){
		$url = 'http://youtu.be/DF2G6rbzxHc';  
		$regex = '/^http(?:s?):\/\/youtu\.be\/([\w-]+)$/i';  
		$key = preg_replace($regex, '$1', $url);  
		$return['thumb'] = "http://i1.ytimg.com/vi/".$key."/hqdefault.jpg";
		$return['iframe'] = '//www.youtube.com/embed/'.$key.'?showinfo=0&modestbranding=1&color=white';
		return $return; 
	} else if ( strpos($url , 'vimeo.com') ){
		$obj = json_decode(file_get_contents("http://vimeo.com/api/oembed.json?url=".$url, true));
		$return['iframe'] = "http://player.vimeo.com/video/".$obj->video_id."?title=0&byline=0&portrait=0&color=ffffff";
		$return['thumb'] = $obj->thumbnail_url;
        return $return;
	}
	return $url;
}

function createElement($id, $instance, $top, $left, $width, $height, $capsuleTop, $capsuleLeft, $capsuleWidth, $capsuleHeight, $fontSize, $lineHeight, $ombre){
	$nom = get_the_title($id);
	$legende = fabriqueLegende($id);
	$abrege = get_field('abrege', $id);
	$type = get_field('type', $id);
	$date = get_field('annee', $id);
	if ($type != "" ){
		if ($type == 'image'){
			$imageInfos = get_field('image', $id);
			$image = $imageInfos[url];
			if ($width=='')				{	$width = $imageInfos[width];			}
			if ($height=='')			{	$height = $imageInfos[height];			}
			if ($capsuleWidth=='')		{	$capsuleWidth = $imageInfos[width];		}
			$capsuleHeight = "auto";
			if ($ombre=='')				{	$ombre = 1;								}
			$contenu =	"<img src=".$image." width=".$capsuleWidth." height=".$capsuleHeight." />";
		} else if ($type == 'video') {
			$videoUrl = get_field('vid_url', $id);
			$video = videoinfo($videoUrl);
			$videoImg = $video["thumb"];
			$videoUrl = $video["iframe"];
			if ($width=='')				{	$width = 420;				}
			if ($height=='')			{	$height = 315;				}
			if ($capsuleWidth=='')		{	$capsuleWidth = 420;		}
			$capsuleHeight = "100%";
			if ($ombre=='')				{	$ombre = 3;					}
			$videoIframe = '<iframe width="'.$capsuleWidth.'" height="'.$capsuleHeight.'" src="'.$videoUrl.'&autoplay=1" frameborder="0" allowfullscreen></iframe>';
			$contenu = "<div class='couvercle'><div class='flechePlay'><span class='play'>►</span></div><img src='".$videoImg."' width='".$capsuleWidth."' height='".$capsuleHeight."' ></div>";
			$contenu .= "<div class='videoBox' title='".$videoIframe."'>";
			$contenu .= "</div>";
		} else if ($type == 'carte') {
			$carte_iframe = get_field('carte_iframe', $id);
			if ($width=='')				{	$width = 500;				}
			if ($height=='')			{	$height = 350;				}
			if ($capsuleWidth=='')		{	$capsuleWidth = 500;		}
			$capsuleHeight = "100%";
			if ($ombre=='')				{	$ombre = 3;					}
			$contenu = "<div class='couvercle'></div>";
			$contenu .= $carte_iframe;
		} else if ($type == 'citation') {
			$citation = strip_tags(get_field('citation', $id), "<br><i><em><b><strong>" );
			
			if ($width=='')				{	$width = 200;				}
			if ($height=='')			{	$height = 200;				}
			if ($capsuleWidth=='')		{	$capsuleWidth = 200;		}
			$capsuleHeight = "auto";
			if ($ombre=='')				{	$ombre = 0;					}
			if ($fontSize=='')			{	$fontSize = 16;				}
			if ($lineHeight=='')		{	$lineHeight = 24;			}
			$contenu = "<p style='font-size:".$fontSize."px;line-height:".$lineHeight."px;'>".$citation."</p>";
		} else if ($type == 'son') {
			$son = get_field('son', $id);
			if ($width=='')				{	$width = 530;				}
			if ($height=='')			{	$height = 60;				}
			if ($capsuleWidth=='')		{	$capsuleWidth = 530;		}
			$capsuleHeight = "auto";
			if ($ombre=='')				{	$ombre = 2;					}		
			//$contenu = do_shortcode('[audio src="'.$son[url].'"]');
			$contenu = '<iframe scrolling="no" src="audioplayer/?src='.$son[url].'"></iframe>';
		} else if ($type == 'hyperlien') {
			if ($width=='')				{	$width = 200;				}
			if ($height=='')			{	$height = 200;				}
			if ($capsuleWidth=='')		{	$capsuleWidth = 200;		}
			$capsuleHeight = "auto";
			if ($fontSize=='')			{	$fontSize = 27;				}
			if ($lineHeight=='')		{	$lineHeight = 38;			}
			if ($ombre=='')				{	$ombre = 0;			}
			$hyperlien = get_field('hyperlien', $id);
			$contenu = "<p style='font-size:".$fontSize."px;line-height:".$lineHeight."px;'>".$hyperlien."</p>";
		}
		if ($capsuleTop == '')			{	$capsuleTop = 0;				}
		if ($capsuleLeft == '')			{	$capsuleLeft = 0;				}
		$type = 'element'.ucfirst($type);					
		$texte = get_field('texte', $id);
		$sortie =	"<div id='".$id."' class='element ".$type." id-".$id." ombre-".$ombre."' data-date=".$date." style='position:absolute;top:".$top."px;left:".$left."px;width:".$width."px;height:".$height."px;'>";
		$sortie .=		"<div class='capsule' style='position:relative;top:".$capsuleTop."px;left:".$capsuleLeft."px;width:".$capsuleWidth."px;height:".$capsuleHeight.";'>";
		$sortie .=			$contenu;
		$sortie .=		"</div>";
		$sortie .=		"<div class='infos'></div>";
		$sortie .=		"<div class='hide'>";
		$sortie .=			"<div class='nom'>".$nom."</div>";
		$sortie .=			"<div class='legende'>".$legende."</div>";
		$sortie .=			"<div class='abrege'>".$abrege."</div>";
		$sortie .=		"</div>";
		$sortie .=	"</div>";
		return ($sortie);
	}
}

function createNote($left, $top, $width, $height, $couleur, $val){
	return ( '<div class="note" style="position:absolute;top:'.$top.'px;left:'.$left.'px;width:'.$width.'px;height:'.$height.'px;"><textarea rows="1" cols="10" style="background:'.$couleur.';">'.strip_tags($val).'</textarea></div>' );
}
function createLigne($left, $top, $width, $height, $couleur, $pathString){
	$num = round( rand(0, 9999999) );
	$svg	 = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="ligne" style="position:absolute;width:'.$width.'px;height:'.$height.'px;left:'.$left.'px;top:'.$top.'px;">';
	$svg	.=  '<defs><marker id="arrow'.$num.'" viewBox="0 0 10 10" refX="1" refY="5" markerUnits="userSpaceOnUse" orient="auto" markerWidth="8" markerHeight="8"><polyline points="0,0 10,5 0,10 1,5" fill="'.$couleur.'" /></marker></defs>';
	$svg 	.= '<path d="'.$pathString.'" style="stroke: '.$couleur.'; fill:none;" marker-end="url(#arrow'.$num.')"/>';
	$svg 	.= '</svg>';
	return $svg ;
}
// Fabrique de miniature de table
function createTableThumbds(){

$image = imagecreate(200,50);
 
$orange = imagecolorallocate($image, 255, 128, 0);
$bleu = imagecolorallocate($image, 0, 0, 255);
$bleuclair = imagecolorallocate($image, 156, 227, 254);
$noir = imagecolorallocate($image, 0, 0, 0);
$blanc = imagecolorallocate($image, 255, 255, 255);

imagestring($image, 4, 35, 15, "Sseqqcfb nwdJSJKJIKQGKQERggos !", $blanc);

imagepng($image, realpath($_SERVER['DOCUMENT_ROOT'])."/atlas/wp-content/themes/html5blank-master/img/test.png");
imagepng($image, realpath($_SERVER['DOCUMENT_ROOT'])."/www.archipels.org/atlas/wp-content/themes/html5blank-master/img/tables/test.png");

}
// Fabrique de miniature de table
function createTableThumb($id){
	$imgW = 420;
	$imgH = 350;
	$Ymin = 0;
	$Ymax = 0;
	$Xmax = 0;
	$Xmin = 0;

	$elements = get_post_meta( $id, 'elements', true ) ;
	if ($elements){
		foreach ($elements as $element){
			$Ymin = min( $element['boiteTop'], $Ymin );
			$Ymax = max ( $element['boiteTop'] + $element['boiteHeight'], $Ymax );
			$Xmin = min( $element['boiteLeft'], $Xmin );
			$Xmax = max( $element['boiteLeft'] + $element['boiteWidth'], $Xmax );
			
		}
	}

	$ratioX = $imgW / ($Xmax - $Xmin);
	$ratioY = $imgH / ($Ymax - $Ymin);
	$ratio = min($ratioX, $ratioY);

	/*$image = imagecreatetruecolor($imgW,$imgH);
	imagesavealpha($image, true);
	$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
	imagefill($image, 0, 0, $trans_colour);*/
	
	$image = imagecreatefrompng( get_bloginfo('template_url')."/img/trame.png" );
	
	$couleur = imagecolorallocate($image, 0, 0, 0);
	
	$white = imagecolorallocate($image, 255, 255, 255);

	if ($elements){
		$i=0;
		foreach ($elements as $element){
			$i++;
			
			$x1 = $element['boiteLeft'] - $Xmin ." ";
			$y1 = $element['boiteTop'] - $Ymin;
			$x2 = $element['boiteLeft'] - $Xmin + $element['boiteWidth'];
			$y2 = $element['boiteTop'] - $Ymin + $element['boiteHeight'];
			$x1 = $x1 * $ratio * .9 + $imgW*.05;
			$y1 = $y1 * $ratio * .9 + $imgH*.05;
			$x2 = $x2 * $ratio  * .9 + $imgW*.05;
			$y2 = $y2 * $ratio * .9 + $imgH*.05;
			
			/*
			$type = get_field('type', $element['id']);
			if ($type =='image') {
				$imageInfos = get_field('image', $element['id']);
				$imageElmt = $imageInfos[sizes][medium];
				$widthElmt = $imageInfos[sizes]['medium-width'];
				$heightElmt = $imageInfos[sizes]['medium-height'];
				
				//$source = imagecreatefromjpeg($imageElmt);
				$source = imagecreatefromstring(file_get_contents($imageElmt));
				imagecopyresampled($image, $source, $x1, $y1, 0, 0, $x2-$x1, $y2-$y1, $widthElmt, $heightElmt);
			} else if ($type == 'video') {
				$videoRef = get_field('video', $element['id']);
				$videoImg = "http://i1.ytimg.com/vi/".$videoRef."/hqdefault.jpg";
				list($widthElmt, $heightElmt) = getimagesize($videoImg);
				$source = imagecreatefromstring(file_get_contents($videoImg));
				imagecopyresampled($image, $source, $x1, $y1, 0, 0, $x2-$x1, $y2-$y1, $widthElmt, $heightElmt);
			} else if ($type == 'hyperlien' || $type == 'citation') {
				ImageRectangle ($image, $x1, $y1, $x2, $y2, $couleur);
			}
			*/

			imageFilledRectangle($image, $x1, $y1, $x2, $y2, $white);
			ImageRectangle ($image, $x1, $y1, $x2, $y2, $couleur);
			imagestring($image, 2, $x1 + ($x2-$x1)/2 - 2 , $y1 + ($y2-$y1)/2 - 6, $i, $couleur);
		}
	}
	
	if (!is_connected()){
		imagepng($image, realpath($_SERVER['DOCUMENT_ROOT'])."/www.archipels.org/atlas/wp-content/themes/html5blank-master/img/tables/table-".$id.".png");
	} else {
		imagepng($image, realpath($_SERVER['DOCUMENT_ROOT'])."/atlas/wp-content/themes/html5blank-master/img/tables/table-".$id.".png");
	}
	return ( get_bloginfo('template_url')."/img/tables/table-".$id.".png?time=".time() );
}

// Fabrique de légende
function fabriqueLegende($id){
	$legende[auteur] = get_field('auteur', $id);
	$legende[titre] = '<em>' . get_field('titre', $id) . '</em>';
	$legende[dimensions] = html_entity_decode(str_replace('x','×',get_field('dimensions', $id) ));

	$jour= get_field('jour', $id);
	$mois= get_field('mois', $id);
	$annee= get_field('annee', $id);
	if ( $jour>0 && $mois>0 && $annee!=''){
		$date = $annee.$mois.$jour;
		$date = date("j n Y", strtotime($date));
	} else if ( $mois>0 && $annee!='') {
		$date = $annee.$mois.'15';
		$date = date("n Y", strtotime($date));
	} else {
		$date = $annee;
	}
	$legende[date] = $date;
	$legende[lieu] = GeoMashup::location_info( "fields=saved_name&object_name=post&object_id=".$id );
	$legende = array_filter($legende);
	return $legende = implode($legende, ', ');
}


/**
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes r, g, and b are contained in the set [0, 255] and
 * returns h, s, and l in the set [0, 1].
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
 */
function rgbToHsl($r, $g, $b){
    $r /= 255;
	$g /= 255;
	$b /= 255;
    $max = max($r, $g, $b);
	$min = min($r, $g, $b);
    $h = $s = $l = ($max + $min) / 2;

    if($max == $min){
        $h = $s = 0; // achromatic
    }else{
        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
        switch($max){
            case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
            case $g: $h = ($b - $r) / $d + 2; break;
            case $b: $h = ($r - $g) / $d + 4; break;
        }
        $h /= 6;
    }
	$hsl = array(
		'h'  	=> $h,
		's'		=> $s,
		'l'		=> $l
	);
    return $hsl;
}

// Convertit MM:SS en secondes
function convertToSecond( $mmss ){
	$mmss = explode(':', $mmss );
	$mm = intval($mmss[0]);
	$ss = intval($mmss[1]) ;
	$mm2ss = $mm*60;
	$t = $mm2ss + $ss + 1;
	return ( $t );
}






/*------------------------------------*\
	Functions
\*------------------------------------*/

// HTML5 Blank navigation
function html5blank_nav()
{
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => '', 
		'container'       => 'div', 
		'container_class' => 'menu-{menu slug}-container', 
		'container_id'    => '',
		'menu_class'      => 'menu', 
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
		)
	);
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if (!is_admin()) {
    
    	wp_deregister_script('jquery'); // Deregister WordPress jQuery
    	wp_register_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.9.1'); // Google CDN jQuery
    	wp_enqueue_script('jquery'); // Enqueue it!
    	
		wp_deregister_script( 'jquery-ui-core' );
    	wp_register_script('jquery-ui', get_template_directory_uri() . '/js/jquery-ui-1.9.2.custom.min.js', array(), '2.2.0'); 
        wp_enqueue_script('jquery-ui'); // Enqueue it!
    	
		wp_register_script('conditionizr', get_template_directory_uri() . '/js/conditionizr.min.js', array(), '2.2.0'); // Conditionizr
        wp_enqueue_script('conditionizr'); // Enqueue it!
        
        wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr.min.js', array(), '2.6.2'); // Modernizr
        wp_enqueue_script('modernizr'); // Enqueue it!
        
        wp_register_script('html5blankscripts', get_template_directory_uri() . '/js/scripts.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('html5blankscripts'); // Enqueue it!
        
        wp_register_script('mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('mousewheel'); // Enqueue it!
        
        wp_register_script('transit', get_template_directory_uri() . '/js/jquery.transit.min.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('transit'); // Enqueue it!
        
        wp_register_script('contextMenu', get_template_directory_uri() . '/js/contextMenu.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('contextMenu'); // Enqueue it!

        wp_register_script('nanoscroller', get_template_directory_uri() . '/js/jquery.nanoscroller.min.js', array(), '1.0.0'); // Custom scripts
        wp_enqueue_script('nanoscroller'); // Enqueue it!
		
        wp_register_script('tipsy', get_template_directory_uri() . '/js/jquery.tipsy.js', array(), '1.0.0'); // topsy
        wp_enqueue_script('tipsy'); // Enqueue it!
		
        wp_register_script('livesearch', get_template_directory_uri() . '/js/jquery.liveSearch.js', array(), '1.0.0'); // topsy
        wp_enqueue_script('livesearch'); // Enqueue it!
		
        //wp_register_script('popcorn', get_template_directory_uri() . '/js/popcorn-complete.min.js', array(), '1.0.0'); // Custom scripts
        //wp_enqueue_script('popcorn'); // Enqueue it!
		
    }
}

// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_page('pagenamehere')) {
        wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0'); // Conditional script(s)
        wp_enqueue_script('scriptname'); // Enqueue it!
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
    wp_register_style('normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all');
    wp_enqueue_style('normalize'); // Enqueue it!
    
    wp_register_style('html5blank', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_enqueue_style('html5blank'); // Enqueue it!

    wp_register_style('ui', get_template_directory_uri() . '/css/custom-theme/jquery-ui-1.9.2.custom.css', array(), '1.0', 'all');
    wp_enqueue_style('ui'); // Enqueue it!

    wp_register_style('font', 'http://fonts.googleapis.com/css?family=Cutive+Mono|PT+Serif:400,700,400italic,700italic|Inconsolata:400,700|Source+Sans+Pro:400,700,400italic,700italic', array(), '1.0', 'all');
    wp_enqueue_style('font'); // Enqueue it!

    wp_register_style('jquery.contextMenu.css', get_template_directory_uri() . '/jquery.contextMenu.css', array(), '1.0', 'all');
    wp_enqueue_style('jquery.contextMenu.css'); // Enqueue it!
	
    wp_register_style('nano', get_template_directory_uri() . '/css/nanoscroller.css', array(), '1.0', 'all');
    wp_enqueue_style('nano'); // Enqueue it!
	
	
}

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'html5blank') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}


// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Area 2', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function html5blankcomments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
	
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
		?>
	</div>

	<?php comment_text() ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('init', 'create_post_type_html5'); // Add our HTML5 Blank Custom Post Type
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination
add_action( 'init', 'createTaxonomies', 0 );

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [html5_shortcode_demo] [html5_shortcode_demo_2] Here's the page title! [/html5_shortcode_demo_2] [/html5_shortcode_demo]

/*------------------------------------*\
	Custom Post Types
\*------------------------------------*/

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_html5()
{
    register_taxonomy_for_object_type('table', 'element'); // Register Taxonomies for Category
    register_post_type('element', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('Atlas', 'html5blank'), // Rename these to suit
            'singular_name' => __('Élément', 'html5blank'),
            'add_new' => __('Nouvel élément', 'html5blank'),
            'add_new_item' => __('Nouvel élément', 'html5blank'),
            'edit' => __('Éditer', 'html5blank'),
            'edit_item' => __('Éditer', 'html5blank'),
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
			'custom-fields'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'table',
        ) // Add Category and Post Tags support
    ));
	
    register_post_type('table', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('Tables', 'html5blank'), // Rename these to suit
            'singular_name' => __('Table', 'html5blank'),
            'add_new' => __('Nouvelle table', 'html5blank'),
            'add_new_item' => __('Nouvelle table', 'html5blank'),
            'edit' => __('Éditer', 'html5blank'),
            'edit_item' => __('Éditer', 'html5blank'),
        ),
        'public' => true,
        'hierarchical' => false, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
			'custom-fields'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
        ) // Add Category and Post Tags support
    ));
	
	register_post_type('sequence', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('Séquences', 'html5blank'), // Rename these to suit
            'singular_name' => __('Séquence', 'html5blank'),
            'add_new' => __('Nouvelle séquence', 'html5blank'),
            'add_new_item' => __('Nouvelle séquence', 'html5blank'),
            'edit' => __('Éditer', 'html5blank'),
            'edit_item' => __('Éditer', 'html5blank'),
        ),
        'public' => true,
        'hierarchical' => false, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
			'custom-fields'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
        ) // Add Category and Post Tags support
    ));
	
	// creating (registering) the custom type 
	register_post_type( 'sav', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Bugs et suggestions', 'bonestheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Bugs et suggestions', 'bonestheme' ), /* This is the individual type */
			'all_items' => __( 'Tout', 'bonestheme' ), /* the all items menu item */
			'add_new' => __( 'Ajouter', 'bonestheme' ), /* The add new menu item */
			'add_new_item' => __( 'Bugs et suggestions', 'bonestheme' ), /* Add New Display Title */
			'edit' => __( 'Bugs et suggestions : éditer', 'bonestheme' ), /* Edit Dialog */
			'edit_item' => __( 'Bugs et suggestions : éditer', 'bonestheme' ), /* Edit Display Title */
			'new_item' => __( 'Ajouter', 'bonestheme' ), /* New Display Title */
			'view_item' => __( 'Voir', 'bonestheme' ), /* View Display Title */
			'search_items' => __( 'Chercher', 'bonestheme' ), /* Search Custom Type Title */ 
			'not_found' =>  __( 'Rien n\'a été trouvé.', 'bonestheme' ), /* This displays if there are no entries yet */ 
			'not_found_in_trash' => __( 'Rien n\'a été trouvé dans la corbeille.', 'bonestheme' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 80, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => "dashicons-format-chat",
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author')
		) /* end of options */
	); /* end of register post type */
}

/*------------------------------------*\
	Taxonomies Functions
\*------------------------------------*/

// Register Custom Taxonomy
function createTaxonomies()  {
	/*
	$labels = array(
		'name'                       => _x( 'Classement', 'Taxonomy General Name', 'html5blank' ),
		'singular_name'              => _x( 'Genre', 'Taxonomy Singular Name', 'html5blank' ),
		'menu_name'                  => __( 'Nouveau genre', 'html5blank' ),
		'all_items'                  => __( 'Tous les genres', 'html5blank' ),
		'parent_item'                => __( '', 'html5blank' ),
		'parent_item_colon'          => __( '', 'html5blank' ),
		'new_item_name'              => __( 'Nouveau genre', 'html5blank' ),
		'add_new_item'               => __( 'Ajouter un nouveau genre', 'html5blank' ),
		'edit_item'                  => __( 'Éditer le genre', 'html5blank' ),
		'update_item'                => __( 'Mettre à jour le genre', 'html5blank' ),
		'separate_items_with_commas' => __( 'Séparer les genres avec des virgules', 'html5blank' ),
		'search_items'               => __( 'Chercher un genre', 'html5blank' ),
		'add_or_remove_items'        => __( 'Ajouter ou supprimer un genre', 'html5blank' ),
		'choose_from_most_used'      => __( 'Choisir parmi les genres les plus utilisés', 'html5blank' ),
	);
	
	$args = array(
		'labels'                     => array( 'name' => 'Tables'),
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'tables', 'element', $args );*/
	
	$args = array(
		'labels'                     => array( 'name' => 'Type'),
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'type', 'element', $args );
	
	/*
	$labels = array(
		'name'                       => _x( 'Materiaux', 'Taxonomy General Name', 'html5blank' ),
		'singular_name'              => _x( 'Materiau', 'Taxonomy Singular Name', 'html5blank' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'materiau', 'element', $args );
	*/
	/*
	$labels = array(
		'name'                       => _x( 'Lieux', 'Taxonomy General Name', 'html5blank' ),
		'singular_name'              => _x( 'Lieu', 'Taxonomy Singular Name', 'html5blank' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'lieu', 'element', $args );
	*/
}


/*------------------------------------*\
	MetaBox
\*------------------------------------*/
add_action('add_meta_boxes','initialisation_metaboxes');
function initialisation_metaboxes(){
	//on utilise la fonction add_metabox() pour initialiser une metabox
	add_meta_box('atlasPosition', 'Coordonnées dans l\'atlas', 'getCoord', 'element', 'side', 'low');
	add_meta_box('tableParente', 'Table parente', 'getTable', 'sequence', 'side', 'low');
}
function getCoord($post){
	if ( $_GET['xy'] ) { $val = $_GET['xy'] ; } else { $val = get_post_meta( $post->ID,'coord',true ) ; }
	echo '<label for="coord">Coordonnées : </label>';
	echo '<input id="coord" type="text" name="coord" value="'.$val.'" />';
}
function getTable($post){
	if ( $_GET['table'] ) { $val = $_GET['table'] ; } else { $val = get_post_meta( $post->ID,'tableParente',true ) ; }
	echo '<label for="tableParente">Table parente : </label>';
	echo '<input id="tableParente" type="text" name="tableParente" value="'.$val.'" />';
}

add_action('save_post','save_metaboxes');
function save_metaboxes($post_ID){
  // si la metabox est définie, on sauvegarde sa valeur
  if(isset($_POST['coord'])){
    update_post_meta($post_ID,'coord', esc_html($_POST['coord']));
  }
  if(isset($_POST['tableParente'])){
    update_post_meta($post_ID,'tableParente', esc_html($_POST['tableParente']));
  }
}

function my_acf_save_post( $post_id )
{
	// vars
	$fields = false;
 
	// load from post
	if( isset($_POST['fields']) )
	{
		$fields = $_POST['fields'];
	}
	// Cherche les avatars supprimés
	$avatars_p = $avatars = $avatars_suppr = array();
	if ( get_field('avatars', $post_id ) ) { $avatars_p = get_field('avatars', $post_id ); }
	if ( $fields[field_5215caee3dacb] ){ $avatars = $fields[field_5215caee3dacb]; }
	$avatars_suppr = array_diff( $avatars_p , $avatars );

	// Pour chacun, soustrait l'element en cours de la liste d'avatars
	foreach ($avatars_suppr as $avatar_id){
		$list_avatars = get_field("avatars", $avatar_id);
		$sub[] = $post_id;
		$list_avatars_up = array_diff ( $list_avatars, $sub );
		update_field('avatars', $list_avatars_up, $avatar_id);
		
	}
	// Pour chaque avatar, ajouter l'élément en cours s'il n'existe pas déjà
	foreach ($avatars as $avatar_id){
		$list_avatars = get_field("avatars", $avatar_id);
		if( !in_array($post_id, $list_avatars, true)){ $list_avatars[] = $post_id; }
		update_field('avatars', $list_avatars, $avatar_id);
	}
}
// run before ACF saves the $_POST['fields'] data
add_action('acf/save_post', 'my_acf_save_post', 1);

function my_relationship_result( $result, $object, $field, $post )
{
    // load a custom field from thie $object and show it in the $result
    $abrege = get_field('abrege', $object->ID);
	if ( $_GET['table'] ) { $table = $_GET['table'] ; } else { $table = get_post_meta( $post->ID,'tableParente',true ) ; }
	$elementsPresents = get_post_meta( $table,'elementsList', true );
	if ( in_array ($object->ID,$elementsPresents) ) { $present = true; } else { $present = false; }
    // add post type to each result
	if ($present) {
		$result = '<b>[ ' . $abrege .  ' ] '. $result . '</b>';
	} else {
		$result = '<span style="opacity:.5">[ ' . $abrege .  ' ] '. $result . '</span>' ;
	}
 
    return $result;
}
 
// filter for a specific field based on it's name
add_filter('acf/fields/relationship/result/name=element', 'my_relationship_result', 10, 4);

function my_acf_result_query( $args, $field, $post )
{
    // eg from https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
    $args['meta_key'] = 'abrege';	
    $args['orderby'] = 'meta_value';
 
    return $args;
}
 
// acf/fields/relationship/result/name={$field_name} - filter for a specific field based on it's name
add_filter('acf/fields/relationship/query/name=element', 'my_acf_result_query', 10, 3);



/*------------------------------------*\
	DashBoard
\*------------------------------------*/


// Ajouter un widget dans le tableau de bord de WordPress
function wpc_dashboard_widget_function() {

	if ( $_REQUEST['alea']== 'alea') { 
		echo '<div class="updated"><p>Page d\'accueil: Aléatoire</p></div>'; 
		update_option('page_accueil', 'alea'); 
	} else if (!empty($_REQUEST['table'])) {
		echo '<div class="updated"><p>Page d\'accueil : ' . get_the_title( $_REQUEST['table'] ) . '</p></div>';
		update_option('page_accueil', $_REQUEST['table']);
	}

	// Saisie du texte entre les guillemets
	echo "<p>Table d'entrée sur www.archipels.org :</p>";
	echo'<form action="" method="post">';
	echo'<p><select name="table">';
	
	// WP_Query arguments
	$args = array (
		'post_type'              => 'table',
		'posts_per_page'         => '-1',
		'order'                  => 'ASC',
		'orderby'                => 'title',
	);

	// The Query
	$query = new WP_Query( $args );
	$page_accueil=get_option('page_accueil');
	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			if (get_the_ID() == $page_accueil){ $selected="selected"; } else { $selected=""; }
			echo '<option value="'.get_the_ID().'" '.$selected.'>'.get_the_title().'</option>';
		}
	} else {
		// no posts found
	}

	// Restore original Post Data
	wp_reset_postdata();
	echo '</select>';
	if ( get_option('page_accueil') == 'alea' ) $checked = 'checked="checked"';
	echo ' — ou — <input type="checkbox" name="alea" value="alea" '.$checked.'> Aléatoire</p>';
	echo '<p><input type="submit" value="Modifier"></p>';
	echo '</form>';


}

// Ajouter Liste KML/KMZ page accueil
function wpc_dashboard_widget_liste() {

	if ( $_GET['delete'] ) {
		if ( wp_delete_attachment( $_GET['delete'] ) ) {
			echo '<div class="updated"><p>Le fichier a été supprimé</p></div>'; 
		} else {
			echo '<div class="updated"><p>Le fichier n\'a pu être supprimé</p></div>'; 
		}
	}
	
	$args = array(
    'post_type' => 'attachment',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => null, // any parent
    ); 
	$attachments = get_posts($args);
	if ($attachments) {
		foreach ($attachments as $post) {
			if (strpos( get_post_mime_type( $post->ID ) ,'google') !== false ){
				setup_postdata($post);
				echo "<strong>";
				echo $post->post_title;
				echo "</strong>";
				echo "<br>";
				echo wp_get_attachment_url( $post->ID );
				echo "<br><span class='submitbox'><a href='?delete=".$post->ID."' class='submitdelete'>Supprimer</a></span>";
				echo '<br><br>';
			}
		}
	}
	wp_reset_postdata();
	echo '<a href="media-new.php">Envoyer un nouveau fichier KML/KMZ</a>';
	
	echo "<br><br><hr><br>Copiez l’URL de votre fichier KML et collez-la dans le champ de recherche de Google Maps. Cliquez ensuite sur Rechercher. Pour insérer ensuite la carte dans Archipels, cliquez sur l'icône (<img style='vertical-align: middle;' src='//storage.googleapis.com/support-kms-prod/SNP_2667618_en_v0' width='33' height='28'>) située dans la partie gauche de la page. Copier le code HTML et coller le dans le champ «carte» de l'élément.<br>";
	echo '<br><a href="https://support.google.com/maps/answer/41136?hl=fr">+ d\'infos</a><br><br>';



}

function wpc_add_dashboard_widgets() {
	wp_add_dashboard_widget('wp_dashboard_widget', "Page d'accueil", 'wpc_dashboard_widget_function');
	wp_add_dashboard_widget('wp_dashboard_widget2', "Liste des fichiers KML/KMZ pour google map", 'wpc_dashboard_widget_liste');
}
add_action('wp_dashboard_setup', 'wpc_add_dashboard_widgets' );




/*------------------------------------*\
	ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}

?>
