<?php
/**
 * @var \Nos\Form\Model_Form $form
 * @var array $fieldsLayout
 * @var array $labelWidthPerPage
 * @var array $errors
 * @var array $enhancer_args
 */

// Gets the current wizard page (the first page with an error)
$current_page = \Nos\Form\Helper_Front_Form::getFirstErrorPage($fieldsLayout, $errors);

// Displays the fields
foreach ($fieldsLayout as $page => $rows) {
    ?>
    <div class="form-page<?= $current_page === $page ? ' current' : '' ?>">
        <?php foreach ($rows as $cols) { ?>
            <div class="row">
                <?php
                foreach ($cols as $field) {
                    // Builds the error
                    $fieldError = \Nos\Form\Helper_Front_Form::renderFieldError(\Arr::get($errors, $field['name']));

                    // Builds the label and field class name
                    $labelClass = $fieldClass = 'twelve';
                    if (!in_array($enhancer_args['label_position'], array('top', 'placeholder'))) {
                        $labelClass = \Nos\Form\Helper_Front_Form::getWidthClassName($labelWidthPerPage[$page] * 3);
                        if ($labelWidthPerPage[$page] < 4) {
                            $fieldClass = $labelClass = \Nos\Form\Helper_Front_Form::getWidthClassName(12 - ($labelWidthPerPage[$page] * 3));
                        }
                    }

                    // Builds the template
                    $template = \Arr::get($field, 'template');
                    if (isset($field['template'])) {
                        $template = $field['template'];
                    } else {
                        if (in_array($enhancer_args['label_position'], array('left', 'right'))) {
                            $template = '
                                <div class="twelve columns">
                                    <span class="inline-label">{label}</span>
                                    <span class="inline-field" >{field} {instructions}</span>
                                    <span class="inline-field-error" >{field_error}</span>
                                </div>
                            ';
                        } else {
                            $template = '';
                            if (!in_array($enhancer_args['label_position'], array('top', 'placeholder')) || !empty($field['label'])) {
                                // Displays the label only if not empty
                                $template .= '<div class="{label_class} columns form-label">{label}</div>';
                            }
                            $template .= '<div class="{field_class} columns form-field">{field} {instructions}</div>';
                            if (!empty($fieldError)) {
                                $template .= '<div class="twelve columns form-field-error">{field_error}</div>';
                            }
                        }
                    }
                    ?>
                    <div class="columns <?= \Nos\Form\Helper_Front_Form::getWidthClassName($field['width'] * 3) ?>">
                        <div class="nos_form_field label-position-<?= $enhancer_args['label_position'] ?>">
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
