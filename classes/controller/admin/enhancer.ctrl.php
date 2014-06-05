<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

class Controller_Admin_Enhancer extends \Nos\Controller_Admin_Enhancer
{
    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_form::common');
    }

    public function action_save(array $args = null)
    {
        if (empty($args)) {
            $args = $_POST;
        }
        $after_submit = \Arr::get($args, 'after_submit', 'message');
        if ($after_submit === 'page_id') {
            $page_id = \Arr::get($args, 'confirmation_page_id', null);
            if (empty($page_id)) {
                $args['after_submit'] = 'message';
                $args['confirmation_message'] = \Arr::get($args, 'confirmation_message', __('Thank you. Your answer has been sent.'));
            }
        }
        parent::action_save($args);
    }
}
