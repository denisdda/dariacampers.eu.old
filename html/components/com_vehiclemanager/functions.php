<?php

/**
*
* @package VehicleManager
* @copyright 2020 by Ordasoft
* @author Andrey Kvasnevskiy - OrdaSoft (akbet@mail.ru); Rob de Cleen (rob@decleen.com);
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Homepage: https://ordasoft.com/
*
* */

if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

if (isset($GLOBALS['mosConfig_absolute_path']))
  $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];
else
  $mosConfig_absolute_path = JPATH_SITE; //JURI::base(true); // if file is calling from the module

include_once ($mosConfig_absolute_path . '/' . 'components' . '/' . 'com_vehiclemanager' . '/' . 'vehiclemanager.main.categories.class.php');
if (version_compare(JVERSION, '3.0', 'lt')){
  require_once (JPATH_ROOT . '/' . 'libraries' . '/' . 'joomla' . '/' . 'database' . '/' . 'table' . '/' . 'menu.php' );
}
require_once ($mosConfig_absolute_path . '/' . 'components' . '/' . 'com_vehiclemanager' . '/' . 'includes' . '/' . 'menu.php' );
jimport('joomla.html.pagination');
if (!isset($GLOBALS['vehiclemanager_configuration'])){
  require_once (JPATH_ROOT . '/' . 'administrator' . '/' . 'components' . '/' . 'com_vehiclemanager' . '/' . 'admin.vehiclemanager.class.conf.php' );
  $GLOBALS['vehiclemanager_configuration'] = isset($vehiclemanager_configuration) ? $vehiclemanager_configuration : null ;
}

use Joomla\Utilities\ArrayHelper;

/**
 * Legacy function, use <jdoc:include type="module" /> instead
 *
 * @deprecated As of version 1.5
 */
if (!function_exists('mosLoadModule'))
{

    function mosLoadModule($name, $style = -1)
    {
        ?><jdoc:include type="module" name="<?php echo $name ?>" style="<?php echo $style ?>" /><?php
    }

}

jimport( 'joomla.filesystem.file' );
if(!function_exists('vm_createImage')){
    function vm_createImage($imgSrc, $imgDest, $width, $height, $crop = true) {
        if (JFile::exists($imgDest)) {
            $info = getimagesize($imgDest, $imageinfo);

            if (($info[0] == $width) && ($info[1] == $height)) {
                return;
            }
        }
        if (JFile::exists($imgSrc)) {
            $info = getimagesize($imgSrc, $imageinfo);
            $sWidth = $info[0];
            $sHeight = $info[1];

                vm_resize_img($imgSrc, $imgDest, $width, $height, $crop);


        }
    }
}

if (!function_exists('incorrect_price')) {
  function incorrect_price($price){
//    if(preg_match('/\D/i',$price) == 1){
//    if(preg_match('/^[0-9]*\.{0,1}[0-9]*$/',$price) == 1){
    if(preg_match('/^[0-9]*[.]{0,1}[0-9]*$/',$price) == 1){
      //price is correct !
      return false;
    }else{
      return true;
    }
  }
}

if(!function_exists('vm_resize_img')){
    function vm_resize_img($imgSrc, $imgDest, $tmp_width, $tmp_height, $crop = true) {
        $info = getimagesize($imgSrc, $imageinfo);
        $sWidth = $info[0];
        $sHeight = $info[1];
    //if (($info[0] >= $tmp_width) && ($info[1] >= $tmp_height)) {
    if (($info[0] >= $tmp_width) || ($info[1] >= $tmp_height)) {
        if ($sHeight / $sWidth > $tmp_height / $tmp_width) {
            $width = $sWidth;
            $height = round(($tmp_height * $sWidth) / $tmp_width);
            $sx = 0;
            $sy = round(($sHeight - $height) / 3);
        }
        else {
            $height = $sHeight;
            $width = round(($sHeight * $tmp_width) / $tmp_height);
            $sx = round(($sWidth - $width) / 2);
            $sy = 0;
        }

        if (!$crop) {
            $sx = 0;
            $sy = 0;
            $width = $sWidth;
            $height = $sHeight;
        }
    } else {
            $sx = 0;
            $sy = 0;
            $width = $sWidth;
            $height = $sHeight;
    }

        $ext = str_replace('image/', '', $info['mime']);
        $imageCreateFunc = vm_getImageCreateFunction($ext);
        $imageSaveFunc = vm_getImageSaveFunction($ext);

        $sImage = $imageCreateFunc($imgSrc);
        if (($info[0] >= $tmp_width) && ($info[1] >= $tmp_height)) {
        $dImage = imagecreatetruecolor($tmp_width, $tmp_height);
        }
        else {$dImage = imagecreatetruecolor($width, $height);
        $tmp_width = $width;
        $tmp_height = $height;
        }

        // Make transparent
        if ($ext == 'png') {
            imagealphablending($dImage, false);
            imagesavealpha($dImage,true);
            $transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
            imagefilledrectangle($dImage, 0, 0, $tmp_width, $tmp_height, $transparent);
        }

        imagecopyresampled($dImage, $sImage, 0, 0, $sx, $sy, $tmp_width, $tmp_height, $width, $height);

        if ($ext == 'png') {
          //for png quality must be 0 - 9
          //0 - max quality no copress, 9 - low quality max compress
          $quality = 6;
          $imageSaveFunc($dImage, $imgDest, $quality);
        }
        else if ($ext == 'bmp') {
          $compressed = true;
          $imageSaveFunc($dImage, $imgDest, $compressed);
        }
        else if ($ext == 'gif') {
          $imageSaveFunc($dImage, $imgDest);
        }
        else if ($ext == 'vnd.wap.wbmp' || $ext == 'xbm') {
          $foreground = '';
          if(isset($foreground) && !empty($foreground)){
            $imageSaveFunc($dImage, $imgDest, $foreground);
          }
          else{
            $imageSaveFunc($dImage, $imgDest);
          }
        }
        else {
          //for jpg or jpeg
          //for png quality must be 0 - 100
          //0 - low quality max compress, 100 - max quality no copress
          $quality = 75;
          $imageSaveFunc($dImage, $imgDest, $quality);
        }
    }
}


if(!function_exists('vm_getImageCreateFunction')){
    function vm_getImageCreateFunction($type) {
        switch ($type) {
            case 'jpeg':
            case 'jpg':
                $imageCreateFunc = 'imagecreatefromjpeg';
                break;

            case 'png':
                $imageCreateFunc = 'imagecreatefrompng';
                break;

            case 'bmp':
                $imageCreateFunc = 'imagecreatefrombmp';
                break;

            case 'gif':
                $imageCreateFunc = 'imagecreatefromgif';
                break;

            case 'vnd.wap.wbmp':
                $imageCreateFunc = 'imagecreatefromwbmp';
                break;

            case 'xbm':
                $imageCreateFunc = 'imagecreatefromxbm';
                break;

            default:
                $imageCreateFunc = 'imagecreatefromjpeg';
        }

        return $imageCreateFunc;
    }
}
if(!function_exists('vm_getImageSaveFunction')){
    function vm_getImageSaveFunction($type) {
        switch ($type) {
            case 'jpeg':
                $imageSaveFunc = 'imagejpeg';
                break;

            case 'png':
                $imageSaveFunc = 'imagepng';
                break;

            case 'bmp':
                $imageSaveFunc = 'imagebmp';
                break;

            case 'gif':
                $imageSaveFunc = 'imagegif';
                break;

            case 'vnd.wap.wbmp':
                $imageSaveFunc = 'imagewbmp';
                break;

            case 'xbm':
                $imageSaveFunc = 'imagexbm';
                break;

            default:
                $imageSaveFunc = 'imagejpeg';
        }

        return $imageSaveFunc;
    }
}

 /**
  * Saves the record on an edit form submit
  * @param database A database connector object
  */
if(!function_exists('vm_picture_thumbnail')){
  function vm_picture_thumbnail($file, $high_original, $width_original, $watermark = false){


    global $mosConfig_absolute_path, $vehiclemanager_configuration;

    //min size in order to adding watermark
    $min_image_width = $vehiclemanager_configuration['watermark']['min_image_width'];
    $min_image_high = $vehiclemanager_configuration['watermark']['min_image_high'];

    $watermark_path = ($watermark) ? 'watermark/' : '';

    $params3 = $vehiclemanager_configuration['thumb_param']['show'];//resize or crop. VehicleManager : Settings - Settings - Crop image: YES - $params3 = 1 (crop), NO - $params3 = 0 (resize, scale image).
    $uploaddir = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/';
    $watermarkfile = $file;


    if($file === '' || !file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/' . $file)){
        copy ( $mosConfig_absolute_path . '/components/com_vehiclemanager/images/no-img_eng_big.gif' ,
                $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/no-img_eng_big.gif' );
        $file = 'no-img_eng_big.gif';
    }elseif($watermark && (($vehiclemanager_configuration['watermark']['type'] == 'text' && $vehiclemanager_configuration['watermark']['text'] != '')
                    || ($vehiclemanager_configuration['watermark']['type'] == 'image' && file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark/watermark_img.png')))){

        if(!file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark/' . $file)){

            veh_createWaterMark($file, $vehiclemanager_configuration['watermark']);
        }


        if($high_original < $min_image_high || $width_original < $min_image_width){
            $watermark = false;
            // $file  = str_ireplace('watermark/', '', $file);
        }else{
            $file = $watermark_path.$file;
        }

    }

    $file_inf = pathinfo($file);
    $file_type = '.' . $file_inf['extension'];
    $file_name = basename($file, $file_type);

    $watermarkfile_name = $file_name;

    $file_name = $watermark_path . $file_name;

    //resize or crop. VehicleManager : Settings - Settings - Crop image: YES - $params3 = 1 (crop), NO - $params3 = 0 (resize, scale image).
    if($params3 == 1){
        $index = "_2_";
    }else{
        $index = "_1_";
    }

    // Setting the resize parameters
    // echo $watermark_path;
    list($width, $height) = getimagesize($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file);

    $size = "_" . $width_original . "_" . $high_original;

    if (file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file_name . $size . $index . $file_type)){

      return $file_name . $size . $index . $file_type;
    }else{
      if ($width < $height){
        if ($height > $high_original){
          $k = $height / $high_original;
        }else if ($width > $width_original){
          $k = $width / $width_original;
        }
        else
          $k = 1;
      }else{
        if ($width > $width_original){
          $k = $width / $width_original;
        }else if ($height > $high_original){
            $k = $height / $high_original;
        }else
          $k = 1;
      }
      $w_ = $width / $k;
      $h_ = $height / $k;
    }

    if($params3 == 1){//resize or crop. VehicleManager : Settings - Settings - Crop image: YES - $params3 = 1 (crop), NO - $params3 = 0 (resize, scale image).

        if ($watermark == false){
        $CreateNewImage = vm_createImage($uploaddir.$file, $uploaddir . $file_name . $size. $index
            .$file_type,$width_original , $high_original);

        return $file_name . $size . $index . $file_type;

        } elseif(($vehiclemanager_configuration['watermark']['type'] == 'text' && $vehiclemanager_configuration['watermark']['text'] != '')
                    || ($vehiclemanager_configuration['watermark']['type'] == 'image' && file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark/watermark_img.png'))) {
            if (file_exists($uploaddir . $watermarkfile_name . $size. $index .$file_type))
            {

                $fileforwatermark = $watermarkfile_name . $size. $index .$file_type;

                veh_createWaterMark($fileforwatermark, $vehiclemanager_configuration['watermark']);
                unlink($uploaddir.$fileforwatermark);
                return $file_name . $size . $index . $file_type;
            } else {
                $fileforwatermark = $watermarkfile_name . $size. $index .$file_type;

                $CreateNewImage = vm_createImage($uploaddir.$watermarkfile, $uploaddir . $watermarkfile_name . $size. $index
                .$file_type,$width_original , $high_original);

                veh_createWaterMark($fileforwatermark, $vehiclemanager_configuration['watermark']);
                unlink($uploaddir.$fileforwatermark);
                return $file_name . $size . $index . $file_type;
            }


        }else{
            $CreateNewImage = vm_createImage($uploaddir.$file, $uploaddir . $file_name . $size. $index
            .$file_type,$width_original , $high_original);

            return $file_name . $size . $index . $file_type;
        }
    }

    // Creating the Canvas
    $tn = imagecreatetruecolor($w_, $h_);
    switch (strtolower($file_type)) {
        case '.png':
            $source = imagecreatefrompng($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/' . $file);
            $file = imagecopyresampled($tn, $source, 0, 0, 0, 0, $w_, $h_, $width, $height);
            imagepng($tn, $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file_name . $size . $index . $file_type);
            break;
        case '.jpg':
            $source = imagecreatefromjpeg($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/' . $file);
            $file = imagecopyresampled($tn, $source, 0, 0, 0, 0, $w_, $h_, $width, $height);
            imagejpeg($tn, $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file_name . $size . $index . $file_type);
            break;
        case '.jpeg':
            $source = imagecreatefromjpeg($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/' . $file);
            $file = imagecopyresampled($tn, $source, 0, 0, 0, 0, $w_, $h_, $width, $height);
            imagejpeg($tn, $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file_name . $size . $index . $file_type);

            break;
        case '.gif':
            $source = imagecreatefromgif($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file);
            $file = imagecopyresampled($tn, $source, 0, 0, 0, 0, $w_, $h_, $width, $height);
            imagegif($tn, $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'. $file_name . $size . $index . $file_type);
            break;
        default:
            echo 'not support';
            return;
    }


    return  $file_name . $size . $index . $file_type;
  }
}

/**
 * Legacy function, using <jdoc:include type="modules" /> instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosMail'))
{
    function mosMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL)
    {
        // old version function
        // $mail = JMail::getInstance();
        // return $mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
        // ----------------------------------------------------
        $mailer = JFactory::getMailer();

        $mailer->setSender(array($from, $fromname));
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        // Are we sending the email as HTML?
        if ($mode) {
            $mailer->isHTML(true);
        }

        if (!is_array($recipient)) {
            $recipient = explode(',', $recipient);
        }

        $mailer->addRecipient($recipient);
        $mailer->addCC($cc);
        $mailer->addBCC($bcc);
        $mailer->addAttachment($attachment);

        // Take care of reply email addresses
        if (is_array($replyto)) {
            $numReplyTo = count($replyto);
            for ($i=0; $i < $numReplyTo; $i++){
                $mailer->addReplyTo(array($replyto[$i], $replytoname[$i]));
            }
        } elseif (isset($replyto)) {
            $mailer->addReplyTo(array($replyto, $replytoname));
        }
        return  $mailer->Send();
    }
}

if (!function_exists('date_to_data_ms')){
    function date_to_data_ms($data_string){             // 2014-01-25 covetr to date in ms

        global $database;
        if($data_string){
           $rent_mas = explode('-', $data_string);
           $month=$rent_mas[1];
           $day=$rent_mas[2];
           $year=$rent_mas[0];
           $rent_ms = mktime ( 0 ,0, 0, $month , $day , $year);
           return $rent_ms;
       }else{
            exit;
        }
    }
}

if (!function_exists('mosLoadAdminModules'))
{

    function mosLoadAdminModules($position = 'left', $style = 0)
    {
        // Select the module chrome function
        if (is_numeric($style))
        {
            switch ($style) {
                case 2:
                    $style = 'xhtml';
                    break;
                case 0:
                default:
                    $style = 'raw';
                    break;
            }
        }
        ?><jdoc:include type="modules" name="<?php echo $position ?>" style="<?php echo $style ?>" /><?php
    }

}


/**
 * Legacy function, using <jdoc:include type="module" /> instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosLoadAdminModule'))
{

    function mosLoadAdminModule($name, $style = 0)
    {
        ?><jdoc:include type="module" name="<?php echo $name ?>" style="<?php echo $style ?>" /><?php
    }

}




/**
 * Legacy function, use {@link JFolder::files()} or {@link JFolder::folders()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosReadDirectory'))
{

    function mosReadDirectory($path, $filter = '.', $recurse = false, $fullpath = false)
    {
        $arr = array(null);
        // Get the files and folders
        jimport('joomla.filesystem.folder');
        $files = JFolder::files($path, $filter, $recurse, $fullpath);
        $folders = JFolder::folders($path, $filter, $recurse, $fullpath);
        // Merge files and folders into one array
        $arr = array();
        if (is_array($files))
            $arr = $files;
        if (is_array($folders))
            $arr = array_merge($arr, $folders);
        // Sort them all
        asort($arr);
        return $arr;
    }

}


/**
 * Legacy function, use {@link JApplication::redirect() JApplication->redirect()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosRedirect'))
{

    function mosRedirect($url, $msg = '')
    {
        global $mainframe;

        if( $msg != '' ) $mainframe->enqueueMessage($msg) ;
        $mainframe->redirect($url);
    }

}


if (!function_exists('imagecopymerge_alpha')) {
    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        // creating a cut resource
        //var_dump($src_x);        var_dump($src_y); exit;
        $cut = imagecreatetruecolor($src_w, $src_h);

        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        imagealphablending($dst_im, false);
        imagesavealpha($dst_im,true);
        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
        return $dst_im;
    }
}


if (!function_exists('getImageCreateFunction')) {
  function getImageCreateFunction($type){
        switch ($type) {
            case 'jpeg':
            case 'jpg':
                $imageCreateFunc = 'imagecreatefromjpeg';
                break;

            case 'png':
                $imageCreateFunc = 'imagecreatefrompng';
                break;

            case 'bmp':
                $imageCreateFunc = 'imagecreatefrombmp';
                break;

            case 'gif':
                $imageCreateFunc = 'imagecreatefromgif';
                break;

            case 'vnd.wap.wbmp':
                $imageCreateFunc = 'imagecreatefromwbmp';
                break;

            case 'xbm':
                $imageCreateFunc = 'imagecreatefromxbm';
                break;

            default:
                $imageCreateFunc = 'imagecreatefromjpeg';
                break;
        }
        return $imageCreateFunc;
    }
}

if (!function_exists('getImageSaveFunction')) {
    function getImageSaveFunction($type) {
        switch ($type) {
            case 'jpeg':
                $imageSaveFunc = 'imagejpeg';
                break;

            case 'png':
                $imageSaveFunc = 'imagepng';
                break;

            case 'bmp':
                $imageSaveFunc = 'imagebmp';
                break;

            case 'gif':
                $imageSaveFunc = 'imagegif';
                break;

            case 'vnd.wap.wbmp':
                $imageSaveFunc = 'imagewbmp';
                break;

            case 'xbm':
                $imageSaveFunc = 'imagexbm';
                break;

            default:
                $imageSaveFunc = 'imagejpeg';
                break;
        }
        return $imageSaveFunc;
    }
}



// add watermark
if (!function_exists('veh_createWaterMark')) {

  function veh_createWaterMark($file_name, $params){

      global $mosConfig_absolute_path;
      global $vehiclemanager_configuration;

      $original_src = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/'.$file_name;
      $original_info = getimagesize($original_src, $original_info);

      $original_width = $original_info[0];
      $original_height = $original_info[1];
      $original_ext = str_replace('image/', '', $original_info['mime']);

      //get create and save function
      $imageCreateFunc = getImageCreateFunction($original_ext);
      $imageSaveFunc = getImageSaveFunction($original_ext);
      $sImage = $imageCreateFunc($original_src);


      $dImage = imagecreatetruecolor($original_width, $original_height);


      // Make transparent
      if ($original_ext == 'png') {
        imagealphablending($dImage, false);
        imagesavealpha($dImage,true);
        $transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
        imagefilledrectangle($dImage, 0, 0, $original_width, $original_height, $transparent);
      }

      //get watermark
      if($vehiclemanager_configuration['watermark']['type']== 'image'){
            $mark = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark/watermark_img.png';
      }else{
          //create text watermark
          //get size of text

      $text = $params["text"];
      $font = $mosConfig_absolute_path . '/components/com_vehiclemanager/includes/fonts/font.ttf';
      $textSizes = 200;
      $textAngle = $params["angle"];
      //size,angle
      $TextSizes = imagettfbbox($textSizes, $textAngle, $font, $text);
      if($textAngle==45){
          $width = $height = abs($TextSizes[5] - $TextSizes[1]);
      }else{
          $width = abs($TextSizes[4] - $TextSizes[0]);
          $height = abs($TextSizes[5] - $TextSizes[1]);
      }

      $height = $height*1.5;

      $mark = imagecreatetruecolor($width, $height);

      imagealphablending($mark, false);
      imagesavealpha($mark,true);
      $white = imagecolorallocatealpha($mark, 255, 255, 255, 127);
      $grey = imagecolorallocate($mark, 128, 128, 128);
      $fontColor = str_replace("rgba(", '', $params["color"]);
      $fontColor = str_replace(")", '', $fontColor);
      $fontColor = explode(',', $fontColor);
      $r = isset($fontColor[0])?$fontColor[0]:0;
      $g = isset($fontColor[1])?$fontColor[1]:0;
      $b = isset($fontColor[2])?$fontColor[2]:0;



      $fontColor = imagecolorallocate($mark, $r, $g, $b);
      imagefilledrectangle($mark, 0, 0, $width, $height, $white);

      // Тень
      //size,angle
      if($textAngle ==45){
          $margin = $textSizes/2;
          $offset = $height;
      }else if($textAngle == 90){
          $margin = $textSizes;
          $offset = $height;
          }else{
              $margin = 0;
              $offset = $textSizes;
          }
          imagettftext($mark, $textSizes, $textAngle, $margin+1, $offset+1, $grey, $font, $text);
          // Текст
          //size,angle
          if($textAngle ==45){
              $margin = $textSizes/2;
              $offset = $height;
          }else if($textAngle == 90){
              $margin = $textSizes;
              $offset = $height;
          }else{
              $margin = 0;
              $offset = $textSizes;
          }
          imagettftext($mark, $textSizes, $textAngle, $margin, $offset, $fontColor, $font, $text);

          $dest = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark/text_watermark.png';
          imagepng($mark, $dest);
          imagedestroy($mark);

          $mark = $dest;
      }
          $watermark_info = getimagesize($mark, $watermark_info);

          $watermark_ext = str_replace('image/', '', $watermark_info['mime']);
          $watermarkCreateFunc = getImageCreateFunction($watermark_ext);
          $watermark = $watermarkCreateFunc($mark);
          $watermark_width = imagesx($watermark);
          $watermark_height = imagesy($watermark);

          $margin = 10;


          //old size (value in px)
          // $sx = ($watermark_width < $original_width)?$watermark_width : $original_width-$original_width*0.5;
          // $sy = ($watermark_height < $original_height)?$watermark_height : $original_height-$original_height*0.5;;

          $sx = $original_width*$params["size"]/100;
          $sy = $watermark_height*($sx/$watermark_width);

          $xx = $original_width;
          $yy = $original_height;

          $position = $params["position"];

          switch ($position) {
              case 'top_left':
                  $x = $margin;
                  $y = $margin;
                  break;
              case 'top_right':
                  $x = $xx - $sx - $margin;
                  $y = $margin;
                  break;
              case 'bottom_left':
                  $x = $margin;
                  $y = $yy - $sy - $margin;
                  break;
              case 'bottom_right':
                  $x = $xx - $sx - $margin;
                  $y = $yy - $sy - $margin;
                  break;
              case 'center':
                  $x = $xx/2-$sx/2;
                  $y = $yy/2-$sy/2;
                  break;
          }

          //RESIZE watermark save transparrent

          $resize_mark = imagecreatetruecolor($sx, $sy);
          imagealphablending($resize_mark, false);
          imagesavealpha($resize_mark,true);
          $transparent = imagecolorallocatealpha($resize_mark, 255, 255, 255, 127);
          imagecopyresampled($resize_mark, $watermark, 0, 0, 0, 0, $sx, $sy, $watermark_width, $watermark_height);

          //save transparent in main image


          $sImage = imagecopymerge_alpha($sImage, $resize_mark, $x, $y, 0, 0, $sx, $sy,$params["opacity"]);

          $save_src = $mosConfig_absolute_path . '/components/com_vehiclemanager/photos/watermark/'.$file_name;

          if ($original_ext == 'png') {
            $imageSaveFunc($sImage, $save_src, 9);
          }
          else if ($original_ext == 'gif') {
            $imageSaveFunc($sImage, $save_src, 90);
          }
          else {
            $imageSaveFunc($sImage, $save_src, 90);
            imagedestroy($sImage);
          }
  }

}

if (!function_exists("formatMoney")){
  function formatMoney($number, $fractional = false, $pattern = "."){
    if(preg_match("/\d/", $pattern)){
      $msg = "Your separator: $pattern - incorrect, you can not use numbers, to split price" ;
      echo '<script>alert("'.$msg.'");</script>';
      $pattern = ".";
    }
    if ($fractional){
      $number = sprintf('%.2f', $number);
    }
    if ($pattern == ".")
      $number = str_replace(".", ",", $number);
    while (true) {
      $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1' . $pattern . '$2', $number);
      //echo $replaced."<br>";
      if ($replaced != $number){
        $number = $replaced;
      } else{
        break;
      }
    }
    // $number = preg_replace('/\^/', $number, $pattern);
    return $number;
  }
}



/**
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosGetParam'))
{

    function mosGetParam(&$arr, $name, $def = null, $mask = 0)
    {

        // Static input filters for specific settings
        static $noHtmlFilter = null;
        static $safeHtmlFilter = null;

        $var = ArrayHelper::getValue($arr, $name, $def, '');

        // If the no trim flag is not set, trim the variable
        if (!($mask & 1) && is_string($var))
            $var = trim($var);

        // Now we handle input filtering
        if ($mask & 2)
        {
            // If the allow html flag is set, apply a safe html filter to the variable
            if (is_null($safeHtmlFilter))
                $safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
            $var = $safeHtmlFilter->clean($var, 'none');
        } elseif ($mask & 4)
        {
            // If the allow raw flag is set, do not modify the variable
            $var = $var;
        } else
        {
            // Since no allow flags were set, we will apply the most strict filter to the variable
            if (is_null($noHtmlFilter))
            {
                $noHtmlFilter = JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
            }
            $var = $noHtmlFilter->clean($var, 'none');
        }
        return $var;
    }

}


/**
 * Legacy function, use {@link JEditor::save()} or {@link JEditor::getContent()} instead
 *
 * @deprecated  As of version 1.5
 */
if( !function_exists('editorArea')) {
    function editorArea($name, $content, $hiddenField, $width, $height, $col, $row,$option=true) {
            jimport( 'joomla.html.editor' );
            $editor = JFactory::getEditor();
            echo $editor->display($hiddenField, $content, $width, $height, $col, $row,$option);
    }
}


/**
 * Legacy function, use {@link JFilterOutput::objectHTMLSafe()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosMakeHtmlSafe'))
{

    function mosMakeHtmlSafe(&$mixed, $quote_style = ENT_QUOTES, $exclude_keys = '')
    {
        JFilterOutput::objectHTMLSafe($mixed, $quote_style, $exclude_keys);
    }

}


/**
 * Legacy utility function to provide ToolTips
 *
 * @deprecated As of version 1.5
 */
/* The Mootools have conflict with GoogleMap. GoogleMap StreetView don`t work when Mootools turn on.
It looks like we use the Mootools only for the function "window.addEvent(domready, function() {". Replace this function on Mootools on the same function on jQoery "jQuerOs(document).ready(function() {".
In the VM at all do not understand why did the Mootools used.
Turn off The Mootools in our components. */
/*if (!function_exists('mosToolTip'))
{
    function mosToolTip($tooltip, $title = '', $width = '', $image = 'tooltip.png', $text = '', $href = '', $link = 1)
    {
        // Initialize the toolips if required
        static $init;
        if (!$init)
        {
            JHTML::_('behavior.tooltip');
            $init = true;
        }
        return JHTML::_('tooltip', $tooltip, $title, $image, $text, $href, $link);
    }
}*/

/**
 * Legacy function to replaces &amp; with & for xhtml compliance
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosTreeRecurse'))
{

    function mosTreeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
    {
        jimport('joomla.html.html');
        return JHTML::_('menu.treerecurse', $id, $indent, $list, $children, $maxlevel, $level, $type);
    }

}
if(!class_exists('vmLittleThings')){
  class vmLittleThings{
    static function getGroupsByUser($uid, $recurse){
      if (version_compare(JVERSION, "1.6.0", "ge")){
          $database = JFactory::getDBO();
          // Custom algorythm
          $usergroups = array();
          if ($recurse == 'RECURSE'){
            // [1]: Recurse getting the usergroups
            $id_group = array();
            $q1 = "SELECT group_id FROM `#__user_usergroup_map` WHERE user_id={$uid}";
            $database->setQuery($q1);
            $rows1 = $database->loadObjectList();
            foreach ($rows1 as $v)
                $id_group[] = $v->group_id;
            for ($k = 0; $k < count($id_group); $k++) {
                $q = "SELECT g2.id FROM `#__usergroups` g1 LEFT JOIN `#__usergroups` g2 ON g1.lft > g2.lft AND g1.lft < g2.rgt WHERE g1.id={$id_group[$k]} ORDER BY g2.lft";
                $database->setQuery($q);
                $rows = $database->loadObjectList();
                foreach ($rows as $r)
                    $usergroups[] = $r->id;
            }
            $usergroups = array_unique($usergroups);
          }
          // [2]: Non-Recurse getting usergroups
          $q = "SELECT * FROM #__user_usergroup_map WHERE user_id = {$uid}";
          $database->setQuery($q);
          $rows = $database->loadObjectList();
          foreach ($rows as $k => $v)
            $usergroups[] = $rows[$k]->group_id;
          // If user is unregistered, Joomla contains it into standard group (Public by default).
          // So, groupId for anonymous users is 1 (by default).
          // But custom algorythm doesnt do this: if user is not autorised, he will NOT connected to any group.
          // And groupId will be 0.
          if (count($rows) == 0)
            $usergroups[] = -2;
          return $usergroups;
      }else{
        echo "Sanity test. Error version check!";
        exit;
      }
    }

    /*
      [description]: instead of using $usergroups.
      (alias_name) its a `#__vehiclemanager_main_categories` table alias.
      (alias_name) depends of the particular query, as usual its "c", "cc" or something like this.
      [call]: $s = vmLittleThings::getWhereUsergroupsString ( "alias_name" );
      [returns]: a WHERE condition for SQL query
      which can be inserted into query like ({$s})
     */

    static function getWhereUsergroupsCondition($table_alias = "c"){
      if (version_compare(JVERSION, "1.6.0", "lt")){
        global $my;
        if (!isset($my))
        { // echo "User is logged out";
          if ($my = JFactory::getUser())
            $gid = $my->gid; else
            $gid = 0;
        } else
          $gid = $my->gid;
        $usergroups_sh = array($gid, -2);
        $s = '';
        for ($i = 0; $i < count($usergroups_sh); $i++) {
          $g = $usergroups_sh[$i];
          $s .= " $table_alias.params LIKE '%,{$g}' or $table_alias.params = '{$g}' or $table_alias.params LIKE '{$g},%' or $table_alias.params LIKE '%,{$g},%' ";
          if (($i + 1) < count($usergroups_sh))
            $s .= ' or ';
        }
        return $s;
      } else if (version_compare(JVERSION, "1.6.0", "ge")){
        $my = JFactory::getUser();
        if (isset($my->id) AND $my->id != 0)
          $usergroups_sh = vmLittleThings::getGroupsByUser($my->id, '');
        else
          $usergroups_sh = array();
        $usergroups_sh[] = -2;
        $s = '';
        for ($i = 0; $i < count($usergroups_sh); $i++) {
          $g = $usergroups_sh[$i];
          $s .= " $table_alias.params LIKE '%,{$g}' or $table_alias.params = '{$g}' or $table_alias.params LIKE '{$g},%' or $table_alias.params LIKE '%,{$g},%' ";
          if (($i + 1) < count($usergroups_sh))
            $s .= ' or ';
        }
        return $s;
      } else{
        echo "Sanity test. Error version check!";
        exit;
      }
    }

    /*
     * function categoryArray ()
     * Replaces the old com_veh_categoryArray ()
     * Returns the Category list depending of user access level.
     */

    static function categoryArray($lang = ''){
      global $database, $my;

      $s = vmLittleThings::getWhereUsergroupsCondition("c");
      $query = "SELECT c.*, c.parent_id AS parent, c.params AS access"
              . "\n FROM #__vehiclemanager_main_categories AS c"
              . "\n  LEFT JOIN #__vehiclemanager_categories AS vc ON c.id=vc.idcat \n"
              . "LEFT JOIN #__vehiclemanager_vehicles AS v ON v.id=vc.iditem AND v.published=1 AND v.approved=1"
              . "\n WHERE section='com_vehiclemanager'"
              . "\n AND c.published <> 0"
              . "\n AND ({$s})" . $lang
              . "\n ORDER BY ordering";
      $database->setQuery($query);
      $items = $database->loadObjectList();
      // establish the hierarchy of the menu
      $children = array();
      // first pass - collect children
      foreach ($items as $v) {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
      }

      // second pass - get an indent list of the items
      $array = vmLittleThings::vehicleManagerTreeRecurse(0, '', array(), $children);
      return $array;
    }

    /*
     * Redefines a standard function to not display &nbsp;
     * 1.6
     */

    static function vehicleManagerTreeRecurse($id, $indent, $list, &$children, $maxlevel = 9999,
                                                        $level = 1, $type = 1, $parents_list = ''){
      if (@$children[$id] && $level <= $maxlevel){
        $parent_id = $id;

        foreach ($children[$id] as $v) {
          $id = $v->id;
          if ($type){
              $pre = " ";
              $spacer = '. -- ';
              $parent_item = $parent_id;
          } else{
            $pre = "- ";
            $spacer = ' . -';
          }
          if ($v->parent == 0){
            $txt = $v->name;
          } else{
            $txt = $pre . $v->name;
          }
          $pt = $v->parent;
          $list[$id] = $v;
          $list[$id]->level = $level;
          $list[$id]->parents_list = $parents_list;
          $list[$id]->treename = "$indent$txt";

          if(isset($children[$id])){
              $list[$id]->children = count(@$children[$id]);
          }else{
              $list[$id]->children = 0;
          }

          $list[$id]->all_fields_in_list = count(@$children[$parent_id]);
          $list = vmLittleThings::vehicleManagerTreeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type,$parents_list." ".$parent_item);
        }
      }

      return $list;
    }

    // An updated back-end SubMenu helper
    static function addSubmenu($vName){

        if (version_compare(JVERSION, "4.0.0-alpha10", "ge")) {
            return;
        } else { 
          if (!defined('_VEHICLE_HEADER_NUMBER')) vmLittleThings::loadConstVechicle();

          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_HEADER_NUMBER),
                   'index.php?option=com_vehiclemanager', $vName == 'Vehicles'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_CATEGORIES_NAME),
                   'index.php?option=com_vehiclemanager&section=categories', $vName == 'Categories'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_MANAGER_LABEL_REVIEWS),
                   'index.php?option=com_vehiclemanager&task=manage_review', $vName == 'Reviews'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_MANAGER_ADMIN_RENT_REQUESTS),
                   'index.php?option=com_vehiclemanager&task=rent_requests', $vName == 'Rent Requests'
          );
          JSubMenuHelper::addEntry(
            JText::_(_VEHICLE_MANAGER_USER_RENT_HISTORY),
             'index.php?option=com_vehiclemanager&task=users_rent_history', $vName == 'User Rent History'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_MANAGER_ADMIN_SALE_MANAGER_MENU),
                   'index.php?option=com_vehiclemanager&task=buying_requests', $vName == 'Sale Manager'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_MANAGER_ADMIN_FEATURES_MANAGER_MENU),
                   'index.php?option=com_vehiclemanager&section=featured_manager', $vName == 'Features Manager'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_MANAGER_LABEL_LANGUAGE_MENU),
                   'index.php?option=com_vehiclemanager&section=language_manager', $vName == 'Language Manager'
          );
          JSubMenuHelper::addEntry(
              JText::_(_VEHICLE_MANAGER_ADMIN_LABEL_SETTINGS),
                   'index.php?option=com_vehiclemanager&task=config', $vName == 'Settings'
          );
          JSubMenuHelper::addEntry(
                  JText::_(_VEHICLE_MANAGER_ADMIN_ABOUT_ABOUT),
                   'index.php?option=com_vehiclemanager&task=about', $vName == 'About'
          );
        
        }


    }

    static function loadConstVechicle() {
        global $database, $mosConfig_absolute_path;
        $is_exception = false;
        $database->setQuery("SELECT * FROM #__vehiclemanager_languages");
        $langs = $database->loadObjectList();
        $component_path = JPath::clean($mosConfig_absolute_path . '/components/com_vehiclemanager/lang/');
        $component_layouts = array();
        if (is_dir($component_path) && ($component_layouts =
          JFolder::files($component_path, '^[^_]*\.php$', false, true)))
        {
          //check and add constants file in DB
          foreach ($component_layouts as $i => $file) {
            $file_name = pathinfo($file);
            $file_name = $file_name['filename'];
            if ($file_name === 'constant') {
              require($mosConfig_absolute_path . "/components/com_vehiclemanager/lang/$file_name.php");
              foreach ( $constMas as $mas ) {
                $database->setQuery(
                  "INSERT IGNORE INTO #__vehiclemanager_const (const, sys_type) VALUES ('".
                  $mas["const"]."','".$mas["sys_type"]."')");
                $database->execute();
              }
            }
          }
          $flag1=true;
          echo ("<br><br><b>These constants exist in Languages files but not exist in file constants:</b>");
          //check and add new text files in DB
          foreach ($component_layouts as $i => $file) {
            $isLang = 0;
            $file_name = pathinfo($file);
            $file_name = $file_name['filename'];
            $LangLocal = '';
            if ($file_name != 'constant'){
              require($mosConfig_absolute_path . "/components/com_vehiclemanager/lang/$file_name.php");
              try {
                  $database->setQuery("INSERT IGNORE INTO #__vehiclemanager_languages " .
                   " (lang_code,title) VALUES ('" . $LangLocal['lang_code'] . "','"
                    . $LangLocal['title'] . "')");
                  $database->execute();
                  $database->setQuery("SELECT id FROM #__vehiclemanager_languages " .
                     " WHERE lang_code = '" . $LangLocal['lang_code'] . "' AND title='".$LangLocal['title']."'");
                  $idLang = $database->loadResult();
                  foreach ($constLang as $item) {
                    $database->setQuery("SELECT id FROM #__vehiclemanager_const " .
                     " WHERE const = '" . $item['const'] . "'");
                    $idConst = $database->loadResult();
                    if(!array_key_exists ( 'value_const'  , $item ) || !$idConst){
                      print_r($item['const']." not exist in file <b>'constant'</b> for this language:  <b>"
                              . $LangLocal['title']."</b>.");
                     $flag1 = false;
                    } else {
                      $database->setQuery("INSERT IGNORE INTO #__vehiclemanager_const_languages " .
                       "(fk_constid,fk_languagesid,value_const) VALUES ($idConst, $idLang, " .
                      $database->quote($item['value_const']) . ")");
                      $database->execute();
                    }
                  }
              } catch (Exception $e) {
                $is_exception = true;
                echo 'Send exception, please write to admin for language check: ',  $e->getMessage(), "\n";
              }

            }
          }
          if($flag1){
            print_r("<p style='color: #0061CC;'><b>Everything is [Ok!]</b></p><br />");
          }
          else{
            print_r("<br><b style='color: red;'>This constants not loaded.!</b><br><br>");
          }
          if($is_exception) vmLittleThings::language_check();
        //if text constant missing recover they in DB
          if (!defined('_VEHICLE_HEADER_NUMBER')) {
            $query = "SELECT c.const, cl.value_const ";
            $query .= "FROM #__vehiclemanager_const_languages as cl ";
            $query .= "LEFT JOIN #__vehiclemanager_languages AS l ON cl.fk_languagesid=l.id ";
            $query .= "LEFT JOIN #__vehiclemanager_const AS c ON cl.fk_constid=c.id ";
            $query .= "WHERE l.lang_code = 'en-GB'";
            $database->setQuery($query);
            $langConst = $database->loadObjectList();
            foreach ($langConst as $item) {
              if(!defined($item->const)){
                define($item->const, $item->value_const);
              }
            }
          }
        }
        $fileMas = array();
        //if some language file missing recover it
        $component_path = JPath::clean($mosConfig_absolute_path . '/components/com_vehiclemanager/lang/');
        $component_layouts = array();
        if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))) {
          foreach ($component_layouts as $i => $file) {
            $isLang = 0;
            $file_name = pathinfo($file);
            $file_name = $file_name['filename'];
            if ($file_name != 'constant') {
              require($mosConfig_absolute_path . "/components/com_vehiclemanager/lang/$file_name.php");
              //$fileMas[] = $LangLocal;
              $fileMas[] = $LangLocal['title'];
            }
          }
        }

        $database->setQuery("SELECT title FROM #__vehiclemanager_languages");
        if (version_compare(JVERSION, '3.0', 'lt')) {
          $langs = $database->loadResultArray();
        } else {
          $langs = $database->loadColumn();
        }

        if (count($langs) > count($fileMas)) {
          $results = array_diff($langs, $fileMas);
          foreach ($results as $result) {
            $database->setQuery("SELECT lang_code FROM #__vehiclemanager_languages WHERE title = '$result'");
            $lang_code = $database->loadResult();
            $langfile = "<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );";
            $langfile .= "\n/**\n*\n* @package  VehicleManager\n* @copyright 2020 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com);\n* Homepage: http://www.ordasoft.com\n* \n* */\n";
            $langfile .= "\$LangLocal = array('lang_code'=>'$lang_code', 'title'=>'$result');\n";
            $langfile .= "\$constLang = array();\n";
            $query = "SELECT c.const, cl.value_const ";
            $query .= "FROM #__vehiclemanager_const_languages as cl ";
            $query .= "LEFT JOIN #__vehiclemanager_languages AS l ON cl.fk_languagesid=l.id ";
            $query .= "LEFT JOIN #__vehiclemanager_const AS c ON cl.fk_constid=c.id ";
            $query .= "WHERE l.title = '$result'";
            $database->setQuery($query);
            $constlanguages = $database->loadObjectList();
            foreach ($constlanguages as $constlanguage) {
                $langfile .= "\$constLang[] = array('const'=>'" . $constlanguage->const . "', 'value_const'=>" . $database->Quote($constlanguage->value_const) . ");\n";
            }
            // Write out new initialization file
            $fd = fopen($mosConfig_absolute_path . "/components/com_vehiclemanager/lang/$result.php", "w") or die("Cannot create language file.");
            fwrite($fd, $langfile);
            fclose($fd);
          }
        }
    }

static function language_load_VM() {
      $database = JFactory::getDBO();
      // load language
      $languagelocale = "";
      global $langContent;

      $query = "SELECT title, lang_code, sef FROM #__vehiclemanager_languages";
      $database->setQuery($query);
      $languages = $database->loadObjectList();
      $lang = JFactory::getLanguage();
      foreach ($lang->getLocale() as $locale) {
        foreach ($languages as $language) {
          if (strtolower($locale) == strtolower($language->title) || strtolower($locale) == strtolower($language->lang_code) || strtolower(str_replace(array('_','-'), '', $locale))
                  == strtolower((str_replace(array('_','-'), '', $language->lang_code)))
              ) {
            $mosConfig_lang = $locale;
            $languagelocale = $language->lang_code;
            break;
          }
        }
      }
      if ($languagelocale == '') {
        $languagelocale = "en-GB";
        $mosConfig_lang = "en-GB";
      }
      // Set content language

      // $langContent = substr($languagelocale, 0, 2);
      $query = "SELECT c.const, cl.value_const ";
      $query .= "FROM #__vehiclemanager_const_languages as cl ";
      $query .= "LEFT JOIN #__vehiclemanager_languages AS l ON cl.fk_languagesid=l.id ";
      $query .= "LEFT JOIN #__vehiclemanager_const AS c ON cl.fk_constid=c.id ";
      $query .= "WHERE l.lang_code = '$languagelocale'";
      $database->setQuery($query);
      $langConst = $database->loadObjectList();
      foreach ($langConst as $item) {
        if(!defined($item->const) )
         define($item->const, $item->value_const); // $database->quote()
      }

      $query = "SELECT l.lang_code "
          . "FROM #__languages as l " ;
      $database->setQuery($query);
      $languages = $database->loadObjectList();



     foreach ($lang->getLocale() as $locale) {

          foreach ($languages as $language) {


              if (strtolower($locale) == strtolower($language->lang_code)
                  || strtolower(str_replace(array('_','-'), '', $locale))
                  == strtolower((str_replace(array('_','-'), '', $language->lang_code)))

                  ) {

                  $langContent = $language->lang_code;
                  break;
              }
          }
      }

}

    static function language_check($component_db_name = 'vehiclemanager' ) {
      global $database;
      $database->setQuery("SELECT * FROM #__".$component_db_name."_languages");
      $langIds = $database->loadObjectList();
      $flag2=true;
      print_r("<br /><b>These constants exit in file constants but not exist in Languages files:</b><br />");
      foreach ($langIds as $langId){
        $query = " SELECT  lc.*  FROM    #__".$component_db_name."_const as lc ";
        $query .= " WHERE  NOT EXISTS ";
        $query .= " ( SELECT  l1.*  FROM #__".$component_db_name."_const_languages as l1 ";
        $query .= " WHERE lc.id = l1.`fk_constid` and l1.fk_languagesid = ".$langId->id.") ";
        $query .= " and lc.sys_type != 'Features' and lc.sys_type != 'Features Category'   ";        
        $database->setQuery($query);
        $badLangConsts = $database->loadObjectList();
        if($badLangConsts){
          $flag2 = false;
          print_r("<br />Languages: ".$langId->title."<br />");
          print_r($badLangConsts);
          echo "<br><br>";
        }
      }
      if($flag2)
        print_r("<br /><p style='color:green;'><b>Everything is [ OK ]</b></p><br /><br />");
    }


    static function remove_langs($component_db_name = 'vehiclemanager' ) {
      global $database;
      $query = " TRUNCATE TABLE #__".$component_db_name."_languages; ";
      $database->setQuery($query);
      $database->execute();
      $query = " TRUNCATE TABLE #__".$component_db_name."_const; ";
      $database->setQuery($query);
      $database->execute();
      $query = " TRUNCATE TABLE #__".$component_db_name."_const_languages ;";
      $database->setQuery($query);
      $database->execute();
    }


    static function com_veh_categoryTreeList($id, $action, $is_new, &$options = array(), $catid = null){
      // add in version 3.9
      global $langContent, $database;
      if (isset($langContent))
      {
        $lang = $langContent;
        // $query = "SELECT lang_code FROM #__languages WHERE sef = '$lang'";
        // $database->setQuery($query);
        // $lang = $database->loadResult();
        $lang = " AND (c.language like 'all' or c.language like '' or " .
            " c.language like '*' or c.language is null or c.language like '$lang') ";
      } else
      {
        $lang = "";
      }
      // ------------------
      $database = JFactory::getDBO();

      $list = vmLittleThings::categoryArray($lang); // for 1.6
      $cat = new mainVehiclemanagerCategories($database); // for 1.6
      $cat->load($id);

      $this_treename = '';
      $childs_ids = Array();
      foreach ($list as $item) {
        if ($item->id == $cat->id || array_key_exists($item->parent_id, $childs_ids))
          $childs_ids[$item->id] = $item->id;
      }
      foreach ($list as $item) {
        if ($this_treename){
          if ($item->id != $cat->id
                  && strpos($item->treename, $this_treename) === false
                  && array_key_exists($item->id, $childs_ids) === false){
            $options[] = mosHTML::makeOption($item->id, $item->treename);
          }
        } else{
          if ($item->id != $cat->id)
            $options[] = mosHTML::makeOption($item->id, $item->treename);
          else
            $this_treename = "$item->treename/";
        }
      }
      //add in version 3.9
      if ($catid != null) $cat->parent_id = $catid;
      //------------------
      $parent = null;
      $parent = mosHTML::selectList($options, 'catid', 'class="inputbox" size="1" style="width: 140px"', 'value', 'text', $cat->parent_id);

      return $parent;
    }
  }
}


// end of vmLittleThings class
// Updated on June 25, 2011
// accessgroupid - array which contains accepted user groups for this item
// usersgroupid - groupId of the user
// For anonymous user Uid = 0 and Gid = 0
if (!function_exists('checkAccess_VM')){
  function checkAccess_VM($accessgroupid, $recurse, $usersgroupid, $acl){
    if (!is_array($usersgroupid)){
      $usersgroupid = explode(',' , $usersgroupid);
    }
    //parse usergroups
    $tempArr = array();
    $tempArr = explode(',', $accessgroupid);
    // print_r($usersgroupid);
    // print_r($tempArr);exit;
    for ($i = 0; $i < count($tempArr); $i++) {
      if (( (!is_array($usersgroupid) && $tempArr[$i] == $usersgroupid )
              OR ( is_array($usersgroupid) && in_array($tempArr[$i], $usersgroupid) ) )
              || $tempArr[$i] == -2){
        //allow access
        return true;
      }else{
        if ($recurse == 'RECURSE'){
          if (is_array($usersgroupid)){
            foreach ( $usersgroupid as $j)
              if (in_array($j, $tempArr))
                return 1;
          }else{
            if (in_array($usersgroupid, $tempArr))
              return 1;
          }
        }
      }
    } // end for
    //deny access
    return 0;
  }
// End of checkAccess_VM ()
}

//this function check - is exist vehicles in folders under this category
if (!function_exists('is_exist_subcategory_vehicles')){
  function is_exist_subcategory_vehicles($catid){
    $database = JFActory::getDBO();
    $my = JFActory::getUser();
    $query = "SELECT *, COUNT(a.id) AS numlinks FROM #__vehiclemanager_main_categories AS cc"
            . "\n LEFT JOIN #__vehiclemanager_categories AS vc ON vc.idcat = cc.id"
            . "\n LEFT JOIN #__vehiclemanager_vehicles AS a ON a.id = vc.iditem"
            . "\n WHERE a.published='1' AND a.approved='1' AND section='com_vehiclemanager' "
            . "\n AND cc.parent_id='$catid' AND cc.published='1' "
            . "\n GROUP BY cc.id"
            . "\n ORDER BY cc.ordering"; // Removed: AND cc.access <= '$my->gid'
    $database->setQuery($query);
    $categories = $database->loadObjectList();
    if (count($categories) != 0)
      return true;
    $query = "SELECT id "
            . "FROM #__vehiclemanager_main_categories AS cc "
            . " WHERE section='com_vehiclemanager'
            AND parent_id='$catid'
            AND published='1' "; // Removed: AND access<='$my->gid'
    $database->setQuery($query);
    $categories = $database->loadObjectList();
    if (count($categories) == 0)
      return false;
    foreach ($categories as $k)
      if (is_exist_subcategory_vehicles($k->id))
        return true;
    return false;
  }
// end of is_exist_subcategory_vehicles
}


//this function check - is exist vehicles in this folder and folders under this category
if (!function_exists('is_exist_curr_and_subcategory_vehicles')){
  function is_exist_curr_and_subcategory_vehicles($catid){
    $database = JFActory::getDBO();
    $my = JFActory::getUser();
    $query = "SELECT *, COUNT(a.id) AS numlinks FROM #__vehiclemanager_main_categories AS cc"
            . "\n LEFT JOIN #__vehiclemanager_categories AS vc ON vc.idcat = cc.id"
            . "\n LEFT JOIN #__vehiclemanager_vehicles AS a ON a.id = vc.iditem"
            . "\n WHERE a.published='1' AND a.approved='1' AND section='com_vehiclemanager' AND cc.id='$catid' AND cc.published='1' "
            . "\n GROUP BY cc.id"
            . "\n ORDER BY cc.ordering"; // Removed: AND cc.access <= '$my->gid'
    $database->setQuery($query);
    $categories = $database->loadObjectList();
    if (count($categories) != 0)
      return true;
    $query = "SELECT id "
            . "FROM #__vehiclemanager_main_categories AS cc "
            . " WHERE section='com_vehiclemanager' AND parent_id='$catid' AND published='1' "; // Removed: AND access<='$my->gid'
    $database->setQuery($query);
    $categories = $database->loadObjectList();
    if (count($categories) == 0)
      return false;
    foreach ($categories as $k)
      if (is_exist_curr_and_subcategory_vehicles($k->id))
        return true;
    return false;
  }
// end of is_exist_curr_and_subcategory_vehicles()
}


//date_transform_vm - TRANSFER DATE or for show from database touser
//or for save to database from user
// $date_format -> "from" - from database to user , show in format from settings
// $date_format -> "to" - to database from user , save to format "Y-m-d"
if (!function_exists('date_transform_vm')) {
  function date_transform_vm($date, $date_format = "from") {
      global $vehiclemanager_configuration, $database;

      if ( $date_format == "to" ) {
          $formatDateFrom = $vehiclemanager_configuration['date_format'];
          $formatDateTo = 'Y-m-d';
      } else { // $date_format == "from"
           $date = substr($date, 0,10);
           $formatDateFrom = 'Y-m-d' ;
           $formatDateTo = $vehiclemanager_configuration['date_format'] ;
      }

      $formatDateSource = str_replace("%","",$formatDateFrom);
      $formatDateTo = str_replace("%","",$formatDateTo);

      if(function_exists('date_format')){
          $dateObject = date_create_from_format($formatDateSource, $date);

          if($dateObject){
              $date = date_format($dateObject, $formatDateTo);
          } else $date = "" ;

      } else $date = "" ;


      if($date == "" ){
          $query = "SELECT STR_TO_DATE('$date','$formatDateTo')";
          $database->setQuery($query);
          $normaDat = $database->loadResult();

          if(strlen($normaDat) > 0){
              $date = $normaDat;
          }
      }
      return $date;

  }

}



if(!class_exists('getLayoutPath')){
  class getLayoutPath{
    static function getLayoutPathCom($components,$type, $layout = 'default'){
      $template = JFactory::getApplication()->getTemplate();

      if($layout == ''){
        $defaultLayout = 'default';
      }
      else{
        $defaultLayout = $layout;
      }

      if (strpos($layout, ':') !== false){
        // Get the template and file name from the string
        $temp = explode(':', $layout);
        $template = ($temp[0] == '_') ? $template : $temp[0];
        $layout = $temp[1];
        $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
      }
      // Build the template and base path for the layout
      $tPath = JPATH_THEMES . '/' . $template . '/html/' . $components . '/'.$type.'/'. $defaultLayout . '.php';
      $cPath = JPATH_BASE . '/components/' . $components . '/views/'.$type.'/tmpl/'.$defaultLayout.'.php';
      $dPath = JPATH_BASE . '/components/' . $components . '/views/'.$type.'/tmpl/default.php';
      // If the template has a layout override use it
      if (file_exists($tPath)){
        return $tPath;
      }
      else if (file_exists($cPath)){
        return $cPath;
      }
      else if (file_exists($dPath)){
        return $dPath;
      } else {
        echo "Bad layout path, please write to admin"; exit;
      }
    }
  }
}


if (!function_exists('getLayoutsVeh')) {
  function getLayoutsVeh($components, $type) {
    global $database;
    // get current template on frontend
    $template = '';
    $database = JFactory::getDBO();
    $query = "SELECT template
              FROM #__template_styles
              WHERE client_id=0
              AND home=1";
    $database->setQuery($query);
    $template = $database->loadResult();
    // Build the template and base path for the layout
    $tPath = JPATH_SITE . '/templates/' . $template . '/html/' . $components . '/' . $type . '/';
    $cPath = JPATH_SITE . '/components/' . $components . '/views/' . $type . '/tmpl/';
    $layouts1 = array();
    $layouts3 = array();
    if (is_dir($tPath) && ($layouts1 = JFolder::files($tPath, '^[^_]*\.php$', false, true))) {
      foreach ($layouts1 as $i => $file) {
        $select_file_name = pathinfo($file);
        $select_file_name = $select_file_name['filename'];
        $layouts3[] = $select_file_name;
      }
    }
    $layouts2 = array();
    $layouts4 = array();
    if (is_dir($cPath) && ($layouts2 = JFolder::files($cPath, '^[^_]*\.php$', false, true))) {
      foreach ($layouts2 as $i => $file) {
        $select_file_name = pathinfo($file);
        $select_file_name = $select_file_name['filename'];
        $layouts4[] =  $select_file_name;
      }
    }
    $layouts = array_merge($layouts3,$layouts4);
    $layouts = array_unique($layouts);
    return $layouts;
  }
}


if(!function_exists('checkRentDayNightVM')){
  function checkRentDayNightVM ($from,$until, $rent_from, $rent_until, $vehiclemanager_configuration){
    if(isset($vehiclemanager_configuration) && $vehiclemanager_configuration['special_price']['show']){
      if (( $rent_from >= $from &&
          $rent_from <= $until) || ($rent_from <= $from &&
          $rent_until >= $until) || (
          $rent_until >= $from && $rent_until <= $until)){
            return 'Sorry, this item not is available from " '. $from .' " until " '. $until . '"';
      }
    }else{
      if($rent_from === $rent_until){
        return 'Sorry, not one night, not selected';
      }
      if(($rent_from < $until && $rent_until > $from)){
        return 'Sorry, this item not is available from " '. $from .' " until " '. $until . '"';
      }
    }
  }
}


if(!function_exists('calculatePriceVM')){
  function calculatePriceVM ($vid,$rent_from,$rent_until,$vehiclemanager_configuration,$database){


    $rent_from = date_transform_vm($rent_from,"to");
    $rent_until = date_transform_vm($rent_until,"to");


    if($rent_from >$rent_until){
      echo '0';exit;
    }
    if($vehiclemanager_configuration['special_price']['show']){
      $query = "SELECT * FROM #__vehiclemanager_rent_sal WHERE fk_vehiclesid = ".intval($vid) .
                " AND (price_from <= ('" .$rent_until. "') AND price_to >= ('" .$rent_from. "'))";
    }else{
      $query = "SELECT * FROM #__vehiclemanager_rent_sal WHERE fk_vehiclesid = ".intval($vid) .
                  " AND (price_from < ('" .$rent_until. "') AND price_to > ('" .$rent_from. "'))";
    }
    $database->setQuery($query);
    $data_for_price = $database->loadObjectList();

    $zapros = "SELECT price, priceunit FROM #__vehiclemanager_vehicles WHERE id=" . intval($vid) . ";";
    $database->setQuery($zapros);
    $item_vehicle = $database->loadObjectList();

    $rent_from_ms = date_to_data_ms($rent_from);
    $rent_to_ms = date_to_data_ms($rent_until);

    if($vehiclemanager_configuration['special_price']['show']){
      $rent_to_ms = $rent_to_ms + (60*60*24);
    }

    $count_day = (($rent_to_ms - $rent_from_ms)/60/60/24);
    $count_day = round($count_day);
    $array_day_between_to_from[0]=$rent_from;

    for($i = 1; $i < $count_day; $i++){
      $array_day_between_to_from[]=date('Y-m-d',$rent_from_ms + (60*60*24)*($i));
    }

    $count_day_spashal_price = 0;
    $comment_rent_price = '';
    $count_spashal_price = 0;

    foreach ($data_for_price as $one_period){
      $from = $one_period->price_from;
      $to = $one_period->price_to;
      for ($day = 0; $day < $count_day; $day++){
        $currentday = ($array_day_between_to_from[$day]);
        if(isset($vehiclemanager_configuration) && $vehiclemanager_configuration['special_price']['show']){
          if (($currentday >= $from) && ($currentday <= $to)){
            $count_day_spashal_price++;
            $count_spashal_price += $one_period->special_price;
            $comment_rent_price .= (string)$one_period->comment_price;
          }
        }else{
          if (($currentday >= $from) && ($currentday < $to)){
            $count_day_spashal_price++;
            $count_spashal_price += $one_period->special_price;
            $comment_rent_price .= (string)$one_period->comment_price;
          }
        }
      }
    }

    // if(!incorrect_price($item_vehicle[0]->price)){
    if( !incorrect_price($item_vehicle[0]->price) && ($item_vehicle[0]->price) > 0 ){

        $count_day_not_sp_price = $count_day - $count_day_spashal_price;
        $sum_price_not_sp_price =  $count_day_not_sp_price * $item_vehicle[0]->price;
        $sum_price = $sum_price_not_sp_price + $count_spashal_price;

        $returnArr[0]=$sum_price;
        $returnArr[1]=$item_vehicle[0]->priceunit;
        $returnArr[2]=$comment_rent_price;
    }else{
        $returnArr[0]=0;
        $returnArr[1]='';
        $returnArr[2]='';
      }


    return $returnArr;
  }
}



if(!function_exists('getCountCarForSingleUserVM')){
  function getCountCarForSingleUserVM($my,$database,$vehiclemanager_configuration){
    $user_group = userGID_VM($my->id);
    $user_group_mas = explode(',', $user_group);
    $max_count_car = 0;
    foreach ($user_group_mas as $value) {
      $count_car_for_single_group = $vehiclemanager_configuration['user_manager_vm'][$value]['count_car'];
      if($count_car_for_single_group>$max_count_car){
        $max_count_car = $count_car_for_single_group;
      }
    }
    $count_car_for_single_group = $max_count_car;
    $database->setQuery("SELECT COUNT('vehicleid') AS `count_car`
                          FROM #__vehiclemanager_vehicles
                          WHERE owner_id= '" . $my->id. " '
                          AND published='1'" );
    $car_single_user = $database->loadObject();
    $arr = array();
    $arr[0] = $car_single_user->count_car;
    $arr[1] = $count_car_for_single_group;
    return $arr;
  }
}

if(!function_exists('createRentTable')){
  function createRentTable($rentTerm, $massage, $typeMessage){
    global $vehiclemanager_configuration;
    $txt = '';
    if($vehiclemanager_configuration['special_price']['show']){
      $switchTranslateDayNight = _VEHICLE_MANAGER_RENT_SPECIAL_PRICE_PER_DAY;
    }else{
      $switchTranslateDayNight = _VEHICLE_MANAGER_RENT_SPECIAL_PRICE_PER_NIGHT;
    }
    if($typeMessage === 'error'){
      $txt.= '<div id ="message-here" style ="color: red; font-size: 18px;" >'.$massage.'</div>';
    }else{
      $txt.= '<div id ="message-here" style ="color: gray; font-size: 18px;" >'.$massage.'</div>';
    }
    $txt.= '<div id ="SpecialPriseBlock">
              <table class="adminlist_04" width ="100%" align ="center">
                <tr>
                  <th class="title" align ="center" width ="20%">'
                    .$switchTranslateDayNight.
                  '</th>
                  <th class="title" align ="center" width ="15%">'
                    ._VEHICLE_MANAGER_FROM.
                  '</th>
                  <th class="title" align ="center" width ="15%" >'
                    ._VEHICLE_MANAGER_TO.
                  '</th>
                  <th width ="30%" class="title" >'
                    ._VEHICLE_MANAGER_LABEL_REVIEW_COMMENT.
                  '</th>
                  <th class="title" align ="center" width ="20%">'
                    ._VEHICLE_MANAGER_LABEL_CALENDAR_SELECT_DELETE.
                  '</th>
                </tr>';
    for ($i = 0; $i < count($rentTerm); $i++) {
        $DateToFormat = str_replace("D",'d',(str_replace("M",'m',(str_replace('%','',
                                    $vehiclemanager_configuration['date_format'])))));
        $date_from = new DateTime($rentTerm[$i]->price_from);
        $date_to = new DateTime($rentTerm[$i]->price_to);
        $txt.= '<tr>
                  <td align ="center">'
                    .$rentTerm[$i]->special_price.' '.$rentTerm[$i]->priceunit.
                  '</td>
                  <td align ="center">'
                    .date_format($date_from, $DateToFormat).
                  '</td>
                  <td align ="center">'
                    .date_format($date_to, $DateToFormat).
                  '</td>
                  <td>'
                    .$rentTerm[$i]->comment_price.
                  '</td>
                  <td align ="center">
                    <input type="checkbox" name="del_rent_sal[]" value="'.$rentTerm[$i]->id.'"
                  </td>
                </tr>';
    }
    $txt.= '</table><p><p></div>';
    echo $txt;
    exit;
  }
}


if(!function_exists('rentPriceVM')){
  if(!function_exists('rentPriceVM')){
    function rentPriceVM($vid,$rent_from,$rent_until,$special_price,
      $comment_price,$currency_spacial_price){
        global $database, $vehiclemanager_configuration;
        $rent_from_transf = date_transform_vm($rent_from,"to");
        $rent_until_transf = date_transform_vm($rent_until,"to");
        if($vid==''){
          $rentTerm = array();
          createRentTable($rentTerm, 'Please save or apply this item first','error');
          return;
        }
        $query = "SELECT * FROM #__vehiclemanager_rent_sal where fk_vehiclesid = " . $vid;
        $database->setQuery($query);
        $rentTerm = $database->loadObjectList();
        if($special_price==''){
          createRentTable($rentTerm, 'You need fill Price','error');
        }
        if($rent_from==''){
          createRentTable($rentTerm, 'You need fill Check In','error');
        }
        if($rent_until==''){
          createRentTable($rentTerm, 'You need fill Check Out','error');
        }
        if($rent_from_transf >$rent_until_transf){
          createRentTable($rentTerm, 'Incorrect Check Out','error');
        }

        if(count($rentTerm) == 0){
          $returnMessage = checkRentDayNightVM (0,0,
            $rent_from_transf, $rent_until_transf, $vehiclemanager_configuration);
          if(strlen($returnMessage) > 0){
            createRentTable($rentTerm, $returnMessage, 'error');
          }
        }
        foreach ($rentTerm as $oneTerm){
          $returnMessage = checkRentDayNightVM (($oneTerm->price_from),($oneTerm->price_to),
            $rent_from_transf, $rent_until_transf, $vehiclemanager_configuration);
          if(strlen($returnMessage) > 0){
            createRentTable($rentTerm, $returnMessage, 'error');
          }
        }
        //echo ":33333333333333:".$rent_from_transf; exit;
        $sql = "INSERT INTO #__vehiclemanager_rent_sal (fk_vehiclesid,
                                                         price_from,
                                                         price_to,
                                                         special_price,
                                                         priceunit,
                                                         comment_price)
                      VALUES (" . intval($vid) . ",
                              '" . $rent_from_transf . "',
                              '" . $rent_until_transf . "',
                              '" . $special_price . "',
                              '" . $currency_spacial_price . "',
                              '" . $comment_price . "')";
        $database->setQuery($sql);
        $database->execute();
        $$query = "SELECT * FROM #__vehiclemanager_rent_sal where fk_vehiclesid = " . intval($vid);
        $database->setQuery($query);
        $rentTerm = $database->loadObjectList();
        createRentTable($rentTerm, 'Add special price on data: from "'.
                        $rent_from.'" to "'.$rent_until.'"','');
    }
  }
}



if(!function_exists('getAvilableVM')){
  function getAvilableVM ($calenDate,$month,$year,$vehiclemanager_configuration,$day){
    global $flag3;
    if(strlen($month) == 1){
        $month = '0'.$month ;
      }
      if(strlen($day) == 1){
        $day = '0'.$day ;
      }
      $toDay = $day+1;
      if(strlen($toDay) == 1){
        $toDay = '0'.$toDay ;
      }
    $cheackDataFrom = $year.'-'.$month.'-'.$day;
    $cheackDataTo = $year.'-'.$month.'-'.$toDay;
    foreach ($calenDate as $oneTerm){
      $from=explode(' ',$oneTerm->rent_from);
      $until=explode(' ',$oneTerm->rent_until);

      if($cheackDataFrom >= $oneTerm->rent_until)continue;
      $resultmsg = checkRentDayNightVM (($oneTerm->rent_from),($oneTerm->rent_until), $cheackDataFrom, $cheackDataTo, $vehiclemanager_configuration);

      if($cheackDataTo <= date('Y-m-d') && strlen($resultmsg) > 1){
        if(!$vehiclemanager_configuration['special_price']['show']
            && ($cheackDataFrom == $until[0] || $cheackDataFrom == $from[0] )){
          if($flag3){
            $flag3 = false;
            return 'calendar_day_gone_not_avaible_night_end';
          }else{
            $flag3 = true;
            return 'calendar_day_gone_not_avaible_night_start';
          }
        }
        return 'calendar_day_gone_not_avaible';
      }
      if(strlen($resultmsg) > 1){
        if(!$vehiclemanager_configuration['special_price']['show']
          && ($cheackDataFrom == $until[0] || $cheackDataFrom == $from[0] )){
          if($flag3){
            $flag3 = false;
            return 'calendar_not_available_night_end';
          }else{
            $flag3 = true;
            return 'calendar_not_available_night_start';
          }
        }
        return 'calendar_not_available';
      }
      if($cheackDataTo <= date('Y-m-d')){
        return 'calendar_day_gone_avaible';
      }
    }
    if(isset($cheackDataTo) && $cheackDataTo <= date('Y-m-d')){
      return 'calendar_day_gone_avaible';
    }
    return 'calendar_available';
  }
}


if(!function_exists('getHTMLPayPalVM')){
  function getHTMLPayPalVM($vehicle,$plugin_name_select){
    global $database;
    if(!getPublicPlugin()){
      echo "You must public plugin Payment group";
    }else{
        //var_dump($plugin_name_select);
      $plugin_name_select = explode(',', $plugin_name_select);
      foreach ($plugin_name_select as $plugin_name){

      $plugin = JPluginHelper::importPlugin( 'payment',$plugin_name);
      $query = "SELECT profile_value FROM #__user_profiles "
            ."\n WHERE profile_key = 'profile.paypal_email' AND user_id = ".$vehicle->owner_id;
      $database->setQuery($query);
      $paypal_email = $database->loadResult();
      $data = array('vtitle' => $vehicle->vtitle, 'price' => $vehicle->price, 'currency_code' => $vehicle->priceunit,'owner_paypal_email' => $paypal_email);
      
      if($plugin_name == 'paypal'){
        $html = JFactory::getApplication()->triggerEvent('getHTMLPayPal', array($data));
      }elseif ($plugin_name == '2checkout') {
        $html = JFactory::getApplication()->triggerEvent('getHTML2Checkout', array($data));
      }elseif ($plugin_name == 'stripe') {
        $html = JFactory::getApplication()->triggerEvent('getHTMLStripe', array($data));
      }

      if(isset($html[0])){
        echo $html[0];
      }else{
             echo "<div class='alert alert-error'>" . JText::_(VEHICLE_MANAGER_SELECT_PLUGIN) . "</div>";
        }
      }
        }
    }
}

if(!function_exists('getPublicPlugin')){
  function getPublicPlugin(){
    $database = JFactory::getDBO();
    $condtion = array(0 => '\'payment\'');
    $condtionatype = join(',',$condtion);
    if(JVERSION >= '1.6.0'){
      $query = "SELECT extension_id as id,name,element,enabled as published
         FROM #__extensions
         WHERE folder in ($condtionatype) AND enabled=1";
    }else{
      $query = "SELECT id,name,element,published
           FROM #__plugins
           WHERE folder in ($condtionatype) AND published=1";
    }
    $database->setQuery($query);
    $gatewayplugin = $database->loadobjectList();
    $retr = count($gatewayplugin);
    if($retr>0){
      $ret_string = "";
      for($i=0;$i<$retr;$i++){
        $ret_string .= "<option value='".$gatewayplugin[$i]->name."'>".$gatewayplugin[$i]->name."</option>";
      }
    return $ret_string;
    }
    else{
      return false;
    }
  }
}

//--------------------------Associate------------Associate----------------Associate---------------



if(!function_exists('getAssociateVehiclesLang')){
  function getAssociateVehiclesLang($vehicleIds){
    global $database;
    $query = "SELECT associate_vehicle
              FROM #__vehiclemanager_vehicles
              WHERE id = ".$vehicleIds."
              AND associate_vehicle is not null";
    $database->setQuery($query);
    $vehicleAssociateVehicle = $database->loadResult();
    if (!empty($vehicleAssociateVehicle)){
      $vehicleLangIds = unserialize($vehicleAssociateVehicle);
    return $vehicleLangIds;
    }
  }
}

if(!function_exists('getAssociateVehicle')){
  function getAssociateVehicle($vehicleIds){
    global $database;
    $one = array();
    $query = "SELECT associate_vehicle
              FROM #__vehiclemanager_vehicles
              WHERE id = ".$vehicleIds."
              AND associate_vehicle is not null";

    $database->setQuery($query);
    $vehicleAssociateVehicle = $database->loadResult();
    if (!empty($vehicleAssociateVehicle)){
      $vehicleIds = unserialize($vehicleAssociateVehicle);
      foreach($vehicleIds as $onevehicleIds){
        if($onevehicleIds != 0){
          $one[] = $onevehicleIds;
        }
      }
    $bids = implode(',', $one);
    return $bids;
    }
  }
}

if(!function_exists('getAssociateDiff')){
  function getAssociateDiff($assocArray1,$assocArray2){
    global $database;
    $diff_ids = array();
    $diff = array_diff($assocArray1,$assocArray2);
    foreach($diff as $key => $value){
      if($value != 0){
        $diff_ids[] = $value;
      }
    }
    return $diff_ids ;
  }
}

if(!function_exists('getAssociateOld')){
    function getAssociateOld(){
        global $database;
        $id_check = intval(protectInjectionWithoutQuote('id', '', 'INT'));
            $query = "SELECT `associate_vehicle`
                      FROM #__vehiclemanager_vehicles
                      WHERE `id` = ".$id_check."";
            $database->setQuery($query);
            $oldAssociate = $database->loadResult();
            $oldAssoc_func = unserialize($oldAssociate);
        return $oldAssoc_func;
    }
}

if(!function_exists('ClearAssociateDiff')){
    function ClearAssociateDiff(){
      global $database;
      $id_check = intval(protectInjectionWithoutQuote('id', '', 'INT'));
      $language_post = protectInjectionWithoutQuote('language', '', 'STRING');
      $oldAssociateArray = getAssociateOld();
      $i = 1;
      $assocArray = array();

        while( isset($_REQUEST["associate_vehicle".$i]) ){
            $langAssoc = protectInjectionWithoutQuote("associate_vehicle_lang".$i);
            $valAssoc = protectInjectionWithoutQuote("language_associate_vehicle".$i);

            $assocArray[$langAssoc] = $valAssoc;
            $i++;
        }
      $assocArray[$language_post] = $id_check;
      if(!empty($oldAssociateArray) && !empty($assocArray))
        $old_ids_assoc = getAssociateDiff($oldAssociateArray,$assocArray);
              if(isset($old_ids_assoc) && count($old_ids_assoc)>0){
                  foreach($old_ids_assoc as $key => $value) {
                    $diff_assoc2 = getAssociateVehicle($value);
                    if(!empty($diff_assoc2)){
                      $ids_assoc_diff2 = explode(',', $diff_assoc2);
                      foreach ($ids_assoc_diff2 as $key2 => $value2){
                        if(!in_array($value2,$old_ids_assoc)){
                          $assoc_lang = getAssociateVehiclesLang($value);
                          foreach ($assoc_lang as $key3 => $value3){
                            if($value3 == $value2){
                              $assoc_lang[$key3] = 0;
                            }
                          }
                          $vehicleLangIds = serialize($assoc_lang);
                          $query = "UPDATE #__vehiclemanager_vehicles
                                    SET `associate_vehicle`='".$vehicleLangIds."'
                                    WHERE `id` = ".$value."";
                          $database->setQuery($query);
                          $database->execute();
                        }
                      }
                    }
                  }
              }
        if(!empty($oldAssociateArray) && !empty($assocArray))
            $new_ids_assoc = getAssociateDiff($assocArray,$oldAssociateArray);
        if(isset($new_ids_assoc) && count($new_ids_assoc)>0)
        {
            foreach($new_ids_assoc as $key => $value) {
              $diff_assoc2 = getAssociateVehicle($value);
              if(!empty($diff_assoc2)){
              $ids_assoc_diff2 = explode(',', $diff_assoc2);
                foreach ($ids_assoc_diff2 as $key2 => $value2){
                  if($value2 == $value || $value2 == 0 ) continue;
                  $assoc_lang = getAssociateVehiclesLang($value2);
                  foreach ($assoc_lang as $key3 => $value3){
                    if($value3 == $value){
                      $assoc_lang[$key3] = 0;
                    }
                  }
                  $vehicleLangIds = serialize($assoc_lang);
                  $query = "UPDATE #__vehiclemanager_vehicles
                            SET `associate_vehicle`='".$vehicleLangIds."'
                            WHERE `id` = ".$value2."";
                  $database->setQuery($query);
                  $database->execute();
                }
              }
            }
        }
    }
}

//--------------------------rent!!!!------------rent----------------rent---------------
if(!function_exists('rent')){
  function rent($option, $vid){
    global $database, $my;

    if (!is_array($vid) || count($vid) !== 1){
      echo "<script> alert('Select one item to rent'); window.history.go(-1);</script>\n";
      exit;
    }
    $vid_veh = implode(',', $vid);
    $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
            "l.rent_return as rent_return, l.rent_until as rent_until, " .
            "l.user_name as user_name, l.user_email as user_email " .
            "\nFROM #__vehiclemanager_vehicles AS a" .
            "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
            "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.id = a.fk_rentid" .
            "\nWHERE a.id = $vid_veh";

    $database->setQuery($select);
    $vehicle = $database->loadObject();
    if($vehicle->listing_type=='2'){
        echo "<script> alert('This vehicle is not for rent'); window.history.go(-1);</script>\n";
        exit;
    }
    $vids = implode(',', $vid);
    $vids = getAssociateVehicle($vids);
    $vehicles_assoc[]= $vehicle;
    if($vids){
      $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
              "l.rent_return as rent_return, l.rent_until as rent_until, " .
              "l.user_name as user_name, l.user_email as user_email " .
              "\nFROM #__vehiclemanager_vehicles AS a" .
              "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
              "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.id = a.fk_rentid" .
              "\nWHERE a.id in ($vids)";

      $database->setQuery($select);
      $vehicles_assoc = $database->loadObjectList();

      //for rent or not
      $count = count($vehicles_assoc);
      for ($i = 0; $i < $count; $i++) {
        if ($vehicles_assoc[$i]->listing_type != 1){
          ?>
          <script type = "text/JavaScript" language = "JavaScript">
              alert('This vehicle has associated vehicle not for rent');
              window.history.go(-1);
          </script>
          <?php
          exit;
        }
      }
    }
    // get list of categories

    $userlist[] = mosHTML :: makeOption('-1', '----------');
    $database->setQuery("SELECT id AS value, name AS text from #__users ORDER BY name");
    $userlist = array_merge($userlist, $database->loadObjectList());
    $usermenu = mosHTML :: selectList($userlist, 'userid', 'class="inputbox" size="1"', 'value', 'text', '-1');

    HTML_vehiclemanager:: showRentVehicles($option, $vehicle, $vehicles_assoc,  $usermenu, "rent");
  }
}

if(!function_exists('edit_rent')){
    function edit_rent($option, $vid)
    {
        global $database, $my;
        if (!is_array($vid) || count($vid) !== 1)
        {
            echo "<script> alert('Select one item to edit rent'); window.history.go(-1);</script>\n";
            exit;
        }

        $vid_veh = implode(',', $vid);
        $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
                "l.rent_return as rent_return, l.rent_until as rent_until, " .
                "l.user_name as user_name, l.user_email as user_email " .
                "\nFROM #__vehiclemanager_vehicles AS a" .
                "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
                "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.fk_vehicleid = a.id" .
                "\nWHERE a.id = $vid_veh";

        $database->setQuery($select);
        $vehicle = $database->loadObject();
        if($vehicle->listing_type=='2'){
            echo "<script> alert('You try edit vehicle that is not for rent'); window.history.go(-1);</script>\n";
            exit;
        }


        $vids = implode(',', $vid);
        $vids = getAssociateVehicle($vids);
        if($vids == "") $vids = implode(',', $vid);
          $vehicles_assoc= array();
          $title_assoc = array();
          if($vids){
            $select = "SELECT a.* , cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
                    "l.rent_return as rent_return, l.rent_until as rent_until, " .
                    "l.user_name as user_name, l.user_email as user_email " .
                    "\nFROM #__vehiclemanager_vehicles AS a" .
                    "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
                    "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.fk_vehicleid = a.id" .
                    "\nWHERE a.id in ($vids)";
  //  print_r($select);
            $database->setQuery($select);
            $vehicles_assoc = $database->loadObjectList();

            $select = "SELECT a.vtitle " .
                      "\nFROM #__vehiclemanager_vehicles AS a" .
                      "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.id = a.fk_rentid" .
                      "\nWHERE a.id in ($vids)";
            $database->setQuery($select);
            $title_assoc = $database->loadObjectList();

            $count = count($vehicles_assoc);
            for ($i = 0; $i < $count; $i++) {
                if ($vehicles_assoc[$i]->listing_type != 1)
                {
                    ?>
                    <script type = "text/JavaScript" language = "JavaScript">
                        alert('This vehicle  is not for rent');
                        window.history.go(-1);
                    </script>
                    <?php

                    exit;
                }
            }

            if ( $count <= 0 )
            {
                ?>
                <script type = "text/JavaScript" language = "JavaScript">
                    alert('You edit vehicles that were not lent out');
                    window.history.go(-1);
                </script>
                <?php

                exit;
            }

            $is_rent_out = false;
            for ($i = 0; $i < count($vehicles_assoc); $i++) {
              if ( $vehicles_assoc[$i]->rent_from != '' && $vehicles_assoc[$i]->rent_return == '' )
              {
                $is_rent_out = true ;
                break ;
              }
            }

            if ( !$is_rent_out )
            {
                ?>
                <script type = "text/JavaScript" language = "JavaScript">
                    alert('You cannot edit vehicles that were not lent out');
                    window.history.go(-1);
                </script>
                <?php
                exit;
            }
           //check rent_return == null count for all assosiate

            $ids = explode(',', $vids);
            $count = count($ids);
            $rent_count = -1;
            $all_assosiate_rent = array();
            for ($i = 0; $i < $count; $i++) {

                $query = "SELECT * FROM #__vehiclemanager_rent WHERE fk_vehicleid = " . $ids[$i] .
                  " and rent_return is null ORDER BY rent_from ";

                $database->setQuery($query);
                $all_assosiate_rent_item = $database->loadObjectList();

                if ( $rent_count != -1 && $rent_count != count($all_assosiate_rent_item) )
                {
                    ?>
                    <script type = "text/JavaScript" language = "JavaScript">
                        alert('Error in rent, for associated');
                        window.history.go(-1);
                    </script>
                    <?php

                    exit;
                }
                $rent_count = count($all_assosiate_rent_item);
                $all_assosiate_rent[] = $all_assosiate_rent_item;
            }
        }
        // get list of users
        $userlist[] = mosHTML::makeOption('-1', '----------');
        $database->setQuery("SELECT id AS value, name AS text from #__users ORDER BY name");
        $userlist = array_merge($userlist, $database->loadObjectList());
        $usermenu = mosHTML::selectList($userlist, 'userid', 'class="inputbox" size="1"', 'value', 'text', '-1');

        HTML_vehiclemanager :: editRentVehicles($option, $vehicle, $vehicles_assoc, $title_assoc,
                                               $usermenu, $all_assosiate_rent, "edit_rent");
    }
}

if(!function_exists('rent_return')){
    function rent_return($option, $vid)
    {

        global $database, $my;
        if (!is_array($vid) || count($vid) !== 1)
        {
            echo "<script> alert('Select one item to return vehicle from rent'); window.history.go(-1);</script>\n";
            exit;
        }
        $vid_veh = implode(',', $vid);
        $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
                "l.rent_return as rent_return, l.rent_until as rent_until, " .
                "l.user_name as user_name, l.user_email as user_email " .
                "\nFROM #__vehiclemanager_vehicles AS a" .
                "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
                "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.fk_vehicleid = a.id" .
                "\nWHERE a.id = $vid_veh";

        $database->setQuery($select);
        $vehicle = $database->loadObject();

        if($vehicle->listing_type=='2'){
            echo "<script> alert('You try return vehicle that is not for rent'); window.history.go(-1);</script>\n";
            exit;
        }
        $vids = implode(',', $vid);
        $vids = getAssociateVehicle($vids);
        if($vids == "") $vids = implode(',', $vid);
        $vehicles_assoc = array();
        $title_assoc = array();
        if($vids){
            $select = "SELECT a.*, cc.name AS category, l.id as rentid, l.rent_from as rent_from, " .
                    "l.rent_return as rent_return, l.rent_until as rent_until, " .
                    "l.user_name as user_name, l.user_email as user_email " .
                    "\nFROM #__vehiclemanager_vehicles AS a" .
                    "\nLEFT JOIN #__vehiclemanager_main_categories AS cc ON cc.id = a.catid" .
                    "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.fk_vehicleid = a.id" .
                    "\nWHERE a.id in ($vids)";

            $database->setQuery($select);
            $vehicles_assoc = $database->loadObjectList();

            $select = "SELECT a.vtitle " .
                      "\nFROM #__vehiclemanager_vehicles AS a" .
                      "\nLEFT JOIN #__vehiclemanager_rent AS l ON l.id = a.fk_rentid" .
                      "\nWHERE a.id in ($vids)";
            $database->setQuery($select);
            $title_assoc = $database->loadObjectList();

            $count = count($vehicles_assoc);
            for ($i = 0; $i < $count; $i++) {
                if ($vehicles_assoc[$i]->listing_type != 1)
                {
                    ?>
                    <script type = "text/JavaScript" language = "JavaScript">
                        alert('This vehicle is not for rent');
                        window.history.go(-1);
                    </script>
                    <?php

                    exit;
                }
            }
            if ( count($vehicles_assoc) <= 0 )
            {
                ?>
                <script type = "text/JavaScript" language = "JavaScript">
                    alert('You try return vehicles that were not lent out');
                    window.history.go(-1);
                </script>
                <?php
                exit;
            }

            $is_rent_out = false;
            for ($i = 0; $i < count($vehicles_assoc); $i++) {
              if ( $vehicles_assoc[$i]->rent_from != '' && $vehicles_assoc[$i]->rent_return == '' )
              {
                $is_rent_out = true ;
                break ;
              }
            }
            if ( !$is_rent_out )
            {
                ?>
                <script type = "text/JavaScript" language = "JavaScript">
                    alert('You cannot return vehicles that were not lent out');
                    window.history.go(-1);
                </script>
                <?php
                exit;
            }
            //check rent_reurn == null count for all assosiate
            $ids = explode(',', $vids);
            $rent_count = -1;
            $all_assosiate_rent = array();
            $count = count($ids);
            for ($i = 0; $i < $count; $i++) {

                $query = "SELECT * FROM #__vehiclemanager_rent WHERE fk_vehicleid =" . $ids[$i] .
                  " and rent_return is null ORDER BY rent_from ";
                $database->setQuery($query);
                $all_assosiate_rent_item = $database->loadObjectList();

                if ( $rent_count != -1 && $rent_count != count($all_assosiate_rent_item) )
                {
                    ?>
                    <script type = "text/JavaScript" language = "JavaScript">
                        alert('Error in rent, for associated');
                        window.history.go(-1);
                    </script>
                    <?php

                    exit;
                }
                $rent_count = count($all_assosiate_rent_item);
                $all_assosiate_rent[] = $all_assosiate_rent_item;
            }
        }
        // get list of users
        $userlist[] = mosHTML :: makeOption('-1', '----------');
        $database->setQuery("SELECT id AS value, name AS text from #__users ORDER BY name");
        $userlist = array_merge($userlist, $database->loadObjectList());
        $usermenu = mosHTML :: selectList($userlist, 'userid', 'class="inputbox" size="1"', 'value', 'text', '-1');

        HTML_vehiclemanager :: editRentVehicles($option, $vehicle, $vehicles_assoc, $title_assoc,
         $usermenu, $all_assosiate_rent, "rent_return");
    }
}

if(!function_exists('saveRent')){
    function saveRent($option, $vids, $task = ""){
        global $database, $vehiclemanager_configuration;

        $id = mosGetParam($_POST, 'id');
        $ids[] = $id ;
        $ids = implode(',', $ids);
        $ids = getAssociateVehicle($ids);
        if($ids == "")  $ids = $id;
        $ids = explode(',', $ids);

        $data = JFactory::getDBO();
        $vehicleid = mosGetParam($_POST, 'vehicleid');
        $rent_from = mosGetParam($_POST, 'rent_from');
        $rent_until = mosGetParam($_POST, 'rent_until');

        if($rent_from == '' || $rent_until == ''){
            echo "<script> alert('".JText::_(_VEHICLE_MANAGER_ADMIN_RENT_FROM_AND_RENT_UNTIL_REQUIRED)."'); window.history.go(-1); </script>\n";
            exit();
        }

        if ($rent_from > $rent_until)
        {
            echo "<script> alert('" . $rent_from . " more then " . $rent_until .
             "'); window.history.go(-1); </script>\n";
            exit();
        }
        if ($task == "edit_rent")
        {
          $check_vids = implode(',', $vids);
          if ($check_vids == 0 || count($vids) > 1)
          {
              echo "<script> alert('Select one item to save edit rent'); window.history.go(-1);</script>\n";
              exit;
          }
          $rent = new mosVehicleManager_rent($database);
          $a_ids = explode(',', $vids[0]);
          for($j = 0, $k = count($a_ids); $j < $k; $j++){
            $rent->load($a_ids[$j]);

            $query = "SELECT * FROM #__vehiclemanager_rent where fk_vehicleid = " .
             $rent->fk_vehicleid . " AND rent_return is NULL ";
            $data->setQuery($query);
            $rentTerm = $data->loadObjectList();
            $rent_from = substr($rent_from, 0, 10);
            $rent_until = substr($rent_until, 0, 10);

            foreach ($rentTerm as $oneTerm){
                if ($a_ids[$j] == $oneTerm->id)
                    continue;

                $oneTerm->rent_from = substr($oneTerm->rent_from, 0, 10);
                $oneTerm->rent_until = substr($oneTerm->rent_until, 0, 10);
                $returnMessage = checkRentDayNightVM (($oneTerm->rent_from),
                  ($oneTerm->rent_until), $rent_from, $rent_until, $vehiclemanager_configuration);
                if($a_ids[$j] !== $oneTerm->id && strlen($returnMessage) > 0){
                    echo "<script> alert('$returnMessage'); window.history.go(-1); </script>\n";
                    exit;
                }
            }

            $rent->rent_from = $rent_from;

            if (mosGetParam($_POST, 'rent_until') != "")
            {
                $rent->rent_until = mosGetParam($_POST, 'rent_until');
            } else
            {
                $rent->rent_until = null;
            }

            $userid = mosGetParam($_POST, 'userid');

            if ($userid == "-1")
            {
                $rent->user_name = mosGetParam($_POST, 'user_name', '');
                $rent->user_email = mosGetParam($_POST, 'user_email', '');
            } else
            {
                $rent->getRentTo(intval($userid));
            }
            if( !$rent->check($rent) )
            {
                return;
            }

            $rent->store();

            $rent->checkin();

          }
        }

        if ($task !== "edit_rent") {
          $checkV = mosGetParam($_POST, 'checkVehicle');

          if ($checkV != "on")
          {
              echo "<script> alert('Select one item to save rent'); window.history.go(-1);</script>\n";
              exit;
          }
          for($i = 0, $n = count($ids); $i < $n; $i++){

            $rent = new mosVehicleManager_rent($database);

            $query = "SELECT * FROM #__vehiclemanager_rent
                      WHERE fk_vehicleid = " . $ids[$i] . "
                      AND rent_return is NULL ";
            $data->setQuery($query);
            $rentTerm = $data->loadObjectList();
            $rent_from = substr($rent_from, 0, 10);
            $rent_until = substr($rent_until, 0, 10);

            foreach ($rentTerm as $oneTerm){

              $oneTerm->rent_from = substr($oneTerm->rent_from, 0, 10);
              $oneTerm->rent_until = substr($oneTerm->rent_until, 0, 10);
              $returnMessage = checkRentDayNightVM (($oneTerm->rent_from),($oneTerm->rent_until)
                                      , $rent_from, $rent_until, $vehiclemanager_configuration);

              if(strlen($returnMessage) > 0){
                  echo "<script> alert('$returnMessage'); window.history.go(-1); </script>\n";
                  exit;
              }
            }

            $rent->rent_from = $rent_from;

            if (mosGetParam($_POST, 'rent_until') != "")
            {
                $rent->rent_until = mosGetParam($_POST, 'rent_until');
            } else
            {
                $rent->rent_until = null;
            }

            $rent->fk_vehicleid = $ids[$i];

            $userid = mosGetParam($_POST, 'userid');

            if ($userid == "-1")
            {
                $rent->user_name = mosGetParam($_POST, 'user_name', '');
                $rent->user_email = mosGetParam($_POST, 'user_email', '');
            } else
            {
                $rent->getRentTo(intval($userid));
                $rent->fk_userid = intval($userid);
            }

            if (!$rent->check($rent))
            {
                return;
            }
            $rent->store() ;

            $rent->checkin();
            $vehicle = new mosVehicleManager($database);
            $vehicle->load($ids[$i]);
            $vehicle->fk_rentid = $rent->id;
            $vehicle->store();
            $vehicle->checkin();

          }
        }
        mosRedirect("index.php?option=$option");
    }
}

if(!function_exists('saveRent_return')){
    function saveRent_return($option, $lids){
        global $database, $my;
        $vehicleid = mosGetParam($_POST, 'vehicleid');
        $id = mosGetParam($_POST, 'id');
        $check_vids = implode(',', $lids);
        if ($check_vids == 0 || count($lids) > 1){
          echo "<script> alert('Select one item to return from rent'); window.history.go(-1);</script>\n";
          exit;
        }

        $r_ids = explode(',', $lids[0]);
        $rent = new mosVehicleManager_rent($database);
        for ($i = 0, $n = count($r_ids); $i < $n; $i++) {
            $rent->load($r_ids[$i]);
            if ($rent->rent_return != null){
              echo "<script> alert('Already returned'); window.history.go(-1);</script>\n";
              exit;
            }
            $rent->rent_return = date("Y-m-d H:i:s");
            if (!$rent->check($rent)){
              return;
            }
            $rent->store() ;

            $rent->checkin();
            $is_update_vehicle_lend = true;
            if ($is_update_vehicle_lend)
            {
                $vehicle = new mosVehicleManager($database);
                $vehicle->load($id);

                $query = "SELECT * FROM #__vehiclemanager_rent
                          WHERE fk_vehicleid=" . $id . "
                          AND rent_return IS NULL";
                $database->setQuery($query);
                $check_rents = $database->loadObjectList();
                if (isset($check_rents[0]->id))
                {
                    $vehicle->fk_rentid = $check_rents[0]->id;
                    $is_update_vehicle_lend = false;
                } else
                {
                    $vehicle->fk_rentid = 0;
                }
                $vehicle->store();
                $vehicle->checkin();
            }
        }
        mosRedirect("index.php?option=" . $option);
    }
}

if(!function_exists('edit_vehicle_associate')){

    function edit_vehicle_associate($vehicle,$call_from,$access)
    {
        global $database, $my;
        $associateArray = array();
        $userid = $my->id;

            $query = "SELECT lang_code FROM `#__languages` WHERE 1";
            $database->setQuery($query);
            $allLanguages =  $database->loadColumn();

        if($call_from=='backend' || $access)
        {
            $query = "SELECT id,language,vtitle
                      FROM `#__vehiclemanager_vehicles`";
        }
        else
        {
            $query = "SELECT id,language,vtitle
                      FROM `#__vehiclemanager_vehicles`
                      WHERE 1
                      AND  owner_id = " . $userid . "";
        }


            $database->setQuery($query);
            $allVehicle =  $database->loadObjectlist();

            $query = "select associate_vehicle from #__vehiclemanager_vehicles where id =".$vehicle->id;
            $database->setQuery($query);
            $vehicleAssociateVehicle =  $database->loadResult();


            if(!empty($vehicleAssociateVehicle)){
                $vehicleAssociateVehicle = unserialize($vehicleAssociateVehicle);
            }else{
                $vehicleAssociateVehicle = array();
            }

            $i = 0;
            foreach ($allLanguages as &$oneLang) {
                $i++;
                $associate_vehicle = array();
                $associate_vehicle[] = mosHtml::makeOption(0, 'select');

                foreach($allVehicle as &$oneVehicle){
                    if($oneLang == $oneVehicle->language && $oneVehicle->id != $vehicle->id){
                        $associate_vehicle[] = mosHtml::makeOption(($oneVehicle->id), $oneVehicle->vtitle);
                    }
                }
              if($vehicle->language != $oneLang){

                    if(isset($vehicleAssociateVehicle[$oneLang])
                        && $vehicleAssociateVehicle[$oneLang] !== $vehicle->id ){
                        $associateArray[$oneLang]['assocId'] = $vehicleAssociateVehicle[$oneLang];
                    }else{
                        $associateArray[$oneLang]['assocId'] = 0;
                    }

                    $associate_vehicle_list = mosHTML :: selectList($associate_vehicle,
                                              'language_associate_vehicle'.$i,
                                              'class="inputbox" size="1"', 'value', 'text',
                                               $associateArray[$oneLang]['assocId']);
              }else{
                  $associate_vehicle_list = null;
              }

              $associateArray[$oneLang]['list'] = $associate_vehicle_list;

              if(isset($vehicleAssociateVehicle[$oneLang]) && $vehicleAssociateVehicle[$oneLang] !== $vehicle->id ){
                  $associateArray[$oneLang]['assocId'] = $vehicleAssociateVehicle[$oneLang];
              }else{
                  $associateArray[$oneLang]['assocId'] = 0;
              }
            }
      return $associateArray;
    }
}


if(!function_exists('save_vehicle_associate')){
  function save_vehicle_associate(){
    global $database;

    $id_check = protectInjectionWithoutQuote('id', '');
    $id_true = protectInjectionWithoutQuote('idtrue', '');
    $language_post = protectInjectionWithoutQuote('language', '');

    if($id_check){
      if(empty($id_true)){
       //----------get new values (what vehicles we choose for chaque language) --------------------------//
        $i = 1;
        $assocArray = array();
        $assocVehicleId = array();

        while( isset($_REQUEST["associate_vehicle".$i]) ){
            $langAssoc = protectInjectionWithoutQuote("associate_vehicle_lang".$i,'');
            $valAssoc = protectInjectionWithoutQuote("language_associate_vehicle".$i,'');

          if($valAssoc == '' ) {
            $i++;
            continue;
          }
          $assocArray[$langAssoc] = $valAssoc;
          if($valAssoc){
            $assocVehicleId[] = $valAssoc;
          }
          $i++;
        }
        $query = "select `language` from #__vehiclemanager_vehicles where `id` = ".$id_check."";
        $database->setQuery($query);
        $oldLang = $database->loadResult();
        if(count($assocArray) > 0 ) {
          $assocArray[$language_post] = $id_check;
          $assocStr = serialize($assocArray);
      //-----------slect associate with old values------------------------------------------//
          $oldAssociateArray = getAssociateOld();
        //----------------------------------------------------------------//
          if(!isset($assocVehicleId[$id_check])){
            $assocVehicleId[] = $id_check;
          }
          if($assocArray !== $oldAssociateArray){ //-----------compare old and new values--
            //---------set null for vehicles that are not more in associates----------------//
            ClearAssociateDiff();
            //---------set new associates for vehicles that are choosed----------------//
            $idToChange = implode(',' , $assocVehicleId); //--ids of new vehicles  where we set new values for column associate_vehicle
            if(count($idToChange) && !empty($idToChange)){
              $query = "SELECT * FROM #__vehiclemanager_rent
                        WHERE `fk_vehicleid` IN (".$idToChange.")
                        AND `rent_return` is NULL";
              $database->setQuery($query);
              $CheckAssociate = $database->loadObjectList();
              if(!empty($CheckAssociate))            {
                echo "<script> alert(' You must return all vehicles from rent first! '); window.history.go(-1); </script>";
                exit;
              }
              $query = "UPDATE #__vehiclemanager_vehicles
                        SET `associate_vehicle`='".$assocStr."' where `id` in (".$idToChange.")";
              $database->setQuery($query);
              $database->execute();
              }else{
                $query = "UPDATE #__vehiclemanager_vehicles
                          SET `associate_vehicle`= null where `id` = ".$id_check."";
                $database->setQuery($query);
                $database->execute();
            }
          }
        }
      }
    }
  }
}

////////////////////////////STORE video/track functions START\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if (!function_exists('VHStoreTrack')) {
  function VHStoreTrack(&$vehicle) {
    global $vehiclemanager_configuration, $mosConfig_absolute_path;
    $app = JFactory::getApplication();
    $jinput = $app->input;
    for ($i = 1;isset($_FILES['new_upload_track' . $i])
      || array_key_exists('new_upload_track_url' . $i, $_POST);$i++) {
        $track_name = '';
        if (isset($_FILES['new_upload_track' . $i]) && $_FILES['new_upload_track' . $i]['name'] != "") {
          //storing e-Document

            $track = $jinput->files->get('new_upload_track' . $i);

            $ext = pathinfo($track['name'], PATHINFO_EXTENSION);
            $allowed_exts = explode(",", $vehiclemanager_configuration['allowed_exts_track']);
            $ext = strtolower($ext);
            if (!in_array($ext, $allowed_exts)) {
            echo "<script> alert(' File ext. not allowed to upload! - " . $ext .
                                 "'); window.history.go(-1); </script>\n";
            exit();
            }
          $code = guid();

          // $track_name = $code . '_' . filter($track['name']);
          $track_name = $code . '_' . vh_remove_forbidden_characters_whitespaces_from_file_name( $track['name'] );

          if (intval($track['error']) > 0 && intval($track['error']) < 4) {
            echo "<script> alert('" . _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_ERROR . " - " .
                                 $track_name . "'); window.history.go(-1); </script>\n";
            exit();
          } else if (intval($track['error']) != 4) {
            $track_new = $mosConfig_absolute_path . $vehiclemanager_configuration['tracks']['location'] . $track_name;
            if (!move_uploaded_file($track['tmp_name'], $track_new)) {
              echo "<script> alert('" . _VEHICLE_MANAGER_LABEL_TRACK_UPLOAD_ERROR . " - " .
                                   $track_name . "'); window.history.go(-1); </script>\n";
              exit();
            }
          }
        }
        if (array_key_exists('new_upload_track_kind' . $i, $_POST)
          && $_POST['new_upload_track_kind' . $i] != "") {
            $uploadTrackKind = protectInjectionWithoutQuote('new_upload_track_kind' . $i, '');
            $uploadTrackKind = strip_tags(trim($uploadTrackKind));
        }
        if (array_key_exists('new_upload_track_scrlang' . $i, $_POST)
          && $_POST['new_upload_track_scrlang' . $i] != "") {
            $uploadTrackScrlang = protectInjectionWithoutQuote('new_upload_track_scrlang' . $i, '');
            $uploadTrackScrlang = strip_tags(trim($uploadTrackScrlang));
        }
        if (array_key_exists('new_upload_track_label' . $i, $_POST)
          && $_POST['new_upload_track_label' . $i] != "") {
            $uploadTrackLabel = protectInjectionWithoutQuote('new_upload_track_label' . $i, '');

            $uploadTrackLabel = strip_tags(trim($uploadTrackLabel));
        }
        if (array_key_exists('new_upload_track_url' . $i, $_POST) && $_POST['new_upload_track_url' . $i] != "") {
          $uploadTrackURL = protectInjectionWithoutQuote('new_upload_track_url' . $i, '');
          $uploadTrackURL = strip_tags(trim($uploadTrackURL));
          if (empty($track_name) && !empty($uploadTrackURL))
            VHSaveTracks($vehicle->id, $uploadTrackURL, $uploadTrackKind, $uploadTrackScrlang, $uploadTrackLabel);
        }
        if (!empty($track_name))
            VHSaveTracks($vehicle->id, $track_name, $uploadTrackKind, $uploadTrackScrlang, $uploadTrackLabel);
    }
  }
}

if (!function_exists('checkMimeType')) {
  function checkMimeType($ext) {
    global $database;
    $database->setQuery("SELECT mime_type FROM #__vehiclemanager_mime_types WHERE mime_ext=".$database->quote($ext));
    $type = $database->loadResult();
    if(!$type)
      $type = 'unknown';
    return $type;
  }
}

if (!function_exists('filter')) {
  function filter($value) {
    $value = str_replace(array("/", "|", "\\", "?", ":", ";", "*", "#", "%", "$", "+", "=", ";", " "), "_", $value);
    return $value;
  }
}

if (!function_exists('guid')) {
  function guid() {
    if (function_exists('com_create_guid')) {
      return com_create_guid();
    } else {
      mt_srand((double)microtime() * 10000); //optional for php 4.2.0 and up.
      $charid = strtoupper(md5(uniqid(rand(), true)));
      $hyphen = chr(45); // "-"
      $uuid = //chr(123)// "{"
      substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
      //.chr(125);// "}"
      return $uuid;
    }
  }
}

if (!function_exists('VHStoreVideo')) {
  function VHStoreVideo(&$vehicle) {
    global $vehiclemanager_configuration, $mosConfig_absolute_path;

    $app = JFactory::getApplication();
    $jinput = $app->input;

    for ($i = 1;isset($_FILES['new_upload_video' . $i])
      || array_key_exists('new_upload_video_url' . $i, $_POST)
      || array_key_exists('new_upload_video_youtube_code' . $i, $_POST);$i++) {
        $video_name = '';
        if (isset($_FILES['new_upload_video' . $i]) && $_FILES['new_upload_video' . $i]['name'] != "") {
          //storing e-Document
          $video = $jinput->files->get('new_upload_video' . $i);
          $ext = pathinfo($video['name'], PATHINFO_EXTENSION);
          $allowed_exts = explode(",", $vehiclemanager_configuration['allowed_exts_video']);
          $ext = strtolower($ext);
          if (!in_array($ext, $allowed_exts)) {
            echo "<script> alert(' File ext. not allowed to upload! - " . $ext.
                                     "'); window.history.go(-1); </script>\n";
            exit();
          }
           $type = checkMimeType($ext);

          if (stripos($type, $video['type'])===false) {
            echo "<script> alert(' File ext. not allowed to upload! - " . $ext.
                                     "'); window.history.go(-1); </script>\n";
            exit();
          }

          $code = guid();

          // $video_name = $code . '_' . filter($video['name']);
          $video_name = $code . '_' . vh_remove_forbidden_characters_whitespaces_from_file_name( $video['name'] );

          if (intval($video['error']) > 0 && intval($video['error']) < 4) {
            echo "<script> alert('" . _VEHICLE_MANAGER_LABEL_VIDEO_UPLOAD_ERROR . " - " .
                                   $video_name . "'); window.history.go(-1); </script>\n";
            exit();
          } else if (intval($video['error']) != 4) {
            $video_new = $mosConfig_absolute_path . $vehiclemanager_configuration['videos']['location']  . $video_name;
            if (!move_uploaded_file($video['tmp_name'], $video_new)) {
              echo "<script> alert('" . _VEHICLE_MANAGER_LABEL_VIDEO_UPLOAD_ERROR . " - " .
                                   $video_name . "'); window.history.go(-1); </script>\n";
              exit();
            }
            VHSaveVideos($video_name, $vehicle->id, $type);
          }
        }
        if (array_key_exists('new_upload_video_url' . $i, $_POST) && $_POST['new_upload_video_url' . $i] != "") {
          $uploadVideoURL = protectInjectionWithoutQuote('new_upload_video_url' . $i, '');
          $uploadVideoURL = strip_tags(trim($uploadVideoURL));
          $end = explode(".", $uploadVideoURL);
          $ext = end($end);
          $type = checkMimeType($ext);
          if(empty($video_name) && !empty($uploadVideoURL))
            VHSaveVideos($uploadVideoURL, $vehicle->id, $type);
        }
        if (array_key_exists('new_upload_video_youtube_code' . $i, $_POST)
          && $_POST['new_upload_video_youtube_code' . $i] != "") {
            $uploadVideoYoutubeCode = protectInjectionWithoutQuote('new_upload_video_youtube_code' . $i, '');
            $uploadVideoYoutubeCode = strip_tags(trim($uploadVideoYoutubeCode));
            VHSaveYouTubeCode($uploadVideoYoutubeCode, $vehicle->id);
        }
      }
  }
}

if (!function_exists('VHSaveTracks')) {
    function VHSaveTracks($veh_id, $src, $uploadTrackKind, $uploadTrackScrlang, $uploadTrackLabel) {
        global $database,$vehiclemanager_configuration, $mosConfig_absolute_path;
        if ($src != "" && !strstr($src, "http")) {
          $query = "INSERT INTO #__vehiclemanager_track_source (fk_vehicle_id,src,kind,scrlang,label)".
                    "\n VALUE ($veh_id,
                              '" . $vehiclemanager_configuration['tracks']['location'].$src . "',
                              '" . $uploadTrackKind . "',
                              '" . $uploadTrackScrlang . "',
                              '" . $uploadTrackLabel . "')";
        }else{
          $query ="INSERT INTO #__vehiclemanager_track_source (fk_vehicle_id,src,kind,scrlang,label)".
                  "\n VALUE ($veh_id,
                            '" . $src."',
                            '" . $uploadTrackKind . "',
                            '" . $uploadTrackScrlang . "',
                            '" . $uploadTrackLabel . "')";
        }
        $database->setQuery($query);
        $database->execute();
    }
}

if (!function_exists('VHSaveVideos')) {
  function VHSaveVideos($src, $veh_id, $type) {
    global $database,$vehiclemanager_configuration, $mosConfig_absolute_path;
    if ($src != "" && strstr($src, "http")) {
      $query = "INSERT INTO #__vehiclemanager_video_source(fk_vehicle_id, src, type)".
                                                  "\n VALUE($veh_id,'" . $src . "', '" . $type . "')";
    }else{
      $query = "INSERT INTO #__vehiclemanager_video_source(fk_vehicle_id,src,type)".
                "\n VALUE($veh_id,
                        '".$vehiclemanager_configuration['videos']['location'].$src."',
                        '".$type."')";
    }
    $database->setQuery($query);
    $database->execute();
  }
}

if (!function_exists('VHSaveYouTubeCode')) {
  function VHSaveYouTubeCode($youtube_code, $veh_id) {
    global $database;
      $database->setQuery("SELECT id FROM #__vehiclemanager_video_source
                            WHERE youtube != ''
                            AND fk_vehicle_id = $veh_id");
      $database->execute();
      $youtubeId = $database->LoadResult();
    if ($youtube_code != '' && !empty($youtubeId)) {
      $query = "UPDATE #__vehiclemanager_video_source".
                "\n SET youtube = '" . $youtube_code . "'".
                "\n WHERE id = $youtubeId";
    } else {
      $query = "INSERT INTO #__vehiclemanager_video_source (fk_vehicle_id,youtube)".
                "\n VALUE($veh_id,'" . $youtube_code . "')";
    }
    $database->setQuery($query);
    $database->execute();
  }
}


////////////////////////////STORE video/track functions END\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

///////////////////////////DELETE video/track functions START\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if (!function_exists('VHDeleteTracks')) {
  function VHDeleteTracks($veh_id) {
    global $database, $mosConfig_absolute_path, $mosConfig_live_site, $vehiclemanager_configuration;
    $database->setQuery("SELECT id FROM #__vehiclemanager_track_source where fk_vehicle_id = $veh_id;");
    $tdiles_id = $database->loadColumn();
    $deleteTr_id = array();
    foreach($tdiles_id as $key => $value) {
      if (isset($_POST['track_option_del' . $value])) {
        array_push($deleteTr_id, protectInjectionWithoutQuote('track_option_del' . $value, ''));
      }
    }
    if ($deleteTr_id) {
      $del_tid = implode(',', $deleteTr_id);
      $sql = "SELECT src FROM #__vehiclemanager_track_source WHERE id IN (" .$del_tid . ")";
      $database->setQuery($sql);
      $tracks = $database->loadColumn();
      if ($tracks) {
        foreach($tracks as $name) {
          if (substr($name, 0, 4) != "http") unlink($mosConfig_absolute_path . $name);
        }
      }
      $sql = "DELETE FROM #__vehiclemanager_track_source WHERE (id IN (" . $del_tid . "))
              AND (fk_vehicle_id = $veh_id)";
      $database->setQuery($sql);
      $database->execute();
    }
  }
}

if (!function_exists('VHDeleteVideos')) {
  function VHDeleteVideos($veh_id) {
    global $database, $mosConfig_absolute_path, $mosConfig_live_site, $vehiclemanager_configuration;
    $database->setQuery("SELECT id FROM #__vehiclemanager_video_source where fk_vehicle_id = $veh_id;");
    $vdiles_id = $database->loadColumn();
    $deleteVid_id = array();
    foreach($vdiles_id as $key => $value) {
      if (isset($_POST['video_option_del' . $value])) {
        array_push($deleteVid_id, protectInjectionWithoutQuote('video_option_del' . $value, ''));
      }
    }
    if ($deleteVid_id) {
      $del_id = implode(',', $deleteVid_id);
      $sql = "SELECT src FROM #__vehiclemanager_video_source WHERE id IN (". $del_id . ")";
      $database->setQuery($sql);
      $videos = $database->loadColumn();
      if ($videos) {
        foreach($videos as $name) {
          if (substr($name, 0, 4) != "http" && file_exists($mosConfig_absolute_path . $name))
            unlink($mosConfig_absolute_path . $name);
        }
      }
      $sql = "DELETE FROM #__vehiclemanager_video_source
              WHERE (id IN (" . $del_id . "))
              AND (fk_vehicle_id=$veh_id)";
      $database->setQuery($sql);
      $database->execute();
    }
    $database->setQuery("SELECT id FROM #__vehiclemanager_video_source where fk_vehicle_id = $veh_id AND youtube IS NOT NULL;");
    $youtubeid = $database->loadResult();
    if (!empty($youtubeid)) {
      if (isset($_POST['youtube_option_del' . $youtubeid])) {
        $y_t_id = mosGetParam($_REQUEST, 'youtube_option_del' . $youtubeid, '');
        $sql = "DELETE FROM #__vehiclemanager_video_source
                WHERE id = $y_t_id
                AND fk_vehicle_id=$veh_id";
        $database->setQuery($sql);
        $database->execute();
      }
    }
  }
}
///////////////////////////DELETE video/track fucntions END\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

if(!function_exists('available_dates')){
  function available_dates($vehicle_id){
   global $database, $vehiclemanager_configuration;
    $date_NA=array();
    $query = "SELECT rent_from, rent_until
              FROM #__vehiclemanager_rent WHERE fk_vehicleid='".$vehicle_id."'
              AND rent_return is null";
    $database->setQuery($query);
    $calenDate = $database->loadObjectList();
     foreach($calenDate as $calenDate){
      $not_av_from = $calenDate->rent_from;
      $not_av_until = $calenDate->rent_until;
      $not_av_from_begin = new DateTime( $not_av_from);
      $not_av_until_end = new DateTime( $not_av_until);
      // $not_av_until_end = $not_av_until_end->modify( '+1 day' );

      if($vehiclemanager_configuration['special_price']['show']){
        $not_av_until_end = $not_av_until_end->modify( '+1 day' );
      }

      $interval = new DateInterval('P1D');
      $daterange = new DatePeriod($not_av_from_begin, $interval, $not_av_until_end);
        foreach($daterange as $datess){
            $date_NA[] = $datess->format("Y-m-d");
            $date_NA[] = $datess->format("d-m-Y");
        }
      }
    return $date_NA;
  }
}

if(!function_exists('transforDateFromPhpToJquery_vm')){
    function transforDateFromPhpToJquery_vm(){
      global $vehiclemanager_configuration;
      $DateToFormat = str_replace("d",'dd',(str_replace("m",'mm',(str_replace("Y",'yy',(
        str_replace('%','',$vehiclemanager_configuration['date_format'])))))));
      return $DateToFormat;
    }
}

/**
 * Return the byte value of a particular string.
 *
 * @param   string  $val  String optionally with G, M or K suffix
 *
 * @return  integer   size in bytes
 *
 * @since 1.6
 */
if(!function_exists('return_bytes')){
    function return_bytes($val)
    {
        if (empty($val))
        {
            return 0;
        }

        $val = trim($val);

        preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

        $last = '';

        if (isset($matches[2]))
        {
            $last = $matches[2];
        }

        if (isset($matches[1]))
        {
            $val = (int) $matches[1];
        }

        switch (strtolower($last))
        {
            case 'g':
            case 'gb':
                $val *= 1024;
            case 'm':
            case 'mb':
                $val *= 1024;
            case 'k':
            case 'kb':
                $val *= 1024;
        }

        return (int) $val;
    }

  // function return_bytes($val) {
  //   $val = trim($val);
  //   $last = strtolower($val[strlen($val)-1]);
  //   switch($last) {
  //       // Модификатор 'G' доступен, начиная с PHP 5.1.0
  //       case 'g':
  //           $val *= 1024;
  //       case 'm':
  //           $val *= 1024;
  //       case 'k':
  //           $val *= 1024;
  //   }
  //   return $val;
  // }
}

if(!function_exists('protectInjection')){
    function protectInjection($element, $def = '', $filter = "STRING",$bypass_get=false){
        
        $database = JFactory::getDBO();

        if(!$bypass_get){
                $value = JFactory::getApplication()->input->get($element, $def, $filter);
                // $value = $element;
             }else{
                $value = $element;
             }

        if(empty($value)) return $value;



        if(is_array($value)){
            foreach($value as $key=>$item_value){
                $value[$key] = $database->quote($item_value);
            }
            return $value ;

        }

        return $database->quote($value);
    }

}

if(!function_exists('protectInjectionWithoutQuote')){
    function protectInjectionWithoutQuote($element, $def = '', $filter = "STRING", $bypass_get=false){
        
        $database = JFactory::getDBO();
        
        if(!$bypass_get){
                $value = JFactory::getApplication()->input->get($element, $def, $filter);
                // $value = $element;
             }else{
                $value = $element;
             }

        if(empty($value)) return $value;
        $special_symb = array("/*","*/","select", "insert", "update", "drop", "delete", "alter");

        if(is_array($value)){
            foreach($value as $key => $val){
                if(!is_array($val)){
                    $isset_spec_symb = false;
                    foreach($special_symb as $symb){
                        if(stripos($val, $symb) !== false){
                            $isset_spec_symb = true;
                            continue;
                        }
                    }
                    if($isset_spec_symb){
                        $val = protectInjection($val, $def , $filter ,true);
                    }else{
                        $val = protectInjectionWithoutQuote($val, $def , $filter ,true);
                    }
                }else{
                    $val = protectInjectionWithoutQuote($val, $def , $filter ,true);
                }
                $value[$key] = $val;
            }
        }else {
            $isset_spec_symb = false;
            foreach($special_symb as $symb){
                if(stripos($value, $symb) !== false){
                    $isset_spec_symb = true;
                    continue;
                }
            }
            if($isset_spec_symb){
                $value = protectInjection($val, $def , $filter ,true);
            }else{
                $value = $database->escape($value);
            }
            
            
        }

        return $value;
    }
}




if (!function_exists('checkJavaScriptIncludedVEH')) {
  function checkJavaScriptIncludedVEH($name) {

      $doc = JFactory::getDocument();

      foreach($doc->_scripts as $script_path=>$value){
        if(strpos( $script_path, $name ) !== false ) return true ;
      }
      return false;
  }
}

if (!function_exists('checkStylesIncludedVEH')) {
  function checkStylesIncludedVEH($name) {

      $doc = JFactory::getDocument();

      foreach($doc->_styleSheets as $script_path=>$value){
        if(strpos( $script_path, $name ) !== false ) return true ;
      }
      return false;
  }
}

if (!function_exists('vm_add_new_features_constant')) {
  function vm_add_new_features_constant() {
    global $database, $mosConfig_absolute_path;

    //add new features constant for all which exists before
    $query = "SELECT f.* ";
    $query .= "FROM #__vehiclemanager_feature as f ";
    $database->setQuery($query);
    $vehicle_features = $database->loadObjectList();
    $i = 0 ;
    foreach ($vehicle_features as $vehicle_feature) {
      //check so consts already added ?
      $query = "select * from #__vehiclemanager_const where const ='".'_VEHICLE_MANAGER_FEATURE'.$vehicle_feature->id."'";
      $database->setQuery($query);
      $defined_consts = $database->loadobjectList();
      if(count($defined_consts) > 0 ) {
       $vehicle_features[$i]->fk_constid = $defined_consts[0]->id ; 
       $i++;
       continue ; 
      }

      //add new features constant
      $query = "insert IGNORE into #__vehiclemanager_const ( `const`,`sys_type`)
        values('_VEHICLE_MANAGER_FEATURE".$vehicle_feature->id."','Features')";
      $database->setQuery($query);
      $database->execute();

      $vehicle_features[$i]->fk_constid = $database->insertid();
      $i++;
    }

    foreach ($vehicle_features as $vehicle_feature) {
      
      $query = "select * from #__vehiclemanager_languages";
      $database->setQuery($query);
      $defined_languages = $database->loadobjectList();
      foreach ($defined_languages as $defined_language) {
          $query = "insert IGNORE into #__vehiclemanager_const_languages ( `fk_constid`,`fk_languagesid`,`value_const`) values(".$vehicle_feature->fk_constid .",".$defined_language->id .",".$database->Quote($vehicle_feature->name).")";
          $database->setQuery($query);
          $database->execute();
      }
    }
  }
}


if (!function_exists('vm_add_new_features_category_constant')) {
    function vm_add_new_features_category_constant() {
        global $vehiclemanager_configuration, $database;

        $categories_constants = array();
        $categ = explode(',', $vehiclemanager_configuration['featuredmanager']['placeholder']);
        for ($i = 0; $i < count($categ); $i++) {
            $categories_constants[] = '_VEHICLE_MANAGER_FEATURE_CATEGORY_'.trim( $categ[$i] );
        }


        if(count($categories_constants) > 0 ) {

            //remove old features category constants
            $query = "select * from #__vehiclemanager_const where `sys_type` = 'Features Category'";
            $database->setQuery($query);
            $defined_constants = $database->loadobjectList();
            $categories_old_constants = array() ;

            $query = "select * from #__vehiclemanager_languages";
            $database->setQuery($query);
            $defined_languages = $database->loadobjectList();            

            foreach ($defined_constants as $defined_constant) {

                //find features in this category
                $query = "select * from #__vehiclemanager_feature where ".
                        " `categories` = '".substr($defined_constant->const ,strlen("_VEHICLE_MANAGER_FEATURE_CATEGORY_") ) ."'";
                $database->setQuery($query);
                $defined_features_category = $database->loadobjectList();

                //if this category not exist and if not exist features with this category
                if( in_array($defined_constant->const,$categories_constants) == FALSE &&
                    count($defined_features_category) == 0 ) {
                    //delete features laguages constant
                    $query = "select * from #__vehiclemanager_const where `const` = '".$defined_constant->const."'";
                    $database->setQuery($query);
                    $vehiclemanager_consts = $database->loadobjectList();
                    foreach ($vehiclemanager_consts as $vehiclemanager_const) {
                        $query = "DELETE FROM #__vehiclemanager_const_languages where `fk_constid` = ".$vehiclemanager_const->id ;
                        $database->setQuery($query);
                        $database->execute();
                    }

                    //delete features constant
                    $query = "DELETE FROM #__vehiclemanager_const where `const` = '".$defined_constant->const."'";
                    $database->setQuery($query);
                    $database->execute();
                }

                //check exist constant only in #__vehiclemanager_const or also in #__vehiclemanager_const_languages
                $query = "select * from #__vehiclemanager_const where `const` = '".$defined_constant->const."'";
                $database->setQuery($query);
                $vm_consts = $database->loadobjectList();   

                if( count($vm_consts)  == 0  ){
                  $categories_old_constants[] = $defined_constant->const ;
                  continue ;

                }

                $query = "select * from #__vehiclemanager_const_languages where `fk_constid` = '".$vm_consts[0]->id."'";
                $database->setQuery($query);
                $vm_consts_lang = $database->loadobjectList() ;

                //if some category constant exist in some languages but not exist an other
                //remove all and unsert again
                if(count($vm_consts_lang) < count($defined_languages) ){

                    $query = "DELETE FROM #__vehiclemanager_const_languages where `fk_constid` = ".$vm_consts[0]->id ;
                    $database->setQuery($query);
                    $database->execute();

                    $query = "DELETE FROM #__vehiclemanager_const where `id` = ".$vm_consts[0]->id ;
                    $database->setQuery($query);
                    $database->execute();     

                    //do continue special for not add this constant to $categories_old_constants and add this constant again to DB 
                    continue;

                }

                $categories_old_constants[] = $defined_constant->const ;

            }

            //create new constants for new features categories
            foreach ($categories_constants as $categories_constant) {
                if( in_array($categories_constant,$categories_old_constants) == FALSE) {
                    //insert features category languages constant
                    $query = "insert IGNORE into #__vehiclemanager_const ( `const`,`sys_type`) values('".$categories_constant."','Features Category')";
                    $database->setQuery($query);
                    $database->execute();

                    $const_id = $database->insertid();

                    foreach ($defined_languages as $defined_language) {
                        $query = "insert IGNORE into #__vehiclemanager_const_languages ( `fk_constid`,`fk_languagesid`,`value_const`) values(".$const_id.",".$defined_language->id .",". $database->Quote(substr($categories_constant,strlen("_VEHICLE_MANAGER_FEATURE_CATEGORY_") ) ).")";
                        $database->setQuery($query);
                        $database->execute();
                    }
                }
            }
        }
    }
}

// function remove forbidden characters and replace whitespaces by hyphens:
if (!function_exists('vh_remove_forbidden_characters_whitespaces_from_file_name')) {
    function vh_remove_forbidden_characters_whitespaces_from_file_name($file_path_name) {
        // split a file name on main parts:
        $file_name_parts = pathinfo( $file_path_name );
        // take the part what we need (file extension and file name):
        $file_name_ext = $file_name_parts['extension'];
        $file_name = $file_name_parts['filename'];
        // file name check with Joomla-function stringURLUnicodeSLug (it is this function remove forbidden characters and replace whitespaces by hyphens):
        $file_name_clear = JFilterOutput::stringURLUnicodeSLug( $file_name );
        // compile file name and file extension in one string by concatenation:
        $file_name_clear_ext = $file_name_clear . "." . $file_name_ext;
        return $file_name_clear_ext;
    }
}

// function check is enabled Joomla Google Captcha-reCaptcha plugin and in administrator global sittings :
if (!function_exists('vh_check_enabled_google_captcha_recaptcha')) {
    function vh_check_enabled_google_captcha_recaptcha() {

        global $vehiclemanager_configuration;

        //Check enabled plugin captcha-recaptcha
        $recaptchaPluginEnabled = JPluginHelper::isEnabled('captcha', 'recaptcha');

        //Check enable option captcha-recaptcha in admin form
        $app = JFactory::getConfig();
        $recaptchaAdminEnabled = false;
        if($app->get('captcha') == 'recaptcha'){
          $recaptchaAdminEnabled = true;
        }

        //Check enable google recaptcha in vehicle settings
        $googleRecaptchaEnabled = false;
        if($recaptchaPluginEnabled && $recaptchaAdminEnabled && $vehiclemanager_configuration['google_captcha']['show']){
          $googleRecaptchaEnabled = true;
        }

        return $googleRecaptchaEnabled;
    }
}


if (!function_exists('vm_rotateImage')) {
    function vm_rotateImage($imagePath){

        $pathinfo = pathinfo($imagePath);
        if(!file_exists($imagePath)) return;

        if(strtolower($pathinfo['extension']) == 'jpeg'
           || strtolower($pathinfo['extension']) == 'jpg'){
            $exif = @exif_read_data($imagePath);

            if (!empty($exif['Orientation'])) {
                $imageResource = imagecreatefromjpeg($imagePath); // provided that the image is jpeg. Use relevant function otherwise
                switch ($exif['Orientation']) {
                    case 3:
                    $image = imagerotate($imageResource, 180, 0);
                    break;
                    case 6:
                    $image = imagerotate($imageResource, -90, 0);
                    break;
                    case 8:
                    $image = imagerotate($imageResource, 90, 0);
                    break;
                    default:
                    $image = $imageResource;
                }

                imagejpeg($image, $imagePath);
                imagedestroy($image);
            }

            return;
        }

    }
}

if (!function_exists('vm_checkFile')) {
    function vm_checkFile() {
        $path = $_GET["path"];
        $filename = basename($_GET["file"]);
        $file = $path . $filename;
        if (file_exists($file)) {
            echo "The file with such name already is!";
        } else {
            echo "";
        }
    }
}

if (!function_exists('vm_pdf_print_logo')) {
    function vm_pdf_print_logo() {
        global $mosConfig_absolute_path, $vehiclemanager_configuration;

        // Getting params from template
        $app  = JFactory::getApplication();
        $template_params = $app->getTemplate(true)->params;
        $sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

        $pdf_logo_file = '';
        
        $pdf_logo_file = ( file_exists($mosConfig_absolute_path . '/components/com_vehiclemanager/photos/pdf_print_logo/pdf_print_logo.png') ) ? 'components/com_vehiclemanager/photos/pdf_print_logo/pdf_print_logo.png' : '' ;

        // Logo file or site title/name param
        if ( $pdf_logo_file != '' && $vehiclemanager_configuration['pdf_print_logo']['show'] ) 
        {
            $logo = '<span align="center"><img src="' . htmlspecialchars(JUri::root() . $pdf_logo_file, ENT_QUOTES) . '" alt="' . $sitename . '" /></span>';
        }
        elseif ($template_params->get('sitetitle'))
        {
            $logo = '<span class="site-title" align="center" title="' . $sitename . '">' . htmlspecialchars($template_params->get('sitetitle'), ENT_COMPAT, 'UTF-8') . '</span>';
        }
        else
        {
            $logo = '<span class="site-title" align="center" title="' . $sitename . '">' . $sitename . '</span>';
        }

        return $logo;
    }
}

if(!function_exists('addInfoAboutUpdate')){
    function addInfoAboutUpdate() {
        
        global $mosConfig_absolute_path;
        // function for check equivalence versions
        function checkVersion($newversion, $oldversion) {
                if (strpos($newversion, " ") !== false ) 
                    $newversion = explode('.', substr($newversion, 0, strpos($newversion, ' ')));
                else  $newversion = explode('.', $newversion);

                $oldversion = explode('.', substr($oldversion, 0, strpos($oldversion, ' ')));
                return $oldversion === max($newversion, $oldversion);
        }
        // add info about update
        $url="https://ordasoft.com/xml_update/vehiclemanager.xml";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $data = curl_exec($ch);
        curl_close($ch);
 
        if (!$data || stristr($data, 'Not Found')) return; // exit from function if not xml
        $xml = simplexml_load_string($data);
        $ordasoftNewV = (string)$xml->version;
        $ordasoftCreationDate = (string)$xml->creationDate;
        unset($xml);

        $xml = simplexml_load_file($mosConfig_absolute_path . "/administrator/components/com_vehiclemanager/vehiclemanager.xml");
        $ordasoftCurV = (string)$xml->version;
        $creationDate = (string)$xml->creationDate;
        unset($xml);

        //-------------------------------------------------
        if (!empty($ordasoftNewV) && !checkVersion($ordasoftNewV, $ordasoftCurV)) {
            $message = "Available new version VehicleManager $ordasoftNewV" . 
            ", creation date $ordasoftCreationDate";
            JFactory::getApplication()->enqueueMessage($message);
        }
    }
}  



if (!function_exists('checkVMVersionProFree')) {
    function checkVMVersionProFree(){

        $activationNeed = false;
        $xml = @simplexml_load_file(JPATH_BASE . "/components/com_vehiclemanager/vehiclemanager.xml");
        if($xml){
            $version = (string)$xml->tag;
            unset($xml);

            return $version;
        }
        return "free" ;
    }
}

if (!function_exists('checkVMVersionNumber')) {
    function checkVMVersionNumber(){

        $activationNeed = false;
        $xml = @simplexml_load_file(JPATH_BASE . "/components/com_vehiclemanager/vehiclemanager.xml");
        if($xml){

            $version = (string)$xml->version;
            unset($xml);

            return $version;
        }
        return "" ;
    }
}

if (!function_exists('checkVMVersionDate')) {
    function checkVMVersionDate(){

        $activationNeed = false;
        $xml = @simplexml_load_file(JPATH_BASE . "/components/com_vehiclemanager/vehiclemanager.xml");
        if($xml){
            $date = (string)$xml->creationDate;
            unset($xml);

            return $date;
        }
        return "" ;
    }
}    

