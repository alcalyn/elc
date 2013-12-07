
var help = {
    
    init: function()
    {
        console.log('Phax help controller has been loaded.');
    },
    
    defaultAction: function(r)
    {
        console.log(r.phax_metadata.message);
    },
    
    testAction: function(r)
    {
        console.log(r);
    },
    
    pingAction: function(r)
    {
        console.log(r.phax_metadata.message);
    }
    
};