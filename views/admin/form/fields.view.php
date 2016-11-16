<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

/**
 * @var $item \Nos\Form\Model_Form
 */

Nos\I18n::current_dictionary(array('noviusos_form::common', 'nos::common'));

$config = \Config::load('noviusos_form::config', true);

// Gets the available fields layouts
$available_fields_layouts = \Arr::get($config, 'available_fields_layouts', array());

// Gets the available fields drivers
$available_fields_drivers = \Arr::get($config, 'available_fields_drivers', array());

// Gets the form layout
$formLayoutFieldsName = $item->getService()->getLayoutFieldsName();

// Builds the drivers config
$driversConfig = array();
foreach ($available_fields_drivers as $driverClass) {
    $driversConfig[$driverClass] = array(
        'name' => $driverClass::getName(),
        'config' => $driverClass::getConfig(),
    );
}

// Displays a warning dialog if answers already collected
if (!$item->is_new() && count($item->answers) > 0) {
    echo \View::forge('noviusos_form::admin/form/warning_answers_collected', $view_params, false);
}

?>

<div id="<?= $uniqid = uniqid('container_') ?>" class="nos-form-layout-fields" style="margin: 0 -1em;">
    <div class="line">
        <div class="col c8" style="position:relative;">

            <div class="form-fields-actions top">
                <span class="button-container">
                    <button type="button" data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'top'))) ?>"><?= __('Add a field') ?></button>
                </span>
            </div>

            <?= \View::forge('noviusos_form::admin/form/fields_blank_slate', array(
                'layouts' => $available_fields_layouts,
            ), false) ?>

            <div class="form_previews">

                <table class="form_preview">
                    <colgroup>
                        <!-- 4 even columns -->
                        <col />
                        <col />
                        <col />
                        <col />
                    </colgroup>
                    <tbody class="preview_container">
                        <?php
                        $page_break_count = 0;
                        foreach ($formLayoutFieldsName as $field_name) {
                            // Page break
                            if ($field_name === 'page_break') {
                                $page_break_count++;
                                ?>
                                <tr class="preview_row page_break">
                                    <?= \View::forge('noviusos_form::admin/form/field_preview', array(
                                        'className' => 'page_break ui-widget-header',
                                        'content' => '',
                                        'data_field_id' => 'page-break-'.$page_break_count,
                                        'page_break_count' => $page_break_count,
                                    ), false); ?>
                                </tr>
                                <?php
                            }
                            // Field
                            elseif (isset($item->fields[$field_name])) {
                                ?>
                                <tr class="preview_row">
                                    <?= \View::forge('noviusos_form::admin/form/field_preview', array(
                                        'content' => \Nos\Form\Service_Field::forge($item->fields[$field_name])->getPreviewHtml(),
                                        'data_field_id' => $field_name,
                                        'page_break_count' => $page_break_count,
                                    ), false); ?>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <div class="ui-widget-content submit_informations" style="display:none;">
                    <p class="form_captcha"><?= __('Help us prevent spam: How much is 3 plus 8?') ?> <input size="3" /></p>
                    <p class="form_submit_label"><input type="button" /></p>
                </div>
            </div>

            <div class="form-fields-actions bottom">
                    <span class="button-container">
                        <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom'))) ?>"><?= __('Add a field') ?></button>
                    </span>
                <span class="button-container">
                        <button data-icon="plus" data-id="add" data-params="<?= e(json_encode(array('where' => 'bottom', 'type' => 'page_break'))) ?>"><?= __('Add a page break') ?></button>
                    </span>
            </div>
        </div>

        <div class="col c4 fields_container" style="display:none;">
            <p class="actions show_hide" style="text-align: left;">
                <button type="button" data-icon="trash" data-id="delete" class="action"><?= __('Delete') ?></button>
                <?php /*<button type="button" data-icon="copy" data-id="copy" class="action"><?= __('Duplicate') ?></button> */ ?>
                <img class="preview_arrow show_hide" src="static/apps/noviusos_form/img/arrow-edition.png" />
            </p>
            <?php
            $page_break_count = 0;
            foreach ($formLayoutFieldsName as $field_name) {
                // Page break
                if ($field_name === 'page_break') {
                    ?>
                    <div class="field_enclosure page_break" data-field-id="page-break-<?= ++$page_break_count ?>"></div>
                    <?php
                }
                // Field
                elseif (isset($item->fields[$field_name])) {
                    echo \Request::forge('noviusos_form/admin/form/render_field_meta')->execute(array($item->fields[$field_name]));
                }
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
                        <div class="preview_content"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
?>

<script type="text/javascript">
require([
    'jquery-nos',
    'jquery-nos-loadspinner',
    'link!static/apps/noviusos_form/dist/css/admin/form.min.css'
], function($) {
    var uniqid = '#<?= $uniqid ?>';
    $(uniqid).find('.preview_container').loadspinner({
        diameter : 64,
        scaling : true
    });
    require(['static/apps/noviusos_form/dist/js/admin/insert_update.min.js?update=20161102'], function(init_form) {
        $(function() {
            init_form(uniqid, <?= \Format::forge()->to_json(array(
                'textDelete' => __('Are you sure?'),
                'driversConfig' => $driversConfig,
            )) ?>,<?= $crud['is_new'] ? 'true' : 'false'; ?>, <?= \Session::user()->user_expert ? 'true' : 'false' ?>);
            $(uniqid).find('.preview_container').loadspinner('destroy');
        });
    });
});
</script>
