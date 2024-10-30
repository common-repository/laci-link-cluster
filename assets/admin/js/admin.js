
const wpInternalLinks = {
    showInfoInternalLinks ($) {
        jQuery('.inbound-links-count, .outbound-links-count, .inbound-links-in-category-count, .outbound-links-in-category-count, .links-back-to-category-count').on('click', function(e) {
            e.preventDefault();
            const postId = jQuery(this).data('post-id');
            const linkType = jQuery(this).data('type');
    
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_internal_links_info',
                    nonce: laci_internal_links.nonce,
                    post_id: postId,
                    link_type: linkType
                },
                success: function (response) {
                  if (response.success === true) {
                    console.log(response.data.html);
                    if (response.success && response.data.html) {
                        // Show popup with response.data.html
                        const $popup = $('<div class="laci-internal-links-popup"></div>').html(response.data.html);
                        const title = response.data.title;
                        $popup.dialog({
                            title: title,
                            width: 600,
                            height: 300,
                            modal: true,
                            buttons: {
                                Close: function() {
                                    $(this).dialog("close");
                                    $(this).remove();
                                }
                            },
                            close: function() { 
                                $(this).remove();
                            }
                        });
                    } else {
                        alert('Not have internal links information.');
                    }
                  }
                },
              });
        });
    },
    getInfoSuggest($) {
        jQuery('.laci-table-suggested-button-copy').on('click', function(e) {
            e.preventDefault();
            console.log(1111);
        });
    },
    copyLinkPost($) {
        jQuery('.laci-copy-link').on('click', function(e) {
            e.preventDefault();
            const postURL = jQuery(this).data('post-url');
            const $temp = jQuery("<input>");
            jQuery("body").append($temp);
            $temp.val(postURL).select();
            document.execCommand("copy");
            $temp.remove();
            alert('Copy link success!');
        });
    
    },
    addKeyWordToPost($) {
        jQuery(document).on("click", ".laci-add-keyword-button", function (e) {
            const newKeyword = jQuery(this).parent().find('input[name="laci_add_key_word"]').val().trim();
            
            if (newKeyword === '') {
                return;
            }
    
            jQuery(this).parent().find('.laci-list-key-word').append('<li class="laci-list-key-word__item" data-key="'+newKeyword+'">  <a class="dashicons dashicons-dismiss remove"></a> ' + jQuery('<div>').text(newKeyword).html() + '</li>');
    
            const existingKeywords = jQuery(this).parent().find('input[name="laci_list_key_word"]').val();
            let updatedKeywords;
    
            if (existingKeywords) {
                updatedKeywords = existingKeywords.split(',');
            } else {
                updatedKeywords = [];
            }
    
            updatedKeywords.push(newKeyword.trim());
            jQuery(this).parent().find('input[name="laci_list_key_word"]').val(updatedKeywords.join(','));
    
            jQuery(this).parent().find('input[name="laci_add_key_word"]').val('');
        });
    },
    displayTooltipTitle($) {
        jQuery('.row-title').hover(
            function() {
                var title = jQuery(this).attr('title');
                if (title) {
                    jQuery(this).data('title', title).removeAttr('title');
                }
            },
            function() {
                var dataTitle = jQuery(this).data('title');
                if (dataTitle) {
                    jQuery(this).attr('title', dataTitle).removeData('title');
                }
            }
        );
    },
    handleChangeCategory($) {
        jQuery('.laci-categories-icon-edit').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery('.laci-category-for-post-select').toggleClass('laci-display-block');
            jQuery('.laci-category-text').toggleClass('laci-display-none');

            jQuery('#taxonomies .laci-categories-title').hide();
            jQuery('#taxonomies .sorting-indicators').hide();
            
            jQuery('.laci-categories-action-change').show();
        });
    },
    handleCancelChangeCategory($) {
        jQuery('.laci-categories-cancel-change').on('click', function(e) {
            e.preventDefault();

            jQuery('.laci-category-for-post-select').toggleClass('laci-display-block');
            jQuery('.laci-category-text').toggleClass('laci-display-none');

            jQuery('#taxonomies .laci-categories-title').show();
            jQuery('#taxonomies .sorting-indicators').show();

            jQuery('.laci-categories-action-change').hide();
        });
    },
    handleSaveChangeCategory($) {
        jQuery('.laci-categories-save-change').on('click', function(e) {
            e.preventDefault();
            var listCategory = [];

            jQuery(this).addClass('laci-updating-message');
          
            jQuery('.laci-category-for-post').each(function() {
                const id = jQuery(this).data('id');
                const categories = jQuery(this).val();
                listCategory.push({
                    post_id: id.toString(),
                    categories: categories
                });
            });
            
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_change_category_for_post',
                    nonce: laci_internal_links.nonce,
                    list_category: listCategory,
                },
                success: function (response) {
                  if (response.success === true) {
                    if (response.success) {
                        jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');
                        
                        notification.showNotification('Category updated successfully');

                        const listCategoryRes = response.data.list_category;

                        for (let i = 0; i < listCategoryRes.length; i++) {
                            const postId = listCategoryRes[i].post_id;
                            const categories_title = listCategoryRes[i].categories_title;
                            const categories_edit_link = listCategoryRes[i].categories_edit_link;
                            jQuery('.laci-category-text__' + postId).html(categories_edit_link.join(', '));
                        }

                        jQuery('.laci-categories-save-change').removeClass('laci-updating-message');

                        jQuery('.laci-category-for-post-select').toggleClass('laci-display-block');
                        jQuery('.laci-category-text').toggleClass('laci-display-none');

                        jQuery('#taxonomies .laci-categories-title').show();
                        jQuery('#taxonomies .sorting-indicators').show();

                        jQuery('.laci-categories-action-change').hide();

                    } else {
                        jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');
                     
                        notification.showNotification('Key words updated');

                        jQuery('.laci-categories-save-change').removeClass('laci-updating-message');
                    }
                  }
                },
            });
        });
    },
    handleChangeKeyWords($) {
        jQuery('.laci-key-words-icon-edit').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery('.laci-key-words-change').toggleClass('laci-display-block');
            jQuery('.laci-key-words-text').toggleClass('laci-display-none');

            jQuery('#key_words .laci-key-words-title').hide();
            jQuery('#key_words .sorting-indicators').hide();
            
            jQuery('.laci-key-words-action-change').show();
        });
    },
    handleCancelChangeKeyWords($) {
        jQuery('.laci-key-words-cancel-change').on('click', function(e) {
            e.preventDefault();

            jQuery('.laci-key-words-change').toggleClass('laci-display-block');
            jQuery('.laci-key-words-text').toggleClass('laci-display-none');

            jQuery('#key_words .laci-key-words-title').show();
            jQuery('#key_words .sorting-indicators').show();

            jQuery('.laci-key-words-action-change').hide();
        });
    },
    handleSaveChangeKeyWords(){
        jQuery('.laci-key-words-save-change').on('click', function(e) {
            e.preventDefault();
            jQuery(this).addClass('laci-updating-message');

            var list_key_words = [];
          
            jQuery('.laci-key-words-change').each(function() {
                const id = jQuery(this).data('id');
                const keyWords = jQuery(this).val();
                list_key_words.push({
                    post_id: id.toString(),
                    key_words: keyWords
                });
            });

            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_change_key_words_for_post',
                    nonce: laci_internal_links.nonce,
                    list_key_words: list_key_words,
                },
                success: function (response) {
                  if (response.success === true) {
                    if (response.success) {
                        jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');

                        notification.showNotification('Key words updated successfully');

                        for (let i = 0; i < list_key_words.length; i++) {
                            const postId = list_key_words[i].post_id;
                            const keyWords = list_key_words[i].key_words;
                            jQuery('.laci-key-words-text__' + postId).text(keyWords);
                        }

                        jQuery('.laci-key-words-change').toggleClass('laci-display-block');
                        jQuery('.laci-key-words-text').toggleClass('laci-display-none');

                        jQuery('#key_words .laci-key-words-title').show();
                        jQuery('#key_words .sorting-indicators').show();

                        jQuery('.laci-key-words-action-change').hide();
                    } else {
                        jQuery('.laci-key-words-save-change').removeClass('laci-updating-message');

                        notification.showNotification('Key words updated');
                    }
                  }
                },
              });
        });
    
    },
    handleRemoveKeyWord() {
        jQuery(document).on("click", ".laci-list-key-word .remove", function (e) {
            const that = jQuery(this);
            const existingKeywords = jQuery('input[name="laci_list_key_word"]').val().split(',');
            const updatedKeywords = existingKeywords.filter(function(keyword) {
                return that.parents('.laci-list-key-word__item').data('key').toString() !== keyword;
            });
            that.closest('.laci-update-keywords').find('input[name="laci_list_key_word"]').val(updatedKeywords.join(','));
            that.parent().remove();
        });
    },
    handelUpdateSinglePostToDB() {
        jQuery('.update-internal-link').on('click', function(e) {
            e.preventDefault();
            const that = jQuery(this);
            const postID = jQuery(this).data('id');
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_update_single_post_to_db',
                    nonce: laci_internal_links.nonce,
                    post_id: postID,
                },
                success: function(response) {
                    if (response.success === true) {
                        const inbound_links = response.data.inbound_links;
                        const outbound_links = response.data.outbound_links;
                        const inbound_links_in_category = response.data.inbound_links_in_category;
                        const outbound_links_in_category = response.data.outbound_links_in_category;
                        const link_back_to_category = response.data.link_back_to_category;
                        const total_links = response.data.total_links;
                        
                        const dataPostID = `[data-post-id="${postID}"]`;

                        jQuery(`.laci-count-link.inbound-links-count${dataPostID}`).text(inbound_links);
                        if (inbound_links > 0) {
                            jQuery(`.laci-count-link.inbound-links-count${dataPostID}`).parent().addClass('bg-green');
                            jQuery(`.laci-count-link.inbound-links-count${dataPostID}`).parent().removeClass('bg-gray');
                        } else {
                            jQuery(`.laci-count-link.inbound-links-count${dataPostID}`).parent().removeClass('bg-green');
                            jQuery(`.laci-count-link.inbound-links-count${dataPostID}`).parent().addClass('bg-gray');
                        }
                       

                        jQuery(`.laci-count-link.outbound-links-count${dataPostID}`).text(outbound_links);
                        if (outbound_links > 0) {
                            jQuery(`.laci-count-link.outbound-links-count${dataPostID}`).parent().addClass('bg-green');
                            jQuery(`.laci-count-link.outbound-links-count${dataPostID}`).parent().removeClass('bg-gray');
                        } else {
                            jQuery(`.laci-count-link.outbound-links-count${dataPostID}`).parent().removeClass('bg-green');
                            jQuery(`.laci-count-link.outbound-links-count${dataPostID}`).parent().addClass('bg-gray');
                        }
                       

                        jQuery(`.laci-count-link.inbound-links-in-category-count${dataPostID}`).text(inbound_links_in_category);
                        if (inbound_links_in_category > 0) {
                            jQuery(`.laci-count-link.inbound-links-in-category-count${dataPostID}`).parent().addClass('bg-green');
                            jQuery(`.laci-count-link.inbound-links-in-category-count${dataPostID}`).parent().removeClass('bg-gray');
                        } else {
                            jQuery(`.laci-count-link.inbound-links-in-category-count${dataPostID}`).parent().removeClass('bg-green');
                            jQuery(`.laci-count-link.inbound-links-in-category-count${dataPostID}`).parent().addClass('bg-gray');
                        }

                        jQuery(`.laci-count-link.outbound-links-in-category-count${dataPostID}`).text(outbound_links_in_category);
                        if (outbound_links_in_category > 0) {
                            jQuery(`.laci-count-link.outbound-links-in-category-count${dataPostID}`).parent().addClass('bg-green');
                            jQuery(`.laci-count-link.outbound-links-in-category-count${dataPostID}`).parent().removeClass('bg-gray');
                        } else {
                            jQuery(`.laci-count-link.outbound-links-in-category-count${dataPostID}`).parent().removeClass('bg-green');
                            jQuery(`.laci-count-link.outbound-links-in-category-count${dataPostID}`).parent().addClass('bg-gray');
                        }

                        jQuery(`.laci-count-link.links-back-to-category-count${dataPostID}`).text(link_back_to_category);
                        if (total_links > 0) {
                            jQuery(`.laci-count-link.links-back-to-category-count${dataPostID}`).parent().addClass('bg-green');
                            jQuery(`.laci-count-link.links-back-to-category-count${dataPostID}`).parent().removeClass('bg-gray');
                        } else {
                            jQuery(`.laci-count-link.links-back-to-category-count${dataPostID}`).parent().removeClass('bg-green');
                            jQuery(`.laci-count-link.links-back-to-category-count${dataPostID}`).parent().addClass('bg-gray');
                        }

                        notification.showNotification('Updated post successfully');
                    } else {
                        notification.showNotification('Updated post fail');
                    }
                },
            });
        });
    },
    handleClearAllFilterLink($) {
        jQuery('.laci-filter-link-group-clear-all').on('click', function(e) {
            e.preventDefault();
            jQuery('.laci-filter-link').val('');
            jQuery('.laci-wrap-from').trigger('submit');
        });
    }
    ,handleDisplayMenuAction($) {
        $('.laci-row-title-action').click(function(event) {
            event.stopPropagation();
            $('.laci-row-title-action').removeClass('active');
            $(this).addClass('active');
        });
    
        $(document).click(function() {
            $('.laci-row-title-action').removeClass('active');
        });
    
        $('.laci-title-action').click(function(event) {
            event.stopPropagation();
        });
    },
    showPopupUpdateKeywords($) {
        function handleSaveChangeKeyWords(postId, $popup) {
            list_key_words = $popup.find('input[name="laci_list_key_word"]').val();
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_change_key_words_for_post',
                    nonce: laci_internal_links.nonce,
                    list_key_words: list_key_words,
                    post_id: postId
                },
                success: function (response) {
                  if (response.success === true) {
                    if (response.success) {
                        if (jQuery('.laci-placement-assistant-title').length > 0) {
                            jQuery('.laci-placement-assistant-title').attr('data-keywords', list_key_words);
                        }
                        notification.showNotification('Key words updated successfully');
                    } else {
                        notification.showNotification('Key words updated');
                    }
                  }
                },
              });
        }

        jQuery('.laci-action-update-focus-keywords').on('click', function(e) {
            e.preventDefault();
            const postId = jQuery(this).data('id');

            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_get_keywords_info',
                    nonce: laci_internal_links.nonce,
                    post_id: postId,
                },
                success: function (response) {
                if (response.success === true) {
                    console.log(response.data.html);
                    if (response.success && response.data.html) {
                        // Show popup with response.data.html
                        const $popup = $('<div class="laci-internal-links-popup"></div>').html(response.data.html);
                        $popup.dialog({
                            title: 'Update Focus Keywords',
                            width: 600,
                            height: 300,
                            modal: true,
                            buttons: {
                                Close: function() {
                                    $(this).dialog("close");
                                    $(this).remove();
                                },
                                Save: function() {
                                    console.log(postId);
                                    handleSaveChangeKeyWords(postId , $popup);
                                },
                                "Save & Close": function() {
                                    handleSaveChangeKeyWords(postId, $popup);
                                    $(this).dialog("close");
                                    $(this).remove();
                                }
                            },
                            close: function() { 
                                $(this).remove();
                            }
                        });
                    } else {
                        alert('Not have internal links information.');
                    }
                }
            },
        });
    });
    }
}

jQuery(document).ready(function($) {
    wpInternalLinks.showInfoInternalLinks($);
    wpInternalLinks.getInfoSuggest($);
    wpInternalLinks.copyLinkPost($);
    wpInternalLinks.addKeyWordToPost($);
    wpInternalLinks.displayTooltipTitle($);
    wpInternalLinks.handleChangeCategory($);
    wpInternalLinks.handleCancelChangeCategory($);
    wpInternalLinks.handleSaveChangeCategory($);
    wpInternalLinks.handleChangeKeyWords($);
    wpInternalLinks.handleCancelChangeKeyWords($);
    wpInternalLinks.handleSaveChangeKeyWords($);
    wpInternalLinks.handleRemoveKeyWord($);
    wpInternalLinks.handelUpdateSinglePostToDB($);
    wpInternalLinks.handleClearAllFilterLink($);
    wpInternalLinks.handleDisplayMenuAction($);
    wpInternalLinks.showPopupUpdateKeywords($);
});