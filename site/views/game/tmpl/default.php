<?php
/**
 * @package     LUPO
 * @copyright   Copyright (C) databauer / Stefan Bauer
 * @author      Stefan Bauer
 * @link        https://www.ludothekprogramm.ch
 * @license     License GNU General Public License version 2 or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$componentParams = JComponentHelper::getParams('com_lupo');
?>


<article class="tm-article">
    <div class="tm-article-content">
        <?php
        if ($this->game == 'error') {
            ?><h2 class="contentheading"><?php echo JText::_("COM_LUPO_ERROR_NOT_FOUND") ?></h2><?php
        } else {
            //description-text
            $description_title = $this->game['description_title'];
            if ($description_title != "") {
                $description_title = '<p><b>' . $description_title . '</b></p>';
            }
            $description = $description_title . $this->game['description'];

            //for navigation
            $pos          = isset($_GET['pos']) ? $_GET['pos'] : 0;
            $session      = JFactory::getSession();
            $session_lupo = $session->get('lupo');

            ?><h2 class="contentheading"><?php echo $this->game['title'] . ' ' . $this->game['edition'] ?></h2>

        <?php
        //navigation
        if ($componentParams->get('detail_show_toy_nav', '1') && $session_lupo != null && isset($_GET['pos'])){
        $style    = $pos + 1 < count($session_lupo) ? '' : 'visibility: hidden';
        $nav_game = isset($session_lupo[$pos + 1]) ? $session_lupo[$pos + 1] : ['id' => null]; ?>
            <div style="<?= $style ?>" class="uk-h3 uk-float-right"><a href="<?php echo JRoute::_('index.php?option=com_lupo&view=game&id=' . $nav_game['id'] . '&pos=' . ($pos + 1)) ?>" title="<?php echo JText::_('COM_LUPO_NAV_NEXT_GAME'); ?>" class="uk-icon-hover uk-icon-chevron-right"></a></div>
            <?php
            $style    = $pos >= 1 ? '' : 'visibility: hidden;';
            $nav_game = isset($session_lupo[$pos - 1]) ? $session_lupo[$pos - 1] : ['id' => null]; ?>
            <div style="<?= $style ?>" class="uk-h3 uk-float-right"><a href="<?php echo JRoute::_('index.php?option=com_lupo&view=game&id=' . $nav_game['id'] . '&pos=' . ($pos - 1)) ?>" title="<?php echo JText::_('COM_LUPO_NAV_PREV_GAME'); ?>" class="uk-icon-hover uk-icon-chevron-left">&nbsp;&nbsp;</a></div>
        <?php } ?>

        <?php
        if ($componentParams->get('detail_show_toy_photo', '1') || $description != ""){
        if ($componentParams->get('detail_show_toy_photo', '1') && $this->game['image_thumb'] != null && $description != "") {
            $grid_width = "2";
        } else {
            $grid_width = "1";
        }
        ?>
            <div class="uk-grid">
                <?php
                if ($description != "") { ?>
                    <div class="uk-width-1-1 uk-width-small-1-<?php echo $grid_width ?> uk-margin-bottom">
                        <div class="lupo_description"><?php echo $description; ?></div>
                    </div>
                <?php }
                if ($componentParams->get('detail_show_toy_photo', '1') && $this->game['image'] != null) { ?>
                    <div class="uk-width-1-1 uk-width-small-1-<?php echo $grid_width ?> uk-margin-bottom">
                        <?php
                        if ($this->game['image'] == null) {
                            ?><img class="lupo_image" src="<?php echo $this->game['image_thumb'] ?>"><?php
                        } else {
                            ?>
                            <?php if ($componentParams->get('detail_photo_lightbox', '1')) { ?>
                                <a href="<?php echo $this->game['image'] ?>" data-uk-lightbox title="<?php echo htmlspecialchars($this->game['title'] . ' ' . $this->game['edition']) ?>"><img class="lupo_image" alt="<?php echo JText::_("COM_LUPO_TOY") . ' ' . $this->game['number'] ?>" src="<?php echo $this->game['image_thumb'] ?>"/></a>
                            <?php } else { ?>
                                <img class="lupo_image" alt="<?php echo JText::_("COM_LUPO_TOY") . ' ' . $this->game['number'] ?>" src="<?php echo $this->game['image_thumb'] ?>"/>
                            <?php } ?>
                            <div id="img-toy" class="uk-modal">
                                <div>
                                    <img src="<?php echo $this->game['image'] ?>" alt="<?php echo JText::_("COM_LUPO_TOY") . ' ' . $this->game['number'] ?>"/>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                    <?php
                } ?>
            </div>
        <?php
        } ?>

        <?php
        //reservation
        if ($componentParams->get('detail_show_toy_res', '0')) {
        $session                = JFactory::getSession();
        $client                 = $session->get('lupo_client');
        $show_only_to_logged_in = $componentParams->get('detail_show_toy_res_only_logged_in', '0');
        if (!$show_only_to_logged_in || ($show_only_to_logged_in && $client)) {
        $clientname = ($client) ? $client->firstname . ' ' . $client->lastname : '';
        $clientnr   = ($client) ? $client->adrnr : '';
        ?>
            <a class="uk-button uk-margin-right uk-margin-bottom" id="btnres" href="#resform" data-uk-modal><i class="uk-icon-calendar-check-o"></i> <?php echo JText::_("COM_LUPO_RES_TOYS"); ?></a>

            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('#resform').on({
                        'show.uk.modal': function () {
                            $('#modal-msg').html(""); //remove old messages
                        }
                    });

                    $('#resnow').click(function () {
                        if ($(this).prop('checked')) {
                            $('#row_resdate').hide();
                        } else {
                            $('#row_resdate').show();
                        }
                    });

                    $('#submitres').click(function () {
                        $.ajax({
                            method: "POST",
                            url: "index.php?option=com_lupo&task=sendres&format=raw",
                            data: {
                                clientname: $('#clientname').val(),
                                clientemail: $('#clientemail').val(),
                                clientmobile: $('#clientmobile').val(),
                                clientnr: $('#clientnr').val(),
                                resdate: ($('#resnow').prop('checked') ? 'sofort' : $('#resdate').val()),
                                comment: $('#comment').val(),
                                toynr: '<?php echo $this->game['number']?>',
                                toyname: '<?php echo $this->game['title']?>'
                            }
                        })
                            .done(function (msg) {
                                if (msg == 'ok') {
                                    var modal = UIkit.modal("#resform");
                                    modal.hide();
                                    $('#btnres').after('<div class="uk-alert uk-alert-success"><?php echo JText::_("COM_LUPO_RES_SUBMIT_SUCCESS_MSG"); ?></div>');
                                } else {
                                    $('#modal-msg').html('<div class="uk-alert uk-alert-danger">' + msg + '</div>');
                                }
                            });
                    });
                    $('#cancelres').click(function () {
                        UIkit.modal("#resform").hide();
                    })
                })
            </script>
            <div id="resform" class="uk-modal">
                <div class="uk-modal-dialog" style="background: #ffffff none repeat scroll 0 0 !important;">
                    <button class="uk-modal-close uk-close" type="button"></button>
                    <div class="uk-modal-header"><h2><?php echo JText::_("COM_LUPO_RES_TOYS"); ?></h2></div>
                    <style>
                        .res-table {
                            width: 600px;
                        }

                        .res-table td:nth-child(1) {
                            width: 150px;
                            vertical-align: top;
                        }

                        <?php if($componentParams->get('detail_show_res_date_now', '1')==1) {?>
                        #row_resdate {
                            display: none;
                        }

                        <?php } ?>
                    </style>
                    <table class="res-table">
                        <tbody>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_TOY"); ?>:</td>
                            <td><input type="text" disabled maxlength="100" size="40"
                                       value="<?php echo $this->game['title'] ?>" id="toy"></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_CLIENT_NO"); ?>:</td>
                            <td><input type="text" maxlength="50" size="40" value="<?= $clientnr ?>" id="clientnr"
                                       name="clientnr"> <span
                                        class="uk-text-muted"><?php echo JText::_("COM_LUPO_RES_CLIENT_NO_IF_AVAILABLE"); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_NAME"); ?>:*</td>
                            <td><input type="text" required maxlength="100" size="40" value="<?= $clientname ?>"
                                       id="clientname" name="clientname"></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_EMAIL"); ?>:*<br></td>
                            <td><input type="email" required maxlength="100" size="40" value="" id="clientemail"
                                       name="clientemail"></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_MOBILE"); ?>:*<br></td>
                            <td><input type="tel" required maxlength="15" size="40" value="" id="clientmobile"
                                       name="clientmobile"></td>
                        </tr>
                        <?php if ($componentParams->get('detail_show_res_date', '1') == 1) { ?>
                            <tr>
                                <td><?php echo JText::_("COM_LUPO_RES_FROM"); ?>:</td>
                                <td>
                                    <div style="margin-bottom: 10px">
                                        <?php if ($componentParams->get('detail_show_res_date_now', '1') == 1) { ?>
                                            <input type="checkbox" value="resnow" id="resnow" name="resnow" checked="checked"> <?php echo JText::_("COM_LUPO_RES_FROM_INSTANTLY"); ?>
                                        <?php } ?>
                                        <span class="uk-text-muted"> <?php echo JText::_("COM_LUPO_RES_FROM_INFO"); ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr id="row_resdate">
                            <td></td>
                            <td><input type="text" maxlength="40" size="40" value="" id="resdate" name="resdate"
                                       placeholder="<?php echo JText::_("COM_LUPO_RES_FROM_DATE"); ?>"></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_ADDITIONAL_INFO"); ?>:</td>
                            <td><textarea rows="10" cols="70" id="comment" name="comment"
                                          style="height: 87px; width: 312px;"></textarea></td>
                        </tr>
                        <?php if ($componentParams->get('detail_toy_res_costs', '') != "") { ?>
                            <tr>
                                <td><?php echo JText::_("COM_LUPO_RES_COSTS"); ?>:</td>
                                <td>
                                    <?= $componentParams->get('detail_toy_res_costs', ''); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="uk-modal-footer">
                        <button id="cancelres" class="uk-button"><?php echo JText::_("JCANCEL"); ?></button>
                        <button id="submitres" class="uk-button uk-button-primary"><?php echo JText::_("COM_LUPO_RES_SUBMIT"); ?></button>
                        <div id="modal-msg" style="margin-top: 10px"></div>
                    </div>
                </div>
            </div>
        <?php
        }
        } ?>

        <?php
        //document links
        foreach ($this->game['documents'] as $document) {
        if ($document['code'] != 'userdefined'){ ?>
            <a class="uk-button uk-margin-right uk-margin-bottom" href="<?php echo $document['href'] ?>" <?php echo $document['lightbox'] ?>><i class="uk-icon-<?php echo $document['icon'] ?>"></i> <?php echo $document['desc'] ?></a>
        <?php
        }
        }
        ?>

            <table class="uk-table uk-table-striped uk-table-condensed" id="lupo_detail_table">
                <colgroup>
                    <col style="width:150px">
                    <col/>
                </colgroup>
                <?php if ($componentParams->get('detail_show_toy_no', '1')) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_ART_NR") ?>:</td>
                        <td><?php echo (substr($this->game['number'], -2, 2) == '.0') ? (int)$this->game['number'] : $this->game['number'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_category', '1')) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_CATEGORY") ?>:</td>
                        <td><a href="<?php echo $this->game['link_cat'] ?>"><?php echo $this->game['category'] ?></a></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_age_category', '1')) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_AGE_CATEGORY") ?>:</td>
                        <td><a href="<?php echo $this->game['link_agecat'] ?>"><?php echo $this->game['age_category'] ?></a></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_genres', '1') && $this->game['genres'] != "") { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_GENRES") ?>:</td>
                        <td>
                            <?php //echo $this->game['genres'] /* use this to output genres without link */?>
                            <?php $separator = "";
                            foreach ($this->game['genres_list'] as $genre) {
                                echo $separator ?><a href="<?php echo $genre['link'] ?>"><?php echo $genre['genre'] ?></a><?php
                                $separator = ", ";
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_play_duration', '1') && $this->game['play_duration'] != "") { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_PLAY_DURATION") ?>:</td>
                        <td><?php echo $this->game['play_duration'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_players', '1') && $this->game['players'] != "") { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_PLAYERS") ?>:</td>
                        <td><?php echo $this->game['players'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_fabricator', '1') && $this->game['fabricator'] != "") { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_FABRICATOR") ?>:</td>
                        <td><?php echo $this->game['fabricator'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_author', '1') && $this->game['author'] != "") { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_AUTHOR") ?>:</td>
                        <td><?php echo $this->game['author'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_artist', '1') && $this->game['artist'] != "") { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_ARTIST") ?>:</td>
                        <td><?php echo $this->game['artist'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_nbrdays', '1')) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_DAYS") ?>:</td>
                        <td><?php echo $this->game['days'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_tax', '1') && ($componentParams->get('detail_show_toy_tax_not_null', '1') == '0' || $this->game['tax'] > 0)) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_TAX") ?>:</td>
                        <td>Fr. <?php echo number_format($this->game['tax'], 2) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('lupo_show_toystatus', '0')) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_AVAILABILITY") ?>:</td>
                        <td><i class="uk-icon uk-icon-circle <?= $this->game['availability_color'] ?> availability_dot"></i> <?= $this->game['availability_text'] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($componentParams->get('detail_show_toy_prolongable', '0')) { ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_PROLONGABLE") ?>:</td>
                        <td><?= $this->game['prolongable']?JText::_("JYES"):JText::_("JNO") ?></td>
                    </tr>
                <?php } ?>
            </table>

            <?php

            //related games
            if ($componentParams->get('detail_show_toy_related', '1')) {
                if (count($this->game['related']) > 0) {
                    ?><p class="uk-margin-large-top"><?php echo JText::_("COM_LUPO_RELATED_TOYS"); ?></p><?php
                    if ($componentParams->get('detail_show_toy_photo', '1') == 0) { ?>
                        <ul><?php
                            foreach ($this->game['related'] as $related) {
                                ?>
                                <li>
                                    <a href="<?php echo $related['link'] ?>"><?php echo $related['title'] ?><?php echo $related['edition'] ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else {
                        $infinite = count($this->game['related']) < 6 ? '{infinite: false}' : ''; ?>
                        <style type="text/css">
                            .uk-overlay:hover img {
                                transform: none;
                            }
                        </style>
                        <div class="uk-slidenav-position" data-uk-slider="<?= $infinite ?>">
                            <div class="uk-slider-container">
                                <ul class="uk-slider uk-grid uk-grid-width-1-3 uk-grid-width-small-1-4 uk-grid-width-medium-1-4 uk-grid-width-large-1-5"><?php
                                    foreach ($this->game['related'] as $related) {
                                        if ($related['image_thumb'] != null) {
                                            $image = $related['image_thumb'];
                                        } else {
                                            $image = 'images/spiele/mini_dice-gray.jpg';
                                        }
                                        ?>
                                        <li>
                                            <a href="<?php echo $related['link'] ?>">
                                                <figure class="uk-overlay uk-overlay-hover">
                                                    <img src="<?php echo $image; ?>"/>
                                                    <figcaption class="uk-overlay-panel uk-overlay-background uk-flex uk-flex-center uk-flex-middle uk-text-center"><?php echo $related['title'] . ' ' . $related['edition'] ?></figcaption>
                                                </figure>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <?php if (count($this->game['related']) > 6) { ?>
                                <a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous"
                                   data-uk-slider-item="previous"></a>
                                <a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-next"
                                   data-uk-slider-item="next"></a>
                            <?php } ?>
                        </div>
                        <?php
                    }
                }
            }
        }  // endif get_game=error?>
    </div>
</article>