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

$uniqueId = uniqid('answer_');
$view_params['container_id'] = $uniqueId;
$view_params['saveField'] = '<a href="#" onclick="javascript:$nos(this).nosTabs(\'close\');return false;">'.__('Cancel').'</a>';

$has_page_break = !empty($view_params['has_page_break']);

echo View::forge('nos::crud/tab', $view_params, false);

echo View::forge('nos::crud/toolbar', $view_params, false);

?>
<link rel="stylesheet" href="static/apps/noviusos_form/css/admin.css" />

<div id="<?= $uniqueId ?>" class="answer">
    <h1 class="title"><?= strtr(__('Answer to ‘{{title}}’'), array('{{title}}' =>  $view_params['item']->form->form_name)) ?></h1>
    <div class="received_at"><?= strtr(__('Received on {{date}}'), array(
        '{{date}}' => \Date::formatPattern($view_params['item']->answer_created_at),
    )) ?></div>
    <br />
    <?php
    foreach ($view_params['fields'] as $page => $fields) {
        if ($has_page_break) {
            ?>
            <h2 style="margin: <?= $page > 1 ? '12' : '0' ?>px 0 7px;">
                <?= strtr(__('Page {{number}}'), array('{{number}}' => $page)) ?>
            </h2>
            <?php
        }
        echo \View::forge('noviusos_form::admin/answer/layout-table', array(
            'fields' => $fields,
        ), false);
    }
    ?>
</div>

<script type="text/javascript">
    require(['jquery-nos', 'wijmo.wijgrid'],
            function ($) {
                $(function () {
                    $('#<?= $uniqueId ?>').find('table').wijgrid({
                        scrollMode : "auto",
                        rowStyleFormatter: function headerColumnRowStyleFormatter(args) {
                            if (args.state & $.wijmo.wijgrid.renderState.rendering) {
                                args.$rows.find('a')
                                    .each(function() {
                                        var attachment = $(this).data('attachment');
                                        if (!attachment) {
                                            return;
                                        }
                                        $('<button type="button"></button>')
                                            .text(<?= \Format::forge(__('Add to Media Centre'))->to_json() ?>)
                                            .css('margin-left', '1em')
                                            .insertAfter(this)
                                            .click(function(e) {
                                                $(this).nosDialog({
                                                    contentUrl: 'admin/noviusos_media/attachment/popup',
                                                    ajaxData: {
                                                        attachment: attachment
                                                    },
                                                    ajax : true,
                                                    title: <?= \Format::forge(__('Add to Media Centre'))->to_json() ?>,
                                                    height: 400,
                                                    width: 700
                                                });
                                            });
                                    })
                                    .end()
                                    .nosFormUI();
                            }

                            if (!((args.state & $.wijmo.wijgrid.renderState.rendering) && (args.type & $.wijmo.wijgrid.rowType.data))) return;
                            // data[2] will be null for rows with <td colspan="2">
                            if (<?= ($has_page_break ? 'true' : 'false').' && '; ?> args.data[2] == null) {
                                args.$rows.removeClass('ui-state-active wijmo-wijgrid-datarow').addClass('ui-widget ui-state-default wijmo-wijgrid-headerrow').removeClass('wijgridtd');
                                args.$rows.find('div.wijmo-wijgrid-innercell').each(function() {
                                    if ($(this).find('.wijmo-wijgrid-headertext').length == 0) {
                                        $(this).wrapInner('<span class="wijmo-wijgrid-headertext"></span>');
                                    }
                                });
                            }
                        },
                        selectionMode: 'none'
                    });
                });
            });
</script>
