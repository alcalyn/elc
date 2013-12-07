
var help = {
    
    init: function()
    {
        console.log('Phax help controller has been loaded.');
    },
    
    defaultAction: function(r)
    {
        console.log(r.help_message);
    },
    
    testAction: function(r)
    {
        console.log(r);
    }
    
};