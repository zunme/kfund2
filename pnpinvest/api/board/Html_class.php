<?
class Html
{
// class - Start()


  // html ��� ���
  function Print_head($jsurl,$cssurl){
	return "
	<HEAD>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">
	<link href=\"$cssurl\" rel=\"stylesheet\" type=\"text/css\">
	<script language=JavaScript src=\"$jsurl\"></script>
	</HEAD>";
  }

// html ��� ���
function Print_js($jsurl){
  return "<script language=JavaScript src=\"$jsurl\"></script>";
}



  // html ���� ���
  function Print_tail(){echo "</BODY></HTML>";}

// class - End()
}
?>