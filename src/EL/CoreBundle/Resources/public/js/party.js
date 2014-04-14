 

var party = {
    
    PREPARATION:    1,
    STARTING:       2,
    ACTIVE:         3,
    ENDED:          4,
    
    
    init: function ()
    {
        console.log('init party');
        
        setInterval(function () {
            var data = {
                locale:     jsContext.locale,
                slugGame:   jsContext.coreParty.game.slug,
                slugParty:  jsContext.coreParty.slug
            };
            
            phax.action('party', 'refresh', data);
        }, 3000);
    },
    
    refreshReaction: function (r)
    {
        party.checkState(r.coreParty.state);
    },
    
    /**
     * Check party state, and redirect to good page
     * if state changed
     * 
     * return true if it will redirect
     * 
     * @param {type} party
     * @returns {boolean} true if party state is sync with current page
     */
    checkState: function (state)
    {
        if (!(parseInt(state) > 0)) {
            console.warn('err state = '+state);
            return false;
        }
        
        if (!(parseInt(jsContext.coreParty.state) > 0)) {
            console.warn('jsContext.coreParty.state = '+jsContext.coreParty.state);
            return false;
        }
        
        if (state === jsContext.coreParty.state) {
            return false;
        } else {
            party.refresh();
            return true;
        }
    },
    
    refresh: function ()
    {
        //console.log('reload !');
        window.location.reload();
    }
};
