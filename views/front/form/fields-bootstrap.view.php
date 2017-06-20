<?php
/**
 * @var \Nos\Form\Model_Form $form
 * @var array                $fieldsLayout
 * @var array                $labelWidthPerPage
 * @var array                $errors
 * @var array                $enhancer_args
 */

// Gets the current wizard page (the first page with an error)
$current_page = \Nos\Form\Helper_Front_Form::getFirstErrorPage($fieldsLayout, $errors);

// Displays the fields
foreach ($fieldsLayout as $page => $rows) {
    ?>
    <div class="form-fields-group fieldset<?= $current_page === $page ? ' current' : '' ?>">
        <?php foreach ($rows as $cols) {
        ?>
            <div class="row">
                <?php
                foreach ($cols as $field) {
                    // Builds the error
                    $fieldError = \Nos\Form\Helper_Front_Form::renderFieldError(\Arr::get($errors, $field['name']));

                    // Builds the label and field class name
                    $labelClass = '';
                    $fieldClass = 'form-group col-xs-12 col-sm-12';

                    // Builds the template
                    $template = \Arr::get($field, 'template');
                    if (isset($field['template'])) {
                        $template = $field['template'];
                    } else {
                        $template = '';
                        if (!empty($field['label'])) {
                            // Displays the label only if not empty
                            $template .= '{label}';
                        }
                        $template .= '{field} {instructions}';
                        if (!empty($fieldError)) {
                            $template .= '<div class="form-field-error">{field_error}</div>';
                        }
                    } ?>
                    <div class="col-xs-12 col-sm-<?= \Arr::get($field, 'width') * 3 ?>">
                        <div class="nos_form_field form-group" id="<?= $field['uniqid'] ?>">
                            <?= \Nos\Form\Helper_Front_Form::renderField($form, $field, $template, array(
                                'labelClass'    => $labelClass,
                                'fieldClass'    => $fieldClass,
                                'fieldError'    => $fieldError,
                                'errors'        => $errors,
                                'page'          => $page,
                                'enhancer_args' => $enhancer_args,
                            )) ?>
                        </div>
                    </div>
                <?php 
                } ?>
            </div>
        <?php 
    } ?>
    </div>
<?php 
} ?>
