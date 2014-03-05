

var tictactoe = {
    
    case_size:      140,
    color_X:        'lightCoral',
    color_O:        'lightBlue',
    color_bg_odd:   '#EEE',
    color_bg_even:  '#DDD',
    
    /**
     * Current player, 1 or 2
     * 
     * @var integer
     */
    currentPlayer: 1,
    
    /**
     * Thread from setInterval for refreshing grid
     */
    thread: undefined,
    
    /**
     * True if grid is displaying animation of 3 in a row,
     * and waiting for next grid
     * 
     * @type Boolean
     */
    waitingForNextGrid: false,
    
    
    init: function ()
    {
        tictactoe.bindCases();
        tictactoe.startRefresh();
    },
    
    /**
     * Listen for cases click
     * 
     * @returns {void}
     */
    bindCases: function ()
    {
        $('.grid-item').click(function () {
            $case = $(this);
            
            if ($case.hasClass('grid-value_-')) {
                $coords = $case.attr('id').split('-');
                $line = parseInt($coords[1]);
                $col = parseInt($coords[2]);
                tictactoe.caseClicked($line, $col);
            }
        });
    },
    
    /**
     * Called when an empty case has been clicked,
     * Check if current user is me
     * 
     * @return {Boolean}
     */
    caseClicked: function (line, col)
    {
        if (jsContext.coreParty.slots[tictactoe.currentPlayer].player.id !== jsContext.player.id) {
            alert('Not your turn');
            return false;
        }
        
        tictactoe.set(line, col, tictactoe.currentPlayer === 1 ? 'X' : 'O');
        tictactoe.tick(line, col);
        tictactoe.changeCurrentPlayer();
        return true;
    },
    
    /**
     * @param {integer} line
     * @param {integer} col
     * 
     * @returns {jQuery} jQuery case item from coords
     */
    getCase: function (line, col)
    {
        return $(['#grid', line, col].join('-'));
    },
    
    /**
     * @param {integer} line
     * @param {integer} col
     * 
     * @returns {jQuery} jQuery case item from coords
     */
    getCaseFromIndex: function (i)
    {
        var line = Math.floor(i / 3);
        var col  = i % 3;
        
        return $(['#grid', line, col].join('-'));
    },
    
    /**
     * 
     * @param {integer} line
     * @param {integer} col
     * @param {char} value X, O or -
     * 
     * @returns {Boolean} if succeed
     */
    set: function (line, col, value)
    {
        if (line < 0 || line > 2 || col < 0 || col > 2) {
            console.warn('tictactoe.set must have line and col between 0 and 2, got '+line+' ; '+col);
            return false;
        }
        
        if (['-', 'X', 'O'].indexOf(value) < 0) {
            console.warn('tictactoe.set must be X, O or -, got "'+value+'"');
            return false;
        }
        
        var $case = tictactoe.getCase(line, col);
        
        if ($case.hasClass('grid-value_'+value)) {
            return true;
        }
        
        $case
            .removeClass('grid-value_-')
            .removeClass('grid-value_X')
            .removeClass('grid-value_O')
            .addClass('grid-value_'+value)
        ;
        
        tictactoe.animate($case);
        
        return true;
    },
    
    /**
     * Return sign at coords
     * 
     * @param {integer} line
     * @param {integer} col
     * 
     * @returns {String} X, O or -
     */
    get: function (line, col)
    {
        var $case = tictactoe.getCase(line, col);

        if ($case.hasClass('grid-value_-')) return '-';
        if ($case.hasClass('grid-value_X')) return 'X';
        if ($case.hasClass('grid-value_O')) return 'O';
        
        console.warn('warn : grid in '+line+' ; '+col+' has no symbol class, "-" returned');
        return '-';
    },
    
    /**
     * Set whole grid from string such as "X--O-XO-X"
     * 
     * @param {String} grid
     * 
     * @returns {Boolean} if grid is not at good format
     */
    setGrid: function (grid)
    {
        if (grid.length != 9) {
            console.warn('tictactoe.setGrid, grid does not contains 9 cases : "'+grid+'"');
            return false;
        }
        
        for (var i = 0; i < 9; i++) {
            var line = Math.floor(i / 3);
            var col = i % 3;
            var value = grid.charAt(i);
            
            if (!tictactoe.set(line, col, value)) {
                return false;
            }
        }
        
        return true;
    },
    
    /**
     * Return grid serialized such as "X--O-XO-X"
     * 
     * @returns {String}
     */
    getGrid: function ()
    {
        var grid = '';
        
        for (var i = 0; i < 9; i++) {
            var line = Math.floor(i / 3);
            var col = i % 3;
            
            grid += tictactoe.get(line, col);
        }
        
        return grid;
    },
    
    /**
     * Animate a case checking
     * 
     * @param {jQuery} $case
     * 
     * @returns {void}
     */
    animate: function ($case)
    {
        var border_size     = tictactoe.case_size / 2;
        var border_color    = $case.hasClass('grid-odd') ? tictactoe.color_bg_odd : tictactoe.color_bg_even ;
        
        $case.css({
            border: border_size+'px solid '+border_color,
        });
        
        $case.animate({
            borderWidth: '0px',
        }, 180);
    },
    
    /**
     * Animate a 3 in a row
     * 
     * @param {jQuery} $case
     * 
     * @returns {void}
     */
    animateRow: function ($case0, $case1, $case2)
    {
        var d = 50;
        
        tictactoe.animate($case0);
        
        setTimeout(function () {
            tictactoe.animate($case1);
        }, d);
        
        setTimeout(function () {
            tictactoe.animate($case2);
        }, d * 2);
    },
    
    /**
     * Animate a 3 in a row
     * 
     * @param {jQuery} $case
     * 
     * @returns {void}
     */
    startAnimateRow: function ($case0, $case1, $case2)
    {
        var d = 800;
        
        tictactoe.animateRow($case0, $case1, $case2);
        
        setTimeout(function () {
            tictactoe.animateRow($case0, $case1, $case2);
        }, d);
        
        setTimeout(function () {
            tictactoe.animateRow($case0, $case1, $case2);
        }, d * 2);
    },
    
    /**
     * Switch current player
     * 
     * @returns {void}
     */
    changeCurrentPlayer: function ()
    {
        tictactoe.currentPlayer = 3 - tictactoe.currentPlayer;
    },
    
    /**
     * Begin ajax calls at interval
     * 
     * @returns {void}
     */
    startRefresh: function ()
    {
        if (tictactoe.thread) {
            clearInterval(tictactoe.thread);
        }
        
        tictactoe.thread = setInterval(tictactoe.refresh, 2000);
    },
    
    /**
     * Stop interval thread
     * 
     * @returns {void}
     */
    stopRefresh: function ()
    {
        if (tictactoe.thread) {
            clearInterval(tictactoe.thread);
            tictactoe.thread = undefined;
        }
    },
    
    /**
     * Ajax call for grid refresh
     * 
     * @returns {void}
     */
    refresh: function ()
    {
        phax.action('tictactoe', 'refresh', {extendedPartyId: jsContext.extendedParty.id});
    },
    
    /**
     * Phax reaction for grid refresh
     * 
     * @param {Object} r response
     * @returns {void}
     */
    refreshReaction: function (r)
    {
        tictactoe.setGrid(r.party.grid);
        tictactoe.currentPlayer = r.party.currentPlayer;
        
        if (tictactoe.waitingForNextGrid) {
            if (null === r.winner) {
                tictactoe.waitingForNextGrid = false;
            }
        } else {
            if (null !== r.winner) {
                tictactoe.waitingForNextGrid = true;

                if ('-' !== r.winner) {
                    var row = tictactoe.search3inARow(tictactoe.getGrid());
                    
                    if (row) {
                        var $case0 = tictactoe.getCaseFromIndex(row[0]);
                        var $case1 = tictactoe.getCaseFromIndex(row[1]);
                        var $case2 = tictactoe.getCaseFromIndex(row[2]);

                        tictactoe.startAnimateRow($case0, $case1, $case2);
                    }
                }
            }
        }
    },
    
    /**
     * Check a case
     */
    tick: function (line, col)
    {
        var data = {
            locale:             jsContext.locale,
            party_slug:         jsContext.coreParty.slug,
            extendedPartyId:    jsContext.extendedParty.id,
            coords: {
                line: line,
                col: col
            }
        };
        
        phax.action('tictactoe', 'tick', data);
    },
    
    /**
     * Phax Reaction for case checking
     * 
     * @param {type} r reaction
     * @returns {undefined}
     */
    tickReaction: function (r)
    {
        tictactoe.refreshReaction(r);
    },
    
    search3inARow: function (grid)
    {
        /**
         * Check for winner
         */
        if (tictactoe.brochette(grid, 0, 1, 2)) return [0, 1, 2];
        if (tictactoe.brochette(grid, 3, 4, 5)) return [3, 4, 5];
        if (tictactoe.brochette(grid, 6, 7, 8)) return [6, 7, 8];
        
        if (tictactoe.brochette(grid, 0, 3, 6)) return [0, 3, 6];
        if (tictactoe.brochette(grid, 1, 4, 7)) return [1, 4, 7];
        if (tictactoe.brochette(grid, 2, 5, 8)) return [2, 5, 8];
        
        if (tictactoe.brochette(grid, 0, 4, 8)) return [0, 4, 8];
        if (tictactoe.brochette(grid, 2, 4, 6)) return [2, 4, 6];
        
        return null;
    },
    
    brochette: function (grid, a, b, c)
    {
        var hasRow =
            grid.charAt(a) !== '-' &&
            grid.charAt(a) === grid.charAt(b) &&
            grid.charAt(a) === grid.charAt(c)
        ;
        
        return hasRow;
    }
    
};

jQuery(function () {
    phax.load_controller('tictactoe');
});

