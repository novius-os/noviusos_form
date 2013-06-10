<?php

?>
<p>
    <label>
        <input type="radio" name="perm[noviusos_form::all][]" value="2_write" <?= (int) $role->getPermissionValue('noviusos_form::all', 2) == 2 ? 'checked' : '' ?> />
        <?= __('Can add, edit and delete forms and answers') ?>
    </label>
</p>

<p>
    <label>
        <input type="radio" name="perm[noviusos_form::all][]" value="1_read" <?= (int) $role->getPermissionValue('noviusos_form::all') == 1 ? 'checked' : '' ?> />
        <?= __('Can visualise answers only') ?>
    </label>
</p>
