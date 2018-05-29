<?php if (!empty($form->wysiwygs->submit_consent)): ?>
    <div class="form-submit-consent">
        <?= \Nos\Tools_Wysiwyg::parse($form->wysiwygs->submit_consent) ?>
    </div>
<?php endif; ?>
