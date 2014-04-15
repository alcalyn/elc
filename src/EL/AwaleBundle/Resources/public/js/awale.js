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
     * move number, incremented on each move
     * 
     * @type integer
     */
    moveNumber: 0,
    
    /**
     * string representation of last move
     * 
     * @type String
     */
    lastMove: '',
    
    /**
     * Animation variables
     * 
     * @type Object
     */
    animation: {
        playing:    false,
        start:      null,
    },
    
    
    init: function ()
    {
        if (awale.initialized) {
            return;
        } else {
            awale.initialized = true;
        }
        
        console.log('awale init');
        awale.bindButtons();
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
     * Called then player click one of his own container.
     * 
     * @param {integer} index from 0 to 5
     */
    clickListener: function ($box)
    {
        console.log('kik', $box);
        
        var currentPlayer   = jsContext.extendedParty.currentPlayer;
        var playerContextId = jsContext.player.id;
        var playerCurrentId = jsContext.coreParty.slots[currentPlayer].id;
        
        // Check current player
        if (playerContextId !== playerCurrentId) {
            //alert(t('not.your.turn'));
            //return;
        }
        
        // Check if box is not empty
        if (0 === parseInt($box.find('p').html())) {
            //alert(t('container.is.empty'));
            return;
        }
        
        awale.play($box);
        awale.startAnimation($box);
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
            box:        parseInt($box.data('coords').split(':')[1]),
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
        var seeds       = parseInt($boxStart.find('p').html());
        
        if (0 === seeds) {
            return;
        }
        
        awale.animation.playing = true;
        awale.animation.start   = $boxStart;
        
        awale.highlightContainer($boxStart);
        $boxStart.find('p').html('0');
        
        awale.feedAnimation(row, container, seeds);
    },
    
    /**
     * Recursive animation to animate feeding
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
            awale.highlightContainer($box);
            $box.find('p').html(parseInt($box.find('p').html()) + 1);
            
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
     */
    storeAnimation: function (row, container)
    {
        var $box            = awale.getBox(row, container);
        var seeds           = parseInt($box.find('p').html());
        var currentPlayer   = jsContext.extendedParty.currentPlayer;
        var $attic          = awale.getBox(currentPlayer, 6);
        
        if (currentPlayer === row) {
            awale.animation.playing = false;
            return;
        }
        
        if ((2 === seeds) || (3 === seeds)) {
            setTimeout(function () {
                awale.highlightContainer($box);
                $box.find('p').html(0);
                $attic.find('p').html(parseInt($attic.find('p').html()) + seeds);
                var stop = false;
        
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

                if (!stop) {
                    awale.storeAnimation(row, container);
                }
            }, awaleConfig.animationTime);
        } else {
            awale.animation.playing = false;
        }
    },
    
    /**
     * Play highlight animation on a box.
     * Set container to 6 to select attic.
     * 
     * @param {index|Object} row
     * @param {index} container
     * @returns jQuery item
     */
    highlightContainer: function (row, container)
    {
        var $box = awale.getBox(row, container);
        
        $box
                .css({opacity: 0.15})
                .animate({opacity: 1}, 180)
        ;
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
        /**
         * Refresh attics
         */
        $('#board .attic-p0 p').html(r.grid[0]['attic']);
        $('#board .attic-p1 p').html(r.grid[1]['attic']);
        
        /**
         * Refresh containers
         */
        for (var i = 0; i < 6; i++) {
            $('#board .boxes .row-p0 .box-'+i+' p').html(r.grid[0]['seeds'][i]);
            $('#board .boxes .row-p1 .box-'+i+' p').html(r.grid[1]['seeds'][i]);
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
};
