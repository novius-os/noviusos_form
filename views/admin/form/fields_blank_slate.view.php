<?php

$max_per_col = !empty($max_per_col) ? $max_per_col : 10;

// Calculates the columns and rows
$layouts = array_map(function($layout) use ($max_per_col) {
    $layout['count'] = count($layout['layout']);
    $layout['nb_cols'] = $layout['count'] > $max_per_col ? 2 : 1;
    $layout['nb_per_col'] = ceil($layout['count'] / $layout['nb_cols']);
    return $layout;
}, $layouts);

$colAvgWidth = round(100 / \Arr::sum($layouts, 'nb_cols'), 2);

?>
<div class="field_blank_slate ui-widget-content" style="display:none;">
    <table class="layouts-list">
        <thead>
            <tr>
                <?php foreach ($layouts as $layout) { ?>
                    <th class="layout-type">
                        <?= \Arr::get($layout, 'title') ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                foreach ($layouts as $layout) {
                    $chunks = array_chunk(\Arr::get($layout, 'layout', array()), \Arr::get($layout, 'nb_per_col'), true);
                    ?>
                    <td class="layout-type" style="width: <?= count($chunks) * $colAvgWidth ?>%">
                        <table class="layout-list">
                            <tr>
                                <?php
                                $innerColAvgWidth = round(100 / count($chunks), 2);
                                foreach ($chunks as $n => $chunk) {
                                    ?>
                                    <td class="<?= $n === 0 ? 'first' : '' ?>" style="width: <?= number_format($innerColAvgWidth, 2, '.', '') ?>%">
                                        <?php
                                        foreach ($chunk as $layoutName => $layoutConfig) {
                                            ?>
                                            <p>
                                                <label data-layout-name="<?= base64_encode($layoutName) ?>">
                                                    <img src="<?= $layoutConfig['icon'] ?>" /> <?= $layoutConfig['title'] ?>
                                                </label>
                                            </p>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <?php
                                }
                                ?>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</div>
