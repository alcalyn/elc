
function connectClank(callback) {
    console.log('Connecting Clank...');
    
    var conf = jsContext.clank;
    var uri = 'ws://'+conf.host+':'+conf.port;
    
    var clank = Clank.connect(uri);
    
    clank.on('socket/connect', function (session) {
        console.log('Clank connected');
        
        window['clank'] = clank;
        window['clankSession'] = session;
        
        callback();
    });
    
    clank.on('socket/disconnect', function (error) {
        console.log('Clank disconnected for '+error.reason+' with code '+error.code);
    });
}
