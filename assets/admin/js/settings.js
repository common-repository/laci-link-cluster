const wpilSettings = {
    wpilColorPicker($) {
        jQuery('#laci-bg-color').wpColorPicker();
        jQuery('#laci-bd-color').wpColorPicker();
        jQuery('#laci-title-color').wpColorPicker();
        jQuery('#laci-content-color').wpColorPicker();
    },
    importKeyWordsRankMath($) {
        function importKeywords(offset = 0, totalPosts = 0) {
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_import_key_words_rank_math',
                    nonce: laci_internal_links.nonce,
                    offset: offset,
                },
                success: function (response) {
                    if (response.success === true) {
                        if (response.success && response.data.message) {
                            // Calculate progress percentage
                            if (totalPosts === 0) {
                                totalPosts = response.data.totalPosts;
                            }
        
                            let progress = Math.min((offset / totalPosts) * 100, 100).toFixed(2);
                            notification.showNotification('Progress: ' + progress + '%');
        
                            // Continue with the next batch if there's more to process
                            if (response.data.nextOffset !== null) {
                                importKeywords(response.data.nextOffset, totalPosts);
                            } else {
                               notification.showNotification(response.data.message);
                            }
                        }
                    } else {
                        alert('No internal links information.');
                    }
                },
            });
        }
        jQuery(document).on("click", ".import-key-work-rank-math", function (e) {
            e.preventDefault();
            importKeywords();
        });
    },
    importKeyWordsYoast($) {
        function importKeywords(offset = 0, totalPosts = 0) {
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_import_key_words_yoast',
                    nonce: laci_internal_links.nonce,
                    offset: offset,
                },
                success: function (response) {
                    if (response.success === true) {
                        if (response.success && response.data.message) {
                            // Calculate progress percentage
                            if (totalPosts === 0) {
                                totalPosts = response.data.totalPosts;
                            }
        
                            let progress = Math.min((offset / totalPosts) * 100, 100).toFixed(2);
                            notification.showNotification('Progress: ' + progress + '%');
        
                            // Continue with the next batch if there's more to process
                            if (response.data.nextOffset !== null) {
                                importKeywords(response.data.nextOffset, totalPosts);
                            } else {
                               notification.showNotification(response.data.message);
                            }
                        }
                    } else {
                        alert('No internal links information.');
                    }
                },
            });
        }
        jQuery(document).on("click", ".import-key-work-yoast", function (e) {
            e.preventDefault();
            console.log(111111);
            importKeywords();
        });
    },
    uploadImage($) {
        var wp_media_frame;

        $('#laci-related-box-image-upload').on('click', function(e) {
            e.preventDefault();
    
            // If the media frame already exists, reopen it.
            if (wp_media_frame) {
                wp_media_frame.open();
                return;
            }
    
            // Create the media frame.
            wp_media_frame = wp.media.frames.wp_media_frame = wp.media({
                title: 'Select Image for Related Box',
                button: {
                    text: 'Use this image',
                },
                multiple: false
            });
    
            // When an image is selected, run a callback.
            wp_media_frame.on('select', function() {
                var attachment = wp_media_frame.state().get('selection').first().toJSON();
                $('#laci-related-box-image').val(attachment.url); // Set the input value to the image URL
            });
    
            // Finally, open the modal.
            wp_media_frame.open();
        });
    }
}

jQuery(document).ready(function($){
    wpilSettings.wpilColorPicker($);
    wpilSettings.importKeyWordsRankMath($);
    wpilSettings.importKeyWordsYoast($);
    wpilSettings.uploadImage($);
});