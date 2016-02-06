<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
	<meta name="author" content="Malte Müller"/>
	<meta name="description" content="Private collection of images, collected silently by M A R K.">
	<meta name="robots" content="noindex, nofollow">

	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	
	<title>M A R K</title>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="vendor/masonry.js"></script>
	<script type="text/javascript">
		
		$(window).load(function(){	
			marked = $('main ul').masonry({
				itemSelector: 'main ul li',
				gutter: 40
			});	
		});

		$(document).ready(function(){
			
			imgdir = 'imgs/';
			images = $('main ul li');
			hidden = null;
		
			// hide sidebar
			$('aside').hide();
			
			// get image size from local storage
			if (localStorage.getItem('MARKsz') === null) {
				// set image size to default value
				sz = parseInt(images.css('width'));
			} else {
				// set image size to stored value
				sz = localStorage.getItem('MARKsz');
					images.css('width',localStorage.getItem('MARKsz')+'px');
			}

			// keyboard controls to adjust image size
			$(window).keydown(function(evt) {	
			  
			  	var colwidth = parseInt(images.css('width'));
			  	var mult = .3; // image resize multiplier for each keypress
			  
			  	// plus
			    if (evt.keyCode === 187) {
					images.css('width', colwidth+colwidth*mult+'px');
					marked.masonry('layout');
					localStorage.setItem('MARKsz',colwidth+colwidth*mult);
			
				// minus
			    } else if (evt.keyCode === 189) {
					images.css('width', colwidth-colwidth*mult+'px');
					marked.masonry('layout');
					localStorage.setItem('MARKsz',colwidth-colwidth*mult);
			    }
			});

			// remove selections by clicking in white space
			$(document).click(function(e){
				
				if (!$(e.target).closest('li').length) {
					
					// remove selected
					if ($('li.selected').length) {
						$('li.selected').removeClass('selected');
						$('main').css({
							'max-width': 'none',
							'margin': '60px auto',
						});
						marked.masonry('layout');
						$('aside').hide();
					}

				} else {
					
					el = $(e.target).closest('li');

					// shift-click images to mark them
					if (e.shiftKey) {
								
						e.preventDefault();
						
						// mark clicked image as selected
						el.toggleClass('selected');
						
						// if at least one image is selected, show sidebar
						if ($('li.selected').length) {
							
							$('main').css({
								'max-width': $(window).width()-$('aside').width(),
								'margin': '60px 0 60px 0'
							});
							marked.masonry('layout');
							$('aside').show();
							
						} else {
							
							$('main').css({
								'max-width': 'none',
								'margin': '60px auto',
							});
							marked.masonry('layout');
							$('aside').hide();
							
						}
					}
				}
			});
			
			// move images to folders, remove images from folders
			$('aside ol li').not('aside ol li:first').each(function(){
				
				$(this).click(function(e){
					
					e.stopPropagation();
										
					// get desitnation folder name
					var folder = $(this).text();
							
					// get image url
					var sel = $('main ul li.selected figure a img');
			
					// pass to move helper
					sel.each(function(){
						
						el = $(this);
						
						thumb = $(this).attr('src');
						url = el.parent().attr('href');;
						
						console.log(thumb);
						console.log(url);
						
						$.post('markmove.php', {f: url, t: thumb, d: folder}).done(function(){
							var findex = el.attr('src').lastIndexOf('/') + 1;
							var sel_filename = el.attr('src').substr(findex);
							var sel_fileurl = imgdir+folder+'/'+sel_filename;
							el.attr('src',sel_fileurl);
							el.parent().attr('href',sel_fileurl);
						});
						$(this).closest('li').addClass(folder);
					});
				});
			})
			
			
			// filter images by folder
			$('nav ol li').click(function(){
				
				if (hidden != null) {
					hidden.appendTo($('main ul'));
					
					marked.masonry('reloadItems');
					marked.masonry('layout');
					
					hidden = null;
				}		
						
				if (!$(this).is('nav ol li:first')) {						
					hidden = images.not('.'+$(this).text());
					marked.masonry('remove',hidden);
					marked.masonry('layout');
				}
				
			});
			
			// delete images
			$('a.del').each(function(){
				$(this).click(function(){
					
					// get image url
					var url = $(this).next().find('img').attr('src');
					
					// pass to delete helper
					$.post('markdel.php', {f: url})
										
					// remove image from view & rearrange layout
					marked.masonry('remove', $(this).parent()).masonry('layout');	
				})
			});
		});
	</script>
	
	<style>
		
		* {
			font-weight: normal;
			font-size: 100%;
		}
		
		body {
			font-family: 'Arial', sans-serif;
			font-weight: normal;
			font-size: 8pt;
			
		}
		
		ul, ol {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		
		header {
			position: fixed;
			top: 10px;
			left: 10px;
			z-index: 10;			
		}
		
		nav {
			display: inline;
			margin-left: 100px;
		}
		
		nav ol {
			display: inline;
		}
		
		nav ol li {
			display: inline;
			margin-right: 20px;
			cursor: pointer;
		}
		
		nav ol li:hover {
			text-decoration: underline;
		}
		
		h1 {
			display: inline;
			letter-spacing: 2px;
		}
		
		main {
			margin: 60px auto;
		}
		
		main ul li {
			width: 250px;
			margin-bottom: 40px;
			
			font-size: 9px;
			font-family: 'Arial', sans-serif;
			letter-spacing: 1px;
		}
		
		main ul li a.del {
			display: none;
			
			font-size: 160%;
			text-decoration: none;
			color: #000;
			line-height: 20px;
			text-align: center;
			
			position: absolute;
			width: 20px;
			height: 20px;
			z-index: 10;
			top: 0;
			right: 0;
		}
		
		main ul li a.del:hover {
			background: #fff;
		}
		
		main ul li:hover a.del {
			display: block;
		}
		
		figure {
			margin: 0;
			padding: 0;
			
		}
	
		main ul li:hover figure > a:after {
			content: ' ';
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
		
			background: rgba(255, 230, 0, 0.5)
		}
		
		main ul li.selected figure > a:after {
			content: ' ';
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
		
			background: rgba(255, 65, 13, 0.5)		
		}
		
		figure span {
			display: block;
		}
		
		img {
			display: block;
			width: 100%;
			height: auto;
		}	
		
		aside {
			position: fixed;
			z-index: 15;
			
			right: 0;
			top: 0;
			height: 100%;
			width: 200px;
			
			background: #efefef;
		}

		aside ol {
			list-style: none;
			padding: 0;
			width: 100%;
		}
		
		aside ol li {
			padding: 0;
			display: block;
			width: 100%;
			height: 60px;
			
			background: #ccc;
			line-height: 20px;
			margin: 0 0 2px 0;
			
			cursor: pointer;
		}
		
		aside ol li:hover {
			background: yellow;
		}
		
		aside ol li span {
			padding: 5px 0 0 5px;
		}
		
		@media only screen 
		and (min-device-width : 320px) 
		and (max-device-width : 568px)  {
			
			ul {
				/* this is hacky but simpler than a js implementation */
				margin-left: -20px;	
			}
			
			li {
				width: 125px;
			}
		}
		
	</style>
</head>

<body>

	<?php
		
		$imgdir = 'imgs'; // main image folder
		$folders = array_filter(glob('imgs/*', GLOB_NOCHECK), 'is_dir'); // read folders in main image folder
		$exp = '-'; // exploder for image names
		$thumb_indicator = 'MARKthumb'; // thumbnail filename prefix
		$images = array();	
		$c = 0;
		
		function remove_thumbs($arr) {
			global $thumb_indicator;
			$thumbs = array_filter($arr, function($var) use ($thumb_indicator) { return preg_match("/\b$thumb_indicator\b/i", $var); });
			$arr = array_diff($arr, $thumbs);
			return $arr;	
		}
		
		// go.
		
		// read main image folder	
		$main_content = glob($imgdir.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
		
		// remove thumbnails
		$main_content = remove_thumbs($main_content);
		
		// add to image object
		foreach ($main_content as $image) {
			$images[$c]['name'] = $image;
			$images[$c]['folder'] = 'imgs';
			$c++;
		}
			
		foreach ($folders as $folder) {
			
			// read folder content
			$folder_content = glob($folder.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
			
			// remove thumbnails
			$folder_content = remove_thumbs($folder_content);
			
			// add to image object
			foreach ($folder_content as $image) {
				$images[$c]['name'] = $image;
				$images[$c]['folder'] = $folder;
				$c++;
			}			
		}
	
		// sort image object
		uasort($images, function($a, $b) {
			return basename($b['name']) - basename($a['name']);
		});
	?>
		
	<header>
		<h1>MARK</h1>
		<nav>
			<ol>
				<li>everything</li>
				<?php
					if (count($folders) > 0) {				
						foreach ($folders as $folder) {
							echo '<li><span>'.basename($folder).'</span></li>';
						}
					}
				?>
			</ol>
		</nav>
	
	</header>
	
	<main>
		<ul>
			<?php
	
				$index = 0;
				
				foreach ($images as $image) {
		
						// parse image info from filename
						$img_info = explode($exp, basename($image['name']));
						$image_date = $img_info[0];
						$image_w = $img_info[1];
						$image_h = $img_info[2];	
						$image_title = $img_info[3];
						$image_thumbnail = substr_replace($image['name'], $exp.$thumb_indicator, strpos($image['name'],$exp), 0);
						
						// self-cleaning: if image is a duplicate, don't show it and delete it from server
						if (in_array($image_title, $displayed_images)) {
						unlink($image);
						
						} else {
							
							// show image
							array_push($displayed_images, $image_title);
							echo '<li id="'.$index.'" class="'.basename($image['folder']).'"><a class="del" href="javascript:void(0);">×</a><figure><a href="'.$image['name'].'"><img width="'.$image_w.'" height="'.$image_h.'" src="'.$image_thumbnail.'" /></a></figure></li>';
							$index++;
						}
				}
			?>
		</ul>

	</main>
	
	<aside>
		<ol>
			<li>everything</li>
			<?php
				
				// list folders 
				if (count($folders) > 0) {				
					foreach ($folders as $folder) {
						echo '<li><span>'.basename($folder).'</span></li>';
					}
				}
			?>
		</ol>
	</aside>
</body>