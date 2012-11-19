<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
?>
<div id="<?= $id = uniqid('temp_') ?>">
    <form method="POST" action="admin/noviusos_form/enhancer/save">
        <div class="line myBody">
            <div class="unit col c1"></div>
            <div class="unit col c10 ui-widget">
                <p style="margin-bottom: 0.5em;">
                <?= __('Pick the form:') ?>&nbsp;
<?php
    $forms = Nos\Form\Controller_Admin_Form::array_pluck(\Nos\Form\Model_Form::find('all'), 'form_name', 'form_id');
    echo \Fuel\Core\Form::select('form_id', \Input::get('form_id', ''), $forms);
?>
                </p>
                <p style="margin-bottom: 0.5em;">
                    <?= __('Label position') ?>&nbsp;
                    <?= \Form::select('label_position', \Input::get('label_position', 'top'), array(
                        'top' => __('On the top'),
                        'left' => __('Left aligned'),
                        'right' => __('On the left, right aligned'),
                        'placeholder' => __('Use placeholder'),
                    )); ?>
                </p>
            <div class="unit lastUnit"></div>
        </div>
        <div class="line">&nbsp;</div>
        <div class="line">
            <div class="unit col c1"></div>
            <div class="unit col c10 ui-widget">
                <?= Str::tr(':save or :cancel', array(
                    'save'   => '<button type="submit" data-icon="check">'.__('Save').'</button>',
                    'cancel' => '<a data-id="close" href="#">'.__('Cancel').'</a>',
                )) ?>
                </div>
            <div class="unit lastUnit"></div>
        </div>
    </form>
</div>

<script type="text/javascript">
require([
    'jquery-nos'
    ], function($) {
        $(function() {
            var div = $('#<?= $id ?>')
                .find('a[data-id=close]')
                .click(function(e) {
                    div.closest('.ui-dialog-content').wijdialog('close');
                    e.preventDefault();
                })
                .end()
                .find('form')
                .submit(function() {
                    var self = this;
                    $(self).ajaxSubmit({
                        dataType: 'json',
                        success: function(json) {
                            div.closest('.ui-dialog-content').trigger('save.enhancer', json);
                        },
                        error: function(error) {
                            $.nosNotify('An error occured', 'error');
                        }
                    });

                    return false;
                })
                .nosFormUI();
        });
    });
</script>
