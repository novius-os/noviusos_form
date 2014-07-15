<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

\Nos\I18n::current_dictionary('noviusos_form::front');
$url = \Nos\Nos::main_controller()->getUrl();
?>
<style type="text/css">
body {
    font: 12px Verdana, Arial, Helvetica, sans-serif;
    color: #000000;
}
table {
    border-top:1px solid #CCCCCC;
}
th, td {
    font-size: 12px;
    font-family: Verdana, Arial, Helvetica, sans-serif;
    color: #000000;
    text-align: left;
    vertical-align: top;
    border-bottom:1px solid #CCCCCC;
}
</style>
<p><?= __('Message sent by:') ?> <a href="<?= $url ?>"><?= $url ?></a></p>
<table border="0" cellspacing="0" cellpadding="3">
<?php
foreach ($data as $rowData) {
    echo '<tr><th>', e($rowData['label']), '</th><th>:</th>';
    echo '<td>', \Str::textToHtml(e($rowData['value'])), '</td></tr>';
}
?>
</table>
