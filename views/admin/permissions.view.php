<?php

?>
<p>
    <label>
        <input type="radio" name="perm[noviusos_form::level][]" value="write" <?= $role->checkPermissionOrEmpty('noviusos_form::level', 'write') ? 'checked' : '' ?> />
        <?= __('Can add, edit and delete forms and answers') ?>
    </label>
</p>

<p>
    <label>
        <input type="radio" name="perm[noviusos_form::level][]" value="read" <?= $role->checkPermission('noviusos_form::level', 'read') ? 'checked' : '' ?> />
        <?= __('Can visualise answers only') ?>
    </label>
</p>
