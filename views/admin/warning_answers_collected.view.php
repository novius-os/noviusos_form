<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
$uniqid_close = uniqid('close_');
if (!$item->is_new()) {
    $actions = $view_params['crud']['actions'];
    $answers_url = null;
    foreach ($actions as $action) {
        if ($action['name'] == 'Nos\Form\Model_Form.answers') {
            $answers_url = $action['action']['tab']['url'];
        }
    }
    ?>
<div id="<?= $uniqid_close ?>" style="display:none;">
    <p><?= __('Answers to this form have already been received. Modifying the form may alter the collected data.') ?></p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p><button class="primary" onclick="return false;"><?= __("You're right, take me to the answers") ?></button></p>
    <p>&nbsp;</p>
    <p><?= __('or') ?> <a href="" onclick="$(this).nosDialog('close'); return false;"><?= __("Don't worry, I know what I'm doing");?></a></p>
</div>
<?php
}
?>
<script type="text/javascript">
    require(
        ['jquery-nos'],
        function ($) {
            $(function () {
                var $container = $('#<?= $fieldset->form()->get_attribute('id') ?>');
                var $close = $('#<?= $uniqid_close ?>');
                $container.find('button.primary').on('click', function() {
                    $close.nosDialog('close');
                    $container.nosTabs('update', {
                        url: <?= \Format::forge()->to_json($answers_url) ?>,
                        reload: true
                    });
                });
                $close.show().nosFormUI();
                $container.nosDialog({
                    title: <?= \Format::forge()->to_json(__('This form is collecting data')) ?>,
                    content: $close,
                    width: 500,
                    height: 200,
                    close: function() {
                        $container.nosDialog('close');
                    }
                });
            });
        });
</script>
