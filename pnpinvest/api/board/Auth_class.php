<?
class Auth
{
  // class - Start
  // ������.
  function Auth()
  {

  }

  // secret �߰� : 2010-03-04 ������
  // �Խù� ���������� �Ѵ�
  function Authcheck($section,$Touser,$U_id,$Writer_id,$secret="")
  {
	  // ������ �Ǿ��ְų� 
	  if( $secret!='y' && (!$Touser || $section!="level") ) 
	  {
   		return "ok";
	  }
	  // ������϶� �ڽſ��� ������ ������ return ok
	  else
	  {
     $tmp	 = explode("/",$Touser);
     if($secret=="y") 
     {
       if( ($U_id!="" && in_array($U_id,$tmp)) || $Writer_id == $U_id || ($U_id!="" && ($U_id == $_SESSION[Com_id])) ){
        return "ok";
       }
     }
     else 
     {
       if( in_array($U_id,$tmp) || $Writer_id == $U_id){
        return "ok";
       }
     }
	  }
  }


// class - End
}
?>