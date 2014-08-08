jQuery(function () {
    initChat();
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
        self.bindSubmitButton();
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
            $message.append('<a href="#">'+message.pseudo+'</a> : '+message.content);
        } else {
            $message.append(message.content);
        }
        
        self.$chat.find('.messages').append($message);
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
            clankSession.publish('chat/general-fr', jQuery('.message-input').val());
            
            self.clearInput();
            
            return false;
        });
    };
    
    this.init();
}

function initChat() {
    var chat = new Chat('general-fr');
    window['chat'] = chat;
    
    connectClank(function () {
        clankSession.subscribe('chat/general-fr', function (uri, data) {
            console.log(uri, data);
            
            chat.addMessage(data.message);
        });
    });
}
