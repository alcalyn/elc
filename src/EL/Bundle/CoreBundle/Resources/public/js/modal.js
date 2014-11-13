
var modal =
{
    pop: function ($modal)
    {
        jQuery('body').append($modal);
        $modal.modal();
    },
    
    /**
     * Generate a simple modal with content, title, level (warning, success...), and a footer
     * 
     * @param {String} content
     * @param {String} title
     * @param {String} level can be 'default', 'success', 'warning', 'danger', ...
     * @param {String} footer
     */
    popSimple: function (content, title, level, footer)
    {
        var $modal = jQuery(modal.getHtml());
        
        $modal.find('.modal-body').html(content);
        
        if (title) {
            $modal.find('.modal-title').html(title);
        } else {
            $modal.find('.modal-header').remove();
        }
        
        if (level) {
            $modal.find('.modal-content').addClass('panel-'+level);
            $modal.find('.modal-header').addClass('panel-heading');
        }
        
        if (footer) {
            $modal.find('.modal-footer').html(footer);
        }
        
        modal.pop($modal);
    },
    
    getHtml: function ()
    {
        return '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
            '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                        '<h4 class="modal-title" id="myModalLabel"></h4>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '</div>' +
                    '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-default" data-dismiss="modal">'+t('close')+'</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }
}
