(function(){
	// check prior inclusion and version
	if (window.jQuery === undefined) {
		var done = false;
		var script = document.createElement("script");
		script.src = "https://code.jquery.com/jquery-2.2.4.min.js";
		script.onload = script.onreadystatechange = function(){
			if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
				done = true;
				initMARK();
			}
		};
		document.body.appendChild(script);
	} else {
		initMARK();
	}
	
	function initMARK() {
		(window.MARK = function() {
			
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
				
				
				// mark img elements
				$('img').css({
					position: 'relative',
					zIndex: '9999',
					border: '5px solid rgba(255, 230, 0, 1)',
					cursor: 'pointer'
				});
				
				// kill link elements around images to prevent leaving the page instead of saving image
				$('img').each(function(){
					$(this).off();
					if ($(this).closest('a').length) {
						var el = $(this).closest('a');
						el.off();
						el.attr('href','javascript:void(0);');
						el.attr('data-url','#');
					}
				
				})					
				
				$('img').click(function(e){
					
					e.preventDefault();
					
					// get actual image size
					var timg= new Image();
					timg.src = $(this).attr("src");
					
					// var itype = ('jpg','jpeg','png','gif');
					
					src = $(this)[0].src;
					
					// remove php vars					
					if (src.indexOf('?') > -1) {
						var url = src.substr(0, src.indexOf('?'));
					} else {
						var url = src;
					}
					
					// add http:// if necessary
					if (src.indexOf('https://') < 0) {
						url = url.replace('//','https://');
					}
					
					$.post(installdir+'/mark.php', {a: 'load', f: url}).done(function(data){
						notify.fadeIn().delay(300).fadeOut();
						console.log(data);
					});
				});
				
			});
				
			
		})();
	}

})();
