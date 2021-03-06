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
        
        awale.bindButtons();
        
        if (awale.isMyTurn()) {
            awale.highlightMyRows(true);
        } else {
            awale.startChecking();
        }
        
        awale.initSeeds();
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
            slugGame:   jsContext.coreParty.game.slug,
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
        
        awale.highlightMyRows(false);
        
        awale.animation.playing = true;
        awale.animation.start   = $boxStart;
        
        awale.flashContainer($boxStart, null, -seeds);
        $boxStart.find('.value').html('0');
        awale.updateSeeds($boxStart, null, 0);
        
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
        } else {
            row         = parseInt(row);
            container   = parseInt(container);
            seeds       = parseInt(seeds);
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
            awale.flashContainer($box, null, 1);
            var newSeedsCount = parseInt($box.find('.value').html()) + 1;
            $box.find('.value').html(newSeedsCount);
            awale.updateSeeds($box, null, newSeedsCount);
            
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
                awale.flashContainer($box, null, -seeds);
                
                // highlight attic
                awale.flashContainer($attic, null, seeds);
                
                // set box value to 0
                $box.find('.value').html(0);
                awale.updateSeeds($box, null, 0);
                
                // increment attic
                var atticValue = parseInt($attic.find('.value').html()) + seeds;
                $attic.find('.value').html(atticValue);
                awale.updateSeeds($attic, null, atticValue);
                
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
    
    /**
     * Init seeds display at page loading
     */
    initSeeds: function ()
    {
        jQuery('.box').each(function () {
            var $box = jQuery(this);
            var count = parseInt($box.find('.value').html());
            
            awale.updateSeeds($box, null, count);
        });
    },
    
    /**
     * Update real seeds pictures in boxes
     * 
     * @param {integer} row
     * @param {integer} container
     * @param {integer} seeds
     */
    updateSeeds: function (row, container, seeds, disableMoveALittle)
    {
        var $seeds = awale.getBox(row, container).find('.seeds');
        var count = $seeds.find('.seed').size();
        
        if (seeds === count) {
            return;
        }
        
        if (seeds < count) {
            $seeds.find('.seed:nth-last-child(-n+'+(count - seeds)+')').remove();
        }
        
        if (seeds > count) {
            var add = seeds - count;
            
            for (var i = 0; i < add; i++) {
                $seed = awale.createSeed();
                
                $seed.css({
                    left: Math.random() * 24 + 7,
                    top: Math.random() * 28 + 2
                });
                
                $seeds.append($seed);
            }
        }
        
        if (!disableMoveALittle) {
            awale.moveALittle(row, container);
        }
    },
    
    /**
     * Create a random seed
     * 
     * @returns {jQuery} new seed item
     */
    createSeed: function ()
    {
        var rand = Math.floor(Math.random() * 10);
        
        var $seed = jQuery('<div class="seed">');
        
        $seed.css({
            backgroundPosition: (24 * rand)+'px 0'
        });
        
        return $seed;
    },
    
    /**
     * Move all seeds in a box a little to notify an update
     * 
     * @param {integer} row
     * @param {integer} container
     */
    moveALittle: function (row, container)
    {
        var $seeds = awale.getBox(row, container).find('.seeds');
        
        $seeds.find('.seed').each(function () {
            $seed = jQuery(this);
            position = $seed.position();
            
            position.left += Math.random() * 2 - 1;
            position.top  += Math.random() * 2 - 1;
            
            $seed.css({
                left: position.left+'px',
                top:  position.top +'px'
            });
        });
    },
    
    /**
     * Add class highlight to my boxes if boolean is true
     * 
     * @param {boolean} boolean
     */
    highlightMyRows: function (boolean)
    {
        if (boolean) {
            jQuery('.row-bottom .box').addClass('highlight');
        } else {
            jQuery('.row-bottom .box').removeClass('highlight');
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
    flashContainer: function (row, container, change)
    {
        var $box = awale.getBox(row, container);
        
        $box
                .css({opacity: 0.50})
                .animate({opacity: 1}, 180)
        ;
        
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
        var data =
        {
            slugParty:  jsContext.coreParty.slug,
            slugGame:   jsContext.coreParty.game.slug,
        };
        
        phax.action('awale', 'refresh', data);
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
                var nextMove = r.awaleParty.lastMove.split('|');
                var lastMove = jsContext.extendedParty.lastMove.split('|');
                
                if (parseInt(nextMove[0]) !== (parseInt(lastMove[0]) + 1)) {
                    console.log('check turn weird, not the next: ', lastMove, nextMove);
                }
                
                awale.startAnimation(jsContext.extendedParty.currentPlayer, nextMove[1]);
                awale.stopChecking();
                awale.callAfterAnimation(function () {
                    awale.updateClient(r);
                    awale.highlightMyRows(true);
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
        awale.updateSeeds(0, 6, grid[0]['attic'], true);
        awale.updateSeeds(1, 6, grid[1]['attic'], true);
        
        /**
         * Refresh containers
         */
        for (var i = 0; i < 6; i++) {
            $('#board .boxes .row-p0 .box-'+i+' .value').html(grid[0]['seeds'][i]);
            $('#board .boxes .row-p1 .box-'+i+' .value').html(grid[1]['seeds'][i]);
            awale.updateSeeds(0, i, grid[0]['seeds'][i], true);
            awale.updateSeeds(1, i, grid[1]['seeds'][i], true);
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
    },
    
    partyStateChanged: function (before, now)
    {
        if ((before === party.ACTIVE) && (now === party.ENDED)) {
            setTimeout(function () {
                location.reload();
            }, 3000);
        } else {
            location.reload();
        }
    },
    
    /**
     * @returns {Boolean} whether board is reversed
     */
    isBoardReversed: function ()
    {
        return jQuery('#board').hasClass('reversed');
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
