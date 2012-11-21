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
    <div calss="line">
        <div class="c12 unit col lastUnit">
            <div><?= __('Receipt date') ?></div>
            <div><?= $view_params['item']->answer_created_at  ?></div>
        </div>
    </div>
<?php
$widths = array(
    1 => 'c1',
    2 => 'c2',
    3 => 'c3',
    4 => 'c4',
    5 => 'c5',
    6 => 'c6',
    7 => 'c7',
    8 => 'c8',
    9 => 'c9',
    10 => 'c10',
    11 => 'c11',
    12 => 'c12',
);

$template = '<div class="{label_class} unit col"><div>{label}</div><div>{value}</div></div>';

// Label width will be set according to the smallest columns
$label_width = 4;
foreach ($view_params['fields'] as $field) {
    $label_width = min($label_width, $field['width']);
}

$first_row = true;
$col_width = 12;

$render_template = function($template, $args) {
    $replacements = array();
    foreach ($args as $name => $value) {
        $replacements['{' . $name . '}'] = $value;
    }
    return strtr($template, $replacements);
};

unset($field);

// Loop through fields now
foreach ($view_params['fields'] as $field) {

    if ($field['new_row']) {
        if (!$first_row) {
            if ($col_width < 12) {
                echo '<div class="unit col '.$widths[12 - $col_width].' lastUnit"></div>';
            }
            echo '</div>';
        }
        if ($first_row) {
            $first_row = false;
        }
        echo '<div class="line" style="margin-top: 1em;">';
        $col_width = 0;
    }

    $available_width = $field['width'] * 3; // 3 = 12 columns grid / 4 column form
    $col_width += $available_width;

    echo $render_template($template, array(
            'label' => $field['label'],
            'value' => $field['value'],
            'label_class' => $widths[$available_width].($col_width === 12 ? ' lastUnit' : ''),
        ));
}

if (!$first_row) {
    if ($col_width < 12) {
        echo '<div class="unit col '.$widths[12 - $col_width].' lastUnit"></div>';
    }
    echo '</div>';
}
?>
</div>
