
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
    
    threadRefresh: undefined,
    
    /**
     * Init
     */
    init: function ()
    {
        console.log('init party');
        
        party.lastCoreParty = jsContext.coreParty;
        
        party.startRefreshing();
    },
    
    /**
     * Start refreshing
     */
    startRefreshing: function ()
    {
        if (!party.threadRefresh) {
            party.threadRefresh = setInterval(party.refresh, 3000);
        }
    },
    
    /**
     * Stop refreshing
     */
    stopRefreshing: function ()
    {
        if (party.threadRefresh) {
            clearInterval(party.threadRefresh);
            party.threadRefresh = undefined;
        }
    },
    
    /**
     * Refresh action
     */
    refresh: function ()
    {
        switch (party.lastCoreParty.state) {
            case party.PREPARATION:
                party.refreshPreparation();
                break;
            
            case party.STARTING:
                party.refreshStarting();
                break;
            
            case party.ACTIVE:
                party.refreshActive();
                break;
            
            case party.ENDED:
                party.refreshEnded();
                break;
            
            default:
                var data = {
                    locale:     jsContext.locale,
                    slugGame:   party.lastCoreParty.game.slug,
                    slugParty:  party.lastCoreParty.slug
                };
                
                phax.action('party', 'refresh', data);
                console.warn('Undefined party state', party.lastCoreParty);
        }
    },
    
    /**
     * Callback for refresh action
     * 
     * Check party state, and if changed,
     * try to call yourGame.partyStateChanged(old_state, new_state);
     * else reload the page
     * 
     * @param {Object} r
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
    },
    
    /**
     * refreshPreparation action
     */
    refreshPreparation: function ()
    {
        var data = {
            locale:     jsContext.locale,
            slugGame:   party.lastCoreParty.game.slug,
            slugParty:  party.lastCoreParty.slug
        };

        phax.action('party', 'refreshPreparation', data);
    },
    
    /**
     * Callback for refreshPreparation
     * 
     * @param {Object} r
     */
    refreshPreparationReaction: function (r)
    {
        party.refreshReaction(r);
    },
    
    /**
     * refreshStarting action
     */
    refreshStarting: function ()
    {
        var data = {
            locale:     jsContext.locale,
            slugGame:   party.lastCoreParty.game.slug,
            slugParty:  party.lastCoreParty.slug
        };

        phax.action('party', 'refreshStarting', data);
    },
    
    /**
     * Callback for refreshStarting
     * 
     * @param {Object} r
     */
    refreshStartingReaction: function (r)
    {
        party.refreshReaction(r);
    },
    
    /**
     * refreshActive action
     */
    refreshActive: function ()
    {
        var data = {
            locale:     jsContext.locale,
            slugGame:   party.lastCoreParty.game.slug,
            slugParty:  party.lastCoreParty.slug
        };

        phax.action('party', 'refreshActive', data);
    },
    
    /**
     * Callback for refreshActive
     * 
     * @param {Object} r
     */
    refreshActiveReaction: function (r)
    {
        party.refreshReaction(r);
    },
    
    /**
     * refreshEnded action
     */
    refreshEnded: function ()
    {
        var data = {
            locale:     jsContext.locale,
            slugGame:   party.lastCoreParty.game.slug,
            slugParty:  party.lastCoreParty.slug
        };

        phax.action('party', 'refreshEnded', data);
    },
    
    /**
     * Callback for refreshEnded
     * 
     * @param {Object} r
     */
    refreshEndedReaction: function (r)
    {
        party.refreshReaction(r);
        
        party.highlightPlayersInRemake(r.playersInRemake);
    },
    
    highlightPlayersInRemake: function (playersInRemake)
    {
        var length = playersInRemake.length;
        
        jQuery('.player-row').removeClass('success');
        jQuery('.player-row .comment').html('');
        
        for (var i = 0; i < length; i++) {
            jQuery('.player-'+playersInRemake[i]).addClass('success');
            jQuery('.player-'+playersInRemake[i]+' .comment')
                    .html('<span class="text-success">'+t('has.remake')+'</span>')
            ;
        }
    }
};
