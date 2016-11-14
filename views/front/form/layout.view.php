<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

\Nos\Nos::main_controller()->addCss('static/apps/noviusos_form/dist/css/front/form.min.css');
\Nos\Nos::main_controller()->addJavascript('static/apps/noviusos_form/dist/js/front/form.min.js');

\Nos\I18n::current_dictionary('noviusos_form::front');

// Sets the form class name
\Arr::set($form_attrs, 'class', \Arr::get($form_attrs, 'class').' nos-form-layout');

// Sets the form locale
$context = \Nos\Nos::main_controller()->getContext();
if (!empty($context)) {
    $form_attrs['data-locale'] = \Nos\Form\Helper_Front_Form::getParsleyLocale($context);
}

$widths = array(
    0 => '',
    1 => 'one',
    2 => 'two',
    3 => 'three',
    4 => 'four',
    5 => 'five',
    6 => 'six',
    7 => 'seven',
    8 => 'eight',
    9 => 'nine',
    10 => 'ten',
    11 => 'eleven',
    12 => 'twelve',
);

// Gets the minimum label width for each page
$label_width = array();
foreach ($fieldsLayout as $page => $rows) {
    foreach ($rows as $cols) {
        foreach ($cols as &$field) {
            // Label width will be set according to the smallest columns
            $label_width[$page] = isset($label_width[$page]) ? min($label_width[$page], $field['width']) : $field['width'];
        }
    }
}

?>
<div class="noviusos_form noviusos_enhancer" id="<?= $id = uniqid('form_') ?>">

    <?php
    // Errors
    foreach ($errors as $name => $error) {
        $id = '';
        $fieldItem = \Arr::get($fields, $name);
        $fieldDriver = !empty($fieldItem) ? $fieldItem->getDriver() : null;
        if (!empty($fieldDriver) && $fieldDriver->getVirtualName() === $name) {
            $id = $fieldDriver->getHtmlId();
        }
        ?>
        <p class="error">
            <label for="<?= $id ?>">
                <?= nl2br(htmlspecialchars($error)) ?>
            </label>
        </p>
        <?php
    }
    ?>

    <form <?= array_to_attr($form_attrs) ?>>
        <input type="hidden" name="_form_id" value="<?= $item->form_id ?>" />

        <?php foreach ($fieldsLayout as $page => $rows) { ?>
            <div class="page_break">
                <?php foreach ($rows as $cols) { ?>
                    <div class="row">
                        <?php foreach ($cols as $field) { ?>
                            <div class="columns <?= $widths[$field['width'] * 3] ?>">
                                <div class="nos_form_field label-position-<?= $enhancer_args['label_position'] ?>">
                                    <?= \View::forge('noviusos_form::front/form/layout/field', array(
                                        'item' => $item,
                                        'field' => $field,
                                        'errors' => $errors,
                                        'widths' => $widths,
                                        'enhancer_args' => $enhancer_args,
                                    ), false) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($page_break_count > 0) { ?>
            <div class="page_break_control row">
                <?= strtr(__('{{previous}}{{pagination}}{{next}}'), array(
                    '{{previous}}' => '
                        <div class="columns four">
                            <a class="page_break_previous" href="#">'.__('Previous page').'</a>
                        </div>
                    ',
                    '{{next}}' => '
                        <div class="columns four">
                            <button type="button" class="page_break_next">'.__('Next page').'</button>'.
                            \Form::submit('submit', $item->form_submit_label, array(
                                'class' => 'page_break_last',
                            )).'
                        </div>
                    ',
                    '{{pagination}}' => '
                        <div class="columns four"> <progress id="progress" value="1" max="'.($page_break_count + 1).'"></progress> '.
                            strtr(__('{{current}} out of {{total}}'), array(
                                '{{current}}' => '<span class="page_break_current">1</span>',
                                '{{total}}' => '<span class="page_break_total">'.($page_break_count + 1).'</span>',
                            )).'
                        </div>
                    ',
                )); ?>
            </div>
            <?php
        } else {
            echo \Form::submit('submit', $item->form_submit_label);
        }
        ?>
    </form>
</div>
