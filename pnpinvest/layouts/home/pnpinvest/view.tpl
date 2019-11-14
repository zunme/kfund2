<?php
include(MARI_VIEW_PATH.'/Common_select_class.php');
?>

<?php
	if ($_COOKIE['ck_id'] != $id)
		{
			$sql = "update mari_write set w_hit = w_hit + 1 where w_table='$table' and w_id='$id'";
			sql_query($sql);

			set_cookie("ck_id", $id, 60*60*24);
		}
?>
{#new_header}
<!-- /////////////////////////////// 본문 시작 /////////////////////////////// -->
<link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&amp;display=swap" rel="stylesheet">
<div id="container" class="sub">
	<!-- Sub title -->
	<h2 class="subtitle t2"><span class="motion" data-animation="flash"><?php echo $bbs_config["bo_subject"]?></span></h2>
	<!-- 펀딩 공지사항 -->
	<div class="board">
		<div class="container">
			<!-- 컨텐츠 본문 -->
			<div class="board_view">
				<div class="bv_header">
					<h3><?php echo $w['w_subject'];?></h3>
					<span class="date"><i class="material-icons">
date_range
</i>&nbsp;<?php echo substr($w['w_datetime'],0,10); ?></span>
				</div>
				<div class="bv_body">
					<?php if($w[file_img]){?>
					<img src="/pnpinvest/data/<?php echo $table;?>/<?php echo $w[file_img]?>" alt="">
					<?php }?>

					<?php echo $w['w_content'];?>
					<?php if( $w['w_rink'] != '' && false){?>
						<p><a href="<?php echo $w['w_rink']?>" target="_blank">자세히 보기 &gt;</a></p>
					<?php }?>
				</div>
			</div>
			<p class="bv_footer"><a href="javascript:;" class="btn" onClick="javascript:history.back(-1);">목 록</a></p>
			<script>
			window.onload=function(){
				var img = $(".bv_body img");
				img.each(function() {
					var imgWidth = $(this).width();
					$(this).css('max-width',imgWidth).css('width','100%');
				});
			}
			</script>
		</div>
	</div>
</div>
<style>
.board table{width:700px; margin:0 auto; border-top: 5px solid #00656a;}
.board table tbody tr{border:0;}
.board table tbody tr td{border:0; style:none;}
.board_view h3{letter-spacing: -1.2px; font-family: 'Noto Sans KR', sans-serif; font-weight:600; font-size: 20px;}
.bv_header{border-top:0; border-bottom: 3px solid #00656a; padding: 40px 0 14px 0; margin-bottom:0;}
.bv_header .date{position:absolute; right:0; padding-top: 36px;}
.bv_body{border-left:1px solid #bfbfbf; border-right:1px solid #bfbfbf; border-bottom:1px solid #bfbfbf; border-top:0; padding:40px 10px;}
.board_view{border:0;}
.material-icons{vertical-align: bottom; font-size:18px;}
.bv_footer{margin:0 45px 70px;}
.bv_footer .btn{width:150px; height:45px; line-height:30px; font-size:18px;}
@media all and (max-width:1000px) {
		.bv_footer{margin:10px 20px 70px;}
	.board table{width:100%; margin:0 auto; border:0; border-top: 5px solid #00656a; letter-spacing: -1.2px; font-family: 'Noto Sans KR', sans-serif;}
}
@media (max-width: 800px){
	.board_view h3{font-size:20px; display:block; margin-bottom:20px;}
	.bv_header span{display:block; right:0; }

}
@media (max-width: 700px){
		.bv_footer{margin:10px 10px 70px;}
}
@media (max-width: 600px){
.bv_footer .btn{width:120px; height:40px; line-height:24px; font-size:16px;}
.board_view h3{font-size:20px; display:block; margin-bottom:20px;}
.bv_header span{display:block; right:0; }
}
</style>
<!-- /////////////////////////////// 본문 끝 /////////////////////////////// -->
{# new_footer}<!--하단-->
