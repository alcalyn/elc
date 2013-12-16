$(function() {
    var $context = jQuery('#js-context');
    if ($context.size() > 0) {
        js_context = $context.data();
    }
    
    phaxConfig = jQuery('#phax-config').data();
    
    var $load_controllers = jQuery('#phax-load-controllers');
    if ($load_controllers.size() > 0) {
        jQuery.each($load_controllers.data(), function(index, controller) {
            phax.load_controller(controller);
        });
    }
});

var js_context = {};
var phaxConfig = null;




var slot = {
    
    init: function()
    {
        console.log('init slot controller');

        if (js_context.is_host) {
            $.each($('.slots .slot'), function(index, _slot) {
                var ul = $(_slot).find('ul.dropdown-menu');
                if ($(ul).size() > 0) {
                    slot.bindSlotMenu(index, ul);
                }
            });
        }
        
        setInterval(function() {
            phax.action('slot', 'refresh', js_context);
        }, 2000);
    },
    
    refreshAction: function(r)
    {
        for(var i=0;i<r.slots.length;i++) {
            slot.update(
                    i,
                    r.slots[i],
                    r.slots[i].player,
                    r.party.host && r.slots[i].player && r.party.host.id === r.slots[i].player.id
            );
        }
    },
    
    update: function(index, _slot, player, is_host)
    {
        var $slot = $('.slots .slot').eq(index);
        
        $slot.removeClass('joueur host slot-open slot-closed ordi');
        
        if (player) {
            var badge = '';
            if (player.badge) {
                badge = ' <span class="badge">'+player.badge+'</span>';
            }
            $slot.find('.player-pseudo').html(player.pseudo+badge);
            if (player.bot) {
                $slot.addClass('ordi');
            } else {
                $slot.addClass('joueur');
                
                if (is_host) {
                    $slot.addClass('host');
                }
            }
        } else {
            var is_open = _slot && _slot.open;
            $slot.addClass('slot-'+(is_open ? 'open' : 'closed'));
            $slot.find('.player-pseudo').html(is_open ? 'Slot open' : 'Slot closed');
        }
        
        $slotmenus = $slot.find('ul.dropdown-menu');
        
        var menus = {
            open:           !player && !_slot.open,
            close:          !player && _slot.open,
            remove:         !player,
            ban:            player,
            inviteplayer:   !player,
            invitecpu:      !player,
        };
        
        $.each(menus, function(key, value) {
        	if (value) {
        		$slotmenus.find('li.slotmenu-'+key).removeClass('disabled')
        	} else {
        		$slotmenus.find('li.slotmenu-'+key).addClass('disabled');
        	}
        });
    },
    
    
    bindSlotMenu: function(index, ul)
    {
    	$(ul).find('li.slotmenu-open a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'open', $.extend(js_context, {slot_index: index, slot_open: true}));
			slot.update(index, {open: true});
			return false;
		});
    	
		$(ul).find('li.slotmenu-close a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'open', $.extend(js_context, {slot_index: index, slot_open: false}));
			slot.update(index, {open: false});
			return false;
		});
    },
    
    
    openAction: function(r)
    {
    	slot.refreshAction(r);
    }
    
    
    
};


