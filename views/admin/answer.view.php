<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

$uniqueId = uniqid('answer_');
$view_params['container_id'] = $uniqueId;
$view_params['saveField'] = '<a href="#" onclick="javascript:$nos(this).nosTabs(\'close\');return false;">'.__('Cancel').'</a>';

echo View::forge('nos::crud/tab', $view_params, false);

echo View::forge('nos::crud/toolbar', $view_params, false);
?>
<div id="<?= $uniqueId ?>" style="margin:2em 2em 1em;">
    <div class="title"><?= \Str::tr(__('Answer of ":form"'), array(':form' =>  $view_params['item']->form->form_name)) ?></div>
    <br />
    <table>
        <thead>
            <tr>
                <th><?= __('Question') ?></th>
                <th><?= __('Answer') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= __('Receipt date') ?></td>
                <td><?= $view_params['item']->answer_created_at  ?></td>
            </tr>
<?php
foreach ($view_params['fields'] as $field) {
    ?>
            <tr>
                <td><?= $field['label'] ?></td>
                <td><?= $field['value']  ?></td>
            </tr>
    <?php
}
?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    require(['jquery-nos', 'wijmo.wijgrid'],
            function ($) {
                $(function () {
                    $('#<?= $uniqueId ?>').find('table').wijgrid();
                });
            });
</script>
