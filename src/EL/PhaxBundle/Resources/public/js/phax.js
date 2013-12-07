

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
            if(r.phax_metadata.has_error) {
                phaxError.reactionError(controller, action, r.phax_metadata.errors);
                return;
            }
		}
		
		
		if(r.phax_metadata.trigger_js_reaction) {
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

var phaxError = {

	reactionError: function(controller, action, errors) {
		console.log('Phax reaction error in '+controller+'::'+action);
		console.log(errors);
		alert(errors[0]);
	},
	
	reactionFatalError: function(controller, action, r) {
		console.log('Phax reaction error (JSON parse error) in '+controller+'::'+action+', r = '+r);
	},
	
	reactionUndefined: function(controller, action) {
		console.log('Phax reaction undefined : '+controller+'::'+action);
	},
	
	jsFileNotFound: function(controller, status) {
		console.log('Phax error : file "controllers/'+controller+'/'+controller+'.js" not found'+(status ? ' (code = '+status+')' : ''));
	},
	
	jsFileHasError: function(controller) {
		console.log('Phax Javascript error : file "controllers/'+controller+'/'+controller+'.js" contains javascript errors');
	},
	
	bindFunctionForTagUndefined: function(tagName) {
		console.log('phax bind function for tag "'+tagName+'" undefined');
	},
	
	controllerNotFound: function(controller_name) {
		console.log('phax Error : controller not found : "'+controller_name+'"');
	}
	
};


