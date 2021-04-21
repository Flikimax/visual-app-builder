jQuery(document).ready(function() {
    // ICONO
    jQuery('.visual-app-builder-icons a').click(function(e){ // TODO: REVISIR LA CONSTANTE DE PHP KM_TEXT_DOMAIN
        console.log('click');
        var a = jQuery(this),
            href= a.attr('href'),
            arr = a.find('.arr');
            jQuery(href).toggle();
        arr.toggleClass('dashicons-arrow-down').toggleClass('dashicons-arrow-up');
        a.toggleClass('open');
        e.preventDefault();
    });
    jQuery('#select input').on('change', function(e){
        var def = jQuery('.def'),
            icon = this.value,
            span = jQuery('<span class="dashicons dashicons-'+icon+'"></span>');

            console.log(icon);

        def.html(span);
        jQuery('.visual-app-builder-icons a').click(); // REVISIR LA CONSTANTE DE PHP FKM_TEXT_DOMAIN
    });

    // SHOW ADMIN
    jQuery("#admin_page_permissions").change(function(){
        show_administrator();
    });
    function show_administrator(){
        var value = jQuery('#admin_page_permissions').children("option:selected").val();
        var checked = null;
        if (jQuery('#admin_page_hide_admin').is(':checked')) {
            checked = true;
        }
        if (value != 0){
            jQuery('#admin_page_hide_admin').attr('checked', false);
            jQuery('.term-admin_page_hide_admin-wrap').hide();

            var permissions = jQuery('#admin_page_permissions').children("option:selected").val();
            if (permissions == 'fkm_' + jQuery('#slug').val()){
                jQuery('.term-admin_page_hide_admin-wrap').show();
                if (checked){
                    jQuery('#admin_page_hide_admin').attr('checked', true);
                }
            }
        } else {
            jQuery('.term-admin_page_hide_admin-wrap').show();
            jQuery('#admin_page_hide_admin').attr('checked', false);
        }
    }
    show_administrator();

    // ADD VAR TO DELETE LINK
    if (jQuery('#edittag .edit-tag-actions #delete-link a.delete')[0]){
        var link = jQuery('#edittag .edit-tag-actions #delete-link a.delete').attr("href");
        var slug = jQuery('#edittag input#slug').val();
        link += '&slug=' + slug;

        jQuery('#edittag .edit-tag-actions #delete-link a.delete').attr("href", link);
    }

    // ADD VAR TO DELETE LINK
    if (jQuery('#edittag .edit-tag-actions #delete-link a.delete')[0]){
        var link = jQuery('#edittag .edit-tag-actions #delete-link a.delete').attr("href");
        var slug = jQuery('#edittag input#slug').val();
        link += '&slug=' + slug;
        // jQuery('#edittag .edit-tag-actions #delete-link a.delete').attr("href", link);
    }
    // LINK EDIT = DELETE
    if (jQuery('#posts-filter .row-actions .delete > a')[0]){
        var items = jQuery('#posts-filter tbody tr');
        
        jQuery.map(items, function(item){
            var link = jQuery('.row-actions .edit > a', item).attr("href");
            // var slug = jQuery('td.slug.column-slug', item).text();
            // link += '&slug=' + slug;
            jQuery('.row-actions .delete > a', item).removeClass("delete-tag");
            jQuery('.row-actions .delete > a', item).attr("href", link);
        });
    }

    
    jQuery('form#addtag #tag-name').on('input', function(){
        // LIMITAR CARACTERES INPUT NAME
        this.value = this.value.replace(/[^a-zA-Z0-9Á-ú_ ]/g,'');
        // NO REPETIR ADMIN PAGE NAME - MEDIANTE SLUG
        var current_role = this.value.replace(/ /g,'-').toLowerCase();

        var admin_pages = jQuery('form#posts-filter tbody tr');
        var array_admin_pages = jQuery.map(admin_pages, function(admin_page){
            return jQuery('.slug.column-slug', admin_page).text();
        });

        if (jQuery.inArray(current_role, array_admin_pages) >= 0){
            jQuery('form#addtag #submit').prop('disabled', true);
        } else {
            jQuery('form#addtag #submit').prop('disabled', false);
        }
    });

    // FORM EDIT
    if (jQuery('form#edittag')[0]){
        // NAME
        var name = jQuery('form#edittag input#name').val();
        jQuery('form#edittag .term-name-wrap label').text(name);
        jQuery('form#edittag input#name').remove();
        jQuery('form#edittag .term-name-wrap p').remove();

        // PARENT CATEGORY
        jQuery('form#edittag .term-parent-wrap label').text('Parent menu page');
        jQuery('form#edittag .term-parent-wrap p').text('Assign a parent menu page to create a hierarchy.');
    }
    // FORM ADD
    if (jQuery('form#addtag')[0]){
        // NAME
        jQuery('form#addtag .term-name-wrap p').html('Name of the menu page.<br/>Warning: You will not be able to edit the name afterwards.');
        // PARENT CATEGORY
        jQuery('form#addtag .term-parent-wrap label').text('Parent menu page');
        jQuery('form#addtag .term-parent-wrap p').text('Assign a parent menu page to create a hierarchy.');
    }


});

