<?php

?>
<p>
    <label>
        <input type="radio" name="perm[noviusos_form::all][]" value="2_write" <?= $role->checkPermissionExistsOrEmpty('noviusos_form::all', '2_write') ? 'checked' : '' ?> />
        <?= __('Can add, edit and delete forms and answers') ?>
    </label>
</p>

<p>
    <label>
        <input type="radio" name="perm[noviusos_form::all][]" value="1_read" <?= $role->checkPermissionExists('noviusos_form::all', '1_read') ? 'checked' : '' ?> />
        <?= __('Can visualise answers only') ?>
    </label>
</p>
