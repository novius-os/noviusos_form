/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
define(
    [
        'jquery-nos'
    ],
    function($) {
        "use strict";

        $.fn.extend({
            appFormEnhancerPopup : function() {
                return this.each(function() {
                    var $confirmation_message = $(this).find('.enhancer_confirmation_message'),
                        $confirmation_page_id = $(this).find('.enhancer_confirmation_page_id'),
                        $radios = $(this).find('.enhancer_after_submit');

                    $radios = $radios.find(':radio').add($radios.filter(':radio'));

                    $radios
                        .click(function(e) {
                            var $radio = $(this);
                            if ($radio.val() === 'message') {
                                $confirmation_message.show().nosOnShow();
                                $confirmation_page_id.hide();
                            } else {
                                $confirmation_message.hide();
                                $confirmation_page_id.show().nosOnShow();
                            }
                        })
                        .filter(':checked').triggerHandler('click');
                });
            }
        });

        return $;
    });
