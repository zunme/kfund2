<?php
// �Խ��� �ΰ���� ���� dir
define(CSSURL,"./css/style.css");
define(JSURL,"./js/board.js");
define(FileBaseDir,ImageBase);

class Board{

// ���� �����Ҷ� �۷ι��� ���Ǵ³�
var $Tmp_delfiles;
var $Img_widmax = 0;
var $DataBase;
var $Sock;

  //-------------------------
  //   ������
  //-------------------------
  function Board($Sock,$DataBase,$inst_main){

    // ���ϻ����Ҷ� tmp ���ڿ� �ʱ�ȭ
    $this->Tmp_delfiles	=	"";
    $this->Img_widmax = 700;
    $this->DataBase = $DataBase;
    $this->Sock = $Sock;
    $this->inst_main = $inst_main;

    //  �Խ��� Ȯ�� ���
    //  �Խ��ǰ� ��ǰ�ı�,���� ���� ���� �޼ҵ� ������ �̷��� ���� ���ش�. 2009-02-02 : ������
    $this->inst_BoardExtends = new BoardExtends(&$inst_main);
    //$this->inst_BoardExtends->LoadBoardOPT();

  }

  //----------------------------------------------------
  //                     ����
  //----------------------------------------------------
  function main(){

    $mysql = $this->Sock;
    ob_start();

    if(!$_POST["section"]){$section = $_GET["section"];}else{$section  =  $_POST["section"];}
    if(!$_GET["Field"]){$Field =	$_POST["Field"];}else{$Field		= $_GET["Field"];}
    if(!$_GET["Key"]){$Key =	$_POST["Key"];}else{$Key		=	&$_GET["Key"];}
    if(!$_POST["baction"]){$baction    =  $_GET["baction"];}else{$baction    =  $_POST["baction"];}
    if(!$_POST["Thread"]){$Thread    =  $_GET["Thread"];}else{$Thread    =  $_POST["Thread"];}
    if(!$_POST["Fid"]){$Fid    =  $_GET["Fid"];}else{$Fid    =  $_POST["Fid"];}
    if(!$_POST["table_name"]){$table_name    =  $_GET["table_name"];}else{$table_name = $_POST["table_name"];}
    if(!$_GET["page"]){$page = 1;}else{$page = $_GET["page"];}
    $U_Level = $_SESSION["ULEVEL"];
    $U_id = $_SESSION["UID"] == "" ? $_SESSION["Com_id"]:$_SESSION["UID"];
    $U_name = $_SESSION["Com_id"] == "" ? $_SESSION["UNAME"]:"������";
    $temp= explode("[",$_SESSION["UNAME"]); // ȸ�� �� �پ� �ִ� ����� ���ش�.
    $U_name = $temp[0];

    $inst_FileIO = $this->inst_main->inst_FileIO;
    $inst_CForm = $this->inst_main->inst_CForm;
    $inst_Lupdate = $this->inst_main->inst_Lupdate;
    $inst_SelectDisplay = $this->inst_main->inst_SelectDisplay;
    $inst_query			=	new Board_query($this->DataBase);	// �Խ��� ���� ���� Ŭ����
    $inst_location		=	new Location_Control();         // location���� Ŭ����
    $inst_html				=  new Html();                    // Html ���� Ŭ����
    $inst_StringCon	=	new String_Con();                 // ���ڿ� ��Ʈ�� Ŭ���� �ε�
    $inst_auth				=	new Auth();                     // �Խù� ���� ���� ����
    $inst_otm				=	new Board_output_template();      // output Ŭ����
    $oFCKeditor = new FCKeditor('Contents') ;           // html �ؽ�Ʈ ������ Ŭ����
    $inst_query->CP_CODE = $this->inst_main->CP_CODE;   // �������� ����� CP_CODE

    // ���̺� ���³�� ���� �� �̹��� �ױ׿� ���̺� �������� ��ũ��Ʈ �߰�
    $_POST[Title] = $inst_StringCon->Guard_table($_POST[Title],"","");
    $_POST[Contents] = $inst_StringCon->Guard_table($_POST[Contents],"","");
    $_POST[Comment] = $inst_StringCon->Guard_table($_POST[Comment],"","");

    // �˻� �� �� �˻� Ű�� ��ȯ�ؼ� �˻����ش�.
    if($Field!='Writer_name'){$Key = $inst_StringCon->Guard_table($Key,"","");}
    else{$Key = $inst_StringCon->clear_text($Key);}

		//	��ǰ����, ��ǰ�ı� ���ο��� �ۼ����ɿ��� üũ - admin_class ���� ���� : 2011-06-22 ������
		if($_SESSION["SelfHugi"]=='on') { $this->AdminBoardWriteCheck = 'Y'; }
		else { $this->AdminBoardWriteCheck = 'N'; }

    //$this->inst_String = &$inst_StringCon;
	// ��ǰ���� �Խ����� ���������� ������ ���̵��� ���� 2012-02-14 ������
	if($_SESSION[U_ID] == "" && $table_name == "Board_goods_qna") {
		$_GET['path'] = "mypage";
		$_POST['path'] = "mypage";	
	}

    switch($baction){

      //  ���̺� ����
      case "create" :

        if($_POST[board_state] == "goods_qna" || $_POST[board_state] == "goods_after") {
          $section = "good";
        } elseif($_POST[board_state] == "gongji") {
          $section = "gongji";
        } else {
          $section = "normal";
        }

        // �Խ��� ��� ���̺��� ������ �������ش�.
        $inst_query->Create_board_list($mysql);
        // �Խ��� ���̺� ���� �� �Խ��� ��Ͽ� ���
        $result = $inst_query->Create_table($section,$_POST,$mysql);
        Return $result;
        //$inst_location->top_reload();
      break;

      // �ڸ�Ʈ ����
      case "comdele" :

        $table = $_GET[table_name]."_c";

        // ���� �����Ͱ� ���� ���� ���ϸ� �ְ� �ȴٸ� �̺κ��� �����ϰ� ��ü �Ѵ�.
        $query	=	"SELECT * FROM $this->DataBase.$table WHERE B_no	=	'$_GET[No]' and C_no='$_GET[Cno]' ";
        $data		=	$mysql->fetch_key($query);

        // �ڽ��� �Խù��� ��� ����
        if($data[c_id][0]==$U_id){
          $query = "delete from $this->DataBase.$table where B_no	=	'$_GET[No]' and C_no='$_GET[Cno]' and C_ID='$_GET[ID]' ";
          $mysql->execute($query);
        }

        // action ==> view
        $inst_location->go($inst_output->self_page."?action=Board&baction=view&table_name=$_GET[table_name]&section=$section&No=$_GET[No]&as=$_GET[as]&path=$_GET[path]&b_title=$_GET[b_title]");
      break;

      //  ���� ����
      case "filedelete" :

        $B_no =	$_POST["No"];
        $inst_filelist = new File_list(); // ���� ��� ��� Ŭ���� �ν��Ͻ�

        // Delete_file_repl �� �迭 ���Ŀ� ���߱����ؼ� �迭�� Ű������ ����
        $_POST["filename"][0] = substr($_POST["delstring"],0,strlen($_POST["delstring"])-1);

        // �۷ι� ������ $this->Tmp_delfiles�� ������ ���ϸ� �Ҵ�.
        $this->Delete_file_repl($_POST);

        // ���� ����
        $this->Delete_file($this->Tmp_delfiles,$U_id);

        // ������Ʈ�� ���Ϲ��ڿ�
        $update_files = $inst_filelist->Sep_strings($_POST);

        if($table_name=='Board_review') { $field_name = "G_code"; }
        else { $field_name = 'Filename';}

        // ���������� ����� ���Ϲ��ڿ��� ������Ʈ���ش�.
        $query = "UPDATE $this->DataBase.$table_name SET $field_name='$update_files' WHERE B_no='$B_no' AND Writer_id='$U_id'";
        $mysql->execute($query);

        // action ==> view
        $inst_location->go($inst_output->self_page."?action=Board&baction=edit&table_name=$table_name&section=$section&No=$B_no&as=$_POST[as]&path=$_POST[path]");
      break;

      // ��ȸ ��
      // ���� ��ȸ�� ���� ��尡 �۵����� �������� view���� �׳� ī��Ʈ
      // �̰� ����Ϸ��� view ��忡�� ��ȸ�ø��⸦ �ּ�ó���ؾ��Ѵ�.
      case "ref" :

        $B_no			 =	$_GET["No"];
        $query	= "UPDATE $this->DataBase.$table_name SET ref = ref+1 WHERE B_no=$B_no ";
        $mysql->execute($query);

        // action ==> view
        $inst_location->go($inst_output->self_page."?action=Board&baction=view&table_name=$table_name&section=$section&No=$B_no&page=$page");
      break;

      // ���� ����
      case "edit_save" :

        $inst_query->inst_FileIO = $inst_FileIO;
        $inst_query->inst_SelectDisplay = $inst_SelectDisplay;

        $B_no =	$_POST["No"];
        $FileCount = count($_FILES["AttFile"]["tmp_name"]); // ÷�ε� ������ ����

        // ÷�ε� ������ ù��° ���ϸ� (������ ÷������ ���� ��쿡 �̸��� null�� �ȴ�.)
        $ReceiveFileName	=	$_FILES["AttFile"]["tmp_name"][0];

        // �� ÷�������� ���� ���
        // ���� ������ 0���� ũ�� ù��° ���ϸ��� null�� �ƴҰ�� ������ ���°����� ó��
        if($FileCount > 0 && $ReceiveFileName!=""){
          // ������ ������ش�.
          $this->Upload_file($inst_query,$inst_output,$inst_location,$_POST,$_FILES,$FileCount,$mysql,$section,$table_name,$U_id,$baction);
        }
        // �� ÷�������� ���� ���
        else{
          // ÷������ �߰��� ���� ��쿡�� ���� ���ϸ�� ���ڿ��� �Ҵ�.
          $_POST["update_filename"] = $_POST["originalfiles"];

          // ���� ���� ���� , ������ �ν��Ͻ��� ����.
          $inst_query->Update_record($_POST,$mysql);
        }
        // action ==> view
        $inst_location->go($inst_output->self_page."?action=Board&baction=view&table_name=$table_name&section=$section&No=$B_no&as=$_POST[as]&path=$_POST[path]&b_title=$_POST[b_title]");
      break;

      // ��� �ۼ�
      case "repl" :

        $tpl	= new Template_;
        $B_no	= $_GET["No"];

        // ����
        $data	=	$inst_query->Select_view($mysql,$B_no,$table_name,$section,$inst_output->self_pager,$U_id,"link");

        $data[contents][0] = nl2br(urldecode($data[contents][0]));
        $data[title][0] = urldecode($data[title][0]);
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����

        $tpl->define('repl',$tpl_file);
        $tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�

        //  ������� ���� 2009-02-02 ������
        //  ������ ���� ������ ������ ��ȯ����
        $this->inst_BoardExtends->SwitchOPT(&$data,'view');

        // �ؽ�Ʈ ������
        $oFCKeditor->BasePath	= '/fckeditor/';
        $oFCKeditor->Width  = '100%';
        $oFCKeditor->Height = '320px';
        $oFCKeditor->Value		= "";
        $tpl->assign('oFCKeditor',$oFCKeditor);
        $tpl->assign('inst_otm',$inst_otm);						// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('data',$data);	// ������ �迭
        $tpl->assign('options_array',array($data["touser"][0],$data["charger"][0],$data["collaborator"][0],$data["start_time"][0],$data["end_time"][0]));	// ������ �迭

        $tpl->assign('essential',
        array('section'=>$section,
        'baction'=>$baction,
        'table_name'=>$table_name,
        'U_id'=>$U_id,
        'U_name'=>$U_name,
        'page'=>$page,
        'path'=>$_GET[path],
        'b_title'=>urlencode($_GET[b_title]),
        'No'=>$B_no,
        'mysql'=>$mysql));  // �ʼ� ������
        $tpl->print_('repl');

        flush();
      break;

      //  �� ����
      case "write_save" :


        // �̹��� ���� üũ : ����� �̹��� ���ϸ� ����
        if($table_name == "Board_review") {
          $tmp_o = @explode(".",$_FILES["AttFile"]["name"][0]);
          $tmp = array_pop($tmp_o);
          $tmp = strtolower($tmp);
          if(trim($_FILES["AttFile"]["name"][0])!="" && $tmp != "gif" && $tmp != "jpg") {
            $inst_location->msg_go($inst_output->self_page."?action=Board&baction=write&table_name=$table_name&section=$section&No=$B_no&b_title=$_POST[b_title]","���� ������ GIF or JPG�� �̹��� ������ �Է��� �ֽñ� �ٶ��ϴ�.");
            exit;
          }
        }

        // �α��� ���ϰ� �������Ҷ�
        if(!$U_id){echo "�α����� ���ּ���.";exit;}
        $FileCount	=	count($_FILES["AttFile"]["tmp_name"]); // ÷�ε� ������ ����

        // ÷�ε� ������ ù��° ���ϸ� (������ ÷������ ���� ��쿡 �̸��� null�� �ȴ�.)
        $ReceiveFileName	=	$_FILES["AttFile"]["tmp_name"][0];

        // �� ÷�������� ���� ���
        // ���� ������ 0���� ũ�� ù��° ���ϸ��� null�� �ƴҰ�� ������ ���°����� ó��
        if($FileCount > 0 && $ReceiveFileName!=""){
          // ������ ������ش�.
          $success	=	$this->Upload_file($inst_query,$inst_output,$inst_location,$_POST,$_FILES,$FileCount,$mysql,$section,$table_name,$U_id,$baction);
        }
        // �� ÷�������� ���� ���
        else{
          $inst_query->inst_FileIO = $inst_FileIO;
          $inst_query->inst_SelectDisplay = $inst_SelectDisplay;

          // SMS �߼� : 2009-03-05 ������
          /*
          if($table_name=='Board_one_qna'){
            if($_POST['thread']=='A'){ // ���
              $List=array();
              $List[0] = 'qa_repl';
              $List[1] = $_POST['writer'];
              $data2 = $this->inst_main->inst_SelectMember->GetMemberSMS($List);
              $this->inst_main->inst_ExecuteMember->SendAutoSms(&$this->inst_main,$data2,6);
            } else {
              $List=array();
              $List[0] = 'qa_write';
              $List[1] = $_POST['writer'];
              $data2 = $this->inst_main->inst_SelectMember->GetMemberSMS($List);
              $this->inst_main->inst_ExecuteMember->SendAutoSms(&$this->inst_main,$data2,5);
            }
          }
          */
		  // �̸��� �ּҰ� ������ ���Ϸ� �亯 ������ 2011-02-25 ������ �߰�
		  if($_POST[email] != '') {

			 // ��ü���� 2011-03-02 ������
			 //$meminfo = $this->inst_main->inst_SelectMember->GetMemInfo($_SESSION[Com_id], 'ECHost');
			 $shopinfo = $this->inst_main->inst_SelectMember->Getshop_info($_SESSION[CP_CODE], 'EC_test');

			 $answer = urldecode($_POST[Contents]);
			 $answer = str_replace("\r","<p>",$answer);
			 $answer = str_replace("\n","<p>",$answer);
			 $subject  = urldecode($_POST[Title]);
			 $content  = "�ȳ��ϼ���.<p>".$shopinfo[0][shop_name]." �Դϴ�.<p>&nbsp;<p>";
			 $content .= "[����]<p>";
			 $content .= $_POST[question]."<p>&nbsp;<p>";
			 $content .= "[�亯]<p>";
			 $content .= $answer."<p>&nbsp;<p>\n";
			 $content .= " <a href='http://".$shopinfo[0][shop_url]."/?action=Detail&GoodsCode=".$_POST[GoodsCode]."'><b>[�ش� ��ǰ �ٷΰ���]</b></a>";
			 //								            �������̸���,        �����»�� �̸�,	�޴��̸���, ����,    ����,
			 $this->inst_main->inst_SendMail->PutMail($shopinfo[0][admin_email], '������', $_POST[email], $subject, $content);

		  }

          $inst_query->Insert_record($inst_query,$inst_output,$inst_location,$_POST,$mysql,$section,$table_name,"nofile"); // �Խù��� ���
        }
      break;

      // �� ����
      case "dele" :

      $B_no			 =	$_GET["No"];
      $com_table = $table_name."_c";
      $admin		 =	$_GET["admin"];

      $query	=	"SELECT * FROM $this->DataBase.$table_name WHERE B_no='$B_no' ";
      $data		=	$mysql->fetch_key($query);

      $Writer		=	$data["writer_id"][0];

      //  ����Խ����ϰ�� : 2009-01-13 �ϴ�...�߰�...�Ф� ��..
      if($table_name=='Board_review') {$Filename	=	$data["g_code"][0];}
      else {$Filename	=	$data["filename"][0];}

      $Thread	=	$data["thread"][0];
      $Fid			=	$data["fid"][0];

      // ���� �Խ����� ��쿡��
      if($section=="file" || $section=="good"){
        // ���� ���ڵ忡 ÷�ε� ��� ������ �����ؿ´� �θ���̶�� �ڽı۵��� ÷�����ϱ��� �����ؿ´�.
        $Repl_data	=	$inst_query->Query_reply_files($mysql,$Thread,$table_name,$Fid,$B_no);

        // ÷�����ϵ��� ���ڿ��� ������ش�. ������� �۷ι� ���� $this->Tmp_delfiles �� ����ȴ�.
        $this->Delete_file_repl($Repl_data);
      }

      // �ڱ� �Խù��� �´ٸ�
      // $U_id==$Writer ||  ����ó��. ���ο����� ���������ϵ���. CP_CODE�� �α��� ���̵� �������� ���������ϵ��� �߰�.: 2011-05-11 ������ - ���κ� �Խ��� ���� �ƹ����� �̰Կ������� ����
      if($admin == "Y" && $_SESSION[U_ID]==$_SESSION[CP_CODE]){
      // if - 1 -start

        // �ܰ� 1
        // ���ڵ�� ��۵��� �����Ѵ�.
        $query	=	"DELETE FROM $this->DataBase.$table_name WHERE B_no	=	'$B_no' ";
        $Result1	=	$mysql->execute($query);
        // ����� ��� �����Ѵ�.
        $query	=	"DELETE FROM $this->DataBase.$com_table WHERE B_no	=	'$B_no' ";
        $mysql->execute($query);

        //  �ܰ� 2
        // �� , �����尡 A ��� �ش��ϴ� Fid�� �Խù��� ��� �����ش�.
        if($Result1!= "" && $Thread=="A"){
          $query		=	"DELETE FROM $this->DataBase.$table_name WHERE Fid	=	'$Fid' ";
          $Result2	=	$mysql->execute($query);
        }

        // �������� ���Ͽ� ������Ʈ
        // DB�� ��ȯ : 2011-03-02 ������
        //if($table_name == "Board_gongji") {
        //  $inst_SelectDisplay->PopUpGongJi(&$inst_FileIO,'');
        //}

        // �ܰ� 3
        // ������ ��� �Ǿ� �ִٸ�
        if($Result1== "Success" && $Filename!=""){

          // ������ �����Ѵ�.
          /*
          $this->Tmp_delfiles �̳���
          ���� $this->Query_reply_files ���� �����Ǿ ���� ������ ���� ��۵� ���� ��� ���ϵ��� �������ش�.
          */
          $this->Delete_file($this->Tmp_delfiles,$U_id);
        }

      // if - 1 - end
      }
      else{echo "�����ڸ� ���� �� �� �ֽ��ϴ�.";exit;}

        # action ==> list
        $inst_location->go($inst_output->self_page."?action=Board&baction=list&table_name=$table_name&section=$section&as=$_GET[as]&path=$_GET[path]");
      break;

      // ���� ����
      case "view" :
        $B_no			 =	$_GET["No"];
        $tpl = new Template_;

        // ��ȸ�� ������Ʈ
        $query	= "UPDATE $this->DataBase.$table_name SET ref = ref+1 WHERE B_no=$B_no ";
        $mysql->execute($query);

        //  ÷�������� ǥ�� Ÿ�� ���� : 2009-01-13 ������
        $file_view_type = $this->GetAddfileType($section,$table_name);

        // ����
        $data	=	$inst_query->Select_view($mysql,$B_no,$table_name,$section,$inst_output->self_pager,$U_id,$file_view_type);
        $data[contents][0] = nl2br(urldecode($data[contents][0]));
        $data[title][0] = urldecode($data[title][0]);

        // ��� ������
        $cdata	=	$inst_query->Select_comm($mysql,$B_no,$table_name);

        // ���� ����Ʈ ������
        $data_sub = $inst_query->Select_sublist($mysql,$data[fid][0],$section,$table_name);

        // ���ø� ���
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����

        $CP_CODE = $this->CP_CODE;
        if($_SESSION[CP_CODE] != "") { $CP_CODE = $_SESSION[CP_CODE]; }

        // ��ǰ ���� �Խ��ǿ� ������ ó�� : 2009-01-13 ������ ����
        if($table_name=="Board_review") {
          $img_string = @implode("<br>",$data[filename][filename]); //  ��¿� �̹�����
          $f_name = explode("|",$data[g_code][0]);
          $data[file_name][0] = $f_name[0];
          $Img_URL = Img_Domain."/".$CP_CODE."/Board";
        }
        else { $Img_URL = Img_Domain."/".$CP_CODE."/Goods"; }

        //  ������� ���� 2009-02-02 ������
        //  ������ ���� ������ ������ ��ȯ����
        $this->inst_BoardExtends->SwitchOPT(&$data,'view');
        $this->inst_BoardExtends->SwitchOPT(&$cdata);
        $this->inst_BoardExtends->SwitchOPT(&$data_sub[0]);

        $tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�
        $tpl->define('view',$tpl_file);
        $tpl->assign('img_string',$img_string);
        $tpl->assign('inst_otm',$inst_otm);	// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('data',$data);	// ������ �迭
        $tpl->assign('cdata',$cdata);	// ��� ������ �迭
        $tpl->assign('data_sub',$data_sub[0]);	// ���긮��Ʈ ������ �迭
        $tpl->assign('data_sub_count',$data_sub[1]);	// ���긮��Ʈ ������ ����
        $tpl->assign('inst_auth',$inst_auth);						// ���� ���� Ŭ���� �ν��Ͻ�
        // ������ �߰� : 2009-04-17 ������
        if($_SESSION["udomain"]!='') { $userdomain = 'http://'.$_SESSION["udomain"]; }
        else { $userdomain = 'http://'.$_SESSION["surl"]; }
        $essential = array('section'=>$section,'baction'=>$baction,'table_name'=>$table_name,'U_id'=>$U_id,'U_name'=>$U_name,'page'=>$page,'Thread'=>$Thread,'Fid'=>$Fid,'No'=>$B_no,'mysql'=>$mysql,'b_title'=>urlencode($_GET[b_title]),'Img_URL'=>$Img_URL,'board_no'=>$_GET[board_no],'path'=>$_GET[path],'userdomain'=>$userdomain);

        $tpl->assign('essential',$essential);  // �ʼ� ������
        $tpl->print_('view');

        flush();
      break;

      case "viewpa" : // ���� �������׿뵵 �߰� : 2011-02-18 ������
        $B_no			 =	$_GET["No"];
        $tpl = new Template_;

        //  ������� ��ȸ ������Ʈ
        $this->inst_main->inst_ExecuteDisplay->UpdateGongji($B_no);

        // ����
        $data	=	$this->inst_main->inst_SelectDisplay->PrintGongji(1,$B_no);

        // ���ø� ���
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����

        $CP_CODE = $_SESSION[CP_CODE] != "" ? $_SESSION[CP_CODE] : $this->CP_CODE;

        $tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�
        $tpl->define('view',$tpl_file);
        $tpl->assign('img_string',$img_string);
        $tpl->assign('inst_otm',$inst_otm);	// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('data',$data[0]);	// ������ �迭
        $tpl->assign('inst_auth',$inst_auth);						// ���� ���� Ŭ���� �ν��Ͻ�
        $essential = array('section'=>$section,'baction'=>$baction,'page'=>$page,'Thread'=>$Thread,'Fid'=>$Fid,'No'=>$B_no,'mysql'=>$mysql,'b_title'=>urlencode($_GET[b_title]),'board_no'=>$_GET[board_no],'path'=>$_GET[path]);
        $tpl->assign('essential',$essential);  // �ʼ� ������
        $tpl->print_('view');

        flush();
      break;

      // ���� ����
      case "viewSet" :
        $B_no			 =	$_GET["nb"];
        $G_code    =  $_GET["GoodsCode"];
        $tpl = new Template_;

        // ��ȸ�� ������Ʈ
        $query	= "select * from $this->DataBase.$table_name WHERE Fid in(select Fid from $this->DataBase.$table_name WHERE B_no=$B_no)";
        $data	=	$mysql->fetch($query);

        echo "<script>";

        $tt_view = "";
        $re_view = "";
        foreach($data as $key=>$val) {

          if($val[b_no] == $B_no && $val[thread] == 'A') {
			if($val[secret] == "") {
				$tt_view = "
				  parent.document.getElementById('sView_${B_no}').style.display = 'none';
				  parent.document.getElementById('rcView_${B_no}').style.display = '';
				  ";
				  $re_view = "���� : ".str_replace("\n","<br>",str_replace("\r","",$val[contents]))."<br>";
			} else {
				if($val[secret] != $_GET["pw"]) {
				  echo "alert('��й�ȣ�� ���� �ʽ��ϴ�');";
				  break;
				}else{
				  $tt_view = "
				  parent.document.getElementById('sView_${B_no}').style.display = 'none';
				  parent.document.getElementById('rcView_${B_no}').style.display = '';
				  ";
				  $re_view = "���� : ".str_replace("\n","<br>",str_replace("\r","",$val[contents]))."<br>";

				}
			}

          }elseif($val[thread] == 'AA') {

            if(is_array($data)) {
              $re_view .= "�亯 : ".str_replace("\n","<br>",str_replace("\r","",urldecode($val[contents])));
              $tt_view .= "
              parent.document.getElementById('rtData_${B_no}').innerHTML= '".urldecode($val[title])."';
              parent.document.getElementById('rtdData_${B_no}').innerHTML= '".$val[regi_date]."'; // �亯 ��¥
              parent.document.getElementById('rtView_${B_no}').style.display = '';
              parent.document.getElementById('rcView_${B_no}').style.display = '';
              ";
            }
          }
        }

		    echo "$('.sview').hide();";
        echo $tt_view;
        echo "parent.document.getElementById('rcData_${B_no}').innerHTML= '$re_view';";
        echo "</script>";

        flush();
      break;

      // ���� ����
      case "edit" :

        $B_no			 =	$_GET["No"];
        // ����
        $data	=	$inst_query->Select_view($mysql,$B_no,$table_name,$section,$inst_output->self_pager,$U_id,"file");
        $data[contents][0] = nl2br(urldecode($data[contents][0]));
        $data[title][0] = urldecode($data[title][0]);
        $inst_filelist = new File_list(); // ���� ��� ��� Ŭ����
        $tpl = new Template_;
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����
        $tpl->define('edit',$tpl_file);
        $tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�

        //  ������� ���� 2009-02-02 ������
        //  ������ ���� ������ ������ ��ȯ����
        $this->inst_BoardExtends->SwitchOPT(&$data,'view');

        //  ÷�ε� ���� ����
        $have_file_count = count($data[filename][filename]);

        // �ؽ�Ʈ ������
        $oFCKeditor->BasePath	= '/fckeditor/';
        $oFCKeditor->Width  = '100%';
        $oFCKeditor->Height = '420px';
        $oFCKeditor->Value		= $data[contents][0];
        $tpl->assign('oFCKeditor',$oFCKeditor);
        $tpl->assign('have_file_count',$have_file_count);
        $tpl->assign('inst_otm',$inst_otm);  // ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_filelist',$inst_filelist); // ���� ��� ����
        $tpl->assign('data',$data);	// ������ �迭
        $tpl->assign('options_array',array($data["touser"][0],$data["charger"][0],$data["collaborator"][0],$data["start_time"][0],$data["end_time"][0]));	// ������ �迭
        $tpl->assign('essential',
        array('section'=>$section,
        'baction'=>$baction,
        'table_name'=>$table_name,
        'U_id'=>$U_id,
        'U_name'=>$U_name,
        'U_Level'=>$U_Level,
        'page'=>$page,
        'No'=>$B_no,
        'Thread'=>$Thread,
        'Fid'=>$Fid,
        'path'=>$_GET[path],
        'b_title'=>urlencode($_GET[b_title]),
        'g_sati'=>$data["g_sati"][0],
        'g_code'=>$data["g_code"][0],
        'mysql'=>$mysql));  // �ʼ� ������
        $tpl->print_('edit');

        flush();
      break;

      // �� �ۼ�
      case "write" :

        if($_GET[table_name] == "Board_goods_qna") {
          $section = "good";
        }

        $tpl = new Template_;
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����
        $tpl->define('write',$tpl_file);

        // �ؽ�Ʈ ������
        $oFCKeditor->BasePath	= '/fckeditor/';
        $oFCKeditor->Width  = '100%';
        $oFCKeditor->Height = '420px';
        $oFCKeditor->Value		= $data[contents][0];

        $action = $_GET[path] == "mypage" ? "MyBoard":"Board";

        $Img_URL = Img_Domain."/".$this->CP_CODE."/Goods";
        $goods_info = $inst_query->GetGoods_img($_GET[GoodsCode],$mysql);

        $tpl->assign('oFCKeditor',$oFCKeditor);
        $tpl->assign('inst_otm',$inst_otm);						// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('essential',
        array('section'=>$section,
        'baction'=>$baction,
        'table_name'=>$table_name,
        'U_id'=>$U_id,
        'U_name'=>$U_name,
        'U_Level'=>$U_Level,
        'page'=>$page,
        'Thread'=>$Thread,
        'Fid'=>$Fid,
        'GoodsCode'=>$_GET[GoodsCode],
        'GoodsImg'=>$Img_URL.'/'.$goods_info[file_name][0],
        'GoodsName'=>$goods_info[goods_name][0],
        'path'=>$_GET[path],
        'Action'=>$action,
        'b_title'=>urlencode($_GET[b_title]),
        'mysql'=>$mysql));  // �ʼ� ������
        $tpl->print_('write');

        flush();
      break;

			// ����Ʈ �˾�
			case "listPop" :

				if(!$_GET["Goods_Code"]) {
					echo "��ǰ �ڵ带 ã�� �� �����ϴ�.";
					exit;
				}
				$code = $_GET["Goods_Code"];
				$tpl = new Template_;
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����
        $tpl->define('list',$tpl_file);
        $JSURL = $inst_html->Print_js(JSURL); # html ���
        $tpl->assign('JSURL',$JSURL);
        $tpl->assign('inst_CForm',$inst_CForm);
        $tpl->assign('inst_Lupdate',$inst_Lupdate);
        $path = $_GET[path]!="" ? $_GET[path] : $_POST[path];

        $su = "";
        // CommonMenu[Board_list] ���� DB�� ���� : 2011-03-03 ������
        $CommonMenu_Board_list = $this->inst_main->inst_SelectDisplay->GetBoardlistInfo();
        if($CommonMenu_Board_list[Board_list][table_name] != "") {
          $k = array_search($_GET[table_name],$CommonMenu_Board_list[Board_list][table_name]);
          //$su = $this->inst_main->CommonMenu[Board_list][Paging][$k];
          $su = 10;
        }

        // MyPage �Խ����� �ڱⰡ �ۼ��Ѱ͸� ����
        $UID = $param_add = "";
        $action = "Board";

        if($path == "mypage") {
          $UID = $_SESSION[UID];
          $action = "MyBoard";
          $this->inst_main->Mode = "";
          $param_add = "&path=mypage";
        }
        else { $param_add = "&as=".$this->inst_main->Mode; }

        //------------------------------------------------------Paging----------------------------------------------------//
        $param				=	sprintf("section=%s&action=$action&baction=%s&table_name=%s&Field=%s&Key=%s&paging=%s",$section,$baction,$table_name,$Field,$Key,$su).$param_add;
        $inst_page		=	new BoardPage($su,$su); // ����Ʈ ����, ����Ʈ ��� ��
        $page_query	=	$inst_query->Get_pagecount2($table_name,$Field,$Key,$UID,$code); // ����¡�� ��ü �Խù� ���� ������
        $total =	 $inst_page->get_total($page_query,$mysql);					// ����¡�� ��ü �Խù� ���� ����
        $inst_page->get_value($total,$page,$_SERVER["PHP_SELF"]," ",$param);
        $limit					=	$inst_page->get_limt();
        $tpl->assign('inst_page',$inst_page);		// ����¡ Ŭ���� �ν��Ͻ�
        //------------------------------------------------------Paging----------------------------------------------------//

        // �˻�� ������츸 ��ü���� ��ư ���
        if(!$Key){$total_view	=	"";}

        // �������� ��� �ϴ� �ּ�
       // else{$total_view = $inst_otm->Print_button("Total","onclick = \"total_listview('$this->self_page','$section','$table_name','$baction');\"");}

        // �˻��� ���ڵ尡 ������.
        if($total==0 && $Key!=""){$inst_location->msg_go($inst_otm->self_page."?section=$section&table_name=$table_name&action=$action&path=$path&baction=$baction&as=$_POST[as]","�˻��� �Խù��� �����ϴ�.");}
        else if($total==0){}

        // Select ���� [0��° ���� ��ġ ������ , 1��° ���� ���ڵ� ����]
        $data_array	=	$inst_query->Select_list($inst_query->Make_list_field($section,$table_name,$limit,$Field,$Key,$UID,$code),$mysql);

				// �Խ��� ���� �������� 2011-05-06 ������ �߰�
				$data = $this->inst_main->inst_SelectDisplay->GetBoardList($this->inst_main->CP_CODE,'0,10',"Board_goods_qna");

				// ȸ������ �ҷ����� 2011-05-06 ������ �߰�
				$com_info = $this->inst_main->inst_SelectDisplay->GetCompanyInfo($this->inst_main->CP_CODE,"com_information");
				$com_data = unserialize($com_info[t_contents][0]);

				$CP_CODE = $this->CP_CODE;
        if($_SESSION[CP_CODE] != "") {
          $CP_CODE = $_SESSION[CP_CODE];
        }

        // �Խ��� no
        $board_no = $inst_page->total - ($inst_page->page-1) * $inst_page->block_set;

        if($table_name=="Board_review") {
          // ��ǰ ���� �Խ��ǿ� ��ǰ�̹��� ���
          for($i=0; $i<sizeof($data_array[0]); $i++) {
            $f_name = explode("|",$data_array[0][$i][g_code]);
            $data_array[0][$i][file_name] = $f_name[0];
          // loop - End
          }
          $Img_URL = Img_Domain."/".$CP_CODE."/Board";
        }
        else { $Img_URL = Img_Domain."/".$CP_CODE."/Goods"; }

        //  ������� ���� 2009-02-02 ������
        //  ������ ���� ������ ������ ��ȯ����
        $this->inst_BoardExtends->SwitchOPT(&$data_array[0]);

				// 2011-05-06 ������ �߰� �亯�� ���ϴ� ������ �亯.
				if($data[0][title_name][0] != "" || $data_array[1] >1) {
					for($i=0; $i<$data_array[1]; $i++) {
						if($data_array[0][$i][thread] == 'AA' && $data[0][title_name][0] !='' ) {
							$answer_title[$i] = str_replace('{_writer}',$data_array[0][$i-1][writer_name],$data[0][title_name][0]);
							$answer_title[$i] = str_replace('{_shopname}',$com_data[shop_name],$answer_title[$i]);
						}
					// loop - End
					}
				}

				if($data[0][name_img][0] != "") {
					$admin_name = "<img src='".Img_Domain."/".$CP_CODE."/Board/".$data[0][name_img][0]."' /> ";
				} else if($data[0][admin_name][0] != "") {
					$admin_name = $data[0][admin_name][0];
				} else {
					$admin_name = "������";
				}

        $tpl->assign('trlist',$data_array[0]);					// tr����Ʈ -> record set
        $tpl->assign('answer_title',$answer_title);					//  2011-05-06 ������ �߰�
        $tpl->assign('admin_name',$admin_name);					//  2011-05-06 ������ �߰�
				$tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_otm',$inst_otm);						// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_auth',$inst_auth);						// ���� ���� Ŭ���� �ν��Ͻ�
        $tpl->assign('viewTotal',$data_array[1]);						// ���� ���� Ŭ���� �ν��Ͻ�
        $tpl->assign('essential',array('section'=>$section,'baction'=>$baction,'table_name'=>$table_name,'U_id'=>$U_id,'total_view'=>$total_view,'Img_URL'=>$Img_URL,'path'=>$path,'b_title'=>urlencode($_GET[b_title]),'board_no'=>$board_no));  // �ʼ� ������
        $tpl->print_('list');

        flush();

			break;

      // ����Ʈ
      case "list" :

		if( $table_name == "Board_one_qna" ) { return ""; }

        $tpl = new Template_;
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����
        $tpl->define('list',$tpl_file);
        $JSURL = $inst_html->Print_js(JSURL); # html ���
        $tpl->assign('JSURL',$JSURL);
        $tpl->assign('inst_CForm',$inst_CForm);
        $tpl->assign('inst_Lupdate',$inst_Lupdate);
        $path = $_GET[path]!="" ? $_GET[path] : $_POST[path];

        $su = "";
        // CommonMenu[Board_list] ���� DB�� ���� : 2011-03-03 ������
        $CommonMenu_Board_list = $this->inst_main->inst_SelectDisplay->GetBoardlistInfo();
        if($CommonMenu_Board_list[Board_list][table_name] != "") {
          $k = array_search($_GET[table_name],$CommonMenu_Board_list[Board_list][table_name]);
          //$su = $this->inst_main->CommonMenu[Board_list][Paging][$k];
          $su = 10;
        }

        // MyPage �Խ����� �ڱⰡ �ۼ��Ѱ͸� ����
        $UID = $param_add = "";
        $action = "Board";

        if($path == "mypage") {
          $UID = $_SESSION[UID];
          $action = "MyBoard";
          $this->inst_main->Mode = "";
          $param_add = "&path=mypage";
        }
        else { $param_add = "&as=".$this->inst_main->Mode; }

        //------------------------------------------------------Paging----------------------------------------------------//
        $param				=	sprintf("section=%s&action=$action&baction=%s&table_name=%s&Field=%s&Key=%s&paging=%s",$section,$baction,$table_name,$Field,$Key,$su).$param_add;
        $inst_page		=	new BoardPage($su,$su); // ����Ʈ ����, ����Ʈ ��� ��
        $page_query	=	$inst_query->Get_pagecount($table_name,$Field,$Key,$UID); // ����¡�� ��ü �Խù� ���� ������
        $total =	 $inst_page->get_total($page_query,$mysql);					// ����¡�� ��ü �Խù� ���� ����
        $inst_page->get_value($total,$page,$_SERVER["PHP_SELF"]," ",$param);
        $limit					=	$inst_page->get_limt();
        $tpl->assign('inst_page',$inst_page);		// ����¡ Ŭ���� �ν��Ͻ�
        //------------------------------------------------------Paging----------------------------------------------------//

        // �˻�� ������츸 ��ü���� ��ư ���
        if(!$Key){$total_view	=	"";}

        // �������� ��� �ϴ� �ּ�
       // else{$total_view = $inst_otm->Print_button("Total","onclick = \"total_listview('$this->self_page','$section','$table_name','$baction');\"");}

        // �˻��� ���ڵ尡 ������.
        if($total==0 && $Key!=""){$inst_location->msg_go($inst_otm->self_page."?section=$section&table_name=$table_name&action=$action&path=$path&baction=$baction&as=$_POST[as]","�˻��� �Խù��� �����ϴ�.");}
        else if($total==0){}

        // Select ���� [0��° ���� ��ġ ������ , 1��° ���� ���ڵ� ����]
        $data_array	=	$inst_query->Select_list($inst_query->Make_list_field($section,$table_name,$limit,$Field,$Key,$UID),$mysql);

				// �Խ��� ���� �������� 2011-05-06 ������ �߰�
				$data = $this->inst_main->inst_SelectDisplay->GetBoardList($this->inst_main->CP_CODE,'0,10',"Board_goods_qna");

				// ȸ������ �ҷ����� 2011-05-06 ������ �߰�
				$com_info = $this->inst_main->inst_SelectDisplay->GetCompanyInfo($this->inst_main->CP_CODE,"com_information");
				$com_data = unserialize($com_info[t_contents][0]);

				$CP_CODE = $this->CP_CODE;
        if($_SESSION[CP_CODE] != "") {
          $CP_CODE = $_SESSION[CP_CODE];
        }

        // �Խ��� no
        $board_no = $inst_page->total - ($inst_page->page-1) * $inst_page->block_set;

        if($table_name=="Board_review") {
          // ��ǰ ���� �Խ��ǿ� ��ǰ�̹��� ���
          for($i=0; $i<sizeof($data_array[0]); $i++) {
            $f_name = explode("|",$data_array[0][$i][g_code]);
            $data_array[0][$i][file_name] = $f_name[0];
          // loop - End
          }
          $Img_URL = Img_Domain."/".$CP_CODE."/Board";
        }
        else { $Img_URL = Img_Domain."/".$CP_CODE."/Goods"; }

        //  ������� ���� 2009-02-02 ������
        //  ������ ���� ������ ������ ��ȯ����
        $this->inst_BoardExtends->SwitchOPT(&$data_array[0]);

				// 2011-05-06 ������ �߰� �亯�� ���ϴ� ������ �亯.
				if($data[0][title_name][0] != "" || $data_array[1] >1) {
					for($i=0; $i<$data_array[1]; $i++) {
						if($data_array[0][$i][thread] == 'AA' && $data[0][title_name][0] !='' ) {
							$answer_title[$i] = str_replace('{_writer}',$data_array[0][$i-1][writer_name],$data[0][title_name][0]);
							$answer_title[$i] = str_replace('{_shopname}',$com_data[shop_name],$answer_title[$i]);
						}
					// loop - End
					}
				}

				if($data[0][name_img][0] != "") {
					$admin_name = "<img src='".Img_Domain."/".$CP_CODE."/Board/".$data[0][name_img][0]."' /> ";
				} else if($data[0][admin_name][0] != "") {
					$admin_name = $data[0][admin_name][0];
				} else {
					$admin_name = "������";
				}

        $tpl->assign('trlist',$data_array[0]);					// tr����Ʈ -> record set
        $tpl->assign('answer_title',$answer_title);					//  2011-05-06 ������ �߰�
        $tpl->assign('admin_name',$admin_name);					//  2011-05-06 ������ �߰�
				$tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_otm',$inst_otm);						// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_auth',$inst_auth);						// ���� ���� Ŭ���� �ν��Ͻ�
        $tpl->assign('essential',array('section'=>$section,'baction'=>$baction,'table_name'=>$table_name,'U_id'=>$U_id,'total_view'=>$total_view,'Img_URL'=>$Img_URL,'path'=>$path,'b_title'=>urlencode($_GET[b_title]),'board_no'=>$board_no));  // �ʼ� ������
        $tpl->print_('list');

        flush();
      break;

      // ����Ʈ
      case "listpa" :

        $tpl = new Template_;
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����
        $tpl->define('list',$tpl_file);
        $JSURL = $inst_html->Print_js(JSURL); # html ���
        $tpl->assign('JSURL',$JSURL);
        $tpl->assign('inst_CForm',$inst_CForm);
        $tpl->assign('inst_Lupdate',$inst_Lupdate);
        $path = $_GET[path]!="" ? $_GET[path] : $_POST[path];

        // MyPage �Խ����� �ڱⰡ �ۼ��Ѱ͸� ����
        $UID = $param_add = "";
        $action = "Board";
        $param_add = "&as=".$this->inst_main->Mode;

        //  ��ü����
        $total_temp = $this->inst_main->inst_SelectDisplay->PrintGongji("total",'');

        //------------------------------------------------------Paging----------------------------------------------------//
        $param				=	sprintf("section=%s&action=$action&baction=%s&paging=%s",$section,$baction,10).$param_add;
        $inst_page		=	new BoardPage(10,10); // ����Ʈ ����, ����Ʈ ��� ��
        $total        =	$total_temp[0][count][0];					// ����¡�� ��ü �Խù� ���� ����
        $inst_page->get_value($total,$page,$_SERVER["PHP_SELF"]," ",$param);
        $limit					=	$inst_page->get_limt();
        $tpl->assign('inst_page',$inst_page);		// ����¡ Ŭ���� �ν��Ͻ�
        //------------------------------------------------------Paging----------------------------------------------------//

        // Select ���� [0��° ���� ��ġ ������ , 1��° ���� ���ڵ� ����]
        $data_array = $this->inst_main->inst_SelectDisplay->PrintGongji("all","","$inst_page->limit_no , $inst_page->page_set");

        $CP_CODE = $_SESSION[CP_CODE] != "" ? $_SESSION[CP_CODE] : $this->CP_CODE;

        // �Խ��� no
        $board_no = $inst_page->total - ($inst_page->page-1) * $inst_page->block_set;
        $tpl->assign('trlist',$data_array[0]);					// tr����Ʈ -> record set
        $tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_otm',$inst_otm);						// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_auth',$inst_auth);						// ���� ���� Ŭ���� �ν��Ͻ�
        $tpl->assign('essential',array('section'=>$section,'baction'=>$baction,'path'=>$path,'b_title'=>urlencode($_GET[b_title]),'board_no'=>$board_no));  // �ʼ� ������
        $tpl->print_('list');

        flush();
      break;

      //  ��� ���
      case "addcom" :

       $B_no =	$_POST["No"];
       $C_ID =   	$_POST["U_id"];
       $C_Name = $_POST["U_name"];
       $Comment = $_POST["Comment"];
       $section = $_POST["section"];
       $table_name = $_POST["table_name"];
       $table = $table_name."_c";

        $query = "INSERT INTO $this->DataBase.$table( `B_no` , `C_ID` , `C_Name` , `Regi_date` , `Comment` ) VALUES ('$B_no', '$C_ID', '$C_Name', NOW( ) , '$Comment');";
        $mysql->execute($query);

        # action ==> view
        $inst_location->go($inst_output->self_page."?action=Board&baction=view&No=$B_no&table_name=$table_name&section=$section&as=$_POST[as]&path=$_POST[path]&b_title=$_POST[b_title]");

      break;

      //  ���� ���
      case "add" :
/*
			 // ������������ �˻�
			 include './zmSpamFree/zmSpamFree.php';
			 if ( !zsfCheck( $_POST['zsfCode'] ) )
			 {
			   $inst_location->msg_go($inst_output->self_page."?action=Detail&GoodsCode=$_POST[GoodsCode]","���������ڵ带 ��Ȯ�� �Է��� �ֽʽÿ�.");
				 exit;
			 }

*/
       $UID			= $_SESSION[UID];
	   $Title		= $_POST["title"];
       $Writer_name = $_POST["writer"];
       $Contents	= $_POST["content"];
       $G_code		= $_POST["GoodsCode"];

       $Secret		= $_POST["pw"];
       $Touser		= $_POST["open"];
       $email		= $_POST["email"] != "" ? $_POST["email"] : "";
       $user_ip		= $_SERVER['REMOTE_ADDR'];
       $table		= "Board_goods_qna";

       $query	=	"SELECT `Fid` FROM $this->DataBase.$table order by `Fid` desc limit 1";
       $data	=	$mysql->fetch($query);

       $Fid = $data[0]['fid']+1;

       if($Title != "" || $Contents != "") {
			$query = "INSERT INTO $this->DataBase.$table( `Title` , `Writer_id`, `Writer_name` , `Regi_date` , `Contents` , `G_code` , `Fid` , `Touser` ,  `Secret`, `email`, `user_ip` ) VALUES ('$Title', '$UID', '$Writer_name', NOW( ) , '$Contents' , '$G_code' , '$Fid' , '$Touser', '$Secret', '$email','$user_ip');";
			$mysql->execute($query);
       }

       # action ==> view
       $inst_location->go($inst_output->self_page."?action=Detail&GoodsCode=$G_code");

      break;

			//  �ı� ��� 2011-07-19 ������
      case "add_hugi" :

			 // ������������ �˻�
			 include './zmSpamFree/zmSpamFree.php';
			 if ( !zsfCheck( $_POST['spCode'] ) )
			 {
			   $inst_location->msg_go($inst_output->self_page."?action=Detail&GoodsCode=$_POST[GoodsCode]","���������ڵ带 ��Ȯ�� �Է��� �ֽʽÿ�.");
				 exit;
			 }

			 $regi_date = date('Y-m-d H:i:s');
       $writer			= $_POST["title"];
       $contents	= $_POST["content"];
       $G_code		= $_POST["GoodsCode"];
			 $goods_name = $_POST["GoodsName"];
       $table		  = "Board_goods_review";
			 $rate      =  $_POST["rate"];

			 if($writer != "" || $contents != "") {
				$query = "INSERT INTO $this->DataBase.$table( regi_date , contents ,  writer , rate , goods_code , goods_name , type,  state ) VALUES ('$regi_date', '$contents', '$writer', $rate , '$G_code' , '$goods_name' , 'ADMIN' , '0');";
				$mysql->execute($query);
			 }

       # action ==> view
       $inst_location->go($inst_output->self_page."?action=Detail&GoodsCode=$G_code");

      break;

	  //  ��ǰ�ı�
      case "hugi" :

        $tpl = new Template_;
        $tpl_file = $inst_otm->Output_changer($baction,$section); // $baction_$section.tpl <== �̷� ����
        $tpl->define('list',$tpl_file);
        $JSURL = $inst_html->Print_js(JSURL); # html ���
        $tpl->assign('JSURL',$JSURL);
        $tpl->assign('inst_CForm',$inst_CForm);
        $tpl->assign('inst_Lupdate',$inst_Lupdate);

				//	��ǰ����, �ı� �����ۼ� ���ɿ��� : 2011-06-22 ������
		    $tpl->assign('AdminBoardWriteCheck',$this->AdminBoardWriteCheck);

		// MyPage �Խ����� �ڱⰡ �ۼ��Ѱ͸� ����
        $UID = $param_add = "";
        $action = "Board";
        $param_add = "&as=".$this->inst_main->Mode;

        //  ��ü����
        $total_temp = $this->inst_main->inst_SelectDisplay->GetHugiList();

        //------------------------------------------------------Paging----------------------------------------------------//
        $param		  =	sprintf("section=%s&action=$action&baction=%s&paging=%s",$section,$baction,10).$param_add;
        $inst_page	  = new BoardPage(10,10); // ����Ʈ ����, ����Ʈ ��� ��
        $total        =	$total_temp[1];					// ����¡�� ��ü �Խù� ���� ����
        $inst_page->get_value($total,$page,$_SERVER["PHP_SELF"]," ",$param);
        $limit		  =	$inst_page->get_limt();
        $tpl->assign('inst_page',$inst_page);		// ����¡ Ŭ���� �ν��Ͻ�
        //------------------------------------------------------Paging----------------------------------------------------//

        // Select ���� [0��° ���� ��ġ ������ , 1��° ���� ���ڵ� ����]
        $data_array	=	$this->inst_main->inst_SelectDisplay->GetHugiList("$inst_page->limit_no , $inst_page->page_set");

        // �Խ��� no
        $board_no = $inst_page->total - ($inst_page->page-1) * $inst_page->block_set;
				$CP_CODE = $_SESSION[CP_CODE] != "" ? $_SESSION[CP_CODE] : $this->CP_CODE;

        // ��ǰ ���� �Խ��ǿ� ��ǰ�̹��� ���
        for($i=0; $i<sizeof($data_array[0][number]); $i++) {
						$goods_code = $data_array[0][goods_code][$i];
						$data_array[0][path][$i] = "http://".$_SESSION[udomain]."/?action=Detail&GoodsCode=".$goods_code;
            $data_array[0][file_name][$i] = $this->inst_main->inst_SelectGoods->Get_GoodsImageInfo($goods_code);
          // loop - End
         }

				$Img_URL = Img_Domain."/".$CP_CODE."/Goods";

        //  ������� ���� 2009-02-02 ������
        //  ������ ���� ������ ������ ��ȯ����
        $this->inst_BoardExtends->SwitchOPT(&$data_array[0]);

        $tpl->assign('trlist',$data_array[0]);	// tr����Ʈ -> record set
        $tpl->assign('inst_StringCon',$inst_StringCon);	// ���ڿ� ��Ʈ�� Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_otm',$inst_otm);				// ���ø��� output Ŭ���� �ν��Ͻ�
        $tpl->assign('inst_auth',$inst_auth);			// ���� ���� Ŭ���� �ν��Ͻ�
        $tpl->assign('essential',array('section'=>$section,'baction'=>$baction,'table_name'=>$table_name,'total_view'=>$total_view,'Img_URL'=>$Img_URL,'path'=>$path,'b_title'=>urlencode($_GET[b_title]),'board_no'=>$board_no));  // �ʼ� ������
        $tpl->print_('list');

        flush();

      break;

	  // ��ǰ�� �����
	  case "DelReview" :

			// ������ ��ǰ ����Ʈ�� �迭��
      $Goods_list = explode("|",$_POST[Str_list]);

      //  �迭���� �ΰ� ����
      Nulldel(&$Goods_list);

			$Array_in = $inst_Lupdate->ArrayToQuery($Goods_list,0);

			$query = "DELETE FROM $this->DataBase.Board_goods_review WHERE number IN(".$Array_in.");";
      $result = $mysql->execute($query);
			$Msg = "���� �Ǿ����ϴ�.";
      $inst_location->msg_go($inst_output->self_page."?baction=hugi&section=list&table_name=$table_name&as=BoardMan",$Msg);

	  break;

	  // ��ǰ�� ���� ����
	  case "ReviewState" :

			// �Ѿ�� ���� ����
			if($_POST[Toggle_value] == 0) {
				$state = 1;
			} else{
				$state = 0;
			}

			$query = "update $this->DataBase.Board_goods_review set state='".$state."' where number = '".$_POST[Code_value]."' ";
      $result = $mysql->execute($query);
      $inst_location->go($inst_output->self_page."?baction=hugi&section=list&table_name=$table_name&as=BoardMan");

	  break;

      //  ����Ʈ �ı� ����
      case "best_ok" :

        $B_no =	$_GET["no"];
        $best = $_GET["best_ok"];
        $table_name = $_GET["table_name"];
        $query = "update $this->DataBase.$table_name set best_ok='$best',Tag='1'  where b_no='$B_no';";
        $result = $mysql->execute($query);
         # action ==> view
        $inst_location->go($inst_output->self_page."?baction=list&section=good&table_name=$table_name&as=BoardMan");

      break;

      //  top ����
      case "top" :

        $B_no =	$_GET["no"];
        $top = $_GET["top"];
        $table_name = $_GET["table_name"];
        $query = "update $this->DataBase.$table_name set top='$top' where b_no='$B_no';";
        $result = $mysql->execute($query);
        if($table_name=='Board_gongji') { $inst_location->go($inst_output->self_page."?baction=list&section=gongji&table_name=$table_name&as=BoardMan"); }
        # action ==> view
        else { $inst_location->go($inst_output->self_page."?baction=list&section=good&table_name=$table_name&as=BoardMan"); }

      break;

      //  ���� �ٿ�
      case "download" :

        ob_end_clean();
        $filePath  = $_GET["url"]."/".$_GET["dnfilename"];
        header("Cache-control: private");

        if(!is_file($filePath)){

          $fileSize = $_GET["filesize"];
          header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
          header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
          header("Cache-Control: cache, must-revalidate");
          header("Cache-Control: post-check=0, pre-check=0", false);
          header("Pragma: no-cache");
          header("Content-type: application/force-download");

          //������ ���Ŀ� �°� ��������/�ƽ�Ű�� ������ ���������� üũ
          if(strstr($file_type, "plane")) { $openType = "r"; $dnflag = "inline"; }
          else{ $openType = "rb"; $dnflag = "attachment"; }

          if(strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")){
            Header("Content-type: application/octet-stream");
            Header("Content-Length: $fileSize"); // �̺κ��� �־� �־���� �ٿ�ε� ���� ���°� ǥ�� �˴ϴ�.
            Header("Content-Disposition: $dnflag; filename=$_GET[dnfilename]");
            Header("Content-Transfer-Encoding: binary");
            Header("Pragma: no-cache");
            Header("Expires: 0");
          }
          else{
            Header("Content-type: file/unknown");
            Header("Content-Length: $fileSize");
            Header("Content-Disposition: $dnflag; filename=$_GET[dnfilename]");
            Header("Content-Description: PHP3 Generated Data");
            Header("Pragma: no-cache");
            Header("Expires: 0");
          }

          $result = $this->Get_file_from_url($filePath);

          echo $result[0];
        }
        else{$inst_location->error("������ �ٿ������ �� �����ϴ�.");}

        // �ݵ�� exit�� �ɾ �޺κ��� ����� �����ش�.
        // �ڿ��� ����� �Ǹ� ���� �ٿ�� ������� �߻�
        exit;

      // ���̺� ���� ��
      default :

        // ó���� �Խ��� �����Ҷ��� ��� Ǯ���ش�.
        /*
        echo $inst_html->Print_head(JSURL,CSSURL);
        echo $inst_otm->Print_create_form();
        */

      break;


      // switch - End()
    }

  // function - End()
  }

  //-----------------------------------------------------------------------------
  //  ���� ���� �ܾ� ���³� : 2008-07-01 ������
  //  ��ü ���� ����)
  //  $URL_info =>
  //  [scheme] => http
  //  [host] => img1.playauto.co.kr
  //  [path] => /mtc/Goods/217d88e811X41527.jpg
  //  [port] => 80
  //-----------------------------------------------------------------------------
  function Get_file_from_url($url) {

    $URL_info = @parse_url($url);
    $URL_info[port] = 80;  //  ��Ʈ �߰�

    //  URL �����߿� �ϳ��� ������� ����
    if($reason = @array_search("",$URL_info)) { return "$reason : ���Է�"; }

    $Heder = "GET ".$URL_info[path]." HTTP/1.0\r\nHost: ".$URL_info[host]."\r\n\r\n";
    $inst_file = @fsockopen($URL_info[host], $URL_info[port], $errno, $errstr, 30);

    //  ���� ����
    if($inst_file) {
      $data = null;
      @socket_set_timeout($inst_file, 30);
      @fwrite($inst_file, $Heder);
      $body = false;
      while(!@feof($inst_file)) {
          $buffer = @fgets($inst_file, 128);
          if($body==true) $data .= $buffer;
          if($buffer=="\r\n") {$body = true;}
      }
      @fclose($inst_file);

      //  ���ϸ��� ���ؼ� �Ҵ�.
      $temp_ = @explode("/",$URL_info[path]);
      $file_name = $temp_[@count($temp_)-1];
      return array($data,$file_name);
    }
    //  ���� ����
    else { return "openerror : URL ���� ����"; }

  // funciton - End
  }


  //--------------------------
  //  ���� ���
  //--------------------------
  function Upload_file($inst_query,$inst_output,$inst_location,$_POST,$_FILES,$FileCount,$mysql,$section,$table_name,$U_id,$baction){

    // ���� ���
    $inst_ftp =	 new file_UDC(FileBaseDir,$this->inst_main->CP_CODE);

    for($i=0; $i < $FileCount; $i++){
      // upload ���� ���� ����
      $ftp_result = $inst_ftp->main($_FILES["AttFile"]["tmp_name"][$i],$_FILES["AttFile"]["name"][$i],$_FILES["AttFile"]["size"][$i],"upload","Board");

      // ����� ���ϸ�
      $real_file_name = $inst_ftp->return_file_name();

      // ���ε� ����� ������ ���ε� ������ ��ó��(��:5�� �ø��� 3��°���� �������� ���� 1,2��° ��鵵 ���� �ϱ� ����
      $inst_ftp->error_process($ftp_result,$real_file_name,$_FILES["AttFile"]["size"][$i]);

    //  loop -end
    }

    if($baction=="write_save"){
      // �Խù� ���
      $inst_query->Insert_record($inst_query,$inst_output,$inst_location,$_POST,$mysql,$section,$table_name,$inst_ftp->tmp_upload_files);
    }
    else{
      // ÷�������� �߰��� ��������� ���� �߰��� ���� ���ڿ��� ���� ����
      $_POST["update_filename"] = $this->Update_file_action($inst_query,$inst_ftp->tmp_upload_files,$_POST);

      // �Խù� ����
      $inst_query->Update_record($_POST,$mysql);
    }
    return "success";

  // function - End
  }

  //----------------------------
  //    ���� ����
  //----------------------------
  function Delete_file($data,$U_id){

    // ���� ��Ʈ��
    $inst_ftp =	 new file_UDC(FileBaseDir,$this->inst_main->CP_CODE);

    // ������ ��� �����Ҷ� ���� ����
    for($i=0; $i < count($data);$i++){
       $inst_ftp->main($filepath,$data[$i],$filesize,"delete","Board");
    }

  // function - End
  }

  //-------------------------------------------------------------------------------
  // DB�� Filename �ʵ忡�� | �����ڸ� �������� ���ϸ��� �и����ش�.
  // �и��ϸ鼭 �ٷ� global ���� $this->Tmp_delfiles �� ���ڿ��� ����ȴ�.
  //-------------------------------------------------------------------------------
  function Delete_file_repl_sub($tmp){
    for($i=0; $i < count($tmp); $i++){
      $tmp2	=	explode("|",$tmp[$i]);
      $this->Tmp_delfiles =	$this->Tmp_delfiles.$tmp2[0]."/";
    }

  // function - End
  }

  //-------------------------------------------------------------------------------
  // �θ� �Խù� ������ ���� ���õ��� ÷�����ϵ� �� �����ֱ� ���ؼ� ���ڿ���.
  //-------------------------------------------------------------------------------
  function Delete_file_repl($data){

    for($i=0; $i < count($data["filename"]); $i++){
      $tmp	 =	explode("/",$data["filename"][$i]);
      $this->Delete_file_repl_sub($tmp);
    }

    // ������ / �� ���ְ� �迭�� ������ش�..
    $this->Tmp_delfiles	=	substr($this->Tmp_delfiles,0,strlen($this->Tmp_delfiles)-1);

    // �迭�� ������ش�.
    $this->Tmp_delfiles	=	explode("/",$this->Tmp_delfiles);

  // function - End
  }

  //-----------------------------------------------------------------------------
  //  ÷�ε� ������ ������������ ������� �������� ������ �������ش�.
  //  �� ������ ���� : 2009-01-13 ������
  //-----------------------------------------------------------------------------
  function GetAddfileType($section, $table_name) {

    switch($section) {
      case "good" : if($table_name=='Board_review') { return "img"; } else { return "none"; } break;
      case "normal" : return "none"; break;
      case "file" : return "link"; break;
    }

  // funciton - End
  }

  //----------------------------------------------------------------
  //   ÷������ �߰��� ���Ǵ³�
  //----------------------------------------------------------------
  function Return_add_filename($original_filestring,$new_string){
    return $original_filestring."/".$new_string;
  }

  //--------------------------------------------------------------------------
  // ���� ���� �Ҷ� ���� �߰� ���� ó���κ�
  //--------------------------------------------------------------------------
   function Update_file_action($inst_query,$_FILES,$_POST){
     // Ű���� ������ �ؼ� �־��ش�.
     $new_string	=	$inst_query->Filetostring($_FILES);
     $update_string = $this->Return_add_filename($_POST["originalfiles"],$new_string);
     return $update_string;
  }

  //-----------------------------------------------------------------------------
  //  �Խ��� ��� �ɼ� �������ִ³�
  /*
  //  ������� ���� 2009-02-02 ������
  //  ������ ���� ������ ������ ��ȯ����
  $this->inst_BoardExtends->SwitchOPT(&$data[writer_name][0],'writer');
  $this->inst_BoardExtends->SwitchOPT(&$data[regi_date][0],'dateview');
  */
  //-----------------------------------------------------------------------------
  function SwitchOPT($data,$section='list') {

    // ��� ������ ������ ���
    if(is_array($data) && $section=='list') {

      foreach($data as $key=>$val) {
        foreach($val as $key2=>$val2) {
          //  �ۼ���
          if($key2=='writer_name') { if($this->BoardOPT[writer]=='id') { $data[$key][$key2] = $data[$key]['writer_id']=='_ADMIN_' ? '������' : $data[$key]['writer_id']; } }
          elseif($key2=='regi_date') { $data[$key][$key2] = $this->inst_String->ext_date($data[$key][$key2],trim($this->BoardOPT[dateview])); }
        // loop - End
        }
      // loop - End
      }

    }
    //  ���� ���
    else {

      //  ������ ���� ����
      if(trim($data[writer_name][0])!="") {
        if($this->BoardOPT[writer]=='id') { $data[writer_name][0] = $data[writer_id]['writer_id'][0]=='_ADMIN_' ? '������' : $data['writer_id'][0]; }
        $data[regi_date][0] = $this->inst_String->ext_date(&$data[regi_date][0],$this->BoardOPT[dateview]);
      }
    }

  // funciton - End
  }

// class - End()
}
?>