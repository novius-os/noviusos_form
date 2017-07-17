<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


Nos\I18n::current_dictionary(array('noviusos_form::common', 'nos::common'));

$answer_count = $item->getAnswersCount();
$labelNbAnswers = __('This form has <strong>1 answer</strong>.');
if ($answer_count > 1) {
    $labelNbAnswers = __('This form has <strong>{{count}} answers</strong>.');
}

?>
<input type="hidden" name="delete_answers" value="1"/>
<input type="hidden" name="id" value="<?= $item->{$crud['pk']} ?>"/>
<div id="<?= $uniqid = uniqid('id_') ?>" class="fieldset standalone">
    <p>
        <?php if ($answer_count > 0) : ?>
            <p>
                <?= strtr($labelNbAnswers,
                    array(
                        '{{count}}' => $answer_count,
                    )
                ) ?>
            </p>
            <p>
                <?= $crud['config']['i18n']['deleting confirmation number'] ?>
            </p>
            <p>
                <?= strtr(__('Yes, I want to delete the {{count}} answer(s).'),
                    array(
                        '{{count}}' => '<input class="verification" data-verification="'.$answer_count.'" size="'.(mb_strlen($answer_count) + 1).'" />',
                    )); ?>
            </p>
        <?php endif; ?>
    </p>
</div>
