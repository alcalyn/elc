$(function() {
    var $context = jQuery('#js-context');
    if ($context.size() > 0) {
        js_context = $context.data();
    }
    
    phaxConfig = jQuery('#phax-config').data();
});

var js_context = {};
var phaxConfig = null;




var slot = {
    
    init: function()
    {
        console.log('init slot controller');
        setInterval(function() {
            phax.action('slot', 'refresh', js_context);
        }, 2000);
    },
    
    refreshAction: function(r)
    {
        for(var i=0;i<r.slots.length;i++) {
            slot.updateSlot(i, r.slots[i], r.slots[i].player, r.party.host);
        }
    },
    
    updateSlot: function(index, slot, player, host)
    {
        var $slot = $('.slots .slot').eq(index);
        
        if (player) {
            $slot.removeClass('slot-closed slot-open');
            $slot.addClass('joueur');
            if (player.id === host.id) {
                $slot.addClass('host');
            }
            $slot.find('.player-pseudo').html(player.pseudo+' <span class="badge">X élo</span>');
        } else {
            $slot.removeClass('joueur host');
            $slot.addClass('slot-'+(slot.open ? 'open' : 'closed'));
            $slot.find('.player-pseudo').html(slot.open ? 'Slot open' : 'Slot closed');
        }
        
    }
    
};


