<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

echo '<div class="field_enclosure page_break">';
echo '<div class="fieldset">';
foreach ($fieldset->field() as $field) {
    echo $field->set_template("{field}\n")->build();
}
echo '</div>';
echo '</div>';
