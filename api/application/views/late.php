<!DOCTYPE HTML>
<html>
<head>
<title> 케이펀딩 투자후기 </title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"> 
	<link rel="stylesheet" href="/css/style.css"/>
	<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="/js/script.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Nanum+Gothic&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
    .pagination .active {
        background-color: #00bcd4;
        border-radius: 50%;
        box-shadow: 0 4px 5px 0 rgba(0, 188, 212, 0.14), 0 1px 10px 0 rgba(0, 188, 212, 0.12), 0 2px 4px -1px rgba(0, 188, 212, 0.2);
    }
    </style>
</head>
<body>
	<div class="pageheader">
		<div class="center">
			<div class="title ft">투자후기</div>
		</div>
	</div>
	<div class="main ft">
		<div class="container">
			<div class="notice">
			<i class="material-icons">create</i>
			<p>케이펀딩 회원님들의 소중한 투자후기입니다.</p>
			</div>

                <!-- <div class="search_row">
                    <form action="/api/late" method="GET">
                        <input type="text" name="search" value="<?php echo (!$search)?'':$search?>" class="search_input">
                        <button class="search_btn">검색</button>
                    </form>
                </div> -->

<?php 
 $row_open=false;
foreach( $list as $idx=>$row) { ?>
<?php if ( $idx == 0 || ($idx % $perrow) == 0) { 
    $row_open=true;
?>
            <div class="row"> 
<?php } ?>
				<div class="card">
					<div class="border">
						<div class="card-image">
							<img src="<?php echo ( $row['late_img'] ) ?>">
						</div>
						<div class="card-body ">
							<div class="card-category">인터뷰</div>
							<div class="card-name"><?php if($row['writer'] !='' ) echo ( "- ".$row['writer']."님" )?> <?php echo ($row['viewdate']=='') ? '':"( ".$row['viewdate']." )"?></div>
							<div class="card-title"><?php echo ( $row['late_title'] ) ?></div>
							<div class="card-content"><?php echo nl2br($row['detail'])?></div>
							<div class="card-btn"><a href="/api/late/view/?idx=<?php echo ( $row['late_idx'] ) ?>&page=<?php echo $page?>">자세히보기</a></div>
						</div>
					</div>
				</div>
				
<?php if ( $idx != 0 && ($idx % $perrow) ==0) { 
    $row_open=false;
?>
            </div>
<?php } ?>            
<?php } 
if ( $row_open ) echo "</div>";
?>
			<div class="row">
				
				<div class="center ft">
					<ul class="pagination">
                    <?php echo ($pages !='') ? $pages : '<li class="active"><a href="javascript">1</a></li>'?>
                    
                    <!--
						<li class="arrow"><a href="#">&lt;&lt;</a></li>
						<li class="arrow"><a href="#">&lt;</a></li>
						<li><a href="#">1</a></li>
						<li><a href="#">2</a></li>
						<li class="circle"><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li class="arrow"><a href="#">&gt;</a></li>
						<li class="arrow"><a href="#">&gt;&gt;</a></li>
                        -->
					</ul>
				</div>
			</div>
		</div>

		<div class="black_bg">
			<div class="join">
				<div class="icon">
					<img src="/img/a.png">
				</div>
				<p>아직 케이펀딩의 회원이 아니신가요?</p>
				<div class="black_btn"><a href="https://www.kfunding.co.kr/pnpinvest/?mode=join01">회원가입 바로가기</a></div>
			</div>
			<div class="invest ">
				<div class="icon">
					<img src="/img/b.png">
				</div>
				<p>케이펀딩의 투자상품이 궁금하신가요?</p>
				<div class="black_btn"><a href="https://www.kfunding.co.kr/pnpinvest/?mode=invest">투자상품 둘러보기</a></div>
			</div>
		</div>
		<div class="subscribe-line"></div>
	</div>
</body>
</html>
