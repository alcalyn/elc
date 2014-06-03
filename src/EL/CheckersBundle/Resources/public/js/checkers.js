
$(function () {
    bindVariantSelect();
    bindVariantPersonalization();
    initOptions();
    checkers.init();
    checkersControls.init();
});


/**
 * Model checkers
 * 
 * @type {Object}
 */
var checkers =
{
    party: undefined,
    
    variant: undefined,
    
    refreshInterval: undefined,
    
    init: function ()
    {
        if ($('.checkers-active').size()) {
            checkers.party = jsContext.extendedParty;
            checkers.variant = new CheckersVariant(jsContext.extendedParty.parameters);
            checkers.startRefreshing();
        }
    },
    
    /**
     * Move a piece from coords to coords
     * 
     * @param {Array} coordsFrom
     * @param {Array} coordsTo
     */
    move: function (coordsFrom, coordsTo)
    {
        var data = {
            slugParty:  jsContext.coreParty.slug,
            slugGame:   jsContext.coreParty.game.slug,
            from: {
                line: coordsFrom[0],
                col:  coordsFrom[1]
            },
            to: {
                line: coordsTo[0],
                col:  coordsTo[1]
            }
        };
        
        phax.action('checkers', 'move', data);
    },
    
    /**
     * Naive checks for a move
     * 
     * @param {type} from
     * @param {type} to
     * 
     * @returns {Move|null} Move
     */
    checkMove: function (from, to)
    {
        // Check if from and to are the same
        if ((from[0] === to[0]) && (from[1] === to[1])) {
            console.log('from = to');
            return null;
        }
        
        var pieceFrom = checkers.pieceAt(from);
        var pieceTo = checkers.pieceAt(to);
        
        // Check if to is empty
        if (!pieceTo.isFree()) {
            console.log('destination occupied');
            return null;
        }
        
        // Check if move is diagonal
        if (Math.abs(to[0] - from[0]) !== Math.abs(to[1] - from[1])) {
            console.log('move not diagonal');
            return null;
        }
        
        var jumpedPieces = [];
        
        if (Math.abs(to[0] - from[0]) === 2) {
            var middle = [
                (from[0] + to[0]) / 2,
                (from[1] + to[1]) / 2
            ];
            
            var pieceMiddle = checkers.pieceAt(middle);
            
            if (pieceMiddle.isFree()) {
                console.log('you must jump over one square');
                return null;
            } else if (pieceMiddle.getColor() === pieceFrom.getColor()) {
                console.log('cannot jump owned pieces');
                return null;
            } else {
                jumpedPieces.push(middle);
            }
        }
        
        return new Move([from, to], jumpedPieces);
    },
    
    /**
     * Callback for move action
     * 
     * @param {Object} r
     */
    moveReaction: function (r)
    {
        if (r.valid) {
            checkers.party = r.party;
        }
        
        checkersControls.moveReaction(r);
    },
    
    /**
     * getLastMove Action, request checkersParty update during opponent turn
     */
    getLastMove: function ()
    {
        var data = {
            slugParty:  jsContext.coreParty.slug,
            slugGame:   jsContext.coreParty.game.slug
        };
        
        phax.action('checkers', 'getLastMove', data);
    },
    
    /**
     * Callback for getLastMove action
     * 
     * @param {Object} r
     */
    getLastMoveReaction: function (r)
    {
        if (r.party.lastMove.number < checkers.party.lastMove.number) {
            return;
        } else if (
                (r.party.lastMove.number > checkers.party.lastMove.number) ||
                (r.party.lastMove.path.length > checkers.party.lastMove.path.length)
        ) {
            checkers.party = r.party;
            checkersControls.moved(r.party.lastMove);
        }
    },
    
    /**
     * Interval refreshing when opponent turn
     */
    startRefreshing: function ()
    {
        checkers.stopRefreshing();
        
        checkers.refreshInterval = setInterval(function () {
            var turnId      = jsContext.coreParty.slots[checkers.party.currentPlayer ? 1 : 0].player.id;
            var loggedId    = jsContext.player.id;
            
            if (turnId !== loggedId) {
                checkers.getLastMove();
            }
        }, 2500);
    },
    
    /**
     * Stop refreshing interval
     */
    stopRefreshing: function ()
    {
        if (checkers.refreshInterval) {
            clearInterval(checkers.refreshInterval);
            checkers.refreshInterval = undefined;
        }
    },
    
    /**
     * Return piece code at coords
     * 
     * @param {Object} coords [line, col]
     * @returns {integer}
     */
    pieceAt: function (coords)
    {
        return new Piece(checkers.party.grid[coords[0]][coords[1]]);
    }
};


/**
 * Layer between model and dom
 * 
 * @type {Object}
 */
var checkersControls =
{
    squareSize: 64,
    
    $squareFrom: undefined,
    
    lastMove: undefined,
    
    /**
     * Init
     */
    init: function ()
    {
        if ($('.checkers-active').size()) {
            checkersControls.enableDragAndDrop();
        }
    },
    
    /**
     * Initialize drag of pieces and drop of used squares
     */
    enableDragAndDrop: function ()
    {
        $('.piece-controlled').draggable({
            revert: 'invalid'
        });
        
        var squareUsed = checkers.variant.getSquareUsed() ? 'even' : 'odd' ;
        
        $('.grid-'+squareUsed).droppable({
            hoverClass: 'piece-over',
            over: function()
            {
                if (!checkersControls.$squareFrom) {
                    checkersControls.$squareFrom = $(this);
                }
            },
            drop: function(event, ui)
            {
                if (checkersControls.$squareFrom) {
                    checkersControls.moveDetected(
                            checkersControls.$squareFrom,
                            $(this),
                            $(ui.draggable)
                    );
                    checkersControls.$squareFrom = undefined;
                }
            }
        });
    },
    
    /**
     * Debug purpose, log coords of clicked square
     */
    getCasesCoordsOnClick: function ()
    {
        $('.grid-item').click(function () {
            console.log($(this).attr('id').split('-').slice(1));
        });
    },
    
    /**
     * Called then a piece has been moved between 2 squares.
     * Check move and send query, or revert move
     * 
     * @param {jQuery} $squareFrom
     * @param {jQuery} $squareTo
     * @param {jQuery} $piece
     */
    moveDetected: function ($squareFrom, $squareTo, $piece)
    {
        var from = $squareFrom.attr('id').split('-');
        var to   = $squareTo  .attr('id').split('-');
        
        var coordsFrom = [
            parseInt(from[1]),
            parseInt(from[2])
        ];
        var coordsTo = [
            parseInt(to[1]),
            parseInt(to[2])
        ];
        
        var move = checkers.checkMove(coordsFrom, coordsTo);
        
        if (move) {
            
            // move piece to the center of its square
            checkersControls.move($piece, coordsTo);
            
            if (move.jumpedPieces.length > 0) {
                checkersControls.eat(move.jumpedPieces[0]);
            }
            
            // memorize this move
            checkersControls.memorizeMove(move);
            
            // notify model from move
            checkers.move(coordsFrom, coordsTo);
            
            return true;
        } else {
            
            // cancel piece move, move it on beginning position
            checkersControls.move($piece, coordsFrom);
            
            return false;
        }
    },
    
    /**
     * Memorize a move so it can be reverted by calling revertMemorizedMove
     * 
     * @param {Object} move
     */
    memorizeMove: function (move)
    {
        checkersControls.lastMove = move;
    },
    
    /**
     * Revert memorized move by moving piece on initial square
     */
    revertMemorizedMove: function ()
    {
        if (checkersControls.lastMove) {
            checkersControls.move(
                    checkersControls.lastMove.path[1],
                    checkersControls.lastMove.path[0]
            );
            
            if (checkersControls.lastMove.jumpedPieces.length > 0) {
                checkersControls.vomit(checkersControls.lastMove.jumpedPieces[0]);
            }
            
            checkersControls.lastMove = undefined;
        }
    },
    
    /**
     * Get piece at coords [line, col]
     * 
     * @param {Array} coords [line, col]
     * 
     * @returns {jQuery}
     */
    getPieceAt: function (coords)
    {
        return $('.piece[data-line='+coords[0]+'][data-col='+coords[1]+']');
    },
    
    /**
     * Get square at coords [line, col]
     * 
     * @param {Array} coords [line, col]
     * 
     * @returns {jQuery}
     */
    getSquareAt: function (coords)
    {
        return $('#grid-'+coords[0]+'-'+coords[1]);
    },
    
    /**
     * Get square at coords [line, col]
     * 
     * @param {Array} coords [line, col]
     * 
     * @returns {jQuery}
     */
    getSquarePositionAt: function (coords)
    {
        return checkersControls.getSquareAt(coords).position();
    },
    
    /**
     * Move a piece on the board
     * 
     * @param {Array|jQuery} mixed $piece or coords from [line, col]
     * @param {Array} to coords [line, col]
     * 
     * @returns {jQuery} $piece moved
     */
    move: function (mixed, to)
    {
        var $piece = checkersControls.getPieceFromMixed(mixed);
        var position = checkersControls.getSquarePositionAt(to);
        
        $piece.animate(position);

        $piece.attr('data-line', to[0]);
        $piece.attr('data-col',  to[1]);
        
        return $piece;
    },
    
    /**
     * Animate a piece eat
     * 
     * @param {jQuery|Object} mixed
     * @returns {undefined}
     */
    eat: function (mixed)
    {
        var $piece = checkersControls.getPieceFromMixed(mixed);
        
        $piece.animate({
            opacity: 0
        }, 400);
    },
    
    /**
     * Undo a piece eat
     * 
     * @param {Object} coords
     */
    vomit: function (coords)
    {
        checkersControls.getPieceAt(coords).animate({
            opacity: 1
        }, 400);
    },
    
    /**
     * Promote piece on coords
     * 
     * @param {Object} coords
     */
    promote: function (coords)
    {
        var $piece = checkersControls.getPieceAt(coords);
        
        if (!$piece.hasClass('piece-king')) {
            $piece.addClass('piece-king');
        }
    },
    
    /**
     * Check all piece to be promoted
     */
    promoteAll: function ()
    {
        var boardSize = checkers.variant.getBoardSize();
        
        for (var col = 0; col < boardSize; col++) {
            if (checkers.party.grid[0][col] > 2) {
                checkersControls.promote([0, col]);
            }
            
            if (checkers.party.grid[boardSize - 1][col] > 2) {
                checkersControls.promote([boardSize - 1, col]);
            }
        }
    },
    
    /**
     * Called then ajax request result has been received by model.
     * Check for move validity, and revert if not.
     * 
     * @param {Object} r
     */
    moveReaction: function (r)
    {
        if (r.valid) {
            console.log('moved successfully')
        } else {
            checkersControls.revertMemorizedMove();
            console.log(r.error);
        }
        
        checkersControls.promoteAll();
    },
    
    /**
     * Called when opponent played his turn
     * 
     * @param {Object} Move instance
     */
    moved: function (move)
    {
        var lastPath = move.path.length - 2;
        
        checkersControls.move(
                [
                    move.path[lastPath].line,
                    move.path[lastPath].col
                ],
                [
                    move.path[lastPath + 1].line,
                    move.path[lastPath + 1].col
                ]
        );
        
        if (move.jumpedPieces.length > 0) {
            checkersControls.eat(
                    [
                        move.jumpedPieces[lastPath].line,
                        move.jumpedPieces[lastPath].col
                    ]
            );
        }
        
        checkersControls.promoteAll();
    },
    
    /**
     * Returns piece jquery instance from everything
     * 
     * @param {jQuery|Object} mixed
     * @returns {jQuery} piece
     */
    getPieceFromMixed: function (mixed)
    {
        if ('Array' === mixed.constructor.name) {
            return checkersControls.getPieceAt(mixed);
        }
        
        if (mixed.line && mixed.col) {
            return checkersControls.getPieceAt([mixed.line, mixed.col]);
        }
        
        return mixed;
    }
};

function Piece(code) {
    this.code = code;
    
    this.isFree = function ()
    {
        return 0 === this.code;
    };
    
    this.getColor = function ()
    {
        if (0 === this.code) {
            return null;
        }
        
        return ((this.code % 2) === 1) ? 1 : 2 ;
    };
}

function Move(path, jumpedPieces) {
    this.path = path;
    this.jumpedPieces = jumpedPieces || [];
}
