<link rel="stylesheet" href="static/apps/noviusos_form/css/admin.css" />


<div id="<?= $uniqid = uniqid('container_') ?>">

    <div class="line">
        <div class="unit col c8" style="position:relative;">
            <p>
                <button type="button" data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'top'))) ?>"><?= __('Add a field') ?></button>
            </p>

            <div class="field_blank_slate ui-widget-content" style="display:none;">
                <table style="width: 100%;">
                    <tr>
                        <th><?= __('Standard fields'); ?></th>
                        <th><?= __('Special fields'); ?></th>
                    </tr>
                    <tr>
                        <td style="width: 50%">
                            <?php
                            foreach (\Config::get('noviusos_form::controller/admin/form.fields_meta.standard') as $type => $meta) {
                                ?>
                                <p><label data-meta="<?= $type ?>"><img src="<?= $meta['icon'] ?>" /> <?= $meta['title'] ?></label></p>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="width: 50%">
                            <?php
                            foreach (\Config::get('noviusos_form::controller/admin/form.fields_meta.special') as $type => $meta) {
                                ?>
                                <p><label data-meta="<?= $type ?>"><img src="<?= $meta['icon'] ?>" /> <?= $meta['title'] ?></label></p>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <table style="width: 100%; table-layout:fixed;">
                <tbody class="preview_container">

                </tbody>
            </table>

            <p>
                <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom'))) ?>"><?= __('Add a field') ?></button>
            </p>
        </div>

        <div class="lastUnit col c4 fields_container">
            <img class="preview_arrow show_hide" src="static/apps/noviusos_form/img/arrow-edition.png" />
            <p class="actions show_hide">
                <button type="button" data-icon="trash" data-id="delete" class="action"><?= ('Delete') ?></button>
                <button type="button" data-icon="copy" data-id="copy" class="action"><?= ('Duplicate') ?></button>
            </p>
            <?php
            $layout = explode("\n", $item->form_layout);
            array_walk($layout, function(&$v) {
                $v = explode(',', $v);
            });
            $layout = \Arr::flatten($layout);
            array_walk($layout, function(&$v) {
                $v = explode('=', $v);
                $v = $v[0];
            });
            foreach ($layout as $field_id) {
                echo \Request::forge('noviusos_form/admin/form/render_field')->execute(array($item->fields[$field_id]));
            }
            ?>
        </div>
    </div>

    <div class="field_preview ui-widget-content" style="display:none;">
        <table width="100%">
            <tbody>
            <tr class="preview_row" data-id="clone_preview">
                <td class="preview" colspan="4">
                    <div class="ui-widget-content">
                        <div class="resizable">
                            <div class="handle ui-widget-header">
                                <img src="static/apps/noviusos_form/img/move-handle-dark3.png" />
                            </div>
                            <label class="preview_label"></label>
                            <div class="preview_content">

                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
require(['jquery-nos', 'jquery-ui.sortable', 'jquery-ui.resizable'], function($) {
    $(function() {
        var $container = $('#<?= $uniqid ?>');
        var $preview_container = $container.find('.preview_container');
        var $fields_container = $container.find('.fields_container');
        var $layout = $container.closest('form').find('[name=form_layout]');

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

        // Fill in the hidden field form_layout upon save
        $container.closest('form').bind('submit', function(e) {
            // Compute the layout
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
                    layout += $field.find('[name=field[id][]]').val() + '=' + Math.round($preview.outerWidth() / col_size);
                });
            });
            $layout.val(layout);
        });

        function add_fields_blank_slate(e) {
            e.preventDefault();
            var params = {
                where: ($blank_slate.data('params') || {}).where || 'bottom'
            };
            $.ajax({
                url: 'admin/noviusos_form/form/form_field_meta/' + $(this).data('meta'),
                dataType: 'json',
                success: function(json) {
                    $blank_slate.hide();
                    var $fields = $(json.fields).filter(function() {
                        return this.nodeType != 3; // 3 == Node.TEXT_NODE
                    });
                    if (params.where == 'top') {
                        $fields = $($fields.get().reverse());
                    }
                    $fields_container.append($fields);
                    $fields.each(function() {
                        var $field = $(this);
                        $fields.nosFormUI();
                        on_field_added($field, params);
                        $field.hide();
                    });
                    apply_layout(json.layout);
                    init_all();
                }
            });
        }

        // Add a new field when clicking the "Add" button, either at top or bottom
        $blank_slate.on('click', 'label', add_fields_blank_slate);

        function on_field_added($field, params) {
            // Make checkbox fill a hidden field instead (we're sending an array, we don't want "missing" values)
            $field.find('input[type=checkbox]').each(function normaliseCheckboxes() {
                var $checkbox = $(this);
                var name     = $checkbox.attr('name');
                var $hidden   = $('<input type="hidden" value="" />');
                $hidden.insertAfter($checkbox);
                $checkbox.on('change', function() {
                    $hidden.attr('name', $(this).is(':checked') ? '' : name);
                }).trigger('change');
            });

            // The clone will be wrapped into a <tr class="preview_row">
            var $preview = get_preview($field);
            $preview_container[params.where == 'top' ? 'prepend' : 'append']($preview.parent());
            $field.find('select[name^="field[type]"]').trigger('change');
        }

        // Add a field
        $container.on('click', '[data-id=add]', function onAdd(e) {
            e.preventDefault();
            var params_button = $(this).data('params');
            $(this).closest('p')[params_button.where == 'top' ? 'after' : 'before']($blank_slate);
            $blank_slate.data('params', params_button);
            $blank_slate.show();
            set_field_padding();
        });

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
            $preview_container.find('td.preview').children().removeClass('ui-state-active');
            $preview.children().addClass('ui-state-active');

            // Show the appropriate field and position it
            $fields_container.find('.show_hide').show();
            $field.show();
            $field.nosOnShow();
            $field.siblings('.accordion').hide();
            set_field_padding();

            $field.find('[name^="field[label"]').focus();
        }

        function set_field_padding() {

            var $focus = $preview_container.find('.ui-state-active').closest('td.preview');
            if ($focus.length > 0) {
                var pos = $focus.position();
                $fields_container.css({
                    paddingTop: Math.max(0, pos.top - 29) + 'px' // 29 = arrow height
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
            $preview.remove();
            hide_field();
            init_all();
        }

        // Delete listener
        $fields_container.on('click', '[data-id=delete]', function on_delete(e) {
            e.preventDefault();
            // Don't bubble to .preview container
            e.stopPropagation();
            if (confirm('Are you sure?')) {
                delete_preview.call($preview_container.find('.ui-widget-content.ui-state-active').closest('.preview'));
            }
        });

        function show_when($field, name, show) {
            $field.find('[name^="field[' + name + ']"]').closest('p')[show ? 'show' : 'hide']()
        }

        // When the "field_type" changes
        $fields_container.on('change', 'select[name^="field[type]"]', function on_type_change(e) {
            var type = $(this).val();
            var $field = $(this).closest('.accordion');

            show_when($field, 'choices', -1 !== $.inArray(type, ['radio', 'checkbox', 'select']));
            show_when($field, 'label', -1 === $.inArray(type, ['hidden', 'separator']));
            show_when($field, 'name', -1 !== $.inArray(type, ['hidden']));
            show_when($field, 'value', -1 !== $.inArray(type, ['hidden']));
            show_when($field, 'details', -1 === $.inArray(type, ['hidden']));
            show_when($field, 'mandatory', -1 === $.inArray(type, ['hidden']));
            show_when($field, 'default_value', -1 === $.inArray(type, ['hidden']));
            show_when($field, 'height', -1 !== $.inArray(type, ['textarea']));
            show_when($field, 'limited_to', -1 !== $.inArray(type, ['text']));
            show_when($field, 'width', -1 !== $.inArray(type, ['text']));

            $field.find('[name^="field[label]"]').trigger('change');
            $field.find('textarea[name^="field[choices]"]').trigger('change');
        });

        // When the "field_label" changes
        $fields_container.on('change keyup', 'input[name^="field[label]"]', function on_label_change(e) {
            var $field = $(this).closest('.accordion');
            var $preview = $field.data('preview');
            $preview.find('label').text($(this).val());
        });

        function generate_preview(e) {
            var $field = $(this).closest('.accordion');
            var type = $field.find('[name^="field[type]"]').val();
            var width = $field.find('[name^="field[width]"]').val();
            var height = $field.find('[name^="field[height]"]').val();
            var $preview = $field.data('preview');
            var $td = $preview.find('div.preview_content');
            var html  = '';

            if (type == 'text' || type == 'email' || type == 'number' || type == 'date') {
                var size = '';
                if (width != '') {
                    size = ' size="' + width + '"';
                }
                html = '<input type="text" ' + size + ' ∕>';
            }

            if (type == 'textarea') {
                var cols = '';
                if (height != '') {
                    cols = ' rows="' + height + '"';
                }
                html = '<textarea' + cols + '></textarea>';
            }

            if (type == 'radio') {
                $.each($(this).val().split("\n"), function(i, text) {
                    html += '<p><label><input type="radio" />' + text +'</label></p>';
                });
            }

            if (type == 'checkbox') {
                $.each($(this).val().split("\n"), function(i, text) {
                    html += '<p><label><input type="checkbox" />' + text +'</label></p>';
                });
            }

            if (type == 'select') {
                html += '<select>';
                $.each($(this).val().split("\n"), function(i, text) {
                    html += '<option>' + text +'</option>';
                });
                html += '</select>';
            }

            if (type == 'message') {
                html += $(this).val().replace(/\n/g, '<br />');
            }

            if (type == 'separator') {
                $preview.find('.ui-widget-content').addClass('ui-widget-header');
                $preview.addClass('separator');
                $preview.find('.resizable').children().unwrap();
            }

            $td.html(html);

            setTimeout(refreshPreviewHeight, 10);
        }

        // When the "field_choices" changes
        $fields_container.on('change keyup', 'textarea[name^="field[choices]"]', generate_preview);
        $fields_container.on('change keyup', 'input[name^="field[width]"]', generate_preview);
        $fields_container.on('change keyup', 'input[name^="field[height]"]', generate_preview);

        function refreshPreviewHeight() {

            $preview_container.find('tr').each(function sameHeight() {
                var $preview = $(this).find('td.preview');
                var max_height = 0;

                $preview.children().height('auto').children().css('height', '');
                $preview.each(function() {
                    max_height = Math.max(max_height, $(this).outerHeight());
                })
                if (max_height) {
                    $preview.children().height(max_height);
                }
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
                cell_sizes.push(cell_size);
                total_size += cell_size;
                $cell.data('colspan', cell_size);

                if ($widest.length == 0 || cell_size > $widest.data('colspan') ) {
                    $widest = $cell;
                }
            });

            // If total size overflow the 4 columns, we need to shrink one of them
            if (total_size > 4) {
                //log("CELL WIDTHS = ", size.join(','), $preview, total_size);

                if (priority === undefined || priority == null) {
                    priority = $widest.get(0);
                    //console.log('priority was not set, automatically using ', priority);
                } else {
                    //console.log('using defined priority ', priority);
                }

                var total_shrink_needed = total_size - 4;
                // Shrink what we can (size > 1)
                var $shrink_me = $cells.filter(function() {
                    return $(this).data('colspan') > 1 && this != priority;
                });
                //console.log('shrinkable items are', $shrink_me);
                $shrink_me.closest('td.preview').each(function() {
                    var $cell = $(this);
                    var size = $cell.data('colspan');
                    var shrink_by = 1;
                    while((size - shrink_by > 1) && (total_shrink_needed - shrink_by) > 0) {
                        shrink_by++;
                    };
                    //console.log('shrinking ', this, ' by ', shrink_by);
                    total_shrink_needed -= shrink_by;
                    $cell.data('colspan', size - shrink_by);
                });
                // We need to shrink priority now...
                if (total_shrink_needed > 0) {
                    $(priority).data('colspan', $(priority).data('size') - total_shrink_needed);
                }
            }

            // Resize the <td> according to the new resized valued
            $tr.find('td.preview').width('auto').each(function() {
                //console.log('col size = ', $(this).data('size'));
                set_cell_colspan($(this), $(this).data('colspan'));
                $(this).removeData('colspan');
            });

            // Add a padding cell if necessary
            if (total_size < 4) {
                var $padding = $('<td class="padding">&nbsp;</td>');
                set_cell_colspan($padding, (4 - total_size));
                //console.log('adding a padding cell with size = ', (4 - total_size), $padding, ' to ', $tr);
                $tr.append($padding);
            } else {
                //console.log('no padding neeeded');
            }
        }

        // Set both width and colspan accordingly
        function set_cell_colspan($td, colspan) {
            var width = Math.round(colspan * col_size);
            $td.attr('colspan', colspan).width(width + 'px');
            $td.children().width(width - 15).children('.resizable').width(width - 15);
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
                return $(this).children(':not(.placeholder)').length == 0;
            }).remove();

            $preview_container.find('td.separator').each(function() {
                var $td = $(this);
                set_cell_colspan($td, 4);
                $td.closest('tr').addClass('separator');
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
                return $(this).children().length < 4;
            }).addClass('preview_row_sortable');

            // @TODO find a way to only connect others lists (not including itself)
            $sortable = $preview_container.find('tr.preview_row:not(.separator)').sortable({
                connectWith: '#<?= $uniqid ?> tr.preview_row_sortable:not(.separator)',
                dropOnEmpty: true,
                helper: "clone", // This is needed when using the "appendTo" option
                appendTo: '#<?= $uniqid ?>', // Where the 'helper' is appended
                items: '> td.preview',
                forceHelperSize: true,
                placeholder: 'sortable_placeholder preview',
                tolerance: 'pointer', // 'intersect' or 'pointer'
                handle: '.handle',
                start: function onSortableStart(e, ui) {
                    var $tr = ui.placeholder.closest('tr');

                    // Blur the selection
                    blur();

                    // Retain container height (the sortable will hide the item, possibly making an empty row without height)
                    $tr.css('height', ui.item.height());

                    // Style the placeholder with jQuery UI skin
                    // Do this after the blur, or the ui-state-active will be removed
                    ui.placeholder.html('<div class="ui-widget-content ui-state-active">&nbsp;</div>');

                    // Firefox: height=100% on absolute div inside the position=relative cell is messed up
                    ui.placeholder.children().css('height', $tr.css('height'));

                    // Make the placeholder the same width as the original item.
                    set_cell_colspan(ui.placeholder, get_cell_colspan(ui.helper));
                },
                update: function onSortableUpdate(e, ui) {
                    // Restore original height
                    ui.item.closest('tr').css('height', '');

                    // Re-initialise everything
                    init_all();
                },
                over: function onSortableOver(e, ui) {
                    var $tr = ui.placeholder.closest('tr');

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
                },
                out: function onSortableOut(e, ui) {
                    var $tr = ui.placeholder.closest('tr');
                    ui.placeholder.remove();
                    restore_cell_width($tr);
                }
            });

            $preview_container.find('tr.preview_row.separator').sortable({
                appendTo: '#<?= $uniqid ?>', // Where the 'helper' is appended
                connectWith: '#<?= $uniqid ?> tr.preview_row_sortable.preview_inserter',
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
                    ui.placeholder.html('<div class="ui-widget-content ui-state-active">&nbsp;</div>');

                    // Firefox: height=100% on absolute div inside the position=relative cell is messed up
                    ui.placeholder.children().css('height', $tr.css('height'));

                    // Make the placeholder the same width as the original item.
                    set_cell_colspan(ui.placeholder, 4);
                }
            });
        }

        function blur() {
            var $preview = $preview_container.find('.ui-widget-content.ui-state-active').parent();
            if ($preview.length == 0) {
                return;
            }
            $preview.children().removeClass('ui-state-active');
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

            $resizable = $preview_container.find('div.resizable');
            $resizable.css('height', '');
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
                    ui.element.css('height', '');
                    // Handle overflow (> 4 columns)
                    resize_to_best($tr, ui.element.closest('td').get(0));
                }
            });
        }

        function init_all() {
            init_resizable();
            init_sortable();
            set_field_padding();
            setTimeout(function() {
                refreshPreviewHeight();
            }, 100);
        }

        $fields_container.children('.accordion').each(function onEachFields() {
            var $field = $(this);
            on_field_added($field, {where: 'bottom'});
            $field.hide();
        });
        $fields_container.find('.show_hide').hide();

        function apply_layout(layout) {
            $.each(layout.split("\n"), function layoutLines() {
                var $previous = null;
                $.each(this.split(','), function layoutCols() {
                    var item = this.split('=');
                    var field_id = item[0];
                    var field_width = item[1];
                    var $preview = $fields_container.find('[name="field[id][]"]').filter(function() {
                        return $(this).val() == field_id;
                    }).closest('.accordion').data('preview');
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

        // Firefox needs this <colgroup> to size the td[colspan] properly
        $preview_container.closest('table').prepend($('<colgroup><col width="' + col_size + '" /><col width="' + col_size + '" /><col width="' + col_size + '" /><col width="' + col_size + '" /></colgroup>'));
    });

});
</script>