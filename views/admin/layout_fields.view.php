<link rel="stylesheet" href="static/apps/noviusos_form/css/admin.css" />


<div id="<?= $uniqid = uniqid('container_') ?>" style="margin: 1em 25% 1em 1%;">
    <table style="width:100%" id="drag_drop">
        <tr class="fields" id="ROW_1">
            <td class="field" colspan="4">
                <label>This is a nice question:</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr class="fields" id="ROW_2">
            <td class="field" colspan="4">
                <label>Another one:</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr class="fields" id="ROW_3">
            <td class="field" colspan="4">
                <label>Third question:</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr class="fields" id="ROW_4">
            <td class="field" colspan="4">
                <label>4th and last (but not the least):</label>
                <input type="text" value="Default" />
            </td>
        </tr>
        <tr id="ROW_5">
            <td colspan="1" style="width:25%"></td>
            <td colspan="1" style="width:25%"></td>
            <td colspan="1" style="width:25%"></td>
            <td colspan="1" style="width:25%"></td>
        </tr>
    </table>
</div>

<script type="text/javascript">
require(['jquery-nos', 'jquery-ui.sortable', 'jquery-ui.droppable', 'jquery-ui.resizable'], function($) {
    $(function() {

        var col_size = Math.round($('#<?= $uniqid ?>').width() / 4);

        $('td.field').wrapInner('<div class="resizable"></div>');
        $('div.resizable').resizable({
            ghost: true,
            handles: 'se',
            autoHideType: true,
            helper: 'helper_resize',
            grid: [col_size, '2000'],
            stop: function(e, ui) {
                var $tr = $(ui.element).closest('tr');
                // Remove existing padding cell
                $tr.find('td.padding').remove();
                // Resize if overflowing
                resize_to_best($tr, ui.element);
            }
        });

        function resize_to_best($tr, priority) {
            var size = calc_size($tr);
            console.log('Sizing to best = ', size);
            _resize_to($tr, size, priority);
        }
        function resize_to_original($tr) {
            var size = $tr.data('saved_size');
            //console.log('Restoring ' + size);
            _resize_to($tr, size);
        }

        function _resize_to($tr, size, priority) {
            $tr.find('td.padding').remove();
            var total_size = 0;
            var widest = null;
            $tr.find('td.field').each(function(i) {
                total_size += parseInt(size[i]);
                if (!widest || size > $(widest).data('size') ) {
                    widest = this;
                }
                $(this).data('size', size[i]);
            });

            if (priority === undefined) {
                priority = widest;
            }

            // If total size overflow the 4 columns, we need to shrink one of the columns
            if (total_size > 4) {
                var shrink_by = $(priority).data('size') - 1;
                console.log('shrinking by = ' + shrink_by);
                var shrink_me = $(priority).closest('tr').find('.resizable').not(':data(size=1)').not(priority);
                shrink_me.data('size', shrink_me.data('size') - shrink_by);
                total_size -= shrink_by;
            }

            console.log("ACTUAL RESIZE VALUES = ", size, total_size);

            // Resize the <td> according to the new resized valued
            $tr.find('td.field').width('auto').each(function() {
                resize_to_col($(this), $(this).data('size'));
            });

            //console.log('total size  = ' + total_size);

            // Add a padding cell if necessary
            if (total_size < 4) {
                //console.log('adding a padding cell with size = ' + (4 - total_size))
                var $padding = $('<td class="padding"></td>');
                resize_to_col($padding, (4 - total_size));
                $tr.append($padding);
            }
        }

        function calc_size($tr, priority) {
            $tr.find('td.padding').remove();
            var calc_size = [];
            var max_item_size = 5 - $tr.find('.field').length;

            $tr.find('td.field').each(function() {
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
            $td.attr('colspan', colspan).css('width', Math.round(colspan * col_size) + 'px');
            $td.find('.resizable').width('auto');
        }

        var $sortable;


        function init_sortable() {
            try {
                $sortable.destroy();
            } catch (e) {}

            $sortable = $('#drag_drop');
            $sortable.find('td.padding').remove();
            // Remove empty lines
            $sortable.find('tr').removeClass('fields').filter(function() {
                return $(this).children(':not(.placeholder)').length == 0;
            }).remove();
            // Add empty lines to drop before / after (above / below) existing fields
            $sortable.find('tr').before('<tr></tr>');
            $sortable.append('<tr></tr>');

            // Allow
            $sortable.find('tr').filter(function() {
                return $(this).children().length < 4;
            }).addClass('fields');

            console.log('init size_to_fit_available');
            $sortable.find('tr.fields').each(function() {
                var $tr = $(this);
                clear_saved_size($tr);
                resize_to_best($tr);
                save_size($tr);
            });

            $sortable = $sortable.find('tr.fields').sortable({
                appendTo: '#<?= $uniqid ?>', // Where the 'helper' is appended
                connectWith: '#drag_drop tr.fields',
                helper: "clone", // This is needed when using the "appendTo" option
                dropOnEmpty: true,
                items: '> td.field',
                //forcePlaceholderSize: true,
                //forceHelperSize: true,
                placeholder: 'sortable_placeholder field',
                tolerance: 'pointer', // 'intersect' or 'pointer'
                out: function(e, ui) {
                    //console.log('OUT from ' + $(this).closest('tr').attr('id'));

                    // Restore saved size
                    var $tr = $(this).closest('tr');
                    resize_to_original($tr);
                },
                over: function(e, ui) {
                    //console.log('OUT from ' + $(this).closest('tr').attr('id'));

                    // Compute best size
                    var colspan = Math.round($(ui.helper).width() / col_size);
                    resize_to_col($(ui.placeholder), colspan);
                    var $tr = $(this).closest('tr');
                    resize_to_best($tr);
                },
                update: function(e, ui) {
                    //console.log('CHANGE from ' + $(this).closest('tr').attr('id'));

                    // Compute best size
                    var $tr = $(this).closest('tr');
                    resize_to_best($tr, this);
                    save_size($tr);

                    init_sortable();
                }
            });
        }
        init_sortable();
    });
});
</script>

<div id="<?= $uniqid = uniqid('container_') ?>">
    <p>
        <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'top'))) ?>"><?= __('Add a field') ?></button>
    </p>

    <div class="line">
        <div class="unit sortable c8" style="position:relative;">
            <div class="preview_container">

            </div>

            <p>
                <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom'))) ?>"><?= __('Add a field') ?></button>
            </p>
        </div>

        <div class="lastUnit sortable c4 fields_container">
            <?php
            foreach ($item->fields as $field) {
                echo \Request::forge('noviusos_form/admin/form/form_field')->execute(array($field));
            }
            ?>
        </div>
    </div>

    <div data-id="clone_preview" class="field_preview ui-widget-content" style="display:none;">
        <table width="100%">
            <tbody>
                <tr>
                    <td width="16">
                        <div class="handle ui-widget-header">
                            <img src="static/apps/noviusos_form/img/move-handle-dark3.png" />
                        </div>
                    </td>
                    <th width="30%"></th>
                    <td width="40%"></td>
                    <td width="30%" style="text-align: right;">
                        <button data-icon="copy" data-id="copy" class="notransform" ><?= ('Duplicate') ?></button>
                        <button data-icon="trash" data-id="delete" class="notransform" ><?= ('Delete') ?></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
require(['jquery-nos'], function($) {
    $(function() {
        var $container = $('#<?= $uniqid ?>');
        var $preview_container = $container.find('.preview_container');
        var $fields_container = $container.find('.fields_container');

        // This object will be use to generate preview
        var $clone_preview = $container.find('[data-id=clone_preview]').clone().removeAttr('data-id');
        $container.find('[data-id=clone_preview]').remove();

        function add_field(params) {
            params.where = params.where || 'bottom';
            $.ajax({
                url: 'admin/noviusos_form/form/form_field_ajax',
                complete: function(xhr) {
                    var $field = $(xhr.responseText);
                    $fields_container.append($field);
                    on_field_added($field, params);
                }
            });
        }

        function on_field_added($field, params) {
            $field.nosFormUI();

            var $preview = get_preview($field);
            $preview_container[params.where == 'top' ? 'prepend' : 'append']($preview);
            on_focus_preview($preview.get(0));
            $field.find('select[name^="field[type]"]').trigger('change');
        }

        function get_preview($field) {

            var $preview = $field.data('preview');
            if ($preview) {
                return $preview;
            }

            // Generate a new preview
            $preview = $clone_preview.clone();

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
            $preview.addClass('ui-state-active').siblings().removeClass('ui-state-active');

            // Show the appropriate field and position it
            $field.show();
            $field.nosOnShow();
            $field.siblings().hide();
            $fields_container.css({
                paddingTop: pos.top
            });
        }

        $preview_container.on('click', '.field_preview', function(e) {
            e.preventDefault();
            on_focus_preview(this);
        });

        // Add a new field when clicking the "Add" button, either at top or bottom
        $container.find('[data-id=add]').click(function add(e) {
            e.preventDefault();
            add_field($(this).data().params);

        });

        // Duplicate a field
        $preview_container.on('click', '[data-id=copy]', function(e) {
            e.preventDefault();
            // Don't bubble to .field_preview container
            e.stopPropagation();
        });

        // Delete a preview + field
        function delete_preview($preview) {
            // Focus the previous field
            var $focus = $preview.prev();
            if ($focus.length == 0) {
                // Or the next if we were the first child
                $focus = $preview.next();
            }

            var $field = $preview.data('field');
            $field.remove();
            $preview.remove();

            // Refocus another field
            if ($focus.length > 0) {
                on_focus_preview($focus);
            }
        }

        // Delete listener
        $preview_container.on('click', '[data-id=delete]', function on_delete(e) {
            e.preventDefault();
            // Don't bubble to .field_preview container
            e.stopPropagation();
            if (confirm('Are you sure?')) {
                delete_preview.call($(this).closest('.field_preview'));
            }
        });


        // When the "field_type" changes
        $fields_container.on('change', 'select[name^="field[type]"]', function on_type_change(e) {
            var type = $(this).val();
            var has_choices = !(type == 'text' || type == 'textarea' || type == 'page_break');
            var $field = $(this).closest('.accordion');

            $field.find('[name^="field[choices]"]').closest('p')[has_choices ? 'show' : 'hide']();
            $field.find('[name^="field[label]"]').trigger('change');
            $field.find('textarea[name^="field[choices]"]').trigger('change');
        });

        // When the "field_label" changes
        $fields_container.on('change keyup', 'input[name^="field[label]"]', function on_label_change(e) {
            var $field = $(this).closest('.accordion');
            var $preview = $field.data('preview');
            $preview.find('th').eq(0).text($(this).val());
        });

        // When the "field_choices" changes
        $fields_container.on('change keyup', 'textarea[name^="field[choices]"]', function on_choices_change(e) {
            var $field = $(this).closest('.accordion');
            var type = $field.find('[name^="field[type]"]').val();
            var $preview = $field.data('preview');
            var $td = $preview.find('td').eq(1);
            var html  = '';

            if (type == 'text') {
                html = '<input type="text" ∕>';
            }

            if (type == 'textarea') {
                html = '<textarea></textarea>';
            }

            if (type == 'radio') {
                $.each($(this).val().split("\n"), function(i, text) {
                    html += '<p><input type="radio" />' + text +'</p>';
                });
            }

            if (type == 'checkbox') {
                $.each($(this).val().split("\n"), function(i, text) {
                    html += '<p><input type="checkbox" />' + text +'</p>';
                });
            }

            if (type == 'select') {
                html += '<select>';
                $.each($(this).val().split("\n"), function(i, text) {
                    html += '<option>' + text +'</option>';
                });
                html += '</select>';
            }

            $td.html(html).nosFormUI();
        });



        var first = true;
        $fields_container.children().each(function() {
            var $field = $(this);

            var $preview = get_preview($field);
            $preview_container.append($preview);
            $field.find('select[name^="field[type]"]').trigger('change');
            if (first) {
                on_focus_preview($preview);
                first = false;
            }
        });

        $preview_container.sortable({
            distance: 10,
            placeholder: "ui-state-highlight"
        });
    });
});
</script>