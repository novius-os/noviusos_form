<?php
$count = count($layouts);
$nbCols = $count > 6 ? 2 : 1;
$nbPerCol = ceil($count / $nbCols);
?>
<div class="field_blank_slate ui-widget-content" style="display:none;">
    <table style="width: 100%;">
        <tr>
            <?php foreach (array_chunk($layouts, $nbPerCol, true) as $chunk) { ?>
                <td style="width: 50%">
                    <?php
                    foreach ($chunk as $layoutName => $layoutConfig) {
                        if (!empty($meta['expert']) && !\Session::user()->user_expert) {
                            continue;
                        }
                        ?>
                        <p>
                            <label data-layout-name="<?= $layoutName ?>">
                                <img src="<?= $layoutConfig['icon'] ?>" /> <?= $layoutConfig['title'] ?>
                            </label>
                        </p>
                        <?php
                    }
                    ?>
                </td>
            <?php } ?>
        </tr>
    </table>
</div>
