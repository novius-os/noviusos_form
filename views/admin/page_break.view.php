<?php

echo '<div class="page_break fieldset">';
foreach ($fieldset->field() as $field) {
    echo $field->build();
}
echo '</div>';