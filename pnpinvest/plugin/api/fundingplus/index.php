<?php
include_once('./_Common_execute_class.php');
include(MARI_VIEW_PATH.'/Common_select_class.php');
		/*대출건수구하기*/
		$sql = " select count(*) as cnt from mari_loan where i_view='Y'";
		$laons_result = sql_fetch($sql);
		$total_result= $laons_result['cnt'];

		$sql = " select * from mari_loan where i_view='Y'  order by i_id desc";
		$result = sql_query($sql, false);
?>
 
 {
"intowin": [
<?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
	$num=$i+1;
	$sql = "select  * from  mari_invest_progress where loan_id='$row[i_id]'";
	$iv = sql_fetch($sql, false);
	/*투자인원 구하기*/
	/*메인에서는 일단 사용안함
	$sql = " select count(*) as cnt from mari_invest where loan_id='$row[i_id]' order by i_pay desc";
	$incn = sql_fetch($sql, false);
	$invest_cn = $incn['cnt'];
	*/

	/*투자참여인원*/
	$sql = "select count(*) as cnt from mari_invest where loan_id = '$row[i_id]'";
	$iv_cnt = sql_fetch($sql);
	$iv_per_cnt = $iv_cnt['cnt'];

		/*대출총액의 투자금액 백분율구하기*/
		$sql="select sum(i_pay) from mari_invest where loan_id='$row[i_id]'"; 
		$top=sql_query($sql, false);
		$order = mysql_result($top, 0, 0);
		$total=$row[i_loan_pay];
		/*투자금액이 0보다클경우에만 연산*/
		if($order>0){
			/* 투자금액 / 대출금액 * 100 */
			$order_pay=floor ($order/$total*100);
		}else{
			$order_pay="0";
		}
	/*카테고리분류*/
	$sql = " select  * from  mari_category where ca_id='$row[i_payment]'";
	$cate = sql_fetch($sql, false);

	/*대출총액의 투자금액 백분율구하기*/
	$sql="select sum(i_pay) from mari_invest where loan_id='$row[i_id]'"; 
	$top=sql_query($sql, false);
	$order = mysql_result($top, 0, 0);

	/*성별 생년월일*/
	$sql = " select  * from  mari_member where m_id='$row[m_id]'";
	$sex = sql_fetch($sql, false);

	/*나이구하기*/

	/*날짜 자르기*/
	$datetime=$sex['m_birth'];
	$datetime = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $datetime); 
	$Y_date = date("Y", strtotime( $datetime ) );
	$M_date = date("m", strtotime( $datetime ) );
	$D_date = date("d", strtotime( $datetime ) );

	$birthday = "".$Y_date."".$M_date."".$D_date."";
	$birthyear = date("Y", strtotime( $birthday )); //생년
	$nowyear = date("Y"); //현재년도
	$age2 = $nowyear-$birthyear+1; //한국나이

	/*위시리스트에 등록내역*/
	$sql = " select  * from  mari_wishlist where m_id='$user[m_id]' and loan_id='$row[i_id]'";
	$wish = sql_fetch($sql, false);

	$face_subject=cut_str(strip_tags($row[i_subject]),22,"…");
	$face_url= "".MARI_HOME_URL."/?mode=invest_view&loan_id=$row[i_id]";
	$face_content = cut_str(strip_tags($row[i_loan_pose]),22,"…");
	$face_img = "".MARI_HOMESKIN_URL."/img/invest_img1.png";

	
	?>
{
"companyname": "이벤트템플릿", 
"i_invest_sday": "<?php echo substr($iv['i_invest_sday'],0,10);?>", 
"i_invest_eday" : "<?php echo substr($iv['i_invest_eday'],0,10);?>" , 
"pr_amount": <?php if(!$order){?>0<?php }else{?><?php echo $order;?><?php }?>, 
"image": "<?php echo MARI_DATA_URL;?>/photoreviewers/<?php echo $iv[i_creditratingviews]?>", 
"amount": <?php echo $row['i_loan_pay'];?>, 
"loan_day": 6, 
"rp_paymenttype": "<?php echo $row['i_repay'];?>", 
"product_uid": "<?php echo $row['i_id'];?>", 
"type": "1", 
"cate": "1", 
"play_condition": "<?php if($iv['i_look']=="Y"){?>start<?php }else if($iv['i_look']=="N"){?>ready<?php }else if($iv['i_look']=="C"){?>success_and<?php }?>", 
"product_subject": "<?=cut_str(strip_tags($row['i_subject']),22,"…")?>", 
"year_plus": <?php echo $row['i_year_plus']?>, 
"url": "<?php echo MARI_HOME_URL;?>/?mode=invest_view&loan_id=<?php echo $row['i_id']; ?>&cate=<?=$cate['ca_subject']?>", 
"credit_level": <?php echo $row['i_creditpoint_two']?>, 
"agency" : "NICE신용정보", 
"company_level" : "<?php if($iv['i_grade'] == "A"){?>A<?php }else if($iv['i_grade'] == "B"){?>B<?php }else if($iv['i_grade'] == "C"){?>C<?php }else if($iv['i_grade'] == "D"){?>D<?php }else if($iv['i_grade'] == "E"){?>E<?php }?>" 
}<?php if($total_result==$num){?><?php }else{?>,<?php }?>
<?php }?>
],
"version": "1.0.0" 
}