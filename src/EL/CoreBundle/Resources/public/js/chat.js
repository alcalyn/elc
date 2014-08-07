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
    var _this = this;
    
    /**
     * Init chat
     */
    this.init = function ()
    {
        _this.bindSubmitButton();
    };
    
    this.addMessage = function (message)
    {
        if (!message || !message.pseudo || !message.content) {
            console.log('message mal formatté : ', message);
            return;
        }
        
        var $message = jQuery('<li class="message">');
        
        $message.append('<a href="#">'+message.pseudo+'</a> : '+message.content);
        
        _this.$chat.find('.messages').append($message);
    };
    
    this.clearInput = function ()
    {
        _this.$chat.find('input.message-input').val('');
    };
    
    this.bindSubmitButton = function ()
    {
        _this.$chat.find('form.submit').submit(function () {
            var formSerial = jQuery(this).serializeArray();
            
            var formJson = {};
            
            jQuery.each(formSerial, function (key, value) {
                formJson[value['name']] = value['value'];
            });
            
            if (0 === jQuery.trim(formJson.content).length) {
                return false;
            }
            
            console.log('submit', formJson);
            
            clankSession.publish('chat/general-fr', formJson);
            
            _this.clearInput();
            
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
