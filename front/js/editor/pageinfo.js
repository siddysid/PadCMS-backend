/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
window.pageInfo = {

    init: function() {
        var context = this;

        colorPickerHandler.init($('.page-color-picker'));

        $('ul.ui-autocomplete').remove();

        $("#page-tag-input").autocomplete({
            minLength: 1,
            source: function(request, response) {
                request.pid = window.pid;
                $.ajax({
                    type: 'POST',
                    url: "/editor/tag-autocomplete",
                    dataType: "json",
                    data: request,
                    success: function( data ) {
                        response( data );
                    }
                });
            }
        });

        $('#show-toc-dialog').click(function(){
            $('#toc-dialog').dialog('option', 'page', window.pid)
                .dialog('open');
        });

        $('#page-pdf-select-btn').click(function(){
          $('#select-pdf-page-dialog').dialog('option', 'page', window.pid);
          pdfPageEditor.load();
        });

        $('#page-tag-btn').click(function() {
            context.onAddTag();
            return false;
        });
        $('#page-pdf-page-btn').click(function(event) {
            context.onSave('pdf_page', $('#page-pdf-page-input').val());
            return false;
        });

        $('#page-title-btn').click(function() {
            context.onSave('title', $('#page-title-input').val(), $('#page-title-input'));
            return false;
        });

        $('#page-machine-name-btn').click(function() {
            context.onSave('machine_name', $('#page-machine-name-input').val(), $('#page-machine-name-input'));
            return false;
        });

        $('#page-color-btn').click(function() {
            context.onSave('color', $('#page-color-input').val());
            return false;
        });

        context.bindDeleteTag();
    },

    bindDeleteTag: function() {
        var context = this;
        $('a', '#page-tag-list').bind('click', context, function(event) {
            event.data.onDeleteTag(event.originalEvent);
            return false;
        });
    },

    onAddTag: function() {
        var context = this;
        var tag = $('#page-tag-input').val().trim();
        if (!tag) return;
        $.ajax({
            url: '/editor/add-tag',
            type: 'POST',
            dataType: 'json',
            data: {
                page: window.pid,
                tag: tag
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        context.updateTags(data.tags);
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
                $('#page-tag-input').val('');
            }
        });
    },

    onSave: function(key, value, target) {
        if (!key)
            return false;

        var _value = value;

        $.ajax({
            url: '/editor/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page: window.pid,
                key: key,
                value: _value
            },
            success: function(data) {
                try {
                    if (!data.status) {
                        alert(data.message);
                    } else {
                        if (typeof target !== 'undefined' && data.value) {
                            _value = data.value;
                            target.val(_value);
                        }
                        if (key == 'title') {
                            $('#page-' + window.pid).parent().parent().parent()
                                    .find('span.page-name')
                                    .html('<b>' + translate('title') + '</b>: ' + _value);
                        }
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
    },

    onDeleteTag: function(event) {
        var context = this;
        var tid = $(event.currentTarget).parent().attr('id').split('-').pop();
        if (!tid) return;
        $.ajax({
            url: '/editor/delete-tag',
            type: 'POST',
            dataType: 'json',
            data: {
                pid: window.pid,
                tid: tid
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $('#tag-' + data.tag, '#page-tag-list').remove();
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
    },

    updateTags: function(tags) {
        var context = this;

        tagsList = $('#page-tag-list').empty();
        $.each(tags, function(index, value) {
            tagsList.append('<span id="tag-' + value.id + '" class="tag level-one">' + value.title + '<a href="/editor/delete-tag/id/' + value.id + '"></a></span>');
        });
        context.bindDeleteTag();
    },

    onSaveToc: function() {
        context = this;
        context.onSave('toc', $('#page-toc-select').val());
    }

};
