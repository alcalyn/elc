

var phax = {
    
	action: function(controller, action, params)
    {
		data = params ? params : {} ;
		
		data.phax_controller = controller;
		data.phax_action = action ? action : 'default' ;
		
		jQuery.post(
			phaxConfig.www_script,
			data,
			function(r) {
				phax.reaction(controller, action, r);
			}
		);
	},
	
	reaction: function(controller, action, r)
    {
		action = action ? action : 'default' ;
        
        console.log(controller, action, r);
		
		if(r) {
			try {
				r = JSON.parse(r);
				
				if(r.phax_metadata.has_error) {
					phaxError.reactionError(controller, action, r.phax_metadata.errors);
					return;
				}
			} catch(e) {
				phaxError.reactionFatalError(controller, action, r);
				return;
			}
		} else {
			r = null;
		}
		
		
		if(r.phax_metadata.js_reaction) {
			if(phax.controller_loaded(controller)) {
				phaxCore.callModuleAction(controller, action, r);
			} else {
				phax.load_controller(controller, function() {
					phaxCore.callModuleAction(controller, action, r);
				});
			}
		}
		
	}
    
};



var phaxCore = {
	
	hasFunction: function(controller, fonction)
    {
		return (window[controller] && window[controller][fonction]);
	},
	
	call: function(controller, fonction, arg)
    {
		return window[controller][fonction](arg);
	},
	
	callModuleAction: function(controller, action, r)
    {
		var fonction = action ? 'phax_'+action : 'phax_default';
		
		if(phaxCore.hasFunction(controller, fonction)) {
			return phaxCore.call(controller, fonction, r);
		} else {
			phaxError.reactionUndefined(controller, fonction);
		}
	}
};

