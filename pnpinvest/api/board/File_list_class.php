<?
// ���� ������ ÷�ε� ���� ��� ǥ�� Ŭ����
class File_list{

function File_list(){
  $this->delete_icon = "/board/img/file_delete.gif";
}

//-------------------------------------
//   ���� ������ ���
//-------------------------------------
function Print_filelist($filelist,$writer_id){

  echo "<style>.input_filelist {height:16px; padding:4px 0 0 9px; color:#8C8C8C; background-color:#F8F8F8;}</style>";
  echo "<table border='0'><tr><td style='padding-top:10px;'>";
  echo "<select size=\"3\" multiple  name=\"filelist\" id=\"filelist\" style=\"width:540px; height:70px;font-size:12px; border:0;\" class=\"input_filelist\">";
  echo "<option value=\"\">-----------------------------------÷�ε� ���ϵ�----------------------------------</option>";

  for($i=0; $i < count($filelist["filename"]); $i++){
    if($filelist["filename"][$i]!=""){
      echo sprintf("<option value=\"%s|%s\" >%s (%sk) </option>",$filelist["filename"][$i],$filelist["size"][$i],$filelist["filename"][$i],round($filelist["size"][$i]/1024,1));
    }
  }

  echo "</select>";
  echo "<img src=\"$this->delete_icon\" border=\"0\" align=\"absmiddle\" style=\"cursor:hand; margin-left:5px; margin-top:-8px;\" onclick=\"if(confirm('���� �Ͻðڽ��ϱ�?')){return delete_filelist();}else{return false;}\">";
  echo "</td></tr></table>";

// function - End
}

//------------------------------------------------------------------------------------------------
//  ���ڿ� ������ / ���� �������ְ� ���� ���� �迭 ����
//------------------------------------------------------------------------------------------------
function dell_seprator($sep,$string){

  $return_array = array();
  $temp = explode($sep,$string);

    for($i=0; $i < count($temp); $i++){
      // �迭�� ���� ���� ��Ҹ� ����
      if($temp[$i]!=""){
        $return_array[] = $temp[$i];
      // if - End
      }
    // loop - End
    }
  return $return_array;

// function - End
}

//----------------------------------------------------------------------------
//  ������ ���ڿ����� ������ ���ڿ��� ����
//----------------------------------------------------------------------------
function Sep_strings($_POST){

  $return_string = "";
  $delete_files = $this->dell_seprator("/",$_POST["delstring"]);  // ������ ���ϵ�
  $original_files = $this->dell_seprator("/",$_POST["originalfiles"]);  // ���� ���ϵ�
  $update_files = array_diff($original_files,$delete_files);  // �ٽ� ������Ʈ�� ���ϵ�
  sort($update_files);  // �迭 �ε��� ����
  $turn =  count($update_files);

  for($i=0; $i < $turn; $i++){

    if($i < $turn-1){
      $return_string = $return_string.$update_files[$i]."/";
    // if - End
    }
    else{
      $return_string = $return_string.$update_files[$i];
    // else - End
    }
  // loop - End
  }

  return $return_string;

// function - End
}

// class - End()
}
?>