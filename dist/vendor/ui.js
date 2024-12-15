$(document).ready(function() {
  var imgDir = $('body').data('imgdir');
  var imgsWrap =  $('main ul');
  var imgs = $('main ul li');
  var thumbSrc = 'thumb';
  var mult = .3; // image resize multiplier for each keypress
  var thumbBreakpoint = 600;
  var filetypes = new Array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif');
  var unsortedmsg = 'No unsorted images.'
  var imgSz = '200';
  var activeFilter = '*';
  
  // hide all the things
  $('main, aside, aside #done, aside #close').hide();
  
  // get settings from local storage
  if (localStorage.getItem('MARKsz') !== null) {	    
  	imgSz = parseInt(localStorage.getItem('MARKsz'));
	  resizeImg();
  }
  
  if (localStorage.getItem('MARKbg') != null) {
      $('body').addClass(localStorage.getItem('MARKbg'));
  }

  if (localStorage.getItem('MARKfilter') != null) {
    activeFilter = localStorage.getItem('MARKfilter');
    filterImg();
  }
     
  // add delete feature to all images
  $('a.del').each(function(){
    addDeleteFunction($(this));
  }); 

  resizeImg();  
  
  // LISTEN FOR KEYBOARD COMMANDS
  
  $(window).keydown(function(e) {	
    // +/- for bigger/smaller thumbnails
    if (e.keyCode === 187 || e.keyCode === 189) {
	    if (e.keyCode === 187) {
	      imgSz = Math.floor(imgSz+imgSz*mult);
	    } else {
	      imgSz = Math.floor(imgSz-imgSz*mult);
	    }
	    localStorage.setItem('MARKsz', imgSz);
	    resizeImg();

    // i (to invert background color)
    } else if (e.keyCode === 73) { invertBG(); }
  });
    
    
  // LISTENERS
  
  // remove selections by clicking in white space
  $(document).click(function(e){
    if (!$(e.target).closest('li').length) {
      hideFilter();
    } else {	
      el = $(e.target).closest('li');
      // shift-click images to mark them
      if (e.shiftKey) {				
    			e.preventDefault();
    			// mark clicked image as selected
    			el.toggleClass('selected');
    			// if at least one image is selected, show sidebar
    			if ($('li.selected').length) {
          	showFilter(false);
    			} else {
          	hideFilter();
    			}
    		}
    	}
  });

  // move images to folders, remove images from folders
  $('aside ol li').each(function(){
    $(this).on('click touchend',function(e){
      e.stopPropagation();
        moveImage($(this));
          $('aside #done').fadeIn(function(){
            if (e.type != 'click') {
              hideFilter(); 
            }
          }).fadeOut();
      });
  })

  // filter images by folder
  $('nav ol li').click(function(){
  	$('nav ol li').removeClass();
  	
    if (!$(this).is('nav ol li:first')) {
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

    $(this).addClass('active'); 
    filterImg();
  });

  // download all images or a particular folder as zip archive
  $('#download').click(function() {
    el = $(this);
    el.text('preparing …');
    
    $.post('mark.php', {a: 'download', d:  activeFilter.substr(1)}, function(zipFilename) {
    	window.location.replace('./'+zipFilename);
    	el.text('download everything');
    });
  });

  // resize images on click (useful on larger touch-based devices)
  $('#zoomIn').click(function() {	
    imgSz = Math.floor(imgSz+imgSz*mult);
    localStorage.setItem('MARKsz', imgSz);
    resizeImg();
  });

  $('#zoomOut').click(function() {	
    imgSz = Math.floor(imgSz-imgSz*mult);
    localStorage.setItem('MARKsz', imgSz);
    resizeImg();
  });
  
	// invert background color on touch-based devices
	$('#mobileInvert').click(function(){
		invertBG();	
	})
  
  
  // FEATURES
    
  // show sidebar
  function showFilter() {
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
    $('aside').show();        
  }
    
  // hide sidebar   
  function hideFilter() {
    // clear selected images
    if ($('li.selected').length) {
       $('li.selected').removeClass('selected');
    }
    			    	
    // reset main container width
    $('main').css({
      'max-width': 'none',
      'margin': '60px auto',
    });
        
    $('header > nav').show();
    $('aside').hide(); 
  }

  // move an image to a different folder
  function moveImage(target) {
    
    // get destination folder name
    var folder = $(target).text();
    
    if (folder == $('aside ol li:first-child').text()) {
      folder = '';
    }
      	
    // get selected images
    var sel = imgs.filter('.selected').find('figure a img');
    
    // pass to move helper
    sel.each(function() {
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
      	if (li.hasClass('selected')) {
      		var selection = true;
      	}
      	
        li.removeClass().addClass(folder);
        
        // if no folder was selected, re-attach base class
        if (folder == '') {
          li.addClass('imgs')
        };

        filterImg();  
      	
      }, target));
  			
    });    
  }

  // upload images by drag and drop
  
  // check if browser is capable
  var dragUpload = function() {
    var div = document.createElement('div');
    return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
  }();
    
  if (dragUpload) { 
   var dragto = $('html');
     
   dragto.on('dragenter', function(e) {
     e.stopPropagation();
     e.preventDefault();
     $('body').removeClass('drag');
   }).on('dragover', function(e) {
     e.stopPropagation();
     e.preventDefault();
     if (!$('body').hasClass('drag')) {
       $('body').addClass('drag');
     }
   }).on('dragleave', function(e) {
     e.stopPropagation();
     e.preventDefault();
     $('body').removeClass('drag');
   }).on('drop', function (e) {
    e.preventDefault();
    
    var files = e.originalEvent.dataTransfer.files; 
     
    // simple file type validation
    if (jQuery.inArray(files[0].type, filetypes) > -1) {
      fdata = new FormData();
      fdata.append('u', files[0]);
      fdata.append('a', 'load');
      
      var upload = $.ajax({
        type: 'POST',                
        url: 'mark.php',
        processData: false,
        contentType: false,
        cache:false,
        data: fdata,
        dataType: 'JSON',
        success: function(data) { appendUploadedImg(data); }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);      
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
      success: function(data) { appendUploadedImg(data); }
    });
	})
  
  // helper to add uploaded image to DOM
  function appendUploadedImg(data) {
    var imgName = data.img_name;
    var thumbName = data.thumb_name;
    var imgWidth = data.img_width;
    var imgHeight = data.img_height;
    var uploadedImg= $('<li class="imgs" data-thumb="'+thumbName+'" data-url="'+imgName+'"><a class="del" href="javascript:void(0);">×</a><figure style="height: '+imgSz+'px;"><a href="'+imgName+'"><img width="'+imgWidth+'" height="'+imgHeight+'" src="'+thumbName+'" /></a></figure></li>');
    addDeleteFunction($('a.del', uploadedImg));
    imgsWrap.prepend(uploadedImg);
    console.log('New image uploaded');
    $('body').removeClass('drag');
  }
	

  // add delete function to image
  function addDeleteFunction(btn) {
      btn.click(function() {

      var thumb = btn.next().find('img').attr('src');
  		var url = btn.next().find('a').attr('href');
  		            
  		// pass to delete helper
  		$.post('mark.php', {a: 'del', f: url, t: thumb});
  				
  		// remove image from view
  		btn.closest('li').fadeOut(function(){$(this).remove()});
  	})        
  } 
  
  function filterImg() {
    imgs.not(activeFilter).hide();
    imgs.filter(activeFilter).show();
    
    $('#download').text('download '+activeFilter.substring(1));

    if (activeFilter != '*') {
      $('nav ol li').filter(activeFilter).addClass('active');
	  } else {
      $('nav ol li:first-child').addClass('active');
    }

  	localStorage.setItem('MARKfilter', activeFilter);   
  }
  
  function resizeImg() {
    if (imgsWrap.css('flex-direction') != 'column') {
      
      $('figure > a > img').each(function(){
        var ratio = $(this).attr('width')/$(this).attr('height');
                
        $(this).css({        
          'height': imgSz+'px',
          'width': Math.floor(imgSz*ratio)+'px'
        });
      })
  
      $('li > figure').css('height', imgSz+'px');

    }
  }
  
  // invert background color
  function invertBG() {
	  $('body').toggleClass('inv');
    localStorage.setItem('MARKbg', $('body').attr('class'));	
  }
  
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

  $('main').fadeIn();
});