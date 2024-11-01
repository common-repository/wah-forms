class WAHForms_Builder {
    constructor() {
        this.name = 'WAHForms_Builder';
        this.body = jQuery('body');
        this.init_elements();
        this.event_handler();
        this.init_sortable_items();
        this.init_admin_tabs();
        this.validate_required_fields();
        this.validate_email_body_tags();
    }

    init_elements() {
        this.body = jQuery('body');
        this.required_field = jQuery('.wahforms-builder-form-fields li.draggable-item input[required]');
    }

    init_admin_tabs() {
        var _this = this;
        jQuery('#wahforms-admin-tabs').tabs({
            activate: function( event, ui ) {
                var index = ui.newTab.index();
                _this.admin_set_cookie('active_wahforms_tab', index, 3);
            }
        });
        if( typeof _this.admin_get_cookie('active_wahforms_tab') !='undefined' && _this.admin_get_cookie('active_wahforms_tab') ) {
            jQuery('#wahforms-admin-tabs').tabs({ active: _this.admin_get_cookie('active_wahforms_tab') });
        }
    }

    validate_email_body_tags() {
        var email_body_tags = [];
        jQuery('.wahforms-email-body-tags-list li code').each( function(){
            email_body_tags.push(
                {
                    index : jQuery(this).parents('li').attr('data-index'),
                    html  : jQuery(this).html()
                }
            );
        });

        var email_body_content = jQuery('textarea[name="wahform_fields[settings][email_body]"]').val();
        if( email_body_tags && ! email_body_content ) {
            // jQuery('#publish').prop('disabled', true);
            // jQuery('#submit-wahforms').prop('disabled', true);
            jQuery('li[aria-controls="wahforms-tab-3"] a').addClass('missing');
        }
        if( email_body_tags ) {
            jQuery.each( email_body_tags, function(key, tag){
                if( email_body_content.indexOf(tag.html) === -1 ) {
                    jQuery('.wahforms-email-body-tags-list li[data-index="'+tag.index+'"]').addClass('missing');
                    // jQuery('#publish').prop('disabled', true);
                    // jQuery('#submit-wahforms').prop('disabled', true);
                    jQuery('li[aria-controls="wahforms-tab-3"] a').addClass('missing');

                } else {
                    jQuery('.wahforms-email-body-tags-list li[data-index="'+tag.index+'"]').removeClass('missing');
                    // jQuery('#publish').prop('disabled', false);
                    // jQuery('#submit-wahforms').prop('disabled', false);
                    jQuery('li[aria-controls="wahforms-tab-3"] a').removeClass('missing');
                }
            });
        }
    }

    validate_required_fields() {
        this.required_field.each( function(){
            if( jQuery(this).val() == ' ' || ! jQuery(this).val() ) {
                jQuery(this).addClass('error-field');
            } else {
                jQuery(this).removeClass('error-field');
            }
        });
        this.required_field.on('blur', function(){
            jQuery(this).val( jQuery(this).val().replace(' ', '_') ); // remove spaces
        });
        if( jQuery('.wahforms-builder-form-fields li.draggable-item input[required].error-field').length ) {
            jQuery('input#publish').prop('disabled', true);
            if( ! jQuery('#major-publishing-actions').find('.wahforms-publish-error').length ) {
                jQuery('#major-publishing-actions').append('<div class="wahforms-publish-error"><strong>WAHForms</strong>: "Please fill all required fields"</div>');
            }
        } else {
            jQuery('#major-publishing-actions .wahforms-publish-error').remove();
            jQuery('input#publish').prop('disabled', false);
        }
    }

    event_handler() {
        var _this = this;
        this.body.on( 'click', '.wahforms-builder-item .add-item', this.add_field.bind(this) );
        this.body.on( 'click', '.wahforms-builder-item .delete-this-item button', this.delete_field.bind(this) );
        this.body.on( 'click', 'input.copy-wahforms-shortcode', this.show_copy_message.bind(this) );
        this.body.on( 'keyup', '.wahforms-builder-form-fields li.draggable-item input[required]', this.validate_required_fields.bind(this) );
        this.body.on( 'click', '.show-all-leads', this.load_leads_by_form_id.bind(this) );
        this.body.on( 'click', '.load-more-leads', this.leads_load_more.bind(this) );
        this.body.on( 'click', '.lead-details', this.show_lead_item.bind(this) );
        this.body.on( 'click', '.wahforms-email-body-tags-list li code', this.select_copy_email_tag.bind(this) );
        this.body.on( 'click', 'button.add-radio-option', this.add_radio_option.bind(this) );
        this.body.on( 'click', 'button.remove-radio-option', this.delete_radio_option.bind(this) );
        this.body.on( 'click', 'button.add-selectbox-option', this.add_selectbox_option.bind(this) );
        this.body.on( 'click', 'button.remove-selectbox-option', this.delete_selectbox_option.bind(this) );
        this.body.on( 'click', '.additional-links a.button', this.additional_tags_toggle.bind(this) );

        // Handle keyup event on label fields
        jQuery('input[name$="[label]"]').on('keyup', function(e){
            var input_value = jQuery(this).val();
            var input_name = jQuery(this).parents('.item-fields').find('input[name$="[input_name]"]').val( input_value.replace(/ /g,'_').toLowerCase() );
        });
        // Email body change
        jQuery('textarea[name="wahform_fields[settings][email_body]"]').on('blur', function(e){
            _this.validate_email_body_tags();
        });
    }

    additional_tags_toggle(e){
        e.preventDefault();
        jQuery(e.currentTarget).parents('.additional-links').find('.additional-links-content').toggle();
    }

    show_copy_message(e){
        e.preventDefault();
        var _this = jQuery(e.currentTarget);
        _this.select();
        document.execCommand("copy");
        _this.parents('.settings-item').append('<div class="copy-message">Copied!</div>');

        setTimeout( function(){
            jQuery('.copy-message').fadeOut(200, function(){
                jQuery(this).remove();
            });
        }, 1500);
    }

    select_copy_email_tag(e){
        var email_tag_item   = jQuery(e.currentTarget);
        var copyText         = e.currentTarget.textContent;
        var textArea         = document.createElement('textarea');
        textArea.className   = "select_copy_email_tag_textarea";
        textArea.textContent = copyText;
        document.body.append(textArea);
        textArea.select();
        document.execCommand("copy");
        jQuery('.select_copy_email_tag_textarea').remove();
        email_tag_item.parents('li').append('<div class="copy-message">Copied!</div>');
        setTimeout( function(){
            jQuery('.copy-message').fadeOut(200, function(){
                jQuery(this).remove();
            });
        }, 1000);
    }

    recalculate_items_index() {
        var counter = 0;
        if( jQuery( ".wahforms-builder-form-fields-inner ul li" ).length ) {
            jQuery( ".wahforms-builder-form-fields-inner ul li" ).each( function(){
                counter++;
                jQuery(this).attr('data-index', counter );
            });
        }
    }

    init_sortable_items() {
        var _this = this;
        jQuery( ".wahforms-builder-form-fields-inner ul#sortable-parent" ).sortable({
            revert: false,
            placeholder: "sortable-placeholder",
            forcePlaceholderSize: true,
            helper: "original",
            stop: function( event, ui ) {
                //_this.recalculate_items_index();
            },

        });
        jQuery( ".wahforms-builder-sidebar .draggable-item" ).draggable({
            cursor            : "move",
            connectToSortable : ".wahforms-builder-form-fields-inner ul#sortable-parent",
            helper            : "clone",
            revert            : false,
            stop : function( event, ui ) {
                var draggable_item = jQuery(ui.helper[0]);
                draggable_item.find('.add-item span').addClass('dashicons-arrow-down-alt2').removeClass('dashicons-menu');
                draggable_item.find('.add-item').prop('disabled', false);
                draggable_item.attr('data-index', _this.get_bigger_index()+1 );
            },
        });
        //jQuery( "ul#sortable-parent > li" ).disableSelection();

        jQuery("ul.is-repeater.is-radio-options").sortable({
            items      : "li",
            placeholder: "sortable-placeholder",
        });
        //jQuery("ul.is-repeater.is-radio-options").disableSelection();
        jQuery("ul.is-repeater.is-radio-options").on("sortstop", function(event, ui) {
            console.log('sortstop children');
            if( jQuery(ui.item).find('button[data-option-key]').length ) {
                console.log( jQuery(ui.item).find('button[data-option-key]').attr('data-option-key') );
            }
        });
    }

    get_bigger_index() {
        var index = null;
        if( jQuery('.wahforms-builder-form-fields ul li.draggable-item').length ) {
            jQuery('.wahforms-builder-form-fields ul li.draggable-item').each( function(){
                var this_index = parseFloat(jQuery(this).attr('data-index'));
                index = (this_index > index) ? this_index : index;
            });
        }
        return index;
    }
    // Add field
    add_field(e){
        e.preventDefault();
        var _this = jQuery(e.currentTarget);
        if( ! _this.parents('.wahforms-builder-item').find('.wahforms-builder-item-options').html() ) {
            var field_type  = _this.parents('.wahforms-builder-item').attr('data-field');
            var field_index = _this.parents('.draggable-item').attr('data-index');
            _this.parents('.draggable-item').attr('data-index', field_index );
            this.load_field_options( field_type, field_index );
        } else {
            if( _this.hasClass('opened') ) {
                console.log('opened');
                _this.parents('.wahforms-builder-item').find('.wahforms-builder-item-options').toggle();
                _this.find('.dashicons').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
                _this.removeClass('opened');
                _this.addClass('closed');
            } else if ( _this.hasClass('closed') ) {
                console.log('closed');
                _this.parents('.wahforms-builder-item').find('.wahforms-builder-item-options').toggle();
                _this.find('.dashicons').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
                _this.removeClass('closed');
                _this.addClass('opened');
            } else {
                console.log('item options already loaded');
            }

        }
    }
    // Load field options
    load_field_options( field_type, field_index ) {

        var _this = this;

        _this.show_field_loader(field_index);

        jQuery.ajax({
            type     : "post",
            dataType : "json",
            url      : ajaxurl,
            data : {
                action      : "wahforms_get_item_options",
                nonce       : wahforms_vars.nonce,
                field_type  : field_type,
                field_index : field_index
            },
            success: function(response) {
                jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] .wahforms-builder-item .wahforms-builder-item-options').html( response );

                _this.hide_field_loader( field_index );

                // Handle click and add radio option
                jQuery('button.add-radio-option').on('click', function(e){
                    _this.add_radio_option( jQuery(this).attr('data-field-index') );
                });
                // Handle keyup event on label fields
                jQuery('input[name$="[label]"]').on('keyup', function(e){
                    var input_value = jQuery(this).val();
                    var input_name = jQuery(this).parents('.item-fields').find('input[name$="[input_name]"]').val( input_value.replace(/ /g,'_').toLowerCase() );
                });
            }
        });
    }
    // Add selectbox option
    add_selectbox_option(e){
        var button      = jQuery(e.currentTarget);
        var field_index = button.attr('data-field-index');
        var option_key  = button.attr('data-option-key');
        button.find('span.dashicons').addClass('loading');
        jQuery.ajax({
            type     : "post",
            dataType : "json",
            url      : ajaxurl,
            data : {
                action      : "wahforms_add_selectbox_option",
                nonce       : wahforms_vars.nonce,
                field_index : field_index,
                option_key : option_key,
            },
            success: function(response) {
                if( response ){
                    jQuery(response).insertAfter( button.parents('label').parent() );
                    //button.parents('.item-group').append(response);
                    button.find('span.dashicons').removeClass('loading');
                }
            }
        });
    }
    // Add radio option
    add_radio_option(e){
        var button      = jQuery(e.currentTarget);
        var field_index = button.attr('data-field-index');
        button.find('span.dashicons').addClass('loading');
        jQuery.ajax({
            type     : "post",
            dataType : "json",
            url      : ajaxurl,
            data : {
                action      : "wahforms_​add_radio_option",
                nonce       : wahforms_vars.nonce,
                field_index : field_index,
            },
            success: function(response) {
                if( response ){
                    jQuery(response).insertAfter( button.parents('label').parent() );
                    //button.parents('.item-group').append(response);
                    button.find('span.dashicons').removeClass('loading');
                }
            }
        });
    }
    // Delete radio option
    delete_radio_option(e){
        var button      = jQuery(e.currentTarget);
        button.parents('label').fadeOut(200, function(){
            jQuery(this).remove();
        });
    }
    // Delete selectbox option
    delete_selectbox_option(e){
        var button      = jQuery(e.currentTarget);
        button.parents('label').parent().fadeOut(200, function(){
            jQuery(this).remove();
        });
    }
    // Show leads loader
    show_leads_loader() {
        jQuery('.wahforms-leads-container').addClass('loading');
    }
    // Hide leads loader
    hide_leads_loader() {
        jQuery('.wahforms-leads-container').removeClass('loading');
    }
    // Load leads by form id
    load_leads_by_form_id( e ) {
        var _this = this;
        var form_id = jQuery( e.currentTarget ).attr('data-wahformid');
        var paged = jQuery( e.currentTarget ).attr('data-paged');
        if( ! jQuery('.wahforms-leads-wrapper').html() ) {
            if( form_id ) {
                _this.show_leads_loader();

                jQuery.ajax({
                    type     : "post",
                    dataType : "json",
                    url      : ajaxurl,
                    data : {
                        action  : "wahforms_load_leads_by_form_id",
                        nonce   : wahforms_vars.nonce,
                        form_id : form_id,
                        paged   : paged
                    },
                    success: function(response) {
                        //_this.hide_leads_loader( field_index )
                        if( response.html){
                            jQuery('.wahforms-leads-wrapper').html( response.html );
                            jQuery('.show-all-leads').prop('disabled', true);
                            if( response.load_more ) {
                                jQuery('.wahforms-leads-load-more').fadeIn(250);
                                jQuery('.wahforms-leads-load-more button.load-more-leads').attr('data-paged', response.paged );
                            } else {
                                jQuery('.wahforms-leads-load-more').fadeOut(250, function(){
                                    jQuery(this).remove();
                                });
                            }
                        }

                        setTimeout( function(){
                            _this.hide_leads_loader();
                        }, 350);
                    }
                });
            }
        }
    }
    // Leads load more
    leads_load_more(e){
        e.preventDefault();
        var _this = this;
        var button = jQuery(e.currentTarget);
        var form_id = button.attr('data-wahformid');
        var paged = button.attr('data-paged');
        _this.show_leads_loader();
        jQuery.ajax({
            type     : "post",
            dataType : "json",
            url      : ajaxurl,
            data : {
                action  : "wahforms_load_leads_by_form_id",
                nonce   : wahforms_vars.nonce,
                form_id : form_id,
                paged   : paged
            },
            success: function(response) {
                if( response.html){
                    jQuery('.wahforms-leads-wrapper .wahforms-lead-item ol').append( response.html );
                    if( response.load_more ) {
                        jQuery('.wahforms-leads-load-more').fadeIn(250);
                        jQuery('.wahforms-leads-load-more button.load-more-leads').attr('data-paged', response.paged );
                    } else {
                        jQuery('.wahforms-leads-load-more').fadeOut(250, function(){
                            jQuery(this).remove();
                        });
                    }
                }

                setTimeout( function(){
                    _this.hide_leads_loader();
                }, 350);
            }
        });
    }
    // Delete Field
    delete_field( field_index ) {
        console.log( field_index );
        var index;
        if( typeof field_index.currentTarget !='undefined' && field_index.currentTarget ) {
            index = jQuery(field_index.currentTarget).parents('li.draggable-item').attr('data-index');
            jQuery(field_index.currentTarget).parents('li.draggable-item').fadeOut( 250, function(){
                jQuery(this).remove();
            });
            if( index ) {
                // remove index item email body tag.
                jQuery('.wahforms-email-body-tags-list li[data-index="'+index+'"]').remove();
            }
        } else {
            jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"]').fadeOut( 250, function(){
                jQuery(this).remove();
            });
        }
    }

    show_field_loader(field_index){
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item').prop('disabled', true );
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item span').addClass( 'loading' );
    }

    hide_field_loader(field_index){
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item').prop('disabled', false );
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item span').removeClass( 'loading' );
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item span').removeClass( 'dashicons-arrow-down-alt2' );
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item span').addClass( 'dashicons-arrow-up-alt2' );
        jQuery('.wahforms-builder-form-fields-inner li.draggable-item[data-index="'+field_index+'"] button.add-item').addClass('opened');
    }
    // Admin set cookies
    admin_set_cookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    // Admin get cookies
    admin_get_cookie(cname) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    // Show lead item
    show_lead_item(e) {
        var _this = this;
        var button = jQuery(e.currentTarget);
        button.parents('.lead-item-preview').find('.list-inner').slideToggle();
    }
}

new WAHForms_Builder();
