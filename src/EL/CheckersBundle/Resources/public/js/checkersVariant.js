
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
     * Set forceCapture
     *
     * @param {boolean} forceCapture
     * @return {CheckersVariant}
     */
    this.setForceCaptureQuantity = function (forceCaptureQuantity)
    {
        return this.set(CheckersVariant.CAPT_QUANTITY, forceCaptureQuantity);
    };

    /**
     * Get forceCapture
     *
     * @return {boolean}
     */
    this.getForceCaptureQuantity = function ()
    {
        return this.get(CheckersVariant.CAPT_QUANTITY);
    };

    /**
     * Set forceCapture
     *
     * @param {boolean} forceCapture
     * @return {CheckersVariant}
     */
    this.setForceCaptureQuality = function (forceCaptureQuality)
    {
        return this.set(CheckersVariant.CAPT_QUALITY, forceCaptureQuality);
    };

    /**
     * Get forceCapture
     *
     * @return {boolean}
     */
    this.getForceCaptureQuality = function ()
    {
        return this.get(CheckersVariant.CAPT_QUALITY);
    };

    /**
     * Set forceCapture
     *
     * @param {boolean} forceCapture
     * @return {CheckersVariant}
     */
    this.setForceCaptureKingOrder = function (forceCaptureKingOrder)
    {
        return this.set(CheckersVariant.CAPT_KING_ORDER, forceCaptureKingOrder);
    };

    /**
     * Get forceCapture
     *
     * @return {boolean}
     */
    this.getForceCaptureKingOrder = function ()
    {
        return this.get(CheckersVariant.CAPT_KING_ORDER);
    };

    /**
     * Set forceCapture
     *
     * @param {boolean} forceCapture
     * @return {CheckersVariant}
     */
    this.setForceCapturePreference = function (forceCapturePreference)
    {
        return this.set(CheckersVariant.CAPT_PREFERENCE, forceCapturePreference);
    };

    /**
     * Get forceCapture
     *
     * @return {boolean}
     */
    this.getForceCapturePreference = function ()
    {
        return this.get(CheckersVariant.CAPT_PREFERENCE);
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
     * Set king Stops Behind
     *
     * @param {boolean} kingStopsBehind
     * @return {CheckersVariant}
     */
    this.setKingStopsBehind = function (kingStopsBehind)
    {
        return this.set(CheckersVariant.KING_STOPS_BEHIND, kingStopsBehind);
    }

    /**
     * Get king Stops Behind
     *
     * @return {boolean}
     */
    this.getKingStopsBehind = function ()
    {
        return this.get(CheckersVariant.KING_STOPS_BEHIND);
    }
    
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

CheckersVariant.BOARD_SIZE        = 31;
CheckersVariant.SQUARE_USED       = 32;
CheckersVariant.RIGHT_SQUARE      = 64;
CheckersVariant.BACKWARD_CAPTURE  = 128;
CheckersVariant.LONG_RANGE_KING   = 256;
CheckersVariant.MEN_JUMP_KING     = 512;
CheckersVariant.KING_PASSING      = 1024;
CheckersVariant.BLOW_UP           = 2048;
CheckersVariant.FIRST_PLAYER      = 4096;
CheckersVariant.FORCE_CAPTURE     = 8192;
CheckersVariant.CAPT_QUANTITY     = 16384;
CheckersVariant.CAPT_QUALITY      = 32768;
CheckersVariant.CAPT_KING_ORDER   = 65536;
CheckersVariant.CAPT_PREFERENCE   = 131072;
CheckersVariant.KING_STOPS_BEHIND = 262144;
    
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
        $('#el_core_options_type_extendedOptions_kingStopsBehind').prop('checked', variant.getKingStopsBehind());
        $('#el_core_options_type_extendedOptions_menJumpKing').prop('checked', variant.getMenJumpKing());
        $('#el_core_options_type_extendedOptions_kingPassing').prop('checked', variant.getKingPassing());
        $('#el_core_options_type_extendedOptions_blowUp').prop('checked', variant.getBlowUp());
        $('#el_core_options_type_extendedOptions_forceCapture').prop('checked', variant.getForceCapture());
        $('#el_core_options_type_extendedOptions_forceCaptureQuantity').prop('checked', variant.getForceCaptureQuantity());
        $('#el_core_options_type_extendedOptions_forceCaptureQuality').prop('checked', variant.getForceCaptureQuality());
        $('#el_core_options_type_extendedOptions_forceCaptureKingOrder').prop('checked', variant.getForceCaptureKingOrder());
        $('#el_core_options_type_extendedOptions_forceCapturePreference').prop('checked', variant.getForceCapturePreference());
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
            .setKingStopsBehind($('#el_core_options_type_extendedOptions_kingStopsBehind').prop('checked'))
            .setMenJumpKing($('#el_core_options_type_extendedOptions_menJumpKing').prop('checked'))
            .setKingPassing($('#el_core_options_type_extendedOptions_kingPassing').prop('checked'))
            .setBlowUp($('#el_core_options_type_extendedOptions_blowUp').prop('checked'))
            .setForceCapture($('#el_core_options_type_extendedOptions_forceCapture').prop('checked'))
            .setForceCaptureQuantity($('#el_core_options_type_extendedOptions_forceCaptureQuantity').prop('checked'))
            .setForceCaptureQuality($('#el_core_options_type_extendedOptions_forceCaptureQuality').prop('checked'))
            .setForceCaptureKingOrder($('#el_core_options_type_extendedOptions_forceCaptureKingOrder').prop('checked'))
            .setForceCapturePreference($('#el_core_options_type_extendedOptions_forceCapturePreference').prop('checked'))
            .setFirstPlayer($('#el_core_options_type_extendedOptions_firstPlayer').prop('checked'))
    ;
}

/**
 * Init checkboxes to represent select first value
 */
function initOptions() {
    $('select#variant-select').change();
}

/**
 * Check or uncheck others criterias when checking a criteria
 */
function dependentCriterias() {
    // Uncheck blow up when check force capture
    $('#el_core_options_type_extendedOptions_forceCapture').change(function () {
        if ($('#el_core_options_type_extendedOptions_forceCapture').prop('checked')) {
            dependentUpdate('#el_core_options_type_extendedOptions_blowUp', false);
        }
    });
    
    // Uncheck force capture when check blow up
    $('#el_core_options_type_extendedOptions_blowUp').change(function () {
        if ($('#el_core_options_type_extendedOptions_blowUp').prop('checked')) {
            dependentUpdate('#el_core_options_type_extendedOptions_forceCapture', false);
        }
    });
    
    // Check long range king when check king stops behind
    $('#el_core_options_type_extendedOptions_kingStopsBehind').change(function () {
        if ($('#el_core_options_type_extendedOptions_kingStopsBehind').prop('checked')) {
            dependentUpdate('#el_core_options_type_extendedOptions_longRangeKing', true);
        }
    });
    
    // Uncheck king stops behind when uncheck long range king
    $('#el_core_options_type_extendedOptions_longRangeKing').change(function () {
        if (!$('#el_core_options_type_extendedOptions_longRangeKing').prop('checked')) {
            dependentUpdate('#el_core_options_type_extendedOptions_kingStopsBehind', false);
        }
    });
}

function dependentUpdate(input, value) {
    var $input = $(input);
    
    if ($input.prop('checked') ^ value) {
        $input.prop('checked', value);
        
        // Animate update
        var $row = $input.closest('.form-group');
        
        $row.css({
            backgroundColor: value ? 'rgba(0, 128, 0, 0.5)' : 'rgba(128, 0, 0, 0.5)'
        });
        $row.animate({
            backgroundColor: value ? 'rgba(0, 128, 0, 0)' : 'rgba(128, 0, 0, 0)'
        }, 800);
    }
}
