<?php
if ($save_filter_cookie_data) {
  $savedfilter = json_decode($save_filter_cookie_data);
}elseif($back_cookie_data){
  $savedfilter = json_decode($back_cookie_data);
}elseif($saveinitialvalue){
  $savedfilter = json_decode($saveinitialvalue);
}
else {
  $savedfilter = "";
}

file_put_contents('save.txt', json_encode($savedfilter) );

$ringmaxmincaratdata = json_decode($ring_setting_cookie_data);

$slider_start = 1;
$loadfilter = $this->diamond_lib->getDiamondFilters($shop_url);
$isShowPrice = $loadfilter['isShowPrice']; 
file_put_contents('loadfilter.txt', json_encode($loadfilter));

$jc_options = $this->diamond_lib->getJCOptions($shop_url);
$data = $this->general_model->getDiamondConfig($shop_url);


if(isset($loadfilter['caratRange'][0])){
$activeFilter      = $filtermode;
$diamondAttributes = $this->diamond_lib->getDiamondAttributes();

$shapeArray        = $symmArray = $polishArray = $fluorArray = $cutArray = $clarityArray = $colorArray = $certiArray = $fancycolorArray = $intintensityArray = $overtoneArray = array();
$originval         = '';


if ($savedfilter != "") {
    if (isset($savedfilter->shapeList)) {
        $shapeArray = explode(',', $savedfilter->shapeList);
    }
    if (isset($savedfilter->OvertoneList)) {
        $overtoneArray = explode(',', $savedfilter->OvertoneList);
    }
    if (isset($savedfilter->certificate)) {
        $certiArray = explode(',', $savedfilter->certificate);
    }

}
if($default_shape_filter){
  $shapeArray = array($default_shape_filter,);
}
$retailerId = '';

if(isset($ringmaxmincaratdata[0]->centerStoneFit)){
  $ring_shape = strtolower($ringmaxmincaratdata[0]->centerStoneFit);
  $temp_shapeArray = explode(',', $ring_shape);
  $shapeArray = array_map('trim', $temp_shapeArray);
  $settingId = $ringmaxmincaratdata[0]->setting_id;
  $settings = $this->diamond_lib->getRingById($settingId,$shop_url);
  $retailerId = $settings['ringData']['retailerInfo']->retailerID;
}
$backdiamonddata = json_decode($back_cookie_data);
$default_view_mode = $this->diamond_lib->getDefaultViewmode($shop_url);
$show_filter_info = $this->diamond_lib->showFilterInfo($shop_url);
?>
<style type="text/css">
.diamonds-details .noUi-value.noUi-value-horizontal.noUi-value-large:last-child {
    display: none;
}
</style>
<div class="filter-details">
  <input name="viewmode" id="viewmode" type="hidden" value="<?php echo ($savedfilter->viewmode ? $savedfilter->viewmode : $default_view_mode) ; ?>" />
  <input name="itemperpage" id="itemperpage" type="hidden" value="<?php echo $this->diamond_lib->getResultsPerPage(); ?>" />
  <input type="hidden" name="shapecookie" id="shapecookie" value='<?php echo json_encode($shapeArray,true); ?>' />
  <input type="hidden" name="orderby" id="orderby" value="<?php echo isset($savedfilter->orderBy) ? $savedfilter->orderBy : 'Size'; ?>" />
  <input type="hidden" name="direction" id="direction" value="<?php echo isset($savedfilter->direction) ? $savedfilter->direction : 'ASC'; ?>" />
  <input type="hidden" name="currentpage" id="currentpage" value="<?php echo isset($savedfilter->currentPage) ? $savedfilter->currentPage : '1'; ?>" />
  <input type="hidden" name="did" id="did" value="" />
  <input type="hidden" name="backdiamondid" id="backdiamondid" value="<?php echo $backdiamonddata->did; ?>" />
  <input type="hidden" name="retailerId" id="retailerId" value="<?php echo $retailerId; ?>" />

   <div class="shape-container shape-flex">
    <?php $shapeOptions = (array) $loadfilter['shapes'];?>
    <?php if($shapeOptions){
        ?>
      <div class="filter-main filter-alignment-right">
         <div class="filter-for-shape shape-bg">
            <h4 class="filter-title"><?php echo 'Shape';?>
              <?php if($show_filter_info == 'true') {?>
              <span class="show-filter-info" onclick="showfilterinfo('shape');"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
            <?php } ?>
            </h4>
            <ul id="shapeul">
                            <?php foreach ($shapeOptions as $options) : ?>
                                <?php if ($options->shapeName) { ?>
                                    <?php if ($shapeArray) { ?>
                                    <li class="<?php echo strtolower($options->shapeName)  ?> <?php echo ($options == end($shapeOptions)) ? 'last' : '' ?>" title="<?php echo $options->shapeName ?>">
                                        <div class="shape-type <?php echo (in_array(strtolower($options->shapeName), $shapeArray)) ? '' : 'unselected' ?>">
                                            <input type="checkbox" class="input-assumpte" id="diamond_shape_<?php echo strtolower($options->shapeName) ?>" name="diamond_shape[]" value="<?php echo strtolower($options->shapeName) ?>" />
                                           
                                        </div>

                                         
                                        <label for="diamond_shape_<?php echo $options->shapeName ?>" class="selected-shape <?php echo (in_array(strtolower($options->shapeName), $shapeArray)) ? '' : 'unselected' ?>"> <?php echo $options->shapeName ?></label>
                                    </li>
                                <?php } else { ?>
                                    <li class="<?php echo strtolower($options->shapeName) ?> <?php echo ($options == end($shapeOptions)) ? 'last' : '' ?>" title="<?php echo $options->shapeName ?>">
                                        <div class="shape-type <?php echo (in_array(strtolower($options->shapeName), $shapeArray)) ? 'selected active' : '' ?>">
                                            <input type="checkbox" class="input-assumpte" id="diamond_shape_<?php echo strtolower($options->shapeName) ?>" name="diamond_shape[]" value="<?php echo strtolower($options->shapeName) ?>" <?php echo (in_array(strtolower($options->shapeName), $shapeArray)) ? 'checked="checked"' : '' ?> />
                                        </div>
                                        <label for="diamond_shape_<?php echo $options->shapeName ?>"><?php echo $options->shapeName ?></label>
                                    </li>
                                <?php } ?>
                                <?php } ?>
                            <?php endforeach; ?>
            </ul>
         </div>
      </div>
      <?php }?>
      <?php $cutRangeOptions = (array) $loadfilter['cutRange']; ?>
      <?php if($cutRangeOptions){ ?>
      <div class="filter-main filter-alignment-left">
         <div class="filter-for-shape shape-bg cut-wrapper">
            <h4 class="filter-title"><?php echo 'Cut';?>
              <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('cut');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
            </h4>
            <div class="cut-main">
              <div id="cut-r-slider" class="cut-slider right-block ui-slider">
                <?php 
                foreach ($cutRangeOptions as $optionsValues) : 
                  $options = (array) $optionsValues;
                  $cuts_attr[]  = array('cutName' => $options['cutName'], 'cutId' => $options['cutId']);
                  $cuts_Idattr[]  = $options['cutId'];
                  if( !next( $cutRangeOptions ) ) {
                    $cuts_attr[]  = array('cutName' => 'Last', 'cutId' => '000');
                  }
                endforeach; 
                  //rangeslider($savedfilter->CutGradeList,$savedfilter->CutStart,$savedfilter->CutStop,$cuts_attr,$cuts_Idattr);
                  
                    if (isset($savedfilter->CutGradeList)) {
                      $CutGradeList = $savedfilter->CutGradeList;
                      file_put_contents('iflog.txt', $CutGradeList);
                    }
                    else
                    {
                      $CutGradeList = "";
                      file_put_contents('elselog.txt', $CutGradeList);

                    }
                    if (isset($savedfilter->CutStart)) {
                      $cutStart = $savedfilter->CutStart;
                    }
                    else
                    {
                      $cutStart = $slider_start;
                    }
                    if (isset($savedfilter->CutStop)) {
                      $cutStop = $savedfilter->CutStop;
                    }
                    else
                    {
                      $cutStop = count($cuts_attr);
                    } 

                    // if (empty($CutGradeList)) {
                    //     $CutGradeList = '1,2,3,4,5';
                    // }


                ?>
                <input type="hidden" class="diamond_cut range_<?php echo $cutStop-1;?>" name="diamond_cut" value="<?php echo $CutGradeList;?>" data-start="<?php echo $cutStart;?>" data-stop="<?php echo $cutStop;?>" />
                <div id="cut-slider" class="input-assumpte" data-steps="<?php echo count($cuts_attr);?>"></div>                            
              </div>
            </div>
         </div>
      </div>
      <?php }?>
   </div>

   <?php if($activeFilter != "navfancycolored") { ?>
   <?php $colorOptions = (array) $loadfilter['colorRange']; ?>
   <?php if($colorOptions){?>
   <div class="color-filter shape-bg" id="norcolor">
      <h4 class="filter-title"><?php echo 'Color';?>
        <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('color');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
      </h4>
      <div class="cut-main">
      <div id="color-r-slider" class="color-slider right-block ui-slider">
        <?php 
          foreach ($colorOptions as $optionsValues) : 
          $options = (array) $optionsValues;
          $colors_attr[]  = array('colorName' => $options['colorName'], 'colorId' => $options['colorId']);
          if( !next( $colorOptions ) ) {
            $colors_attr[]  = array('colorName' => 'Last', 'colorId' => '000');
          }
          $colors_Idattr[]  = $options['colorId'];
          endforeach;
          $_SESSION['colors_attr'] = $colors_attr;

          if(isset($savedfilter))
          {
            if (isset($savedfilter->ColorList)) {
              $colorsGradeList = $savedfilter->ColorList;
            }
            else
            {
              $colorsGradeList = "";
            }
            if (isset($savedfilter->ColorStart)) {
              $colorsStart = $savedfilter->ColorStart;
            }
            else
            {
              $colorsStart = $slider_start;
            }
            if (isset($savedfilter->ColorStop)) {
              $colorsStop = $savedfilter->ColorStop;
            }
            else
            {
              $colorsStop = count($colors_attr);
            }
          }

          if (empty($colorsGradeList)) {
              $colorsGradeList = '68,69,70,71,72,73,74,75,76,77,78,79,80';
          }
        ?>
        <input type="hidden" class="diamond_color range_<?php echo $colorsStop-1;?>" name="diamond_color" value="<?php echo $colorsGradeList;?>" data-start="<?php echo $colorsStart;?>" data-stop="<?php echo $colorsStop;?>"/>
        <div id="color-slider" class="input-assumpte" data-steps="<?php echo count($colors_attr);?>"></div>                            
      </div>
    </div>
   </div>
   <?php }
    } else { ?>
   <div id="fancydiv">
    <?php $diamondColorOptions = (array) $loadfilter['diamondColorRange']; 
    if($diamondColorOptions){
    ?>
   <div class="color-filter fancy-color-filter shape-bg">
      <h4 class="filter-title"><?php echo 'Color';?>
        <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('fancy_color');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
      </h4>
      <div class="cut-main">
      <div id="diamondcolor-r-slider" class="diamondcolor-slider right-block ui-slider">
        <?php 
        foreach ($diamondColorOptions as $optionsValues) : 
        $options = (array) $optionsValues;    
        $diamondcolors_attr[]  = array('diamondcolorName' => $options['diamondColorId'], 'diamondcolorId' => strtolower($options['diamondColorId']));

        $colorInitValues .= $options['diamondcolorName'].',';

        if( !next( $diamondColorOptions ) ) {
          $diamondcolors_attr[]  = array('diamondcolorName' => 'Last', 'diamondColorId' => '000');
        }
        $diamondcolors_Idattr[]  = $options['diamondColorId'];
        endforeach; 
        if (isset($savedfilter->FancycolorList)) {
          $fancyClrGradeList = $savedfilter->FancycolorList;
        }
        else
        {
          $fancyClrGradeList = "";
        }
        if (isset($savedfilter->FancycolorStart)) {
          $fancyStart = $savedfilter->FancycolorStart;
        }
        else
        {
          $fancyStart = $slider_start;
        }
        if (isset($savedfilter->FancycolorStop)) {
          $fancyStop = $savedfilter->FancycolorStop;
        }
        else
        {
          $fancyStop = count($diamondcolors_attr);
        }

        if (empty($fancyClrGradeList)) {
        $fancyClrGradeList = 'blue,pink,yellow,champagne,green,grey,purple,chameleon,violet';
        }
        ?>
        <input type="hidden" class="diamond_diamondcolor range_<?php echo $fancyStop-1;?>" name="diamond_fancycolor" value="<?php echo $fancyClrGradeList;?>" data-start="<?php echo $fancyStart;?>" data-stop="<?php echo $fancyStop;?>" />
        <div id="diamondcolor-slider" class="input-assumpte" data-steps="<?php echo count($diamondcolors_attr);?>"></div>                            
      </div>
    </div>
   </div>
   <?php } ?>
   <?php $intensityOptions = $loadfilter['intensity']; ?>
   <?php if($intensityOptions){?>
   <div class="color-filter fancy-IntIntensity-filter shape-bg">
      <h4 class="filter-title"><?php echo 'Fancy Intensity';?><?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('fancy_intensity');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
      </h4>
      <div class="cut-main">
      <div id="intensity-r-slider" class="intensity-slider right-block ui-slider">
        <?php 
        foreach ($intensityOptions as $optionsValues) : 
        $options = (array) $optionsValues;
        $intensitys_attr[]  = array('intensityName' => $options['intensityName'], 'intensityId' => strtolower($options['intensityName']));

        $intensityInitValues .= $options['intensityName'].',';

        if( !next( $intensityOptions ) ) {
          $intensitys_attr[]  = array('intensityName' => 'Last', 'intensityId' => '000');
        }
        $intensitys_Idattr[]  = $options['intensityName'];
        if (isset($savedfilter->IntintensityList)) {
          $intensitysGradeList = $savedfilter->IntintensityList;
        }
        else
        {
          $intensitysGradeList = "";
        }
        if (isset($savedfilter->IntintensityStart)) {
          $intensityStart = $savedfilter->IntintensityStart;
        }
        else
        {
          $intensityStart = $slider_start;
        }
        if (isset($savedfilter->IntintensityStop)) {
          $intensityStop = $savedfilter->IntintensityStop;
        }
        else
        {
          $intensityStop = count($intensitys_attr);
        }
        endforeach; 


        if (empty($intensitysGradeList)) {
        $intensitysGradeList = $intensityInitValues;
        }
      
        ?>
        <input type="hidden" class="diamond_intensity range_<?php echo $intensityStop-1;?>" name="diamond_intintensity" value="<?php echo $intensitysGradeList;?>" data-start="<?php echo $intensityStart;?>" data-stop="<?php echo $intensityStop;?>">
        <div id="intensity-slider" class="input-assumpte" data-steps="<?php echo count($intensitys_attr);?>"></div>                            
      </div>
    </div>
   </div>
   <?php } ?>
   </div>
   <?php } ?>
   <?php $clarityOptions = (array) $loadfilter['clarityRange']; 
   if($clarityOptions){
   ?>

   <div class="color-filter clarity-filter shape-bg">
      <h4 class="filter-title"><?php echo 'Clarity';?>
        <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('clarity');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
      </h4>
      <div class="cut-main">
        <div id="clarity-r-slider" class="clarity-slider right-block ui-slider">
          <?php 

          foreach ($clarityOptions as $optionsValues) : 
          $options = (array) $optionsValues;


          $claritys_attr[]  = array('clarityName' => $options['clarityName'], 'clarityId' => $options['clarityId']);
          $claritys_Idattr[]  = $options['clarityId'];


          $clarityInitValues .= $options['clarityName'].',';
         

          if( !next( $clarityOptions ) ) {
            $claritys_attr[]  = array('clarityName' => 'Last', 'clarityId' => '000');
          }
          endforeach; 


          if (isset($savedfilter->ClarityList)) {
            $clarityGradeList = $savedfilter->ClarityList;
          }
          else
          {
            $clarityGradeList = "";
          }
          if (isset($savedfilter->ClarityStart)) {
            $clarityStart = $savedfilter->ClarityStart;
          }
          else
          {
            $clarityStart = $slider_start;
          }
          if (isset($savedfilter->ClarityStop)) {
            $clarityStop = $savedfilter->ClarityStop;
          }
          else
          {
            $clarityStop = count($claritys_attr);
          }
          if (empty($clarityGradeList)) {
              $clarityGradeList = $clarityInitValues;
          }
          ?>
          <input type="hidden" class="diamond_clarity range_<?php echo $clarityStop-1;?>" name="diamond_clarity" value="<?php echo $clarityGradeList;?>" data-start="<?php echo $clarityStart;?>" data-stop="<?php echo $clarityStop;?>">
          <div id="clarity-slider" class="input-assumpte" data-steps="<?php echo count($claritys_attr);?>"></div>                            
        </div>
      </div>
   </div>
   <?php } ?>
    <div class="shape-container shape-flex price_carat-filter eq_wrapper">
      <?php $caratrange = (array) $loadfilter['caratRange'][0]; ?>
      <?php if($caratrange){ 
              if(isset($ringmaxmincaratdata[0]->ringmaxcarat)){ 
                $caratmaxval = $ringmaxmincaratdata[0]->ringmaxcarat;
              }
              if(isset($ringmaxmincaratdata[0]->ringmincarat)){ 
                $caratminval = $ringmaxmincaratdata[0]->ringmincarat;
              } 
              $caratrange = (array) $loadfilter['caratRange'][0]; 
              if($caratminval){ 
                $carat_from = $caratminval;
                $caratrange['minCarat'] = $caratminval;
              }
              else
              {
                $carat_from = isset($savedfilter->caratMin) ? $savedfilter->caratMin : $caratrange['minCarat']; 
              }
              if($caratmaxval)
              {
                $carat_to = $caratmaxval;
                $caratrange['maxCarat'] = $caratmaxval;
              }
              else
              {
                $carat_to = isset($savedfilter->caratMax) ? $savedfilter->caratMax : $caratrange['maxCarat'];
              }
      ?>
          <div class="filter-main filter-alignment-right carat-filter">
             <div class="filter-for-shape shape-bg">
                <h4 class="filter-title"><?php  echo 'Carat'; ?><?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('carat');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?></h4>
                <div class="slider_wrapper">
                  <div class="carat-main ui-slider" id="noui_carat_slider" data-min="<?php echo $caratrange['minCarat']; ?>" data-max="<?php echo $caratrange['maxCarat']; ?>">
                    <input type="text" class="ui-slider-val slider-left" name="diamond_carats[from]" autocomplete="off" value="<?php echo $carat_from ?>" data-type="min" inputmode="decimal" pattern="[-+]?[0-9]*[.,]?[0-9]+" />
                    <input type="text" class="ui-slider-val slider-right" name="diamond_carats[to]" autocomplete="off" value="<?php echo  $carat_to ?>" data-type="max" inputmode="decimal" pattern="[-+]?[0-9]*[.,]?[0-9]+" />
                    <input type="hidden" name="caratto" class="slider-right-val" value="<?php echo $caratrange['maxCarat'] ?>">
                  </div>
                </div>
             </div>
          </div>
      <?php }?>
      <?php $pricerange = (array) $loadfilter['priceRange'][0]; ?>
      <?php if($pricerange){?>
    <?php $pricerange = (array) $loadfilter['priceRange'][0]; ?>
    <?php $price_from = isset($savedfilter->PriceMin) ? $savedfilter->PriceMin : $pricerange['minPrice']; ?>
    <?php $price_to = isset($savedfilter->PriceMax) ? $savedfilter->PriceMax : $pricerange['maxPrice']; ?>
      <div class="filter-main filter-alignment-left price-filter">
      <?php  if ($isShowPrice == '1'){ ?> 
         <div class="filter-for-shape shape-bg">
            <h4 class="filter-title"><?php  echo 'Price'; ?>
               <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('price');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
            </h4>
            <div class="slider_wrapper">
               <div class="price-main ui-slider" id="price_slider" data-min="<?php echo $pricerange['minPrice']; ?>" data-max="<?php echo $pricerange['maxPrice']; ?>">

                <?php if($data->price_row_format == 'left') { ?> 
                  <div class="price-left price-filter_left">
                    <span class="currency-icon">
                    <?php 
                     if($loadfilter['currencyFrom'] == 'USD'){
                        echo "$"; 
                     }else{
                        echo $loadfilter['currencyFrom'].$loadfilter['currencySymbol']; 
                     }
                     ?>
                    </span>
                     <input type="text"
                        class="ui-slider-val slider-left"
                        name="price[from]"
                        value="<?php echo str_replace(",","",$price_from); ?>" inputmode="numeric" pattern="[-+]?[0-9]*[.,]?[0-9]+" data-type="min" autocomplete="off"/>
                  </div>
                  <div class="price-right price-filter_right">
                     <span class="currency-icon">
                      <?php 
                      if($loadfilter['currencyFrom'] == 'USD'){
                          echo "$"; 
                       }else{
                          echo $loadfilter['currencyFrom'].$loadfilter['currencySymbol']; 
                       }
                      ?>
                      </span>
                     <input type="text"
                        class="ui-slider-val slider-right"
                        name="price[to]"
                        value="<?php echo str_replace(",","",$price_to); ?>" inputmode="numeric"  data-type="max" autocomplete="off" />
                     <input type="hidden" name="priceto" class="slider-right-val" value="<?php echo round($pricerange['maxPrice']) ?>">
                  </div>
                <?php } else { ?>
                   <div class="price-left">
                      <span class="currency-icon">
                      <?php 
                       if($loadfilter['currencyFrom'] == 'USD'){
                          echo "$"; 
                       }else{
                          echo $loadfilter['currencyFrom'].$loadfilter['currencySymbol']; 
                       }
                       ?>
                      </span>
                     <input type="text"
                        class="ui-slider-val slider-left"
                        name="price[from]"
                        value="<?php echo str_replace(",","",$price_from); ?>" inputmode="numeric" pattern="[-+]?[0-9]*[.,]?[0-9]+" data-type="min" autocomplete="off"/>
                  </div>
                  <div class="price-right">
                     <span class="currency-icon">
                      <?php 
                      if($loadfilter['currencyFrom'] == 'USD'){
                          echo "$"; 
                       }else{
                          echo $loadfilter['currencyFrom'].$loadfilter['currencySymbol']; 
                       }
                      ?>
                      </span>
                     <input type="text"
                        class="ui-slider-val slider-right"
                        name="price[to]"
                        value="<?php echo str_replace(",","",$price_to); ?>" inputmode="numeric"  data-type="max" autocomplete="off" />
                     <input type="hidden" name="priceto" class="slider-right-val" value="<?php echo round($pricerange['maxPrice']) ?>">
                  </div>
                 <?php } ?>
               </div>
            </div>
         </div>
         <?php  } ?>
      </div>
      <?php }?>
   </div>
   <div class="filter-advanced shape-bg">
      <?php if($jc_options['jc_options']->show_Advance_options_as_Default_in_Diamond_Search) { ?>
      <button class="accordion"><?php echo 'Advanced Search';?></button>
    <?php }?>
      <div class="panel cls-for-hide">
         <div class="shape-flex eq_wrapper">
        <?php $depthRange = (array) $loadfilter['depthRange'][0]; ?>
        <?php if($depthRange){?>
    <?php $depthRange = (array) $loadfilter['depthRange'][0]; ?>
    <?php $depthFrom = isset($savedfilter->depthMin) ? $savedfilter->depthMin : floor($depthRange['minDepth']); 
    $maxDepth = $depthRange['maxDepth'];
    if($maxDepth == 0 || $maxDepth == 0.00)
    {
      $maxDepth = 100;
    }
    $depthTo = isset($savedfilter->depthMax) ? $savedfilter->depthMax : floor($maxDepth); ?>
         <div class="filter-main filter-alignment-left rb_depth_filter">
            <div class="filter-for-shape shape-bg">
               <h4 class="filter-title"><?php  echo 'Depth'; ?>
                 <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('depth');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
               </h4>
               <div class="slider_wrapper">
                  <div class="depth-main ui-slider" id="depth_slider" data-min="<?php echo $depthRange['minDepth']; ?>" data-max="<?php echo $maxDepth; ?>">
                     <div class="depth-left">
                        <input type="number" class="ui-slider-val slider-left" name="diamond_depth[from]" value="<?php echo $depthFrom; ?>" pattern="[\d\.]*" step="0.01" data-type="min" autocomplete="off"/>
                        <span class="currency-icon">%</span>
                     </div>
                     <?php $depthTo = isset($savedfilter->depthMax) ? $savedfilter->depthMax : floor($maxDepth) ?>
                     <div class="depth-right">
                        <input type="number" class="ui-slider-val slider-right" name="diamond_depth[to]" value="<?php echo $depthTo; ?>" pattern="[\d\.]*" step="0.01" data-type="max" autocomplete="off"/>
                        <input type="hidden" name="depthTo" class="slider-right-val" value="<?php echo floor($maxDepth); ?>"> 
                        <span class="currency-icon">%</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
        <?php }?>
        <?php $tableRange = (array) $loadfilter['tableRange'][0]; 
        if($tableRange){
      $tableRange = (array) $loadfilter['tableRange'][0];
      $maxTable = $tableRange['maxTable'];
      if($maxTable == 0 || $maxTable == 0.00)
      {
        $maxTable = 100;
      }
        $tableFrom = isset($savedfilter->tableMin) ? $savedfilter->tableMin : floor($tableRange['minTable']); 
      $tableTo = isset($savedfilter->tableMax) ? $savedfilter->tableMax : floor($maxTable);
    ?>
         <div class="filter-main filter-alignment-left rb_table_filter">
            <div class="filter-for-shape shape-bg">
               <h4 class="filter-title"><?php  echo  'Table'; ?>
                 <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('table');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
               </h4>
               <div class="slider_wrapper">
                  <div  class="tableper-main ui-slider" id="tableper_slider" data-min="<?php echo $tableRange['minTable']; ?>" data-max="<?php echo $maxTable; ?>">
                    <?php 
                     $tableRange = (array) $loadfilter['tableRange'][0];
                     $tableFrom = isset($savedfilter->tableMin) ? $savedfilter->tableMin : 0 ?>
                     <div class="table-left">
                        <input type="number" class="ui-slider-val slider-left" name="diamond_table[from]" value="<?php echo $tableFrom; ?>" pattern="[\d\.]*" step="0.01" data-type="min" autocomplete="off"/>
                        <span class="currency-icon">%</span>
                     </div>
                     <div class="table-right">
                        <input type="number" class="ui-slider-val slider-right" name="diamond_table[to]" value="<?php echo $tableTo; ?>" pattern="[\d\.]*" step="0.01" data-type="max" autocomplete="off"/>
                        <input type="hidden" name="tableTo" class="slider-right-val" value="<?php echo floor($maxTable); ?>">
                        <span class="currency-icon">%</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
          <?php }?>
        </div>
          <?php $polishOptions = (array) $loadfilter['polishRange']; ?>
           <div class="shape-flex">
         <div class="filter-advanced-main advance-left rb_polish_filter">
          <?php if($polishOptions){?>
            <div class="polish-depth shape-bg">
            <h4 class="filter-title"><?php echo 'Polish';?>
              <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('polish');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
            </h4>
              <div class="cut-main">
                <div id="polish-r-slider" class="polish-slider right-block ui-slider">
                  <?php 
                  foreach ($polishOptions as $optionsValues) : 
                  $options = (array) $optionsValues;
                  $polishs_attr[]  = array('polishName' => $options['polishName'], 'polishId' => $options['polishId']);
                  if( !next( $polishOptions ) ) {
                    $polishs_attr[]  = array('clarityName' => 'Last', 'clarityId' => '000');
                  }
                  $polishs_Idattr[]  = $options['polishId'];
                  endforeach; 
                  if (isset($savedfilter->polishList)) {
                    $polishsGradeList = $savedfilter->polishList;
                  }
                  else
                  {
                    $polishsGradeList = "";
                  }
                  if (isset($savedfilter->PolishStart)) {
                    $polishsStart = $savedfilter->PolishStart;
                  }
                  else
                  {
                    $polishsStart = $slider_start;
                  }
                  if (isset($savedfilter->PolishStop)) {
                    $polishsStop = $savedfilter->PolishStop;
                  }
                  else
                  {
                    $polishsStop = count($polishs_attr);
                  }
                  ?>
                  <input type="hidden" class="diamond_polish range_<?php echo $polishsStop-1;?>" name="diamond_polish" value="<?php echo $polishsGradeList;?>" data-start="<?php echo $polishsStart;?>" data-stop="<?php echo $polishsStop;?>" />
                  <div id="polish-slider" class="input-assumpte" data-steps="<?php echo count($polishs_attr);?>"></div>                            
                </div>
              </div>
            </div>
             <?php } ?>
             <?php $fluorescenceOptions = (array) $loadfilter['fluorescenceRange']; ?>
             <?php if($fluorescenceOptions){?>
            <div class="polish-depth shape-bg filter-Fluoroscence rb_fluoroscence_filter">
            <h4 class="filter-title"><?php echo 'Fluorescence';?>
              <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('fluorescence');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
            </h4>
              <div class="cut-main">
                <div id="fluorescence-r-slider" class="fluorescence-slider right-block ui-slider">
                  <?php 
                  foreach ($fluorescenceOptions as $optionsValues) : 
                  $options = (array) $optionsValues;
                  $fluorescences_attr[]  = array('fluorescenceName' => $options['fluorescenceName'], 'fluorescenceId' => $options['fluorescenceId']);
                  if( !next( $fluorescenceOptions ) ) {
                    $fluorescences_attr[]  = array('fluorescenceName' => 'Last', 'fluorescenceId' => '000');
                  }
                  $fluorescences_Idattr[]  = $options['fluorescenceId'];
                  endforeach; 
                  if (isset($savedfilter->FluorescenceList)) {
                    $fluoreList = $savedfilter->FluorescenceList;
                  }
                  else
                  {
                    $fluoreList = "";
                  }
                  if (isset($savedfilter->FluorescenceStart)) {
                    $fluoreStart = $savedfilter->FluorescenceStart;
                  }
                  else
                  {
                    $fluoreStart = $slider_start;
                  }
                  if (isset($savedfilter->FluorescenceStop)) {
                    $fluoreStop = $savedfilter->FluorescenceStop;
                  }
                  else
                  {
                    $fluoreStop = count($fluorescences_attr);
                  }
                  ?>
                  <input type="hidden" class="diamond_fluorescence range_<?php echo $fluoreStop-1;?>" name="diamond_fluorescence" value="<?php echo $fluoreList;?>" data-start="<?php echo $fluoreStart;?>" data-stop="<?php echo $fluoreStop;?>"/>
                  <div id="fluorescence-slider" class="input-assumpte" data-steps="<?php echo count($fluorescences_attr);?>"></div>                            
                </div>
              </div>
            </div>
            <?php } ?>
         </div>
        
         <div class="filter-advanced-main advance-right">
          <?php $symmetryOptions = (array) $loadfilter['symmetryRange']; ?>
            <?php if($symmetryOptions){?>
            <div class="polish-depth shape-bg">
           <h4 class="filter-title"><?php echo 'Symmetry';?>
             <?php if($show_filter_info == 'true') {?><span class="show-filter-info" onclick="showfilterinfo('symmetry');"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php } ?>
           </h4>
           <div class="cut-main">
              <div id="symmetry-r-slider" class="symmetry-slider right-block ui-slider">
                <?php 
                foreach ($symmetryOptions as $optionsValues) : 
                $options = (array) $optionsValues;
                $symmetrys_attr[]  = array('symmetryName' => $options['symmteryName'], 'symmetryId' => $options['symmetryId']);
                if( !next( $symmetryOptions ) ) {
                  $symmetrys_attr[]  = array('symmetryName' => 'Last', 'symmetryId' => '000');
                }
                $symmetrys_Idattr[]  = $options['symmetryId'];
               endforeach; 
                if (isset($savedfilter->SymmetryList)) {
                  $symmetryGradeList = $savedfilter->SymmetryList;
                }
                else
                {
                  $symmetryGradeList = "";
                }
                if (isset($savedfilter->SymmetryStart)) {
                  $symmetryStart = $savedfilter->SymmetryStart;
                }
                else
                {
                  $symmetryStart = $slider_start;
                }
                if (isset($savedfilter->SymmetryStop)) {
                  $symmetryStop = $savedfilter->SymmetryStop;
                }
                else
                {
                  $symmetryStop = count($symmetrys_attr);
                }
                ?>
                <input type="hidden" class="diamond_symmetry range_<?php echo $symmetryStop-1;?>" name="diamond_symmetry" value="<?php echo $symmetryGradeList;?>" data-start="<?php echo $symmetryStart;?>" data-stop="<?php echo $symmetryStop;?>"/>
                <div id="symmetry-slider" class="input-assumpte" data-steps="<?php echo count($symmetrys_attr);?>"></div>                            
              </div>
            </div>
            </div>
            <?php } ?>  
            <?php $certificatesOptions = (array) $loadfilter['certificateRange']; ?>
            
            <div class="certificate-div eq_wrapper">
        <?php 
        if($jc_options['jc_options']->show_Certificate_in_Diamond_Search) {
        if($certificatesOptions){
        ?>
          <div class="shape-bg">
              <h4 class="filter-title"><?php echo 'Certificates';?></h4>
               <select name="diamond_certificates[]" multiple="multiple" id="certi-dropdown" placeholder="Certificates" class="testSelAll">
                  <?php $certificatesOptions = (array) $loadfilter['certificateRange'];
                        foreach ($certificatesOptions as $value) {
                           $certificatevalue = (array) $value; 
                           if($certificatevalue['certificateName'] != 'Show All Cerificate' && $certificatevalue['certificateName'] != ''){ ?>
                           <?php if($activeFilter == "navstandard" && $certificatevalue['certificateName'] != 'GCAL') {  ?>
                           <option value="<?php echo str_replace(' ', '%20', $certificatevalue['certificateName'])?>" class="navstandard_gcal" <?php echo (in_array(str_replace(' ', '_', $certificatevalue['certificateName']), $certiArray)) ? 'selected="selected"' : ''?>><?php echo $certificatevalue['certificateName']?></option>   
                         <?php } if($activeFilter == "navfancycolored" && $certificatevalue['certificateName'] != 'GCAL') {  ?>
                           <option value="<?php echo str_replace(' ', '%20', $certificatevalue['certificateName'])?>" class="navfancycolored_gcal" <?php echo (in_array($certificatevalue['certificateName'], $certiArray)) ? 'selected="selected"' : ''?>><?php echo $certificatevalue['certificateName']?></option>   
                           <?php } if($activeFilter == "navlabgrown") { ?>
                           <option value="<?php echo str_replace(' ', '%20', $certificatevalue['certificateName'])?>" class="navlabgrown_certificate" <?php echo (in_array($certificatevalue['certificateName'], $certiArray)) ? 'selected="selected"' : ''?>><?php echo $certificatevalue['certificateName']?></option>   
                       <?php } } } ?>
               </select>
          </div>
                <?php 
        }
          }
        ?>

       <div class="shape-bg" style="display:none;"></div>
            </div>

         </div>
       </div>
      </div>
   </div>
</div>
<?php include("range_pipe_slider.php"); ?>
<script type="text/javascript">  
  jQuery(document).ready(function(){
   jQuery('#gemfind_diamond_origin').SumoSelect({ 
                    forceCustomRendering: true,
                    triggerChangeCombined:false
                });
   jQuery('#certi-dropdown').SumoSelect({
                        csvDispCount: 2, 
                        okCancelInMulti: false,
                        selectAll: true,
                        forceCustomRendering: true,
                        triggerChangeCombined:false,
                        captionFormatAllSelected:'Show All Certificates'
                    });   
   })
</script>
<?php } else {
echo 'Something went wrong, please try after some time!';  
} ?>