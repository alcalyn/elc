
function CheckersVariant(binaryValue) {
    
    /**
     * @var {integer}
     */
    this.binaryValue = binaryValue || 0;
    
    
    /**
     * @param {integer} criteria
     * @param {boolean} boolean
     * 
     * @return BitwiseValue
     */
    this.set = function (criteria, boolean)
    {
        if (boolean) {
            this.binaryValue = this.binaryValue | criteria;
        } else {
            this.binaryValue = this.binaryValue & (~criteria);
        }
        
        return this;
    };
    
    /**
     * @param {integer} criteria
     * 
     * @return {boolean}
     */
    this.get = function (criteria)
    {
        return (this.binaryValue & criteria) > 0;
    };
    
    /**
     * @param {integer} value
     * 
     * @return BitwiseValue
     */
    this.setBinaryValue = function (value)
    {
        this.binaryValue = value;
        
        return this;
    };
    
    /**
     * @return {integer}
     */
    this.getBinaryValue = function ()
    {
        return this.binaryValue;
    };
    
    /**
     * Set boardSize
     *
     * @param {integer} boardSize
     * @return {CheckersVariant}
     */
    this.setBoardSize = function (boardSize)
    {
        if (boardSize > CheckersVariant.BOARD_SIZE) {
            throw new Error('Board size must be <= '+CheckersVariant.BOARD_SIZE+', got '+boardSize);
        }
        
        if (boardSize < 0) {
            throw new Error('Board size must be >= 0, got '+boardSize);
        }
        
        this.binaryValue = this.binaryValue & (~CheckersVariant.BOARD_SIZE);
        this.binaryValue = this.binaryValue | boardSize;
        
        return this;
    };

    /**
     * Get boardSize
     *
     * @return {integer}
     */
    this.getBoardSize = function ()
    {
        return this.binaryValue & CheckersVariant.BOARD_SIZE;
    };

    /**
     * Set squareUsed
     *
     * @param {boolean} squareUsed
     * @return {CheckersVariant}
     */
    this.setSquareUsed = function (squareUsed)
    {
        return this.set(CheckersVariant.SQUARE_USED, squareUsed);
    };

    /**
     * Get squareUsed
     *
     * @return {boolean}
     */
    this.getSquareUsed = function ()
    {
        return this.get(CheckersVariant.SQUARE_USED);
    };

    /**
     * Set rightSquare
     *
     * @param {boolean} rightSquare
     * @return {CheckersVariant}
     */
    this.setRightSquare = function (rightSquare)
    {
        return this.set(CheckersVariant.RIGHT_SQUARE, rightSquare);
    };

    /**
     * Get rightSquare
     *
     * @return {boolean}
     */
    this.getRightSquare = function ()
    {
        return this.get(CheckersVariant.RIGHT_SQUARE);
    };

    /**
     * Set backwardCapture
     *
     * @param {boolean} backwardCapture
     * 
     * @return {CheckersVariant}
     */
    this.setBackwardCapture = function (backwardCapture)
    {
        return this.set(CheckersVariant.BACKWARD_CAPTURE, backwardCapture);
    };

    /**
     * Get backwardCapture
     *
     * @return {boolean}
     */
    this.getBackwardCapture = function ()
    {
        return this.get(CheckersVariant.BACKWARD_CAPTURE);
    };

    /**
     * Set longRangeKing
     *
     * @param {boolean} longRangeKing
     * @return {CheckersVariant}
     */
    this.setLongRangeKing = function (longRangeKing)
    {
        return this.set(CheckersVariant.LONG_RANGE_KING, longRangeKing);
    };

    /**
     * Get longRangeKing
     *
     * @return {boolean}
     */
    this.getLongRangeKing = function ()
    {
        return this.get(CheckersVariant.LONG_RANGE_KING);
    };

    /**
     * Set menJumpKing
     *
     * @param {boolean} menJumpKing
     * @return {CheckersVariant}
     */
    this.setMenJumpKing = function (menJumpKing)
    {
        return this.set(CheckersVariant.MEN_JUMP_KING, menJumpKing);
    };

    /**
     * Get menJumpKing
     *
     * @return {boolean}
     */
    this.getMenJumpKing = function ()
    {
        return this.get(CheckersVariant.MEN_JUMP_KING);
    };

    /**
     * Set kingPassing
     *
     * @param {boolean} kingPassing
     * @return {CheckersVariant}
     */
    this.setKingPassing = function (kingPassing)
    {
        return this.set(CheckersVariant.KING_PASSING, kingPassing);
    };

    /**
     * Get kingPassing
     *
     * @return {boolean}
     */
    this.getKingPassing = function ()
    {
        return this.get(CheckersVariant.KING_PASSING);
    };

    /**
     * Set maximumCapture
     *
     * @param {boolean} maximumCapture
     * @return {CheckersVariant}
     */
    this.setMaximumCapture = function (maximumCapture)
    {
        return this.set(CheckersVariant.MAXIMUM_CAPTURE, maximumCapture);
    };

    /**
     * Get maximumCapture
     *
     * @return {boolean}
     */
    this.getMaximumCapture = function ()
    {
        return this.get(CheckersVariant.MAXIMUM_CAPTURE);
    };

    /**
     * Set blowUp
     *
     * @param {boolean} blowUp
     * @return {CheckersVariant}
     */
    this.setBlowUp = function (blowUp)
    {
        return this.set(CheckersVariant.BLOW_UP, blowUp);
    };

    /**
     * Get blowUp
     *
     * @return {boolean}
     */
    this.getBlowUp = function ()
    {
        return this.get(CheckersVariant.BLOW_UP);
    };

    /**
     * Set forceCapture
     *
     * @param {boolean} forceCapture
     * @return {CheckersVariant}
     */
    this.setForceCapture = function (forceCapture)
    {
        return this.set(CheckersVariant.FORCE_CAPTURE, forceCapture);
    };

    /**
     * Get forceCapture
     *
     * @return {boolean}
     */
    this.getForceCapture = function ()
    {
        return this.get(CheckersVariant.FORCE_CAPTURE);
    };

    /**
     * Set letDo
     *
     * @param {boolean} letDo
     * @return {CheckersVariant}
     */
    this.setLetDo = function (letDo)
    {
        return this.set(CheckersVariant.LET_DO, letDo);
    };

    /**
     * Get letDo
     *
     * @return {boolean}
     */
    this.getLetDo = function ()
    {
        return this.get(CheckersVariant.LET_DO);
    };

    /**
     * Set firstPlayer
     *
     * @param {boolean} firstPlayer
     * @return {CheckersVariant}
     */
    this.setFirstPlayer = function (firstPlayer)
    {
        return this.set(CheckersVariant.FIRST_PLAYER, firstPlayer);
    };

    /**
     * Get firstPlayer
     *
     * @return {boolean}
     */
    this.getFirstPlayer = function ()
    {
        return this.get(CheckersVariant.FIRST_PLAYER);
    };
    
    /**
     * Check if this variant is equals to an other
     * 
     * @param {CheckersVariant} checkerVariant
     * 
     * @return {boolean}
     */
    this.equals = function (checkerVariant)
    {
        return this.getBinaryValue() === checkerVariant.getBinaryValue();
    };
};

CheckersVariant.BOARD_SIZE        = 15;
CheckersVariant.SQUARE_USED       = 16;
CheckersVariant.RIGHT_SQUARE      = 32;
CheckersVariant.BACKWARD_CAPTURE  = 64;
CheckersVariant.LONG_RANGE_KING   = 128;
CheckersVariant.MEN_JUMP_KING     = 256;
CheckersVariant.KING_PASSING      = 512;
CheckersVariant.MAXIMUM_CAPTURE   = 1024;
CheckersVariant.BLOW_UP           = 2048;
CheckersVariant.FORCE_CAPTURE     = 4096;
CheckersVariant.LET_DO            = 8192;
CheckersVariant.FIRST_PLAYER      = 16384;


/**
 * Bind events related to variant selection
 * (when user change select option)
 */
function bindVariantSelect() {
    $('select#variant-select').change(function () {
        var parameters = parseInt($('select#variant-select option:selected').attr('value'));
        
        if (0 === parameters) {
            return;
        }
        
        var variant = new CheckersVariant(parameters);
        
        $('#el_core_options_type_extendedOptions_boardSize').val(variant.getBoardSize());
        $('#el_core_options_type_extendedOptions_squareUsed').prop('checked', variant.getSquareUsed());
        $('#el_core_options_type_extendedOptions_rightSquare').prop('checked', variant.getRightSquare());
        $('#el_core_options_type_extendedOptions_backwardCapture').prop('checked', variant.getBackwardCapture());
        $('#el_core_options_type_extendedOptions_longRangeKing').prop('checked', variant.getLongRangeKing());
        $('#el_core_options_type_extendedOptions_menJumpKing').prop('checked', variant.getMenJumpKing());
        $('#el_core_options_type_extendedOptions_kingPassing').prop('checked', variant.getKingPassing());
        $('#el_core_options_type_extendedOptions_maximumCapture').prop('checked', variant.getMaximumCapture());
        $('#el_core_options_type_extendedOptions_blowUp').prop('checked', variant.getBlowUp());
        $('#el_core_options_type_extendedOptions_forceCapture').prop('checked', variant.getForceCapture());
        $('#el_core_options_type_extendedOptions_letDo').prop('checked', variant.getLetDo());
        $('#el_core_options_type_extendedOptions_firstPlayer').prop('checked', variant.getFirstPlayer());
    });
}

/**
 * Bind events related to variant personalization
 * (when user click on checkboxes or change input)
 */
function bindVariantPersonalization() {
    $('.checkers-personalization input').change(refreshSelectOption);
}

/**
 * Refresh select option after personalization
 */
function refreshSelectOption() {
    var variant = getVariantFromForm();
    
    var $option = $('select#variant-select option[value='+variant.getBinaryValue()+']');
    
    if ($option.size()) {
        $('select#variant-select').val(variant.getBinaryValue());
    } else {
        $('select#variant-select').val(0);
    }
}

/**
 * Return an instance of CheckersVariant from form
 * 
 * @return {CheckersVariant}
 */
function getVariantFromForm() {
    var variant = new CheckersVariant();
    
    return variant
            .setBoardSize($('#el_core_options_type_extendedOptions_boardSize').val())
            .setSquareUsed($('#el_core_options_type_extendedOptions_squareUsed').prop('checked'))
            .setRightSquare($('#el_core_options_type_extendedOptions_rightSquare').prop('checked'))
            .setBackwardCapture($('#el_core_options_type_extendedOptions_backwardCapture').prop('checked'))
            .setLongRangeKing($('#el_core_options_type_extendedOptions_longRangeKing').prop('checked'))
            .setMenJumpKing($('#el_core_options_type_extendedOptions_menJumpKing').prop('checked'))
            .setKingPassing($('#el_core_options_type_extendedOptions_kingPassing').prop('checked'))
            .setMaximumCapture($('#el_core_options_type_extendedOptions_maximumCapture').prop('checked'))
            .setBlowUp($('#el_core_options_type_extendedOptions_blowUp').prop('checked'))
            .setForceCapture($('#el_core_options_type_extendedOptions_forceCapture').prop('checked'))
            .setLetDo($('#el_core_options_type_extendedOptions_letDo').prop('checked'))
            .setFirstPlayer($('#el_core_options_type_extendedOptions_firstPlayer').prop('checked'))
    ;
}

/**
 * Init checkboxes to represent select first value
 */
function initOptions() {
    $('select#variant-select').change();
}
