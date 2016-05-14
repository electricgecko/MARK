$(window).load(function(){	
    marked = $('main ul').isotope({
    	itemSelector: 'main ul li',
    	masonry: {
    		gutter: 40
    	}
    });	
});

$(document).ready(function(){
    
    imgdir = $('body').data('imgdir');
    images = $('main ul li');
    hidden = null;
    thumbBreakpoint = 600;
    activeFilter = '*';

    // hide sidebar
    $('aside').hide();
    $('aside #done').hide();
    
    // focus login form
    $('form input').first().focus();
    
    // get image size from local storage
    if (localStorage.getItem('MARKsz') === null) {
    	// set image size to default value
    	sz = parseInt(images.css('width'));
    } else {
    	// set image size to stored value
    	sz = localStorage.getItem('MARKsz');
    		images.css('width',localStorage.getItem('MARKsz')+'px');
    }
    
    // dodgy, magic-number method to load full-sized images if thumbnail size is big
    if (sz >= thumbBreakpoint) {
    	images.each(function(){
    		$(this).find('figure a img').attr('src',$(this).data('url'));
    	});
    }
    
    // keyboard controls to adjust image size
    $(window).keydown(function(evt) {	
      
      	var colwidth = parseInt(images.css('width'));
      	var mult = .3; // image resize multiplier for each keypress
      
      	// plus
        if (evt.keyCode === 187) {
    		images.css('width', colwidth+colwidth*mult+'px');

    		if (colwidth+colwidth*mult >= thumbBreakpoint) {
    			images.each(function(){
    				$(this).find('figure a img').attr('src',$(this).data('url'));
    				console.log('big images');
    			});						
    		}

    		marked.isotope('layout');
    		localStorage.setItem('MARKsz',colwidth+colwidth*mult);
    
    	// minus
        } else if (evt.keyCode === 189) {
    		images.css('width', colwidth-colwidth*mult+'px');

    		if (colwidth-colwidth*mult < thumbBreakpoint) {
    			images.each(function(){
    				$(this).find('figure a img').attr('src',$(this).data('thumb'));
    				console.log('small image');
    			});						
    		}
    		
    		marked.isotope('layout');
    		localStorage.setItem('MARKsz', colwidth-colwidth*mult);
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
    			
    			$('header > nav').show();
    			
    			marked.isotope('layout');
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
    				
    				// adjust main container width to sidebar width
    				$('main').css({
    					'max-width': $(window).width()-$('aside').outerWidth(),
    					'margin': '60px 0 60px 0'
    				});
    				
    				$('header > nav').hide();
    				
    				// add leading 0
    				n = $('li.selected').length;
    				if ((n < 10)) {
    					n = '0'+n
    				}
    				
    				$('aside p > span').text(n);
    				
    				marked.isotope('layout');
    				$('aside').show();
    				
    			} else {
    			
    			// reset main container width
    			$('main').css({
    					'max-width': 'none',
    					'margin': '60px auto',
    				});
    				
    				$('header > nav').show();
    				
    				marked.isotope('layout');
    				$('aside').hide();
    				
    			}
    		}
    	}
    });
    
    
    // move images to folders, remove images from folders
    $('aside ol li').each(function(){
    	
    	$(this).click(function(e){
    		
    		e.stopPropagation();
    							
    		// get destination folder name
    		var folder = $(this).text();
    		
    		if (folder == $('aside ol li:first-child').text()) {
    			folder = '';
    		}
    				
    		// get image url
    		var sel = $('main ul li.selected figure a img');
    
    		// pass to move helper
    		sel.each(function(){

    			var thumb = $(this).attr('src');
    			var file = $(this).parent().attr('href');
    			
    			$.post('mark.php', {a: 'move', f: file, t: thumb, d: folder}).done($.proxy(function(){
    				
    				// set correct urls for image and thumb
    				$(this).attr('src', $(this).data('url'));
    				$(this).parent().attr('href', $(this).data('thumb'));
    				
    				// keep selection
    				if ($(this).closest('li').hasClass('selected')) { var selection = true; }
                    $(this).closest('li').removeClass().addClass(folder);
    				if (selection) {$(this).closest('li').addClass('selected')};
    				
    				// re-apply active filter
                    marked.isotope({filter: activeFilter});
    			}, this));	
    		});

    		$('aside #done').fadeIn().fadeOut();
    	});
    })
    

    // filter images by folder
    $('nav ol li').click(function(){
    			
    	if (!$(this).is('nav ol li:first')) {
    		activeFilter = '.'+$(this).text();			
    	} else {
    		activeFilter = '*';	
    	}
    	
    	marked.isotope({filter: activeFilter});
    	
    });
    
    // delete images
    $('a.del').each(function(){
    	$(this).click(function(){

            var thumb = $(this).next().find('img').attr('src');
    		var url = $(this).next().find('a').attr('href');

    		// pass to delete helper
    		$.post('mark.php', {a: 'del', f: url, t: thumb});
    							
    		// remove image from view & rearrange layout
    		marked.isotope('remove', $(this).parent()).isotope('layout');	
    	})
    });
});