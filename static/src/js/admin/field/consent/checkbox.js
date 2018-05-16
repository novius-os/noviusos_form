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

    var debounceTimeout;

    /**
     * This function will be called each time a field meta is loaded
     */
    return function ($field, options, is_new, is_expert) {
        updatePreviewOnContentChange($field);
    };

    /**
     * Updates the preview on content change
     */
    function updatePreviewOnContentChange($field) {
        $field.nosOnShow('one', function() {
            // Delays the execution of the following code, as the wysiwyg is initialized just after this script
            setTimeout(function() {
                // Waits for the wysiwyg plugin to be loaded
                require(['jquery-nos-wysiwyg'], function($) {
                    $(function() {
                        // Waits for the editor to be fully initialized
                        setTimeout(function() {
                            // Checks if TinyMCE is loaded
                            if (typeof tinyMCE === 'undefined') {
                                console.warn('Cannot find TinyMCE.');
                                return;
                            }

                            // Gets the TinyMCE instance
                            var editor = tinyMCE.get($field.find('[name*="[field_content]"]').prop('id'));
                            if (typeof editor === 'undefined') {
                                console.warn('Cannot find the TinyMCE editor instance.');
                                return;
                            }

                            // Refreshes the preview on editor change with debouncing
                            editor.onChange.add(function() {
                                refreshPreviewWithDebounce($field);
                            });
                            editor.onKeyUp.add(function() {
                                refreshPreviewWithDebounce($field);
                            })
                        }, 100);
                    });
                });
            }, 1);
        });
    }

    /**
     * Refreshes the preview with debounce
     *
     * @param $field
     */
    function refreshPreviewWithDebounce($field) {
        if (debounceTimeout) {
            clearTimeout(debounceTimeout);
        }
        debounceTimeout = setTimeout(function() {
            console.log('refresh');
            $field.trigger('refreshPreview');
        }, 500);
    };
});
