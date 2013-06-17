<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
$id = uniqid('form_');
?>
<p>&nbsp;</p>
<p>
    <?= strtr(__('You don’t have any form to add. Go ahead, <a>add your first form.</a>'), array(
        '<a>' => '<a href="#" id="'.$id.'">',
    ));
    ?>
</p>
<script type="text/javascript">
    require(
        ['jquery-nos'],
        function($) {
            $(function() {
                $('#<?= $id ?>').on('click', function(e) {
                    e.preventDefault();
                    $(this).nosDialog('close');
                    $(this).nosTabs('open', {
                        url: 'admin/noviusos_form/form/insert_update?context=<?= $params['_parent_context'] ?>'
                    });
                });
            });
        });
</script>
