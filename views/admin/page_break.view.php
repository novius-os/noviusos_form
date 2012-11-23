<?php

echo '<div class="field_enclosure page_break">';
echo '<div class="fieldset">';
foreach ($fieldset->field() as $field) {
    echo $field->set_template("{field}\n")->build();
}
echo '</div>';
echo '</div>';