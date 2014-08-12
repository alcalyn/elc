jQuery(function () {
    initChats();
});

function Chat(id)
{
    /**
     * Dom Id of the chat item
     * 
     * @var string
     */
    this.id = id;
    
    /**
     * jQuery item referencing chat
     * 
     * @var jQuery
     */
    this.$chat = jQuery('#'+this.id+'.chat');
    
    /**
     * @type Chat
     */
    var self = this;
    
    /**
     * Init chat
     */
    this.init = function ()
    {
        if (1 !== this.$chat.size()) {
            console.warn(this.$chat.size()+' instance of chat "'+self.id+'" was found. Impossible to init.');
            return;
        }
        
        self.setEnabledInput(false);
        self.listenClank();
        self.bindSubmitButton();
        self.unsuscribeOnLeave();
    };
    
    /**
     * Add message to chat
     * 
     * @param {Object} message
     */
    this.addMessage = function (message)
    {
        if (!message || !message.content) {
            console.log('message mal formatté : ', message);
            return;
        }
        
        var $message = jQuery('<li class="message">');
        
        if (message.pseudo) {
            if (message.pseudoLink) {
                $message.append('<a href="'+message.pseudoLink+'">'+message.pseudo+'</a> : '+message.content);
            } else {
                $message.append(message.pseudo+' : '+message.content);
            }
        } else {
            $message.append(message.content);
        }
        
        self.$chat.find('.messages').append($message);
        self.scrollToBottom();
    };
    
    /**
     * Clear message input
     */
    this.clearInput = function ()
    {
        self.$chat.find('input.message-input').val('');
    };
    
    /**
     * Send message on submit
     */
    this.bindSubmitButton = function ()
    {
        self.$chat.find('form.submit').submit(function () {
            var message = jQuery.trim(jQuery('.message-input').val());
            
            if (message.length > 0) {
                clankSession.publish('chat/'+self.id, message);

                self.clearInput();
            }
            
            return false;
        });
    };
    
    /**
     * Scroll to bottom to follow discuss
     */
    this.scrollToBottom = function ()
    {
        var $messages = self.$chat.find('.messages');
        var messagesHeight = $messages.scrollTop() + $messages.innerHeight();
        var currentScroll = $messages[0].scrollHeight - $messages.find('.message').last().innerHeight();
        var isAtBottom = messagesHeight >= currentScroll;
        
        if (isAtBottom) {
            $messages.animate({ scrollTop: $messages[0].scrollHeight}, 200);
        }
    };
    
    /**
     * Enable or disable input to avoid submit while websocket not connected
     * 
     * @param {Boolean} enabled
     */
    this.setEnabledInput = function (enabled)
    {
        self.$chat.find('form [type=submit]').prop('disabled', !enabled);
    };
    
    /**
     * Called when clank is connected
     */
    this.onConnect = function ()
    {
        console.log('chat '+self.id+' connected');
        
        self.subscribe();
        self.setEnabledInput(true);
    };
    
    /**
     * Called when clank is disconnected
     */
    this.onDisConnect = function ()
    {
        console.log('chat '+self.id+' disconnected');
        
        self.setEnabledInput(false);
    };
    
    /**
     * Listen clank connects and disconnects
     */
    this.listenClank = function ()
    {
        clank.on('socket/connect', function () {
            self.onConnect();
        });
        
        clank.on('socket/disconnect', function () {
            self.onDisConnect();
        });
        
        // Call onConnect() if websocket is already connected
        if ((typeof clankSession !== 'undefined') && clankSession._websocket_connected) {
            self.onConnect();
        }
    };
    
    /**
     * subscribe to chat topic
     */
    this.subscribe = function ()
    {
        clankSession.subscribe('chat/'+self.id, function (uri, data) {
            self.addMessage(data.message);
        });
    };
    
    /**
     * unsuscribe to chat topic
     */
    this.unsuscribe = function ()
    {
        clankSession.unsubscribe('chat/'+self.id);
    };
    
    /**
     * Listen window unload event to unsuscribe when user leave the page
     */
    this.unsuscribeOnLeave = function ()
    {
        window.onbeforeunload = function() {
            self.unsubscribe();
        };
    };
    
    // Init chat instance
    this.init();
}

function initChats () {
    var chats = {};
    
    jQuery('.chat').each(function () {
        var id = jQuery(this).attr('id');
        
        chats[id] = new Chat(id);
    });
    
    window['chats'] = chats;
}
