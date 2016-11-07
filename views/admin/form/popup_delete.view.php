<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


Nos\I18n::current_dictionary(array('noviusos_form::common', 'nos::common'));

$answer_count = count($item->answers);
?>
<input type="hidden" name="id" value="<?= $item->{$crud['pk']} ?>" />
<div id="<?= $uniqid = uniqid('id_') ?>" class="fieldset standalone">
<p>
<?php
if ($answer_count > 0) {
    ?>
    <p><?=
        strtr(
            n__(
                'This form has already received <strong>one answer</strong>.',
                'This form has already received <strong>{{count}} answers</strong>.',
                $answer_count
            ),
            array(
                '{{count}}' => $answer_count,
            )
        ) ?></p>
    <p><?= $crud['config']['i18n']['deleting confirmation number'] ?></p>
    <p><?= strtr(__('Yes, I want to delete this form and the {{count}} answers received.'), array(
        '{{count}}' => '<input class="verification" data-verification="'.$answer_count.'" size="'.(mb_strlen($answer_count) + 1).'" />',
        )); ?></p>
    <?php
}
?>
    </p>
    <input type="checkbox" name="contexts[]" class="count" data-count="1" value="all" checked style="display:none;" />
</div>
