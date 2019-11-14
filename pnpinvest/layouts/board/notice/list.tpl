<?php
include(MARI_VIEW_PATH.'/Common_select_class.php');
//include('/var/www/html/pnpinvest/module/mode_bbs_list.php');
//include('/home/pnpinvest/www/pnpinvest/module/mode_bbs_list.php');
include (getcwd().'/module/mode_bbs_list.php');
?>
{# new_header}
<link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&amp;display=swap" rel="stylesheet">
<style>
.board h3{letter-spacing: -1.2px; font-family: 'Noto Sans KR', sans-serif; font-weight:600; font-size: 22px;}
.board table tbody tr.notice {  background-color: #f5f5f5; color:#00656a; font-weight:600;}
	p.paging span{margin-right: 5px;}

.board table {border-top: 5px solid #00656a; line-height: 50px; font-family: 'Noto Sans KR', sans-serif;}
.board table thead{border-left:0; border-bottom: 1px solid #00656a; color:#00656a; font-weight:600;}
.board table thead span{margin:0; border:0;background-color: #fff}
.board table thead th{border-right:0;}
.board table tbody tr{border-bottom:1px solid #ccc; border-left:0; border-top:0; border:right:0;}
.board table tbody tr td{border-right:0;}
.board table tbody tr td a{line-height: 18px;}
.board .board_sch .txt_sch input[type="text"]{border-bottom:0; font-family: 'Noto Sans KR', sans-serif; padding-top:3px; padding-left:5px; letter-spacing: -1.2px;}
.board table tbody tr td a:visited{text-decoration:none;}
.board table tbody tr td a:hover{text-decoration:none; color:#232323;}
.paging span{width:35px; height:35px; line-height: 35px; border:0;}
.paging span a{width:35px; height:35px; line-height: 35px;}
.paging span.on{background-color: #00656a;}
.paging span.prev{padding-left:6px;}
	@media (max-width: 1000px){
		.board .container{margin-top:80px;}
	}
		@media (max-width: 600px){
			.board h3{font-size: 18px; font-weight:600; display:inline; float:left; margin-top:6px;}
			.board .board_sch .txt_sch{width:50%}
		}
@media (max-width: 600px){
	.board .board_sch .txt_sch{width:35%}
	.board .container{margin-top:60px;}
}
</style>
<!-- /////////////////////////////// 본문 시작 /////////////////////////////// -->
<div id="container" class="sub">
	<!-- Sub title -->
	<h2 class="subtitle"><span class="motion" data-animation="flash"><?php echo $bbs_config["bo_subject"]?></span></h2>
	<!-- 펀딩 공지사항 -->
	<div class="board">
		<div class="container">
			<!-- 검색 -->
			<form action="/pnpinvest/" method="GET">
        <input type="hidden" name="mode" value="bbs_list">
        <input type="hidden" name="table" value="<?php echo $table?>">
				<input type="hidden" name="search" value="<?php echo htmlspecialchars ($_GET['searchtxt'],ENT_QUOTES )?>">
				<!--div style="vertical-align: top;position: absolute;top: 0;right: 400px;text-align: right;">
					<select>
						<option>제목순</option>
					</select>
				</div-->

			</form>
			<h3>케이펀딩 <?php echo $bbs_config["bo_subject"]?></h3>
			<div class="board_sch">
				<p class="txt_sch"><input type="text" placeholder="<?php echo $bbs_config["bo_subject"]?> 검색" name="searchtxt" value="<?php echo htmlspecialchars ($_GET['searchtxt'],ENT_QUOTES )?>"></p>
				<button class="btn_sch" type="submit">검색</button>
			</div>
			<!-- 게시판 테이블 -->
			<table>
				<caption>케이펀딩 공지사항</caption>
				<colgroup>
					<col class="no" style="width:10%;">
					<col>
					<col class="pc" style="width:10%;">
					<col class="pc" style="width:12%;">

				</colgroup>
				<thead>
					<tr>
						<th><span>번호</span></th>
						<th><span>제목</span></th>
						<th class="pc"><span>작성자</span></th>
						<th class="pc"><span>작성일</span></th>

					</tr>
				</thead>
				<tbody id="notice">

				</tbody>
			</table>
			<!-- 페이징 -->
			<p class="paging" id="noticepage">
			</p>
			<?php if ( $table == 'notice' ) { ?>
			<!-- 바로가기 -->
			<!--h3 class="evtbn">케이펀딩 이벤트</h3>
			<a class="go_url" href="/pnpinvest/?mode=bbs_list&table=event">현재 진행중인 이벤트 참여하러 가기</a-->
		<?php } ?>
		</div>
	</div>
</div>

<script>
function getboard(url,id){
  $.ajax({
   type : 'GET',
   url : url,
   dataType : 'json',
   success : function(result) {
     $("#"+id+"page").html(result.page);

		$("#"+id+ " > tr").not(".notice").each( function (ind){ $(this).remove()});
		 $.each(result.list, function (ind, row) {
			 $("#"+id).append( "<tr" + ( (row.w_notice=="Y")?" class='notice'":"" ) +"><td>" + ( (row.w_notice=="Y")?"공지":row.w_id ) + "</td><td class='tl'><a href='/pnpinvest/?mode=bbs_view&type=view&table=<?php echo $table?>&id="+row.w_id+"'>"+row.w_subject+"</a></td><td class='pc'>관리자</td><td class='pc'>"+row.w_datetime+"</td><!--td class='pc'>"+row.w_hit+"</td--></tr>");
		 });
   }
  });
}
function pagechange(link, id){
	getboard( link, id );
}

$(document).ready(function () {
  getboard("/api/index.php/boardapi/boardlist?table=<?php echo $table?>&target=notice&noti=Y&per=7&search="+$("input[name='search']").val(),'notice' );
});
</script>

<!-- /////////////////////////////// 본문 끝 /////////////////////////////// -->

{# new_footer}
