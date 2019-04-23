
<form id="form01">
<table>
  <tr>
    <th>서비스명</th>
    <td><input type="text" name="servicename" value="케이펀딩">
      <input type="hidden" name="apikey" value="c5b75d0592da369ccf6131169aff22cdfb7d1838d1e48cbb7f088dea34ed1344">
      <input type="hidden" name="mode" value="<?php echo $data['mode']?>">
    </td>
  </tr>
  <tr>
    <th>상품명</th>
    <td><input type="text" name="productname" value="<?php echo $data['productname']?>"></td>
  </tr>
  <tr>
    <th>상품 고유 코드</th>
    <td><input type="text" name="productcode" value="<?php echo $data['productcode']?>"></td>
  </tr>
  <tr>
    <th>페이지 URL</th>
    <td><input type="text" name="url" value="<?php echo $data['url']?>"></td>
  </tr>
  <tr>
    <th>상품 이미지 URL</th>
    <td><input type="text" name="image" value="<?php echo $data['image']?>"></td>
  </tr>
  <tr>
    <th>이자율</th>
    <td><input type="text" name="returnrate" value="<?php echo $data['returnrate']?>"></td>
  </tr>
  <tr>
    <th>상품 시작 시간</th>
    <td><input type="text" name="startat" value="<?php echo $data['startat']?>"></td>
  </tr>
  <tr>
    <th>상환 기간(개월 수)</th>
    <td><input type="text" name="period" value="<?php echo $data['period']?>"></td>
  </tr>
  <tr>
    <th>모집금액</th>
    <td><input type="text" name="amount" value="<?php echo $data['amount']?>"></td>
  </tr>
  <tr>
    <th> 상품 리워드</th>
    <td><textarea rows="5" name="reward"><?php echo $data['reward']?></textarea></td>
  </tr>
  <tr>
    <th>이자상환방식</th>
    <td>
      <select name="repaymenttype">
          <option value="1" <?php echo (isset($data['repaymenttype']) && $data['repaymenttype']=='1' ? "selected":"")?>>만기일시상환</option>
          <option value="2" <?php echo (isset($data['repaymenttype']) && $data['repaymenttype']=='2' ? "selected":"")?>>원리금균등</option>
          <option value="3" <?php echo (isset($data['repaymenttype']) && $data['repaymenttype']=='3' ? "selected":"")?>>원금균등상환</option>
      </select>
    </td>
  </tr>
  <tr>
    <th>진행상태</th>
    <td>
      <select name="productstep">
          <option value="1" <?php echo (isset($data['productstep']) && $data['productstep']=='1' ? "selected":"")?>>모집대기중</option>
          <option value="2" <?php echo (isset($data['productstep']) && $data['productstep']=='2' ? "selected":"")?>>펀딩진행중</option>
          <option value="3" <?php echo (isset($data['productstep']) && $data['productstep']=='3' ? "selected":"")?>>상환대기중</option>
          <option value="4" <?php echo (isset($data['productstep']) && $data['productstep']=='4' ? "selected":"")?>>이자상환중</option>
          <option value="5" <?php echo (isset($data['productstep']) && $data['productstep']=='5' ? "selected":"")?>>연체중</option>
          <option value="6" <?php echo (isset($data['productstep']) && $data['productstep']=='6' ? "selected":"")?>>중도상환완료</option>
          <option value="7" <?php echo (isset($data['productstep']) && $data['productstep']=='7' ? "selected":"")?>>만기상환완료</option>
          <option value="8" <?php echo (isset($data['productstep']) && $data['productstep']=='8' ? "selected":"")?>>투자취소</option>
          <option value="9" <?php echo (isset($data['productstep']) && $data['productstep']=='9' ? "selected":"")?>>부실</option>
          <option value="99" <?php echo (!isset($data['productstep']) || !in_array($data['productstep'], array("1","2","3","4","5","6","7","8","9")) ? "selected":"")?>>알수없음</option>
      </select>
    </td>
  </tr>
  <tr>
    <th>노출 여부</th>
    <td>
      <select name="publish">
          <option value="Y" <?php echo (isset($data['publish']) && $data['publish']=='1' ? "selected":"")?>>Y</option>
          <option value="N" <?php echo (isset($data['publish']) && $data['publish']=='2' ? "selected":"")?>>N</option>
      </select>
    </td>
  </tr>
</table>
</form>
<span class="btn" onClick="regdata()">등록<span>
<script>
 function regdata(){
   $.ajax({
     type : 'POST',
     url : '/api/funscan/fncall',
     dataType : 'json',
     data : $("#form01").serialize(),
     success : function(result) {
       console.log( result )
     }
   });
 }
 function result() {

 }
</script>
