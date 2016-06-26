<?php
################################################################################
# This file was created by M-cms core.                                         #
#                                                                              #
# This module configuration file.                                              #
# There you can add entry in the admin menu, specify which of the existing     #
# can use controllers. You can also specify the parameters of caching          #
# and specify template will be used.                                           #
# If in process call module does not find this file, it will be called 404.    #
#                                                                              #
# If you want to use any controller, you'll need to specify                    #
# in $controller->add_controller();                                            #
# Also, you can specify the default controller. This controller will be        #
# called if the URL is not specified controller or if specified controller     #
# was not found or not connected.                                              #
#                                                                              #
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.01.2009                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



$core->modules->this['menu'][]= array('controller' => 'list' , 'name'=>'Список альбомов', 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'add' , 'name'=>'Добавить альбом', 'action_id'=>5);
$core->modules->this['controllers_path']= '/controllers/';


$core->modules->add_controller('list', 'edit', 'delete', 'add', 'show', 'save');
$core->modules->add_default_controller('list');


$core->modules->this['tpl']['cach_param']= array('_site' => 1 , '_lang' => 1 , '_module' => 1 , '_action' => 1);
$core->modules->this['tpl']['cach_expire'] = 0;
$core->modules->this['tpl']['cached'] = 0;
$core->modules->this['tpl']['name'] = 'main.html';


    if($_SERVER['SERVER_NAME'] == 'admin.m-cms.loc')
        {
                $core->CONFIG['albums']['torrent']['pathForSave'] = $core->CONFIG['var_path'].'music/torrents/';
                $core->CONFIG['albums']['torrent']['anoncePath'] = 'http://m-cms.loc/ru/torrent-tracker/api/action/announce/';
                $core->CONFIG['albums']['torrent']['webgate']['user'] = 'root';
                $core->CONFIG['albums']['torrent']['webgate']['password'] = '123';
                $core->CONFIG['albums']['torrent']['webgate']['host'] = 'localhost';
                $core->CONFIG['albums']['albumsPath'] = $core->CONFIG['var_path'].'music/albums/';
                $core->CONFIG['albums']['zipPath'] = $core->CONFIG['var_path'].'music/archive/';
                $core->CONFIG['albums']['albumsURL'] = 'http://torrents.sharedmp3.ru/music/';
                $core->CONFIG['albums']['images']['url'] = 'http://covers.cms.loc/images';
                $core->CONFIG['albums']['images']['upload_path'] = 'C:/WEB/covers.cms.loc/images/';  
                $core->CONFIG['albums']['images']['local_path'] = '/';
                $core->CONFIG['albums']['images']['thumb']['width'] = 50;
                $core->CONFIG['albums']['images']['thumb']['height'] = 50;
                $core->CONFIG['albums']['images']['thumb']['path'] = '/thumbs/';
                $core->CONFIG['albums']['images']['preview']['width'] = 150;
                $core->CONFIG['albums']['images']['preview']['height'] = 150;
                $core->CONFIG['albums']['images']['preview']['path'] = '/preview/';
                $core->CONFIG['albums']['images']['source']['width'] = 200;
                $core->CONFIG['albums']['images']['source']['height'] = 200;
        }
        else
                {
                        $core->CONFIG['albums']['torrent']['pathForSave'] = $core->CONFIG['var_path'].'music/torrents/';
                 		$core->CONFIG['albums']['torrent']['anoncePath'] = 'http://www.ultra-music.org/ru/torrent-tracker/api/action/announce/';
                        $core->CONFIG['albums']['torrent']['webgate']['user'] = 'root';
                        $core->CONFIG['albums']['torrent']['webgate']['password'] = '123';
                        $core->CONFIG['albums']['torrent']['webgate']['host'] = 'localhost';
                        $core->CONFIG['albums']['albumsPath'] = $core->CONFIG['var_path'].'music/albums/';
                        $core->CONFIG['albums']['zipPath'] = $core->CONFIG['var_path'].'music/archive/';

                        
                        $core->CONFIG['albums']['torrent']['torrentsURL'] = 'http://bt1.sharedmp3.ru/torrents/';
                        $core->CONFIG['albums']['torrent']['torrentsURL'] = 'http://78.47.15.216/torrents/';
                  
                        $core->CONFIG['albums']['albumsURL'] = 'http://bt1.sharedmp3.ru/music/';          
                        $core->CONFIG['albums']['albumsURL'] = 'http://78.47.15.216/music/';          
                        
                        
                        
                        $core->CONFIG['albums']['images']['url'] = 'http://covers.ultra-music.org/images';
                        $core->CONFIG['albums']['images']['upload_path'] = '/var/www/covers.ultra-music.org/www-data/images/';  
                        $core->CONFIG['albums']['images']['local_path'] = '/';
                        $core->CONFIG['albums']['images']['thumb']['width'] = 50;
                        $core->CONFIG['albums']['images']['thumb']['height'] = 50;
                        $core->CONFIG['albums']['images']['thumb']['path'] = '/thumbs/';
                        $core->CONFIG['albums']['images']['preview']['width'] = 150;
                        $core->CONFIG['albums']['images']['preview']['height'] = 150;
                        $core->CONFIG['albums']['images']['preview']['path'] = '/preview/';
                        $core->CONFIG['albums']['images']['source']['width'] = 200;
                        $core->CONFIG['albums']['images']['source']['height'] = 200;
                        $core->CONFIG['albums']['meta_description'] = 'Скачать mp3 бесплатно, электронная музыка, mp3 torrent, free download mp3, free electronic music';
                        $core->CONFIG['albums']['meta_keywords'] = 'free mp3 downloads, Скачать mp3 бесплатно,электронная музыка, mp3 torrent, free download mp3, free electronic music';
                }




?>