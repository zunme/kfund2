<?
//-----------------------------------------------------------------------
//  �Խ��� �������� ������� FTP Ŭ����
//  2007-10-05 : ������ 
//  �ٸ� ���� ���� FTP Ŭ������ �⺻����
//  ������ ������ �̳��� �Խ��ǿ� Ưȭ��
//  Ŭ�����̱� ������ ȥ�� �ϸ� �ȵ�.
//-----------------------------------------------------------------------
class file_UDC{
# class - Start()

	# ftp ���� ����
	var $host,$port,$id,$pw,$fconn;

	# ftp�� �����ؼ� ù��°�� �̵��� ���丮��
	var $first_move,$CP_CODE;
	
	# ���� ��� ���� ����
	var $filepath,$filename,$filesize,$virtual_filename, $extension,$name_non_extension,$max_size;
	
	# ���� ���� ���� ����
	var $delete_file_name,$old_file_name,$tmp_upload_files;

	##########
	# ������ ###############
	# ftp ���� ������ ���� #
	########################
	function file_UDC($first_move,$CP_CODE){
		// ���� ���� ���丮�� ������ �ʾ�����
		if(!$first_move){echo "���丮�� �����ؾ��մϴ�.";exit;}

		# ���� ���� ��
		$this->host			=	"127.0.0.1";
		$this->port			=	"21";
		$this->id				=	"sing0912";
		$this->pw				=	"tjsenxkfghks1";
		$this->first_move	=	ImageBase;
		$this->CP_CODE	=	$CP_CODE; // ����� ID���� ������ ���� ���鶧 ���̴³� ������ ��� ���ϵ��丮 1���� ����

		# ���� ���ε� ���� 
		$this->tmp_upload_files	= array(); // ���ε� �Ǵ� ���ϵ��� ��ȯ�� �̸��� �迭�� �������ִ´�.
		$this->max_size				=	10000000;
	}
	
	# ���� ������ üũ
	function check_file_size($img_size){
		# true �� 1�� ��ȯ false �� 0�� ��ȯ
		if($img_size > $this->max_size){return "0";}else{return "1";}
	}

	# Ȯ���� ���� (Ȯ���ڸ� �߰������ָ� �ȴ�.) 
	function check_file_name($filename){
		
  # ���ϸ�� Ȯ���ڸ� �и�
		$tmp = explode(".",$filename);
		
		# Ȯ���� 
		$this->extension = $tmp[sizeof($tmp)-1];
		
		# Ȯ���ڸ� �� ���ϸ�
		$this->name_non_extension	=	$tmp[0];

		# true �� 1�� ��ȯ false �� 0�� ��ȯ
		if((strcmp($this->extension,"jpg")=="0" or strcmp($this->extension,"gif")=="0" or strcmp($this->extension,"JPG")=="0" or strcmp($this->extension,"GIF")=="0"  or strcmp($this->extension,"xls")=="0" or strcmp($this->extension,"doc")=="0" or strcmp($this->extension,"ppt")=="0" or strcmp($this->extension,"hwp")=="0" or strcmp($this->extension,"zip")=="0" or strcmp($this->extension,"txt")=="0"  or strcmp($this->extension,"pdf")=="0"  or strcmp($this->extension,"cab")=="0"))
		{return "1";}
		else{return "0";}
	}

	# ����� ���ϸ� (�� ���ϸ� ��� ��¥�� �ٿ��ش� ��: MGP0671(17061238).jpg )
	function make_file_name(){
		$file_name = str_replace(" ","",str_replace("+","",str_replace("-","",trim($this->name_non_extension))));
		$this->virtual_filename = $file_name."(".date("dhis",mktime()).").".$this->extension;
	} 

	# ����� ���ϸ��� ������ ���� 
	function return_file_name(){return $this->virtual_filename;}

	# ���� ���ε�
	function file_upload($fconn){
		# ���� ������,���ϸ� ���� (�Ѵ� 1�� ��쿡�� ���)
		if($this->check_file_size($this->filesize)==0 || $this->check_file_name($this->filename)==0)
		{return "0";}
		else{
			# ������ ���ε�� ���ϸ��� ������ش�
			$this->make_file_name();
			
			# ���� ���ε� (������ ok ���н� error ��ȯ)
			if(!@ftp_put($fconn, $this->virtual_filename, $this->filepath, FTP_BINARY))
			{return "error";}
			else{return "ok";}
		}
	}

	# ���� ���� 
	function file_delete(){if(@ftp_delete($this->fconn, $this->delete_file_name)) {return "ok";}else {return "error";}}
	
	# ���ε� �ǰ��ִ� ���ϵ��� ������ ��Ƶд�.
	function tmp_upload_files($upload_files,$filesize){
		$this->tmp_upload_files["filename"][]	=	$upload_files;
		$this->tmp_upload_files["size"][]		=	$filesize;
	}
	
	# ���� ���� ���ε� ���н� �ö��ִ� ���ϵ� ���� 
	function file_err_delete(){
		for($i = 0; $i < count($this->tmp_upload_files["filename"]); $i++){
			# ������ ������ �ش�
			$ftp_result = $this->main($filepath,$this->tmp_upload_files["filename"][$i],$filesize,"delete");
			if($ftp_result!="ok"){echo "���� ���� ���� : ftp���� ������ Ȯ���ϼ���."; exit;}
		}
	}

	# ���ε� ����������� ����ó��
	function error_process($ftp_result,$real_file_name,$filesize){
		// ���ε� ���н�
		if($ftp_result!="ok"){
			$inst_location	=	new Location_Control(); // location���� Ŭ����
			$msg_X	=	sprintf("���� ���ε� ���� : %s MB�� �Ѱų� ���Ŀ� ���� �ʴ� �����ʹ� ���ε� �� �� �����ϴ�.",(($this->max_size/1000)/1000));

			# ���� ���� ���ε� ���н� ������ �ö��ִ� ���ϵ� ���� 
			$this->file_err_delete();

   $inst_location->error($msg_X);
			exit;
		}
		// ���ε� ������
		else{
			// ���� ���ε����� ������ �̸��� �迭�� �������ִ´�
			$this->tmp_upload_files($real_file_name,$filesize);
		}
	}

	# ���� ���ε忡 �ʿ��� ���� ����
	function upload_info_set($filepath,$filename,$filesize){
		# ���� ���� ����
		$this->filepath		=	$filepath;
		$this->filename		=	$filename;
		$this->filesize		=	$filesize;
	}

	# ���� ���ε忡 �ʿ��� ���� ����
	function delete_info_set($filename){
		# ���� ���� ����
		$this->delete_file_name		=	$filename;
	}

	# ftp�� �α����ϰ� �ش� ���丮�� �̵��� �� �����ν��Ͻ��� $this->fconn �� �Ҵ�
	function ftp_login($Folder){
		# ftp ����
		$fconn = @ftp_connect(&$this->host, &$this->port) or die(" this->$host : $this->port - ���� ����"); 

		# �α���
		@ftp_login($fconn, &$this->id, &$this->pw) or die("FTP �α��� ����"); // $this->id - login  

		# ù��° ���丮 ����
		@ftp_chdir($fconn, $this->first_move) or die($fconn);

    # ���丮 ����
		# �켱 ��ü�ڵ� ���丮�� �ű��
		$change_result2 = @ftp_chdir($fconn, $this->CP_CODE); 

    if(!$change_result2){
			# ���丮�� ������ �ش�
			@ftp_mkdir($fconn,$this->CP_CODE) or die("���丮 ���� ����");

			# �ٽ� ���丮 ����
			@ftp_chdir($fconn, $this->CP_CODE) or die("e"); 
		}

		# �̹��� ���ε��� ���丮�� �ű��
		$change_result = @ftp_chdir($fconn, $Folder); 

		# ���丮 ���濡 �������� ��� ,���丮�� ���� ���� �ʴ°��
		if(!$change_result){
			# ���丮�� ������ �ش�
			@ftp_mkdir($fconn,$Folder) or die("���丮 ���� ����");

			# �ٽ� ���丮 ����
			$change_result = @ftp_chdir($fconn, $Folder) or die("e"); 
		}
		$this->fconn	 =	$fconn;
	}

	function ftp_close(){
		ftp_close($this->fconn);
	}

	# �����ÿ� OLD �̹��� �ҷ����̴� ��
	function call_old_img($old_file_name){
		$this->old_file_name	=	$old_file_name;
	}


	# ���� �޼ҵ�
	function main($filepath,$filename,$filesize,$action,$Folder='Board'){	

    # action�� ���� upload�� delete�� ����
		switch($action){
			case "upload" : 
				
				# ���ε带 ���� ���� ����
				$this->upload_info_set($filepath,$filename,$filesize);
				#  Ȯ���� üũ : Ȯ���ڴ� $this->extension
				$this->check_file_name($this->filename);
				# ftp�� �α���
				$this->ftp_login($Folder);
				# ������ ���ε��Ѵ�
				$result = $this->file_upload($this->fconn);
				$this->ftp_close();
				return $result;

			break;

			case "delete" : 
			
				# ���� ���� ���� ����
				$this->delete_info_set($filename);
				# ftp�� �α���
				$this->ftp_login($Folder);
				# ���� ����
				$result = $this->file_delete();
				$this->ftp_close();
				return $result;

			break;

			case "modify" : 
				
				# ���ε带 ���� ���� ����
				$this->upload_info_set($filepath,$filename,$filesize);
				
				#  Ȯ���� üũ : Ȯ���ڴ� $this->extension
				$this->check_file_name($this->filename);
				
				# ftp�� �α���
				$this->ftp_login($Folder);
				
				# ������ ���ε��Ѵ�
				$result = $this->file_upload($this->fconn);
				
				# ���ε忡 ����������� ���� ������ �����Ѵ�
				if($result=="ok"){
					# ���� ���� ���� ����
					$this->delete_info_set($this->old_file_name);

					# ftp�� �α���
					$this->ftp_login($Folder);
					
					# ���� ����
					$this->file_delete($this->fconn);
				}
				$this->ftp_close();
				return $result;
			break;
		}
	}

# class - End()
}
?>