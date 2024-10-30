
const updateData = {
    handelUpdatePostToDB: function(offset) {
        offset = offset || 0; 
        var batchSize = 30;
        jQuery('.laci-percent-updated').show();
        jQuery('.laci-action-update-post-to-db').hide();
        jQuery('.laci-time-updated').hide();
        jQuery('.laci-icon-update-post').hide();
        
        jQuery.ajax({
            url: laci_internal_links_update_database.ajax_url,
            type: 'POST',
            data: {
                action: 'laci_update_post_to_db',
                nonce: laci_internal_links_update_database.nonce,
                batch_size: batchSize,
                offset: offset,
            },
            success: function(response) {
                if (response.success === true) {
                    const totalPosts = response.data.total_posts
                    const processedPosts = offset + batchSize;
                    const percentage = Math.min((processedPosts / totalPosts) * 100, 100).toFixed(2);
                    console.log(percentage + '%');
                    offset += batchSize;
                    jQuery('.laci-percent-updated .laci-percent-number').text(percentage);
                    if (offset < totalPosts) {
                        updateData.handelUpdatePostToDB(offset); // Gọi lại hàm với offset mới
                    } else {
                        jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');

                        notification.showNotification('Updated post successfully');

                        jQuery('.laci-percent-updated').hide();
                        jQuery('.laci-action-update-post-to-db').show();
                        jQuery('.laci-time-updated').show();
                        jQuery('.laci-icon-update-post').show();
                        jQuery('.laci-action-update-post-to-db').removeClass('laci-updating-message');
                        jQuery('.laci-action-update-post-to-db').removeAttr('disabled');
                        window.location.reload();
                    }
                } else {
                    jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');

                    notification.showNotification('Updated post fail');

                    jQuery('.laci-percent-updated').hide();
                    jQuery('.laci-action-update-post-to-db').show();
                    jQuery('.laci-time-updated').show();
                    jQuery('.laci-icon-update-post').show();
                    jQuery('.laci-action-update-post-to-db').removeClass('laci-updating-message');
                    jQuery('.laci-action-update-post-to-db').removeAttr('disabled');
                }
            },
            error: function(error) {
                jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');

                notification.showNotification('Updated post fail');

                jQuery('.laci-action-update-post-to-db').removeClass('laci-updating-message');
                jQuery('.laci-action-update-post-to-db').removeAttr('disabled');
            }
        });
    },
    handleStartUpdatePostCron() {
        jQuery.ajax({
            url: laci_internal_links_update_database.ajax_url,
            type: 'POST',
            data: {
                action: 'laci_start_update_post_cron',
                nonce: laci_internal_links_update_database.nonce,
            },
            success: function(response) {
              
            },
            error: function(response) {
                console.log(response);
            }
        });
    },
    checkCronJobStatus($) {
        jQuery.ajax({
            url: laci_internal_links_update_database.ajax_url,
            type: 'POST',
            data: {
                action: 'laci_check_cron_job_status',
                nonce: laci_internal_links_update_database.nonce,
            },
            success: function(response) {
                if (response.success) {
                    const percent = response.data.percent;
                    const cron_job_status = response.data.cron_job_status;
                    const is_next_scheduled = response.data.is_next_scheduled;

                    const data = {
                        'percent' : percent,
                        'current_post_update' : response.data.current,
                        'cron_job_status' :cron_job_status,
                        'next_schedule' : is_next_scheduled
                    }

                    if ( cron_job_status === 'running' ) {
                        console.log(data);
                        jQuery('.laci-percent-updated').show();
                        jQuery('.laci-action-update-post-to-db').hide();
                        jQuery('.laci-time-updated').hide();
                        jQuery('.laci-icon-update-post').hide();
                        jQuery('.laci-percent-number').text(percent);

                        if (!is_next_scheduled && cron_job_status === 'running') {
                            updateData.handleStartUpdatePostCron();
                        }

                        if (cron_job_status === 'running') {
                            setTimeout(updateData.checkCronJobStatus, 10000);
                        }
                    }

                    if (cron_job_status === 'completed') {
                        notification.showNotification('Update successfully');

                        jQuery(this).attr('disabled', 'false');
                        jQuery('.laci-percent-updated').hide();
                        jQuery('.laci-action-update-post-to-db').show();
                        jQuery('.laci-time-updated').show();
                        jQuery('.laci-icon-update-post').show();
                        window.location.reload();
                    }
                }
            },
            error: function(response) {
                console.log(response);
            }
        });
    }
    
    
}

jQuery(document).ready(function($) {
    if (laci_internal_links_update_database.is_update_with_cron) {
        console.log('is_update_with_cron');
        jQuery('.laci-action-update-post-to-db').on('click', function(e) {
            e.preventDefault();
            jQuery(this).attr('disabled', 'true');
            updateData.handleStartUpdatePostCron();
            setTimeout(updateData.checkCronJobStatus, 1500);
        });
    
        if (laci_internal_links_update_database.cron_job_status === 'running') {
            console.log('running check cron job status');
            jQuery('.laci-action-update-post-to-db').attr('disabled', 'true');
            updateData.checkCronJobStatus($)
        }
              
    } else {
        console.log('not_update_with_cron');
        jQuery('.laci-action-update-post-to-db').on('click', function(e) {
            e.preventDefault();
            jQuery(this).addClass('laci-updating-message');
            jQuery(this).attr('disabled', 'true');
            updateData.handelUpdatePostToDB();
        });
    }
});