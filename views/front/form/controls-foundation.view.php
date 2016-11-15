<?php

// Wizard mode
if ($pageBreakCount > 0) {
    ?>
    <div class="wizard-controls row">
        <?= strtr(__('{{previous}}{{pagination}}{{next}}'), array(
            '{{previous}}' => '
                <div class="columns large-4">
                    <a class="wizard-control-previous" href="#">'.__('Previous page').'</a>
                </div>
            ',
            '{{next}}' => '
                <div class="columns large-4">
                    <button type="button" class="wizard-control-next">'.__('Next page').'</button>'.
                        \Form::submit('submit', $form->form_submit_label, array(
                            'class' => 'wizard-control-submit',
                        )).'
                </div>
            ',
            '{{pagination}}' => '
                <div class="columns large-4"> <progress class="wizard-control-progress" id="progress" value="1" max="'.($pageBreakCount + 1).'"></progress> '.
                    strtr(__('{{current}} out of {{total}}'), array(
                        '{{current}}' => '<span class="wizard-control-current-page wizard-control-current-page">1</span>',
                        '{{total}}' => '<span class="wizard-total-pages">'.($pageBreakCount + 1).'</span>',
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
