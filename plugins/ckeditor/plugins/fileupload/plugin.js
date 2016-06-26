( function() {
	CKEDITOR.plugins.add( 'fileupload',
	{
		lang: ['ru'],
		init: function( editor )
		{
			editor.addCommand( 'fileupload', new CKEDITOR.dialogCommand( 'fileupload', {
				//allowedContent: 'a[!width,!height,!src,!frameborder,!allowfullscreen];'
			}));

			editor.ui.addButton( 'fileupload',
			{
				label : editor.lang.youtube.button,
				toolbar : 'insert',
				command : 'fileupload',
				icon : this.path + 'images/icon.png'
			});

			CKEDITOR.dialog.add( 'fileupload', function ( instance )
			{
				var uploadContainerId = CKEDITOR.tools.getNextId();
				var uplaodFileUrl = CKEDITOR.tools.getNextId();
				var uplaodFileName = CKEDITOR.tools.getNextId();
				var uploadItemCaption = CKEDITOR.tools.getNextId();
				var uplaodContainer = '<div id="'+uploadContainerId+'" class="uploadZoneProgress"></div><input id="'+uplaodFileUrl+'" type="hidden" value=""><input id="'+uplaodFileName+'" type="hidden" value=""><div id="'+uploadItemCaption+'"></div>';
				return {
					title: 'File Upload Dialog',
					resizable: CKEDITOR.DIALOG_RESIZE_BOTH,
					minWidth: 255,
					minHeight: 100,
					onShow: function(inst) {
						$('#'+uplaodFileUrl).val('');
						$('#'+uploadContainerId).html('');
						var selfDialog = this;
						var ckuploader = uploader.init({
							filetype : 'mixed',
							container : $("#"+uploadContainerId),
							formCaption : '',
							buttonElement : $('<a href="javascript:void(0)" class="cke_dialog_ui_button"><span class="cke_dialog_ui_button">Загрузить файл на сервер</span></a>'),
							hideUploaded : true,
							done : function(info) {
								info = info[0];								
								$('#'+uplaodFileUrl).val(info.name);
								$('#'+uplaodFileName).val(info.filename);
								$('#'+uploadItemCaption).val(info.filename);
								selfDialog.setValueOf('fileUploadPlugin', 'txtLink', 'Скачать файл');
								
							}
						});
					},

					onOk: function() {
						var title = this.getValueOf( 'fileUploadPlugin', 'txtLink') || $('#'+uplaodFileName).val();
						var link = $('#'+uplaodFileUrl).val();
						var html = '<a href="'+link+'" name="downloadLink" class="fileDownload">'+title+'</a>';
						editor.insertHtml(html);
					},

					contents :
						[{
							id : 'fileUploadPlugin',
							expand : true,
							elements : [
							    {
								type: 'html',
								id: 'testText1',
								html: uplaodContainer
							    },
							    {
								type : 'text',
								id : 'txtLink',
								width : '100%',
								label : editor.lang.fileupload.titleLink,
							    }	
							]
					}]	
				};
			});
		}
	});
})();


