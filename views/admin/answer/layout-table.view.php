<table>
    <thead>
        <tr>
            <th><?= __('Question') ?></th>
            <th><?= __('Answer') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($fields as $field) { ?>
        <tr>
            <td><?= $field['label'] ?></td>
            <td><?= $field['value'] ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
