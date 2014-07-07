
$(function () {
    bindVariantSelect();
    bindVariantPersonalization();
    initOptions();
    checkers.init();
    checkersControls.init();
    dependentCriterias();
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
    
    player: undefined,
    
    init: function ()
    {
        if ($('.checkers-active').size()) {
            checkers.party = jsContext.extendedParty;
            checkers.variant = new CheckersVariant(jsContext.extendedParty.parameters);
            checkers.startRefreshing();
            
            if (checkers.getPlayer(false).id === jsContext.player.id) {
                checkers.player = false;
            } else if (checkers.getPlayer(true).id === jsContext.player.id) {
                checkers.player = true;
            }
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
     * Player tries to huff an opponent piece
     * 
     * @param {jQuery} $piece to be huff
     */
    huff: function (coords)
    {
        var data = {
            slugParty:  jsContext.coreParty.slug,
            slugGame:   jsContext.coreParty.game.slug,
            coords: {
                line: coords[0],
                col:  coords[1]
            }
        };
        
        phax.action('checkers', 'huff', data);
    },
    
    /**
     * Naive checks for a move.
     * Return a move instance with jumped pieces if there is.
     * 
     * @param {Array} from
     * @param {Array} to
     * 
     * @returns {Move|null} Move
     */
    checkMove: function (from, to)
    {
        // Check turn
        if (checkers.player !== checkers.party.currentPlayer) {
            checkers.invalidMove(t('not.your.turn'));
            return null;
        }
        
        // Check if from and to are the same
        if ((from[0] === to[0]) && (from[1] === to[1])) {
            return null;
        }
        
        var pieceFrom = checkers.pieceAt(from);
        var pieceTo = checkers.pieceAt(to);
        
        // Check if to is empty
        if (!pieceTo.isFree()) {
            checkers.invalidMove(t('illegalmove.destination.occupied'));
            return null;
        }
        
        // Check if move is diagonal
        if (Math.abs(to[0] - from[0]) !== Math.abs(to[1] - from[1])) {
            checkers.invalidMove(t('illegalmove.must.move.diagonally'), 'simplemove.jpg');
            return null;
        }
        
        var jumpedCoords = [];
        var distance = Math.abs(to[0] - from[0]);
        
        if (!pieceFrom.isKing()) {
            
            if (distance > 2) {
                checkers.invalidMove(t('illegalmove.cannot.move.too.far'), 'move-or-jump.jpg');
                return null;
            }
            
            if (distance === 2) {
                var middle = [
                    (from[0] + to[0]) / 2,
                    (from[1] + to[1]) / 2
                ];

                var pieceMiddle = checkers.pieceAt(middle);

                if (pieceMiddle.isFree()) {
                    checkers.invalidMove(t('illegalmove.cannot.move.too.far'), 'move-or-jump.jpg');
                    return null;
                } else if (pieceMiddle.getColor() === pieceFrom.getColor()) {
                    checkers.invalidMove(t('illegalmove.cannot.jump.own.pieces'), 'jump-own-piece.jpg');
                    return null;
                }
                
                // Check backward capture
                if (!checkers.variant.getBackwardCapture()) {
                    if ((pieceFrom.getColor() === 2) ^ ((to[0] - from[0]) > 0)) {
                        checkers.invalidMove(t('illegalmove.cannot.backward.jump'));
                        return null;
                    }
                }

                jumpedCoords.push(middle);
            }
            
            if (distance === 1) {
                
                // Check if piece goes forward
                if ((pieceFrom.getColor() === 2) ^ ((to[0] - from[0]) > 0)) {
                    checkers.invalidMove(t('illegalmove.cannot.move.back'));
                    return null;
                }
            }
        } else {
            if (checkers.variant.getLongRangeKing()) {
                
                var path = diagonalPath(from, to);
                var middle = null;
                var pieceMiddle = null;
                
                path.forEach(function (value, key) {
                    var p = checkers.pieceAt(value);
                    
                    if (!p.isFree()) {
                        if (null === pieceMiddle) {
                            if (p.getColor() === pieceFrom.getColor()) {
                                checkers.invalidMove(t('illegalmove.cannot.jump.own.pieces'), 'jump-own-piece.jpg');
                                return null;
                            } else {
                                pieceMiddle = p;
                                middle = value;
                            }
                        } else {
                            checkers.invalidMove(t('illegalmove.cannot.jump.two.pieces'));
                            return null;
                        }
                    } else if ((null !== pieceMiddle) && (checkers.variant.getKingStopsBehind())) {
                        checkers.invalidMove(t('illegalmove.king.must.stop.behind'));
                        return null;
                    }
                });
                
                if (middle) {
                    jumpedCoords.push(middle);
                }
                
            } else {
                if (distance > 2) {
                    checkers.invalidMove(t('illegalmove.no.long.range.king'));
                    return null;
                }
                
                if (distance === 2) {
                    var middle = [
                        (from[0] + to[0]) / 2,
                        (from[1] + to[1]) / 2
                    ];

                    var pieceMiddle = checkers.pieceAt(middle);
                    
                    if (pieceMiddle.isFree()) {
                        checkers.invalidMove(t('illegalmove.no.long.range.king'));
                        return null;
                    } else if (pieceMiddle.getColor() === pieceFrom.getColor()) {
                        checkers.invalidMove(t('illegalmove.cannot.jump.own.pieces'), 'jump-own-piece.jpg');
                        return null;
                    } else {
                        jumpedCoords.push(middle);
                    }
                }
            }
        }
        
        return new Move([from, to], jumpedCoords);
    },
    
    /**
     * Naive checks for a huff.
     * Return true of false if query to server can be done
     * 
     * @param {Array} coords
     * 
     * @returns {Boolean}
     */
    checkHuff: function (coords)
    {
        if (checkers.party.lastMove.jumpedCoords.length > 0) {
            checkers.invalidMove(t('illegalmove.cannot.huff.already.jumped'));
            return false;
        }
        
        if (!checkers.party.huff) {
            checkers.invalidMove(t('illegalmove.cannot.huff.this.piece'));
            return false;
        }
        
        // Check if player has already huffed this turn
        if (checkers.party.huff.huffed) {
            checkers.invalidMove(t('illegalmove.cannot.huff.already.huffed'));
            return false;
        }
        
        // Check if piece is huffable
        var huffable = false;
        var huffCoordsCount = checkers.party.huff.huffCoords.length;
        
        for (var i = 0; i < huffCoordsCount; i++) {
            var huffCoord = checkers.party.huff.huffCoords[i];
            
            if ((huffCoord.line === parseInt(coords[0])) && (huffCoord.col === parseInt(coords[1]))) {
                huffable = true;
                break;
            }
        }
        
        if (!huffable) {
            checkers.invalidMove(t('illegalmove.cannot.huff.this.piece'));
            return false;
        }
        
        return true;
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
     * Callback for huff action
     * 
     * @param {Object} r
     */
    huffReaction: function (r)
    {
        if (r.valid) {
            checkers.party = r.party;
        }
        
        checkersControls.huffReaction(r);
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
        if (r.party.huff && (r.party.huff.huffed !== null) && (checkers.party.huff.huffed === null)) {
            checkersControls.huffed(r.party.huff);
            checkers.party.huff = r.party.huff;
        }
        
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
     * Return player instance from boolean (false: 0, true: 1)
     * 
     * @param {boolean} boolean
     * @returns {Object}
     */
    getPlayer: function (boolean)
    {
        if (boolean === undefined) {
            boolean = checkers.party.currentPlayer;
        }
        
        return jsContext.coreParty.slots[boolean ? 1 : 0].player;
    },
    
    /**
     * Interval refreshing when opponent turn
     */
    startRefreshing: function ()
    {
        checkers.stopRefreshing();
        
        checkers.refreshInterval = setInterval(function () {
            if (!checkers.isMyTurn()) {
                checkers.getLastMove();
            }
        }, 2500);
    },
    
    /**
     * @returns {Boolean}
     */
    isMyTurn: function ()
    {
        var turnId      = checkers.getPlayer().id;
        var loggedId    = jsContext.player.id;
        
        return turnId === loggedId;
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
     * @returns {Piece}
     */
    pieceAt: function (coords)
    {
        return new Piece(checkers.party.grid[coords[0]][coords[1]]);
    },
    
    /**
     * Pop a bootstrap modal to notify an invalid move and the reason
     * 
     * @param {String} error reason of why the move is invalid
     */
    invalidMove: function (error, img)
    {
        if (img) {
            var tag = '<img '+
                    'class="img-thumbnail illegalmove-illustration" '+
                    'src="'+jsContext.illustrationsUrl+img+'" '+
                    'alt="Illustration for '+error.replace('illegalmove.', '').replace('.', ' ').replace('%', '')+'" '+
            '/>';
            
            modal.popSimple('<p class="clearfix lead">'+tag+error+'</p>', t('illegal.move'), 'warning');
        } else {
            modal.popSimple('<p class="lead">'+error+'</p>', t('illegal.move'), 'warning');
        }
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
    
    /**
     * Init
     */
    init: function ()
    {
        if ($('.checkers-active').size()) {
            checkersControls.squareSize = jsContext.squareSize;
            checkersControls.enableDrag();
            checkersControls.enableDrop();
            checkersControls.highlightMove(checkers.party.lastMove);
            
            if (checkers.isMyTurn()) {
                checkersControls.myTurn();
            } else {
                checkersControls.notMyTurn();
            }
            
            if (checkers.variant.getBlowUp()) {
                jQuery('body').on('click', '.piece-huffable', function () {
                    checkersControls.huffDetected(jQuery(this));
                    
                    return true;
                });
            }
        }
    },
    
    /**
     * Initialize drag of pieces
     */
    enableDrag: function ()
    {
        $('.piece-controlled').draggable({
            revert: 'invalid',
            start: function (event, ui)
            {
                var $piece = jQuery(ui.helper);
                
                checkersControls
                        .getSquareAt([$piece.attr('data-line'), $piece.attr('data-col')])
                        .addClass('grid-item-highlight-1')
                ;
            },
            stop: function (event, ui)
            {
                var $piece = jQuery(ui.helper);
                
                checkersControls
                        .getSquareAt([$piece.attr('data-line'), $piece.attr('data-col')])
                        .removeClass('grid-item-highlight-1')
                ;
            }
        });
    },
    
    /**
     * Initialize drop of used squares
     */
    enableDrop: function ()
    {
        var squareUsed = checkers.variant.getSquareUsed() ? 'odd' : 'even' ;
        
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
            
            // Remove jumped piece
            if (move.jumpedCoords.length > 0) {
                checkersControls.eat(move.jumpedCoords[0]);
            }
            
            // notify model from move
            checkers.move(coordsFrom, coordsTo);
            
            checkersControls.notMyTurn();
            checkersControls.highlightMove();
            
            return true;
        } else {
            
            // cancel piece move, move it on beginning position
            checkersControls.move($piece, coordsFrom);
            
            return false;
        }
    },
    
    huffDetected: function ($piece)
    {
        var coords = [
            $piece.attr('data-line'),
            $piece.attr('data-col')
        ];
        
        if (checkers.checkHuff(coords)) {
            checkers.huff(coords);
            checkersControls.eat(coords);
            checkersControls.animateHuff(coords);
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
     * Create a piece at coords [line, col] from code
     * 
     * @param {Object} coords
     */
    setPieceAt: function (coords, code)
    {
        checkersControls.getPieceAt(coords).remove();
        
        if (code > 0) {
            var $piece = jQuery('<div class="piece">');
            var player = (code % 2) === 0;
            
            if (player) {
                $piece.addClass('piece-black');
            } else {
                $piece.addClass('piece-white');
            }
            
            if (player === checkers.player) {
                $piece.addClass('piece-controlled ui-draggable');
            }
            
            if (code > 2) {
                $piece.addClass('piece-king');
            }
            
            $piece.attr('data-line', coords[0]);
            $piece.attr('data-col', coords[1]);
            $piece.css(checkersControls.getSquarePositionAt(coords));
            
            jQuery('.grid').append($piece);
        }
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
        
        if (!$piece.hasClass('piece-king')) {
            if (
                    $piece.hasClass('piece-white') && (to[0] === 0) ||
                    $piece.hasClass('piece-black') && (to[0] === (checkers.variant.getBoardSize() - 1))
            ) {
                checkersControls.promote(to);
            }
        }
        
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
        }, 400, function () {
            $piece.remove();
        });
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
            $piece.addClass('piece-king', 400);
        }
    },
    
    /**
     * Refresh pieces without animation
     */
    hardRefresh: function ()
    {
        var boardSize = checkers.variant.getBoardSize();
        
        for (var line = 0; line < boardSize; line++) {
            for (var col = 0; col < boardSize; col++) {
                checkersControls.setPieceAt([line, col], checkers.party.grid[line][col]);
            }
        }
        
        checkersControls.enableDrag();
        checkersControls.highlightMove(checkers.party.lastMove);
        
        if (checkers.isMyTurn()) {
            checkersControls.myTurn();
        } else {
            checkersControls.notMyTurn();
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
            console.log('moved successfully');
            
            if (checkers.isMyTurn()) {
                checkersControls.myTurn();
            } else {
                checkersControls.notMyTurn();
            }
        } else {
            checkersControls.hardRefresh();
            
            checkers.invalidMove(r.error, r.illus);
        }
    },
    
    /**
     * Called then ajax request result has been received by model.
     * Check for huff validity, and revert if not.
     * 
     * @param {Object} r
     */
    huffReaction: function (r)
    {
        if (r.valid) {
            console.log('huffed successfully');
            
            if (checkers.isMyTurn()) {
                checkersControls.myTurn();
            } else {
                checkersControls.notMyTurn();
            }
        } else {
            checkersControls.hardRefresh();
            checkers.invalidMove(r.error, r.illus);
        }
    },
    
    /**
     * Called when opponent played his turn
     * 
     * @param {Object} Move instance
     */
    moved: function (move)
    {
        var lastPath = 0;
        
        // Find where we are in the possible multiple move
        while (0 === checkersControls.getPieceAt([move.path[lastPath].line, move.path[lastPath].col]).length) {
            lastPath++;
            
            if (lastPath >= pathLength) {
                console.log('error: multiple move cannot be animated, no piece on path');
                return;
            }
        }
        
        var pathLength = move.path.length;
        var pathLengthLess1 = pathLength - 1;
        
        // Animate all move until now
        for (; lastPath < pathLengthLess1; lastPath++) {
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

            if (move.jumpedCoords.length > 0) {
                checkersControls.eat(
                        [
                            move.jumpedCoords[lastPath].line,
                            move.jumpedCoords[lastPath].col
                        ]
                );
            }
        }
        
        checkersControls.highlightMove(move);
        checkersControls.myTurn();
    },
    
    /**
     * A piece has been huffed
     * 
     * @param {Object} huff
     */
    huffed: function (huff)
    {
        var huffed = huff.huffed;
        
        checkersControls.eat(huffed);
        checkersControls.animateHuff([huffed.line, huffed.col]);
    },
    
    /**
     * Animation for a huff
     */
    animateHuff: function (coords)
    {
        var $square = checkersControls.getSquareAt(coords);
        
        $square
                .addClass('grid-item-highlight-1')
                .popover({
                    placement: 'top',
                    content: t('blow.up')
                })
                .popover('show')
                .popover('disable')
        ;
        
        setTimeout(function () {
            $square.popover('hide');
        }, 3000);
    },
    
    highlightMove: function (move)
    {
        jQuery('.grid-item-highlight-1').removeClass('grid-item-highlight-1');
        jQuery('.grid-item-highlight-2').removeClass('grid-item-highlight-2');
        
        if (!move) {
            return;
        }
        
        var length = move.path.length;
        
        for (var i = 0; i < length; i++) {
            var coords = move.path[i];
            var $square = checkersControls.getSquareAt([coords.line, coords.col]);
            
            if (i !== (length - 1)) {
                $square.addClass('grid-item-highlight-1');
            } else {
                $square.addClass('grid-item-highlight-2');
            }
        }
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
        
        if ((mixed.line !== undefined) && (mixed.col !== undefined)) {
            return checkersControls.getPieceAt([mixed.line, mixed.col]);
        }
        
        return mixed;
    },
    
    /**
     * Called when it is my turn
     */
    myTurn: function ()
    {
        jQuery('.piece-controlled')
                .addClass('piece-draggable')
                .draggable({disabled: false})
        ;
        
        if (checkers.variant.getBlowUp()) {
            jQuery('.piece:not(.piece-controlled)').addClass('piece-huffable');
        }
    },
    
    /**
     * Called when it is not longer my turn
     */
    notMyTurn: function ()
    {
        jQuery('.piece-draggable')
                .removeClass('piece-draggable')
        ;
        
        jQuery('.piece-controlled')
                .draggable({disabled: true})
        ;
        
        if (checkers.variant.getBlowUp()) {
            jQuery('.piece-huffable').removeClass('piece-huffable');
        }
    }
};

function Piece(code) {
    this.code = code;
    
    this.isFree = function ()
    {
        return 0 === this.code;
    };
    
    this.isKing = function ()
    {
        return this.code > 2;
    };
    
    this.getColor = function ()
    {
        if (0 === this.code) {
            return null;
        }
        
        return ((this.code % 2) === 1) ? 1 : 2 ;
    };
}

function Move(path, jumpedCoords) {
    this.path = path;
    this.jumpedCoords = jumpedCoords || [];
}

/**
 * 
 * @param {Object} from [line, col]
 * @param {Object} to [line, col]
 * @returns {Object} array of coords
 */
function diagonalPath(from, to) {
    var path = [];
    var iterator = [
        (to[0] > from[0]) ? 1 : -1,
        (to[1] > from[1]) ? 1 : -1
    ];
    
    var p = [from[0], from[1]];
    
    while (p[0] !== to[0]) {
        p[0] += iterator[0];
        p[1] += iterator[1];
        
        path.push([p[0], p[1]]);
    }
    
    path.pop();
    
    return path;
}
