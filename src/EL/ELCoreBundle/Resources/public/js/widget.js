

var widget = {
    
    init: function ()
    {
        console.log('widget init');
        
        $('.widget.my-games input[name="my-turn"]').change(widget.myPartiesRefreshMyTurnFilter);
        widget.myPartiesRefreshMyTurnFilter();
        
        setInterval(function () {
            phax.action('widget', 'myParties', {_locale: jsContext.locale});
        }, 3000);
    },
    
    myPartiesRefreshMyTurnFilter: function ()
    {
        if ($('.widget.my-games input[name="my-turn"]').is(':checked')) {
            $('.widget.my-games a.list-group-item').addClass('hide-if-not-my-turn');
        } else {
            $('.widget.my-games a.list-group-item').removeClass('hide-if-not-my-turn');
        }
    },
    
    myPartiesReaction: function (r)
    {
        /**
         * Update parties list
         */
        var $partiesList = $('.widget.my-games .list-group');
        
        $partiesList.find('.list-group-item').remove();
        
        for (var key in r.currentParties) {
            var currentParty = r.currentParties[key];
            
            $partiesList.append('\
                <a href="'+currentParty.link+'" class="list-group-item'+(currentParty.myTurn ? ' my-turn' : '')+'">\
                    <h4 class="list-group-item-heading">'+currentParty.game.title+'</h4>\
                    <p class="list-group-item-text">'+currentParty.description+'</p>\
                </a>\
            ');
        }
        
        widget.myPartiesRefreshMyTurnFilter();
        
        /**
         * Update games select
         */
        var $gamesSelect    = $('.widget.my-games form select');
        var selectedGameId  = $gamesSelect.val();
        
        $gamesSelect.find('option[value!="-1"]').remove();
        
        for (var gameId in r.gamesList) {
            var gameTitle = r.gamesList[gameId];
            $gamesSelect.append('<option value="'+gameId+'">'+gameTitle+'</option>');
        }
        
        $gamesSelect.find('option[value="'+selectedGameId+'"]').prop('selected', true);
    }
};
