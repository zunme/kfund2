{# new_header}
<!-- /////////////////////////////// 본문 시작 /////////////////////////////// -->
<meta http-equiv="x-ua-compatible" content="IE=edge" />
<link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&amp;display=swap" rel="stylesheet">
<style>
body{background: #f9f9f9;}
*{margin:0; padding:0; border:0; outline:0;}
.login_form {max-width: 400px; background: #fff;}
.login_form1{margin:0 auto; max-width: 400px;}
.sub .container{    /* margin: 50px; */
    max-width: 1000px;
    border: 0;
    /*border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
    /* border-radius: 10px; */
    padding: 40px;
    border-left-bottom-radius: 6px;
    border-right-bottom-radius: 6px;
  box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.07), 0 6px 30px 5px rgba(0, 0, 0, 0), 0 8px 10px -5px rgba(0, 0, 0, 0.07);}
.sub .container1{    /* margin: 50px; */
    max-width: 1000px;

    /* border-radius: 10px;
    padding: 30px;
    border-left-top-radius: 6px;
    border-right-top-radius: 6px;
  /*box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.07), 0 6px 30px 5px rgba(0, 0, 0, 0.05), 0 8px 10px -5px rgba(0, 0, 0, 0.02);*/}
.login_form .top_txt{border:0; font-size: 16px; letter-spacing: -0.4px; font-family: 'Noto Sans KR', sans-serif; font-weight:500; margin-bottom: 30px;}
.btn.t1{background-color: #061551; color:#fff; border-color:#061551;}
.login_form .btn{line-height: 40px; border-radius: 0; margin:10px 0 20px;}
.login_form input[type="text"], .login_form input[type="password"]{border-radius:0; width:84%; padding:0 15px;}
.login_form .link{letter-spacing: -0.4px; font-family: 'Noto Sans KR', sans-serif;}
.login_id_icon{border-top: 1px solid #ccc;border-left: 1px solid #ccc;border-bottom: 1px solid #ccc;
    width: 16%;
    background-color: transparent;
    line-height: 45px;
    height: 45px;
    margin-bottom: 10px;float:left; display:inline;}
.material-icons{font-size: 28px; line-height: 1.5; color:#061551;}
.login_logo{margin:0 auto; text-align: center; margin:10px auto 30px;}
@media (max-width: 500px){
  body{background: #fff;}
  .sub .container{box-shadow: 0 0 0 0;}
  .login_logo{display:none;}
}
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active){
  .material-icons{line-height: 1.8;}
  .login_form .top_txt{border:0; font-size: 16px; letter-spacing: -0.4px; font-family: 'Noto Sans KR', sans-serif; font-weight:500; margin-bottom: 30px;}
  .login_form .link{letter-spacing: -0.4px; font-family: 'Noto Sans KR', sans-serif;}
}



</style>
<div id="container" class="sub">
	<!-- Sub title -->
	<h2 class="subtitle t3"><span class="motion" data-animation="flash">로그인</span></h2>
	<!-- 로그인 -->
  <div class="login_form1 userform1">
		<div class="container1">
      	<!--<p class="top_txt">케이펀딩의 다양한 서비스를 이용해보세요.</p>-->

    </div>
  </div>
	<div class="login_form userform">
		<div class="container">
      <img class="login_logo" src="/pnpinvest/img/login_logo.png" alt="케이펀딩로고">
			<p class="top_txt">케이펀딩의 다양한 서비스를 이용해보세요.</p>
			<form id="f" name="f" method="post" onSubmit="return sendit()">
				<div>
					<div class="login_id_icon"><i class="material-icons">
email
</i></div><input id="login_id" name="m_id" class="iptxt" title="이메일 입력 폼" type="text" placeholder="이메일을 입력해주세요.">
					<div class="login_id_icon"><i class="material-icons">
lock
</i></div><input id="login_pass" name="m_password" class="iptxt" title="비밀번호 입력 폼" type="password" placeholder="비밀번호를 입력해주세요.">
					<!--<span class="checktxt">아이디나 비밀번호를 확인해주세요.</span>-->
				</div>
				<div>
					<button type="submit" class="btn t1 f2" href="javascript:;" data-onClick="sendit()">로그인</button>
				</div>
				<div class="link">
					<p>아직 회원이 아니세요? <a href="/pnpinvest/?mode=join01">가입하기</a></p>
					<p>비밀번호가 기억나지 않으신가요? <a href="/pnpinvest/?mode=find">비밀번호 찾기</a></p>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /////////////////////////////// 본문 끝 /////////////////////////////// -->
<script>
function ValidateEmail(mail)
{
return (true)
  console.log(mail);
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
  {
    return (true)
  }
    return (false)
}

function sendit(){
  if ( !ValidateEmail($("input[name=m_id]").val()) ){
    alert('이메일을 확인해주세요.');f.m_id.focus();return false;
  }
	else if($("input[name=m_password]").val().trim() == '' ){
		alert('패스워드를 입력해주세요');f.m_password.focus();return false;
	}
  else {
    document.f.action='/pnpinvest/?mode=login_ck';
    return true;
  }
	//
	//document.f.submit();
}
</script>
{# new_footer}
