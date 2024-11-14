<?php
error_reporting(E_ALL);
$this->load->library('ringbuilder_lib');

$list = $this->ringbuilder_lib->getRings($filter_data);


$noimageurl = base_url() . "assets/images/no-image.jpg";
$loaderimg = base_url() . "assets/images/loader-2.gif";

$siteurl = base_url();
$shopurl = $filter_data['shopurl'];

$access_token = $this->ringbuilder_lib->getShopAccessToken($shopurl);
$pathprefixshop = $filter_data['path_prefix_shop'];
$data = $this->general_model->getDiamondConfig($shopurl);
// echo "<pre>";
// print_r($data);
$base_shop_domain = actual_shop_address($access_token, $shopurl, $pathprefixshop);


$resultperpageoptions = $this->ringbuilder_lib->getResultsPerPageOptionsRings();
function isMobile()
{
  return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
?>
<span style="display:none;" class="test12">
  <?php
  $metalType = $filter_data["ring_metal"];
  if ($metalType == "") {
    $metalTypes = json_decode(json_encode($list['data'][0]), true);
    $metalType = $metalTypes["metalTypes"][0]["metalType"];
  }
  ?>
</span>
<div class="search-details no-padding" id="scrollPage">
  <div class="searching-result">
    <div class="number-of-search">
      <!-- <p><strong><?php //echo number_format($list['pagination']['total']); ?></strong><?php //echo 'Settings'; ?></p> -->
    </div>
    <div class="view-or-search-result">
      <div class="change-view-result">
        <select class="pagesize" id="pagesize" name="pagesize" onchange="ItemPerPage(this)">
          <?php foreach ($resultperpageoptions as $value) { ?>
            <option value="<?php echo $value['value'] ?>"><?php echo $value['label'] ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="grid-view-sort">
        <select name="gridview-orderby" id="gridview-orderby" class="gridview-orderby" onchange="gridSort(this)">
          <option value="cost-l-h"><?php echo 'Price: Low - High'; ?></option>
          <option value="cost-h-l"><?php echo 'Price: High - Low'; ?></option>
        </select>
      </div>
      <div class="change-view">
        <ul>
          <li class="grid-view" data-placement="top" data-toggle="tooltip" title="Grid view 3 columns"> <a href="javascript:;" id="gridmodenarrow" class="gridmodenarrow"><?php echo 'Grid view 3 column'; ?></a> </li>
          <li class="grid-view-wide" data-placement="top" data-toggle="tooltip" title="Grid view 4 columns"> <a href="javascript:;" id="gridmodewidecol" class="active gridmodewidecol"><?php echo 'Grid view 4 column'; ?></a> </li>
        </ul>
      </div>
      <div class="search-in-table" id="searchintable">
        <input type="text" name="searchdidfield" id="searchdidfield" placeholder="<?php echo 'Search Setting#'; ?>"><a href="javascript:;" title="close" id="resetsearchdata">X</a><button id="searchsettingid" title="Search Setting"></button>
      </div>
    </div>
  </div>
</div>
</div>
<?php if (isset($list['pagination']['total']) && $list['pagination']['total'] != 0) : ?>
  <div class="search-details no-padding">
    <div class="search-view-grid gridmodewide" id="grid-mode">
      <div class="grid-product-listing">
        <?php foreach ($list['data'] as $result) :

        ?>
          <?php $diamondviewurl = ''; ?>

          <div class="search-product-grid" id="<?php echo $result->settingId; ?>">
            <?php if ($result->showVideo == true) { ?>
              <a href="javascript:;" class="triggerVideo" data-id="<?php echo $result->settingId; ?>" onclick="showModal()"> <i class="fa fa-video-camera"> </i> </a>
            <?php } ?>
            <a href="<?php echo $this->ringbuilder_lib->getRingViewUrl($result->priceSettingId, $result->name, $base_shop_domain, $pathprefixshop, $metalType); ?>" id="ringurlid-<?php echo $result->settingId ?>" class="ringautourl" onclick="SetBackValue('<?php echo $result->priceSettingId ?>','<?php //echo $this->ringbuilder_lib->getRingViewUrl($result->settingId) 
                                                                                                                                                                                                                                                                                                        ?>');" title="<?php echo 'View Ring'; ?>">
              <div class="product-images">
                <span class="imagecheck" data-src="<?php echo $result->imageUrl; ?>" data-srcbig="<?php echo $result->imageUrl; ?>" data-id="<?php echo $result->settingId; ?>"></span>
                <img class="main-setting-img" src="<?php echo $loaderimg; ?>" alt="<?php echo $result->name; ?>" title="<?php echo $result->name; ?>" />
                <?php
                /*$ch = curl_init($result->videoURL);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch); 
                if($httpcode>=200 && $httpcode<300){
                    $status = 200;
                } else {
                    $status = 404;
                }*/
                ?>
                <?php if ($result->videoURL && !isMobile()) { ?>
                  <img class="main-setting-video" style="display: none;" src="<?php echo $loaderimg; ?>" alt="<?php echo $result->name; ?>" title="<?php echo $result->name; ?>" />
                  <video class="ring_video" width="" height="" autoplay="" loop="" muted="" playsinline="" style="display: none;">
                    <source src="<?php //echo $result->videoURL; 
                                  ?>" type="video/mp4">
                  </video>
                <?php } ?>
              </div>
              <div class="product-details">
                <div class="product-item-name">
                  <span><strong><?php echo $result->name ?></strong></span>
                </div>
                <div class="product-box-pricing">
                  <span>
                    <?php
                        $rprice = $result->cost;
                        $rprice = str_replace(',', '', $rprice);
                    ?>
                    <?php if ($result->showPrice) {
                          if($data->price_row_format == 'left'){
                            if($result->currencyFrom == 'USD'){

                              echo "$".number_format($rprice); 

                            }else{

                              echo number_format($rprice).' '.$result->currencySymbol.' '.$result->currencyFrom;

                            }
                          }else{

                            if($result->currencyFrom == 'USD'){

                              echo "$".number_format($rprice); 

                            }else{

                              echo $result->currencyFrom.' '.$result->currencySymbol.' '.number_format($rprice);   

                            }

                        }
                      
                    } else {
                      echo 'Call For Price';
                    } ?>
                  </span>
                </div>
              </div>
              <?php 
                $config_data = $this->general_model->getDiamondConfig($shopurl);
                $charge_details = $this->general_model->getAppDetails($shopurl);
                //If enabled in Admin Setting then only show
                if(isset($config_data->display_tryon) && $config_data->display_tryon == 1  && $charge_details->plan == 'Gemfind Tryon Plan'){ 
                $sku_first = current(array_slice(explode("-", $result->stockNumber), 0, 1));
                ?>

                <a title="Tryon" href="https://cdn.camweara.com/gemfind/index_client.php?company_name=Gemfind&ringbuilder=1&skus=<?php echo $sku_first; ?>&buynow=0" class="tryonbtn fancybox fancybox.iframe" data-fancybox-type="iframe" id="tryon">Virtual Try On</a>
              <?php }?>
            </a>
            <input type="hidden" name="diamondimage" id="diamondimage-<?php echo $result->settingId; ?>" value="" />
            <input type="hidden" name="diamondprice" id="diamondprice-<?php echo $result->settingId; ?>" value="<?php echo number_format($result->cost); ?>" />
          </div>

        <?php endforeach; ?>
      </div>
    </div>
    <div class="grid-paginatin" style="text-align:center;">
      <?php $current = 1;
      $number = $list['perpage'];
      $pages = ceil($list['pagination']['total'] / $number);
      if ($list['pagination']['currentpage'] > 1) {
        $current = $list['pagination']['currentpage'];
      }
      if ($current - 1 == 0) {
        $value = 1;
      } else {
        $value = $current - 1;
      }
      ?>
      <div class="pagination-div pagination_scroll">
        <input type="hidden" name="tool_version" value="Version 2.6.0">
        <ul>
          <li class="grid-next-double">
            <a href="javascript:void(0);" onclick="PagerClick('1');"></a>
          </li>
          <li data-placement="top" data-toggle="tooltip" title="Previous" <?= ($current == 1) ? 'class="disabled grid-next"' : 'class="grid-next"' ?>>
            <a href="javascript:void(0);" <?php if (($current - 1) != 0) { ?> onclick="PagerClick('<?php echo ($value) ?>');" <?php } ?>><?php echo ($value) ?></a>
          </li>
          <?php for ($i = 1; $i <= $pages; $i++) {
            if ($i <> $current) {
              if ($i >= $current + 3) {
                continue;
              }
              if ($i <= $current - 3) {
                continue;
              }
          ?>
              <li>
                <a href="javascript:void(0);" onclick="PagerClick('<?php echo $i ?>');"><?php echo $i; ?></a>
              </li>
            <?php } else { ?>
              <li class="active">
                <a href="javascript:void(0);" class="active" onclick="PagerClick('<?php echo $i ?>');"><?php echo $i; ?></a>
              </li>
          <?php }
          } ?>
          <li data-placement="top" data-toggle="tooltip" title="Next" <?= ($current == $pages) ? 'class="disabled grid-previous"' : 'class="grid-previous"' ?>>
            <a href="javascript:void(0);" <?php if ($current != $pages) { ?> onclick="PagerClick('<?php echo ($current + 1); ?>');" <?php } ?>><?php echo ($current + 1); ?></a>
          </li>
          <li class="grid-previous-double">
            <a href="javascript:void(0);" onclick="PagerClick('<?php echo $pages; ?>');"></a>
          </li>
        </ul>
      </div>
      <?php
      if ($current == 1) {
        $from = 1;
        $to = $number;
      } else {
        $from = (($current - 1) * $number) + 1;
        $to = ($current * $number);
      }

      if ($list['pagination']['total'] < $to) {
        $to = $list['pagination']['total'];
      }

      echo "<div class='page-checked'><div class='result-bottom ring-result'>Results " . number_format($from) . " to " . number_format($to) . " of " . number_format($list['pagination']['total']) . " </div></div> ";
      ?>
    </div>
  </div>
  <?php /*foreach ($list['data'] as $result) : ?>
            <?php $diamondviewurltes[] = $block->getRingViewUrl($result->settingId); ?>
<?php endforeach;*/ ?>
<?php else : ?>
  <div class="search-details no-padding no-result-main">
    <div class="searching-result no-result-div">
      <?php echo 'No Data Found.'; ?>
    </div>
  </div>
<?php endif; ?>

<div id="myRbModal" class="Rbmodal">
  <!-- Modal content -->
  <div class="Rbmodal-content">
    <span class="Rbclose">&times;</span>
    <div class="loader_rb" style="display: none;">
      <img src="<?php echo base_url('assets/images/ring.gif') ?>" style="width: 350px; height: 300px;">
    </div>
    <iframe src="" id="iframevideo" frameBorder="0" allowtransparency="true" scrolling="no" style="width:100%; height:90%;" allow="autoplay"> 
    </iframe>
    <video width="100%" height="90%" id="mp4video" loop autoplay>
            <source src=""  type="video/mp4">
    </video>
  </div>
</div>

<script type="text/javascript">
  $("span.imagecheck").each(function() {
    var id = $(this).attr("data-id");
    var src = $(this).attr("data-src");
    imageExists(src, function(exists) {
      if (exists) {
        $('tr#' + id + ' td img').attr('src', src);
        $('div#' + id + ' div.product-images > img.main-setting-img').attr('src', src);
        $('input#diamondimage-' + id).val(src);
      } else {
        $('tr#' + id + ' td img').attr('src', '<?php echo $noimageurl ?>');
        $('div#' + id + ' div.product-images > img.main-setting-img').attr('src', '<?php echo $noimageurl ?>');
        $('input#diamondimage-' + id).val('<?php echo $noimageurl ?>');
      }
    });
  });

  function imageExists(url, callback) {
    var img = new Image();
    img.onload = function() {
      callback(true);
    };
    img.onerror = function() {
      callback(false);
    };
    img.src = url;
  }
  $('.change-view ul li a').click(function() {
    console.log('click');
    $('.change-view ul li a').removeClass('active');
    $(this).addClass('active');
    if ($(this).hasClass('gridmodenarrow')) {
      $('#grid-mode').removeClass('gridmodewide');
      $('#grid-mode').addClass('gridmode');
    } else {
      $('#grid-mode').addClass('gridmodewide');
      $('#grid-mode').removeClass('gridmode');
    }
  });
  // $('.search-product-grid').each(function(num,val){
  //   var divid = $(this).attr('id');
  //   var videosrc = $('#'+divid+' div.product-images video source').attr('src');

  //     $.ajax({
  //       url: $('#search-rings-form #baseurl').val() + 'ringbuilder/settings/checkvideo/',
  //       data: {setting_video_url: videosrc},
  //       type: 'POST',
  //       beforeSend: function(settings) {

  //       },
  //       //dataType: 'json',
  //       cache: true,
  //       success: function(response) {
  //           if(response == 1){
  //             $('#'+divid+' div.product-images > img.main-setting-video').remove();
  //           }else{
  //             $('#'+divid+' div.product-images > img.main-setting-video').remove();
  //             $('#'+divid+' div.product-images video').remove();
  //           }
  //       }
  //   });
  // });
  // $(".search-product-grid").mouseenter(function(){
  //   if($( window ).width() > 767){
  //     var divid = $(this).attr('id');
  //     $('#'+divid+' div.product-images > img.main-setting-video').css('display','block');
  //     $('#'+divid+' div.product-images > .imagecheck').css('display','block');

  //     if($('#'+divid+' div.product-images video').length){
  //       $('#'+divid+' div.product-images > img.main-setting-img').css('display','none');
  //       $('#'+divid+' div.product-images video').css('display','block');
  //     }else{
  //       $('#'+divid+' div.product-images > img.main-setting-img').css('display','block');
  //     }
  //   }
  // });
  // $(".search-product-grid").mouseleave(function(){
  //   if($( window ).width() > 767){
  //     var divid = $(this).attr('id');
  //     $('#'+divid+' div.product-images > img.main-setting-video').css('display','none');
  //     $('#'+divid+' div.product-images > .imagecheck').css('display','none');
  //     if($('#'+divid+' div.product-images video').length){
  //       $('#'+divid+' div.product-images > img.main-setting-img').css('display','block');
  //       $('#'+divid+' div.product-images video').css('display','none');
  //     }
  //   }
  // });

  $(document).ready(function() {
    // $('[data-toggle="tooltip"]').tooltip();
    jQuery('[data-toggle="tooltip"]').tooltip({
      trigger: "hover"
    });
  });

  function showModal() {
        jQuery("#iframevideo").removeAttr("src");
        jQuery("#mp4video").removeAttr("src");
        jQuery("#mp4video").attr("src", '');
        jQuery('#myRbModal').modal('show');
        jQuery('.loader_rb').show();
        var divid = jQuery(event.currentTarget).data('id');

        jQuery.ajax({
            type: "POST",
            url  : "<?php echo base_url(); ?>/ringbuilder/settings/getringvideos",
            data: {
                action: 'getringvideos',
                product_id: divid
            },
            cache: true,
            success: function(response) {
                response = JSON.parse(response);
                console.log(response);
                if (response.showVideo == true) {
                    var fileExtension = response.videoURL.replace(/^.*\./, '');
                    console.log (fileExtension);
                    if(fileExtension=="mp4"){
                        jQuery('#iframevideo').hide();
                        setTimeout(function() {
                           jQuery("#mp4video").attr("src", response.videoURL);
                           jQuery('.loader_rb').hide();
                           jQuery('#mp4video').get(0).play();
                        }, 3000);
                    }
                    else{
                        //jQuery('#mp4video').hide();
                        setTimeout(function() {
                            jQuery("#iframevideo").attr("source", response.videoURL);
                            jQuery('.loader_rb').hide();
                            jQuery('#iframevideo').show();
                        }, 3000);
                    }
                }   
            }         
        });
    }
  jQuery(".Rbclose").click(function() {
    jQuery('#myRbModal').modal('hide');
  });

  jQuery('.pagination_scroll').click(
    function(e) {
      jQuery('html, body').animate({
        scrollTop: jQuery('#scrollPage').position().top
      }, 800);
    });
  //For Tryon button
jQuery(document).ready(function ($) {

    $("a.tryonbtn").fancybox({
        type: "iframe",
        iframe: {
            // Iframe template
            tpl: '<iframe id="tryoniFrameID" allowfullscreen class="fancybox-iframe" scrolling="auto" width="1200" height="800" allow="camera"></iframe>'
        }
    });
    
    //when click on close
    window.addEventListener('message', function(event) {
        
        if (~event.origin.indexOf('https://cdn.camweara.com')) { 
            
            if(event.data == "closeIframe"){ //Close
                
                //var iframe = document.getElementById("iFrameID"); 
                //iframe.contentWindow.location.replace("");
                //iframe.style.display = "none";
 
                $.fancybox.close();
                
            } else if(event.data.includes("buynow")){
                
                $('#product_addtocart_form').submit();
            }
        }
    });
});
//For Tryon button
jQuery(document).ready(function ($) {

    $("a.tryonbtn").fancybox({
        type: "iframe",
        iframe: {
            // Iframe template
            tpl: '<iframe id="tryoniFrameID" allowfullscreen class="fancybox-iframe" scrolling="auto" width="1200" height="800" allow="camera"></iframe>'
        }
    });
    
    //when click on close
    window.addEventListener('message', function(event) {
        
        if (~event.origin.indexOf('https://cdn.camweara.com')) { 
            
            if(event.data == "closeIframe"){ //Close
                
                //var iframe = document.getElementById("iFrameID"); 
                //iframe.contentWindow.location.replace("");
                //iframe.style.display = "none";
 
                $.fancybox.close();
                
            } else if(event.data.includes("buynow")){
                
                $('#product_addtocart_form').submit();
            }
        }
    });
});
</script>