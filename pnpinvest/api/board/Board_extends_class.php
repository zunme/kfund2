<?
class BoardExtends{

	// ���� �����Ҷ� �۷ι��� ���Ǵ³�
	var $Tmp_delfiles;
	var $Img_widmax = 0;
	var $DataBase;
	var $Sock;
	var $inst_main;

	//-------------------------
	//   ������
	//-------------------------
	function BoardExtends($inst_main){
	//  �Խ��� ���� ���̵�,�̸���� / ��¥ ǥ�� : 2009-02-02 ������
	//$this->User_Dir = SiteBase."/configs/BoardOPT";
	$this->inst_main = $inst_main;
	$this->inst_String = $inst_main->inst_String;
	$this->LoadBoardOPT();
	}

	//-----------------------------------------------------------------------------
	//  ����ɼ� �δ� 
	//  �����ڿ��� ������ �ɼ� �ε� 
	//  DB�� ��ȯ : 2011-03-03 ������
	//  �ν���� CP_CODE�� ���� ��ġ�� �����ǹǷ� ��ġ�� �ʴ� ó�� �߰� : 2011-03-09 ������
	//-----------------------------------------------------------------------------
	function LoadBoardOPT(){
		if($this->inst_main->DataBase != 'EC_'){
			if($_GET[action] == 'Detail' || $_GET[action] == 'AjaxQna' ){
				$query	= "select dateview from ".$this->inst_main->DataBase.".Board_list where table_name='Board_goods_qna';";
				$data	=	$this->inst_main->select_Sock->fetch($query);
			}else{
				$query	= "select dateview from ".$this->inst_main->DataBase.".Board_list where table_name='Board_gongji';";
				$data	=	$this->inst_main->select_Sock->fetch($query);
			}
		$this->BoardOPT[dateview] = $data[0][dateview] != '' ? $data[0][dateview] : 'ymdm';
		$this->BoardOPT[writer] = 'name';    
		}
	}// funciton - End
	//-----------------------------------------------------------------------------
	//  �Խ��� ��� �ɼ� �������ִ³� 
	/*
	//  ������� ���� 2009-02-02 ������ 
	//  ������ ���� ������ ������ ��ȯ����	
	$this->SwitchOPT(&$data[writer_name][0],'writer');
	$this->SwitchOPT(&$data[regi_date][0],'dateview');
	*/
	//-----------------------------------------------------------------------------
	
	function SwitchOPT($data,$section='list') {
	// ��� ������ ������ ��� 
		if(is_array($data) && $section=='list') {
			foreach($data as $key=>$val) {
				foreach($val as $key2=>$val2) {
				//  �ۼ��� 
				//  ��� �߰� : 2009-11-30 ������
					if($key2=='writer_name'){
						if($this->BoardOPT[writer]=='id'){ 
							$data[$key][$key2] = $data[$key]['writer_id'] == '_ADMIN_' ? '������' : $data[$key]['writer_id']; 
						} 
					}elseif($key2=='regi_date'){ 
					//  �����ϰ�� allŸ������ ���� : 2010-12-30 ������
						if(ereg('ecadmin.playautocorp.com',$_SERVER[HTTP_HOST])){ 
							$data[$key][$key2] = $this->inst_String->ext_date($data[$key][$key2],'all');
						}else{ 
							$data[$key][$key2] = $this->inst_String->ext_date($data[$key][$key2],trim($this->BoardOPT[dateview])); 
						}
					}elseif($key2=='c_name'){ 
						if($this->BoardOPT[writer]=='id'){
							$data[$key][$key2] = $data[$key]['c_id']=='_ADMIN_' ? '������' : $data[$key]['c_id'];
						} 
					}
				}// loop - End
			}// loop - End
		}else{//���ϰ��
		//  ������ ���� ���� 
			if(trim($data[writer_name][0])!=""){
				if($this->BoardOPT[writer]=='id'){
					$data[writer_name][0] = $data[writer_id]['writer_id'][0]=='_ADMIN_' ? '������' : $data['writer_id'][0];
				}
				//  �����ϰ�� allŸ������ ���� : 2010-12-30 ������
				if(ereg('ecadmin.playautocorp.com',$_SERVER[HTTP_HOST])){
						$data[regi_date][0] = $this->inst_String->ext_date(&$data[regi_date][0],'all');
				}else{ 
					$data[regi_date][0] = $this->inst_String->ext_date(&$data[regi_date][0],$this->BoardOPT[dateview]);
				}
			}
		}
	}// funciton - End


	//-----------------------------------------------------------------------------
	//  �Խ��� ��� �ɼ� �������ִ³� 2
	//  ���� ������ ���ǰԽ��� ���¿� �°� ���� : 2011-03-07 ������
	//-----------------------------------------------------------------------------
	function SwitchOPT2($data){
		// ��� ������ ������ ��� 
		if(is_array($data)){
			foreach($data as $key=>$val){
				$data[$key][regi_date] = $this->inst_String->ext_date($data[$key][regi_date],trim($this->BoardOPT[dateview]));
			}
		}
	}// funciton - End

}// class - End()
?>