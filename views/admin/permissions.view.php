<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

?>
<p>
    <label>
        <input type="radio" name="perm[noviusos_form::all][]" value="2_write" <?= $role->getPermissionValue('noviusos_form::all', 2) == 2 ? 'checked' : '' ?> />
        <?= __('Can add, edit and delete forms and answers') ?>
    </label>
</p>

<p>
    <label>
        <input type="radio" name="perm[noviusos_form::all][]" value="1_read" <?= $role->getPermissionValue('noviusos_form::all', 2) == 1 ? 'checked' : '' ?> />
        <?= __('Can visualise answers only') ?>
    </label>
</p>
