<?php get_header();

if (have_posts()): while (have_posts()) : the_post(); ?>
	
<?php



function convertToSecond( $mmss ){
	$mmss = explode(':', $mmss );
	$mm = intval($mmss[0]);
	$ss = intval($mmss[1]) ;
	$mm2ss = $mm*60;
	$t = $mm2ss + $ss + 1;
	return ( $t );
}

$rows = get_field('elements');
if($rows)
{
	$videos = array();
	$images = array();
	$sons = array();
	
	foreach($rows as $row)
	{
		$type = $row['type'];
		$cutin = convertToSecond( $row['cutin'] );
		$cutout = convertToSecond( $row['cutout'] );
		$start = convertToSecond ($row['start'] );
		$end = convertToSecond( $row['end'] );
		$volume = $row['volume'] / 10;
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

	// ensure the web page (DOM) has loaded
	$(function () {
	
		Popcorn.player( "baseplayer" );
		var baseline = Popcorn.baseplayer( "#base" );
		baseline.on( "timeupdate", function() {
			document.getElementById( "timer" ).innerHTML = this.currentTime().toFixed(2) + "sec.";
		});
		
		var pop = new Array();
		<?php
		if ($videos){
			foreach ($videos as $key => $video){
			?>
				//Créer les balises vidéo
				pop[<?php echo $key; ?>] = Popcorn.smart( "#video-container-<?php echo $key; ?>", "<?php echo $video['url']; ?>?title=0&byline=0&portrait=0" );
				
				//Paramétrer les vidéos lorsque elle sont chargés
				if (pop[<?php echo $key; ?>].duration()) {
					pop[<?php echo $key; ?>].currentTime(<?php echo $video['cutin']; ?>);
					pop[<?php echo $key; ?>].volume(<?php echo $video['volume']; ?>);
					pop[<?php echo $key; ?>].pause();
				} else {
					pop[<?php echo $key; ?>].on('loadedmetadata', function() {
						pop[<?php echo $key; ?>].currentTime(<?php echo $video['cutin']; ?>);
						pop[<?php echo $key; ?>].volume(<?php echo $video['volume']; ?>);
						pop[<?php echo $key; ?>].pause();
					});
				}
				pop[<?php echo $key; ?>].cue(<?php echo $video['cutout']; ?>, function() {
					pop[<?php echo $key; ?>].currentTime(<?php echo $video['cutin']; ?>);
				});
				
				//déclencher les vidéos au bon moment
				baseline.code({
					start: <?php echo $video['start']; ?>,
					end: <?php echo $video['end']; ?>,
					onStart: function( options ) {         
					  pop[<?php echo $key; ?>].play();
					  $("#video-container-<?php echo $key; ?>").removeClass('video-inactive');
					  $("#video-container-<?php echo $key; ?>").addClass('video-active');
					},
					onEnd: function( options ) {
					  pop[<?php echo $key; ?>].pause();
					  $("#video-container-<?php echo $key; ?>").removeClass('video-active');
					  $("#video-container-<?php echo $key; ?>").addClass('video-inactive');
					}
				});
		
			<?php
			}
		}
		?>
	

		<?php
		if ($images){
			foreach ($images as $key => $image){
			?>
			baseline.code({
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
			});
			<?php
			}
		}
		?>
		
		<?php
		if ($sousTitres){
			foreach ($sousTitres as $key => $sousTitre){
			?>
			baseline.footnote({
				start: <?php echo $sousTitre['start']; ?>,
				end: <?php echo $sousTitre['end']; ?>,
				text: "<?php echo $sousTitre['text']; ?>",
				target: "sstitres-container"					
			});
			<?php
			}
		}
		?>
		
		<?php
		if ($titres){
			foreach ($titres as $key => $titre){
			?>
			baseline.footnote({
				start: <?php echo $titre['start']; ?>,
				end: <?php echo $titre['end']; ?>,
				text: "<?php echo $titre['text']; ?>",
				target: "titres-container"					
			});
			<?php
			}
		}
		?>
		
		<?php
		if ($surTitres){
			foreach ($surTitres as $key => $surTitres){
			?>
			baseline.footnote({
				start: <?php echo $surTitres['start']; ?>,
				end: <?php echo $surTitres['end']; ?>,
				text: "<?php echo $surTitres['text']; ?>",
				target: "surtitres-container"					
			});
			<?php
			}
		}
		?>
		var audio = new Array();
		<?php
		if ($sons){
			foreach ($sons as $key => $son){
			?>
				audio[<?php echo $key; ?>] = Popcorn.smart( "#audio-container", "<?php echo $son['url']; ?>" );
				if (audio[<?php echo $key; ?>].duration()) {
					audio[<?php echo $key; ?>].currentTime(<?php echo $son['cutin']; ?>);
					audio[<?php echo $key; ?>].volume(<?php echo $son['volume']; ?>);
					audio[<?php echo $key; ?>].pause();
				} else {
					audio[<?php echo $key; ?>].on('loadedmetadata', function() {
						audio[<?php echo $key; ?>].currentTime(<?php echo $son['cutin']; ?>);
						audio[<?php echo $key; ?>].volume(<?php echo $son['volume']; ?>);
						audio[<?php echo $key; ?>].pause();
					});
				}
				pop[<?php echo $key; ?>].cue(<?php echo $video['cutout']; ?>, function() {
					pop[<?php echo $key; ?>].currentTime(<?php echo $video['cutin']; ?>);
				});
				
				//déclencher les sons au bon moment
				baseline.code({
					start: <?php echo $son['start']; ?>,
					end: <?php echo $son['end']; ?>,
					onStart: function( options ) {         
					  audio[<?php echo $key; ?>].play();
					},
					onEnd: function( options ) {
					  audio[<?php echo $key; ?>].pause();
					}
				});
				
			<?php
			}
		}
		?>
			
		<?php
		if ($fonds){
			foreach ($fonds as $key => $fond){
			?>
				
				baseline.code({
					start: <?php echo $fond['start']; ?>,
					end: <?php echo $fond['end']; ?>,
					onStart: function( options ) {         
					  $('#fond').css('background', "<?php echo $fond['couleur']; ?>");
					  $('#fond').css('z-index', "<?php echo $fond['niveau']; ?>");
					  $('#fond').show();
					},
					onEnd: function( options ) {
					  $('#fond').hide();
					}
				});
				
			<?php
			}
		}
		?>

		window.onload = function () {
			$("#loading").html('<span class="play">►</span>');
			$("#loading").click(function(){
				$(this).hide();
				<?php 
				if ($_GET['chapiter']){ 
					$chapiter = $_GET['chapiter'] - 1 ;
					echo "baseline.currentTime( ".$chapiter." );";
				}
				?>
				baseline.play();			
			});
		}
	

		

	}, false);
	  
</script>

<style>
	.video-container {
		position: absolute;
		top:0;
		left:0;
		bottom:0;
		right:0;
	}
	.video-container iframe {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.video-active {
		z-index: 2;
	}
	.video-inactive {
		z-index: 0;
	}
	#blank, #fond{
		position:absolute;
		top:0;
		left:0;
		bottom:0;
		right:0;
		z-index:1;
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
	#general-container {
		font-size:32px;
		line-height:36px;
		color:#FFF;
		text-shadow:3px 3px 3px #000;
	}
	#general-container{
		position: fixed;
		top: 15px;
		bottom: 30px;
		left: 15px;
		right: 15px;
	}
	#audio-container{
		z-index:0;
	}
	#controler{
		position:absolute;
		bottom:5px;
		left:15px;
		right:15px;
		height:15px;
		z-index: 10;
	}
	#controler #barre{
		width:100%;
		height:5px;
		position:relative;
	}
	#controler a{
		width: 5px;
		height: 20px;
		position:absolute;
		margin: 0;
	}
	#chapitres {
		position: absolute;
		bottom: -20px;
		left: 0;
		z-index: 20;
		right: 0;
		height: 15px;
	}
	#chapitres a {
		background: #000;
		display: block;
		width: 1px;
		height: 1px;
		position: absolute;
		top: 0;
		left: 50%;
		text-indent: -9999px;
		overflow: hidden;
		cursor: pointer;
		border-radius: 3px;
		border: 3px solid #000;
		margin-left:-5px;
	}
	#cache{
		position:fixed;
		top:0;
		left:0;
		bottom:0;
		right:0;
		z-index:10;
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
</style>
<?php include('inc-panneau.php'); ?>
<div id="general-container">
	<div id="base"></div>
	<div id="timer" class='detail'></div>
	<?php
		foreach ($videos as $key => $video){
			echo '<div id="video-container-'.$key.'" class="video-container video-inactive"></div>';
		}
	?>
	<div id="image-container"></div>
	<div id="sstitres-container"></div>
	<div id="titres-container"></div>
	<div id="surtitres-container"></div>
	<div id="audio-container"></div>
	<div id="blank"></div>
	<div id="fond"></div>
	<div id="cache"></div>
	<div id="loading" class="detail">Loading...</div>
	<div id="chapitres">
		<a href='javascript:void(0)' class='detail marker' style='left:0%'> </a>
		<?php
		$lenght = max($chapiters);
		
		foreach ($chapiters as $key => $chapiter){
			$percent = ($chapiter / $lenght )* 100;
			echo "<a href='?chapiter=".$chapiter."' class='detail' style='left:".$percent."%'>".$chapiter."</a> ";
		}
		?>
	</div>
</div>



<?php 
get_footer(); ?>