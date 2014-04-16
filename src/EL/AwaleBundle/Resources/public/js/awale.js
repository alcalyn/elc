$(function () {
    awale.init();
});

var awale =
{
    /**
     * true if function init has been called
     * 
     * @type Boolean
     */
    initialized: false,
    
    /**
     * Thread instance for refreshing
     * 
     * @type integer
     */
    threadCheck: undefined,
    
    /**
     * Animation variables
     * 
     * @type Object
     */
    animation: {
        playing:    false,
        start:      null,
        callbacks:  []
    },
    
    
    init: function ()
    {
        // Check if we are on active screen
        if (0 === $('.awale-active').size()) {
            return;
        }
        
        if (awale.initialized) {
            return;
        } else {
            awale.initialized = true;
        }
        
        console.log('awale init');
        
        awale.bindButtons();
        
        if (!awale.isMyTurn()) {
            awale.startChecking();
        }
    },
    
    /**
     * Relative to model grid
     * 
     * Select box from row and container.
     * Set container to 6 to select attic.
     * 
     * @param {index|Object} row
     * @param {index}        container
     * 
     * @returns jQuery item
     */
    getBox: function (row, container)
    {
        if ('object' === (typeof row)) {
            return row;
        }
        
        if (6 === container) {
            return $('#board .attic-p'+row);
        } else {
            return $('#board .boxes .row-p'+row+' .box-'+container);
        }
    },
    
    /**
     * Return true if it is my turn to play
     * 
     * @returns {Boolean}
     */
    isMyTurn: function ()
    {
        var currentPlayer   = jsContext.extendedParty.currentPlayer;
        var playerContextId = jsContext.player.id;
        var playerCurrentId = jsContext.coreParty.slots[currentPlayer].player.id;
        
        return playerContextId === playerCurrentId;
    },
    
    /**
     * Called then player click one of his own container.
     * 
     * @param {jQuery} $box clicked
     */
    clickListener: function ($box)
    {
        console.log('kik', $box);
        
        // Check current player
        if (!awale.isMyTurn()) {
            alert(t('not.your.turn'));
            return;
        }
        
        // Check if box is not empty
        if (0 === parseInt($box.find('p').html())) {
            alert(t('container.is.empty'));
            return;
        }
        
        awale.play($box);
        awale.startAnimation($box);
        awale.startChecking();
    },
    
    /**
     * Send to server move made by player
     * 
     * @param {jQuery} $box which the player move
     */
    play: function ($box)
    {
        var data =
        {
            slugParty:  jsContext.coreParty.slug,
            box:        parseInt($box.data('coords').split(':')[1])
        };
        
        phax.action('awale', 'play', data);
    },
    
    /**
     * Callback for play action
     * 
     * @param {Object} r
     */
    playReaction: function (r)
    {
        jsContext.coreParty     = r.coreParty;
        jsContext.extendedParty = r.awaleParty;
    },
    
    /**
     * Play move animation
     * 
     * @param {integer|Object} row index
     * @param {integer} container index
     * @returns {void}
     */
    startAnimation: function (row, container)
    {
        if (awale.animation.playing) {
            return;
        }
        
        var $boxStart   = awale.getBox(row, container);
        var seeds       = parseInt($boxStart.find('.value').html());
        
        if (0 === seeds) {
            return;
        }
        
        awale.animation.playing = true;
        awale.animation.start   = $boxStart;
        
        awale.highlightContainer($boxStart, null, -seeds);
        $boxStart.find('.value').html('0');
        
        awale.feedAnimation(row, container, seeds);
    },
    
    /**
     * Recursive animation to animate feeding
     * 
     * @param {integer} row
     * @param {integer} container
     * @param {integer} seeds
     */
    feedAnimation: function (row, container, seeds)
    {
        if ('object' === (typeof row)) {
            var data    = row.data('coords').split(':');
            row         = parseInt(data[0]);
            container   = parseInt(data[1]);
            console.log('parse', row, container);
        }
        
        // Calculate next box, except start box from
        var $box;
        
        do {
            if (0 === row) {
                if (5 === container) {
                    row = 1;
                } else {
                    container++;
                }
            } else {
                if (0 === container) {
                    row = 0;
                } else {
                    container--;
                }
            }
            
            $box = awale.getBox(row, container);
        } while ($box.is(awale.animation.start));
        
        // Wait
        setTimeout(function () {
            // highlight and increment next box
            awale.highlightContainer($box, null, 1);
            $box.find('.value').html(parseInt($box.find('.value').html()) + 1);
            
            // decrement hand seeds
            seeds--;
            
            // if we have still seeds in hands, continue
            // else store seeds
            if (seeds > 0) {
                awale.feedAnimation(row, container, seeds);
            } else {
                awale.storeAnimation(row, container);
            }
        }, awaleConfig.animationTime);
    },
    
    /**
     * Recursive animation to animate storing in attic
     * 
     * @param {integer} row
     * @param {integer} container
     */
    storeAnimation: function (row, container)
    {
        var $box            = awale.getBox(row, container);
        var seeds           = parseInt($box.find('.value').html());
        var currentPlayer   = parseInt(awale.animation.start.data('coords').split(':')[0]);
        var $attic          = awale.getBox(currentPlayer, 6);
        
        if ((row !== currentPlayer) && (2 === seeds || 3 === seeds)) {
            setTimeout(function () {
                // highlight box
                awale.highlightContainer($box, null, -seeds);
                
                // highlight attic
                awale.highlightContainer($attic, null, seeds);
                
                // set box value to 0
                $box.find('.value').html(0);
                
                // increment attic
                var atticValue = parseInt($attic.find('.value').html()) + seeds;
                $attic.find('p').html(atticValue);
                
                // update score
                $('#players .p'+currentPlayer+' .score').html(atticValue);
                
                // check previous box
                if (0 === row) {
                    if (0 === container) {
                        row = 1;
                    } else {
                        container--;
                    }
                } else {
                    if (5 === container) {
                        row = 0;
                    } else {
                        container++;
                    }
                }

                awale.storeAnimation(row, container);
                
            }, awaleConfig.animationTime);
        } else {
            awale.stopAnimation();
        }
    },
    
    callAfterAnimation: function (callback)
    {
        awale.animation.callbacks.push(callback);
    },
    
    /**
     * Says that the animation is ended
     */
    stopAnimation: function ()
    {
        for (var i = 0; i < awale.animation.callbacks.length; i++) {
            awale.animation.callbacks[i]();
        }
        
        awale.animation.playing     = false;
        awale.animation.start       = null;
        awale.animation.callbacks   = [];
    },
    
    /**
     * Play highlight animation on a box.
     * Set container to 6 to select attic.
     * 
     * @param {index|Object} row
     * @param {index} container
     * @param {integer} change (optional) display +n or -n over value
     * @returns jQuery item
     */
    highlightContainer: function (row, container, change)
    {
        var $box = awale.getBox(row, container);
        
        $box
                .css({opacity: 0.15})
                .animate({opacity: 1}, 180)
        ;
        
        console.log(row, container, change);
        
        if (change) {
            var $change = $('<p class="change">');
            
            if (change > 0) {
                $change
                        .addClass('positive')
                        .html('+ '+change)
                ;
            }
            if (change < 0) {
                $change
                        .addClass('negative')
                        .html('- '+(-change))
                ;
            }
            
            $box.prepend($change);
            
            $change.animate({
                marginTop:  '-24px',
                opacity:    0
            }, 1200, function () {
                $change.remove();
            });
        }
    },
    
    /**
     * Start interval refreshing
     */
    startChecking: function ()
    {
        if (!awale.threadCheck) {
            awale.threadCheck = setInterval(awale.refresh, awaleConfig.checkInterval);
        }
    },
    
    /**
     * Stop interval refreshing
     */
    stopChecking: function ()
    {
        if (awale.threadCheck) {
            clearInterval(awale.threadCheck);
            awale.threadCheck = undefined;
        }
    },
    
    /**
     * Ask server to refresh board values
     */
    refresh: function ()
    {
        phax.action('awale', 'refresh', {slugParty: jsContext.coreParty.slug});
    },
    
    /**
     * Callback for refresh action
     * 
     * @param {Object} r
     */
    refreshReaction: function (r)
    {
        if (awale.animation.playing) {
            return;
        }
        
        if (!awale.isMyTurn()) {
            if (r.awaleParty.lastMove !== jsContext.extendedParty.lastMove) {
                console.log('opponent played');
                
                var nextMove = r.awaleParty.lastMove.split('|');
                var lastMove = jsContext.extendedParty.lastMove.split('|');
                
                if (parseInt(nextMove[0]) !== (parseInt(lastMove[0]) + 1)) {
                    console.log('check turn weird, not the next: ', lastMove, nextMove);
                }
                
                awale.startAnimation(jsContext.extendedParty.currentPlayer, nextMove[1]);
                awale.stopChecking();
                awale.callAfterAnimation(function () {
                    awale.updateClient(r);
                });
                
                return;
            }
        }
        
        awale.updateClient(r);
    },
    
    /**
     * Hard update (no animation) of board values
     * 
     * @param {Object} r
     */
    updateClient: function (r)
    {
        jsContext.coreParty     = r.coreParty;
        jsContext.extendedParty = r.awaleParty;
        
        var grid = r.awaleParty.grid;
        
        /**
         * Refresh attics
         */
        $('#board .attic-p0 .value').html(grid[0]['attic']);
        $('#board .attic-p1 .value').html(grid[1]['attic']);
        
        /**
         * Refresh containers
         */
        for (var i = 0; i < 6; i++) {
            $('#board .boxes .row-p0 .box-'+i+' .value').html(grid[0]['seeds'][i]);
            $('#board .boxes .row-p1 .box-'+i+' .value').html(grid[1]['seeds'][i]);
        }
        
        /**
         * Refresh scores
         */
        $('#players .p0 .score').html(jsContext.coreParty.slots[0].score);
        $('#players .p1 .score').html(jsContext.coreParty.slots[1].score);
    },
    
    /**
     * Add listener to player's containers
     * 
     * @returns {undefined}
     */
    bindButtons: function ()
    {
        $('#board .boxes .row:eq(1) button').each(function () {
            var $box = $(this);
            $box.click(function () {
                awale.clickListener($box);
            });
        });
    }
};


/**
 * Constains awale configuration values
 */
var awaleConfig =
{
    /**
     * @var integer
     */
    animationTime: 200,
    
    /**
     * @var integer
     */
    checkInterval: 1500
};
