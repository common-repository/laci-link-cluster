
const linkAssistant = {
    actionChangeKeyword($) {
        jQuery('.laci-key-words-change').on('input', function() {
            const value = jQuery(this).val();
            const options = value.split(',').map(item => item.trim());
            const select = jQuery('.laci-search-input-control');

            select.empty();

            const $defaultOption = jQuery('<option></option>')
                .attr('value', '')
                .text('Select a keyword');

            select.append($defaultOption);

            options.forEach(option => {
                const $option = jQuery('<option></option>')
                    .attr('value', option)
                    .text(option);
                select.append($option);
            });

            select.trigger('change');
        });
    },
    handleShowPopupEdit($){
        jQuery(document).on("click", ".laci-same-cat-content-edit", function (e) {
            e.preventDefault();

            const midSection = jQuery(this).closest('.laci-mid-section').addClass('laci-current-section-active');
            const currentExcerpt = parseInt(midSection.attr('data-current-excerpt'));

            const title = midSection.find('.laci-name-post').data('title');
            const content = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`).html();

            const postID = jQuery(this).closest('.laci-container-same-cat-item').data('id');

            jQuery("#popup-editor").html(content);

            jQuery("#laci-current-data").html(content);
            jQuery("#laci-current-data").attr('data-id', postID);
            jQuery("#laci-current-data").attr('data-currentExcerpt', currentExcerpt);

            jQuery('.laci-internal-links-popup').dialog('open');
            jQuery('.laci-internal-links-popup').dialog('option', 'title', title);

            if (tinymce.get('popup-editor')) {
                setTimeout(function() {
                    tinymce.get('popup-editor').setContent(content);
                }, 10);
            }
        });

        jQuery(document).on("click", ".laci-diff-cat-content-edit", function (e) {
            e.preventDefault();

            const midSection = jQuery(this).closest('.laci-mid-section').addClass('laci-current-section-active');
            const currentExcerpt = parseInt(midSection.attr('data-current-excerpt'));

            const title = midSection.find('.laci-name-post').data('title');
            const content = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`).html();

            const postID = jQuery(this).closest('.laci-container-diff-cat-item').data('id');

            jQuery("#popup-editor").html(content);

            jQuery("#laci-current-data").html(content);
            jQuery("#laci-current-data").attr('data-id', postID);
            jQuery("#laci-current-data").attr('data-currentExcerpt', currentExcerpt);

            jQuery('.laci-internal-links-popup').dialog('open');
            jQuery('.laci-internal-links-popup').dialog('option', 'title', title);

            if (tinymce.get('popup-editor')) {
                setTimeout(function() {
                    tinymce.get('popup-editor').setContent(content);
                }, 10);
            }
        });
        
    },
    createdPopup($) {
        $('.laci-internal-links-popup').dialog({
            autoOpen: false,
            title: 'Title',
            width: 800,
            height: 400,
            modal: true,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                    jQuery('.laci-mid-section').removeClass('laci-current-section-active');
                    //$(this).remove();
                },
                Save: function() {
                    HandelSaveContent();
                },
                "Save & Close": function() {
                    HandelSaveContent();
                    jQuery('.laci-mid-section').removeClass('laci-current-section-active');
                    $(this).dialog("close");
                    //$(this).remove();
                }

            },
            close: function() { 
                //$(this).remove();
            }
        });

        function HandelSaveContent() {
            const midSection = jQuery('.laci-mid-section.laci-current-section-active');
            const postID =  jQuery("#laci-current-data").attr('data-id');
            const currentExcerpt = jQuery("#laci-current-data").attr('data-currentExcerpt');

            const contentBefore = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`).html();
            const contentAfter = tinymce.get('popup-editor').getContent().replace(/<p>(?:&nbsp;|\s)*<\/p>/g, '');
           
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_save_internal_links_for_post',
                    nonce: laci_internal_links.nonce,
                    content_after: contentAfter,
                    content_before: contentBefore,
                    post_id: postID

                },
                success: function(response) {
                    if (response.success === true) {
                        midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`).html(contentAfter)
                        notification.showNotification('Key words updated');
                    } else {
                        notification.showNotification('Key words can\'t be updated');
                    }
                },
                error: function(response) {
                    notification.showNotification('Key words can\'t be updated');
                }
            });
        }
    },
    handleSearchKeyWordSameCate($) {
        function Search() {
            const keyWord = jQuery('.laci-search-input-control').val();
            const postId = jQuery('.laci-search-input-control').data('id');
            $('.laci-search-button').addClass('laci-updating-message');
            $('.laci-search-button').attr('disabled', 'disabled');
        
            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_search_keyword_same_cate',
                    nonce: laci_internal_links.nonce,
                    key_word: keyWord,
                    post_id: postId,
                },
                success: function(response) {
                    $('.laci-search-button').removeClass('laci-updating-message');
                    $('.laci-search-button').removeAttr('disabled', 'disabled');
                    if (response.success && response.data) {
                        jQuery('.laci-search-results__same-category__content').html(response.data.html_same_cate);
                        jQuery('.laci-load-more-same-category').attr('data-max-pages', response.data.max_pages_same_cate);
                        jQuery('.laci-load-more-same-category').attr('data-post-id', postId);
                        jQuery('.laci-load-more-same-category').attr('data-key-word', keyWord);
                        jQuery('.laci-load-more-same-category').attr('data-set-paging', 1);


                        jQuery('.laci-search-results__diff-category__content').html(response.data.html_diff_cate);
                        jQuery('.laci-load-more-diff-category').attr('data-max-pages', response.data.max_pages_diff_cate);
                        jQuery('.laci-load-more-diff-category').attr('data-post-id', postId);
                        jQuery('.laci-load-more-diff-category').attr('data-key-word', keyWord);
                        jQuery('.laci-load-more-diff-category').attr('data-set-paging', 1);


                        if (+response.data.max_pages_same_cate > 1) {
                            jQuery('.laci-search-results__same-category__load-more').removeClass('laci-display-none');
                        } else {
                            jQuery('.laci-search-results__same-category__load-more').addClass('laci-display-none');
                        }

                        if (+response.data.max_pages_diff_cate > 1) {
                            jQuery('.laci-search-results__diff-category__load-more').removeClass('laci-display-none');
                        } else {
                            jQuery('.laci-search-results__diff-category__load-more').addClass('laci-display-none');
                        }

                        const currentURL = response.data.current_url;
                        window.history.replaceState(null, '', currentURL);

                        if(response.data.same_cate_error) {
                            jQuery('.laci-search-results__same-category__content').html(`<p class="laci-text-not-info">${response.data.same_cate_error}</p>`);
                        }

                        if(response.data.diff_cate_error) {
                            jQuery('.laci-search-results__diff-category__content').html(`<p class="laci-text-not-info">${response.data.diff_cate_error}</p>`);
                        }

                        notification.showNotification('Search completed');
                    } else {
                        jQuery('.laci-search-results__same-category__load-more').addClass('laci-display-none');
                        jQuery('.laci-load-more-same-category').attr('data-set-paging', 1);
                        jQuery('.laci-search-results__same-category__content').html(`<p class="laci-text-not-info">${response.data.mess_same_cate}</p>`);

                        jQuery('.laci-search-results__diff-category__load-more').addClass('laci-display-none');
                        jQuery('.laci-load-more-diff-category').attr('data-set-paging', 1);
                        jQuery('.laci-search-results__diff-category__content').html(`<p class="laci-text-not-info">${response.data.mess_diff_cate}</p>`);
                        
                        const currentURL = response.data.current_url;
                        window.history.replaceState(null, '', currentURL);
                        notification.showNotification('Search completed');
                    }
                },
                error: function(response) {
                    $('.laci-search-button').removeClass('laci-updating-message');
                    $('.laci-search-button').removeAttr('disabled', 'disabled');
                    console.log(response);
                    notification.showNotification('Search failed', true);
                }
            });
        }

        jQuery('.laci-search-button').on('click', function() {
            Search();
        });

        $('.laci-search-input-control').on('keypress', function(e) {
            if (e.which === 13) { 
                Search();
            }
        });
    
    },
     actionNextAndPreviousContent($) {
        function updateButtons(midSection, currentExcerpt, totalExcerpts) {
            const totalKeyWord = midSection.find('.laci-text-highlight').length;

            const isDisabledPreviousBtn = currentExcerpt === 0 && midSection.find('.laci-text-highlight').eq(0).hasClass('active-highlight');

            midSection.find('.laci-same-cat-content-previous').prop('disabled', isDisabledPreviousBtn);

            const isDisabledNextBtn = currentExcerpt === totalExcerpts - 1 && midSection.find('.laci-text-highlight').eq(totalKeyWord - 1).hasClass('active-highlight');
            
            midSection.find('.laci-same-cat-content-next').prop('disabled', isDisabledNextBtn);
        }

        function updateButtonsForDiff(midSection, currentExcerpt, totalExcerpts) {
            const totalKeyWord = midSection.find('.laci-text-highlight').length;

            const isDisabledPreviousBtn = currentExcerpt === 0 && midSection.find('.laci-text-highlight').eq(0).hasClass('active-highlight');

            midSection.find('.laci-diff-cat-content-previous').prop('disabled', isDisabledPreviousBtn);

            const isDisabledNextBtn = currentExcerpt === totalExcerpts - 1 && midSection.find('.laci-text-highlight').eq(totalKeyWord - 1).hasClass('active-highlight');
            
            midSection.find('.laci-diff-cat-content-next').prop('disabled', isDisabledNextBtn);
        } 
    
        jQuery(document).on("click", ".laci-same-cat-content-next", function (e) {
            let midSection = jQuery(this).closest('.laci-mid-section');
            let currentExcerpt = parseInt(midSection.attr('data-current-excerpt'));
            const totalExcerpts = midSection.find('.laci-post-content__excerpt').length;
            const currentElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`);
            const nextElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt + 1}"]`);
        
            // Find the current active highlight
            let activeHighlight = currentElement.find('.laci-text-highlight.active-highlight');
            if (activeHighlight.length) {
                // Look for the next highlight element
                let nextHighlight = currentElement.find('.laci-text-highlight').filter(function() {
                    return jQuery(this).offset().left > activeHighlight.offset().left;
                }).first();
        
                if (nextHighlight.length) {
                    // Remove active-highlight from current and add to next
                    activeHighlight.removeClass('active-highlight');
                    nextHighlight.addClass('active-highlight');
                } else if (nextElement.length) {
                    // Switch to the next excerpt if there are no more highlights
                    activeHighlight.removeClass('active-highlight');
                    currentElement.hide();
                    nextElement.show();
                    nextElement.find('.laci-text-highlight').first().addClass('active-highlight');
                    currentExcerpt++;
                }
            } else if (nextElement.length) {
                // If no active highlight in current, switch to the next excerpt
                currentElement.hide();
                nextElement.show();
                nextElement.find('.laci-text-highlight').first().addClass('active-highlight');
                currentExcerpt++;
            }
            midSection.attr('data-current-excerpt', currentExcerpt);
            updateButtons(midSection, currentExcerpt, totalExcerpts);
        });
        
        jQuery(document).on("click", ".laci-same-cat-content-previous", function (e) {
            let midSection = jQuery(this).closest('.laci-mid-section');
            let currentExcerpt = parseInt(midSection.attr('data-current-excerpt'));
            const totalExcerpts = midSection.find('.laci-post-content__excerpt').length;
            const currentElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`);
            const prevElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt - 1}"]`);
        
            // Find the current active highlight
            let activeHighlight = currentElement.find('.laci-text-highlight.active-highlight');
            if (activeHighlight.length) {
                // Look for the previous highlight element
                let prevHighlight = currentElement.find('.laci-text-highlight').filter(function() {
                    return jQuery(this).offset().left < activeHighlight.offset().left;
                }).last();
        
                if (prevHighlight.length) {
                    // Remove active-highlight from current and add to previous
                    activeHighlight.removeClass('active-highlight');
                    prevHighlight.addClass('active-highlight');
                } else if (prevElement.length) {
                    // Switch to the previous excerpt if there are no previous highlights
                    activeHighlight.removeClass('active-highlight');
                    currentElement.hide();
                    prevElement.show();
                    prevElement.find('.laci-text-highlight').last().addClass('active-highlight');
                    currentExcerpt--;
                }
            } else if (prevElement.length) {
                // If no active highlight in current, switch to the previous excerpt
                currentElement.hide();
                prevElement.show();
                prevElement.find('.laci-text-highlight').last().addClass('active-highlight');
                currentExcerpt--;
            }
            midSection.attr('data-current-excerpt', currentExcerpt);
            updateButtons(midSection, currentExcerpt, totalExcerpts);
        });
          

        jQuery(document).on("click", ".laci-diff-cat-content-next", function (e) {
            let midSection = jQuery(this).closest('.laci-mid-section');
            let currentExcerpt = parseInt(midSection.attr('data-current-excerpt'));
            const totalExcerpts = midSection.find('.laci-post-content__excerpt').length;
            const currentElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`);
            const nextElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt + 1}"]`);
        
            // Find the current active highlight
            let activeHighlight = currentElement.find('.laci-text-highlight.active-highlight');
            if (activeHighlight.length) {
                // Look for the next highlight element
                let nextHighlight = currentElement.find('.laci-text-highlight').filter(function() {
                    return jQuery(this).offset().left > activeHighlight.offset().left;
                }).first();
        
                if (nextHighlight.length) {
                    // Remove active-highlight from current and add to next
                    activeHighlight.removeClass('active-highlight');
                    nextHighlight.addClass('active-highlight');
                } else if (nextElement.length) {
                    // Switch to the next excerpt if there are no more highlights
                    activeHighlight.removeClass('active-highlight');
                    currentElement.hide();
                    nextElement.show();
                    nextElement.find('.laci-text-highlight').first().addClass('active-highlight');
                    currentExcerpt++;
                }
            } else if (nextElement.length) {
                // If no active highlight in current, switch to the next excerpt
                currentElement.hide();
                nextElement.show();
                nextElement.find('.laci-text-highlight').first().addClass('active-highlight');
                currentExcerpt++;
            }
            midSection.attr('data-current-excerpt', currentExcerpt);
            updateButtonsForDiff(midSection, currentExcerpt, totalExcerpts);
        });

        jQuery(document).on("click", ".laci-diff-cat-content-previous", function (e) {
            let midSection = jQuery(this).closest('.laci-mid-section');
            let currentExcerpt = parseInt(midSection.attr('data-current-excerpt'));
            const totalExcerpts = midSection.find('.laci-post-content__excerpt').length;
            const currentElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt}"]`);
            const prevElement = midSection.find(`.laci-post-content__excerpt[data-num="${currentExcerpt - 1}"]`);
        
            // Find the current active highlight
            let activeHighlight = currentElement.find('.laci-text-highlight.active-highlight');
            if (activeHighlight.length) {
                // Look for the previous highlight element
                let prevHighlight = currentElement.find('.laci-text-highlight').filter(function() {
                    return jQuery(this).offset().left < activeHighlight.offset().left;
                }).last();
        
                if (prevHighlight.length) {
                    // Remove active-highlight from current and add to previous
                    activeHighlight.removeClass('active-highlight');
                    prevHighlight.addClass('active-highlight');
                } else if (prevElement.length) {
                    // Switch to the previous excerpt if there are no previous highlights
                    activeHighlight.removeClass('active-highlight');
                    currentElement.hide();
                    prevElement.show();
                    prevElement.find('.laci-text-highlight').last().addClass('active-highlight');
                    currentExcerpt--;
                }
            } else if (prevElement.length) {
                // If no active highlight in current, switch to the previous excerpt
                currentElement.hide();
                prevElement.show();
                prevElement.find('.laci-text-highlight').last().addClass('active-highlight');
                currentExcerpt--;
            }
            midSection.attr('data-current-excerpt', currentExcerpt);
            updateButtonsForDiff(midSection, currentExcerpt, totalExcerpts);
        });
    },    
    tinymceCustomButton($) {
        const postTitle = jQuery('.laci-placement-assistant-title').data('title');

        tinymce.create('tinymce.plugins.CustomButtonAddLink', {
            init: function(editor, url) {
                editor.addButton('custom_button_add_link', {
                    text: `⥅ Place a link to: ${postTitle}`,
                    classes: 'laci-custom-button-add-link',
                    icon: false,
                    onclick: function() {
                        var editor = tinymce.activeEditor;
                        var selectedText = editor.selection.getContent();

                        const postLinkElement = jQuery('.laci-placement-assistant-title');
                        if (!postLinkElement.length) {
                            alert('Error: Post link element not found.');
                            return;
                        }
                        
                        const postLink = postLinkElement.data('link');
                        if (!postLink) {
                            alert('Error: Post link is not available.');
                            return;
                        }

                        let contentChanged = false;

                        if (selectedText.length > 0) {
                            editor.execCommand('mceInsertLink', false, {
                                href: postLink,
                                text: selectedText,
                               'data-id' : postLinkElement.data('id'),
                               'data-type': 'post'
                            });
                            contentChanged = true;
                        } else {
                            alert("Please select a text before adding a link.");
                        }

                        if (contentChanged) {
                            tinymce.triggerSave();
                        } else {
                            alert('Error: None of the keywords were found in the content.');
                        }

                    }
                });
            }
        });
        
        tinymce.PluginManager.add('custom_button_add_link', tinymce.plugins.CustomButtonAddLink);
    },
    getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    },
    triggerChangeKeyword($) {
        //let keyWord = linkAssistant.getUrlParameter('key_word');
        let keyWord = jQuery('.laci-placement-assistant-title').data('keywords');
        
        if(keyWord) {
            keyWord = keyWord.replace(/,+/g, ',');
            keyWord = keyWord.replace(/^,|,$/g, '');
            jQuery('.laci-search-input-control').val(keyWord).trigger('change');
        }
    },
    handleLoadMore($) {
        $('.laci-load-more-same-category').click(function() {
            let wpilCurrentPage = $(this).data('set-paging') || 1;
            const wpilMaxPages = $(this).data('max-pages');
            const postId = $(this).data('post-id');
            const keyWord = $(this).data('key-word');
            
            jQuery('.laci-loading').show();

            wpilCurrentPage++;
            $(this).attr('data-set-paging', wpilCurrentPage);
    
            if (wpilCurrentPage <= wpilMaxPages) {
                jQuery('.laci-loading').show();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'laci_load_more_post_same_cate',
                        nonce: laci_internal_links.nonce,
                        post_id: postId,
                        key_word: keyWord,
                        page: wpilCurrentPage,
                    },
                    success: function(response) {
                        jQuery('.laci-loading').hide();
                        if (response.success) {
                            $('.laci-search-results__same-category__content').append(response.data.html_same_cate);
                            if (wpilCurrentPage >= wpilMaxPages) {
                                jQuery('.laci-search-results__same-category__load-more').addClass('laci-display-none');
                            }
                        } else {
                            alert(response.data.mess);
                        }
                    }
                });
            } else {
                jQuery('.laci-search-results__same-category__load-more').addClass('laci-display-none');
            }
        });

        $('.laci-load-more-diff-category').click(function() {
            let wpilCurrentPage = $(this).data('set-paging') || 1;
            const wpilMaxPages = $(this).data('max-pages');
            const postId = $(this).data('post-id');
            const keyWord = $(this).data('key-word');
            
            jQuery('.laci-loading').show();

            wpilCurrentPage++;
            $(this).attr('data-set-paging', wpilCurrentPage);
    
            if (wpilCurrentPage <= wpilMaxPages) {
                jQuery('.laci-loading').show();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'laci_load_more_post_diff_cate',
                        nonce: laci_internal_links.nonce,
                        post_id: postId,
                        key_word: keyWord,
                        page: wpilCurrentPage,
                    },
                    success: function(response) {
                        jQuery('.laci-loading').hide();
                        if (response.success) {
                            $('.laci-search-results__diff-category__content').append(response.data.html_diff_cate);
                            if (wpilCurrentPage >= wpilMaxPages) {
                                jQuery('.laci-search-results__diff-category__load-more').addClass('laci-display-none');
                            }
                        } else {
                            alert(response.data.mess);
                        }
                    }
                });
            } else {
                jQuery('.laci-search-results__diff-category__load-more').addClass('laci-display-none');
            }
        });
    },
    showPopupRelatedPost($) {
        function scrollToTarget() {
            const wrapContainer = $('.laci-add-related-box-popup');
            const target = wrapContainer.find('.laci-related-box-container');
        
            if (target.length) {
                wrapContainer.animate({
                    scrollTop: target.position().top + wrapContainer.scrollTop() - wrapContainer.position().top
                }, 500);
            } else {
                alert('Error: Target element not found.');
            }
        }

        jQuery(document).on("click", ".laci-add-related-box", function (e) {
            e.preventDefault();
            const postId = $(this).parents('.laci-container-cat-item').data('id');
            const relatedPostTitle = $('.laci-placement-assistant-title').data('title');
            const keywords = $('.laci-search-input-control').val();
            $(this).addClass('laci-updating-message');
            $(this).attr('disabled', 'disabled');

            jQuery.ajax({
                url: laci_internal_links.ajax_url,
                type: 'POST',
                data: {
                    action: 'laci_get_content_post',
                    nonce: laci_internal_links.nonce,
                    post_id: postId,
                    key_words: keywords,
                    related_post_title: relatedPostTitle
                },
                success: function (response) {
                    if (response.success === true) {
                        $('.laci-add-related-box').removeClass('laci-updating-message');
                        $('.laci-add-related-box').removeAttr('disabled');
                        if (response.success && response.data.html) {
                            let $popup = $(".laci-add-related-box-popup");
                            if ($popup.length === 0) {
                                $popup = $('<div class="laci-add-related-box-popup"></div>').appendTo('body');
                            }
    
                            $popup.html(response.data.html);
    
                            const title = response.data.title;
                            $popup.dialog({
                                title: title,
                                width: '60%',
                                height: 500,
                                modal: true,
                                buttons: {
                                    "Move Up Related Posts": function() {
                                        const div = $('.laci-add-related-box-popup .laci-related-box-container');
                                        const currentGroup = div.closest('.wp-block-group');

                                        // Check if there are any previous siblings within the same wp-block-group
                                        const prev = div.prevAll().filter(function() {
                                            return !($(this).is('p') && $(this).text().trim() === '');
                                        }).first();

                                        // If there's a valid previous sibling, move the container before it
                                        if (prev.length && prev.prop("tagName") !== 'BUTTON') {
                                            div.insertBefore(prev);
                                        } else if (currentGroup.length > 0) {
                                            // Move to the last element of the previous wp-block-group
                                            const prevGroup = currentGroup.prevAll('.wp-block-group').first();
                                            if (prevGroup.length > 0) {
                                                // Find the last child in the previous group, skipping the last DOM element
                                                const lastChildInPrevGroup = prevGroup.children().last();
                                                const secondLastChild = prevGroup.children().eq(-2); // Get the second last child
                                                
                                                // If there's a second last child, move the container after it
                                                if (secondLastChild.length) {
                                                    div.insertAfter(secondLastChild);
                                                } else {
                                                    // If no valid second last child, append it to the previous group
                                                    prevGroup.append(div);
                                                }
                                            }
                                        } else {
                                            const prev = div.prevAll().filter(function() {
                                                return !($(this).is('p') && $(this).text().trim() === '');
                                            }).first();

                                            if (prev.length && prev.prop("tagName") !== 'BUTTON') {
                                                div.insertBefore(prev);
                                            }
                                        }


                                        scrollToTarget();
                                    },
                                    "Move Down Related Posts": function() {
                                        const div = $('.laci-add-related-box-popup .laci-related-box-container');

                                        if (div.closest('.wp-block-group').length > 0) {
                                            // Find the current wp-block-group containing the laci-related-box-container
                                            const currentGroup = div.closest('.wp-block-group');

                                            // Find the next element inside the current wp-block-group, skipping any empty paragraphs
                                            const nextElementInGroup = div.nextAll().filter(function() {
                                                return !($(this).is('p') && $(this).text().trim() === '');
                                            }).first();

                                            // If there's another element in the current group, move the container after it
                                            if (nextElementInGroup.length) {
                                                div.insertAfter(nextElementInGroup);
                                            } else {
                                                // Otherwise, move it to the next wp-block-group
                                                const nextGroup = currentGroup.nextAll('.wp-block-group').first();
                                                if (nextGroup.length) {
                                                    // Skip the first child of the next wp-block-group
                                                    const firstChildInNextGroup = nextGroup.children().first();
                                                    if (firstChildInNextGroup.length) {
                                                        div.insertAfter(firstChildInNextGroup); // Move after the first child
                                                    } else {
                                                        nextGroup.append(div); // If no children, append to the group
                                                    }
                                                }
                                            }
                                        } else {
                                            const next = div.nextAll().filter(function() {
                                                return !($(this).is('p') && $(this).text().trim() === '');
                                            }).first();

                                            if (next.length) {
                                                div.insertAfter(next);
                                            }
                                        }


                                        scrollToTarget();
                                    },
                                    "Place Related Post": function() {
                                        const content = $('.laci-post-content-container').html();
                                        const relatedPostId = $('.laci-placement-assistant-title').data('id');
                                        jQuery.ajax({
                                            url: laci_internal_links.ajax_url,
                                            type: 'POST',
                                            data: {
                                                action: 'laci_save_related_post',
                                                nonce: laci_internal_links.nonce,
                                                post_id: postId,
                                                content: content,
                                                related_post_id: relatedPostId
                                            },
                                            success: function(response) {
                                                if (response.success && response.data) {
                                                   notification.showNotification('Related post added');
                                                } else {
                                                    notification.showNotification('Related post can\'t be added');
                                                }
                                            },
                                            error: function(response) {
                                                notification.showNotification('Related post can\'t be added');
                                            }
                                        });
                                        $(this).dialog("close");
                                        $(this).remove();
                                    },
                                    Close: function() {
                                        $(this).dialog("close");
                                        $(this).remove();
                                    },
                                },
                                open: function() {
                                    $(this).parent().addClass('laci-add-related-box-popup-container');
                                    $(this).parent().find('.ui-dialog-buttonpane button:contains("Move Up")').addClass('laci-move-up-button');
                                    $(this).parent().find('.ui-dialog-buttonpane button:contains("Move Down")').addClass('laci-move-down-button');
                                    $(this).parent().find('.ui-dialog-buttonpane button:contains("Close")').addClass('laci-close-button');
                                    $(this).parent().find('.ui-dialog-buttonpane button:contains("Place Related Post")').addClass('laci-place-post-button');
                                    
                                    setTimeout(function() {
                                        scrollToTarget();
                                    }, 500);
                                }
                            });
                        } else {
                            alert('Not have internal links information.');
                        }
                    }
                },
                error: function(response) {
                    $('.laci-add-related-box').removeClass('laci-updating-message');
                    $('.laci-add-related-box').removeAttr('disabled');
                    alert('Error: Failed to get content post.');
                }
            });

          
        });
    },
    insertMainKeywords($) {
        jQuery(document).on("click", ".laci-insert-main-key-word", function (e) {
            e.preventDefault();
            const postId = jQuery('.laci-placement-assistant-title').data('id');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'laci_insert_main_keywords',
                    nonce: laci_internal_links.nonce,
                    post_id: postId,
                },
                success: function(response) {
                    jQuery('.laci-loading').hide();
                    if (response.success) {
                        jQuery('.laci-search-input-control').val(response.data.key_word).trigger('change');
                    } else {
                        alert(response.data.mess);
                    }
                }
            });
        
        });
    },
}

jQuery(document).ready(function($) {
    linkAssistant.actionChangeKeyword($);
    linkAssistant.createdPopup($);
    linkAssistant.handleShowPopupEdit($);
    linkAssistant.handleSearchKeyWordSameCate($);
    linkAssistant.actionNextAndPreviousContent($);
    linkAssistant.tinymceCustomButton($);
    linkAssistant.triggerChangeKeyword($);
    linkAssistant.handleLoadMore($);
    linkAssistant.showPopupRelatedPost($);
    linkAssistant.insertMainKeywords($);
});