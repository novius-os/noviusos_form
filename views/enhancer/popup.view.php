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

?>
<p style="margin-bottom: 0.5em;">
<?= __('Pick the form:') ?>&nbsp;
<?php
    $forms = Nos\Form\Controller_Admin_Form::array_pluck(\Nos\Form\Model_Form::find('all'), 'form_name', 'form_id');
    echo \Fuel\Core\Form::select('form_id', \Arr::get($enhancer_args, 'form_id', ''), $forms);
?>
</p>
<p style="margin-bottom: 0.5em;">
    <?= __('Label position') ?>&nbsp;
    <?= \Form::select('label_position', \Arr::get($enhancer_args, 'label_position', 'top'), array(
        'top' => __('On the top'),
        'left' => __('Left aligned'),
        'right' => __('On the left, right aligned'),
        'placeholder' => __('Use placeholder'),
    )); ?>
</p>
<p style="margin-bottom: 0.3em;">
    <?= __('Message displayed after submission:') ?>
</p>
<p style="margin-bottom: 0.5em;">
    <?= \Form::textarea('confirmation_message', \Arr::get($enhancer_args, 'confirmation_message', __('Thank you. Your answer has been sent.')), array(
    'rows' => '4',
    'style' => 'width: 100%',
)); ?>
</p>
