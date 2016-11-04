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
        $field.on('blur change', 'textarea[name$="[field_choices]"]', generateFieldDefaultValue);

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
                $('<select />').html(
                    '<option value=""></option>'
                    + $.map(choices.split("\n"), function(choice, i) {
                        var pair = choice.split("=", 2);
                        var value = pair.length == 2 ? pair[1] : i + '';
                        var text = pair[0];
                        return '<option value="' + value + '" ' + (default_value_value[0] == value ? 'selected' : '') + '>' + text + '</option>';
                    }).join('')
                )
            ).attr({
                name: $default_value.attr('name'),
                id: $default_value.attr('id')
            });

            // Append to DOM
            var $parent = $default_value.closest('span');
            $parent.empty().append($new).nosFormUI();
        }
    };

    function getFieldProperty($context, field_name) {
        return $context.find('[name*="[' + field_name + ']"]');
    }
});
