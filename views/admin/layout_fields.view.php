<link rel="stylesheet" href="static/apps/noviusos_form/css/admin.css" />


<div id="<?= $uniqid = uniqid('container_') ?>">
    <p>
        <button type="button" data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'top'))) ?>"><?= __('Add a field') ?></button>
    </p>

    <div class="line">
        <div class="unit col c8" style="position:relative;">
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
            foreach ($item->fields as $field) {
                echo \Request::forge('noviusos_form/admin/form/form_field')->execute(array($field));
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
    <?php /*
    <td width="30%" style="text-align: right;">
        <button data-icon="copy" data-id="copy" class="notransform" ><?= ('Duplicate') ?></button>
        <button data-icon="trash" data-id="delete" class="notransform" ><?= ('Delete') ?></button>
    </td>
    */ ?>
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

        var col_size = Math.round($preview_container.outerWidth() / 4);

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
            //console.log(layout);
            $layout.val(layout);
        });



        function add_field(params) {
            params.where = params.where || 'bottom';
            $.ajax({
                url: 'admin/noviusos_form/form/form_field_ajax',
                complete: function(xhr) {
                    var $field = $(xhr.responseText);
                    $fields_container.append($field);
                    $field.nosFormUI();
                    on_field_added($field, params);
                    init_all();
                }
            });
        }
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
            /*setTimeout(function() {
                on_focus_preview($preview);
            }, 10);*/
            $field.find('select[name^="field[type]"]').trigger('change');
        }

        function get_preview($field) {

            var $preview = $field.data('preview');
            if ($preview) {
                return $preview;
            }

            // Generate a new preview
            var $clone = $clone_preview.clone();
            $preview = $clone.find('td.preview');
            resize_to_col($preview, 4);

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
            var pos = $preview.position();

            // Make the preview look "active"
            $preview_container.find('td.preview').children().removeClass('ui-state-active');
            $preview.children().addClass('ui-state-active');

            // Show the appropriate field and position it
            $fields_container.find('.show_hide').show();
            $field.show();
            $field.nosOnShow();
            $field.siblings('.accordion').hide();
            $fields_container.css({
                paddingTop: Math.max(0, pos.top - 29) + 'px' // 29 = arrow height
            });

            $field.find('[name^="field[label"]').focus();
        }

        $preview_container.on('click', 'td.preview', function onClickPreview(e) {
            e.preventDefault();
            on_focus_preview(this);
        });

        // Add a new field when clicking the "Add" button, either at top or bottom
        $container.find('[data-id=add]').click(function onClickAdd(e) {
            e.preventDefault();
            add_field($(this).data().params);
        });

        // Duplicate a field
        $preview_container.on('click', '[data-id=copy]', function onClickCopy(e) {
            e.preventDefault();
            // Don't bubble to .preview container
            e.stopPropagation();
        });

        // Delete a preview + field
        function delete_preview() {
            // Focus the previous field
            var $preview = $(this);
            var $focus = $preview.prev();
            if ($focus.length == 0) {
                // Or the next if we were the first child
                $focus = $preview.next();
            }
            if ($focus.length == 0) {
                try {
                    $focus = $(this).closest('.preview_row').prev().prev();
                } catch (e) {}
                if ($focus.length == 0) {
                    try {
                        $focus = $(this).closest('.preview_row').next().next();
                    } catch (e) {}
                }
                if ($focus.length > 0) {
                    $focus = $focus.find('.preview').first();
                }
            }

            var $field = $preview.data('field');
            $field.remove();
            $preview.remove();

            init_all();
            // Refocus another field
            if ($focus.length > 0) {
                on_focus_preview($focus);
            }
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
            show_when($field, 'label', -1 === $.inArray(type, ['hidden', 'page_break']));
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

            if (type == 'text') {
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
                $preview.children().height('auto');
                $preview.children().height($preview.height());
            });
        }



        function resize_to_best($tr, priority) {
            var size = calc_size($tr);
            //console.log('Sizing to best = ', size);
            _resize_to($tr, size, priority);
        }
        function resize_to_original($tr) {
            var size = $tr.data('saved_size');
            //console.log('Restoring ', size);
            _resize_to($tr, size);
        }

        function _resize_to($tr, size, priority) {
            $tr.find('td.padding').remove();
            var total_size = 0;
            var widest = null;
            var $preview = $tr.find('td.preview');

            if (size.length == 0) {
                $preview = $preview.not('.sortable_placeholder');
            }

            $preview.each(function(i) {
                total_size += parseInt(size[i]);
                if (!widest || size > $(widest).data('size') ) {
                    widest = this;
                }
                $(this).data('size', size[i]);
            });

            if (priority === undefined || priority == null) {
                priority = widest;
            }
            //console.log('priority = ', priority, $.isArray(priority));
            //console.log("RESIZE VALUES BEFORE OVERFLOW = ", size, total_size);

            // If total size overflow the 4 columns, we need to shrink one of them
            if (total_size > 4) {
                var shrink_by = total_size - 4;
                //console.log('shrinking by = ', shrink_by);
                var shrink_me = $(priority).closest('tr').find('td.preview').filter(function() {
                    var $this = $(this);
                    //console.log($this.data('size'), this, priority);
                    return $this.data('size') > 1 && this != priority;
                }).filter(':first').closest('.preview');
                //console.log('shrinking ', shrink_me);
                shrink_me.data('size', shrink_me.data('size') - shrink_by);
                total_size -= shrink_by;
            }

            //console.log("ACTUAL RESIZE VALUES = ", size, total_size);

            // Resize the <td> according to the new resized valued
            $tr.find('td.preview').width('auto').each(function() {
                resize_to_col($(this), $(this).data('size'));
            });

            //console.log('total size  = ', total_size);

            // Add a padding cell if necessary
            if (total_size < 4) {
                var $padding = $('<td class="padding">&nbsp;</td>');
                resize_to_col($padding, (4 - total_size));
                //console.log('adding a padding cell with size = ', (4 - total_size), $padding, ' to ', $tr);
                $tr.append($padding);
            }
        }

        function calc_size($tr, priority) {
            $tr.find('td.padding').hide();
            var calc_size = [];
            var max_item_size = 5 - $tr.find('td.preview').length;

            $tr.find('td.preview').each(function() {
                // When doing a resize, the <td> keeps the same width, only the div.resizable changes size
                var $item = $(this).find('.resizable');
                if ($item.length == 0) {
                    $item = $(this);
                }
                var size = Math.round($item.outerWidth() / col_size);
                if (size < 1) {
                    size = 1;
                }
                if (size > max_item_size) {
                    size = max_item_size;
                }
                calc_size.push(size);
            });
            $tr.find('td.padding').show();

            return calc_size;
        }

        function save_size($tr) {
            $tr.data('saved_size', calc_size($tr));
            //console.log('Saved size = ', $tr.data('saved_size'));
        }

        function clear_saved_size($tr) {
            $tr.removeData('saved_size');
        }

        // Set both width and colspan accordingly
        function resize_to_col($td, colspan) {
            var width = Math.round(colspan * col_size);
            $td.attr('colspan', colspan).width(width + 'px');
            $td.children().width(width - 15).children('.resizable').width(width - 15);
        }

        var $sortable;
        function init_sortable() {
            try {
                $sortable.destroy();
            } catch (e) {}

            $preview_container.find('td.padding').remove();
            // Remove empty lines
            $preview_container.find('tr').addClass('preview_row').filter(function() {
                return $(this).children(':not(.placeholder)').length == 0;
            }).remove();

            // Add empty lines to drop before / after (above / below) existing .preview_row
            $preview_container.find('tr').before('<tr class="preview_row"><td class="padding" colspan="4">&nbsp;</td></tr>');
            $preview_container.append('<tr class="preview_row"><td class="padding" colspan="4">&nbsp;</td></tr>');

            // Connects only list which don't have 4 children
            $preview_container.find('tr').removeClass('preview_row_sortable').filter(function() {
                //console.log($(this).children().length);
                return $(this).children().length < 4;
            }).addClass('preview_row_sortable');

            //console.log('init size_to_fit_available');
            $preview_container.find('tr.preview_row').each(function() {
                var $tr = $(this);
                clear_saved_size($tr);
                resize_to_best($tr);
                save_size($tr);
            });

            // @TODO find a way to only connect others lists (not including itself)
            $sortable = $preview_container.find('tr.preview_row').sortable({
                appendTo: '#<?= $uniqid ?>', // Where the 'helper' is appended
                connectWith: '#<?= $uniqid ?> tr.preview_row_sortable',
                helper: "clone", // This is needed when using the "appendTo" option
                dropOnEmpty: true,
                items: '> td.preview',
                //forcePlaceholderSize: true,
                forceHelperSize: true,
                placeholder: 'sortable_placeholder preview ui-state-active',
                tolerance: 'pointer', // 'intersect' or 'pointer'
                handle: '.handle',
                out: function onSortableOut(e, ui) {
                    //console.log('OUT from ' + $(this).closest('tr').attr('id'));

                    // Restore saved size
                    var $tr = $(this).closest('tr');
                    resize_to_original($tr);
                },
                over: function onSortableOver(e, ui) {
                    //console.log('OVER from ' + $(this).closest('tr').attr('id'));

                    // Compute best size
                    var colspan = Math.round($(ui.helper).width() / col_size);
                    resize_to_col($(ui.placeholder), colspan);
                    var $tr = $(this).closest('tr');
                    resize_to_best($tr, this);
                },
                change: function onSortableChange(e, ui) {
                    var $placeholder = $(ui.placeholder);
                    //if (!$placeholder.has('.ui-widget-content')) {
                    //    $placeholder.wrapInner($('<div class="ui-widget-content">&nbsp;</div>').height($(ui.item).height()));
                    //}
                },
                update: function onSortableUpdate(e, ui) {
                    //console.log('CHANGE from ' + $(this).closest('tr').attr('id'));

                    // Compute best size
                    var $tr = $(this).closest('tr');
                    //resize_to_best($tr, this);
                    save_size($tr);

                    init_all();
                },
                start: function onSortableStart(e, ui) {
                    $(ui.placeholder).html('&nbsp;');
                    var $preview = $preview_container.find('.ui-widget-content.ui-state-active').parent();
                    if ($preview.length == 0) {
                        return;
                    }
                    var $field = $preview.data('field');
                    $preview.children().removeClass('ui-state-active');
                    $fields_container.find('.show_hide').hide();
                    $field.hide();
                }
            });
        }

        var $resizable;
        function init_resizable() {
            try {
                $resizable.destroy();
            } catch (e) {}

            $resizable = $preview_container.find('div.resizable').resizable({
                ghost: true,
                handles: 'se',
                autoHideType: true,
                helper: 'helper_resize',
                grid: [col_size, '2000'],
                stop: function(e, ui) {
                    var $tr = $(ui.element).closest('tr');
                    // Resize if overflowing
                    resize_to_best($tr, $(ui.element).closest('td').get(0));
                }
            });
        }

        function init_all() {
            init_resizable();
            init_sortable();

            setTimeout(refreshPreviewHeight, 10);
        }

        var focus;
        $fields_container.children('.accordion').each(function onEachFields() {
            var $field = $(this);

            on_field_added($field, {where: 'bottom'});
            if (!focus) {
                //focus = $field.data('preview');
            }
            $field.hide();
        });
        $fields_container.find('.show_hide').hide();

        $.each($layout.val().split("\n"), function() {
            var $previous = null;
            $.each(this.split(','), function() {
                var item = this.split('=');
                var field_id = item[0];
                var field_width = item[1];
                var $preview = $fields_container.find('[name="field[id][]"]').filter(function() {
                    return $(this).val() == field_id;
                }).closest('.accordion').data('preview');
                resize_to_col($preview, field_width);
                if ($previous) {
                    $previous.after($preview);
                }
                $previous = $preview;
            });
        });

        // init all the thing
        init_all();

        if (focus) {
            setTimeout(function() {
                on_focus_preview(focus);
            }, 10);
        }
    });

});
</script>

<?php
/*
?>
<div id="<?= $uniqid = uniqid('container_') ?>" style="margin: 1em 25% 1em 1%;">
    <table style="width:100%" id="drag_drop">
        <tr class="empty">
            <td colspan="1" stlye="width: 25%;">&nbsp;</td>
            <td colspan="1" stlye="width: 25%;">&nbsp;</td>
            <td colspan="1" stlye="width: 25%;">&nbsp;</td>
            <td colspan="1" stlye="width: 25%;">&nbsp;</td>
        </tr>
        <tr class="preview_row" id="ROW_1">
            <td class="preview" colspan="4">
                <label>This is a nice question:</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr class="preview_row" id="ROW_2">
            <td class="preview" colspan="4">
                <label>Another one:</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr class="preview_row" id="ROW_3">
            <td class="preview" colspan="4">
                <label>Third question:</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr class="preview_row" id="ROW_4">
            <td class="preview" colspan="4">
                <label>4th and last (but not the least):</label>
                <input type="text" value="Default" />
            </td>
        </tr>
    </table>
</div>
-->
<script type="text/javascript">
    require(['jquery-nos', 'jquery-ui.sortable', 'jquery-ui.resizable'], function($) {
        $(function() {
        });
    });
</script>
*/
