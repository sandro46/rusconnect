<? $this->md5_compil='8f83fb5cb2e2b577bd221b7cbea326ed'; ?>
<div class="row-fluid">
   <div class="span12" style="max-width:800px">
      <div class="widget">
            <div class="widget-title">
                <h4 id="nodechat-headerTitleUser"><i class="icon-reorder"></i>Выберите пользователя для диалога</h4>
                <div class="actions">
                    
                </div>
            </div>
             <table style="width:100%">
                 <tr>
                   <td width="200" style="border-right:1px dashed #d5d5d5" valign="top">
                      <div style="background:none repeat scroll 0 0 #FBFBFB;">
                        <ul class="msguserlist" id="nodechat-usersList">
                          <?php if(count($this->vars['nodechatUsers'])) : 
							   		 $this->vars['foreach']['nodechatUsers']['count'] = count($this->vars['nodechatUsers']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['nodechatUsers']);
							   		 foreach($this->vars['nodechatUsers'] as $this->vars['foreach']['nodechatUsers']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['nodechatUsers']['key']; ?>
                          <li onclick="nodechat.open(this)" data-id="<?php if (isset($this->vars['item']['user_id']) && $this->vars['item']['user_id']  ): ?><?php if(isset($this->vars['item']['user_id'])) echo $this->vars['item']['user_id']; ?><?php else: ?><?php if(isset($this->vars['item']['contact_id'])) echo $this->vars['item']['contact_id']; ?><?php endif; ?>" data-name="<?php if(isset($this->vars['item']['name_last'])) echo $this->vars['item']['name_last']; ?> <?php if(isset($this->vars['item']['name_first'])) echo $this->vars['item']['name_first']; ?>" <?php if (isset($this->vars['item']['exists_not_readed']) && $this->vars['item']['exists_not_readed']  ): ?>class="new-msg" data-exists="1"<?php else: ?><?php if (isset($this->vars['item']['exists_room']) && $this->vars['item']['exists_room']  ): ?>class="opened-dialog" data-exists="1"<?php endif; ?><?php endif; ?> ><?php if(isset($this->vars['item']['name_last'])) echo $this->vars['item']['name_last']; ?> <?php if(isset($this->vars['item']['name_first'])) echo $this->vars['item']['name_first']; ?> <?php if (isset($this->vars['item']['exists_not_readed']) && $this->vars['item']['exists_not_readed']  ): ?><i>(<?php if(isset($this->vars['item']['count_not_readed'])) echo $this->vars['item']['count_not_readed']; ?>)</i><?php endif; ?></li>
                          <?php endforeach; ?><?php endif; ?>
                        </ul>
                      </div>
                    </td>
                    <td >
                        <div class="widget-body" style="background-color: white;" id="nodechat-chatRommContainer">
                          <script type="text/template"  id="nodechat-chatRoomClearTpl">
                            <ul class="chats normal-chat" data-user_to="{to_user}"></ul>
                          </script>
                          <script type="text/template"  id="nodechat-chatMessageClearTpl">
                            <li class="{message_type}">
                                <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/photo.jpg" alt="" class="avatar">
                                <div class="message">
                                   <span class="arrow"></span>
                                   <span class="username">{username}</span>
                                   <span class="datetime">{timestamp}</span>
								   <span class="body">{text}</span>
                                </div>
                            </li>
                          </script>
                          
                                    <?php if(count($this->vars['nodechatMessages'])) : 
							   		 $this->vars['foreach']['nodechatMessages']['count'] = count($this->vars['nodechatMessages']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['nodechatMessages']);
							   		 foreach($this->vars['nodechatMessages'] as $this->vars['foreach']['nodechatMessages']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['nodechatMessages']['key']; ?>
                                      <ul class="chats normal-chat" data-user_to="<?php if (isset($this->vars['item']['0']['from_uid']) && $this->vars['item']['0']['from_uid'] == $this->vars['userInfo']['user_id']): ?><?php if(isset($this->vars['item']['0']['to_uid'])) echo $this->vars['item']['0']['to_uid']; ?><?php else: ?><?php if(isset($this->vars['item']['0']['from_uid'])) echo $this->vars['item']['0']['from_uid']; ?><?php endif; ?>" style="display:none">
                                          <?php if(count($this->vars['item'])) : 
							   		 $this->vars['foreach']['item']['count'] = count($this->vars['item']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']);
							   		 foreach($this->vars['item'] as $this->vars['foreach']['item']['key'] => $this->vars['message']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item']['key']; ?>
                                            <li class="<?php if(isset($this->vars['message']['type_message'])) echo $this->vars['message']['type_message']; ?>">
                                                <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/photo.jpg" alt="" class="avatar">
                                                <div class="message ">
                                                    <span class="arrow"></span>
                                                    <span class="username"><?php if (isset($this->vars['message']['from_uid']) && $this->vars['message']['from_uid'] == $this->vars['userInfo']['user_id']): ?><?php if(isset($this->vars['message']['name_from'])) echo $this->vars['message']['name_from']; ?><?php else: ?><?php if(isset($this->vars['message']['name_from'])) echo $this->vars['message']['name_from']; ?><?php endif; ?></span>
                                                    <span class="datetime"><?php if(isset($this->vars['message']['timestamp'])) echo $this->vars['message']['timestamp']; ?></span>
													<span class="body">
                                                      <?php if(isset($this->vars['message']['text'])) echo $this->vars['message']['text']; ?>
													</span>
                                                </div>	
                                            </li>
                                          <?php endforeach; ?><?php endif; ?>
                                        </ul>
                                    <?php endforeach; ?><?php endif; ?>
                                    <div class="chat-form" style="display:none" id="nodechat-InputForm">
                                        <div class="input-cont">
                                        	<textarea style="width: 99%" rows="4"></textarea>
                                        </div>
                                        <div class="btn-cont">
                                            <a class="btn btn-primary" href="javascript:;" onclick="nodechat.send()">Отправить</a>
                                        </div>
                                        <!--
                                        <div class="upload-zone">
	                                      <b>Прикрепить файл:</b> 
                                          <form id="nodechat-upload" enctype="multipart/form-data" action="/ru/files/upload/source/nodechat/application/simple_upload/" method="post">
                                              <input type="hidden" name="callback" value="nodechat.uploadComplete">
                                              <div class="controls">
                                                  <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <span class="btn btn-file">
                                                      <span class="fileupload-new selectFile">Выбрать файл</span>
                                                      <span class="fileupload-new uploadStart" onclick="$(this).closest('form').submit();" style="display:none">Загрузить</span>
                                                      <input type="file" name="file" class="default" onchange="nodechat.startUpload(this)" />
                                                    </span>
                                                    <span class="fileupload-preview"></span>
                                                    <a href="javascript:void(0)" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                                                  </div>
                                              </div>
                                          </form>
                                          <div id="nodechat-UploadFiles" style="display:none"></div>
                                          <div id="nodechat-UploadProgress" style="display:none">
                                            <div class="progress progress-striped progress-success" style="width: 100%;">
                                              <div class="bar" id="nodechat-UploadProgress-bar" style="width: 0%;"></div>
                                            </div>
                                            <span></span>
                                          </div>
                                        </div>
										-->
                                    </div>
                                    
                                    
                               </div>
                               </td></tr>
                          </table>
                    </div>
               </div>               
            </div>     