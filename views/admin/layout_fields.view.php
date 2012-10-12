<link rel="stylesheet" href="static/apps/noviusos_form/css/admin.css" />


<div id="<?= $uniqid = uniqid('container_') ?>" style="margin: 1em 25% 1em 1%;">
    <table width="100%" id="drag_drop">
        <tr class="fields">
            <td class="droppable">&nbsp;</td>
            <td class="draggable" colspan="7">
                <label>This is a nice question:</label>
                <input type="text" value="Default" />
            </td>
            <td class="droppable">&nbsp;</td>
        </tr>
        <tr class="fields">
            <td class="droppable">&nbsp;</td>
            <td class="draggable" colspan="7" id="test">
                <label>Another one:</label>
                <input type="text" value="Default" />
            </td>
            <td class="droppable">&nbsp;</td>
        </tr>
        <tr class="fields">
            <td class="droppable">&nbsp;</td>
            <td class="draggable" colspan="7" id="test">
                <label>Thrid question:</label>
                <input type="text" value="Default" />
            </td>
            <td class="droppable">&nbsp;</td>
        </tr>
        <tr class="fields">
            <td class="droppable">&nbsp;</td>
            <td class="draggable" colspan="7" id="test">
                <label>4tha nd last (but not the least):</label>
                <input type="text" value="Default" />
            </td>
            <td class="droppable">&nbsp;</td>
        </tr>
        <tr class="fields">
            <td class="droppable" colspan="9">&nbsp;</td>
        </tr>
        <tr class="" style="visibility: hidden;">
            <td class="">&nbsp;</td>
            <td class="draggable">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="draggable">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="draggable">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="draggable">&nbsp;</td>
            <td class="">&nbsp;</td>
        </tr>
    </table>
</div>

<script type="text/javascript">
require(['jquery-nos', 'jquery-ui.draggable', 'jquery-ui.droppable', 'jquery-ui.resizable'], function($) {
    $(function() {
        $('td.draggable').draggable({
            revert: 'invalid',
            helper: 'clone'
        });

        $('td.draggable').wrapInner('<div class="resizable"></div>');
        $('div.resizable').resizable({
            ghost: true,
            handles: 'se',
            autoHideType: true,
            helper: 'helper_resize',
            grid: [$('#<?= $uniqid ?>').width() / 4, '2000']
        });

        var $droppable;

        function init_droppable() {
            try {
                $droppable.destroy();
            } catch (e) {}

            $droppable = $('#drag_drop .droppable').droppable({
                activeClass: 'active',
                hoverClass: 'hover',
                accept: 'td.draggable',
                tolerance: 'pointer',
                drop: function on_drop(e, ui) {
                    var $item = $(ui.draggable);
                    var $new_row = $(this).closest('tr.fields');
                    var $old_row = $item.closest('tr.fields');

                    console.log($new_row);
                    console.log($new_row.children());
                    console.log($new_row.children().length);
                    console.log($new_row.clone());


                    if ($new_row.children().length == 1) {
                        $new_row.after($new_row.clone());
                    }



                    $(this).after($item.add($item.next()));

                    // When dragging from one line to another one
                    //if ($new_row.get(0) != $old_row.get(0)) {

                        if ($old_row.find('td.draggable:not(.ui-draggable-dragging)').length == 0) {
                            $old_row.remove();
                        }
                    //}

                    var $draggable = $new_row.find('td.draggable:not(.ui-draggable-dragging)');
                    $new_row.find('td.droppable').attr('colspan', 1);

                    if ($draggable.length == 1) {
                        $draggable.attr('colspan', 7);
                    }
                    if ($draggable.length == 2) {
                        $draggable.attr('colspan', 3);
                    }
                    if ($draggable.length == 3) {
                        $draggable.attr('colspan', 1).eq(0).attr('colspan', 3);
                    }
                    if ($draggable.length == 4) {
                        $draggable.attr('colspan', 1);
                    }
                    init_droppable();
                }
            });
        }
        init_droppable();
    });
});
</script>

<div id="<?= $uniqid = uniqid('container_') ?>">
    <p>
        <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'top'))) ?>"><?= __('Add a field') ?></button>
    </p>

    <div class="line">
        <div class="unit draggable c8" style="position:relative;">
            <div class="preview_container">

            </div>

            <p>
                <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom'))) ?>"><?= __('Add a field') ?></button>
            </p>
        </div>

        <div class="lastUnit draggable c4 fields_container">
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