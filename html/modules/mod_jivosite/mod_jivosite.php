<?php
   
/**
 * @package     Jivosite module
 * @subpackage  Module
 * @copyright   (C) 2016 http://jivosite.com
 * @license     License GNU General Public License version 2 or later; 
 */
   

defined('_JEXEC') or die;

$chatid = $params->get('chatcode');
$geo = $params->get('geo', '');
echo "<!--This code is installed via module --> <script type='text/javascript'>(function(){ var widget_id = '";
echo "$chatid";
if ($geo) {
		echo "';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/geo-widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>";
	}
	else {
		echo "';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>";
	}

?>
