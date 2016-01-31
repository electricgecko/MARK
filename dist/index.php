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
			
			$('aside').hide();
			
			if (localStorage.getItem('MARKsz') === null) {
				// set image size to default value
				sz = parseInt($('main ul li').css('width'));
			} else {
				// set image size to stored value
				sz = localStorage.getItem('MARKsz');
					$('main ul li').css('width',localStorage.getItem('MARKsz')+'px');
					
			}

			// keyboard controls to adjust image size
			$(window).keydown(function(evt) {	
			  
			  	var colwidth = parseInt($('li').css('width'));
			  	var mult = .3; // image resize multiplier for each keypress
			  
			  	// plus
			    if (evt.keyCode === 187) {
					$('main ul li').css('width', colwidth+colwidth*mult+'px');
					marked.masonry('layout');
					localStorage.setItem('MARKsz',colwidth+colwidth*mult);
			
				// minus
			    } else if (evt.keyCode === 189) {
					$('main ul li').css('width', colwidth-colwidth*mult+'px');
					marked.masonry('layout');
					localStorage.setItem('MARKsz',colwidth-colwidth*mult);
			    }
			});
			
			// shift-click images to mark them
			$('main ul li').click(function(e) {
				if (e.shiftKey) {
					
					e.preventDefault();
					
					// mark clicked image as selected
					$(this).toggleClass('selected');
					
					// if at least one image is selected, show sidebar
					if ($('li.selected').length) {
						$('aside').show();
					} else {
						$('aside').hide();
					}
				}
			});
			
			// move images to folders, remove images from folders
			
			$('aside ol li').not('aside ol li:first').each(function(){
				$(this).click(function(){
										
					// get desitnation folder name
					var dir = $(this).text();
							
					// get image url
					var sel = $('main ul li.selected figure a img');
					
					// pass to move helper
					sel.each(function(){
						url = $(this).attr('src');
						$.post('markmove.php', {f: url, d: dir})
					})
					
				});
			})
			
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
		body {
			font-family: 'Arial', sans-serif;
		}
		
		h1 {
			font-weight: normal;
			font-size: 8pt;
			letter-spacing: 2px;
			
			position: fixed;
			top: 10px;
			left: 10px;
		}
		
		main {
			margin: 60px auto;
		}
		
		ul {
			list-style: none;
		}
		
		li {
			width: 250px;
			margin-bottom: 40px;
			
			font-size: 9px;
			font-family: 'Arial', sans-serif;
			letter-spacing: 1px;
		}
		
		li a.del {
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
		
		li a.del:hover {
			background: #fff;
		}
		
		li:hover a.del {
			display: block;
		}
		
		figure {
			margin: 0;
			padding: 0;
			
		}
	
		li:hover figure > a:after {
			content: ' ';
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
		
			background: rgba(255, 230, 0, 0.5)
		}
		
		li.selected figure > a:after {
			content: ' ';
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
		
			background: rgba(255, 65, 13, 0.5)		
		}
		
		span {
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
			width: 300px;
			
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
	
	<h1>MARK</h1>
	
	<main>
		
		<ul>

		<?php
			$imgdir = 'imgs';
			$folders = array_filter(glob('imgs/*', GLOB_NOCHECK), 'is_dir');
			$images = array();
		
			// get images, including subfolders
			
			$images = glob($imgdir.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
			
			foreach ($folders as $folder) {
				$t = glob($folder.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
				$images = array_merge($images,$t);	
			}
			
			// sort images, ingoring folders
			
			function cmp($a, $b) {
				$a = basename($a);
				$b = basename($b);
				
				if ($a == $b) {
					return 0;
				}	
					return ($a < $b) ? 1 : -1;
			}
			
			uasort($images, 'cmp');
	
			
			foreach($images as $image) {
				
			// read image info from filename
			
				$img_info = explode("-", basename($image));
				
				$image_date = $img_info[0];
				$image_w = $img_info[1];
				$image_h = $img_info[2];	
				$image_title = $img_info[3];
				
				// self-cleaning: if image is a duplicate, don't show it and delete it from server
				
				if (in_array($image_title, $imgs)) {
					unlink($image);
				} else {
					array_push($imgs, $image_title);
					echo '<li><a class="del" href="javascript:void(0);">×</a><figure><a href="'.$image.'"><img width="'.$image_w.'" height="'.$image_h.'" src="'.$image.'" /></a></figure></li>';
				}
			
			}
		
		
		?>
		
		</ul>

	</main>
	
	<aside>
		<ol>
			<li>everything</li>
		<?php
			
			if (count($folders) > 0) {				
				foreach ($folders as $folder) {
					echo '<li><span>'.basename($folder).'</span></li>';
				}

			} else {
				echo 'no folders';
			}
		?>
		</ol>
		
	</aside>
	

</body>