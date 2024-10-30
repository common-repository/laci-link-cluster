const notification = {
    showNotification(message, error = false) {
        if (!message) {
            return;
        }
        const iconClass = error ? 'dashicons-dismiss' : 'dashicons-yes-alt';
        const notificationClass = error ? 'laci-error' : '';
        const notification = jQuery(`<div class="laci-notification ${notificationClass}"><span style="font-size:20px" class="dashicons ${iconClass}"></span> ${message} </div>`);
        jQuery('body').append(notification);
        setTimeout(function() {
            notification.fadeOut(500, function() {
                jQuery(this).remove();
            });
        }, 2000);
    },
};

jQuery(document).ready(function($) {
    notification.showNotification(message = null);
});