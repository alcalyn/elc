

var surf = {
	
	/**
	 * Contains Url object representing current url
	 */
	current_url: undefined,
	
	
	
	/**
	 * Initialize hash change event,
	 * or redirect to root and append hash if not :
	 * redirect	http://www.domain.com/fr/path/article
	 * to =>	http://www.domain.com/fr/#!/path/article
	 */
	init: function()
	{
		console.log('init');
		
		if (surf.path().length > 0) {
			window.location.href = surf.hashedUrl();
			return;
		}
		
		surf.current_url = new Url(surf.getHash());
		surf.refresh();
		surf.bindLinks();
		
		jQuery(window).bind('hashchange', function() {
			console.log('hash change event');
			
			var url = new Url(surf.getHash());
			
            if(url.differentExceptAnchor(surf.current_url)) {
                if(surf.isValidHash()) {
                	surf.refresh();
                    surf.current_url = url;
                } else {
                    console.warm('surf warning : not a valid url hash : "'+window.location.hash+'"');
                }
	        } else {
                surf.current_url = url;
	        }
		});
	},
	
	
	/**
	 * Change hash, then the new page will be loaded
	 */
	go: function(url)
	{
		window.location.hash = '#!'+url;
	},
	
	
	/**
	 * Return current hash without #!
	 */
	getHash: function() {
        var href = window.location.hash.split('#!');
        return href.length > 1 ? href[1] : '';
	},
	
	
	/**
	 * Load page of current hash
	 */
	refresh: function()
	{
		var hash = surf.getHash();
		var path = '/' + jsContext.locale;
		
		if (hash.charAt(0) != '/') {
			path += '/';
		}
		
		path += hash;
		
		console.log('load '+path);
		
		surf.loadTemplate(path);
	},
	
	
	/**
	 * Load template representing path
	 */
	loadTemplate: function(path)
	{
		phax.action('surf', 'loadTemplate', {path: path});
	},
	
	
	/**
	 * Display new page
	 */
	loadTemplateAction: function(r)
	{
		$('title').html(r.data.title);
		$('#block-body').html(r.data.body);
		
		surf.bindLinks();
	},
	
	
	/**
	 * Return true if hash begins with #!
	 */
	isValidHash: function()
	{
        var hash = window.location.hash;
        return hash.charAt(0) == '#' && hash.charAt(1) == '!';
	},
	
	
	isExternalUrl: function(url)
	{
		var match = url.match(/^([^:\/?#]+:)?(?:\/\/([^\/?#]*))?([^?#]+)?(\?[^#]*)?(#.*)?/);
	    if (typeof match[1] === "string" && match[1].length > 0 && match[1].toLowerCase() !== location.protocol) return true;
	    if (typeof match[2] === "string" && match[2].length > 0 && match[2].replace(new RegExp(":("+{"http:":80,"https:":443}[location.protocol]+")?$"), "") !== location.host) return true;
	    return false;
	},
	
	/**
	 * Return current path before #!
	 * For example : "http://www.domain.com/base/folder/path/article"
	 * If www_root is "/base/folder/"
	 * Returns "path/article".
	 * Should be empty, or if not, should redirect to "http://www.domain.com/fr/#!/path/article"
	 */
	path: function(href)
	{
        if(!href) href = window.location.href;
        
        href = href.split(phaxConfig.www_root).pop();
        var index = href.indexOf('#!');
        if(index >= 0) href = href.substring(0, index);
        
        return href;
	},
	
	
	/**
	 * If url is not hashed (http://www.domain.com/fr/path/article),
	 * return hashed url such as "http://www.domain.com/fr/#!/path/article"
	 * 
	 * Do nothing with external Urls
	 */
	hashedUrl: function(href)
	{
		if (!href) {
			href = window.location.href;
		}
		
		if (surf.isExternalUrl(href)) {
			return href;
		}
		
        var path = surf.path(href);
        
        if (href.indexOf('#!'+path) >= 0) {
            return href;
        } else {
            return phaxConfig.www_root + '#!/' + path;
        }
	},
	
	
	/**
	 * Replace links of current page by hashed links,
	 * and tag them as hashed to not rebind later.
	 */
	bindLinks: function()
	{
		jQuery('a:not(.surf-binded)').each(function() {
			var $this = jQuery(this);
			var href = $this.attr('href');
			
			if (href) {
				$this
					.attr('href', surf.hashedUrl(href))
					.addClass('surf-binded')
				;
			}
		});
	}
	
};


function Url(href) {
    
    this._path = '';
    this._gets = '';
    this._anchor = '';
    
    
    if(href) {
            var explode_anchor = href.split('#');
            this._anchor = (explode_anchor.length > 1) ? explode_anchor[1] : '';
            var explode_gets = explode_anchor[0].split('?');
            this._gets = (explode_gets.length) > 1 ? explode_gets[1] : '';
            this._path = explode_gets[0];
    }
    
    
    this.path = function(setter) {
            if(setter) this._path = setter;
            return this._path;
    }
    
    this.gets = function(setter) {
            if(setter) this._gets = setter;
            return this._gets;
    }
    
    this.anchor = function(setter) {
            if(setter) this._anchor = setter;
            return this._anchor;
    }
    
    this.differentExceptAnchor = function(other) {
            return (this._path != other._path) || (this._gets != other._gets);
    }
    
    this.clone = function() {
            var clone = new Url();
            
            clone._path = this._path;
            clone._gets = this._gets;
            clone._anchor = this._anchor;
            
            return clone;
    }
    
    this.toString = function(without_anchor) {
            var ret = this._path;
            
            if(this._gets.length > 0) ret += '?'+this._gets;
            if((!without_anchor) && (this._anchor.length > 0)) ret += '#'+this._anchor;
            
            return ret;
    }

}

