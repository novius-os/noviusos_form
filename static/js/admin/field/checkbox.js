/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define(['jquery-nos'], function($) {
    "use strict";

    /**
     * This function will be called each time a field meta is loaded
     */
    return function ($field, options, is_new, is_expert) {

        var field_driver = getFieldProperty($field, 'field_driver').val();
        var type = getFieldProperty($field, 'field_type').val();
        var $default_value = getFieldProperty($field, 'field_default_value');
        var choices = getFieldProperty($field, 'field_choices').val();

        // Updates the preview on choices change
        $field.on('blur', 'textarea[name$="[field_choices]"]', generateFieldDefaultValue());

        generateFieldDefaultValue();

        function generateFieldDefaultValue() {

            // Gets the default value
            var default_value_value = $default_value.val();
            if (default_value_value.match(/^[0-9,]+$/)) {
                default_value_value = default_value_value.split(',');
            } else {
                default_value_value = default_value_value.split("\n")
            }

            // Creates the checkboxes
            var $new = $(
                $('<div />').css({ paddingLeft: 6, borderLeft: '1px solid #aaa', margin: '5px 0 5px 2px' }).html(
                    '<input type="hidden" size="1" name="' + $default_value.attr('name') + '" value=""  />'
                    + $.map(choices.split("\n"), function(choice, i) {
                        return '<label><input type="checkbox" class="checkbox" size="1" value="' + i + '" ' + (-1 !== $.inArray(choice, default_value_value) ? 'checked' : '') + '> ' + choice + '</label><br />';
                    }).join('')
                )
            );

            // Append to DOM
            var $parent = $default_value.closest('span');
            $parent.empty().append($new).nosFormUI();

            // Updates preview on change
            var $checkboxes = $new.find('input.checkbox');
            $checkboxes.on('change', function(e) {
                var value = [];
                $checkboxes.filter(':checked').each(function() {
                    value.push($(this).val());
                });
                $new.find('input[type="hidden"]:first').val(value.join(','));
                // Event doesn't seems to trigger with the hidden field on a delegated handler
                $field.trigger('refreshPreview');
            });
            $checkboxes.first().trigger('change');
        }
    };

    function getFieldProperty($context, field_name) {
        return $context.find('[name*="[' + field_name + ']"]');
    }
});
