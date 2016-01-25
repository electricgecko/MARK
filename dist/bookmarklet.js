(function(){

	// the minimum version of jQuery we want
	var v = "2.1.1";

	// check prior inclusion and version
	if (window.jQuery === undefined || window.jQuery.fn.jquery < v) {
		var done = false;
		var script = document.createElement("script");
		script.src = "http://ajax.googleapis.com/ajax/libs/jquery/" + v + "/jquery.min.js";
		script.onload = script.onreadystatechange = function(){
			if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
				done = true;
				initMyBookmarklet();
			}
		};
		document.getElementsByTagName("head")[0].appendChild(script);
	} else {
		initMyBookmarklet();
	}
	
	function initMyBookmarklet() {
		(window.myBookmarklet = function() {
			
			$(document).ready(function(){	
				
				// create notification element
				
				notify = $('<span id="MARK-notify" class="MARK-notify">Saved to MARK</span>').appendTo('body');
				
				notify.css({
					display: 'block',
					background: '#fff',
					color: '#000',
					position: 'fixed',
					padding: '10px',
					fontSize: '20px',
					fontFamily: 'Arial',
					bottom: 0,
					left: 0,
					zIndex: 9999
				});
				
				notify.hide();
				
				$('img').css({
					border: '5px solid rgba(255, 230, 0, 1)',
					cursor: 'pointer'
				});
				
				$('img').click(function(e){
					e.preventDefault();
				
					
					notify.fadeIn().delay(300).fadeOut();
					
					// get actual image size
					var timg= new Image();
					timg.src = $(this).attr("src");
					var width = timg.width;
					var height = timg.height;	
										
					$.post('http://dev.electricgecko.de/mark/upload.php', {f: $(this).attr('src'), w: width, h: height }).done(function(){
						
					});
				});
				
			});
				
			
		})();
	}

})();