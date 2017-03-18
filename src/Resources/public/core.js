
AjaxRequest.toggleFeaturedArticle = function(el, id) {
	el.blur();

	var image = $(el).getFirst('img'),
		featured = (image.get('data-state') == 1);

	// Backwards compatibility
	if (image.get('data-state') === null) {
		featured = (image.src.indexOf('featured_') == -1);
		console.warn('Using a featured toggle without a "data-state" attribute is deprecated. Please adjust your Contao DCA file.');
	}

	// Send the request
	if (!featured) {
		image.src = AjaxRequest.themePath + 'icons/featured.svg';
		image.set('data-state', 1);
		new Request.Contao().post({'action':'toggleFeaturedArticle', 'id':id, 'state':1, 'REQUEST_TOKEN':Contao.request_token});
	} else {
		image.src = AjaxRequest.themePath + 'icons/featured_.svg';
		image.set('data-state', 0);
		new Request.Contao().post({'action':'toggleFeaturedArticle', 'id':id, 'state':0, 'REQUEST_TOKEN':Contao.request_token});
	}

	return false;	
}
