<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary(array('noviusos_form::common', 'nos::common'));

?>
<link rel="stylesheet" href="<?= Uri::base(false) ?>static/apps/noviusos_form/css/admin.css" />

<?php
if (!$item->is_new() && count($item->answers) > 0) {
    echo \View::forge('noviusos_form::admin/warning_answers_collected', $view_params, false);
}
?>

<div id="<?= $uniqid = uniqid('container_') ?>">

<?php
if (!\Email::hasDefaultFrom()) {
    echo '<div class="line"><div class="col c12 ui-state-error" style="padding:0.5em;">',
        __(
            'You have a problem here: Your Novius OS is not set up to send emails. '.
            'You’ll have to ask your developer to set it up for you.'
        ),
        '</div></div>';
}
if (!$item->is_new()) {
    $count = \Nos\Model_Wysiwyg::count(array(
        'where' => array(
            array('wysiwyg_text', 'LIKE', '%&quot;form_id&quot;:&quot;'.$item->form_id.'%'),
        ),
    ));
    if ($count == 0) {
        echo \View::forge('noviusos_form::admin/warning_not_published', $view_params, false);
    }
}
?>
    <div class="line">
        <div class="col c8" style="position:relative;">
            <p style="height: 40px;">
                <button type="button" data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'top'))) ?>"><?= __('Add a field') ?></button>
            </p>

            <div class="field_blank_slate ui-widget-content" style="display:none;">
                <table style="width: 100%;">
                    <tr>
                        <th><?= __('Standard fields'); ?></th>
                        <th><?= __('Special fields'); ?></th>
                    </tr>
                    <tr>
<?php
foreach (array('standard', 'special') as $type) {
    ?>
                        <td style="width: 50%">
    <?php
    foreach (\Config::get('noviusos_form::controller/admin/form.fields_meta.'.$type) as $type => $meta) {
        if (!empty($meta['expert']) && !\Session::user()->user_expert) {
            continue;
        }
        ?>
        <p><label data-meta="<?= $type ?>"><img src="<?= $meta['icon'] ?>" /> <?= $meta['title'] ?></label></p>
        <?php
    }
    ?>
                        </td>
    <?php
}
?>
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
                <p class="form_captcha"><?= __('Help us prevent spam: How much is 3 plus 8?') ?> <input size="3" /></p>
                <p class="form_submit_label"><input type="button" /></p>
            </div>
        </div>

        <div class="col c4 fields_container" style="display:none;">
            <p class="actions show_hide" style="text-align: left;">
                <button type="button" data-icon="trash" data-id="delete" class="action"><?= __('Delete') ?></button>
                <?php /*<button type="button" data-icon="copy" data-id="copy" class="action"><?= __('Duplicate') ?></button> */ ?>
                <img class="preview_arrow show_hide" src="static/apps/noviusos_form/img/arrow-edition.png" />
            </p>
<?php
$layout = explode("\n", $item->form_layout);
array_walk($layout, function (&$v) {
    $v = explode(',', $v);
});
$layout = \Arr::flatten($layout);
// Remove empty values
$layout = array_filter($layout);
array_walk($layout, function (&$v) {
    $v = explode('=', $v);
    $v = $v[0];
});
foreach ($layout as $field_id) {
    echo \Request::forge('noviusos_form/admin/form/render_field')->execute(array($item->fields[$field_id]));
}
?>
            <div class="accordion field_enclosure fieldset">
                <h3><?= __('Form submission') ?></h3>
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
            init_form(uniqid, <?= \Format::forge()->to_json(array(
                'textDelete' => __('Are you sure?'),
            )) ?>,<?= $crud['is_new'] ? 'true' : 'false'; ?>, <?= \Session::user()->user_expert ? 'true' : 'false' ?>);
            $(uniqid).find('.preview_container').loadspinner('destroy');
        });
    });
});
</script>
