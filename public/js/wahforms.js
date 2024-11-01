class WAHForms {
    constructor() {
        this.name = 'WAHForms';
        this.version = '1.0';
        this.init_elements();
        this.event_handler();

        console.log(this.name + ' init, version: ' + this.version );
    }

    init_elements() {
        this.body          = jQuery('body');
        this.forms         = jQuery('.wahforms-wrapper .wahforms-form');
    }

    event_handler() {
        this.forms.on( 'submit', this.submit_form.bind( jQuery(this) ) );
        if( wahforms_vars.validate_before_submit ){
            this.forms.find('input, select, textarea').on('blur', this.field_required_error.bind( jQuery(this) ) );
        }
        jQuery('.wahforms-form input[type="file"]').on('change', this.input_file_validation.bind( jQuery(this) ) );
    }

    input_file_validation(e) {
        e.preventDefault();
        var file_errors = [];

        var input_field = jQuery( e.currentTarget );
        var accepted_types = input_field.attr('accept');
        var file_size_limit = input_field.attr('data-sl');
        if( accepted_types ) {
            accepted_types = accepted_types.split(',');
        }

        var file_data = input_field[0].files[0];
        if( typeof file_data !='undefined' ){
            var file_name = file_data.name;
            var file_type = file_data.type;
            var file_ext = '.'+file_data.name.split('.').pop().toLowerCase();
            var file_size = file_data.size;
        }

        // console.log('name: ', file_name );
        // console.log('size: ', file_size );
        // console.log('type: ', file_type );
        // console.log('file_ext: ', file_ext );
        // console.log('accepted_types: ', accepted_types);
        // console.log( jQuery.inArray(file_ext, accepted_types) );

        if( typeof file_size_limit !='undefined' && file_size_limit < file_size ){
            file_errors.push({message_limit:wahforms_vars.submit_errors['message_limit']});
        }
        if( typeof accepted_types !='undefined' && jQuery.inArray(file_ext, accepted_types) == -1 ) {
            file_errors.push({message_type:wahforms_vars.submit_errors['message_type']});
        }

        if( file_errors.length ){
            jQuery.each( file_errors, function(key,item){
                if( typeof item.message_limit !='undefined' ){
                    if( ! input_field.parents('.wf-controls').find('.message_limit').length ){
                        input_field.parents('.wf-controls').append('<p class="error-message message_limit">'+item.message_limit+'</p>');
                    }
                }
                if( typeof item.message_type !='undefined' ){
                    if( ! input_field.parents('.wf-controls').find('.message_type').length ){
                        input_field.parents('.wf-controls').append('<p class="error-message message_type">'+item.message_type+'</p>');
                    }
                }
            });
        }
    }

    field_required_error(e){
        e.preventDefault();
        var input_field = jQuery( e.currentTarget );
        if( ( input_field.prop('required') && input_field.val() == '' ) || ( input_field.prop('required') && input_field.val() == ' ' ) ){
            input_field.addClass('wahforms-required-field');
            input_field.attr('aria-invalid', 'true');
            if( ! input_field.parents('.wf-controls').find( '.required-message' ).length ){
                input_field.parents('.wf-controls').append( wahforms_vars.presubmit_errors.required );
            }
        } else {
            input_field.removeClass('wahforms-required-field');
            input_field.removeAttr('aria-invalid');
            input_field.parents('.wf-controls').find( '.required-message' ).fadeOut( 250, function(){
                jQuery(this).remove();
            });
        }
    }

    submit_form(e) {
        e.preventDefault();
        var _this = this;
        var wahform     = jQuery( e.currentTarget );
        wahform.addClass('loading');
        var current_post_id = wahform.find('input[name="post_id"]').val();
        var upload = false;
        if( typeof wahform.attr('enctype') !=='undefined' && wahform.attr('enctype') === 'multipart/form-data' ) {
            upload = true;
        }

        if( ! upload ) {
            jQuery.ajax({
                type     : "post",
                dataType : "json",
                url      : wahforms_vars.ajax,
                data : {
                    action  : "wahforms_public_submit_form",
                    nonce   : wahforms_vars.nonce,
                    plugin  : _this.name,
                    version : _this.version,
                    form    : wahform.serialize(),
                    post_id : current_post_id
                },
                success: function(response) {
                    wahform.find('.wahforms-ajax-response').html(response.message);
                    if( response.success ) {
                        // reset inputs
                        wahform.find('input, textarea').val('');
                        wahform.find('input[type="checkbox"]').prop('checked', false);
                        wahform.find('input[type="radio"]').prop('checked', false);
                    }
                    setTimeout( function(){
                        wahform.removeClass('loading');
                    }, 250);
                }
            });
        } else if( upload ) {
            var form_data = new FormData();
            var i = 0;
            wahform.find('input[type="file"]').each( function(){
                var file_item = jQuery(this).prop('files')[0];
                form_data.append( 'file[]', file_item );
                i++;
            });

            form_data.append('post_id', current_post_id);
            form_data.append('form', wahform.serialize());
            form_data.append('nonce', wahforms_vars.nonce );
            form_data.append('action', 'wahforms_public_submit_form');

            jQuery.ajax({
                type     : "post",
                dataType : "json",
                url      : wahforms_vars.ajax,
                contentType: false,
                processData: false,
                data : form_data,
                success: function(response) {
                    wahform.find('.wahforms-ajax-response').html(response.message);
                    if( typeof response.file_type_error !='undefined' && response.file_type_error ) {
                        wahform.find('.wahforms-ajax-response').append('<span class="wahforms-ajax-error">' + response.file_type_error + '</span>');
                    }
                    if( typeof response.lead_error !='undefined' && response.lead_error ) {
                        wahform.find('.wahforms-ajax-response').append('<span class="wahforms-ajax-error">' + response.lead_error + '</span>');
                    }
                    if( response.success ) {
                        // reset inputs
                        wahform.find('input, textarea').val('');
                    }
                    setTimeout( function(){
                        wahform.removeClass('loading');
                    }, 250);
                }
            });
        }

    }

}
new WAHForms();
