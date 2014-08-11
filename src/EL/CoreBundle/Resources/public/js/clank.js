jQuery(function () {
    connectClank();
});

/**
 * Connect clank
 */
function connectClank() {
    console.log('Connecting Clank...');
    
    var conf = jsContext.clank;
    var uri = 'ws://'+conf.host+':'+conf.port;
    
    if (document.domain !== conf.host) {
        console.warn('Clank server domain ('+conf.host+') is different to current domain ('+document.domain+'),');
        console.warn('so session cookies could not be sent...');
    }
    
    var clank = Clank.connect(uri);
    window['clank'] = clank;
    
    clank.on('socket/connect', function (session) {
        console.log('Clank connected');
        
        window['clankSession'] = session;
    });
    
    clank.on('socket/disconnect', function (error) {
        console.log('Clank disconnected for '+error.reason+' with code '+error.code);
    });
}
