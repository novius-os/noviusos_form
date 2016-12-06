<?php

// Wizard mode
if ($pageBreakCount > 0) {
    ?>
    <div class="wizard-controls row">
        <?= strtr(__('{{previous}}{{pagination}}{{next}}'), array(
            '{{previous}}' => '
                <div class="col-xs-4 col-sm-4 text-left">
                    <a class="wizard-control-previous" href="#">'.__('Previous page').'</a>
                </div>
            ',
            '{{pagination}}' => '
                <div class="col-xs-4 col-sm-4 text-center">
                    <progress class="wizard-control-progress" id="progress" value="1" max="'.($pageBreakCount + 1).'"></progress>
                    '.strtr(__('{{current}} out of {{total}}'), array(
                    '{{current}}' => '<span class="wizard-control-current-page wizard-control-current-page">1</span>',
                    '{{total}}' => '<span class="wizard-total-pages">'.($pageBreakCount + 1).'</span>',
                )).'
                </div>
            ',
            '{{next}}' => '
                <div class="col-xs-4 col-sm-4 text-right">
                    <button type="button" class="wizard-control-next">'.__('Next page').'</button>'.
                        \Form::submit('submit', $form->form_submit_label, array(
                            'class' => 'wizard-control-submit',
                        )).'
                </div>
            ',
        )); ?>
    </div>
    <?php
}

// Normal mode
else {
    echo \Form::submit('submit', $form->form_submit_label);
}
