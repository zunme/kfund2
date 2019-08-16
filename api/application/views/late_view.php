<!DOCTYPE HTML>
<html>
<head>
<title> 케이펀딩 투자후기 </title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"> 
	<link rel="stylesheet" href="/css/style1.css"/>
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
	<div class="main">
		<div class="container">
			<div class="late_container_header_wrap">
				<div class="late_container_header_title"><?php echo $data['late_title']?></div>
				<div class="late_container_header_date"><?php echo $data['regdate']?></div>
			</div>
			<div class="text">
            	<?php echo $data['late_body']?>
			</div>
			<div class="btn"><a href="/api/late/<?php echo $page?>">목록</a></div>
		</div>
	</div>	

</body>