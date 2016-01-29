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
				itemSelector: 'li',
				gutter: 40
			});	
			
		});
		
		$(document).ready(function(){
			
			if (localStorage.getItem('MARKsz') === null) {
				// set image size to default value
				sz = parseInt($('main ul li').css('width'));
			} else {
				// set image size to stored value
				sz = localStorage.getItem('MARKsz');
					$('li').css('width',localStorage.getItem('MARKsz')+'px');
					
			}

			// keyboard controls to adjust image size
			$(window).keydown(function(evt) {	
			  
			  	var colwidth = parseInt($('li').css('width'));
			  	var mult = .3; // image resize multiplier for each keypress
			  
			  	// plus
			    if (evt.keyCode === 187) {
					$('li').css('width', colwidth+colwidth*mult+'px');
					marked.masonry('layout');
					localStorage.setItem('MARKsz',colwidth+colwidth*mult);
			
				// minus
			    } else if (evt.keyCode === 189) {
					$('li').css('width', colwidth-colwidth*mult+'px');
					marked.masonry('layout');
					localStorage.setItem('MARKsz',colwidth-colwidth*mult);
			    }
			    
			});
			
			// delete images
			$('a.del').each(function(){
				$(this).click(function(){
					// get image url
					var url = $(this).next().find('img').attr('src');
					
					// pass to delete helper
					$.post('markdel.php', {f: url,})
										
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
		
			
		
		span {
			display: block;
		}
		
		img {
			display: block;
			width: 100%;
			height: auto;
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
			$dirname = 'imgs/';
			$images = glob($dirname.'*.{jpg,jpeg,gif,png}', GLOB_BRACE);
			rsort($images);
		
		
			foreach($images as $image) {
				
			// read image info from filename
			
				$img_info = explode("-", basename($image));
				
				$image_date = $img_info[0];
				$image_w = $img_info[1];
				$image_h = $img_info[2];	
				$image_title = $img_info[3];
	
				echo '<li><a class="del" href="javascript:void(0);">×</a><figure><a href="'.$image.'"><img width="'.$image_w.'" height="'.$image_h.'" src="'.$image.'" /></a></figure></li>';
			}
		
		?>
		
		</ul>

	</main>

</body>