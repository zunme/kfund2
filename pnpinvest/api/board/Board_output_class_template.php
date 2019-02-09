<?
define(Default_td_color, "#efefef");	// �⺻ TD ��
define(Top_td_color, "#cdcdce");		// ������ ��� TD ��
define(_REPLY_IMG, "<img src=\"./board/img/re.gif\" align=\"absmiddle\" >");	// �亯 �̹���
define(_SECRET_IMG, "<img src=\"./board/img/secret.gif\" align=\"absmiddle\" >");	// ��� �̹���
define(_NOSECRET_IMG, "<img src=\"./board/img/nosecret.gif\" align=\"absmiddle\" >");	// ���� �̹���

// ��¿�
class Board_output_template{
// class - Start()

	// �Խ��� ����
	var $self_page;

	// �۾� ����
	var $secret_font_color;

	// ���̺� ����
	var $bgcolor_table,$bgcolor_td,$bgcolor_td_title,$bgcolor_turm_td;

	// Tr ����
	var $list_tr_height,$list_title_height,$page_padding,$view_title_height,$view_td_width,$view_td_height,$file_tr_height,$file_turm_height,$file_turm_width;

// ����� ǥ�� ȭ��ǥ ������
var $arrow_now;

// �Խ��� ��� ��ư��
var $list_button,$list_write,$list_modify, $list_reply, $list_delete, $list_search, $list_comment ,$list_button_dot;

//-----------------------------
//     ������
//-----------------------------
function Board_output_template(){

  // ��ũ�� �������ϸ�
  $this->self_page			= $_SERVER["PHP_SELF"];

  // ��׶��� ����
  $this->bgcolor_table	=	"#DCDCDC";
  $this->bgcolor_top =	"#EDECE1";
  $this->bgcolor_td_title	=	"#FDFFF4";
  $this->bgcolor_td =	"#FFFFFF";
  $this->bgcolor_turm_td	 =	"#FDFFF4";
  $this->view_td_title =	"#EDECE1";  // view ���Ͽ��� ����κ�
  $this->view_td =	"#FFFFFF";   // view ���Ͽ��� TD�κ�

  // �۾� ����
  $this->secret_font_color = "#CDDCDC";

  // list tr ����
  $this->list_tr_height		=	20;
  $this->list_title_height	=	24;
  $this->page_padding	=	10;	// ����Ʈ�� ����¡�� ����

  // view ����
  $this->view_title_height	=	30;
  $this->view_contents_height	=	200;
  $this->view_td_width		=	340;
  $this->view_td_height	=	40;
  $this->file_tr_height		=	18;
  $this->file_turm_height	=	18;
  $this->file_turm_width	=	160;

  // ����� ǥ�� ȭ��ǥ
  $this->arrow_now = "board/img/img_arrow.gif";
  $this->file_addicon = "./board/img/file.gif";

  // �Խ��ǿ� ���Ǵ� ��ư �̹�����
  $this->list_button = "";
  $this->list_write = "";
  $this->list_modify = "";
  $this->list_reply = "";
  $this->list_delete = "";
  $this->list_search = "";
  $this->list_comment = "";
  $this->list_button_dot = "";

// function - End
}

//--------------------------------------------
//     �Խ��� ������
//--------------------------------------------
function Print_create_form(){

  echo "<body onload=\"document.createform.board_name.focus();\">";
  echo "<table border='1'>";
  echo "<form name=\"createform\" action=\"$this->self_page\" method=\"post\" onsubmit=\"if(!document.createform.board_name.value){return false;}else{document.createform.submit();}\">";
  echo "<INPUT TYPE=\"hidden\" NAME=\"baction\" value=\"create\">";
  echo "<INPUT TYPE=\"hidden\" NAME=\"action\" value=\"Board\">";
  echo "<tr>";
  echo "<td align=\"center\">�Խ��� �뵵</td>
  <td align=\"center\"><INPUT TYPE=\"radio\" NAME=\"section\" value='normal' checked>�Ϲ�</td>
  <td align=\"center\"><INPUT TYPE=\"radio\" NAME=\"section\" value='file'>����</td>
  <td align=\"center\"><INPUT TYPE=\"radio\" NAME=\"section\" value='level'>�ӿ�</td>
  <td align=\"center\"><INPUT TYPE=\"radio\" NAME=\"section\" value='turm'>����</td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=\"center\">�Խ��� �̸�</td><td colspan='4' align=\"center\"><INPUT TYPE=\"text\" NAME=\"board_name\" style=\"ime-mode:active\" OnKeyPress=\"korean_only(this);\"></td>";
  echo "</tr>";
  echo "</form>";
  echo "<tr>";
  echo "<td align=\"center\" colspan='5' ><INPUT TYPE=\"button\" value=\"�Խ��� ����\" onclick=\"if(!document.createform.board_name.value){return false;}else{document.createform.submit();}\"></td>";
  echo "</tr>";
  echo "</table>";
  echo "</body>";

// function - End
}

//----------------------------------------------------------------
//   �亯 ���� ���
//----------------------------------------------------------------
function Make_space($Thread){

  $space = "";
  $turn = strlen($Thread);

  // ����� ���
  if($turn > 1){
    // �����尡 2ĭ �̻��̸� ������ ����ְ� �׷��� ������ ������ ���� �ʴ´�.
    if($turn >= 2){
      for($i=0; $i < $turn; $i++){$space = $space."&nbsp;&nbsp;";}
    }
  return $space._REPLY_IMG;
  }
  // �ܵ� ���� ���
  else{return "";}

// function - End
}

//----------------------------------------------------------------------------
// ���� ����Ʈ���� ����� ǥ�� ������ ���
//----------------------------------------------------------------------------
function Print_arrow($b_no, $l_no){

 // ���� �۹�ȣ�� ����Ʈ �۹�ȣ�� ������ ȭ��ǥ ǥ��
 if($b_no == $l_no){
   echo "<img src='$this->arrow_now' align='absmiddle'>";
 }

// function - End
}


//----------------------------------------------------------------------------
// ����Ʈ���� ��б� ǥ�� ������ ��� �߰� : 2010-03-04 ������
//----------------------------------------------------------------------------
function Print_secret($secret){
 if($secret == 'y'){ echo "<img src=\"./board/img/ico-lock.gif\" align=\"absmiddle\" >"; }
// function - End
}

//-------------------------------------
//   TD ���� ���
//-------------------------------------
function Print_td_color($Top){
  if($Top=="o"){return $this->bgcolor_top;}
  else{return $this->bgcolor_td;}
// function - End
}

//----------------------------------------------------------------
//   ���ǿ����� tpl���� ü����
//----------------------------------------------------------------
function Output_changer($baction,$section){
  return "board/".$baction."_".$section.".tpl";
}

//-----------------------------------------------------
//   �ɼ� �Խ��ǿ��� �ɼ� ǥ��
//-----------------------------------------------------
function Print_optionstatus($section,$data){

  switch($section){
    case "file" :
      // ÷�������� ���� ��
      if(strlen($data) >= 1)
      {return "<IMG SRC=\"$this->file_addicon\"  BORDER=\"0\" align=\"absmiddle\">";}
      // ÷�� ������
      else{return "&nbsp;";}
    break;

    case "level" :
      // Touser�� ������
      if(strlen($data) >= 1){return _SECRET_IMG;} // ��� �̹���
      // Touser�� ������
      else{return _NOSECRET_IMG;}
    break;
  // switch - End
  }
// function - End()
}

//------------------------------------------------------------------------------
// ���� ���� �Խ��ǰ� ������ �Խ����� �и�
//------------------------------------------------------------------------------
function Sep_level($inst_StringCon,$inst_auth,$table_name,$section,$b_no,$page,$option,$U_id,$Writer_id,$Title,$adminYN,$board_no='',$path='',$secret=''){

  $Title = $inst_StringCon->change_gal($inst_StringCon->ugly_han(urldecode($Title)));

  // �ش�Խù��� ���������� �Ǵ�
  $auth = $inst_auth->Authcheck($section,$option,$U_id,$Writer_id,$secret);

  // ���ο��� ���� ������� as ���ѱ��
  $as = $adminYN == "Y" ? "BoardMan":"";

  if($auth=="ok"){
    echo sprintf("<A HREF=\"%s?action=Board&baction=view&table_name=%s&section=%s&No=%s&page=%s&as=%s&board_no=%s&path=%s&b_title=%s&secret=%s\">%s</A>",$this->self_page,$table_name,$section,$b_no,$page,$as,$board_no,$path,urlencode($_GET[b_title]),$secret,$Title);
  }
  else{
    echo sprintf("<font size=\"2\" color=\"$this->secret_font_color\">%s</font>",$Title);
  }
// function - End
}

//----------------------------------------------------------------
// ����Ҷ� �Խ��� �ɼǺ� ���
//----------------------------------------------------------------
function Print_write_option($section,$baction,$mysql,$Edit_array){

  switch($section){
    // ���� ����
    case "level" :
      //--------------------------------------------------
      //        ��ü ����� ��� ����
      //--------------------------------------------------
      $inst_member	= new Member();

      // 0��°�� �迭�� , 1��°�� ���ڵ� ������
      $data = $inst_member->Query_member($mysql);

      // ���� �ÿ��� �б� �����ڷ� ��ϵ� ����� ���ڿ��� �迭�� �и����ش�
      if($baction !="write"){
      // ���õ� ���
      $Touser	=	explode("/",$Edit_array[0]);

      // ��ü ��Ͽ��� ���õ��� ���� ��鸸 �̾��ش�.
      $result_array = array_diff($data[0]["u_name"],$Touser);
      sort($result_array); // �迭 �ε��� ����
      }
      else{$result_array	 =	$data[0]["u_name"];}

      echo "<FORM METHOD=POST ACTION=\"$this->self_page\" NAME=\"multi1\">";
      echo "<INPUT TYPE=\"hidden\" NAME=\"aa\" value=\"aa\">";

      // to ��� ����
      echo "<tr height=\"150\" valign=\"middle\">";
      echo "	<td align=\"center\" width=\"120\" bgcolor=\"$this->view_td_title\">To</td>";
      echo "	<td bgcolor=\"$this->view_td\">";
      echo " <table>";
      echo " <tr>";

      // ������ �����
      echo "	<td  valign=\"middle\">";
      echo "<select multiple name=\"haveBox\" ONDBLCLICK=\"moveOver2(this.form);\" style=\"width:120px;height:160px;\">";
      $this->Print_Memlist($result_array);
      echo "</select>";
      echo "  </td>";
      echo "<td>
      <table width=\"65\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <tr>
      <td align=\"center\" height=\"40\"><input type=\"button\" value=\"�߰�\" onClick=\"multi_moveOver(this.form);\"></td>
      </tr>
      <tr>
      <td background=\"../img/dot_line.gif\"><img src=\"../img/blank.gif\" width=\"5\" height=\"1\"></td>
      </tr>
      <tr>
      <td align=\"center\" height=\"40\"><input type=\"button\" value=\"����\" onclick=\"removeMe(this.form);\"></td>
      </tr>
      </table>
      </td>";

      // ������ �����
      echo "	<td  valign=\"middle\">";
      echo "<select multiple name=\"addBox\" ONDBLCLICK=\"removeMe(this.form);\" style=\"width:120px;height:160px;\">";
      $this->Print_Memlist($Touser);
      echo "</select multiple>";
      echo "  </td>";
      echo " </tr>";
      echo "</table>";
      echo "	</td>";
      echo "</tr>";
      echo "</FORM>";
      break;

    // ���� ����
    case "turm" :
     // ������ �ƴҰ�츸
      if($baction!="repl"){
        // if - Start()
        //--------------------------------------------------
        //        ��ü ����� ��� ����
        //--------------------------------------------------
        $inst_member	= new Member();

        // 0��°�� �迭�� , 1��°�� ���ڵ� ������
        $data = $inst_member->Query_member($mysql);

        // ���� �ÿ��� �б� �����ڷ� ��ϵ� ����� ���ڿ��� �迭�� �и����ش�
        if($baction !="write"){
          // ����� ���
          $Charger = explode("/",$Edit_array[1]);

          // ������ ���
          $Collaborator = explode("/",$Edit_array[2]);

          // ��ü ��Ͽ��� ���õ��� ���� ����� ����� �̾��ش�.
          $result_Charger = array_diff($data[0]["u_name"],$Charger);
          sort($result_Charger); // �迭 �ε��� ����

          // ��ü ��Ͽ��� ���õ��� ���� ����� ����� �̾��ش�.
          $result_Collaborator = array_diff($data[0]["u_name"],$Collaborator);
          sort($result_Collaborator); // �迭 �ε��� ����
        }
        else{
          $result_Charger = $data[0]["u_name"];			// ����� ���
          $result_Collaborator = $data[0]["u_name"];	// ������ ���
        }

        echo "<FORM METHOD=POST ACTION=\"$this->self_page\" NAME=\"multi1\">";
        echo "<INPUT TYPE=\"hidden\" NAME=\"aa\" value=\"aa\">";

        // to ��� ����
        echo "<tr height=\"150\" valign=\"middle\">";
        echo "	<td align=\"center\" width=\"120\" bgcolor=\"$this->view_td_title\">�����</td>";
        echo "	<td bgcolor=\"$this->view_td\">";
        echo " <table>";
        echo " <tr>";

        // ������ �����
        echo "	<td  valign=\"middle\">";
        echo "<select multiple name=\"haveBox\" ONDBLCLICK=\"moveOver2(this.form);\" style=\"width:120px;height:160px;\">";
        $this->Print_Memlist($result_Charger);
        echo "</select>";
        echo "  </td>";
        echo "<td>
        <table width=\"65\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
        <td align=\"center\" height=\"40\"><input type=\"button\" value=\"�߰�\" onClick=\"multi_moveOver(this.form);\"></td>
        </tr>
        <tr>
        <td background=\"../img/dot_line.gif\"><img src=\"../img/blank.gif\" width=\"5\" height=\"1\"></td>
        </tr>
        <tr>
        <td align=\"center\" height=\"40\"><input type=\"button\" value=\"����\" onclick=\"removeMe(this.form);\"></td>
        </tr>
        </table>
        </td>";

        // ������ �����
        echo "	<td  valign=\"middle\">";
        echo "<select multiple name=\"addBox\" ONDBLCLICK=\"removeMe(this.form);\" style=\"width:120px;height:160px;\">";
        $this->Print_Memlist($Charger);
        echo "</select multiple>";
        echo "  </td>";
        echo " </tr>";
        echo "</table>";
        echo "	</td>";
        echo "</tr>";

        echo "</FORM>";
        echo "<FORM METHOD=POST ACTION=\"$this->self_page\" NAME=\"multi2\">";
        echo "<INPUT TYPE=\"hidden\" NAME=\"bb\" value=\"bb\">";

        // to ��� ����
        echo "<tr height=\"150\" valign=\"middle\">";
        echo "	<td align=\"center\" width=\"120\" bgcolor=\"$this->view_td_title\">������</td>";
        echo "	<td bgcolor=\"$this->view_td\">";
        echo " <table>";
        echo " <tr>";

        // ������ �����
        echo "	<td  valign=\"middle\">";
        echo "<select multiple name=\"haveBox\" ONDBLCLICK=\"moveOver2(this.form);\" style=\"width:120px;height:160px;\">";
        $this->Print_Memlist($result_Collaborator);
        echo "</select>";
        echo "  </td>";
        echo "<td>
        <table width=\"65\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
        <td align=\"center\" height=\"40\"><input type=\"button\" value=\"�߰�\" onClick=\"multi_moveOver(this.form);\"></td>
        </tr>
        <tr>
        <td background=\"../img/dot_line.gif\"><img src=\"../img/blank.gif\" width=\"5\" height=\"1\"></td>
        </tr>
        <tr>
        <td align=\"center\" height=\"40\"><input type=\"button\" value=\"����\" onclick=\"removeMe(this.form);\"></td>
        </tr>
        </table>
        </td>";
        // ������ �����
        echo "	<td  valign=\"middle\">";
        echo "<select multiple name=\"addBox\" ONDBLCLICK=\"removeMe(this.form);\" style=\"width:120px;height:160px;\">";
        $this->Print_Memlist($Collaborator);
        echo "</select multiple>";
        echo "  </td>";
        echo " </tr>";
        echo "</table>";
        echo "	</td>";
        echo "</tr>";
        echo "</FORM>";
      // if - End();
      }
    break;

  // switch - End
  }

// function - End
}
//------------------------------------------
//   ���� ��� ���
//------------------------------------------
function Print_Memlist($data){
  for($i=0; $i < count($data); $i++){
    $U_name	=	$data[$i];
    echo "<option value='$U_name'>$U_name</option>";
  }
// function - End
}

//----------------------------------------------------
//   ���Ű�� ��� ����
//----------------------------------------------------
function Print_viewfunction($mysql,$option,$baction,$table_name,$section,$B_no,$U_id,$Writer,$Top,$Thread,$Fid,$admin='N',$rebuyn='Y'){

  //$inst_query	=	new Board_query();
  // �����۰� �������� ��ȣ�� �����ؼ� ����
 // $Direction	 =	$inst_query->Find_nprecord($mysql,$B_no,$table_name,$Top);
  echo "<div class='viewbtn'>";

  // ���� ��ư ����� TD
  //echo $this->Print_button("Space","");

  // ������, ������ ��ư ��� ( �亯���� �ƴ� ��츸 ǥ���Ѵ�. / ���Ŀ� �亯�۵� �ʿ��ϸ� �亯�� ���� �������� �۵��� �����ش�.)
  // if(strlen($Thread) <= 1){$this->Print_directionbutton($Direction,$table_name,$section);}
  //echo $this->Print_button("Space","");

  $action = $_GET[path] == "mypage" ? "MyBoard":"Board";

  // ���ο��� ���� ������� as ���ѱ��
  $as = $admin=="Y" ? "BoardMan":"";
  $b_title = urlencode($_GET[b_title]);

  // �����̳� ���� ��ư�� ������ �׹�ư��η�
  if($this->list_button!="") {  $list_button = sprintf("%s",$this->list_button); }
  else { $list_button = "board/img/btn-03.gif"; }
  echo "<img src='$list_button' align='absmiddle' onclick=\"document.location.replace('$this->self_page?action=$action&baction=list&table_name=$table_name&section=$section&page=$_GET[page]&as=$as&path=$_GET[path]&b_title=$b_title');\" style='cursor:pointer'> ";
  //echo $this->Print_button("Space","");

  // ��� ���� ������ ���� ����� 1����������. ���� �Խ����� ����� �޼�����
  // ��� ���ް��� �Խ���.. 2008-04-01 : ������
  $NoRepl = array("Board_goods_after","Board_one_qna","Board_goods_qna","Board_gongji","Board_review");
  if($admin=="Y") $NoRepl = array("Board_goods_after","Board_gongji");

  if(($U_id!="") && $Thread=="A" && $Top!="o" && $section!="turm" && !in_array($table_name,$NoRepl)){
    // �����̳� ���� ��ư�� ������ �׹�ư��η�
    if($this->list_reply!="") {  $list_reply = sprintf("%s",$this->list_reply); }
    else { $list_reply = "board/img/btn-04.gif"; }
    if($rebuyn=='Y') {
      echo "<img src='$list_reply' align='absmiddle' onclick=\"document.location.replace('$this->self_page?action=Board&baction=repl&table_name=$table_name&section=$section&No=$B_no&page=$_GET[page]&as=$as&path=$_GET[path]&b_title=$b_title&secret=$_GET[secret]');\" style='cursor:pointer'>";
    }

    //echo $this->Print_button("Space","");
  }
  // ���� , ���� ��ư ���
  $this->Print_edbutton($table_name,$section,$U_id,$Writer,$B_no,$Thread,$admin);
  echo "</div>";

  // ÷�������� ������츸 ����� TR
  if(trim($option["size"][0])!="0" && trim($option["size"][0])!=""){$this->Print_viewfile($option);}

// function - End
}

//---------------------------------------------
//   ����, ���� ��ư ���
//---------------------------------------------
function Print_edbutton($table_name,$section,$U_id,$Writer,$B_no,$Thread,$admin='N'){

  // �����̳� ���� ��ư�� ������ �׹�ư��η�
  if($this->list_modify!="") {  $list_modify = sprintf("%s",$this->list_modify); }
  else { $list_modify = "board/img/btn-05.gif"; }

  // �����̳� ���� ��ư�� ������ �׹�ư��η�
  if($this->list_delete!="") {  $list_delete = sprintf("%s",$this->list_delete); }
  else { $list_delete = "board/img/btn-06.gif"; }

  // �����̳� ���� ��ư�� ������ �׹�ư��η�
  if($this->list_button_dot=="none") { $list_button_dot = sprintf(" ");}
  else { $list_button_dot = "<img src='board/img/btn-dot_gray.gif' width='1' height='22' align='absmiddle' style='margin:0 5px;'>";  }

  // 2009-04-20 �����Ϲ� ������ mypage�� �����س��� ������ : ������
  if($_GET[path]!="") { $path_ = $_GET[path]; }
  else { $path_ = ''; }
  $as = $admin=="Y" ? "BoardMan&path=main":"&path=$path_";

  if( $Writer==$U_id || $admin=="Y" ){
    $b_title = urlencode($_GET[b_title]);
    echo "$list_button_dot<img src='$list_modify' align='absmiddle' onclick=\"document.location.replace('$this->self_page?action=Board&baction=edit&table_name=$table_name&section=$section&No=$B_no&Thread=$Thread&page=$_GET[page]&as=$as&b_title=$b_title&secret=$_GET[secret]');\"  style='cursor:pointer'>";
    echo " <img src='$list_delete' align='absmiddle' onclick=\"if(confirm('���� �Ͻðڽ��ϱ�?')){return document.location.replace('$this->self_page?action=Board&baction=dele&table_name=$table_name&section=$section&No=$B_no&page=$_GET[page]&as=$as&admin=$admin');}else{return false;}\" style='cursor:pointer'>";
   // echo $this->Print_button("Space","");
  }
// function - End
}

//----------------------------
//   ��ư ���
//----------------------------
function Print_button($b_action,$param){

  switch($b_action){
    case "Space" :
      return "&nbsp;&nbsp;";
    break;

    case "Write" :
      return "<INPUT TYPE=\"button\" value=\"�۾���\" $param>";
    break;

    case "Write_submit" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Edit" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Del" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Next" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Prev" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "List" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Repl" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Search" :
      return "<INPUT TYPE=\"button\" value=\"�� ��\" $param>";
    break;

    case "Total" :  // bt_img12.gif
      return "<IMG SRC='/img/page5/bt_img12.gif' $param align='absmiddle' style='cursor:pointer'>";
    break;
  }

// function - End
}

//----------------------------------------------------------------
// ��¥ �������̽� ��ư ��� �޼ҵ�
//----------------------------------------------------------------
function button_ud($act,$field){

  return "<table width=\"13\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
  <td><img src=\"./img/bt_up.gif\" width=\"13\" height=\"9\" border=\"0\" style=\"cursor:pointer\" onclick=\"check_value('$act','$field','up','writeform')\"></td>
  </tr>
  <tr><td><img src=\"\" width=\"13\" height=\"1\"></td></tr>
  <tr>
  <td><img src=\"./img/bt_down.gif\" width=\"13\" height=\"9\" border=\"0\" style=\"cursor:pointer\" onclick=\"check_value('$act','$field','down','writeform')\"></td>
  </tr>
  </table>";

// function - End
}

//-----------------------------------------------
//   ��¥ �������̽� ����
//-----------------------------------------------
function Print_date($date1,$date2){

  // �����ÿ��� �Ѱܹ��� �����ͷ� ��¥ ����
  if($date1!=""){
    $date1	=	str_replace("-"," ",str_replace(":"," ",$date1));
    $date2	=	str_replace("-"," ",str_replace(":"," ",$date2));
    $temp_date1	=	explode(" ",$date1);
    $temp_date2	=	explode(" ",$date2);
  }
  // �ۼ��ÿ��� ���� ��¥ �����ؼ� ��¥�ۼ�
  else{
    $date			=	date("Y m d H i s",mktime());
    $temp_date1	=	explode(" ",$date);
    $temp_date2	=	explode(" ",$date);
  }

  $year	=	intval($temp_date1[0]);
  $month	=	intval($temp_date1[1]);
  $day	 	=	intval($temp_date1[2]);
  $hour	=	intval($temp_date1[3]);
  $min	 	=	intval($temp_date1[4]);
  $sec	 	=	intval($temp_date1[5]);

  $year_e	=	intval($temp_date2[0]);
  $month_e	=	intval($temp_date2[1]);
  $day_e	 	=	intval($temp_date2[2]);
  $hour_e	=	intval($temp_date2[3]);
  $min_e	 	=	intval($temp_date2[4]);
  $sec_e 	=	intval($temp_date2[5]);

  // ���� ����
  $year_b	=	$this->button_ud("year","year");
  $month_b	=	$this->button_ud("month","month");
  $day_b		=	$this->button_ud("day","day");
  $hour_b	=	$this->button_ud("hour","hour");
  $min_b		=	$this->button_ud("min","min");
  $sec_b		=	$this->button_ud("sec","sec");

  // ���� ����
  $eyear_b		=	$this->button_ud("year","eyear");
  $emonth_b	=	$this->button_ud("month","emonth");
  $eday_b		=	$this->button_ud("day","eday");
  $ehour_b		=	$this->button_ud("hour","ehour");
  $emin_b		=	$this->button_ud("min","emin");
  $esec_b		=	$this->button_ud("sec","esec");

  echo "
  <tr>
  <td bgcolor=\"$this->view_td_title\" height=\"30\" align=\"center\">���� ����</td>
  <td bgcolor=\"$this->view_td\">
  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
  <!--��-->
  <td width=\"35\" align=\"center\"><input name=\"year\" type=\"text\" class=\"input_date1\" value=\"$year\"></td>
  <td width=\"16\" align=\"center\">$year_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"month\" type=\"text\" class=\"input_date2\" value=\"$month\"></td>
  <td width=\"16\">$month_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"day\" type=\"text\" class=\"input_date2\" value=\"$day\"></td>
  <td width=\"16\">$day_b</td>
  <td width=\"10\" align=\"center\">&nbsp;</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"hour\" type=\"text\" class=\"input_date2\" value=\"$hour\"></td>
  <td width=\"16\" align=\"center\">$hour_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"min\" type=\"text\" class=\"input_date2\" value=\"$min\"></td>
  <td width=\"16\" align=\"center\">$min_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"sec\" type=\"text\" class=\"input_date2\" value=\"$sec\"></td>
  <td width=\"16\" align=\"center\">$sec_b</td>
  </tr>
  </table>
  </td>
  </tr>

  <!--���� ��¥-->
  <tr>
  <td bgcolor=\"$this->view_td_title\" height=\"30\" align=\"center\">���� ����</td>
  <td bgcolor=\"$this->view_td\">
  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
  <!--��-->
  <td width=\"35\" align=\"center\"><input name=\"eyear\" type=\"text\" class=\"input_date1\" value=\"$year_e\"></td>
  <td width=\"16\" align=\"center\">$eyear_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"emonth\" type=\"text\" class=\"input_date2\" value=\"$month_e\"></td>
  <td width=\"16\">$emonth_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"eday\" type=\"text\" class=\"input_date2\" value=\"$day_e\"></td>
  <td width=\"16\">$eday_b</td>
  <td width=\"10\" align=\"center\">&nbsp;</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"ehour\" type=\"text\" class=\"input_date2\" value=\"$hour_e\"></td>
  <td width=\"16\" align=\"center\">$ehour_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"emin\" type=\"text\" class=\"input_date2\" value=\"$min_e\"></td>
  <td width=\"16\" align=\"center\">$emin_b</td>
  <!--��-->
  <td width=\"22\" align=\"center\"><input name=\"esec\" type=\"text\" class=\"input_date2\" value=\"$sec_e\"></td>
  <td width=\"16\" align=\"center\">$esec_b</td>
  </tr>
  </table>
  </td>
  </tr>";

// function - End
}

//-----------------------------------------------
//   ������,������ ���
//-----------------------------------------------
function Print_directionbutton($Direction,$table_name,$section){

  // �������̳� �������� ������ ��ư ����
  $Next	=	$Direction[1];
  $Prev	=	$Direction[0];

  if($Prev!=""){
     echo $this->Print_button("Prev","onclick=\"document.location.replace('$this->self_page?action=Board&baction=view&table_name=$table_name&section=$section&No=$Prev');\"");
    echo $this->Print_button("Space","");
  }

  if($Next!=""){
    echo $this->Print_button("Next","onclick=\"document.location.replace('$this->self_page?action=Board&baction=view&table_name=$table_name&section=$section&No=$Next');\"");
  }

// function - End
}


//-----------------------------------------
//   ÷������ ��� ǥ��
//-----------------------------------------
function Print_viewfile($option){

//  echo "<tr><td colspan='5'  style='padding:5px'>";
  echo "<table align=\"center\" border = '0' width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" height='40'>";
  for($i=0; $i<count($option["filename"]);$i++){
    echo "<tr>";
    echo sprintf("<td height=\"$this->file_tr_height\"  style='padding:5px' align='right'><B>%s(%sk)</B></td>",$option["filename"][$i],round($option["size"][$i]/1024,1));
    echo "</tr>";
  }
  echo "</table>";
//  echo "</td></tr>";
//  echo "<tr><td height='1' colspan='5' bgcolor='#EBEBEB'></td></tr>";
// function - End
}


//--------------------------------------------------------------------
// ���� �Խ����� ����ڿ� ������ ���
//--------------------------------------------------------------------
function Print_turm_member($Stringvalue){

  $tmp = explode("/",$Stringvalue);
  echo "<TABLE cellpadding='0' cellspacing='0' border=\"0\"  bgcolor=\"$this->bgcolor_table\">";

  for($i=0; $i < count($tmp); $i++){
    echo "<TR>";
    echo "<TD height=\"$this->file_turm_height\" bgcolor=\"$this->bgcolor_td\" align=\"center\" valign=\"middle\">".$tmp[$i]."</TD>";
    echo "</TR>";
  }
  echo "</TABLE>";

// function - End
}

//-----------------------------------------
//   üũ�ڽ� �ɼ� ����
//-----------------------------------------
function Checkbox_check($value){
  if($value!=""){return "checked";}
}

//-------------------------------------------
//  �������� üũ tr ���
//-------------------------------------------
function Print_notice_check($U_Level, $Top,$Thread){

  if($U_Level >=3  && $Thread=="A"){
    $checked = $this->Checkbox_check($Top);
    echo "<TR>
    <TD align=\"center\" width=\"120\" bgcolor=\"$this->view_td_title\">��������</TD>
    <TD bgcolor=\"$this->view_td\"><INPUT TYPE=\"checkbox\" NAME=\"Top\" $checked>������ �����</TD>
    </TR>";
  }

// function - End
}


//----------------------------------------------------
//   ���Ű�� ��� ����
//----------------------------------------------------
function Print_viewfunction_click($mysql,$option,$baction,$table_name,$section,$B_no,$U_id,$Writer,$Top,$Thread,$Fid,$admin='N'){

  //$inst_query	=	new Board_query();
  // �����۰� �������� ��ȣ�� �����ؼ� ����
 // $Direction	 =	$inst_query->Find_nprecord($mysql,$B_no,$table_name,$Top);
  echo "<div class='viewbtn'>";

  // ���� ��ư ����� TD
  //echo $this->Print_button("Space","");

  // ������, ������ ��ư ��� ( �亯���� �ƴ� ��츸 ǥ���Ѵ�. / ���Ŀ� �亯�۵� �ʿ��ϸ� �亯�� ���� �������� �۵��� �����ش�.)
  // if(strlen($Thread) <= 1){$this->Print_directionbutton($Direction,$table_name,$section);}
  //echo $this->Print_button("Space","");

  $action = $_GET[path] == "mypage" ? "MyBoard":"Board";

  // ���ο��� ���� ������� as ���ѱ��
  $as = $admin=="Y" ? "BoardMan":"";
  $b_title = urlencode($_GET[b_title]);
  echo "<img src='board/img/btn-03.gif' align='absmiddle' onclick=\"document.location.replace('$this->self_page?action=$action&baction=list&table_name=$table_name&section=$section&page=$_GET[page]&as=$as&path=$_GET[path]&b_title=$b_title');\" style='cursor:pointer'> ";
  //echo $this->Print_button("Space","");

  // ��� ���� ������ ���� ����� 1����������. ���� �Խ����� ����� �޼�����
  // ��� ���ް��� �Խ���.. 2008-04-01 : ������
  $NoRepl = array("Board_goods_after","Board_one_qna","Board_goods_qna","Board_gongji","Board_review","Board_87222503");
  if($admin=="Y") $NoRepl = array("Board_goods_after","Board_gongji");

  if(($U_id!="") && $Thread=="A" && $Top!="o" && $section!="turm" && !in_array($table_name,$NoRepl)){
    echo "<img src='board/img/btn-04.gif' align='absmiddle' onclick=\"document.location.replace('$this->self_page?action=Board&baction=repl&table_name=$table_name&section=$section&No=$B_no&page=$_GET[page]&as=$as');\" style='cursor:pointer'>";
    //echo $this->Print_button("Space","");
  }
  // ���� , ���� ��ư ���
  $this->Print_edbutton($table_name,$section,$U_id,$Writer,$B_no,$Thread,$admin);
  echo "</div>";

  // ÷�������� ������츸 ����� TR
  if(trim($option["size"][0])!="0" && trim($option["size"][0])!=""){$this->Print_viewfile($option);}

// function - End
}


//---------------------------------------------
//   ����, ���� ��ư ���
//---------------------------------------------
function Print_edbutton_click($table_name,$section,$U_id,$Writer,$B_no,$Thread,$admin='N'){

  $as = $admin=="Y" ? "BoardMan&path=main":"&path=mypage";
  if( $Writer==$U_id || $admin=="Y" ){
    $b_title = urlencode($_GET[b_title]);
    echo "<img src='board/img/dot_gray.gif' width='1' height='22' align='absmiddle' style='margin:0 5px;'><img src='board/img/btn-05.gif' align='absmiddle' onclick=\"document.location.replace('$this->self_page?action=Board&baction=edit&table_name=$table_name&section=$section&No=$B_no&Thread=$Thread&page=$_GET[page]&as=$as&b_title=$b_title');\"  style='cursor:pointer'>";
    echo " <img src='board/img/btn-06.gif' align='absmiddle' onclick=\"if(confirm('���� �Ͻðڽ��ϱ�?')){return document.location.replace('$this->self_page?action=Board&baction=dele&table_name=$table_name&section=$section&No=$B_no&page=$_GET[page]&as=$as&admin=$admin');}else{return false;}\" style='cursor:pointer'>";
   // echo $this->Print_button("Space","");
  }
// function - End
}

//-----------------------------------------------------------------------------
//  �Խ����� ��ư�̹������� Ŭ���� ���η� ���� ��Ű�³�.
//-----------------------------------------------------------------------------
function PushButtonImage($list, $write, $modify, $reply, $delete ,$search,$comment='none',$dot='none' ) {

  $this->list_button = $list;
  $this->list_write = $write;
  $this->list_modify = $modify;
  $this->list_reply = $reply;
  $this->list_delete = $delete;
  $this->list_search = $search;
  $this->list_comment = $comment;
  $this->list_button_dot = $dot;

// funciton - End
}


// class - End()
}
?>