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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
</head>
<body>
		<div class="full-container">
            <div class="write_btn_wrap">
                <a href="/api/late/write" class="write_btn">글작성</a>
            </div>
            <table class="table">
            <!--caption>캐스트 리스트</caption-->
                <thead>
                    <tr>
                        <th class="hidden-xs">#</th>
                        <th class="">SHOW</th>
                        <th class="">EDIT</th>
                        <th>제목</th>
                        <th class="hidden-xs hidden-sm">일자</th>
                        <th>삭제</th>
                    </tr>
                    </thead>
                <tbody id="CASTLISTBODY">
                    <?php foreach ($data as $row) { ?>
                        <tr class="<?php echo($row['isview']=='Y')  ? 'view_Y':'view_N' ?>">
                            <td scope="row" class="hidden-xs"><?php echo $row['late_idx']?></td>
                            <td>
                                <a class="btn btn-warning modal-contents view_btn" href="javascript:;" onClick="changeView(this)" data-idx="<?php echo $row['late_idx']?>" data-isview="<?php echo($row['isview'])?>"><i class="fas <?php echo($row['isview']=='Y')  ? 'fa-eye':'fa-eye-slash' ?>"></i></a>
                            </td>
                            <td>
                                <a href="/api/late/edit/?idx=<?php echo $row['late_idx']?>" ><i class="far fa-edit"></i></a>
                            </td>
                            <td>
                                <a href="/api/late/view/?idx=<?php echo $row['late_idx']?>" target='_blank'><?php echo $row['late_title']?></a>
                            </td>
                            <td><?php echo $row['viewdate']?></td>
                            <td><span onclick="dellate(this, <?php echo $row['late_idx']?>)">삭제</span></td>
                        </tr>
                    <?php } ?>
                </tbody>
        </div>
<script>
function dellate(ln, idx){
    $.ajax({
            url : "/api/index.php/late/del",
            data : {idx:idx},
            type : "POST",
            dataType: 'json',
            success : function (result){
                if( result.code==200){
                    $(ln).closest("tr").remove();
                }else alert( result.msg)
            },
            error : function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus + " " + errorThrown);
            }
        });
}
function changeView(ln){
    var isview = $(ln).data('isview')=='Y' ? 'N' : 'Y';
    if (confirm("상태를 변경하시겠습니까?")){
        $.ajax({
            url : "/api/index.php/late/changeview",
            data : {idx:$(ln).data('idx'), isview:isview},
            type : "POST",
            dataType: 'json',
            success : function (result){
                if( result.code==200){
                    if( isview=='N') $(ln).html( '<i class="fas fa-eye-slash"></i>')
                    else $(ln).html( '<i class="fas fa-eye"></i>')
                    $(ln).data('isview', isview)
                }else alert( result.msg)
            },
            error : function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus + " " + errorThrown);
            }
        });
    }
}
</script>
</body>