

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
        var $parties_list = $('.widget.my-games .list-group');
        
        $parties_list.find('.list-group-item').remove();
        
        for (var key in r.current_parties) {
            var current_party = r.current_parties[key];
            
            $parties_list.append('\
                <a href="'+current_party.link+'" class="list-group-item'+(current_party.my_turn ? ' my-turn' : '')+'">\
                    <h4 class="list-group-item-heading">'+current_party.game.title+'</h4>\
                    <p class="list-group-item-text">'+current_party.description+'</p>\
                </a>\
            ');
        }
        
        widget.myPartiesRefreshMyTurnFilter();
        
        /**
         * Update games select
         */
        var $games_select       = $('.widget.my-games form select');
        var selected_game_id    = $games_select.val();
        
        $games_select.find('option[value!="-1"]').remove();
        
        for (var game_id in r.games_list) {
            var game_title = r.games_list[game_id];
            $games_select.append('<option value="'+game_id+'">'+game_title+'</option>');
        }
        
        $games_select.find('option[value="'+selected_game_id+'"]').prop('selected', true);
    }
};
