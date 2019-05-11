<?php
include(MARI_VIEW_PATH.'/Common_select_class.php');

require './vendor/autoload.php';
use phpFastCache\CacheManager;
// Setup File Path on your config files
CacheManager::setup(array(
    "path" => sys_get_temp_dir(), // or in windows "C:/tmp/"
));
CacheManager::CachingMethod("phpfastcache");
$InstanceCache = CacheManager::Files();

$sql="
  select
  	a.i_id, a.i_subject, b.i_invest_sday, a.i_year_plus, a.i_loan_day
  	, if( a.i_repay = '원리금균등상환' , '원금균등상환',  a.i_repay) as i_repay
  	, a.i_loan_pay
    , a.i_security   , b.i_creditratingviews as mainpost, b.i_look,i_payment
  	, date_format(b.i_invest_sday,'%Y/%m/%d') as i_mainimg_txt1_date
  	, b.i_mainimg_txt1, b.i_mainimg_txt2, b.i_mainimg_txt3, b.i_mainimg_txt4, b.i_mainimg_txt5, b.i_mainimg_txt6
  	, (select  ifnull( sum( mari_invest.i_pay ), 0) from mari_invest where mari_invest.loan_id = a.i_id ) as payed

  from mari_loan a
  join mari_invest_progress b on a.i_id = b.loan_id
  where a.i_view='Y'
    and ( a.i_look in ( 'N','Y') or b.i_look in ( 'N','Y'))
  order by  b.i_invest_sday desc
  # limit 3;
";
$result = sql_query($sql, false);
$z_loaninfo_slide = array();
for ($i=0; $row=sql_fetch_array($result); $i++) {
  if( $row != false ) {
    $row['percent'] = ($row['payed'] == 0 ) ? '0' : floor($row['payed'] / $row['i_loan_pay']*100);
    $row['type'] = ($row['i_payment']=="cate02"||$row['i_payment']=="cate04") ? "부동산":"비부동산";
    $z_loaninfo_slide[] = $row;
  }
}

if( count($z_loaninfo_slide) < 1 ){
  $sql = "
  select
    a.i_id, a.i_subject, b.i_invest_sday, a.i_year_plus, a.i_loan_day
    , if( a.i_repay = '원리금균등상환' , '원금균등상환',  a.i_repay) as i_repay
    , a.i_loan_pay
    , a.i_security  , b.i_creditratingviews as mainpost, a.i_look,i_payment
    , date_format(b.i_invest_sday,'%Y/%m/%d') as i_mainimg_txt1_date
    , b.i_mainimg_txt1, b.i_mainimg_txt2, b.i_mainimg_txt3, b.i_mainimg_txt4, b.i_mainimg_txt5, b.i_mainimg_txt6
    , (select  ifnull( sum( mari_invest.i_pay ), 0) from mari_invest where mari_invest.loan_id = a.i_id ) as payed

  from mari_loan a
  join mari_invest_progress b on a.i_id = b.loan_id
  where a.i_view='Y'  and b.i_look not in ( 'N','Y')
  order by  b.i_invest_sday desc
  limit 3;
  ";
  $result = sql_query($sql, false);
  for ($i=count(z_loaninfo_slide); $row=sql_fetch_array($result); $i++) {
    if( $row != false ) {
      $row['percent'] = ($row['payed'] == 0 ) ? '0' : floor($row['payed'] / $row['i_loan_pay']*100);
      $row['type'] = ($row['i_payment']=="cate02"||$row['i_payment']=="cate04") ? "부동산":"비부동산";
      $z_loaninfo_slide[] = $row;
    }
    //if ($i >= 2) break;
  }
}
$perpage = 6;
$searchsql= '';
if(get_magic_quotes_gpc()) {
$search = mysql_real_escape_string(stripslashes($_GET['search']));
}
else $search = mysql_real_escape_string($_GET['search']);

if($search != ''){
  $searchsql = " and a.i_subject like '%".$search."%' ";
}
$nowpage = ($_GET['page'] > 1 ) ? $_GET['page']: 1;
$sql = "select count(1) as total from mari_loan a where a.i_view='Y'  $searchsql";
$totaltmp =  sql_fetch($sql);

$totalnum = ( isset( $totaltmp['total']) ) ? (int)$totaltmp['total'] : 0;
$totalpage = (int)( $totalnum / $perpage )+ ($totalnum % $perpage > 0 ? 1 : 0) ;

$start = ($_GET['page'] > 1 ) ? ((int)$_GET['page'] -1)* $perpage : 0;

$sql = "
select
  a.i_id, a.i_subject, b.i_invest_sday, a.i_year_plus, a.i_loan_day
  , if( a.i_repay = '원리금균등상환' , '원금균등상환',  a.i_repay) as i_repay
  , a.i_loan_pay
  , a.i_security
  , b.i_creditratingviews as mainpost
  , a.i_look,i_payment
  , date_format(b.i_invest_sday,'%Y/%m/%d') as i_mainimg_txt1_date
  , b.i_mainimg_txt1, b.i_mainimg_txt2, b.i_mainimg_txt3, b.i_mainimg_txt4, b.i_mainimg_txt5, b.i_mainimg_txt6
  , (select  ifnull( sum( mari_invest.i_pay ), 0) from mari_invest where mari_invest.loan_id = a.i_id ) as payed

from mari_loan a
join mari_invest_progress b on a.i_id = b.loan_id
where a.i_view='Y'
$searchsql
order by  b.i_invest_sday desc
limit $start , $perpage;
";

$result = sql_query($sql, false);
?>
{# new_header}
<link rel="stylesheet" href="js/owl/assets/owl.carousel.min.css">
<link rel="stylesheet" href="js/owl/assets/owl.theme.default.min.css">
<link rel="stylesheet" href="/pnpinvest/layouts/home/pnpinvest/simplomodal/learn.css" type="text/css" media="screen" title="no title" charset="utf-8">
<script src="js/owl/owl.carousel.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.5.1/css/iziModal.min.css" >
<script src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.5.1/js/iziModal.min.js" type="text/javascript"></script>

<!-- /////////////////////////////// 본문 시작 /////////////////////////////// -->
<style>
.product.gallery .item .item_con {
    padding: 8px 14px;
    width: auto;
    height: auto;
    line-height: unset;
}
</style>
<div id="container" class="sub">
	<!-- Sub title -->
	<h2 class="subtitle t1 invest_list">
      <span class="motion" data-animation="flash">투자하기

      </span>
        <span class="triggeriziModal hvr-buzz-out" href="/api/index.php/consulting" style="font-size: 15px;">법인 / 전문 투자상담</span>
<style>
.subtitle {overflow: hidden;}
.motion {position:relative;}
.triggeriziModal{
  position: absolute;
color: white;
border: 1px solid #FFF;
border-radius: 5px;
text-transform: uppercase;
letter-spacing: 0;
will-change: box-shadow, transform;
transition: box-shadow 0.2s cubic-bezier(0.4, 0, 1, 1), background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
padding: 7px 8px;

margin-left:283px;
font-weight: 400;
text-align: center;
white-space: nowrap;
touch-action: manipulation;
cursor: pointer;
user-select: none;
box-shadow: 0 2px 2px 0 rgba(153, 153, 153, 0.14), 0 3px 1px -2px rgba(153, 153, 153, 0.2), 0 1px 5px 0 rgba(153, 153, 153, 0.12);

}
@media all and (max-width:1120px) {
.motion {position:relative; margin-right:330px; margin-left:330px;}
.subtitle {font-size: 24px;}
.triggeriziModal{position: relative;margin-left: 290px;margin-right:290px;margin-top:15px;padding: 7px 8px;}
}

@media all and (max-width:1000px) {
.motion {position:relative; margin-right:330px; margin-left:330px;}
.subtitle {font-size: 24px;margin-top:-10px;padding-bottom:20px;}
#container h2.subtitle {margin-top: 0;padding: 100px 0 100px 0;}
.subtitle {overflow: none;}
.subtitle.invest_list {margin-bottom:0px;}
.triggeriziModal{position: relative;margin-left: 290px;margin-right:290px;margin-top:15px;padding: 7px 8px;margin-bottom:15px;}
}
@media all and (max-width:800px) {
  .motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
  position: relative;
margin-left: 275px;
margin-right:275px;
margin-top:15px;

}
}
@media all and (max-width:735px) {
  .motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
  position: relative;
margin-left: 240px;
margin-right:240px;
margin-top:15px;

}
}
@media all and (max-width:645px) {
  .motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
  position: relative;
margin-left: 195px;
margin-right:195px;
margin-top:15px;

}
}

@media all and (max-width:575px) {
  .motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
  position: relative;
margin-left: 160px;
margin-right:160px;
margin-top:15px;

}
}
@media all and (max-width:500px) {
  .motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
  position: relative;
margin-left: 125px;
margin-right:125px;
margin-top:15px;

}
}
@media all and (max-width:430px) {
  .motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
  position: relative;
margin-left: 90px;
margin-right:90px;
margin-top:15px;

}
}
@media all and (max-width:400px) {
.motion {position:relative;margin-right:0px; margin-left:0px;}
.subtitle {font-size: 24px;}
.triggeriziModal{
position: relative;
margin-left: 80px;
margin-right:80px;
margin-top:15px;

}
}
</style>


  </h2>
	<!-- 금일 상품 -->
  <div class="invest_top" style="display:none">
    <h3 class="skip">금일 상품</h3>
    <div class="container">
      <!-- slider -->
      <div class="invest_slider owl-carousel owl-theme navbarbox">
  <?php foreach ( $z_loaninfo_slide as $idx=>$row ){?>
        <!-- loop start -->
        <div class="item">
          <p class="timeout  <?php echo (in_array($row['i_look'], array('N'))) ? 'item_time':''; ?>" data-loan_id="<?php echo $row['i_id']?>" data-loan_look="<?php echo $row['i_look']?>" style="display:none" <?php echo (!in_array($row['i_look'], array('N'))) ? 'style="display:none"':''; ?>>
            <i class="clock"></i>
            <span class="txt">이 상품의 투자시작 시간이 <span>....</span></span>
          </p>
          <div class="item_wrap">

            <div class="item_info info1 fl">
                <?php
                  //N 대기, Y 진행중, C 마감, D 이자, F 완료
                    switch( $row['i_look']){
                      case ('N') :
                  ?>
                      <span class="item_con end">투자대기</span>
                  <?php
                      break;
                      case ('Y') :
                  ?>
                      <span class="item_con ing">투자모집</span>
                  <?php
                      break;
                      case ('C') :
                  ?>
                      <span class="item_con end">투자마감</span>
                  <?php
                      break;
                      case ('D') :
                  ?>
                      <span class="item_con end">이자상환</span>
                  <?php
                      break;
                      default:
                  ?>
                      <span class="item_con end">상환완료</span>
                  <?php
                      break;
                    }
                  ?>
              <p class="img_wrap" width="428" height="282"><span class="img img_w"><a href="/pnpinvest/?mode=invest_view&loan_id=<?php echo $row['i_id']?>" target="_self"><img src="/pnpinvest/data/photoreviewers/<?php echo $row['i_id']?>/<?php echo $row['mainpost']?>" alt></a></span></p>
              <p class="txt"><span class="date fl"><?php echo $row['i_mainimg_txt1']?></span><span class="time fr"><?php echo $row['i_mainimg_txt2']?></span></p>
            </div>
            <div class="item_info info2 fr">
              <h4>
                <a href="/pnpinvest/?mode=invest_view&loan_id=<?php echo $row['i_id']?>" target="_self">
                <span><?php echo $row['i_subject']?></span>
                </a>
              </h4>
              <ul class="summary fl">
                <li class="sm_1">
                  <dl>
                    <dt>상품분류</dt>
                    <dd><?php echo $row['type']?></dd>
                  </dl>
                </li>
                <li class="sm_2">
                  <dl>
                    <dt>수익률</dt>
                    <dd><?php echo $row['i_year_plus']?>%</dd>
                  </dl>
                </li>
                <li class="sm_3">
                  <dl>
                    <dt>투자기간</dt>
                    <dd><?php echo $row['i_loan_day']?>개월</dd>
                  </dl>
                </li>
                <li class="sm_5">
                  <dl>
                    <dt>모집금액</dt>
                    <dd><?php echo change_pay($row['payed'])?>/<?php echo change_pay($row['i_loan_pay'])?></dd>
                  </dl>
                </li>
              </ul>
              <div class="donut fr">
                <p class="donut_progress">
                  <span class="ib fill" style="height:<?php echo $row['percent']?>%;"></span>
                </p>
                <p class="donut_txt center">
                  <span>모집률</span>
                  <strong><?php echo $row['percent']?>%</strong>
                </p>
              </div>
              <p class="event fr">
                <span class="ib center event1"><?php echo $row['i_mainimg_txt3']?> <?php echo $row['i_mainimg_txt4']?></span>
                <span class="ib center event2"><?php echo $row['i_mainimg_txt5']?><?php echo ($row['i_mainimg_txt6']!='')?'/'.$row['i_mainimg_txt6']:''?></span>
              </p>
            </div>
          </div>
        </div>
        <!-- loop end -->
  <?php } ?>
      </div>
      <script>
      //$('.invest_slider').bxSlider({
      //	auto: false,
      //	autoControls: true,
      //	stopAutoOnClick: false,
      //	pager: true,
      //	speed: 1000
      //});
      </script>
      <style>
      button:focus {outline:0;}
      .owl-carousel.navbarbox .owl-nav button.owl-next, .owl-carousel.navbarbox .owl-nav button.owl-prev, .owl-carousel.navbarbox  button.owl-dot {
          width: 38px;
      }
      .owl-carousel.navbarbox .owl-nav button:hover{
        box-shadow:none;
      }
      .owl-carousel.navbarbox .owl-prev {
          width: 15px;
          height: 100px;
          position: absolute;
          top: 35%;
          margin-left: -10px;
          display: block!IMPORTANT;
          border:0px solid black;
      }

      .owl-carousel.navbarbox .owl-next {
          width: 15px;
          height: 100px;
          position: absolute;
          top: 35%;
          right: 0px;
          display: block!IMPORTANT;
          border:0px solid black;
          margin-right: -10px;
      }
      .owl-carousel.navbarbox .owl-prev i, .owl-carousel.navbarbox .owl-next i {transform : scale(1,6); color: #ccc;}
      .owl-carousel.navbarbox .owl-dots{
        position: absolute;
            bottom: 7px;
            width: 100%;
        }
        .owl-carousel.navbarbox .owl-dots .owl-dot span {background-color:#999 }
        .owl-carousel.navbarbox .owl-dots .owl-dot.active span {background-color:#00656a }
        .detail_con.product_info .viewarea {
            min-height: 220px;
          }
        .owl-carousel .single{ transform: translate3d(0px,0px, 0px) !important; }
      </style>
      <script>
      function callbackOwl(event) {
        if (parseInt($(event.target).find('div.item').length) <= 1) {
          ;
          event.relatedTarget.options.loop = false;
        }
      }

      $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
          items:1,
          margin:10,
          autoHeight:true,
          loop : true,
          autoplay:true,
          autoplayTimeout:7000,
          autoplayHoverPause:true,
          center:true,
          itemsScaleUp:true,
          nav: true,
          navText: ["<img src='js/owl/assets/left2.png' style='width: 38px;'>","<img src='js/owl/assets/right2.png' style='width: 38px;'>"],
          onInitialize: callbackOwl,
        });
      });
      </script>
    </div>
  </div>



	<!-- search -->
	<div class="search">
		<div class="container">
			<form action="/pnpinvest/" method="get" name="form1">
  <input type="hidden" name="mode" value="invest">
				<h3 class="skip">검색창</h3>
        <div style="position: absolute;top: 0;right: 220px;display: inline-block">
          <select name="prc">
            <option value="">전체</option>
            <option value="N">투자대기</option>
            <option value="Y">투자중</option>
            <option value="D">이자상환</option>
            <option value="F">상환완료</option>
          </select>
        </div>
				<div class="sch_wrap">
					<span class="sch_box"><input type="text" name="search" id="" title="검색어 입력" value="<?php echo ($_GET['search']!='')? $_GET['search'] : "투자상품 검색"?>" onfocus="if(this.value=='투자상품 검색')this.value=''" onblur="if(this.value=='')this.value='투자상품 검색'"></span>
					<button type="submit" name="" id="" class="btn_sch"><img src="img/btn_sch.png" alt="검색"></button>
				</div>
			</form>
		</div>
	</div>
	<!-- 지난상품 리스트 -->
	<div class="invest_all">
		<div class="container">
			<ul class="product gallery">
<?php for ($i=0; $row=sql_fetch_array($result); $i++) {

  if( $row != false ) {
    $row['percent'] = ($row['payed'] == 0 ) ? '0' : floor($row['payed'] / $row['i_loan_pay']*100);
    $row['type'] = ($row['i_payment']=="cate02"||$row['i_payment']=="cate04") ? "부동산":"비부동산";
?>
				<!-- Item Start -->
				<li>
					<div class="item">

						<div class="item_info info1 fl">
              <?php
              //N 대기, Y 진행중, C 마감, D 이자, F 완료
                switch( $row['i_look']){
                  case ('N') :
              ?>
                  <span class="item_con end">투자대기</span>
              <?php
                  break;
                  case ('Y') :
              ?>
                  <span class="item_con ing">투자모집</span>
              <?php
                  break;
                  case ('C') :
              ?>
                  <span class="item_con end">투자마감</span>
              <?php
                  break;
                  case ('D') :
              ?>
                  <span class="item_con end">이자상환</span>
              <?php
                  break;
                  default:
              ?>
                  <span class="item_con end">상환완료</span>
              <?php
                  break;
                }
              ?>
							<div class="img_wrap">
								<p class="img">
                  <a class="item_name" href="/pnpinvest/?mode=invest_view&loan_id=<?php echo $row['i_id']?>">
									   <img src="/pnpinvest/data/photoreviewers/<?php echo $row['i_id']?>/<?php echo $row['mainpost']?>" alt>
                  </a>
								</p>
							</div>
							<p class="txt"><span class="date fl"><?php echo $row['i_mainimg_txt1']?></span><span class="time fr"><?php echo $row['i_mainimg_txt2']?></span></p>
						</div>
						<div class="item_info info2 fr">
							<h4>
								<a class="item_name" href="/pnpinvest/?mode=invest_view&loan_id=<?php echo $row['i_id']?>">
									<span class="subject"><?php echo $row['i_subject']?></span>
								</a>
							</h4>
							<ul class="summary">
								<li class="sm_1">
									<dl>
										<dt>상품분류</dt>
										<dd><?php echo $row['type']?></dd>
									</dl>
								</li>
								<li class="sm_2">
									<dl>
										<dt>수익률</dt>
										<dd><?php echo $row['i_year_plus']?>%</dd>
									</dl>
								</li>
								<li class="sm_3">
									<dl>
										<dt>투자기간</dt>
										<dd><?php echo $row['i_loan_day']?>개월</dd>
									</dl>
								</li>
								<li class="sm_5">
									<dl>
										<dt>모집금액</dt>
										<dd><?php echo change_pay($row['payed'])?>/<?php echo change_pay($row['i_loan_pay'])?></dd>
									</dl>
								</li>
							</ul>
							<div class="item_progress">
								<span class="ib left">모집률</span>
								<div><p class="progress"><span class="p_bar" style="width:<?php echo $row['percent']?>%;"></span></p><?php echo $row['percent']?>%</div>
							</div>
							<span class="item_event event1"><?php echo $row['i_mainimg_txt3']?> <?php echo $row['i_mainimg_txt4']?></span>
							<span class="item_event event2"><?php echo $row['i_mainimg_txt5']?><?php echo ($row['i_mainimg_txt6']!='')?'/'.$row['i_mainimg_txt6']:''?></span>
						</div>
					</div>
				</li>

				<!-- Item End -->
<?php } } ?>

			</ul>
			<!-- paging -->
      <?php echo paginate($perpage, $nowpage, $totalnum, $totalpage, '/pnpinvest/?mode=invest&search='.$_GET['search']) ?>

		</div>
	</div>
</div>
<div id="izimodal"></div>

<!-- /////////////////////////////// 본문 끝 /////////////////////////////// -->
<script>
$("document").ready( function() {
  $(".item_time").each(function (){
    checkstatus(this);
		$(this).fadeIn("slow");
  });
  $("#izimodal").iziModal({
    iframe: true,
    iframeHeight: 500,
    overlayClose: false,
    overlayColor: 'rgba(0, 0, 0, 0.6)'
  });
  $('.triggeriziModal').on('click', function (event) {
    console.log("modal");
  	$("#izimodal").iziModal('open',event);
  });

});
function CountDown(duration, display,start) {
    if (!isNaN(duration)) {
        var timer = duration, minutes, seconds;

      var interVal=  setInterval(function () {
        var string = "";
        var afterstr="";
        var sec_num = parseInt(timer, 10); // don't forget the second param
        var days = Math.floor(sec_num / 3600/24);

        var hours   = Math.floor((sec_num - days*3600*24)  / 3600);
        var minutes = Math.floor((sec_num - days*3600*24 - (hours * 3600)) / 60);
        var seconds = sec_num - (days*3600*24)- (hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        if( days > 0 ) {string = days +"일 ";}
        if( start ) afterstr = " 남았습니다.";
        else afterstr = " 후 투자마감";
            $(display).children('span').children('span').html(string + hours+':'+minutes+':'+seconds + afterstr);
            if (--timer < 0) {
               if(start) {
                 $(display).html("투자시작");
               }
               else $(display).html("투자마감");
               clearInterval(interVal);
               setTimeout( checkstatus(display) , 1000);
            }
            else if(seconds=="00"){
              clearInterval(interVal);
              checkstatus(display);
              return;
            }
            },1000);
    }
}
function checkstatus(display){
  if($(display).data('loan_id') ){
    $.ajax({
      url:"/api/index.php/timer/now/"+$(display).data('loan_id'),
       type : 'GET',
       data:{'_':new Date().getTime()},
       dataType : 'json',
       success : function(result) {
         if( $(display).data('loan_look') != result.data.look ){
           location.reload();
           return;
         }
         if(result.data.status !='drop' && (result.data.look=='Y'|| result.data.look =='N' ) ) { //||result.data.look =='N' 투자시작용
           if(result.data.status=='end'){
              //CountDown(result.data.e_seconds,display, false);
           }else if (result.data.status=='start'){
              CountDown(result.data.s_seconds,display, true);
           }else if( result.data.status=='ready'){
              $(display).html("<p class='txt'>곧 투자시작가 시작됩니다.</p>");
           }else {
             $(display).remove();
           }
         }else {
           $(display).remove();
         }
       },
       error: function(request, status, error) {
         console.log(request + "/" + status + "/" + error);
       }
    });
  }
}
function reloadnow(display){
  $.ajax({
    url:"/api/index.php/timer/now/"+$(display).data('loan_id'),
     type : 'GET',
     dataType : 'json',
     success : function(result) {
         location.reload();
         return;
     }
   });

}
</script>
{# new_footer}

<?php
function paginate($item_per_page, $current_page, $total_records, $total_pages, $page_url)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<p class="paging paging2">';
          $pagination .= "
          ";

        $right_links    = ($current_page < 3) ? 6 : $current_page + 3;
        $previous       = $current_page - 3; //previous link
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link

        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= '<span class="first"><a href="'.$page_url.'&page=1" title="First">&laquo;</a></span>'; //first link
            $pagination .= "
            ";
            $pagination .= '<span><a href="'.$page_url.'&page='.$previous_link.'" title="Previous">&lt;</a></span>'; //previous link
            $pagination .= "
            ";
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<span><a href="'.$page_url.'&page='.$i.'">'.$i.'</a></span>';
                        $pagination .= "
                        ";
                    }
                }
            $first_link = false; //set first link to false
        }

        if($first_link){ //if current active page is first link
            $pagination .= '<span class="first active on">'.$current_page.'</span>';
            $pagination .= "
            ";
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<span class="last active on">'.$current_page.'</span>';
            $pagination .= "
            ";
        }else{ //regular current link
            $pagination .= '<span class="active on">'.$current_page.'</span>';
            $pagination .= "
            ";
        }

        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<span><a href="'.$page_url.'&page='.$i.'">'.$i.'</a></span>';
                $pagination .= "
                ";
            }
        }
        if($current_page < $total_pages){
                $next_link = ($i > $total_pages)? $total_pages : $i;
                $pagination .= '<span><a href="'.$page_url.'&page='.$next_link.'" >&gt;</a></span>'; //next link
                $pagination .= "
                ";
                $pagination .= '<span class="last"><a href="'.$page_url.'&page='.$total_pages.'" title="Last">&raquo;</a></span>'; //last link
                $pagination .= "
                ";
        }

        $pagination .= '</p>';
    }
    return $pagination; //return pagination links
}
?>
<style>
.invest_top .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 18px;width:140px;height:40px;line-height: 38px;text-align: center;}
.invest_all .folditem {width:150px;}


.product > li {padding:30px 50px 0; margin-bottom:0px;}
.product .item {position:relative; background-color:#fff;border:1px solid #cecece; padding:10px; overflow:hidden; transition:0.2s ease-out;}
.product .info1 .img_wrap {height:100%; width:100%; transition: .3 ease-out;}
.product .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 32px;text-align: center;}
.product .item h4 {margin-top:8px;}
.product .summary {width:98%; line-height: 30px; margin: 30px auto 31px; text-align: left;}
.product .summary .sm_1 {margin-bottom:22px; min-width: 39%;}
.product .summary .sm_3 { min-width: 39%;}
.product .item_progress .ib {width:12%;}
.product .item_progress > div {width:87%;}
.progress {margin-bottom:0px;}


.product.gallery > li {padding:0 0 30px;}
.product.gallery .info1 .img_wrap {height:100%; width:100%; transition: .3 ease-out;}
.product.gallery .item .item_con {position:absolute;z-index: 10;top:5px;right:5px;border-radius: 5px;font-size: 14px;width:90px;height:30px;line-height: 10px;text-align: center;}
.product.gallery .summary {width:130%;overflow: hidden;line-height: 30px; margin: 0 auto 20px; text-align: left;}
.product.gallery .summary > li {display:inline-block;padding-left: 40px;background-position:left center;background-repeat: no-repeat;line-height: 22px;}
.product.gallery .item_progress .ib {width:16%;}
.product.gallery .item_progress > div {display:inline-inline-block;width:82%;}







@media all and (max-width:1010px) {
.product .summary {width:98%; line-height: 30px; margin: 28px auto 28px; text-align: left;}
}
@media all and (max-width:1000px) {
.product .summary {width:98%; line-height: 30px; margin: 26px auto 27px; text-align: left;}
.product.gallery .item .item_con {position:absolute;z-index: 10;top:5px;right:5px;border-radius: 5px;font-size: 16px;width:120px;height:34px;line-height: 14px;text-align: center;}
.product.gallery .item_progress .ib {width:12%;}
.product.gallery .item_progress > div {display:inline-inline-block;width:87%;}
.product.gallery > li {padding:0; margin-bottom:30px;}
}
@media all and (max-width:990px) {
.product .summary {width:98%; line-height: 30px; margin: 25px auto 25px; text-align: left;}
}
@media all and (max-width:980px) {
.product .summary {width:98%; line-height: 30px; margin: 23px auto 23px; text-align: left;}
}
@media all and (max-width:970px) {
.product .summary {width:98%; line-height: 30px; margin: 21px auto 22px; text-align: left;}
}
@media all and (max-width:960px) {
.product .summary {width:98%; line-height: 30px; margin: 20px auto 20px; text-align: left;}
}
@media all and (max-width:950px) {
.product .summary {width:98%; line-height: 30px; margin: 18px auto 18px; text-align: left;}
	.invest_top .item .item_con {position:absolute;z-index: 10;top:5px;right:5px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 32px;text-align: center;}
}
@media all and (max-width:940px) {
.product .summary {width:98%; line-height: 30px; margin: 16px auto 17px; text-align: left;}
}
@media all and (max-width:930px) {
.product .summary {width:98%; line-height: 30px; margin: 15px auto 15px; text-align: left;}
}
@media all and (max-width:920px) {
.product .summary {width:98%; line-height: 30px; margin: 13px auto 14px; text-align: left;}
}
@media all and (max-width:910px) {
.product .summary {width:98%; line-height: 30px; margin: 12px auto 12px; text-align: left;}
}
@media all and (max-width:900px) {
.product .summary {width:98%; line-height: 30px; margin: 26px auto 27px; text-align: left;}
.product > li {padding:0; margin-bottom:30px;}
}
@media all and (max-width:890px) {
.product .summary {width:98%; line-height: 30px; margin: 24px auto 25px; text-align: left;}
}
@media all and (max-width:880px) {
.product .summary {width:98%; line-height: 30px; margin: 23px auto 23px; text-align: left;}
}
@media all and (max-width:870px) {
.product .summary {width:98%; line-height: 30px; margin: 21px auto 22px; text-align: left;}
}
@media all and (max-width:860px) {
.product .summary {width:98%; line-height: 30px; margin: 20px auto 20px; text-align: left;}
}
@media all and (max-width:850px) {
.product .summary {width:98%; line-height: 30px; margin: 18px auto 19px; text-align: left;}
}
@media all and (max-width:840px) {
.product .summary {width:98%; line-height: 30px; margin: 16px auto 17px; text-align: left;}
}
@media all and (max-width:830px) {
.product .summary {width:98%; line-height: 30px; margin: 15px auto 15px; text-align: left;}
}
@media all and (max-width:820px) {
.product .summary {width:98%; line-height: 30px; margin: 13px auto 14px; text-align: left;}
.product.gallery .item_progress .ib {width:14%;}
.product.gallery .item_progress > div {display:inline-inline-block;width:85%;}
}
@media all and (max-width:810px) {
.product .summary {width:98%; line-height: 30px; margin: 12px auto 12px; text-align: left;}
}
@media all and (max-width:800px) {
.invest_top .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 18px;width:140px;height:40px;line-height: 38px;text-align: center;}

.product .summary .sm_1 {margin-bottom:15px;}
.product .summary {width:98%; line-height: 30px; margin: 14px auto 14px; text-align: left;}
.product .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 14px;width:110px;height:34px;line-height: 30px;text-align: center;}
}
@media all and (max-width:790px) {
.product .summary {width:98%; line-height: 30px; margin: 12px auto 12px; text-align: left;}
}
@media all and (max-width:780px) {
.product .summary {width:98%; line-height: 30px; margin: 10px auto 11px; text-align: left;}
}
@media all and (max-width:770px) {
.product .summary {width:98%; line-height: 30px; margin: 9px auto 9px; text-align: left;}
}
@media all and (max-width:760px) {
.product .summary {width:98%; line-height: 30px; margin: 7px auto 7px; text-align: left;}
}
@media all and (max-width:750px) {
.product .summary .sm_1 {margin-bottom:10px;}
.product .item_progress .ib {width:15%;}
.product .item_progress > div {width:84%;}
.product .summary {width:98%; line-height: 30px; margin: 8px auto 8px; text-align: left;}
}
@media all and (max-width:740px) {
.product.gallery .item_progress .ib {width:16%;}
.product.gallery .item_progress > div {display:inline-inline-block;width:82%;}

.product .summary {width:98%; line-height: 30px; margin: 6px auto 7px; text-align: left;}
}
@media all and (max-width:730px) {
.product .summary {width:98%; line-height: 30px; margin: 5px auto 5px; text-align: left;}
}
@media all and (max-width:720px) {
.product .summary .sm_1 {margin-bottom:8px;    min-width: 39%;}
.product .summary {width:98%; line-height: 30px; margin: 4px auto 5px; text-align: left;}
}
@media all and (max-width:710px) {
.product .item_progress .ib {width:15%;}
.product .item_progress > div {width:83%;}
.product .summary {width:98%; line-height: 30px; margin: 3px auto 3px; text-align: left;}
}
@media all and (max-width:700px) {
.product .summary .sm_1 {margin-bottom:22px;}
.product .summary {width:98%; line-height: 30px; margin: 5px auto 30px; text-align: left;}
.product .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 18px;width:140px;height:40px;line-height: 38px;text-align: center;}
.product.gallery .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 18px;width:140px;height:40px;line-height: 20px;text-align: center;}
.product .item_progress .ib {width:10%;}
.product .item_progress > div {width:89%;}
.product.gallery .item_progress .ib {width:10%;}
.product.gallery .item_progress > div {width:89%;}
}

@media all and (max-width:635px) {
.iziModal {width:94%; margin:auto;}
}

@media all and (max-width:550px) {
	.invest_top .item .item_con {position:absolute;z-index: 10;top:5px;right:5px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 32px;text-align: center;}

.product .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 32px;text-align: center;}
.product.gallery .item .item_con {position:absolute;z-index: 10;top:8px;right:8px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 16px;text-align: center;}
}
@media all and (max-width:500px){
.product .item_progress .ib {width:12%;}
.product .item_progress > div {width:87%;}
.product.gallery .item_progress .ib {width:12%;}
.product.gallery .item_progress > div {width:87%;}

	.invest_top .donut_txt span {margin-top:22%;}
	.invest_top .donut_txt strong {font-size: 32px;}
}


@media all and (max-width:400px) {
.product > li {padding:0; margin-bottom:20px;}
.product .summary {width:98%; line-height: 30px; margin: 5px auto 15px; text-align: left;}
.product .summary .sm_1 {margin-bottom:15px;}
.product .item {position:relative;background-color: #fff;border:1px solid #cecece; padding:10px; overflow:hidden; transition:0.2s ease-out;}
.product .info1 .img_wrap{width:100%;height:100%;}
.product .item .item_con {position:absolute;z-index: 10;top:5px;right:5px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 34px;text-align: center;}

.product .item h4 {margin:10px 0 0;height:65px;overflow:hidden;}
.product .item .item_info .item_progress .progress {margin-bottom:0px;}

.progress {margin-bottom:0px;}
.product .item_progress .ib {width:14%;}
.product .item_progress > div {width:85%;}
.product.gallery .item_progress .ib {width:14%;}
.product.gallery .item_progress > div {width:85%;}
.invest_top .summary > li {background-size:28px;padding-left:35px;line-height:20px;margin-bottom:15px;}
.invest_top .item .item_con {position:absolute;z-index: 10;top:5px;right:5px;border-radius: 5px;font-size: 16px;width:120px;height:36px;line-height: 35px;text-align: center;}
.invest_top .donut_txt span {margin-top:18px;}
.invest_top .donut_txt strong {font-size:30px; margin-top:5%;}
.invest_top .timeout {color:#fff; font-size:14px;margin-bottom:10px;}
.invest_top .timeout .clock {background-size: 76%;margin-right:0px;}




}
</style>
