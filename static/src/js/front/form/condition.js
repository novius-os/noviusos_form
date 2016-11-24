/**
 * The form condition script
 */
(function () {

    /**
     * Constructor
     *
     * @param $form
     * @param settings
     * @constructor
     */
    function NosFormCondition($form, settings) {
        this.$form = $form;
        this.settings = $.extend(true, {
            fields: {},
        }, settings);
    }

    /**
     * Initializes the conditional fields
     */
    NosFormCondition.prototype.init = function () {
        if (typeof this.settings.fields === 'object') {
            for (var fieldName in this.settings.fields) {
                if (this.settings.fields.hasOwnProperty(fieldName)) {
                    this.initField(fieldName, this.settings.fields[fieldName]);
                }
            }
        }
    };

    /**
     * Initializes the field with the specified settings
     */
    NosFormCondition.prototype.initField = function (fieldName, fieldSettings) {
        var self = this;

        // Gets the target field
        var $field = this.$form.find('[name="' + fieldName + '"]');
        if (!$field.length) {
            $field = this.$form.find('[data-id-field="' + fieldName + '"]');
        }
        if (!$field.length) {
            if (console) console.warn('NosFormCondition: target field `' + fieldName + '` not found.');
            return false;
        }

        // Gets the observed field
        if (!fieldSettings.observedFieldName) {
            if (console) console.warn('NosFormCondition: missing `observedFieldName` in fields settings.');
            return false;
        }
        var $observedField = this.$form.find('[name^="' + fieldSettings.observedFieldName + '"]');
        if (!$observedField.length) {
            if (console) console.warn('NosFormCondition: observed field `' + fieldSettings.observedFieldName + '` not found.');
            return false;
        }

        // Initializes the required data attribute on the target field
        if ($field.is('input, select, textarea') && typeof $field.data('required') === 'undefined') {
            if (typeof $field.attr('required') === 'undefined') {
                $field.data('required', '');
            } else {
                $field.data('required', $field.attr('required'));
            }
        } else {
            // Multiple elements
            $field.parent().find('input, select, textarea').each(function () {
                if (typeof $(this).attr('required') === 'undefined') {
                    $(this).data('required', '');
                } else {
                    $(this).data('required', $(this).attr('required'));
                }
            });
        }

        // Updates the target field state when the observable field changes
        $observedField.on('init keyup input change', function () {
            self.updateFieldState($field, $observedField, fieldSettings);
        }).trigger('init');

        return true;
    };

    /**
     * Updates the field state according to the observed field value
     *
     * @param $field
     */
    NosFormCondition.prototype.updateFieldState = function ($field, $observedField, fieldSettings) {
        // Checks if the observed field matches the expected value
        if (this.checkFieldMatchValue($observedField, fieldSettings.observedFieldValue)) {
            // Displays the target field
            $field.closest('.nos_form_field').removeClass('nos_form_disabled').addClass('nos_form_enabled');
            if ($field.is('input, select, textarea') && $field.data('required') != '') {
                $field.attr('required', 'required');
            } else {
                // Multiple elements
                $field.parent().find('input, select, textarea').each(function () {
                    if ($(this).data('required') != '') {
                        $(this).attr('required', 'required');
                    }
                });
            }
        } else {
            // Hides the target field
            $field.closest('.nos_form_field').addClass('nos_form_disabled').removeClass('nos_form_enabled');
            if ($field.is('input, select, textarea') && $field.data('required') != '') {
                $field.removeAttr('required');
            } else {
                $field.parent().find('input, select, textarea').removeAttr('required');
            }
        }
    };

    /**
     * Checks if the specified field matches the expected value
     *
     * @param $field
     */
    NosFormCondition.prototype.checkFieldMatchValue = function ($field, expectedValue) {
        if ($field.is('input[type="radio"], input[type="checkbox"]')) {
            return $field.filter('[value="' + expectedValue + '"]:checked').length > 0;
        } else {
            return $field.val() == expectedValue;
        }
    };

    window.NosFormCondition = NosFormCondition;
})();
