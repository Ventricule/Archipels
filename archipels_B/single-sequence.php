<?php get_header();?>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/popcorn.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/wrappers/common/popcorn._MediaElementProto.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/wrappers/null/popcorn.HTMLNullVideoElement.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/wrappers/youtube/popcorn.HTMLYouTubeVideoElement.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/wrappers/vimeo/popcorn.HTMLVimeoVideoElement.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/modules/player/popcorn.player.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/players/youtube/popcorn.youtube.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/players/vimeo/popcorn.vimeo.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/plugins/code/popcorn.code.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/popcorn-js/plugins/footnote/popcorn.footnote.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/popcorn.sequencer.js"></script>
<script src="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/external/require/require.js"></script>
<link href="<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/css/controls.css" rel="stylesheet">

<style>
	/* INTERFACE */
	
	#blank, #fond{
		position:absolute;
		top:0;
		left:0;
		bottom:0;
		right:0;
		z-index:0;
		background: #ddd;
	}
	#timer{
		position: fixed;
		top: 0;
		right: 15px;
		z-index: 1001;
		color: black;
		text-shadow: none;
		width: 50px;
	}
	#loading{
		z-index:1001;
		position: fixed;
		top: 50%;
		left: 50%;
		color:black;
		text-shadow:none;
	}
	#loading .play{
		font-size: 24px;
		cursor: pointer;
		border: 2px solid #eee;
		border-radius: 100px;
		width: 60px;
		display: block;
		height: 60px;
		margin: -30px auto auto -30px;
		text-align: center;
		vertical-align: middle;
		line-height: 59px;
		padding-left: 3px;
		background: #fff;
		box-shadow: 0px 0px 20px #fff;
		text-shadow: 0px 0px 25px rgba(0, 0, 0, 0.29);
	}
	.controls {
		background: none repeat scroll 0 0 white;
		border: 1px solid #D5D6D5;
		bottom: 0;
		height: 41px;
		left: 0;
		opacity: 1;
		position: absolute;
		right: 0;
		visibility: visible;
		z-index: 1000;
    }
	
	/* IMAGE */
	
	#general-container{
		position: fixed;
		top: 15px;
		bottom: 15px;
		left: 15px;
		right: 15px;
	}
	#container {
		position: absolute;
		top:0;
		right:0;
		bottom:41px;
		left:0;
		z-index: 1;
    }
	#video {
		position: absolute;
		top:0;
		left:0;
		bottom:0;
		right:0;
		background:#ddd;
		z-index: 1;
	}
	#video-container {
		position: absolute;
		top:0;
		left:0;
		bottom:0;
		right:0;
	}
	#image-container{
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		z-index: 3;
		background-size: cover !important;
		background-position: 50% 50% !important;
		display:none;
	}
	#sstitres-container, #titres-container, #surtitres-container{
		position: absolute;
		left: 0;
		bottom: 0;
		right: 0;
		z-index: 4;
		text-align: center;
		width: 70%;
		margin: auto;
		
		font-size:32px;
		line-height:36px;
		color:#FFF;
		text-shadow:3px 3px 3px #000;
	}
	#titres-container {
		top: 50%;
		margin-top:-36px;
	}
	#sstitres-container {
		top: 90%;
	}
	#surtitres-container {
		top: 10%;
	}
	#audio-container{
		z-index:0;
	}
	
</style>
	
<?php
if (have_posts()): while (have_posts()) : the_post(); 

$rows = get_field('elements');
if($rows)
{
	
	foreach($rows as $row)
	{
		$type = $row['type'];
		$cutin = convertToSecond( $row['cutin'] );
		$cutout = convertToSecond( $row['cutout'] );
		$start = convertToSecond ($row['start'] );
		$end = convertToSecond( $row['end'] );
		$volume = $row['volume'] * 10;
		$chapiters[] = $start;
		
		if ($type=='video'){
			$url = get_field('vid_url', $row['element'][0]);
			$videos[] = array(
				'url' => $url,
				'cutin' => $cutin,
				'cutout' => $cutout,
				'start' => $start,
				'end' => $end,
				'volume' => $volume
			);

		} else if ($type=='image'){	
			$img = get_field('image', $row['element'][0]);	
			$images[] = array(
				//'url' => $img[sizes][full],
				'url' => $img[url],
				'start' => $start,
				'end' => $end
			);
		} else if ($type=='son'){
			$file = get_field('son', $row['element'][0]);
			$url = $file[url];
			$sons[] = array(
				'url' => $url,
				'cutin' => $cutin,
				'cutout' => $cutout,
				'start' => $start,
				'end' => $end,
				'volume' => $volume
			);
		}

	}
}

$rows = get_field('textes');
if($rows)
{
	
	foreach($rows as $row)
	{
		$type = $row['type'];
		$start = convertToSecond ($row['start'] );
		$end = convertToSecond( $row['end'] );
		$chapiters[] = $start;
		
		if ($type=='sous-titre'){	
			$sousTitres[] = array(
				'text' => $row['texte'],
				'start' => $start,
				'end' => $end
			);
		} else if ($type=='titre'){	
			$titres[] = array(
				'text' => $row['texte'],
				'start' => $start,
				'end' => $end
			);
		} else if ($type=='sur-titre'){	
			$surTitres[] = array(
				'text' => $row['texte'],
				'start' => $start,
				'end' => $end
			);
		}
	}
}

$rows = get_field('fonds');
if($rows)
{
	
	foreach($rows as $row)
	{
		$couleur = $row['couleur'];
		$niveau = substr($row['lvl'], 1);
		$start = convertToSecond ($row['start'] );
		$end = convertToSecond( $row['end'] );
		
		$fonds[] = array(
			'couleur' => $couleur,
			'niveau' => $niveau,
			'start' => $start,
			'end' => $end
		);

	}
}
?>

<?php endwhile;endif; ?>


 <script>
var popcorn;
	// ensure the web page (DOM) has loaded
	$(function () {
		popcorn = Popcorn.smart( "#video", "#t=,120" )
		.volume( 0 )
		<?php
		// VIDEOS
		if ($videos){
		foreach ($videos as $key => $video){
		?>
		.sequencer({
			start: 		<?php echo $video['start']; ?>,
			end: 		<?php echo $video['end']; ?>,
			from: 		<?php echo $video['cutin']; ?>,
			duration: 	<?php echo $video['cutout']; ?>,
			target: 	"video-container",
			width: 		100,
			zindex: 	2,
			volume: 	<?php echo $video['volume']; ?>,
			mute: 		false,
			source: 	"<?php echo $video['url']; ?>",
		})
		<?php
		}
		}
		// SONS
		if ($sons){
		foreach ($sons as $key => $son){
		?>
		.sequencer({
			start: 		<?php echo $son['start']; ?>,
			end: 		<?php echo $son['end']; ?>,
			from: 		<?php echo $son['cutin']; ?>,
			duration: 	<?php echo $son['end'] - $son['start']; ?>,
			target: 	"video-container",
			width: 		100,
			zindex: 	0,
			volume: 	<?php echo $son['volume']; ?>,
			mute: 		false,
			source: 	"<?php echo $son['url']; ?>"
		})
		<?php
		}
		}
		// IMAGES
		if ($images){
		foreach ($images as $key => $image){
		?>
		.code({
			start: <?php echo $image['start']; ?>,
			end: <?php echo $image['end']; ?>,
			onStart: function( options ) { 
				$("#image-container").css('background', 'url("<?php echo $image['url']; ?>")');
				$("#image-container").show();
			},
			onEnd: function( options ) {
				$("#image-container").hide();
				$("#image-container").css('background', '');
			}
		})
		<?php
		}
		}
		// SOUS-TITRES
		if ($sousTitres){
		foreach ($sousTitres as $key => $sousTitre){
		?>
		.footnote({
			start: <?php echo $sousTitre['start']; ?>,
			end: <?php echo $sousTitre['end']; ?>,
			text: "<?php echo $sousTitre['text']; ?>",
			target: "sstitres-container"					
		})
		<?php
		}
		}
		// TITRES
		if ($titres){
		foreach ($titres as $key => $titre){
		?>
		.footnote({
			start: <?php echo $titre['start']; ?>,
			end: <?php echo $titre['end']; ?>,
			text: "<?php echo $titre['text']; ?>",
			target: "titres-container"					
		})
		<?php
		}
		}
		// SUR-TITRES
		if ($surTitres){
		foreach ($surTitres as $key => $surTitres){
		?>
		.footnote({
			start: <?php echo $surTitres['start']; ?>,
			end: <?php echo $surTitres['end']; ?>,
			text: "<?php echo $surTitres['text']; ?>",
			target: "surtitres-container"					
		})
		<?php
		}
		}
		// FONDS

		?>

		var require = requirejs.config({
			baseUrl: "<?php echo get_bloginfo('template_url'); ?>/popcorn.maker/src",
			paths: {
			  "text": "../external/require/text"
			}
		});

		define( "sequencer-example", [ "ui/widget/controls" ], function( Controls ) {
			popcorn.controls( true );
			Controls.create( "controls", popcorn );
		});
		require(["sequencer-example"]);
		
	}, false);
	  
</script>


<?php include('inc-panneau.php'); ?>

<div id="general-container" class="embed">
	<div id="container" class="container">
		<div id="video-container" class="video video-container">
			<div id="video" class="video" style="background: #FFFFFF"></div>
			<div id="image-container"></div>
			<div id="surtitres-container"></div>
			<div id="titres-container"></div>
			<div id="sstitres-container"></div>
			<div id="fond"></div>
		</div>
	</div>
	
	<div id="timer" class='detail'></div>
	<div class="loading-message detail">Loading...</div>
	<div id="controls" class="controls"><div>
	<div id="controls-big-play-button"></div>
	<div id="attribution-info"></div>
</div>


<?php 
get_footer(); ?>