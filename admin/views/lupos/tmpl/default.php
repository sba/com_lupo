<?php
/**
 * @package		Joomla
 * @subpackage	LUPO
 * @copyright   Copyright (C) databauer / Stefan Bauer
 * @author		Stefan Bauer
 * @link		http://www.ludothekprogramm.ch
 * @license		License GNU General Public License version 2 or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<br />

<script src="components/com_lupo/assets/plupload/js/plupload.full.min.js" ></script>
<script>
	jQuery(document).ready(function() {
		var uploader = new plupload.Uploader({
			runtimes : 'html5,flash,silverlight,html4',
			browse_button : 'pickfiles',
			container: 'container',

			max_file_size : '250mb',
			multi_selection: false,
			max_file_count: 1,

			chunk_size: "3000kb",
			max_retries: 3,

			url : 'components/com_lupo/models/upload.php',

			flash_swf_url : 'components/com_lupo/assets/plupload/js/Moxie.swf',
			silverlight_xap_url : 'components/com_lupo/assets/plupload/js/Moxie.xap',
			filters : [
				{title : "Zip files", extensions : "zip"}
			],

			init: {
				PostInit: function() {
					$('filelist').innerHTML = '';

					$('uploadfiles').onclick = function() {
						uploader.start();
                        jQuery('#processzip').prop('disabled', true);
                        overlay.showLoading();
						return false;
					};
				},

				FilesAdded: function(up, files) {
					var fileCount = up.files.length;
					var ids = jQuery.map(up.files, function (item) { return item.id; });

					for (i = 1; i < fileCount; i++) {
						uploader.removeFile(uploader.getFile(ids[i]));
					}

					plupload.each(files, function(file) {
						jQuery('filelist').innerHTML = ' <span id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></span>';
						jQuery('#upload_percent').html('');
					});
				},

				UploadProgress: function(up, file) {
					jQuery('#upload_percent').html(file.percent + "%");
				},

				FileUploaded: function(up, file) {
					jQuery('#upload_percent').html('<b>100%</b>  <span class="icon-ok"> </span> ' + file.name);
					jQuery('#processzip').prop('disabled', false);
					jQuery('#uploadfiles').prop('disabled', true);
					jQuery('filelist').innerHTML = '';
					uploader.splice();
                    overlay.hideLoading();
				},

				Error: function(up, err) {
					var errorMsg;
					if(err.code==-600){
						errorMsg = "FEHLER: Die Datei " + err.file.name + " ist zu gross.";
						errorMsg = '<span style="color:red"><b>' + errorMsg + '</b></span>';
						jQuery('#filelist').html(errorMsg);
					} else {
						errorMsg = "\nError #" + err.code + ": " + err.message;
						jQuery('#upload_percent').html(errorMsg);
					}
                    overlay.hideLoading();
				}
			}
		});

		uploader.init();


        // Add spindle-wheel for upload:
        var outerDiv = jQuery("#content");

        overlay.getLoadingOverlay()
            .css("top", outerDiv.position().top - jQuery(window).scrollTop())
            .css("left", "0")
            .css("width", "100%")
            .css("height", "100%")
            .css("display", "none")
            .css("margin-top", "-10px");

	});

    var overlay = {
        getLoadingOverlay: function () {
            return jQuery("#loading");
        },
        showLoading: function () {
            this.getLoadingOverlay().css("display", "block");
        },
        hideLoading: function () {
            this.getLoadingOverlay().css("display", "none");
        }
    };


</script>

<style type="text/css">
	#lupo .btn {
		min-width: 340px;
	}
	#upload_percent {
		font-size: 120%;
	}

    #loading {
        background: rgba(255, 255, 255, .8) url(' <?php echo JHtml::_('image', 'jui/ajax-loader.gif', '', null, true, true) ?>') 50% 15% no-repeat;
        position: fixed;
        opacity: 0.8;
        -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity = 80);
        filter: alpha(opacity = 80);
        overflow: hidden;
    }

</style>

<div id="lupo">
    <div id="loading" style="top: 153px; left: 0px; width: 100%; height: 100%; display: none; margin-top: -10px;"></div>
	<div id="container">
		<a id="pickfiles" href="javascript:;"><button class="btn btn-large">1. <?php echo JText::_("COM_LUPO_ADMIN_SELECT_ZIP")?></button></a> <span id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</span>
		<br />
		<br/>
		<a id="uploadfiles" href="javascript:;"><button class="btn btn-large">2. <?php echo JText::_("COM_LUPO_ADMIN_UPLOAD_FILE")?></button></a>
		<span id="upload_percent"></span>
	</div>

	<br />

	<form action="<?php echo JRoute::_('index.php?option=com_lupo'); ?>" method="post" name="processZIPForm">
		<input type="hidden" name="act" value="processzip" />

		<input type="submit" name="submit" class="btn btn-large"  id="processzip" value="3. <?php echo JText::_("COM_LUPO_ADMIN_PROCESS")?>" />
	</form>

	<br />
	<hr />
	<br />
	<br />

    <?php
    //show processxml button only to me (if user is superadmin)
    $user = JFactory::getUser();
    $isroot = $user->authorise('core.admin');
    if($isroot){ ?>
	<form action="<?php echo JRoute::_('index.php?option=com_lupo'); ?>" method="post" name="processXMLForm">
		<input type="hidden" name="act" value="processxml" />
		<input type="submit" name="submit" class="btn" value="<?php echo JText::_("COM_LUPO_ADMIN_PROCESS_AGAIN")?>" />
	</form>
    <?php } ?>


    <form action="<?php echo JRoute::_('index.php?option=com_lupo'); ?>" method="post" name="deleteImagesForm">
        <input type="hidden" name="act" value="deleteimages" />

        <input type="submit" name="submit" class="btn btn-danger" value="<?php echo JText::_("COM_LUPO_ADMIN_DELETE_IMAGES")?>" />
    </form>

</div>

