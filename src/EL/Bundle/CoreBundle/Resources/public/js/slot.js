
/**
 * Enable controls for preparation screen.
 * 
 * @type object
 */
var slot = {
    
    /**
     * Init the preparation screen controls by:
     * 
     * - enabling slots dragging and sorting,
     * - setInterval for refreshing continuously slots configuration,
     * - bind buttons and menus to actions
     * 
     * @returns {Boolean}
     */
    init: function ()
    {
        console.log('init slot controller');
        
        if ($('.slots .slot').size() === 0) {
            console.log('init slot phax controller when not in preparation page...');
            return false;
        }
        
        slot.enableDragAndDrop();
        
        $.each($('.slots .slot'), function (index, _slot) {
            slot.bindSlotMenu(index, _slot, {id: $(_slot).val('playerId')});
            slot.bindJoinButton(index);
        });
        
        setInterval(function () {
            phax.action('slot', 'refresh', slot.context());
        }, 3000);
        
        return true;
    },
    
    /**
     * Refresh screen after ajax return
     * 
     * @param {object} r
     * @returns {undefined}
     */
    refreshReaction: function (r)
    {
        for(var i=0;i<r.party.slots.length;i++) {
            if (slot.hasChanged(i, r)) {
                slot.update(
                    i,
                    r.party.slots[i],
                    r.party.slots[i].player,
                    r.party.host && r.party.slots[i].player && r.party.host.id === r.party.slots[i].player.id
                );
            }
        }
    },
    
    /**
     * True if slot at index needs to be updated with ajax return
     * 
     * @param {integer} index
     * @param {object} r
     * @returns {Boolean}
     */
    hasChanged: function (index, r)
    {
        var $slot = $('.slots .slot').eq(index);
        var _slot = r.party.slots[index];
        
        // host is no longer me, or i am now host
        if (!jsContext.isHost === (r.party.host.id === jsContext.player.id)) {
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
    
    /**
     * Update slot with new configuration
     * 
     * @param {integer} index
     * @param {object} _slot default null: closed slot
     * @param {object} player default null: empty slot
     * @param {Boolean} isHost default false
     * @returns {undefined}
     */
    update: function (index, _slot, player, isHost)
    {
        // TODO
        /**
         * replace slot if we need to change buttons (player, join, menu),
         * or if it is a minor update,
         * dont replace html, and change some classes (for non host players)...
         */
        slotTemplates.replace(index, _slot, player, isHost);
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
                
                if (isHost) {
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
        
        $.each(menus, function (key, value) {
            if (value) {
                $slotmenus.find('li.slotmenu-'+key).removeClass('disabled');
            } else {
                $slotmenus.find('li.slotmenu-'+key).addClass('disabled');
            }
        });
    },
    
    /**
     * Return object with active or disabled items
     * for slot menu for host
     * 
     * @param {object} _slot
     * @param {object} player
     * @returns {object}
     */
    activeMenus: function (_slot, player)
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
    
    /**
     * Binds all menu item for a slot to its action
     * 
     * @param {integer} index
     * @param {object} _slot
     * @param {object} player
     * @returns {Boolean}
     */
    bindSlotMenu: function (index, _slot, player)
    {
        $ul = $('.slots .slot').eq(index).find('ul.dropdown-menu');
        
        if ($ul.size() === 0) {
            return false;
        }
        
        $ul.find('li.slotmenu-open a').click(function () {
            if ($(this).parent('li').hasClass('disabled')) {
                return false;
            }
            var currentIndex = $(this).closest('.slot').index();
            phax.action('slot', 'open', $.extend({}, slot.context(), {slotIndex: currentIndex, slotOpen: true}));
            slot.update(currentIndex, {open: true});
            $(this).hide();
            return false;
        });
        
        $ul.find('li.slotmenu-close a').click(function () {
            if ($(this).parent('li').hasClass('disabled')) {
                return false;
            }
            var currentIndex = $(this).closest('.slot').index();
            phax.action('slot', 'open', $.extend({}, slot.context(), {slotIndex: currentIndex, slotOpen: false}));
            slot.update(currentIndex, {open: false});
            $(this).hide();
            return false;
        });
        
        $ul.find('li.slotmenu-ban a').click(function () {
            if ($(this).parent('li').hasClass('disabled')) {
                return false;
            }
            var currentIndex = $(this).closest('.slot').index();
            phax.action('slot', 'ban', $.extend({}, slot.context(), {playerId: $(this).closest('.slot').data('playerid')}));
            slot.update(currentIndex, {open: true});
            $(this).hide();
            return false;
        });
    },
    
    /**
     * Bind join button of slot to a join action
     * 
     * @param {integer} index
     * @returns {undefined}
     */
    bindJoinButton: function (index)
    {
        $joinButton = $('.slots .slot').eq(index).find('.slot-join');
        
        if ($joinButton.size() > 0) {
            $joinButton.click(function () {
                var currentIndex = $(this).closest('.slot').index();
                phax.action('slot', 'ajaxJoin', $.extend({}, slot.context(), {slotIndex: currentIndex}));
                var currentIndex = slot.getIndexWhere(function ($slot) {
                    return parseInt($slot.data('playerid')) === jsContext.player.id;
                });
                
                if (currentIndex >= 0) {
                    slot.update(currentIndex, {open:true});
                }
                slot.update(currentIndex, {open:true}, jsContext.player, jsContext.isHost);
            });
        }
    },
    
    /**
     * Enable sortable for slot at index
     * 
     * @param {integer} index
     * @returns {undefined}
     */
    enableDragAndDrop: function (index)
    {
        $('.slots').sortable({
            revert:     200,
            handle:     '.player-pseudo',
            cancel:     false,
            opacity:    0.75,
            zIndex:     1001,
            start: function () {
                jQuery.each($('.slots .slot'), function (index, _slot) {
                    $(_slot).data('order', index);
                });
            },
            update: function () {
                var newOrder = [];
                jQuery.each($('.slots .slot'), function (index, _slot) {
                    var order = $(_slot).data('order');
                    newOrder.push(order);
                });
                slot.reoder(newOrder);
            }
        });
    },
    
    /**
     * Return index of slot for which callback(slot) returns true
     * 
     * @param {function} callback
     * @returns {integer}
     */
    getIndexWhere: function (callback)
    {
        var found = -1;
        jQuery.each($('.slots .slot'), function (index, _slot) {
            if (callback($(_slot))) {
                found = index;
                return false;
            }
        });
        
        return found;
    },
    
    /**
     * Callback for ajaxJoin action
     * 
     * @param {object} r
     * @returns {undefined}
     */
    ajaxJoinReaction: function (r)
    {
        slot.refreshReaction(r);
    },
    
    /**
     * Callback for open a slot
     * 
     * @param {object} r
     * @returns {undefined}
     */
    openReaction: function (r)
    {
        slot.refreshReaction(r);
    },
    
    /**
     * Callback for ban a player
     * 
     * @param {object} r
     * @returns {undefined}
     */
    banReaction: function (r)
    {
        slot.refreshReaction(r);
    },
    
    /**
     * Reorder slots with order newOrder (ie [0, 2, 1, 3])
     * 
     * @param {array} newOrder contening new indexes
     * @returns {void}
     */
    reoder: function (newOrder)
    {
        phax.action('slot', 'reorder', $.extend({}, slot.context(), {newOrder: newOrder}));
    },
    
    /**
     * Callback for reorder action
     * 
     * @param {object} r
     * @returns {undefined}
     */
    reorderReaction: function (r)
    {
        slot.refreshReaction(r);
    },
    
    context: function ()
    {
        return {
            slugGame:  jsContext.coreParty.game.slug,
            slugParty: jsContext.coreParty.slug
        };
    }
};


/**
 * Contains slots template for each use cases
 * 
 * @type object
 */
var slotTemplates = {
    
    /**
     * Replace slot at index with a new html code
     * 
     * @param {integer} index
     * @param {object} _slot
     * @param {object} player
     * @param {Boolean} isHost
     * @returns {undefined}
     */
    replace: function (index, _slot, player, isHost)
    {
        var $slot = $(slotTemplates.get(_slot, player, isHost));
        
        $('.slots .slot').eq(index).after($slot);
        $('.slots .slot').eq(index).remove();
        
        slot.bindSlotMenu(index, _slot, player);
        slot.bindJoinButton(index);
        slot.enableDragAndDrop(index);
    },
    
    /**
     * Get the template depending of slot state
     * 
     * @param {integer} index
     * @param {object} _slot
     * @param {object} player
     * @param {Boolean} isHost
     * @returns {String}
     */
    get: function (_slot, player, isHost)
    {
        var isMe = player && (player.id === jsContext.player.id);
        
        if (parseInt(jsContext.isHost) === 1) {
            if (player) {
                if (isMe) {
                    return slotTemplates.getHostPlayerMe(_slot, player, isHost);
                } else {
                    return slotTemplates.getHostPlayer(_slot, player, isHost);
                }
            } else {
                if (_slot && _slot.open) {
                    return slotTemplates.getHostOpen(_slot, player, isHost);
                } else {
                    return slotTemplates.getHostClosed(_slot, player, isHost);
                }
            }
        } else {
            if (player) {
                return slotTemplates.getPlayer(_slot, player, isHost);
            } else {
                if (_slot && _slot.open) {
                    return slotTemplates.getOpen(_slot, player, isHost);
                } else {
                    return slotTemplates.getClosed(_slot, player, isHost);
                }
            }
        }
    },
    
    getHostPlayerMe: function (_slot, player, isHost)
    {
        return '\
            <div class="btn-group slot joueur host" data-playerid="'+player.id+'">\
                <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
                    '+player.pseudo+'\
                    \
                    '+on(player.badge, '<span class="badge">'+player.badge+'</span>')+'\
                </button>\
            </div>\
        ';
    },
    
    getHostPlayer: function (_slot, player, isHost)
    {
        return '\
            <div class="btn-group slot joueur" data-playerid="'+player.id+'">\
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
    
    getHostOpen: function (_slot, player, isHost)
    {
        return '\
            <div class="btn-group slot slot-open">\
                <button type="button" class="btn btn-default player-pseudo btn-slot-6">\
                    '+t('slot.open')+'\
                </button>\
                <button class="btn btn-default slot-join btn-slot-5" type="button">\
                    '+(jsContext.inParty ? t('change.slot') : t('join'))+'\
                </button>\
                <button type="button" class="btn btn-default dropdown-toggle btn-slot-1" data-toggle="dropdown">\
                    <span class="caret"></span>\
                </button>\
                '+slotTemplates.getMenu(_slot, player)+'\
            </div>\
        ';
    },
    
    getHostClosed: function (_slot, player, isHost)
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
    
    getPlayer: function (_slot, player, isHost)
    {
        return '\
            <div class="btn-group slot joueur '+on(isHost, 'host')+'" data-playerid="'+player.id+'">\
                <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
                    '+player.pseudo+'\
                    \
                    '+on(player.badge, '<span class="badge">'+player.badge+'</span>')+'\
                </button>\
            </div>\
        ';
    },
    
    getOpen: function (_slot, player, isHost)
    {
        return '\
            <div class="btn-group slot slot-open">\
                <button type="button" class="btn btn-default player-pseudo btn-slot-7">\
                    '+t('slot.open')+'\
                </button>\
                <button class="btn btn-default slot-join btn-slot-5" type="button">\
                    '+(jsContext.inParty ? t('change.slot') : t('join'))+'\
                </button>\
            </div>\
        ';
    },
    
    getClosed: function (_slot, player, isHost)
    {
        return '\
            <div class="btn-group slot slot-closed">\
                <button type="button" class="btn btn-default player-pseudo btn-slot-12">\
                    '+t('slot.closed')+'\
                </button>\
            </div>\
        ';
    },
    
    
    
    getMenu: function (_slot, player)
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
