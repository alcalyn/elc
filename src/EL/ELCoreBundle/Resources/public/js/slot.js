

var slot = {
    
    init: function()
    {
        console.log('init slot controller');
        
        if ($('.slots .slot').size() === 0) {
        	console.log('init slot phax controller when not in preparation page...');
        	return false;
        }
        
        slot.enableDragAndDrop();
        
        if (jsContext.is_host) {
            $.each($('.slots .slot'), function(index, _slot) {
                if ($(_slot).find('ul.dropdown-menu').size() > 0) {
                    slot.bindSlotMenu(index, _slot, {id: $(_slot).val('player_id')});
            		slot.bindJoinButton(index);
                }
            });
        }
        
        setInterval(function() {
            phax.action('slot', 'refresh', jsContext);
        }, 5000);
        
        return true;
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
    	var $slot = $('.slots .slot').eq(index);
    	var _slot = r.slots[index];
    	
    	// host is no longer me, or i am now host
    	if (!jsContext.is_host === (r.party.host.id === jsContext.player.id)) {
    		console.log('host is no longer me, or i am now host');
    		return true;
    	}
    	
    	// slot has been open or closed
    	if ($slot.hasClass('slot-closed') === _slot.open) {
    		console.log('slot has been open or closed');
    		return true;
    	}
    	
    	if (_slot.open) {
    		// player has join or leave
    		if (!_slot.player === ($slot.hasClass('joueur') || $slot.hasClass('ordi'))) {
    			console.log('player has join or leave')
    			return true;
    		}
    		
    		if (_slot.player) {
    			// player has relaced cpu, or cpu has replaced player
    			if (_slot.player.bot !== $slot.hasClass('ordi')) {
    				console.log('player has relaced cpu, or cpu has replaced player');
    				return true;
    			}
    			
    			// player has become host, or has lost host position
    			
    		}
    	}
    	
    	
    	/**
    	 * return false if other cases
    	 */
    	return false;
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
    
    
    bindSlotMenu: function(index, _slot, player)
    {
    	$ul = $('.slots .slot').eq(index).find('ul.dropdown-menu');
    	
    	if ($ul.size() === 0) {
    		return false;
    	}
    	
    	$ul.find('li.slotmenu-open a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'open', $.extend({}, jsContext, {slot_index: index, slot_open: true}));
			slot.update(index, {open: true});
			$(this).hide();
			return false;
		});
    	
		$ul.find('li.slotmenu-close a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'open', $.extend({}, jsContext, {slot_index: index, slot_open: false}));
			slot.update(index, {open: false});
			$(this).hide();
			return false;
		});
		
		$ul.find('li.slotmenu-ban a').click(function() {
			if ($(this).parent('li').hasClass('disabled')) {
				return false;
			}
			phax.action('slot', 'ban', $.extend({}, jsContext, {player_id: player.id}));
			slot.update(index, {open: true});
			$(this).hide();
			return false;
		});
    },
    
    
    bindJoinButton: function(index)
    {
    	$joinButton = $('.slots .slot').eq(index).find('.slot-join');
    	
    	if ($joinButton.size() > 0) {
    		$joinButton.click(function() {
    			phax.action('slot', 'ajaxJoin', $.extend({}, jsContext, {slot_index: index}));
    			var current_index = slot.getIndexWhere(function($slot) {
    				return parseInt($slot.data('player_id')) === jsContext.player.id;
    			});
    			
    			if (current_index >= 0) {
    				slot.update(current_index, {open:true});
    			}
    			slot.update(index, {open:true}, jsContext.player, jsContext.is_host);
    		});
    	}
    },
    
    
    enableDragAndDrop: function(index)
    {
		$('.slots').sortable({
	        revert:		200,
			handle:		'.player-pseudo',
			cancel:		false,
			opacity:	0.75,
			zIndex:		1001,
			start: function() {
				jQuery.each($('.slots .slot'), function(index, _slot) {
		    		$(_slot).data('order', index);
		    	});
			},
			update: function() {
				var new_order = [];
				jQuery.each($('.slots .slot'), function(index, _slot) {
		    		var order = $(_slot).data('order');
		    		new_order.push(order);
		    	});
				phax.action('slot', 'reorder', $.extend({}, jsContext, {new_order: new_order}));
			}
		});
		$('.slots, .slot').disableSelection();
    },
    
    
    getIndexWhere: function(callback)
    {
    	var found = -1;
    	jQuery.each($('.slots .slot'), function(index, _slot) {
    		if (callback($(_slot))) {
    			found = index;
    			return false;
    		}
    	});
    	
    	return found;
    },
    
    
    ajaxJoinAction: function(r)
    {
    	slot.refreshAction(r);
    },
    
    
    openAction: function(r)
    {
    	slot.refreshAction(r);
    },
    
    
    banAction: function(r)
    {
    	slot.refreshAction(r);
    },
    
    
    reorderAction: function(r)
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
		
		slot.bindSlotMenu(index, _slot, player);
		slot.bindJoinButton(index);
		slot.enableDragAndDrop(index);
	},
	
	get: function(_slot, player, is_host)
	{
		var is_me = player && (player.id === jsContext.player.id);
		
		if (parseInt(jsContext.is_host) === 1) {
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
			<div class="btn-group slot joueur host" data-player_id="'+player.id+'">\
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
			<div class="btn-group slot joueur" data-player_id="'+player.id+'">\
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
	                '+t('slot.open')+'\
	            </button>\
	            <button class="btn btn-default slot-join btn-slot-5" type="button">\
        			'+(jsContext.in_party ? t('change.slot') : t('join'))+'\
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
					'+t('slot.closed')+'\
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
	        <div class="btn-group slot joueur '+on(is_host, 'host')+'" data-player_id="'+player.id+'">\
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
					'+t('slot.open')+'\
	            </button>\
	            <button class="btn btn-default slot-join btn-slot-5" type="button">\
	                '+(jsContext.in_party ? t('change.slot') : t('join'))+'\
	            </button>\
	        </div>\
		';
	},
	
	getClosed: function(_slot, player, is_host)
	{
		return '\
	        <div class="btn-group slot slot-closed">\
	            <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
					'+t('slot.closed')+'\
	            </button>\
	        </div>\
		';
	},
	
	
	
	getMenu: function(_slot, player)
	{
		var menus = slot.activeMenus(_slot, player);
		
		return '\
			<ul class="dropdown-menu" role="menu">\
			    <li class="slotmenu-open '+on(!menus.open, 'disabled')+'">\
			        <a href="#">'+t('open')+'</a>\
			    </li>\
			    <li class="slotmenu-close '+on(!menus.close, 'disabled')+'">\
			        <a href="#">'+t('close')+'</a>\
			    </li>\
			    <li class="slotmenu-remove '+on(!menus.remove, 'disabled')+'">\
			        <a href="#">'+t('delete.slot')+'</a>\
			    </li>\
			    <li class="divider">\
			    </li>\
			    <li class="slotmenu-ban '+on(!menus.ban, 'disabled')+'">\
			        <a href="#">'+t('ban')+'</a>\
			    </li>\
			    <li class="divider">\
			    </li>\
			    <li class="slotmenu-inviteplayer '+on(!menus.inviteplayer, 'disabled')+'">\
			        <a href="#">'+t('invite.player')+'</a>\
			    </li>\
			    <li class="slotmenu-invitecpu '+on(!menus.invitecpu, 'disabled')+'">\
			        <a href="#">'+t('invite.cpu')+'</a>\
			    </li>\
			</ul>\
		';
	}
	
};


