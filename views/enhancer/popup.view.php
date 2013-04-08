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

$id = uniqid();
?>
<p style="margin-bottom: 0.5em;">
    <label><?= __('Select a form:') ?>&nbsp;
<?php
$options = array();
$nosContext = \Arr::get($enhancer_args, 'nosContext', null);
if (!empty($nosContext)) {
    $options['where'] = array(array('context', $nosContext));
}

$forms = \Arr::pluck(\Nos\Form\Model_Form::find('all', $options), 'form_name', 'form_id');
echo \Fuel\Core\Form::select('form_id', \Arr::get($enhancer_args, 'form_id', ''), $forms);
?>
    </label>
</p>
<p style="margin-bottom: 0.5em;">
    <label>
    <?= __('Label position:') ?>&nbsp;
    <?= \Form::select('label_position', \Arr::get($enhancer_args, 'label_position', 'top'), array(
        'top' => __('Top aligned'),
        'left' => __('Left aligned'),
        'right' => __('Right aligned'),
        'placeholder' => __('In the field (placeholder)'),
    )); ?>
    </label>
</p>
<p style="margin-bottom: 0.3em;">
    <?= __('Once the user submitted the form') ?>
</p>
<p style="margin-bottom: 0.3em;" class="enhancer_after_submit" id="<?= $id ?>">
    <label><input type="radio" name="after_submit" value="message" <?=  \Arr::get($enhancer_args, 'after_submit', 'message') === 'message' ? 'checked' : '' ?> />&nbsp;<?= __('Display a message') ?></label>
    <label><input type="radio" name="after_submit" value="page_id" <?=  \Arr::get($enhancer_args, 'after_submit', 'message') === 'page_id' ? 'checked' : '' ?> />&nbsp;<?= __('Redirect to a page') ?></label>
</p>
<p style="display:none;margin-bottom: 0.5em;" class="enhancer_confirmation_message">
    <?= \Form::textarea('confirmation_message', \Arr::get($enhancer_args, 'confirmation_message', __('Thank you. Your answer has been sent.')), array(
    'rows' => '4',
    'style' => 'width: 100%',
)); ?>
</p>
<div style="display:none;margin-bottom: 0.5em;" class="enhancer_confirmation_page_id">
    <?= \Nos\Page\Renderer_Selector::renderer(array(
        'input_name' => 'confirmation_page_id',
        'selected' => array(
            'id' => \Arr::get($enhancer_args, 'confirmation_page_id', null),
        ),
        'treeOptions' => array(
            'context' => \Arr::get($enhancer_args, 'nosContext', \Nos\Tools_Context::defaultContext()),
        ),
    )); ?>
</div>
<script type="text/javascript">
    require(
        ['static/apps/noviusos_form/js/admin/enhancer.js'],
        function($) {
            $(function() {
                $('#<?= $id ?>').parent().appFormEnhancerPopup();
            });
        });
</script>
