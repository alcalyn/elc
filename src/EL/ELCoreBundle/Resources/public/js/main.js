$(function() {
    phaxConfig = jQuery('#phax-config').data();
});

var phaxConfig = null;




var slot = {
    
    init: function()
    {
        console.log('init slot controller');
    },
    
    testAction: function(r)
    {
        console.log(r.help_message);
    }
    
}


