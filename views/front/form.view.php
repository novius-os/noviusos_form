<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
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

    <?php if (!empty($errors)) {
    ?>
        <div class="form-errors">
            <?= __('Oops, it seems there are some errors.') ?>
        </div>
    <?php 
} ?>

    <form <?= array_to_attr($form_attrs) ?>>
        <input type="hidden" name="_form_id" value="<?= $form->form_id ?>" />

        <?= \View::forge(!empty($view_fields) ? $view_fields : 'noviusos_form::front/form/fields', array(
            'form' => $form,
            'errors' => $errors,
            'fieldsLayout' => $fieldsLayout,
            'labelWidthPerPage' => $labelWidthPerPage,
            'enhancer_args' => $enhancer_args,
        ), false); ?>

        <?= \View::forge(!empty($view_controls) ? $view_controls : 'noviusos_form::front/form/controls', array(
            'form' => $form,
            'pageBreakCount' => $pageBreakCount,
        ), false); ?>

        <div class="clearfix"></div>
    </form>
</div>
