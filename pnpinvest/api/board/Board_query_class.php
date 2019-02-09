<?
class Board_query{
// class - Start()

var $DataBase;

//---------------------------------
//            ������
//---------------------------------
function Board_query($DataBase){
  $this->DataBase = $DataBase;
}

//--------------------------------------------------------------------------------------------
// select �ʵ忡 �ɼǺ��� �ٴ� �ʵ带 ���ڿ��� ����
//--------------------------------------------------------------------------------------------
function Make_select_tails($section){

  switch($section){
    case "level" :  return ", a.Touser "; break;
    case "file" : return ", a.Filename "; break;
    case "normal"	: return "";  break;
    case "good"	: return ", a.G_code"; break;
    case "turm" : return ", a.Start_time,  a.End_time,  a.Charger,  a.Collaborator "; break;
    case "gongji" : return ", a.PopYN, a.G_width, a.G_height, a.P_top, a.P_left"; break;
  }

// function - End
}

//------------------------------------------------------------------------------------
//  �Խ��� ���¿� ���� ���� �ʵ带 �ٲ��ش�.
//------------------------------------------------------------------------------------
function Make_list_field($section,$table_name,$limit,$Field,$Key,$UID='',$code=""){

  // �ɼǺ��� ���̳��� �ʵ���� ���� �� �ְ� �������� ������ش�.
  $Fields_last	=	$this->Make_select_tails($section);
  $com_table = $table_name."_c";

  // ��ǰ�ı� ����Ʈ
  if($table_name=="Board_goods_after") {
    // �˻�� ������
    if(!$Key){
      return sprintf("SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top, a.G_sati, b.Goods_name, c.File_name, b.Sale_cost, d.Made_by ,a.G_code ,a.best_ok %s
      FROM
      $this->DataBase.%s as a
      LEFT JOIN $this->DataBase.Goods_Basic as b ON(a.G_code=b.Goods_no)
      LEFT JOIN $this->DataBase.Goods_img as c ON(a.G_code=c.Goods_no)
      LEFT JOIN $this->DataBase.Goods_Extra as d ON(a.G_code=d.Goods_no)
      WHERE c.Size = 2
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc,a.B_no desc  LIMIT $limit",
      $Fields_last,$table_name);
    }
    // �˻�� ������
    else{
      return "SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top, a.G_sati, b.Goods_name, c.File_name, b.Sale_cost, d.Made_by , a.G_code ,a.best_ok $Fields_last
      FROM
      $this->DataBase.$table_name as a
      LEFT JOIN $this->DataBase.Goods_Basic as b ON(a.G_code=b.Goods_no)
      LEFT JOIN $this->DataBase.Goods_img as c ON(a.G_code=c.Goods_no)
      LEFT JOIN $this->DataBase.Goods_Extra as d ON(a.G_code=d.Goods_no)
      WHERE c.Size = 2 and $Field like '%$Key%'
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc LIMIT $limit";
    }
  }
  // ��ǰ���� ����Ʈ �߰� : 2009-06-10 ������
  // �۹��� �ϴ� ��û��ü�� ������. ���� ��ü�۹Խ� ��ü�ڵ� �����ؾ���. DB���� best_ok�߰��ؾ���.
  elseif($table_name=="Board_review" && ($this->CP_CODE=='mtc' || $this->CP_CODE=='thecraze')) {
    // �˻�� ������
    if(!$Key){
      $UID = $UID == "" ? "":" where ( Writer_id = '".$UID."' or Touser = '".$UID."' )";
      return sprintf("SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top, a.best_ok %s
      FROM
      $this->DataBase.%s as a
      $UID
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc,a.B_no desc  LIMIT $limit",
      $Fields_last,$table_name);
    }
    // �˻�� ������
    else{
      $UID = $UID == "" ? "":" and ( Writer_id = '".$UID."' or Touser = '".$UID."' )";
      return "SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top, a.best_ok $Fields_last
      FROM
      $this->DataBase.$table_name as a
      WHERE
      $Field like '%$Key%' $UID
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc LIMIT $limit";
    }
  }
  // ��ǰ���� ����Ʈ
  elseif($table_name=="Board_goods_qna") {
    // �˻�� ������
		$code = $code == "" ? "" :" and G_code = '".$code."' ";
    if(!$Key){
      $UID = $UID == "" ? "":" and ( Writer_id = '".$UID."' or Touser = '".$UID."' )";
      return sprintf("SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top, a.G_sati, b.Goods_name, c.File_name, b.Sale_cost, d.Made_by, a.G_code  %s
      FROM
      $this->DataBase.%s as a
      LEFT JOIN $this->DataBase.Goods_Basic as b ON(a.G_code=b.Goods_no)
      LEFT JOIN $this->DataBase.Goods_img as c ON(a.G_code=c.Goods_no)
      LEFT JOIN $this->DataBase.Goods_Extra as d ON(a.G_code=d.Goods_no)
      WHERE c.Size = 2  $UID $code
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc,a.B_no desc  LIMIT $limit",
      $Fields_last,$table_name);
    }
    // �˻�� ������
    else{
      $UID = $UID == "" ? "":" and ( Writer_id = '".$UID."' or Touser = '".$UID."' )";
      return "SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top, a.G_sati, b.Goods_name, c.File_name, b.Sale_cost, d.Made_by, a.G_code $Fields_last
      FROM
      $this->DataBase.$table_name as a
      LEFT JOIN $this->DataBase.Goods_Basic as b ON(a.G_code=b.Goods_no)
      LEFT JOIN $this->DataBase.Goods_img as c ON(a.G_code=c.Goods_no)
      LEFT JOIN $this->DataBase.Goods_Extra as d ON(a.G_code=d.Goods_no)

      WHERE c.Size = 2 and $Field like '%$Key%' $UID $code
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc LIMIT $limit";
    }
  }
  // ��ǰ�ı� ����Ʈ
  elseif($table_name=="Board_goods_review") {
		return sprintf("
			SELECT * , (select a.File_name from $this->DataBase.Goods_img as a where a.Goods_no=goods_code and a.Size=2 limit 1 ) as file_name
			FROM $this->DataBase.%s ORDER BY `number` desc, `regi_date` desc LIMIT %s
		", $table_name,$limit);
  }
  else {

    // ��б� �߰� : 2010-03-04 ������
    if($this->CP_CODE=='dochiqueen' && ereg("Board_[0-9]+",$table_name)) { $Secret_yn = ", a.Secret, a.Touser "; }
    else {$Secret_yn = "";}

    // �˻�� ������
    if(!$Key){
      $UID = $UID == "" ? "":" where ( Writer_id = '".$UID."' or Touser = '".$UID."' )";
      return sprintf("SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top $Secret_yn $gongji_filed %s
      FROM
      $this->DataBase.%s as a
      $UID
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc,a.B_no desc  LIMIT $limit",
      $Fields_last,$table_name);
    }
    // �˻�� ������
    else{
      $UID = $UID == "" ? "":" and ( Writer_id = '".$UID."' or Touser = '".$UID."' )";
      return "SELECT
      (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top $Secret_yn $gongji_filed $Fields_last
      FROM
      $this->DataBase.$table_name as a
      WHERE
      $Field like '%$Key%' $UID
      ORDER BY
      a.Top desc ,a.Fid desc ,a.Thread asc LIMIT $limit";
    }
  }

// function - End
}

//------------------------------------------
// ����¡�� ���� ������
//------------------------------------------
function Get_pagecount($table_name,$Field,$Key,$UID){
	if($table_name=='Board_goods_review') {
		$query = "SELECT count(number) as total FROM $this->DataBase.$table_name";
	}
	else {
		$UID1 = $UID == "" ? "":" where (Writer_id = '".$UID."' or Touser = '".$UID."')";
		$UID2 = $UID == "" ? "":" and (Writer_id = '".$UID."' or Touser = '".$UID."')";
		if(!$Key){$query = "SELECT count(B_no) as total FROM $this->DataBase.$table_name".$UID1;}
		else{$query = "SELECT count(B_no) as total FROM $this->DataBase.$table_name WHERE $Field like '%$Key%'".$UID2;}		
	}
  return $query;
}


//------------------------------------------
// ����¡�� ���� ������ 2011-06-01 ��ǰ�ڵ� �߰� ������
//------------------------------------------
function Get_pagecount2($table_name,$Field,$Key,$UID,$code){
  $UID1 = $UID == "" ? "":" where (Writer_id = '".$UID."' or Touser = '".$UID."')";
  $UID2 = $UID == "" ? "":" and (Writer_id = '".$UID."' or Touser = '".$UID."')";
	if($UID1 != "") {
		$code = $code == "" ? "" : " and G_code = '".$code."' ";
	} else{
		$code = $code == "" ? "" : " WHERE G_code = '".$code."' ";
	}
  if(!$Key){$query = "SELECT count(B_no) as total FROM $this->DataBase.$table_name".$UID1.$code;}
  else{$query = "SELECT count(B_no) as total FROM $this->DataBase.$table_name WHERE $Field like '%$Key%'".$UID2.$code;}

  return $query;
}


//-----------------------------------------------------------------
//                      �Ʒ����� select ����
//-----------------------------------------------------------------

//------------------------
// ����Ʈ ����
//------------------------
function Select_list($query,$mysql){

	$data	= $mysql -> fetch($query);
  $rows	= $mysql -> rownum;
  return array($data,$rows);

// function - End
}

//------------------------------------------
// ���� �ٿ�ε� ��ũ
//------------------------------------------
function Query_filelink($fstring,$Writer_id,$self_page,$baction_file){

  // a��ũ���� ����� �迭
  $alink	=	array();
  $fsize	=	array();
  $tmp		=	explode("/",$fstring);
  $Img_Domain = Img_Domain;

  for($i=0; $i < count($tmp); $i++){

    if($tmp[$i] == "") continue;

    $sub_tmp	=	explode("|",$tmp[$i]);

      switch($baction_file) {

        //  ��ũ ����
        case "link" :
          $url = $Img_Domain."/".$this->CP_CODE."/Board";
          $alink["filename"][]	= "<A HREF=\"board_download.php?as=BoardMan&action=Board&baction=download&filesize=".$sub_tmp[1]."&url=$url&dnfilename=".$sub_tmp[0]."\"><U>".$sub_tmp[0]."</U></A>";
        break;

        //  �׳� �̹�����ü �迭�� ����
        case "file" : $alink["filename"][]	= $sub_tmp[0]; break;

        //  �׳� �̹�����ü �迭�� ����
        case "none" : $alink["filename"][]	= $sub_tmp[0]; break;

        //  �̹��� ��� ���·� ����
        case "img" :

          $url = $Img_Domain."/".$this->CP_CODE."/Board";
          $filesize = @getimagesize(RootBase.ImageBase."/".$this->CP_CODE."/Board/".$sub_tmp[0]);
          if($filesize[0] > 700) { $img_with = " width='700' "; }
          else { $img_with = "";}
          $alink['filename'][] = sprintf("<img src='%s/%s' %s alt='%s'>",$url,urlencode($sub_tmp[0]),$img_with,$sub_tmp[0]);
        break;

      // switch - End
      }
    $alink["size"][]	=	$sub_tmp[1];

  // loop - End
  }

  // ���� �����κ��϶� ���� ���� ���ڿ��� �������ش�.
  if($baction_file=="file"){$alink["origin"][0]=$fstring;}
  return $alink;

// function - End
}

//-------------------------------------
// ÷������ ��� ����
//-------------------------------------
function Query_reply_files($mysql,$Thread,$table_name,$Fid,$B_no){

  //  ��ǰ ���� �Խ��� �ϰ��
  if($table_name!='Board_review' && $table_name!= 'Board_goods_qna' && $table_name!='Board_goods_after') { $FileField = "Filename"; }
  else { $FileField = "G_code as Filename"; }

  // ������ ��� �ش� Fid�� ��� ÷�����ϸ��� ����
  if($Thread=="A"){
    $query	=	"SELECT $FileField FROM $this->DataBase.$table_name WHERE Fid='$Fid' ";
    $mysql->reset_rownum();
    $data	=	$mysql->fetch_key($query);
  }
  // ����� ��� �ش� ���� ÷�����ϸ� ����
  else{
    $query	=	"SELECT $FileField FROM $this->DataBase.$table_name WHERE B_no='$B_no' ";
    $mysql->reset_rownum();
    $data	=	$mysql->fetch_key($query);
  }
  return $data;

// function - End
}

//-------------------------
//  �� ����
//------------------------
function Select_view($mysql,$B_no,$table_name,$section,$self_pager,$U_id,$baction_file){

  $inst_auth	=	new Auth();

  // �ɼǺ��� ���̳��� �ʵ���� ���� �� �ְ� �������� ������ش�.
  $Fields_last	=	$this->Make_select_tails($section);

  if($section == "good") {

    // ���� �Խ��� ����
    if($table_name == "Board_review") {
      $query	=	sprintf("SELECT a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Tag, a.Top, a.G_sati, a.Writer_id %s
      FROM $this->DataBase.%s as a WHERE a.B_no='$B_no'",$Fields_last,$table_name);
    }
    // ��ǰ����, �ı� ����
    else {
      $query	=	sprintf("SELECT a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Tag, a.Top, a.G_sati, a.email, b.Goods_name, d.Made_by, b.Sale_cost ,b.Goods_no , c.File_name %s
      FROM $this->DataBase.%s as a
       LEFT JOIN $this->DataBase.Goods_Basic as b ON(a.G_code=b.Goods_no)
       LEFT JOIN $this->DataBase.Goods_img as c ON(a.G_code=c.Goods_no)
       LEFT JOIN $this->DataBase.Goods_Extra as d ON(a.G_code=d.Goods_no)

      WHERE a.B_no='$B_no' and c.Size = 1 ",$Fields_last,$table_name);
    }
  }
  else {
    $query	=	sprintf("SELECT a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Tag, a.Writer_id %s
    FROM $this->DataBase.%s as a WHERE a.B_no='$B_no' ",$Fields_last,$table_name);
  }

  $data	=	$mysql->fetch_key($query);

  //  ���� �Խ����ϰ�� / ���� ���� �ʵ带 G_CODE��³����� ��ü�س��� ���۳𶧹���
  //  �迭�� �ѹ� �������ش�. 2009-01-13 : ������
  if($table_name=='Board_review') { $data[filename][0] = $data[g_code][0]; }

  // �ش�Խù��� ���������� �Ǵ�
  $auth = $inst_auth->Authcheck($section,$data["touser"][0],$U_id,$data["writer_id"][0]);
  if($auth!="ok"){echo "�Խù� ���� ������ �����ϴ�.";$mysql->close();exit;}

  // ÷������ �Խ����� ��� ÷�����ϸ� ��ũ�� �ɾ��ִ³� ( link �ϰ�� ��ũ�� �迭�� ���� , file �ϰ�� ���ϸ��� �迭�� ����)
  $options_array	 =	$this->Query_filelink($data["filename"][0],$data["writer_id"][0],$self_page,$baction_file);

  // ���ϸ� ���ڿ��� ���� ������� ���ϸ����� �и��� �迭���·� �ٲ㼭 �״�� �־��ش�.
  // ������ �迭���� �ٲ�ġ��
  // ������ $data["Filename"]�� �׳� �迭�̿�����
  // �ٽ� ���� �������³��� �迭�� �迭�̴�.
  $data["filename"]	=	$options_array;
  return $data;

// function - End
}


//----------------------------------
//  ���� ����Ʈ ����
//----------------------------------
function Select_sublist($mysql,$fid,$section,$table_name){

  // �ɼǺ��� ���̳��� �ʵ���� ���� �� �ְ� �������� ������ش�.
  $Fields_last	=	$this->Make_select_tails($section);
  $com_table = $table_name."_c";
  $query = sprintf("SELECT
  (select count(C_no) from $this->DataBase.$com_table where B_no=a.B_no) as c_count ,a.B_no, a.Title, a.Writer_id, a.Writer_name, a.Regi_date, a.Ref, a.Contents, a.Fid, a.Thread, a.Top %s FROM $this->DataBase.%s as a WHERE a.Fid='$fid' ORDER BY a.Top desc ,a.Fid desc ,a.Thread asc,a.B_no desc",$Fields_last,$table_name);

  $data	= $mysql -> fetch($query);
  $rows	= $mysql -> rownum;

  return array($data,$rows);
// function - End
}


//-------------------------------------
// �ڸ�Ʈ ������ ����
//-------------------------------------
function Select_comm($mysql,$B_no,$table_name){

  $query	=	sprintf("SELECT * FROM $this->DataBase.%s WHERE B_no='%s' ORDER BY C_no asc ",$table_name."_c",$B_no);
  $mysql->reset_rownum();

  return $mysql->fetch($query);

}

//--------------------------------------
// ������ , ������ ã��
//--------------------------------------
function Find_nprecord($mysql,$No,$table_name,$Top){
// mysql 4.1 �̻� �������� �Ǹ� ������ function�� ���ο��� ���� �ٲ��ش�.

  // ���� ���� �ƴҰ��
  if($Top!="o"){$Where_last	 =	" AND Top!='o' ";}
  // ������
  else{$Where_last	 =	" AND Top='o' ";}

  // ������
  $query		=	"SELECT min(B_no) as No FROM $this->DataBase.$table_name WHERE B_No > $No AND length(Thread) <= 1 $Where_last";
  $data_P	=	$mysql->fetch_key($query);

  // ������
  $query		=	"SELECT max(B_no) as No FROM $this->DataBase.$table_name WHERE B_No < $No AND length(Thread) <= 1 $Where_last";
  $data_N	=	$mysql->fetch_key($query);
  return array($data_P["no"][0],$data_N["no"][0]);

// function - End
}


//--------------------------------------------------------------------------------------------
//                              insert , update  ����
//--------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------
// ÷�� ���ϸ� ���ڿ��� ������ִ³�
//--------------------------------------------------------------------------------------------
function Filetostring($Filearray){

  // ÷������ ����
  $count	=	count($Filearray["filename"]);
  $file_name	=	"";
  for($i = 0; $i < $count; $i++){
    // �������� / ���� ���ؼ�
    if($i == $count-1){$file_name	=	$file_name.$Filearray["filename"][$i]."|".$Filearray["size"][$i];}
    else{$file_name	=	$file_name.$Filearray["filename"][$i]."|".$Filearray["size"][$i]."/";}

  // loop - End
  }
  return $file_name;

// function - End
}

//--------------------------------------------------
// �亯 ������ ���� ����
//--------------------------------------------------
function Make_thread($Fid,$Thread,$mysql,$table_name){

  $query="
  SELECT right(Thread,1) as Thread
  FROM $this->DataBase.$table_name
  WHERE Fid=$Fid and length(Thread) = length('$Thread')+1  and locate('$Thread',Thread)=1
  ORDER BY Thread desc limit 1";

  $data	=	$mysql->fetch_key($query);
  if($mysql->rownum > 0){
    $thread_foot = ++$data["thread"][0];
    $new_thread=$Thread . $thread_foot;
  }
  else{
    $new_thread=$Thread."A";
  }

  return $new_thread;

// function - End
}

//---------------------------------------
//  �Խù� ��� switch
//---------------------------------------
function Insert_record($inst_query,$inst_output,$inst_location,$_POST,$mysql,$section,$table_name,$Filearray){

  $Uname_temp = explode("[",$_SESSION[UNAME]);
  $Title = $_POST["Title"];
  $Contents = $_POST["Contents"];
  $Top = $_POST["Top"];
  $Tag = $_POST["Tag"];
  $U_id = $_POST["U_id"];
  $U_name =	$_POST["U_name"] !="" ? $_POST[U_name] : $Uname_temp[0];
  $Touser	=	$_POST["addstring"];
  $Thread	=	$_POST["Thread"];
  $Fid = $_POST["Fid"];
  $as = $_POST["as"];

  $Secret = $_POST["secret"]; // ��б� ���� y�� �Ѿ�� : 2010-03-04 ������

  if($_POST["Start_time"]!=""){
  $Start_time = $_POST["Start_time"];}
  else{ $Start_time = "2000-01-01 00:00:00";}

  if($_POST["End_time"]!=""){
  $End_time = $_POST["End_time"];}
  else{ $End_time = "2000-01-01 00:00:00";}

  $dura = intval($_POST["dura"]);
  $Collaborator = $_POST["Collaborator"];
  $Charger = $_POST["charger"];

  $G_code = $_POST[GoodsCode];
  $G_sati = $_POST[sati];

  // ���ο��� ������ ���
  if($_POST['as'] != "") {
    $U_name = "������";
    $admin = "Y";
    $U_id = "_ADMIN_";
  }

  $PopYN = $_POST["popup"];

  $G_width = $_POST["G_width"];
  $G_height = $_POST["G_height"];

  $P_top = $_POST["P_top"];
  $P_left = $_POST["P_left"];

  $Touser = $_POST['as'] == "" ? $U_id:$_POST[writer];
  $Filename	=	"";

  //-----------------------------------------------------------
  // �� ���� ÷�ΰ� �Ǿ��� ����.
  // ÷�����ϵ��� �迭�� �޾Ƽ� ���ڿ��� ����
  if($Filearray!="nofile"){$Filename	=	$this->Filetostring($Filearray);}
  //-----------------------------------------------------------

  // ��ǰ���� �Խ����� ��ǰ�ڵ忡 �̹��� ����
  $G_code = $table_name == "Board_review" ? $Filename:$G_code;

  // �����尡 ���ų� �йи� ���̵� ������ ����� �ƴϰ� �ܼ� �۾��� => ������� A���ȴ�.
  if(!$Thread || !$Fid){
    $Thread	=	"A";
  }
  else{
    $Thread	=	$this->Make_thread($Fid,$Thread,$mysql,$table_name);
  }

  // �йи� ���̵� ���� ���
  if(!$Fid){
    //--------------------------------------------------------------
    // �� mysql 4.1 �̻� �������� �ٲ�� ���������� �ۼ�
    $query	=	"SELECT max(`Fid`)+1 as Fid FROM $this->DataBase.$table_name WHERE 1";
    $data	=	$mysql->fetch_key($query);
    $Fid	=	$data["fid"][0];
    if(!$Fid){$Fid="1";} // ù���϶� Fid�� ������ 1�� ����
    //--------------------------------------------------------------
  }

  switch($section){
    case "file" :
      $query	=	"INSERT INTO
      $this->DataBase.`$table_name` (`B_no` ,`Title` ,`Writer_id` ,`Writer_name` ,`Regi_date` ,`Ref` ,`Contents` ,`Fid` ,`Thread` ,`Top` ,`Tag`,`Filename` )
      VALUES (NULL , '$Title', '$U_id', '$U_name', NOW( ) , '0', '$Contents', '$Fid', '$Thread', '$Top','$Tag','$Filename');";
    break;

    case "normal" :
      // ��б� ��� �߰� : 2010-03-04 ������
      if($Secret=='y') {
        $query	=	"INSERT INTO
        $this->DataBase.`$table_name` (`B_no`, `Title`, `Writer_id`, `Writer_name`, `Regi_date`, `Ref`, `Contents`, `Fid`, `Thread`, `Top`, `Tag`, `Touser`, `Secret` )
        VALUES (NULL, '$Title', '$U_id', '$U_name', NOW( ), '0', '$Contents', '$Fid', '$Thread', '$Top', '$Tag', '$Touser', '$Secret');";
      }
      // �Ϲ����� ���
      else {
        $query	=	"INSERT INTO
        $this->DataBase.`$table_name` (`B_no` ,`Title` ,`Writer_id` ,`Writer_name` ,`Regi_date` ,`Ref` ,`Contents` ,`Fid` ,`Thread` ,`Top` ,`Tag`, `Touser` )
        VALUES (NULL , '$Title', '$U_id', '$U_name', NOW( ) , '0', '$Contents', '$Fid', '$Thread', '$Top','$Tag','$Touser');";
      }
    break;

    case "good" :
      $query	=	"INSERT INTO
      $this->DataBase.`$table_name` (`B_no` ,`Title` ,`Writer_id` ,`Writer_name` ,`Regi_date` ,`Ref` ,`Contents` ,`Fid` ,`Thread` ,`Top` ,`Tag` ,`G_code` ,`G_sati`, `Touser` )
      VALUES (NULL , '$Title', '$U_id', '$U_name', NOW( ) , '0', '$Contents', '$Fid', '$Thread', '$Top','$Tag', '$G_code','$G_sati','$Touser');";
    break;

    case "gongji" :
      $query	=	"INSERT INTO
      $this->DataBase.`$table_name` (`B_no` ,`Title` ,`Writer_id` ,`Writer_name` ,`Regi_date` ,`Ref` ,`Contents` ,`Fid` ,`Thread` ,`Top` ,`Tag`, `Touser`, `PopYN`, `G_width`, `G_height`, `P_top`, `P_left` )
      VALUES (NULL , '$Title', '$U_id', '$U_name', NOW( ) , '0', '$Contents', '$Fid', '$Thread', '$Top','$Tag','$Touser','$PopYN','$G_width','$G_height','$P_top','$P_left');";
    break;

    case "level" :
      $query	=	"INSERT INTO
      $this->DataBase.`$table_name` (`B_no` ,`Title` ,`Writer_id` ,`Writer_name` ,`Regi_date` ,`Ref` ,`Contents` ,`Fid` ,`Thread` ,`Top` ,`Tag` ,`Touser`)
      VALUES (NULL , '$Title', '$U_id', '$U_name', NOW( ) , '0', '$Contents', '$Fid', '$Thread', '$Top','$Tag','$Touser');";
    break;

    case "turm" :
      $query	=	"INSERT INTO
      $this->DataBase.`$table_name` (`B_no` ,`Title` ,`Writer_id` ,`Writer_name` ,`Regi_date` ,`Ref` ,`Contents` ,`Fid` ,`Thread` ,`Top` ,`Tag` ,`Start_time`,`End_time`,`Charger`,`Collaborator`)
      VALUES (NULL , '$Title', '$U_id', '$U_name', NOW( ) , '0', '$Contents', '$Fid', '$Thread', '$Top','$Tag','$Start_time','$End_time','$Charger','$Collaborator');";
    break;

  // switch - End
  }

  $mysql->execute($query);

  // �������� ���Ͽ� ����
  // DB�� ��ȯ : 2011-03-02 ������
  //if($_POST[table_name] == "Board_gongji") {
  //  $this->inst_SelectDisplay->PopUpGongJi(&$this->inst_FileIO,'');
  //}

  $action = $_POST[action] == "" ? "Board":$_POST[action];
  // action ==> list

  // ��ǰ���� ��Ͻÿ��� ������������
  if($_POST[table_name] == "Board_goods_qna" && $admin != "Y") {
    $inst_location->go($inst_output->self_page."?action=MyBoard&section=good&table_name=Board_goods_qna&baction=list&path=mypage");
  }
  // �ϹݰԽ���
  else {
    $inst_location->go($inst_output->self_page."?action=$action&baction=list&table_name=$table_name&section=$section&as=$as&path=".$_POST[path]);
  }


// function - End
}

//------------------------------------
// �Խù� ���� switch
//------------------------------------
function Update_record($POST,$mysql){

  $section = $POST["section"];
  $table_name = $POST["table_name"];
  $B_no	 = $POST["No"];
  $U_id	 = $POST["U_id"];
  $U_level = $POST["U_level"];
  $U_name = $POST["U_name"];
  $Writer_name = $POST["Writer_name"];
  $addstring	 = $POST["addstring"];
  $Charger	 = $POST["charger"];
  $Collaborator	 = $POST["Collaborator"];
  $Start_time	 = $POST["Start_time"];
  $End_time	 = $POST["End_time"];
  $Top	 = $POST["Top"];
  $Tag	 = $POST["Tag"];
  $Title	 = $POST["Title"];
  $Contents	 = $POST["Contents"];
  $Regi_date	 = $POST["Regi_date"]." 00:00:00";
  $End_time	 = $POST["End_time"];
  $update_filename = $POST["update_filename"]; // ������Ʈ�� ���ϸ���Ʈ ���ڿ�
  $PopYN = $_POST["popup"];       // ���������� �˾�����
  $G_width = $POST["G_width"];   // ���������� �˾�â ���� �ȼ�
  $G_height = $POST["G_height"]; // ���������� �˾�â ���� �ȼ�
  $P_top = $POST["P_top"];   // ���������� �˾�â ��ġ top
  $P_left = $POST["P_left"]; // ���������� �˾�â ��ġ left
  $Sati = $POST["sati"]; // ��ǰ�ı� ������

  $Secret = $_POST["secret"]; // ��б� ���� y�� �Ѿ�� : 2010-03-04 ������

  // ���ο��� ������ ���
  if($POST['as'] != "") { $U_name = "������"; $U_id = "_ADMIN_"; }

  // �����ڸ� ���� ����� ������� ���� 2008-08-29 : ������
  $admin_name = $U_name == "������" ? "":"AND Writer_id='$U_id'";

  // ��ǰ���� �̹��� ����
  $g_code = ($table_name == "Board_review" && $update_filename != "") ? ", `G_code`='$update_filename'" : "";

  // ���ǿ� ���� �������� ����
  switch($section){

    // ���ϸ� ���� ����.
    case "fileonly" :
    break;

    case "level" :
      $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag' ,Touser='$addstring'
      WHERE B_no='$B_no' AND Writer_id='$U_id'";
      $mysql->execute($query);
    break;

    case "turm" :
      $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top',
      `Tag`='$Tag',Charger='$Charger',Collaborator='$Collaborator',Start_time='$Start_time',End_time='$End_time'
      WHERE B_no='$B_no' AND Writer_id='$U_id'";
      $mysql->execute($query);
    break;

    // ���� �Խù��� ������ �ϹݰԽù��� ������ ���� ó�����ش�.
    case "normal" :
      // ��б� �߰� : 2010-03-05 ������
      if($Secret!='') {
        $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag', `Secret`='$Secret'
        WHERE B_no='$B_no' $admin_name";
      }
      else {
        $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag'
        WHERE B_no='$B_no' $admin_name";
      }
      $mysql->execute($query);
    break;

    case "gongji" :
      $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag', `PopYN`='$PopYN', `G_width`='$G_width', `G_height`='$G_height', `P_top`='$P_top', `P_left`='$P_left'
      WHERE B_no='$B_no' $admin_name";
      $mysql->execute($query);

      // �������� ���Ͽ� ����
      // DB�� ��ȯ : 2011-03-02 ������
      //$this->inst_SelectDisplay->PopUpGongJi(&$this->inst_FileIO,'');

    break;

    case "good" :

      //  ������ ������ ���
      if($POST['as'] == 'BoardMan') {
      $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag', `G_sati`='$Sati',Regi_date='$Regi_date',Writer_name='$Writer_name' $g_code WHERE B_no='$B_no' $admin_name";
      }
      //  �Ϲ� ����
      else {
        $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag', `G_sati`='$Sati' $g_code WHERE B_no='$B_no' $admin_name";
      }

      $mysql->execute($query);
    break;

    case "file" :
      $query = "UPDATE $this->DataBase.$table_name SET `Title`='$Title' , `Contents`='$Contents', `Top`='$Top', `Tag`='$Tag', Filename='$update_filename'
      WHERE B_no='$B_no' $admin_name";
      $mysql->execute($query);
    break;

  // switch - End
  }

// function - End
}

//  ���̺� ����  ����
function Create_board_list($mysql){

  $query = "
  CREATE TABLE IF NOT EXISTS $this->DataBase.`Board_list` (
  `idx` int(11) NOT NULL auto_increment,
  `table_name` varchar(20) NOT NULL default '',
  `board_name` varchar(128) NOT NULL default '',
  `board_type` varchar(20) NOT NULL default '',
  `regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idx`),
  UNIQUE KEY `table_name` (`table_name`)
  ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1 ;
  ";
  $mysql->execute($query);

// function - End
}

//-------------------------------------------
// �Խ��� DB���̺� ����
//-------------------------------------------
function Create_table($section,$post,$mysql){

  $table_name = $post[table_name];

  // �Խ��� ���̺��
  if($table_name == "") {
    $table_name = "Board_".rand(1000,9999).rand(1000,9999);
  } else {
    $table_name = "Board_".$table_name;
  }

  $post[table_name] = $table_name;

  // �ڸ�Ʈ ���̺��
  $Comment_name	=	sprintf("%s_c",$table_name);

  // section ����
  switch($section){
    // switch - Start()

    // ���� ÷�� ����
      case "file" :
    $query = "
    CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
    `B_no` int(11) NOT NULL auto_increment,
    `Title` varchar(255) NOT NULL default '',
    `Writer_id` varchar(40) NOT NULL default '',
    `Writer_name` varchar(16) NOT NULL default '0',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Ref` int(11) NOT NULL default '0',
    `Contents` text NOT NULL,
    `Fid` int(11) NOT NULL default '1',
    `Thread` varchar(10) NOT NULL default 'A',
    `Top` char(1) NOT NULL default '',
    `Tag` char(1) NOT NULL default '',
    `Filename` text NOT NULL,
    PRIMARY KEY  (`B_no`),
    KEY `Writer_id` (`Writer_id`),
    KEY `Thread` (`Thread`),
    KEY `Fid` (`Fid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);

    $query	=	"
    CREATE TABLE IF NOT EXISTS $this->DataBase.$Comment_name(
    `C_no` int(11) NOT NULL auto_increment,
    `B_no` int(11) NOT NULL default '0',
    `C_ID` varchar(40) NOT NULL default '',
    `C_Name` varchar(40) NOT NULL default '',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Comment` text NOT NULL,
    PRIMARY KEY  (`C_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);
    break;

    //  �ӿ� ���� ����
    case "level" :
    $query = "
    CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
    `B_no` int(11) NOT NULL auto_increment,
    `Title` varchar(255) NOT NULL default '',
    `Writer_id` varchar(40) NOT NULL default '',
    `Writer_name` varchar(16) NOT NULL default '0',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Ref` int(11) NOT NULL default '0',
    `Contents` text NOT NULL,
    `Fid` int(11) NOT NULL default '1',
    `Thread` varchar(10) NOT NULL default 'A',
    `Top` char(1) NOT NULL default '',
    `Tag` char(1) NOT NULL default '',
    `Touser` varchar(255) NOT NULL default '',
    `Level` TINYINT NOT NULL default '0',
    PRIMARY KEY  (`B_no`),
    KEY `Writer_id` (`Writer_id`),
    KEY `Thread` (`Thread`),
    KEY `Fid` (`Fid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);

    $query	=	"
    CREATE TABLE IF NOT EXISTS $this->DataBase.$Comment_name(
    `C_no` int(11) NOT NULL auto_increment,
    `B_no` int(11) NOT NULL default '0',
    `C_ID` varchar(40) NOT NULL default '',
    `C_Name` varchar(40) NOT NULL default '',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Comment` text NOT NULL,
    PRIMARY KEY  (`C_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);
    break;

    // �Ϲ� ���� ����
    case "normal" :

    //  ��б� ���� : 2010-03-05 ������
    if($this->CP_CODE=='dochiqueen') {
      $query = "
      CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
      `B_no` int(11) NOT NULL auto_increment,
      `Title` varchar(255) NOT NULL default '',
      `Writer_id` varchar(40) NOT NULL default '',
      `Writer_name` varchar(16) NOT NULL default '0',
      `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
      `Ref` int(11) NOT NULL default '0',
      `Contents` text NOT NULL,
      `Fid` int(11) NOT NULL default '1',
      `Thread` varchar(10) NOT NULL default 'A',
      `Top` char(1) NOT NULL default '',
      `Tag` char(1) NOT NULL default '',
      `Touser` varchar(255) NOT NULL default '',
      `Secret` char(1) NOT NULL default '',
      PRIMARY KEY  (`B_no`),
      KEY `Writer_id` (`Writer_id`),
      KEY `Thread` (`Thread`),
      KEY `Fid` (`Fid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
      ";
      $mysql->execute($query);
    }
    else {
      $query = "
      CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
      `B_no` int(11) NOT NULL auto_increment,
      `Title` varchar(255) NOT NULL default '',
      `Writer_id` varchar(40) NOT NULL default '',
      `Writer_name` varchar(16) NOT NULL default '0',
      `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
      `Ref` int(11) NOT NULL default '0',
      `Contents` text NOT NULL,
      `Fid` int(11) NOT NULL default '1',
      `Thread` varchar(10) NOT NULL default 'A',
      `Top` char(1) NOT NULL default '',
      `Tag` char(1) NOT NULL default '',
      `Touser` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`B_no`),
      KEY `Writer_id` (`Writer_id`),
      KEY `Thread` (`Thread`),
      KEY `Fid` (`Fid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
      ";
      $mysql->execute($query);
    }

    $query	=	"
    CREATE TABLE IF NOT EXISTS $this->DataBase.$Comment_name(
    `C_no` int(11) NOT NULL auto_increment,
    `B_no` int(11) NOT NULL default '0',
    `C_ID` varchar(40) NOT NULL default '',
    `C_Name` varchar(40) NOT NULL default '',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Comment` text NOT NULL,
    PRIMARY KEY  (`C_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);
    break;

    // �Ϲ� ���� ����
    case "gongji" :

    $query = "
    CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
    `B_no` int(11) NOT NULL auto_increment,
    `Title` varchar(255) NOT NULL default '',
    `Writer_id` varchar(40) NOT NULL default '',
    `Writer_name` varchar(16) NOT NULL default '0',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Ref` int(11) NOT NULL default '0',
    `Contents` text NOT NULL,
    `Fid` int(11) NOT NULL default '1',
    `Thread` varchar(10) NOT NULL default 'A',
    `Top` char(1) NOT NULL default '',
    `Tag` char(1) NOT NULL default '',
    `Touser` varchar(255) NOT NULL default '',
    `PopYN` varchar(1) NOT NULL default '',
    PRIMARY KEY  (`B_no`),
    KEY `Writer_id` (`Writer_id`),
    KEY `Thread` (`Thread`),
    KEY `Fid` (`Fid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);

    $query	=	"
    CREATE TABLE IF NOT EXISTS $this->DataBase.$Comment_name(
    `C_no` int(11) NOT NULL auto_increment,
    `B_no` int(11) NOT NULL default '0',
    `C_ID` varchar(40) NOT NULL default '',
    `C_Name` varchar(40) NOT NULL default '',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Comment` text NOT NULL,
    PRIMARY KEY  (`C_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);
    break;

    // ��ǰ�ı� ����
    case "good" :

    $query = "
    CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
    `B_no` int(11) NOT NULL auto_increment,
    `Title` varchar(255) NOT NULL default '',
    `Writer_id` varchar(40) NOT NULL default '',
    `Writer_name` varchar(16) NOT NULL default '0',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Ref` int(11) NOT NULL default '0',
    `Contents` text NOT NULL,
    `Fid` int(11) NOT NULL default '1',
    `Thread` varchar(10) NOT NULL default 'A',
    `Top` char(1) NOT NULL default '',
    `Tag` char(1) NOT NULL default '',
    `G_code` varchar(255) NOT NULL default '',
    `G_sati` char(1) NOT NULL default '',
    `Touser` varchar(255) NOT NULL default '',
    `best_ok` TINYINT NOT NULL default '',
    PRIMARY KEY  (`B_no`),
    KEY `Writer_id` (`Writer_id`),
    KEY `Thread` (`Thread`),
    KEY `Fid` (`Fid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);

    $query	=	"
    CREATE TABLE IF NOT EXISTS $this->DataBase.$Comment_name(
    `C_no` int(11) NOT NULL auto_increment,
    `B_no` int(11) NOT NULL default '0',
    `C_ID` varchar(40) NOT NULL default '',
    `C_Name` varchar(40) NOT NULL default '',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Comment` text NOT NULL,
    PRIMARY KEY  (`C_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);
    break;

    // ���� �Խ���
    case "turm" :
    $query = "
    CREATE TABLE IF NOT EXISTS $this->DataBase.$table_name(
    `B_no` int(11) NOT NULL auto_increment,
    `Title` varchar(255) NOT NULL default '',
    `Writer_id` varchar(40) NOT NULL default '',
    `Writer_name` varchar(16) NOT NULL default '0',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Ref` int(11) NOT NULL default '0',
    `Contents` text NOT NULL,
    `Fid` int(11) NOT NULL default '1',
    `Thread` varchar(10) NOT NULL default 'A',
    `Top` char(1) NOT NULL default '',
    `Tag` char(1) NOT NULL default '',
    `Start_time` datetime NOT NULL default '0000-00-00 00:00:00',
    `End_time` datetime NOT NULL default '0000-00-00 00:00:00',
    `Charger` varchar(255) NOT NULL default'',
    `Collaborator` varchar(255) NOT NULL default'',
    PRIMARY KEY  (`B_no`),
    KEY `Writer_id` (`Writer_id`),
    KEY `Thread` (`Thread`),
    KEY `Fid` (`Fid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);

    $query	=	"
    CREATE TABLE IF NOT EXISTS $this->DataBase.$Comment_name(
    `C_no` int(11) NOT NULL auto_increment,
    `B_no` int(11) NOT NULL default '0',
    `C_ID` varchar(40) NOT NULL default '',
    `C_Name` varchar(40) NOT NULL default '',
    `Regi_date` datetime NOT NULL default '0000-00-00 00:00:00',
    `Comment` text NOT NULL,
    PRIMARY KEY  (`C_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=euckr AUTO_INCREMENT=1;
    ";
    $mysql->execute($query);
    break;

  // switch - End()
  }
	// �Խ��� ��Ͽ� ���
	$result = $this->Insert_board_list($section,$post,$mysql);
 Return $result;
// function - End
}

// �Խ��� ��� ��� ����
function Insert_board_list($board_type,$post,$mysql){
  $file_name = $post[file_name][0]."|".$post[file_name][1];
  $file_name_title = $post[file_name_title];
  $OmpYN = $post[OmpYN];

  $query = "INSERT INTO $this->DataBase.`Board_list` (`idx` ,`table_name` ,`board_name`,`board_type`,`board_state` ,`regi_date` ,`Paging` ,`Img` ,`Title_Img`, `OmpYN` ) VALUES (NULL , '$post[table_name]', '$post[board_name]', '$board_type', '$post[board_state]',NOW( ), '$post[page]', '$file_name', '$file_name_title', '$OmpYN' );";
  $result = $mysql->execute($query);
  Return $result;
}

// ��ǰ�ڵ忡 �ش��ϴ� �̹��� ��� (��ǰ����, �ı��)
function GetGoods_img($GoodsCode,$mysql){

    $query	=	"SELECT a.File_name, b.Goods_name FROM $this->DataBase.Goods_img as a
                 LEFT JOIN $this->DataBase.Goods_Basic as b on(a.Goods_no=b.Goods_no)
               WHERE a.Goods_no ='$GoodsCode' and a.Size = '2';";
    $data	=	$mysql->fetch_key($query);

  Return $data;
}

//---------------------------------
// �ڸ�Ʈ
//---------------------------------
function Select_comment(){

}
//----------------------------------------------------------------
// ��� ��� : ����� �ʿ��ϸ� �߰�.
//----------------------------------------------------------------
function Insert_comment(){

}


// class - End()
}
?>