
var party =
{
    PREPARATION:    1,
    STARTING:       2,
    ACTIVE:         3,
    ENDED:          4,
    
    /**
     * Instance of coreParty
     * 
     * @type {Object}
     */
    lastCoreParty: undefined,
    
    /**
     * Init
     */
    init: function ()
    {
        console.log('init party');
        
        party.lastCoreParty = jsContext.coreParty;
        
        setInterval(party.refresh, 3000);
    },
    
    /**
     * Refresh action
     */
    refresh: function ()
    {
        var data = {
            locale:     jsContext.locale,
            slugGame:   party.lastCoreParty.game.slug,
            slugParty:  party.lastCoreParty.slug
        };

        phax.action('party', 'refresh', data);
    },
    
    /**
     * Callback for refresh action
     * 
     * Check party state, and redirect to good page
     * if state changed
     * 
     * @param {type} party
     */
    refreshReaction: function (r)
    {
        if (r.coreParty.state !== party.lastCoreParty.state) {
            if (window[r.coreParty.game.name]['partyStateChanged']) {
                window[r.coreParty.game.name]['partyStateChanged'](party.lastCoreParty.state, r.coreParty.state);
            } else {
                location.reload();
            }
        }
        
        party.lastCoreParty = r.coreParty;
    }
};
