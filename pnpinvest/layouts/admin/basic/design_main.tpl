<?php
include(MARI_VIEW_PATH.'/Common_select_class.php');
?>
<!--
┏━━━━━━━━━━━━━━━━━━━━━━━┓
▶ ADMIN 디자인관리
┗━━━━━━━━━━━━━━━━━━━━━━━┛
-->
{# s_header}<!--상단-->



<div id="wrapper">
	<div id="left_container">
		{# left_bar}

		<div class="lnb_wrap">
			<div class="title01">디자인관리</div>
			{# lnb}<!--메인메뉴-->
		</div><!-- /lnb_wrap -->
	</div><!-- /left_container -->
    
	<div id="container">
		<div class="title03"><span>디자인관리</span></div>
		 <ul class="info1">
			<li>사이트의 디자인을 관리할 수 있습니다.</li>
			<li>SEO, 페이지보안, 로고, 파비콘 등을 설정합니다.</li>
		</ul>

		<!-- <h4 class="t_tite1"><span></span></h4> -->
		<div class="tbl_wrap2">
			<table class="type1">
				<caption>디자인관리</caption>
				<colgroup>
					<col width="">
					<col width="">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">디스플레이 설정</th>
						<td>REAL TIME, SMS 상담을 설정 할 수 있습니다.<a href="{MARI_HOME_URL}/?cms=exposure_settings"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">SEO 설정</th>
						<td>SEO, 로그분석 스크립트등을 설정 할 수 있습니다. <a href="{MARI_HOME_URL}/?cms=seo_config"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">페이지보안 설정</th>
						<td>페이지 보안을 설정 할 수 있습니다. <a href="{MARI_HOME_URL}/?cms=page_security"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">로고/파비콘 설정</th>
						<td>로고, 파비콘을 설정 할 수 있습니다.<a href="{MARI_HOME_URL}/?cms=favicon"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">하단정보 설정</th>
						<td>사이트하단의 카피라이트, 부가정보등을 직접 설정하실 수 있습니다. <a href="{MARI_HOME_URL}/?cms=copyright"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">카테고리 설정</th>
						<td>상품의 카테고리를 설정하고 분류를 자유롭게 분리하여 운영하실 수 있습니다. <a href="{MARI_HOME_URL}/?cms=category_list"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">파일매니저</th>
						<td>파일매니저로 접속하여 파일을 직접 자유롭게 관리하실 수 있습니다. <a href="{MARI_HOME_URL}/?cms=filemanager"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">INCLUDE관리</th>
						<td>header, footer, left등 기타 include 파일을 자유롭게 설정하여 사용하실 수 있습니다. <a href="{MARI_HOME_URL}/?cms=management_inc"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<tr>
						<th scope="row">페이지관리</th>
						<td>사이트의 디자인 페이지를 직접관리하실 수 있습니다. <a href="{MARI_HOME_URL}/?cms=management_page"><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr>
					<!-- <tr>
						<th scope="row">FTP접속</th>
						<td>ftp로 바로 접속 할 수 있습니다.<a href="{MARI_HOME_URL}/?cms="><img src="{MARI_ADMINSKIN_URL}/img/shortcut_btn.png" alt="바로가기"></a></td>
					</tr> -->
				</tbody>
			</table>
		</div>
    </div><!-- /contaner -->
</div><!-- /wrapper -->


{# s_footer}<!--하단-->