<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'popup' => array(
        'layout' => array(
            'view' => 'noviusos_form::enhancer/popup',
        ),
    ),
    'preview' => array(
        'params' => array(
            'title' => function($enhancer_args) {
                if (!empty($enhancer_args['form_id'])) {
                    $form = \Nos\Form\Model_Form::find($enhancer_args['form_id']);
                }
                if (!empty($form)) {
                    return $form->form_name;
                } else {
                    return __('Form');
                }
            },
        ),
    ),
);
