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
 * @var \Nos\Form\Model_Form $form
 * @var array $fields
 * @var array $fieldsLayout
 * @var array $labelWidthPerPage
 * @var array $errors
 * @var array $enhancer_args
 * @var array $form_attrs
 */

\Nos\I18n::current_dictionary('noviusos_form::front');

// Injects the stylesheet
if (!empty($stylesheet_url)) {
    \Nos\Nos::main_controller()->addCss($stylesheet_url);
}

// Injects the script
if (!empty($script_url)) {
    Nos\Nos::main_controller()->addJavascript($script_url);
}

// Sets the form class name
\Arr::set($form_attrs, 'class', \Arr::get($form_attrs, 'class').' nos-form-layout');

// Counts the page breaks in the form layout
$pageBreakCount = $form->getService()->getPageBreakCount();

?>
<div class="noviusos_form noviusos_enhancer" id="<?= $id = uniqid('form_') ?>">

    <?php
    $current_page = 0;
    if (!empty($errors)) {
        ?>
        <div class="form-errors">
            <?= __('Oops, it seems there are some errors.') ?>
        </div>
        <?php
        // Gets the first error field page
        foreach ($errors as $name => $error) {
            foreach ($fieldsLayout as $page => $rows) {
                foreach ($rows as $cols) {
                    foreach ($cols as $field) {
                        if ($field['name'] === $name) {
                            $current_page = $page;
                            break 4;
                        }
                    }
                }
            }
        }
    }
    ?>

    <form <?= array_to_attr($form_attrs) ?>>
        <input type="hidden" name="_form_id" value="<?= $form->form_id ?>" />

        <?php foreach ($fieldsLayout as $page => $rows) { ?>
            <div class="page_break<?= $current_page === $page ? ' current' : '' ?>">
                <?php foreach ($rows as $cols) { ?>
                    <div class="row">
                        <?php foreach ($cols as $field) { ?>
                            <div class="columns <?= \Nos\Form\Helper_Front_Form::getWidthClassName($field['width'] * 3) ?>">
                                <div class="nos_form_field label-position-<?= $enhancer_args['label_position'] ?>">
                                    <?= \View::forge('noviusos_form::front/form/field', array(
                                        'form' => $form,
                                        'field' => $field,
                                        'errors' => $errors,
                                        'labelWidth' => $labelWidthPerPage,
                                        'page' => $page,
                                        'enhancer_args' => $enhancer_args,
                                    ), false) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($pageBreakCount > 0) { ?>
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
                            \Form::submit('submit', $form->form_submit_label, array(
                                'class' => 'page_break_last',
                            )).'
                        </div>
                    ',
                    '{{pagination}}' => '
                        <div class="columns four"> <progress id="progress" value="1" max="'.($pageBreakCount + 1).'"></progress> '.
                            strtr(__('{{current}} out of {{total}}'), array(
                                '{{current}}' => '<span class="page_break_current">1</span>',
                                '{{total}}' => '<span class="page_break_total">'.($pageBreakCount + 1).'</span>',
                            )).'
                        </div>
                    ',
                )); ?>
            </div>
            <?php
        } else {
            echo \Form::submit('submit', $form->form_submit_label);
        }
        ?>
    </form>
</div>
