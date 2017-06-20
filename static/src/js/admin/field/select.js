/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
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
        var $choices = getFieldProperty($field, 'field_choices');

        // Updates the preview on choices change
        $choices.on('blur change keyup', generateFieldDefaultValue);

        generateFieldDefaultValue();

        function generateFieldDefaultValue() {
            // Gets the default value
            var $default_value = getFieldProperty($field, 'field_default_value');
            var default_value_value = $default_value.val();

            // Creates the select
            var $new = $('<select />').attr({
                name: $default_value.attr('name'),
                id: $default_value.attr('id')
            });

            // Creates the options
            $.each($choices.val().split("\n"), function(index, choice) {
                // @todo do it better
                var value = index;
                var parts = choice.split("=");
                if (parts.length > 1) {
                    value = parts.pop();
                }
                var text = parts.join('=');
                $new.append(
                    $('<option value="' + value + '" ' + (default_value_value == value ? 'selected' : '') + '>' + text + '</option>')
                );
            });

            // Append to DOM
            var $parent = $default_value.closest('span');
            $parent.html($new).nosFormUI();
        }
    };

    function getFieldProperty($context, field_name) {
        return $context.find('[name*="[' + field_name + ']"]');
    }
});
