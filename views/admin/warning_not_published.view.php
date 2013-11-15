<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary(array('noviusos_form::common', 'nos::common'));

?>

<div class="line" style="margin: 1em 0 2em;">
    <div class="col c12">
        <img src="static/novius-os/admin/novius-os/img/icons/status-red.png" style="vertical-align: middle;" />
        <?= __('Not published') ?>.
        <?= __('To publish this form, add it to a page, a blog post or any other text editor.') ?>
        <?= \View::forge('nos::admin/tooltip', array(
            'title' => '',
            'content' => strtr(__('<p>Most Novius OS text editors (also called WYSIWYG editors) feature this button {{preview}} in the toolbar.</p><p>Click it to access the list of applications you can enhance your text with.</p>'), array(
                '{{preview}}' => '<span class="tinymce_button"><img src="static/novius-os/admin/vendor/tinymce/themes/nos/img/enhancer.gif" /> '.__('Applications').'</span>',
            )),
        ), false) ?>
    </div>
</div>