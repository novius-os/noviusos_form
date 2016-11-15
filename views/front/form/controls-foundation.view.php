<?php

// Wizard mode
if ($pageBreakCount > 0) {
    ?>
    <div class="page_break_control row">
        <?= strtr(__('{{previous}}{{pagination}}{{next}}'), array(
            '{{previous}}' => '
                <div class="columns large-4 right">
                    <a class="page_break_previous" href="#">'.__('Previous page').'</a>
                </div>
            ',
            '{{next}}' => '
                <div class="columns large-4 right">
                    <button type="button" class="page_break_next">'.__('Next page').'</button>'.
                        \Form::submit('submit', $form->form_submit_label, array(
                            'class' => 'page_break_last',
                        )).'
                </div>
            ',
            '{{pagination}}' => '
                <div class="columns large-4 right"> <progress id="progress" value="1" max="'.($pageBreakCount + 1).'"></progress> '.
                    strtr(__('{{current}} out of {{total}}'), array(
                        '{{current}}' => '<span class="page_break_current">1</span>',
                        '{{total}}' => '<span class="page_break_total">'.($pageBreakCount + 1).'</span>',
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
