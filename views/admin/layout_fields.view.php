<link rel="stylesheet" href="static/apps/noviusos_form/css/admin.css" />

<?php
if (!$item->is_new() && count($item->answers) > 0) {
    echo \View::forge('noviusos_form::admin/warning_answers_collected', $view_params, false);
}
?>

<div id="<?= $uniqid = uniqid('container_') ?>">

    <?php
        if (!$item->is_new()) {
            $count = \Nos\Model_Wysiwyg::count(array(
                'where' => array(
                    array('wysiwyg_text', 'LIKE', '%&quot;form_id&quot;:&quot;'.$item->form_id.'%'),
                ),
            ));
            if ($count == 0) {
                ?>
                <div class="line" style="margin-bottom: 1em;"">
                    <div class="unit col c12 lastUnit" style="position:relative;">
                        <img src="static/novius-os/admin/novius-os/img/icons/status-red.png" style="vertical-align: middle;" />
                        <?= __('Not published') ?>.
                        <?= __('To publish it, insert the form into a page, blog post or any other content area.') ?>
                    </div>
                </div>
                <?php
            }
        }
    ?>
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

            <table class="form_preview">
                <colgroup>
                    <!-- 4 even columns -->
                    <col />
                    <col />
                    <col />
                    <col />
                </colgroup>
                <tbody class="preview_container">

                </tbody>
            </table>
            <p>
                <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom'))) ?>"><?= __('Add a field') ?></button>
                <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom', 'type' => 'page_break'))) ?>"><?= __('Add a page break') ?></button>
            </p>
            <div class="ui-widget-content submit_informations" style="display:none;">
                <p class="form_captcha">How much is 3 + 8? <input size="3" /></p>
                <p clas="form_submit_label"><input type="button" /></p>
            </div>
        </div>

        <div class="lastUnit col c4 fields_container" style="display:none;">
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
// Remove empty values
$layout = array_filter($layout);
array_walk($layout, function(&$v) {
    $v = explode('=', $v);
    $v = $v[0];
});
foreach ($layout as $field_id) {
    echo \Request::forge('noviusos_form/admin/form/render_field')->execute(array($item->fields[$field_id]));
}
?>
            <div class="accordion field_enclosure fieldset">
                <h3><?= __('Submit informations') ?></h3>
                <div>
                    <?= $fieldset->field('form_captcha')->set_template('<p><span>{label} {field}</span></p>') ?>
                    <?= $fieldset->field('form_submit_label')->set_template('<p><span>{label}<br />{field}</span></p>') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="field_preview ui-widget-content" style="display:none;">
        <table width="100%">
            <tbody>
            <tr class="preview_row" data-id="clone_preview">
                <td class="preview ui-widget-content" colspan="4">
                    <div class="resizable">
                        <div class="handle ui-widget-header">
                            <img src="static/apps/noviusos_form/img/move-handle-dark3.png" />
                        </div>
                        <label class="preview_label"></label>
                        <div class="preview_content">

                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
require(['jquery-nos', 'jquery-nos-loadspinner'], function($) {
    var uniqid = '#<?= $uniqid ?>';
    $(uniqid).find('.preview_container').loadspinner({
        diameter : 64,
        scaling : true
    });
    require(['static/apps/noviusos_form/js/admin/insert_update.js'], function(init_form) {
        $(function() {
            init_form(uniqid);
            $(uniqid).find('.preview_container').loadspinner('destroy');
        });
    });
});
</script>