<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

$wrapperId = uniqid('field_enclosure_');

?>
<div class="field_enclosure" id="<?= $wrapperId ?>" data-field-id="<?= $field->id ?>">
    <?php
    $has_restricted_fields = false;
    foreach ($fieldset->field() as $fieldsetField) {
        if ($fieldsetField->isRestricted()) {
            if (!$has_restricted_fields) {
                echo '<div style="display:none;">';
                $has_restricted_fields = true;
            }
            echo $fieldsetField->set_template('{field}')->build();
        }
    }
    if ($has_restricted_fields) {
        echo '</div>';
    }

    echo $fieldset->build_hidden_fields();

    foreach ($layout as $view) {
        if (!empty($view['view'])) {
            $view['params'] = empty($view['params']) ? array() : $view['params'];
            echo View::forge($view['view'], $view['params'] + $view_params, false);
        }
    }
    ?>
</div>

<?php if (!empty($js_file)) {
        ?>
    <script type="text/javascript">
        require(
            [<?= \Format::forge($js_file)->to_json() ?>],
            function (callback) {
                callback($('#<?= $wrapperId ?>'));
            });
    </script>
<?php 
    } ?>
