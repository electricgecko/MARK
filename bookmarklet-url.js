javascript:(function(){
	if (window.MARK!==undefined) {
		mark();
	} else {
			if (document.body) {console.log('exists');}
			document.body.appendChild(document.createElement('script')).src='http://dev.electricgecko.de/mark/mark.js';
		}
	})();