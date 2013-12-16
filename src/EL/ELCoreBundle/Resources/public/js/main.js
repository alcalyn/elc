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
                if ($(_slot).find('ul.dropdown-menu').size() > 0) {
                    slot.bindSlotMenu(index);
                }
            });
        }
        
        setInterval(function() {
            phax.action('slot', 'refresh', js_context);
        }, 3000);
    },
    
    refreshAction: function(r)
    {
        for(var i=0;i<r.slots.length;i++) {
            if (slot.hasChanged(i, r)) {
            	slot.update(
                    i,
                    r.slots[i],
                    r.slots[i].player,
                    r.party.host && r.slots[i].player && r.party.host.id === r.slots[i].player.id
            	);
            }
        }
    },
    
    hasChanged: function(index, r)
    {
    	// TODO
    	/**
    	 * return true if slot need to be updated
    	 */
    	return true;
    },
    
    update: function(index, _slot, player, is_host)
    {
    	// TODO
    	/**
    	 * replace slot if we need to change buttons (player, join, menu),
    	 * or if it is a minor update,
    	 * dont replace html, and change some classes (for non host players)...
    	 */
    	slotTemplates.replace(index, _slot, player, is_host);
    	return;
    	
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
        
        var menus = slot.activeMenus(_slot, player);
        
        $.each(menus, function(key, value) {
        	if (value) {
        		$slotmenus.find('li.slotmenu-'+key).removeClass('disabled');
        	} else {
        		$slotmenus.find('li.slotmenu-'+key).addClass('disabled');
        	}
        });
    },
    
    
    activeMenus: function(_slot, player)
    {
    	return {
            open:           !player && !_slot.open,
            close:          !player && _slot.open,
            remove:         !player,
            ban:            player,
            inviteplayer:   !player,
            invitecpu:      !player,
        };
    },
    
    
    bindSlotMenu: function(index)
    {
    	$ul = $('.slots .slot').eq(index).find('ul.dropdown-menu');
    	
    	if ($ul.size() === 0) {
    		return false;
    	}
    	
    	$ul.find('li.slotmenu-open a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'open', $.extend(js_context, {slot_index: index, slot_open: true}));
			slot.update(index, {open: true});
			$(this).hide();
			return false;
		});
    	
		$ul.find('li.slotmenu-close a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'open', $.extend(js_context, {slot_index: index, slot_open: false}));
			slot.update(index, {open: false});
			$(this).hide();
			return false;
		});
    },
    
    
    openAction: function(r)
    {
    	slot.refreshAction(r);
    }
    
    
    
};



var slotTemplates = {
	
	replace: function(index, _slot, player, is_host)
	{
		var $slot = $(slotTemplates.get(_slot, player, is_host));
		
		$('.slots .slot').eq(index).after($slot);
		$('.slots .slot').eq(index).remove();
		
		slot.bindSlotMenu(index);
	},
	
	get: function(_slot, player, is_host)
	{
		var is_me = player && (player.id === js_context.player_id);
		
		if (parseInt(js_context.is_host) === 1) {
			if (player) {
				if (is_me) {
					return slotTemplates.getHostPlayerMe(_slot, player, is_host);
				} else {
					return slotTemplates.getHostPlayer(_slot, player, is_host);
				}
			} else {
				if (_slot && _slot.open) {
					return slotTemplates.getHostOpen(_slot, player, is_host);
				} else {
					return slotTemplates.getHostClosed(_slot, player, is_host);
				}
			}
		} else {
			if (player) {
				return slotTemplates.getPlayer(_slot, player, is_host);
			} else {
				if (_slot && _slot.open) {
					return slotTemplates.getOpen(_slot, player, is_host);
				} else {
					return slotTemplates.getClosed(_slot, player, is_host);
				}
			}
		}
	},
	
	getHostPlayerMe: function(_slot, player, is_host)
	{
		return '\
			<div class="btn-group slot joueur host">\
		        <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
		            '+player.pseudo+'\
		            \
		            '+on(player.badge, '<span class="badge">'+player.badge+'</span>')+'\
		        </button>\
		    </div>\
    	';
	},
	
	getHostPlayer: function(_slot, player, is_host)
	{
		return '\
			<div class="btn-group slot joueur">\
		        <button type="button" class="btn btn-default player-pseudo btn-slot-11">\
		            '+player.pseudo+'\
		            \
		            '+on(player.badge, '<span class="badge">'+player.badge+'</span>')+'\
		        </button>\
		        <button type="button" class="btn btn-default dropdown-toggle btn-slot-1" data-toggle="dropdown">\
		            <span class="caret"></span>\
		        </button>\
		        '+slotTemplates.getMenu(_slot, player)+'\
		    </div>\
		';
	},
	
	getHostOpen: function(_slot, player, is_host)
	{
		return '\
	        <div class="btn-group slot slot-open">\
	            <button type="button" class="btn btn-default player-pseudo btn-slot-6">\
	                open.slot\
	            </button>\
	            <button class="btn btn-default slot-join btn-slot-5" type="button">\
	                join\
	            </button>\
	            <button type="button" class="btn btn-default dropdown-toggle btn-slot-1" data-toggle="dropdown">\
	                <span class="caret"></span>\
	            </button>\
	            '+slotTemplates.getMenu(_slot, player)+'\
	        </div>\
	    ';
	},
	
	getHostClosed: function(_slot, player, is_host)
	{
		return '\
	        <div class="btn-group slot slot-closed">\
	            <button type="button" class="btn btn-default player-pseudo btn-slot-11">\
	                closed.slot\
	            </button>\
	            <button type="button" class="btn btn-default dropdown-toggle btn-slot-1" data-toggle="dropdown">\
	                <span class="caret"></span>\
	            </button>\
        		'+slotTemplates.getMenu(_slot, player)+'\
	        </div>\
		';
	},
	
	getPlayer: function(_slot, player, is_host)
	{
		return '\
	        <div class="btn-group slot joueur '+on(is_host, 'host')+'">\
	            <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
        			'+player.pseudo+'\
					\
		            '+on(player.badge, '<span class="badge">'+player.badge+'</span>')+'\
	            </button>\
	        </div>\
		';
	},
	
	getOpen: function(_slot, player, is_host)
	{
		return '\
	        <div class="btn-group slot slot-open">\
	            <button type="button" class="btn btn-default player-pseudo btn-slot-7">\
	                open.slot\
	            </button>\
	            <button class="btn btn-default slot-join btn-slot-5" type="button">\
	                '+(js_context.in_party ? 'change.slot' : 'join')+'\
	            </button>\
	        </div>\
		';
	},
	
	getClosed: function(_slot, player, is_host)
	{
		return '\
	        <div class="btn-group slot slot-closed">\
	            <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
	                closed.slot\
	            </button>\
	        </div>\
		';
	},
	
	
	
	getMenu: function(_slot, player)
	{
		var menus = slot.activeMenus(_slot, player);
		console.log(menus);
		
		return '\
			<ul class="dropdown-menu" role="menu">\
			    <li class="slotmenu-open '+on(!menus.open, 'disabled')+'">\
			        <a href="#">open</a>\
			    </li>\
			    <li class="slotmenu-close '+on(!menus.close, 'disabled')+'">\
			        <a href="#">close</a>\
			    </li>\
			    <li class="slotmenu-remove '+on(!menus.remove, 'disabled')+'">\
			        <a href="#">delete.slot</a>\
			    </li>\
			    <li class="divider">\
			    </li>\
			    <li class="slotmenu-ban '+on(!menus.ban, 'disabled')+'">\
			        <a href="#">ban</a>\
			    </li>\
			    <li class="divider">\
			    </li>\
			    <li class="slotmenu-inviteplayer '+on(!menus.inviteplayer, 'disabled')+'">\
			        <a href="#">invite.player</a>\
			    </li>\
			    <li class="slotmenu-invitecpu '+on(!menus.invitecpu, 'disabled')+'">\
			        <a href="#">invite.cpu</a>\
			    </li>\
			</ul>\
		';
	}
	
};


function on(boolean, string_true, string_false) {
	if (boolean) {
		return string_true;
	} else {
		if (string_false) {
			return string_false;
		} else {
			return '';
		}
	}
}


