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
    <div class="form-page form-fields-group<?= $current_page === $page ? ' current' : '' ?>">
        <?php foreach ($rows as $cols) { ?>
            <div class="row">
                <?php
                foreach ($cols as $field) {
                    // Builds the error
                    $fieldError = \Nos\Form\Helper_Front_Form::renderFieldError(\Arr::get($errors, $field['name']));

                    // Builds the label and field class name
                    $labelClass = $fieldClass = 'twelve';

                    // Builds the template
                    $template = \Arr::get($field, 'template');
                    if (isset($field['template'])) {
                        $template = $field['template'];
                    } else {
                        $template = '';
                        if (!empty($field['label'])) {
                            // Displays the label only if not empty
                            $template .= '<div class="{label_class} columns form-field-label">{label}</div>';
                        }
                        $template .= '<div class="{field_class} columns form-field-input">{field} {instructions}</div>';
                        if (!empty($fieldError)) {
                            $template .= '<div class="twelve columns form-field-error">{field_error}</div>';
                        }
                    }
                    ?>
                    <div class="columns <?= \Nos\Form\Helper_Front_Form::getWidthClassName($field['width'] * 3) ?>">
                        <div class="nos_form_field label-position-top"
                             id="<?= $field['uniqid'] ?>">
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
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
