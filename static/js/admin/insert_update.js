/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
define(
    [
        'jquery-nos',
        'jquery-ui.sortable',
        'jquery-ui.resizable'
    ],
    function($) {
        "use strict";
        return function(id, options, is_new, is_expert) {

            var $container = $(id);

            var $preview_container = $container.find('.preview_container');
            var $fields_container = $container.find('.fields_container');
            var $layout = $container.closest('form').find('[name=form_layout]');
            var $submit_informations = $container.find('.submit_informations');
            $fields_container.show();
            $submit_informations.show();

            // This object will be use to generate preview
            var $clone_preview = $container.find('[data-id=clone_preview]').clone().removeAttr('data-id');
            $container.find('[data-id=clone_preview]').remove();

            var $blank_slate = $container.find('.field_blank_slate');

            $blank_slate.find('label').hover(function() {
                $(this).addClass('ui-state-hover');
            }, function() {
                $(this).removeClass('ui-state-hover');
            });

            var col_size = Math.round($preview_container.outerWidth() / 4);
            $preview_container.width($preview_container.outerWidth() - $preview_container.width());

            // Fill in the hidden field form_layout upon save
            $container.closest('form').on('submit', function computeLayout(e) {
                var layout = '';
                $container.find('tr.preview_row').each(function(i) {
                    var $preview = $(this).find('td.preview');
                    if ($preview.length > 0 && layout != '') {
                        layout += "\n";
                    }
                    $preview.each(function(j) {
                        var $preview = $(this);
                        var $field = $preview.data('field');
                        if (!$field) {
                            return;
                        }
                        j == 0 || (layout += ',');
                        layout += find_field($field, 'field_id').val() + '=' + get_cell_colspan($preview);
                    });
                });
                $layout.val(layout);
            });

            // Add a field
            $container.on('click', '[data-id=add]', function onAdd(e) {
                e.preventDefault();
                var params_button = $(this).data('params');
                $blank_slate.data('params', params_button);
                if (params_button.type == 'page_break') {
                    add_fields_blank_slate(e, 'page_break');
                } else {
                    $(this).closest('p')[params_button.where == 'top' ? 'after' : 'before']($blank_slate);
                    $blank_slate.show();
                    set_field_padding();
                }
            });

            function add_fields_blank_slate(e, type) {
                e && e.preventDefault();
                var params = {
                    where: ($blank_slate.data('params') || {}).where || 'bottom',
                    type:  type || $(this).data('meta')
                };

                var nos_fixed_content = $container.closest('.nos-fixed-content').get(0);
                var old_scroll_top = nos_fixed_content.scrollTop;
                $.ajax({
                    url: 'admin/noviusos_form/form/form_field_meta/' + params.type,
                    dataType: 'json',
                    success: function(json) {
                        $blank_slate.hide();
                        var $fields = $(json.fields).filter(function() {
                            return this.nodeType != 3; // 3 == Node.TEXT_NODE
                        });
                        if (params.where == 'top') {
                            $fields = $($fields.get().reverse());
                        }
                        var $previews = $(); // $([]) in jQuery < 1.4
                        $fields_container.append($fields);
                        $fields = $fields.not('script');
                        $fields.nosFormUI();
                        $fields.each(function() {
                            var $field = $(this);
                            on_field_added($field, params);
                            $field.hide();
                            $previews = $previews.add($field.data('preview'));
                        });
                        apply_layout(json.layout);
                        init_all();
                        nos_fixed_content.scrollTop = old_scroll_top;
                        $previews.addClass('ui-state-hover');
                        setTimeout(function() {
                            $previews.removeClass('ui-state-hover');
                        }, 500);
                    }
                });
            }

            // Add a new field when clicking the "Add" button, either at top or bottom
            $blank_slate.on('click', 'label', add_fields_blank_slate);

            function on_field_added($field, params) {
                var $type = find_field($field, 'field_type');
                if ($type.length == 0) {
                    // Submit informations
                    return;
                }

                // The clone will be wrapped into a <tr class="preview_row">
                var $preview = get_preview($field);
                $preview_container[params.where == 'top' ? 'prepend' : 'append']($preview.parent());
                $type.trigger('change');
            }

            function get_preview($field) {

                var $preview = $field.data('preview');
                if ($preview) {
                    return $preview;
                }

                // Generate a new preview
                var $clone = $clone_preview.clone();
                $preview = $clone.find('td.preview');
                set_cell_colspan($preview, 4);

                $field.data('preview', $preview);
                $preview.data('field', $field);

                $preview.find('button.notransform').removeClass('notransform');
                $preview.nosFormUI().show().nosOnShow();
                $preview.find('input, select').on('click', function(e) {
                    e.preventDefault();
                });

                return $preview;
            }

            function on_focus_preview(preview) {
                var $preview = $(preview);
                var $field = $preview.data('field');

                // Make the preview look "active"
                $preview_container.find('td.preview').removeClass('ui-state-active');
                $preview.addClass('ui-state-active');

                // Show the appropriate field and position it
                show_field($field);

                find_field($field, 'field_label').focus();
            }

            function show_field($field) {
                $fields_container.find('.show_hide').show();
                if ($field.is('.field_enclosure') && !$field.is('.page_break')) {
                    $field.show();
                    $field.nosOnShow();
                }
                $field.siblings('.field_enclosure').hide();
                set_field_padding();
                var $submit_label = find_field($field, 'field_submit_label');
                if ($submit_label.length == 0) {
                    $submit_informations.removeClass('ui-state-active');
                }
            }

            function set_field_padding($focus) {

                $focus = $focus || $preview_container.find('.preview.ui-state-active');
                if ($focus.length == 0) {
                    $focus = $submit_informations.not(':not(.ui-state-active)');
                }
                if ($focus.length > 0) {
                    var diff = $focus.is('.submit_informations') ? 14 : -40;
                    var pos = $focus.position();
                    $fields_container.css({
                        paddingTop: Math.max(0, pos.top + diff) + 'px' // 29 = arrow height
                    });
                }
            }

            $preview_container.on('click', 'td.preview', function onClickPreview(e) {
                e.preventDefault();
                on_focus_preview(this);
            });

            // Duplicate a field
            $preview_container.on('click', '[data-id=copy]', function onClickCopy(e) {
                e.preventDefault();
                // Don't bubble to .preview container
                e.stopPropagation();
            });

            // Delete a preview + field
            function delete_preview() {
                var $preview = $(this);
                var $field = $preview.data('field');
                $field.remove();
                hide_field();
                $preview.addClass('ui-state-error').hide(500, function() {
                    $preview.remove();
                    init_all();
                });
            }

            // Delete listener
            $fields_container.on('click', '[data-id=delete]', function on_delete(e) {
                e.preventDefault();
                // Don't bubble to .preview container
                e.stopPropagation();
                if (confirm($.nosCleanupTranslation(options.textDelete))) {
                    delete_preview.call($preview_container.find('.preview.ui-state-active'));
                }
            });

            function show_when($field, name, show) {
                find_field($field, name).closest('p')[show ? 'show' : 'hide']()
            }

            // When the "field_type" changes
            $fields_container.on('change', 'select[name*="[field_type]"]', function on_type_change(e) {
                var type = $(this).val();
                var $field = $(this).closest('.field_enclosure');
                var $inject_origin_after = null;

                if (is_expert && -1 !== $.inArray(type, ['text', 'email', 'number', 'textarea'])) {
                    $inject_origin_after = find_field($field, 'field_default_value').closest('p');
                } else if (-1 !== $.inArray(type, ['hidden', 'variable'])) {
                    $inject_origin_after = find_field($field, 'field_type').closest('p');
                }

                if ($inject_origin_after !== null) {
                    $inject_origin_after.after(find_field($field, 'field_origin_var').closest('p'));
                    $inject_origin_after.after(find_field($field, 'field_origin').closest('p'));
                }
                show_when($field, 'field_origin', $inject_origin_after !== null);
                show_when($field, 'field_origin_var', $inject_origin_after !== null);

                show_when($field, 'field_choices', -1 !== $.inArray(type, ['radio', 'checkbox', 'select']));
                show_when($field, 'field_label', -1 === $.inArray(type, ['separator', 'message']));
                show_when($field, 'field_message', -1 !== $.inArray(type, ['message']));
                show_when($field, 'field_name', -1 !== $.inArray(type, ['hidden']));
                show_when($field, 'field_details', -1 === $.inArray(type, ['hidden', 'variable', 'separator']));
                show_when($field, 'field_mandatory', -1 === $.inArray(type, ['hidden', 'variable', 'checkbox', 'separator']));
                show_when($field, 'field_default_value', -1 === $.inArray(type, ['hidden', 'variable', 'separator']));
                show_when($field, 'field_style', -1 !== $.inArray(type, ['message']));
                show_when($field, 'field_width', -1 !== $.inArray(type, ['text']));
                show_when($field, 'field_height', -1 !== $.inArray(type, ['textarea']));
                show_when($field, 'field_limited_to', -1 !== $.inArray(type, ['text']));

                // if the mandatory field is not visible, it needs to be unchecked...
                var $field_mandatory = find_field($field, 'field_mandatory');
                if ($field_mandatory.closest('p').css('display') === 'none') {
                    $field_mandatory.prop('checked', false);
                }

                // The 'type' field is for sure in the first wijmo-wijaccordion-content so we know $field IS an .accordion too
                // So the selectedIndex is for sure '0'
                $field.find('.wijmo-wijaccordion-content').each(function() {
                    var $accordion_content = $(this);
                    // We need to select the appropriate index with wijaccordion() prior to changing the style or it's all messed up
                    if ($accordion_content.find(':input').filter(function() {
                        return $(this).closest('p').css('display') != 'none';
                    }).length == 0) {
                        // wijaccordion('activate', 0) does not work properly.

                        // Hide .accordion-header
                        $accordion_content.prev().hide();
                    } else {
                        $accordion_content.prev().show();
                    }
                });

                // Generate default value before preview, because the preview uses it
                generate_default_value($field);
                $field.find('[name*="[field_label]"]').trigger('change');
                $field.find('[name*="[field_style]"]').trigger('change');
                generate_preview.call($field.get(0), e);
            });

            $fields_container.on('blur', 'textarea[name*="[field_choices]"]', function regenerate_default_value(e) {
                var $field = $(this).closest('.field_enclosure');
                generate_default_value($field);
            });

            $fields_container.on('change', 'select[name*="[field_style]"]', function on_style_change(e) {
                var style = $(this).val();
                var $field = $(this).closest('.field_enclosure');
                var $message = $field.find('[name*="[field_message]"]');
                var $new = $(style == 'p' ? '<textarea rows="4"></textarea>' : '<input type="text" />');

                $new.attr({
                    name: $message.attr('name'),
                    id: $message.attr('id')
                });
                $new.val($message.val());

                $new.insertAfter($message);
                $message.remove();
                $new.parent().nosFormUI();
                // Converting textarea to input will loose line breaks
                $new.trigger('change');
            });

            $fields_container.on('change', 'input[name*="[field_mandatory]"]', function on_change_field_mandatory(e) {
                var $field = $(this).closest('.field_enclosure');
                generate_default_value($field);
            });

            function find_field($context, field_name) {
                return $context.find('[name*="[' + field_name + ']"]');
            }


            function generate_label(e) {
                var $field = $(this).closest('.field_enclosure');
                var $preview = $field.data('preview');
                if (!$preview) {
                    return;
                }
                var $label = $preview.find('label.preview_label');
                var is_mandatory = find_field($field, 'field_mandatory').is(':checked');
                $label.text(find_field($field, 'field_label').val() + (is_mandatory ? ' *' : ''));
                if ($(this).is(':visible')) {
                    $label.show();
                } else {
                    $label.hide();
                }
            }
            // Events that regenerates the preview label
            $fields_container.on('change keyup', 'input[name*="[field_label]"]', generate_label);
            $fields_container.on('change', 'input[name*="[field_mandatory]"]', generate_label);

            function generate_default_value($field) {

                var type = find_field($field, 'field_type').val();
                var $default_value = find_field($field, 'field_default_value');
                var choices = find_field($field, 'field_choices').val();
                var default_value_value = $default_value.val();
                if (default_value_value.match(/^[0-9,]+$/)) {
                    default_value_value = default_value_value.split(',');
                } else {
                    default_value_value = default_value_value.split("\n")
                }
                var $new = null;
                var name = $default_value.attr('name');

                if (-1 !== $.inArray(type, ['radio', 'select'])) {
                    var html = '<select>';
                    html += '<option value=""></option>';
                    $.each(choices.split("\n"), function(i, choice) {
                        html += '<option value="' + i + '" ' + (default_value_value[0] == choice ? 'selected' : '') + '>' + choice + '</option>';
                    });
                    html += '</select>';
                    $new = $(html).attr({
                        name: $default_value.attr('name'),
                        id: $default_value.attr('id')
                    });
                } else if (type == 'checkbox') {
                    var html = '<input type="hidden" size="1" name="' + $default_value.attr('name') + '" value=""  />';
                    $.each(choices.split("\n"), function(i, choice) {
                        html += '<label><input type="checkbox" class="checkbox" size="1" value="' + i + '" ' + (-1 !== $.inArray(choice, default_value_value) ? 'checked' : '') + '> ' + choice + '</label><br />';
                    });
                    $new = $(html);
                } else {
                    var html_type = (-1 !== $.inArray(type, ['email', 'number', 'date']) ? type : 'text');
                    $new = $(type == 'textarea' ? '<textarea rows="3" />' : '<input type="' + html_type + '" />').attr({
                        name: $default_value.attr('name'),
                        id: $default_value.attr('id')
                    }).val(default_value_value);
                }
                var $parent = $default_value.closest('span');
                $parent.empty().append($new).nosFormUI();

                var $checkboxes = $new.find('input.checkbox');
                $checkboxes.on('change', function(e) {
                    var value = [];
                    $checkboxes.filter(':checked').each(function() {
                        value.push($(this).val());
                    });
                    $new.first().val(value.join(','));
                    // Event doesn't seems to trigger with the hidden field on a delegated handler
                    generate_preview.call(this, e);
                });
                $checkboxes.first().trigger('change');
            }

            function generate_preview(e) {
                var $field = $(this).closest('.field_enclosure');
                var type = find_field($field, 'field_type').val();
                var choices = find_field($field, 'field_choices').val();
                var width = find_field($field, 'field_width').val();
                var height = find_field($field, 'field_height').val();
                var details = find_field($field, 'field_details').val();
                var $preview = $field.data('preview');
                var $td = $preview.find('div.preview_content');
                var html  = '';
                var default_value_value = find_field($field, 'field_default_value').val().split(',');

                if (type == 'text' || type == 'email' || type == 'number' || type == 'date' || type == 'file') {
                    var size = '';
                    if (width != '') {
                        size = ' size="' + width + '"';
                    }
                    html = '<input type="text" ' + size + ' value="' + default_value_value.join('') + '" ∕>';
                }

                if (type == 'textarea') {
                    var cols = '';
                    if (height != '') {
                        cols = ' rows="' + height + '"';
                    }
                    html = '<textarea' + cols + '>' + default_value_value.join('') + '</textarea>';
                }

                if (type == 'radio') {
                    $.each(choices.split("\n"), function(i, text) {
                        html += '<p><label><input type="radio" value="' + i + '" ' + (-1 !== $.inArray(i + '', default_value_value) ? 'checked' : '') + ' />' + text +'</label></p>';
                    });
                }

                if (type == 'checkbox') {
                    $.each(choices.split("\n"), function(i, text) {
                        html += '<p><label><input type="checkbox" value="' + i + '" ' + (-1 !== $.inArray(i + '', default_value_value) ? 'checked' : '') + ' />' + text +'</label></p>';
                    });
                }

                if (type == 'select') {
                    html += '<select>';
                    html += '<option value=""></option>';
                    $.each(choices.split("\n"), function(i, text) {
                        html += '<option value="' + i + '" ' + (-1 !== $.inArray(i + '', default_value_value) ? 'selected' : '') + '>' + text +'</option>';
                    });
                    html += '</select>';
                }

                if (type == 'message') {
                    var message = find_field($field, 'field_message').val().replace(/\n/g, '<br />');
                    var style = find_field($field, 'field_style').val();
                    html += '<' + style + '>' + message + '</' + style + '>';
                }

                if (type == 'separator') {
                    html += '<hr />';
                }

                if (type == 'page_break') {
                    $preview.addClass('page_break ui-widget-header');
                    //$preview.find('.resizable').removeClass('.resizable');
                }

                if (details != '') {
                    html += '<div class="details">' + $('<div/>').text(details).html() + '</div>';
                }

                $td.html(html);

                refreshPreviewHeight($preview.closest('tr'));
            }

            // Events that regenerates the preview content
            $fields_container.on('change keyup', 'textarea[name*="[field_choices]"]', generate_preview);
            $fields_container.on('change keyup', '[name*="[field_message]"]', generate_preview);
            $fields_container.on('change keyup', '[name*="[field_default_value]"]', generate_preview);
            $fields_container.on('change keyup', 'input[name*="[field_width]"]', generate_preview);
            $fields_container.on('change keyup', 'input[name*="[field_height]"]', generate_preview);
            $fields_container.on('change keyup', 'textarea[name*="[field_details]"]', generate_preview);

            function refreshPreviewHeight($tr) {
                ($tr || $preview_container.find('tr')).css('height', '').each(function sameHeight() {
                    var $resizable = $(this).find('div.resizable').css('height', '');
                    $resizable.css('height', $(this).height());
                });
            }

            function resize_to_best($tr, priority) {

                // If there's only one padding cell, it's already good!
                if ($tr.find('td:not(.padding)').length == 0) {
                    return;
                }

                // Don't take padding into account (will be re-added at the end if needed)
                $tr.find('td.padding').remove();

                var $cells = $tr.find('td.preview:visible');
                var cell_sizes = [];
                var total_size = 0;
                var $widest = [];
                var max_cell_size = 5 - $cells.length;

                $cells.each(function() {
                    var $cell = $(this);
                    // When doing a resize, the <td> keeps the same width, only the div.resizable changes size
                    var $item = $cell.find('.resizable');
                    if ($item.length == 0) {
                        $item = $cell;
                    }
                    var cell_size = Math.round($item.outerWidth() / col_size);
                    cell_size = Math.max(1, Math.min(max_cell_size, cell_size));
                    cell_size = parseInt($item.attr('colspan')) || cell_size;
                    cell_sizes.push(cell_size);
                    total_size += cell_size;
                    $cell.data('colspan', cell_size);

                    if ($widest.length == 0 || cell_size > $widest.data('colspan') ) {
                        $widest = $cell;
                    }
                });

                // If total size overflow the 4 columns, we need to shrink one of them
                if (total_size > 4) {
                    //log("CELL WIDTHS = ", cell_sizes.join(','), total_size);

                    if (priority === undefined || priority == null) {
                        priority = $widest.get(0);
                        //log('priority was not set, automatically using ', priority);
                    } else {
                        //log('using defined priority ', priority);
                    }

                    var total_shrink_needed = total_size - 4;
                    // Shrink what we can (size > 1)
                    var $shrink_me = $cells.filter(function() {
                        return $(this).data('colspan') > 1 && this != priority;
                    });
                    //log('shrinkable items are', $shrink_me);
                    $shrink_me.closest('td.preview').each(function() {
                        var $cell = $(this);
                        var size = $cell.data('colspan');
                        var shrink_by = 1;
                        while((size - shrink_by > 1) && (total_shrink_needed - shrink_by) > 0) {
                            shrink_by++;
                        };
                        //log('shrinking ', this, ' by ', shrink_by);
                        total_shrink_needed -= shrink_by;
                        $cell.data('colspan', size - shrink_by);
                    });
                    // We need to shrink priority now...
                    if (total_shrink_needed > 0) {
                        //log('shrinking priority cell', priority, ' by ', total_shrink_needed);
                        $(priority).data('colspan', $(priority).data('colspan') - total_shrink_needed);
                    }
                }

                // Resize the <td> according to the new resized valued
                $tr.find('td.preview').each(function() {
                    //log('col size = ', $(this).data('colspan'));
                    set_cell_colspan($(this), $(this).data('colspan'));
                    $(this).removeData('colspan');
                });

                // Add a padding cell if necessary
                if (total_size < 4) {
                    var $padding = $('<td class="padding">&nbsp;</td>');
                    set_cell_colspan($padding, (4 - total_size));
                    //log('adding a padding cell with size = ', (4 - total_size), $padding, ' to ', $tr);
                    $tr.append($padding);
                } else {
                    //log('no padding neeeded');
                }
            }

            // Set colspan and restore children width
            function set_cell_colspan($td, colspan) {
                $td.attr('colspan', colspan).children().css('width', 'auto');
            }

            // Returns the colspan, according to width
            function get_cell_colspan($td) {
                return Math.round($td.width() / col_size);
            }

            // Save cell width
            function save_cell_width($tr) {
                $tr.find('td.preview:visible').each(function() {
                    var $this = $(this);
                    $this.data('saved_colspan', get_cell_colspan($this));
                })
            }

            // Save cell width
            function restore_cell_width($tr) {
                $tr.find('td.preview:visible').each(function() {
                    var $this = $(this);
                    var saved_colspan = $this.data('saved_colspan');
                    if (saved_colspan) {
                        set_cell_colspan($this, saved_colspan);
                    }
                });
            }

            var $sortable;
            function init_sortable() {
                try {
                    $sortable.destroy();
                } catch (e) {}

                $preview_container.find('td.padding').remove();
                $preview_container.find('td.sortable_placeholder').remove();
                // Remove empty lines
                $preview_container.find('tr').addClass('preview_row').filter(function() {
                    return $(this).children().not('.placeholder').length == 0;
                }).remove();

                $preview_container.find('td.page_break').each(function() {
                    var $td = $(this);
                    set_cell_colspan($td, 4);
                    $td.closest('tr').addClass('page_break');
                });

                // Add empty lines to drop before / after (above / below) existing .preview_row
                $preview_container.find('tr').before('<tr class="preview_row preview_inserter"><td class="padding" colspan="4">&nbsp;</td></tr>');
                $preview_container.append('<tr class="preview_row preview_inserter"><td class="padding" colspan="4">&nbsp;</td></tr>');

                //console.log('init size_to_fit_available');
                $preview_container.find('tr.preview_row').each(function() {
                    resize_to_best($(this));
                });

                // Connects only list which don't have 4 children
                $preview_container.find('tr').removeClass('preview_row_sortable').filter(function() {
                    //console.log($(this).children().length);
                    return $(this).children().not('.padding').length < 4;
                }).addClass('preview_row_sortable');

                var field_colspan;

                // @TODO find a way to only connect others lists (not including itself)
                $sortable = $preview_container.find('tr.preview_row:not(.page_break)').sortable({
                    connectWith: id + ' tr.preview_row_sortable:not(.page_break)',
                    dropOnEmpty: true,
                    helper: "clone", // This is needed when using the "appendTo" option
                    appendTo: id, // Where the 'helper' is appended
                    items: '> td.preview',
                    forceHelperSize: true,
                    placeholder: 'sortable_placeholder preview',
                    tolerance: 'pointer', // 'intersect' or 'pointer'
                    handle: '.handle',
                    beforeStop: function(e, ui) {
                        // http://bugs.jqueryui.com/ticket/6054
                        // When dropping occurs outside of the items
                        //return false;
                    },
                    start: function onSortableStart(e, ui) {
                        var $tr = ui.placeholder.closest('tr');

                        // Blur the selection
                        blur();

                        // Retain container height while dragging (the sortable will hide the item, possibly making an empty row without height)
                        $tr.css('height', ui.item.height());

                        // Style the placeholder with jQuery UI skin
                        // Do this after the blur, or the ui-state-active will be removed
                        ui.placeholder.addClass('ui-widget-content ui-state-active');

                        // Firefox: height=100% on absolute div inside the position=relative cell is messed up
                        ui.placeholder.children().css('height', $tr.css('height'));

                        // Make the placeholder the same width as the original item.
                        set_cell_colspan(ui.placeholder, get_cell_colspan(ui.helper));
                    },
                    update: function onSortableUpdate(e, ui) {
                        var $tr = ui.item.closest('tr');
                        // Restore original height
                        $tr.css('height', '');

                        // Resize the current line on drop, or the lines above would be all messed up because the total
                        // colspan of a line below can exceed 4
                        $tr.children().css('height', $tr.css('height'));
                        set_cell_colspan(ui.item, field_colspan);
                        resize_to_best($tr, ui.item.get(0));

                        // Re-initialise everything
                        init_all();
                    },
                    over: function onSortableOver(e, ui) {
                        var $tr = ui.placeholder.closest('tr');

                        ui.item.children().css('height', '');

                        // Save old size on over
                        ui.placeholder.hide();
                        save_cell_width($tr);
                        ui.placeholder.show();

                        // Firefox: height=100% on absolute div inside the position=relative cell is messed up
                        ui.placeholder.children().css('height', $tr.css('height'));

                        // Let's try to retain original item size
                        set_cell_colspan(ui.placeholder, get_cell_colspan(ui.helper));

                        // Handle overflow (> 4 columns)
                        resize_to_best($tr, ui.placeholder.get(0));

                        // Save appropriate colspan for the dragged field
                        field_colspan = get_cell_colspan(ui.placeholder);
                    },
                    out: function onSortableOut(e, ui) {
                        var $tr = ui.placeholder.closest('tr');
                        //ui.placeholder.remove();
                        restore_cell_width($tr);
                    }
                });

                $preview_container.find('tr.preview_row.page_break').sortable({
                    appendTo: id, // Where the 'helper' is appended
                    connectWith: id + ' tr.preview_row_sortable.preview_inserter',
                    helper: "clone", // This is needed when using the "appendTo" option
                    dropOnEmpty: true,
                    forceHelperSize: true,
                    forcePlaceholderSize: true,
                    placeholder: 'sortable_placeholder preview', // ui-state-active
                    handle: '.handle',
                    start: function onSortableStart(e, ui) {
                        var $tr = ui.placeholder.closest('tr');

                        // Blur the selection
                        blur();

                        // Retain container height (the sortable will hide the item, possibly making an empty row without height)
                        $tr.css('height', ui.item.height());

                        // Style the placeholder with jQuery UI skin
                        // Do this after the blur, or the ui-state-active will be removed
                        ui.placeholder.addClass('ui-widget-content ui-state-active');

                        // Firefox: height=100% on absolute div inside the position=relative cell is messed up
                        ui.placeholder.children().css('height', $tr.css('height'));

                        // Make the placeholder the same width as the original item.
                        set_cell_colspan(ui.placeholder, 4);
                    }
                });
            }

            function blur() {
                var $preview = $preview_container.find('.ui-widget-content.ui-state-active');
                if ($preview.length == 0) {
                    return;
                }
                $preview.removeClass('ui-state-active');
                hide_field($preview.data('field'));
            }
            function hide_field($field) {
                $fields_container.find('.show_hide').hide();
                if ($field) {
                    $field.hide();
                }
            }

            var $resizable;
            function init_resizable() {
                try {
                    $resizable.destroy();
                } catch (e) {}

                $resizable = $preview_container.find('td.preview').not('.page_break').find('div.resizable');
                $resizable = $resizable.resizable({
                    ghost: true,
                    handles: 'se',
                    autoHideType: true,
                    helper: 'helper_resize preview ui-state-active',
                    grid: [col_size, '2000'],
                    start: function(e, ui) {
                        blur();
                    },
                    stop: function(e, ui) {
                        var $tr = ui.element.closest('tr');
                        // Handle overflow (> 4 columns)
                        resize_to_best($tr, ui.element.closest('td').get(0));
                        refreshPreviewHeight($tr);
                    }
                });
            }

            function init_all($tr) {
                init_resizable();
                init_sortable();
                refreshPreviewHeight($tr);
            }

            $fields_container.children('.field_enclosure').each(function onEachFields() {
                var $field = $(this);
                on_field_added($field, {where: 'bottom'});
                $field.hide();
            });
            $fields_container.find('.show_hide').hide();

            function apply_layout(layout) {
                $.each(layout.split("\n"), function layoutLines() {
                    var $previous = null;
                    if (this == '') {
                        return;
                    }
                    $.each(this.split(','), function layoutCols() {
                        var item = this.split('=');
                        var field_id = item[0];
                        var field_width = item[1];
                        var $preview = find_field($fields_container, 'field_id').filter(function() {
                            return $(this).val() == field_id;
                        }).closest('.field_enclosure').data('preview');
                        set_cell_colspan($preview, field_width);
                        if ($previous) {
                            $previous.after($preview);
                        }
                        $previous = $preview;
                    });
                });
            }

            apply_layout($layout.val());

            // init all the thing
            init_all();
            setTimeout(function() {
                refreshPreviewHeight();
            }, 100);

            is_new && add_fields_blank_slate(null, 'default');

            var $form_captcha = $container.find('[name=form_captcha]'),
                $form_submit_label = $container.find('[name=form_submit_label]'),
                $form_submit_email = $container.find('[name=form_submit_email]');

            $form_captcha.on('change', function() {
                $submit_informations.find('.form_captcha')[$(this).is(':checked') ? 'show' : 'hide']();
            }).trigger('change');

            $form_submit_label.on('change keyup', function() {
                $submit_informations.find('input:last').val($(this).val());
            }).trigger('change');

            $form_submit_email.on('change keyup', function() {
                var mail = $.trim($(this).val());
                $submit_informations.find('.form_submit_email')
                    .find('span:first')
                    .html(mail.replace('\n', ', ', 'g'))
                    .end()
                    .find('span:last')[mail ? 'hide': 'show']();
            }).trigger('change');

            $submit_informations.on('click', function() {
                $container.find('.preview').removeClass('ui-state-active');
                var $accordion = $form_submit_label.closest('.accordion');
                show_field($accordion);
                hide_field();
                set_field_padding($submit_informations);
                $submit_informations.addClass('ui-state-active');
                $container.find('.preview_arrow').show();
            });


            // Firefox needs this <colgroup> to size the td[colspan] properly
            //$preview_container.closest('table').prepend($('<colgroup><col width="' + col_size + '" /><col width="' + col_size + '" /><col width="' + col_size + '" /><col width="' + col_size + '" /></colgroup>'));
        };
    });
