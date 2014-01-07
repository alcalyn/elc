
var lastr = null;

var surf = {
	
	loadTemplate: function(path)
	{
		phax.action('surf', 'loadTemplate', {path: path});
	},
	
	loadTemplateAction: function(r)
	{
		console.log('ok ', r);
		lastr = r;
	}
	
};