<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
?>
<script type="text/javascript">
    require(
        ['static/apps/noviusos_form/dist/js/admin/enhancer.min.js'],
        function($) {
            $(function() {
                $('#<?= $id ?>').appFormEnhancerPopup();
            });
        });
</script>
