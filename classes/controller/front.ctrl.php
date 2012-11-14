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

use Nos\Controller_Front_Application;

use View;

class Controller_Front extends Controller_Front_Application
{
    public function action_main($args = array())
    {
        $this->main_controller->addCss('static/apps/noviusos_form/css/front.css');
        //$this->main_controller->addJs('static/apps/noviusos_form/js/___.js');

        $form_id = $args['form_id'];
        if (empty($form_id)) {
            return '';
        }
        $item = \Nos\Form\Model_Form::find($form_id);
        if (empty($item)) {
            return '';
        }

        return \View::forge('noviusos_form::front', array(
            'item' => $item,
            'args' => $args,
        ), false);

    }
}
