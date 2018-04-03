var dvnewsletter = {
    'saveMail'  : function ($button) {
        var mail = $button.closest('.newsletter-content').find('.newsletter_email').val();
        var container = $button.closest('.newsletter-container');

        $.ajax({
            url: 'index.php?route=diva/newsletter/subscribe',
            type: 'post',
            data: {
                mail : mail
            },
            beforeSend: function () {
                $button.button('loading');
                container.find('.newsletter-notification').removeClass().addClass('newsletter-notification').html('');
            },
            success: function (json) {
                if(json['status'] == true) {
                    container.find('.newsletter-notification').addClass('success').html(json['success']);
                } else {
                    container.find('.newsletter-notification').addClass('error').html(json['error']);
                }
            },
            complete: function () {
                $button.button('reset');
            }
        });
    },
    
    'closePopup': function () {
        
    },
    
    'resetCookies' : function () {
        
    }
}