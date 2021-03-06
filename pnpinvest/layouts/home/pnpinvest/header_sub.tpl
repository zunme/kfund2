<?php
include(MARI_VIEW_PATH.'/Common_select_class.php');
?>

<!--
┏━━━━━━━━━━━━━━━━━━━━━━━┓
▶ 메인상단
┗━━━━━━━━━━━━━━━━━━━━━━━┛
-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<!--css 시작-->
<link rel="stylesheet" type="text/css" href="{MARI_HOMESKIN_URL}/css/common.css" />
<link rel="stylesheet" type="text/css" href="{MARI_HOMESKIN_URL}/css/style.css" />
<link rel="stylesheet" type="text/css" href="{MARI_HOMESKIN_URL}/css/pro-bars.min.css" />
<link rel="stylesheet" type="text/css" href="{MARI_HOMESKIN_URL}/css/jquery.bxslider.css" />
<!-- script 시작-->
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/jquery.bxslider.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/slides.min.jquery.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/javascript2.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/jquery.vticker-min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/jquery.slides.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js/jquery-migrate-1.2.1.min.js"></script>
<script src="//code.jquery.com/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js3/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js3/jquery.easing.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js3/Chart.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/check.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/bootstrap.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/jquery.slides.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/jquery.vticker-min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/jquery.bxslider.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/slider-tab.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/jquery-ui.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/canvasjs.min.js"></script>
<script type="text/javascript" src="{MARI_HOMESKIN_URL}/js2/javascript.js"></script>

<style>
body, table, div, p, span, pre {font-family: -apple-system,'Apple SD Gothic Neo','Noto Sans KR','Nanum Gothic','맑은 고딕','malgun gothic','돋움',Dotum,-apple-system,Helvetica,sans-serif !important}
a.TopMenu {display: block; text-align: center; font-size: 19px; font-weight: 600; color: #515253;}
span.TopMenuBottom {text-align: center; display: block; font-size: 10px; color: #9c9b9b;}
a.TopMenuOver { display: block; font-size: 10px !important; font-weight: 600; color: #525252 !important; white-space: nowrap; }
li.contact  {  width: 100px !important;  }
div.main_topline  {	background-color: #ffffff !important;	}
img.LogoImg  {	margin-left: 24px; margin-top: -16px; width: 220px !important;	}
</style>


<div id="wrap">
	<div id="header">
		<div class="main_topline">
			<ul class="member_mn">
				<li class="home"><a class="TopMenuOver" href="/">HOME</a></li>
				<li class="contact"><a class="TopMenuOver" href="{MARI_HOME_URL}/?mode=company_intro">CONTACT US</a></li>
				<?php if(!$member_ck){?>
					<li><a class="TopMenuOver" href="{MARI_HOME_URL}/?mode=join1">Join</a></li>
				<?php } else{?>
					<li><a class="TopMenuOver" href="{MARI_HOME_URL}/?mode=mypage">Mypage</a></li>
				<?php }?>

			       <?php if(!$member_ck){?>
					<li><a class="TopMenuOver" href="{MARI_HOME_URL}/?mode=login">Login</a></li>
				<?php } else{?>
					<li><a class="TopMenuOver" href="{MARI_HOME_URL}/?mode=logout">Loguot</a></li>
				<?php }?>

			</ul><!--member_mnEnd-->
		</div>
		<div class="gnb_wrap">
			<div class="gnb_inner" style="border-bottom: 1px solid #cfcfcf;">
				<h1 class="logo">
					<a href="{MARI_HOME_URL}/?mode=main">
						<!--<img  class="imgoff" src="{MARI_DATA_URL}/favicon/yellow_logo.png"  alt="{_config['c_title']}"/>-->
						<!-- <img  class="imgon" src="{MARI_DATA_URL}/favicon/yellow_logo.png"  alt="{_config['c_title']}"/>-->
						<!--로고이미지 사이즈잡아주세요-->
						<img class="LogoImg" src="{MARI_DATA_URL}/favicon/{_config['c_logo']}"  alt="{_config['c_title']}"/></a>
					</a>

				</h1>
				<h2 class="hidden">메뉴 네비게이션</h2>

				<ul class="gnb">
                                    <li class=""><a href="{MARI_HOME_URL}/?mode=company_intro">키스톤소개<span class="TopMenuBottom">COMPANY</span></a>
                                    <li class=""><a href="{MARI_HOME_URL}/?mode=invest">투자하기<span class="TopMenuBottom">INVESTMENT</span></a>
						<!--<div class="sub_gnb">
							<ul>
								<li><a href="{MARI_HOME_URL}/?mode=invest&i_loan_type=real">담보투자</a></li>
								<li><a href="{MARI_HOME_URL}/?mode=invest&i_loan_type=credit">신용투자</a></li>
							</ul>
						</div>-->
					</li>

				 <li style="cursor:pointer"><a href="{MARI_HOME_URL}/?mode=loan_real">대출하기<span class="TopMenuBottom">LOAN</span></a>
					<div class="sub_gnb">
							<ul>
								  <li><a href="{MARI_HOME_URL}/?mode=loan_real">부동산</a></li>
								<li><a href="{MARI_HOME_URL}/?mode=loan_credit">개인신용</a></li>
								<li><a href="{MARI_HOME_URL}/?mode=loan_business">사업자</a></li>
							</ul>
						</div>

					 </li>

					<!--<li><a href="{MARI_HOME_URL}/?mode=company_intro">회사소개</a></li>-->
					<li style="cursor:pointer"><a href="{MARI_HOME_URL}/?mode=guide_invest">이용가이드<span class="TopMenuBottom">HOW TO USE</span></a>
						<!--<div class="sub_gnb">
							<ul>
								  <li><a href="{MARI_HOME_URL}/?mode=bbs_list&table=notice&subject=공지사항">공지사항</a></li>
								<li><a href="{MARI_HOME_URL}/?mode=bbs_list&table=media&subject=언론보도&인터뷰">언론보도</a></li>
								<!--<li><a href="{MARI_HOME_URL}/?mode=bbs_list&table=qna&subject=질문과답변">문의하기</a></li>
							</ul>
						</div>-->
					</li>
					<li style="cursor:pointer"><a href="{MARI_HOME_URL}/?mode=faq">서비스소개<span class="TopMenuBottom">SERVICES</span></a>
					     <!--<div class="sub_gnb">
							<ul>
								  <li><a href="{MARI_HOME_URL}/?mode=guide">자주묻는질문</a></li>
								<li><a href="{MARI_HOME_URL}/?mode=guide_invest">투자하기가이드</a></li>
								<li><a href="{MARI_HOME_URL}/?mode=guide_loan">대출하기가이드</a></li>
							</ul>
						</div>-->
					</li>
				</ul><!--gnbEnd-->





			</div><!--gnb_inner-->
		</div><!-- gnb_wrapEnd -->


	</div><!-- header -->



<script>
	$(function(){
			var obj = $('ul.gnb li');
			$('.sub_gnb').hide();
			obj.mouseenter(function(){

			$(this).find('.sub_gnb').stop().slideDown(500);
			})
			obj.mouseleave(function(){
				$(this).find('.sub_gnb').stop().slideUp(500);
			})
		})



</script>