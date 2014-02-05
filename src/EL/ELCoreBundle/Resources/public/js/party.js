

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
                slug_party: jsContext.core_party.slug
            };
            
            phax.action('party', 'refresh', data);
        }, 3000);
    },
    
    refreshReaction: function (r)
    {
        party.checkState(r.core_party.state);
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
        
        if (!(parseInt(jsContext.core_party.state) > 0)) {
            console.warn('jsContext.core_party.state = '+jsContext.core_party.state);
            return false;
        }
        
        if (state === jsContext.core_party.state) {
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