javascript:(function(){
	if (window.MARK!==undefined) {
		mark();
	} else {
			document.body.appendChild(document.createElement('script')).src='http://dev.electricgecko.de/mark/bookmarklet.js';
		}
	})();