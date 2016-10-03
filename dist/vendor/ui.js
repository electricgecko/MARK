$(window).load(function(){	
    marked = $('main ul').isotope({
    	itemSelector: 'main ul li',
    	masonry: {
    		gutter: 40
    	},
    	filter: activeFilter
    }, $('main ul').fadeIn(100));
    
    if (activeFilter == '.imgs' && $(activeFilter).length == 0) {
        showmessage(unsortedmsg);
    }
});

    
// show a message to the user
function showmessage(msg) {
    if ($('main').children('p').length > 0 ) {
    	$('main > p').html(msg);
    } else {
    	$('main').prepend('<p>'+msg+'</p>');		
    }
}

// remove message
function removemessage() {
    $('main p').remove();
}

// invert background color
function invertBG() {
	$('body').toggleClass('inv');
	localStorage.setItem('MARKbg', $('body').attr('class'));	
}

$(document).ready(function(){
    
    imgdir = $('body').data('imgdir');
    images = $('main ul li');
    hidden = null;
    thumbBreakpoint = 600;
    activeFilter = '*';
    filetypes = new Array('image/jpeg', 'image/png', 'image/gif');
    
    unsortedmsg = 'No unsorted images.'

    // hide sidebar
    $('aside').hide();
    $('aside #done').hide();
    
    // hide images until sorted
    $('main ul').hide();
    
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
    
    // get background color from local storage
    if (localStorage.getItem('MARKbg') != null) {
        $('body').addClass(localStorage.getItem('MARKbg'));
    }
    
    // if set, get active filter from local storage
    if (localStorage.getItem('MARKfilter') != null) {
        activeFilter = localStorage.getItem('MARKfilter');

        switch (activeFilter) {
        case '*':
            setActive = $('nav ol li').first();
            break;
        case '.imgs':
            setActive = $('nav ol li').last();
            break;
        default:
            setActive = $('nav ol li:contains('+activeFilter.substr(1)+')');          
        }
        
        setActive.addClass('active');
    }
   
    // dodgy, magic-number method to load full-sized images if thumbnails are set to a big size
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
    			});						
    		}
    		
    		marked.isotope('layout');
    		localStorage.setItem('MARKsz', colwidth-colwidth*mult)

    	// i (to invert background color)
        } else if (evt.keyCode === 73) {
			invertBG();
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
    
    
    // add delete function to image
    function addDeleteFunction(btn) {
        btn.click(function(){

            var thumb = btn.next().find('img').attr('src');
    		var url = btn.next().find('a').attr('href');
    		            
    		// pass to delete helper
    		$.post('mark.php', {a: 'del', f: url, t: thumb});
    							
    		// remove image from view & rearrange layout
    		marked.isotope('remove', btn.parent()).isotope('layout');	
    	})        
    }   
     
    // moves an image to a different folder
    
    function moveImage(target) {
	// get destination folder name
    		var folder = $(target).text();
    		
    		if (folder == $('aside ol li:first-child').text()) {
    			folder = '';
    		}
    				
    		// get image url
    		var sel = $('main ul li.selected figure a img');
    
    		// pass to move helper
    		sel.each(function(){
        		
        		var item = $(this)
    			var thumb = $(this).attr('src');
    			var file = $(this).parent().attr('href');
    			var li = $(this).closest('li');
    			
    			$.post('mark.php', {a: 'move', f: file, t: thumb, d: folder}).done($.proxy(function(){  				

    				// determine new correct urls for image and thumb
    				if (li.attr('class').split(' ')[0] != 'imgs') { 
        				var pre = '';
    				} else {
        				var pre = 'imgs/';
    				}

                    var newurl = li.data('url').replace(li.attr('class').split(' ')[0],pre+folder);
                    li.data('url', newurl);
                    var newthumb = li.data('thumb').replace(li.attr('class').split(' ')[0], pre+folder);
                    li.data('thumb', newthumb);

    				// apply urls	
    				item.attr('src', li.data('thumb'));
    				item.parent().attr('href', li.data('url'));

    				// keep selection & add appropiate classes
    				if (li.hasClass('selected')) { var selection = true; }
                    li.removeClass().addClass(folder);
                    if (folder == '') { li.addClass('imgs') };
    				if (selection) { li.addClass('selected') };
    			
                    // re-apply active filter
                    if (activeFilter == '.imgs' && $(activeFilter).length == 0) {
                        showmessage(unsortedmsg);
                    }
                    marked.isotope({filter: activeFilter});
    				
    			}, target));	
    		});

    		$('aside #done').fadeIn().fadeOut();        
    }
    
    // move images to folders, remove images from folders
    $('aside ol li').each(function(){
    	$(this).click(function(e){
    		e.stopPropagation();
            moveImage($(this));
    	});
    })


    // long press images on mobile to filter to folder
    // not yet implemented
    images.each(function(){
        $(this).longpress(function(e) {
            
            /*
            
            e.preventDefault();
            
            $('aside').show();
            
            // TO DO
            // STYLE ASIDE OVERLAY ON MOBILE
            
            */
    });
        
    })

    // filter images by folder
    $('nav ol li').click(function(){
        $('nav ol li').removeClass();
    	if (!$(this).is('nav ol li:first') ) {
        	if ($(this).is('nav ol li:last')) {
                activeFilter = '.imgs';
                if ($(activeFilter).length == 0) {
	            	showmessage(unsortedmsg);   
                }
        	} else {
            	removemessage();
    		    activeFilter = '.'+$(this).text();
            }        
    	} else {
        	removemessage();
    		activeFilter = '*';	
    	}
    	
    	marked.isotope({filter: activeFilter});
    	localStorage.setItem('MARKfilter', activeFilter);
    	$(this).addClass('active');
    });
    
    // delete images
    $('a.del').each(function(){
        addDeleteFunction($(this));
    }); 
    
    
    // upload images by drag and drop
    
    // check if browser is capable
    var isAdvancedUpload = function() {
      var div = document.createElement('div');
      return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    }();
    
    if (isAdvancedUpload) { 
                
       var dragto = $('html');
       
       dragto.on('dragenter', function(e) {
           e.stopPropagation();
           e.preventDefault();
           
       }).on('dragover', function(e) {
            e.stopPropagation();
            e.preventDefault();
            $('body').addClass('drag');
            
       }).on('dragleave', function(e) {
           e.stopPropagation();
           e.preventDefault();
           $('body').removeClass('drag');
           
       }).on('drop', function (e) {
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            
            // simple file type validation
            if (jQuery.inArray(files[0].type, filetypes) > -1) {
             
                var fdata = new FormData();
                fdata.append( 'u', files[0] );
                fdata.append( 'a', 'load');     
              
                var upload = $.ajax({
                    type: 'POST',                
                    url: 'mark.php',
                    processData: false,
                    contentType: false,
                    cache:false,
                    data: fdata,
                    dataType: 'JSON',
                    success: function(data) {
                        var imgName = data.img_name;
                        var thumbName = data.thumb_name;
                        var theIMG = $('<li class="imgs" data-thumb="'+thumbName+'" data-url="'+imgName+'" style="width: '+images.css('width')+';"><a class="del" href="javascript:void(0);">×</a><figure><a href="'+imgName+'"><img src="'+thumbName+'" /></a></figure></li>');
                    
                        // add delete feature
                        addDeleteFunction($('a.del', theIMG));
                    
                        $('img', theIMG).load(function(){
                    
                            // add to collection
                            marked.prepend(theIMG).isotope('prepended', theIMG)
                            marked.isotope('layout');
                          
                            $('body').removeClass('drag');
                                              
                        });
                    }
                }) 
            }
        })
        }  

	// upload images on touch-based devices
	$('#mobileUpload').change(function() {
    	
        var fdata = new FormData();
        fdata.append( 'u', $('#mobileUpload')[0].files[0] );
        fdata.append( 'a', 'load');     
              
              $.ajax({
                 type: "POST",                
                 url: "mark.php",
                 processData: false,
                 contentType: false,
                 cache:false,
                 data: fdata,
                 dataType: 'JSON',
                 success: function(data) {
                     var imgName = data.img_name;
                     var thumbName = data.thumb_name;
                     var theIMG = $('<li class="imgs" data-thumb="'+thumbName+'" data-url="'+imgName+'" style="width: '+images.css('width')+';"><a class="del" href="javascript:void(0);">×</a><figure><a href="'+imgName+'"><img src="'+thumbName+'" /></a></figure></li>');
                 
                     // add delete feature
                     addDeleteFunction($('a.del', theIMG));
                 
                     $('img', theIMG).load(function(){
                 
                         // add to collection
                         marked.prepend(theIMG).isotope('prepended', theIMG)
                         marked.isotope('layout');
                       
                         $('body').removeClass('drag');
                                           
                     });
                 }
              });
	})
	
	// invert background color on touch-based devices
	$('#mobileInvert').click(function(){
		invertBG();	
	})
});