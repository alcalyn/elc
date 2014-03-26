
var jsContext			= {}
var phaxConfig			= {};
var phaxLoadControllers	= {};
var translations		= {};

var jsVars = {
	
	load: function (id)
	{
		return $('#elcore_js_vars #'+id).data();
	}
	
}

$(function () {
    jsContext			= jsVars.load('js-context');
    phaxConfig			= jsVars.load('phax-config');
    phaxLoadControllers	= jsVars.load('phax-load-controllers');
    translations		= jsVars.load('translations');
    
    // Load phax controllers
    jQuery.each(phaxLoadControllers, function (index, controller) {
    	phax.load_controller(controller);
    });
    
    qTipInit();
});


function t(s) {
	if (translations[s]) {
		return translations[s];
	} else {
		return s;
	}
}

function beginWith(string, pattern) {
	return string.substring(0, pattern.length) === pattern;
}



function on(boolean, string_true, string_false) {
	if (boolean) {
		return string_true;
	} else {
		if (string_false) {
			return string_false;
		} else {
			return '';
		}
	}
}


function qTipInit() {
    $('[data-qtip]').each(function () {
        
        var $item = $(this);
        
        $(this).qtip({
            content: {
                text:   $item.data('qtip'),
                title:  $item.data('qtipTitle')
            },
            style: {
                classes: 'qtip-rounded qtip-shadow'
            }
        });
    });
}
