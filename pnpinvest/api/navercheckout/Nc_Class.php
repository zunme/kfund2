<?
//-------------------------------------------------------------------------
//
//  ���̹� üũ�ƿ� ��ǰ CLASS : 2010-12-07 ������
//
//  üũ�ƿ� �ֹ��� ���� ��ǰ���� ������ ���� ó��
//  ��û���� - http://������������/������������?ITEM_ID=XXX&ITEM_ID=XXX&ITEM_ID=XXX
//  �����ּ� : http://minishop.ec.playautocorp.com/navercheckout/index.html?ITEM_ID=test05
//-------------------------------------------------------------------------

class NaverCheckOut {

  //-------------------------------------------------
  //  ������
  //-------------------------------------------------
  function NaverCheckOut($CP_CODE){

    //  Sock
    $this->main_Sock = new Mysql(DBTYPE_MAIN,DBHOST_MAIN, DBUSER_MAIN, DBPASS_MAIN, DBNAME_MAIN);
    $this->select_Sock = new Mysql(DBTYPE_SELECT,DBHOST_SELECT, DBUSER_SELECT, DBPASS_SELECT, DBNAME_SELECT);
    
    $this->DataBase = "EC_".$CP_CODE;

    //  class
    $this->inst_NcSelect = new NcSelect(&$this->select_Sock,&$this->DataBase,&$this,$this->MainDataBase,&$this->main_Sock);  // ��ǰ ������ȸ �޼ҵ�
    include ClassBase."/sql/shop/Shop_select_class.php";	 // Shop��ȸ Ŭ����
    $this->inst_SelectShop = new ShopSelect(&$this->select_Sock,&$this->DataBase,&$this,&$this->main_Sock);  // ��ǰ ������ȸ �޼ҵ�

    include ClassBase."/vips/none_class.php";	 // Shop��ȸ Ŭ����
    include ClassBase."/api/shop/Shop_View_class.php";	 // Shop��ȸ Ŭ����
    $this->inst_ShopView = new ShopView(&$this);  // ��ǰ ������ȸ �޼ҵ�

    $this->inst_FileIO = new FileIO(); // ���� IO ����
    $this->inst_Cate = new Cate();  // ī�װ�

    $this->CP_CODE_VALUE = $CP_CODE;

  // function - End
  }


  //-----------------------------------------------------------------------------
  //  ���� Param
  //-----------------------------------------------------------------------------
  function NaverCheckOut_Execution() {

    $ITEM_ID_TEMP = $_GET[ITEM_ID];

    if(is_array($ITEM_ID_TEMP)) {
      $ITEM_ID = @implode("','",$ITEM_ID_TEMP);
      $this->NaverCheckOut_Prod_Xml($ITEM_ID);
    }
    else {
      if($ITEM_ID_TEMP!='') {
        $ITEM_ID = $ITEM_ID_TEMP;
        $this->NaverCheckOut_Prod_Xml($ITEM_ID);
      }
      else {
        echo "�߸��� ���Դϴ�.";
        exit;
      }
    }

  // funciton - End
  }


  //-----------------------------------------------------------------------------
  //  ��ǰ ���� ��� (XML)
  //-----------------------------------------------------------------------------
  function NaverCheckOut_Prod_Xml($Goods_no) {

    //  ��ǰ����
    $data = $this->inst_NcSelect->GetGoodSelect($this->CP_CODE_VALUE, $Goods_no);

    // ���ΰ� ���� ��ü��ǰ
    $CouponInfoList = $this->inst_SelectShop->GetCouponInfoNewList('','');
    $this->inst_ShopView->CouponDiscountSet(&$data,$CouponInfoList,'goods_no','sale_cost','cate_no',true);

    // ���̹� üũ�ƿ� ������ ���� ����ǰ
    $CouponInfoList = $this->inst_SelectShop->GetCouponInfoNhnList($_GET,$data);
    $this->inst_ShopView->CouponDiscountNhnSet(&$data,$CouponInfoList,'goods_no','sale_cost','cate_no',true);

    //  ī�װ� ����
    $temp = $this->inst_NcSelect->GetCategorySelect($this->CP_CODE_VALUE);

    //  ī�װ��� ī�װ� ��� ��Ī�ؼ� ���ڿ���
    $Have_Catelist	=	$this->inst_Cate->find_parent($data[cate_no],&$temp);
    $CateCount = count($Have_Catelist[Str]);

    header('Content-Type: application/xml;charset=euc-kr');

    $Str = "";
    $Str = $Str."<?xml version=\"1.0\" encoding=\"euc-kr\" ?>\n";
    $Str = $Str."<response>\n";

    for($i=0; $i<count($data[goods_no]); $i++) {

      $goods_no = $data[goods_no][$i];
      $goods_name = $data[goods_name][$i];
      $price = $data[sale_cost][$i];
      $price2 = $data[sale_costbk_nhn][$i];
      $contents = $data[contents][$i]; //�󼼼���
      $volume = $data[volume][$i]; //������
      $pgurl = "http://".$_SERVER[HTTP_HOST]."/?action=Detail&GoodsCode=$goods_no";
      $igurl = Img_Domain."/".$this->CP_CODE_VALUE."/Goods/".$data[file_name][$i];
      $sgurl_name=str_replace(".jpg","_s.jpg",$data[file_name][$i]);
      $sgurl = Img_Domain."/".$this->CP_CODE_VALUE."/Goods/".$sgurl_name; //����� �̹���
      $temp_cate = explode(" > ",$Have_Catelist[Str][$i]); //ī�װ� 

      //�ɼ�
      $Opt=explode("^||^",$data[opt][$i]);
      //�߰����ſɼ�
      $AddOpt=explode("^||^",$data[addopt][$i]);

      $opt_data="";
      if(trim($Opt[2])!="" && eregi("manual\[_Opt_\]",$Opt[2])){
        $opt_3=explode("manual[_Opt_]",$Opt[2]);
        $opt_seri=unserialize($opt_3[1]);

        if(trim($Opt[0])==trim($Opt[1])){
          //�ɼ�1
          $opt_data = $opt_data."   <options>\n";// <!-- ��ǰ�� �ɼ��� ������ �� ������ ��� �ȴ�. -->
          if(is_array($opt_seri)){
            $opt_data = $opt_data."    <option name=\"".$this->ChangeWord($Opt[0])."\">\n";// <!-- �ɼ� ���� �̸� -->
            foreach($opt_seri as $se_k=>$se_v) {
              foreach($se_v as $kk=>$vv) {
                $opt_data = $opt_data."       <select><![CDATA[".$kk."]]></select>\n";// <!-- �ش� �ɼ��� ���� ���� -->           
              }
              continue;
            }
          }
          $opt_data = $opt_data."     </option>\n";
          $opt_data = $opt_data."   </options>\n";// <!-- ��ǰ�� �ɼ��� ������ �� ������ ��� �ȴ�. -->

        }else{
            //�ɼ�1
            $opt_data = $opt_data."   <options>\n";// <!-- ��ǰ�� �ɼ��� ������ �� ������ ��� �ȴ�. -->
            if(is_array($opt_seri)) {
              $opt_data = $opt_data."    <option name=\"".$this->ChangeWord($Opt[0])."\">\n";// <!-- �ɼ� ���� �̸� -->
              foreach($opt_seri as $se_k=>$se_v) {
                foreach($se_v as $kk=>$vv) {
                  $opt_data = $opt_data."       <select><![CDATA[".$se_k."]]></select>\n";// <!-- �ش� �ɼ��� ���� ���� -->           
                }
              }
            }
            $opt_data = $opt_data."     </option>\n";
            
						if(@trim($Opt[1])!='') {
							//�ɼ�2
							//if(sizeof($opt_seri)>1){
								$opt_data = $opt_data."    <option name=\"".$this->ChangeWord($Opt[1])."\">\n";// <!-- �ɼ� ���� �̸� -->
								foreach($opt_seri as $se_k=>$se_v) {
									foreach($se_v as $kk=>$vv) {
										$opt_data = $opt_data."       <select><![CDATA[".$kk."]]></select>\n";// <!-- �ش� �ɼ��� ���� ���� -->           
									}
								}
							//}
							$opt_data = $opt_data."     </option>\n";
						}
            $opt_data = $opt_data."   </options>\n";// <!-- ��ǰ�� �ɼ��� ������ �� ������ ��� �ȴ�. -->
        }
      }else{
        //�ɼǾ���
        $opt_data="";
      }     

      $Str = $Str." <item id=\"".$goods_no."\">\n";// <!-- ��ǰ ID. -->
      $Str = $Str."   <name><![CDATA[".$goods_name."]]></name>\n";
      $Str = $Str."   <url>".$this->ChangeWord($pgurl)."</url>\n";// <!-- ��ǰ ���� ������ url -->";
      $Str = $Str."   <description><![CDATA[".$contents."]]></description>\n";//
      $Str = $Str."   <image>".$this->ChangeWord($igurl)."</image>\n";// <!-- ��ǰ image url -->
      $Str = $Str."   <thumb>".$this->ChangeWord($sgurl)."</thumb>\n";// <!-- ��ǰ ����� image url -->
      $Str = $Str."   <price>".$price."</price>\n";// <!-- ���ΰ� + �������� -->
      $Str = $Str."   <price2>".$price2."</price2>\n";// <!-- ���� ���� -->
      $Str = $Str."   <quantity>".$volume."</quantity>\n";// <!-- ��� -->
      
      //��ǰ�� �ɼ��� ������ �ɼ� ������ ��� �ȴ�.
      $Str = $Str.$opt_data;

      $Str = $Str."   <category>\n";// <!-- ī�װ� �ִ� 4�ܰ� ī�װ����� ǥ�� �����ϴ�. (first ~ fourth) --->
      $Str = $Str."     <first>".$this->ChangeWord($temp_cate[0])."</first>\n";
      if($temp_cate[1]!="") $Str = $Str."     <second>".$this->ChangeWord($temp_cate[1])."</second>\n";
      if($temp_cate[2]!="") $Str = $Str."     <first>".$this->ChangeWord($temp_cate[2])."</first>\n";
      if($temp_cate[3]!="") $Str = $Str."     <fourth>".$this->ChangeWord($temp_cate[3])."</fourth>\n";
      $Str = $Str."   </category>\n";
      $Str = $Str." </item>\n";

    // loop - End
    }
    $Str = $Str."</response>";

    echo $Str;

  // funciton - End
  }


  //-----------------------------------------------------------------------------
  //  ����� ��ȯó��
  //-----------------------------------------------------------------------------
  function ChangeWord($word) {
    if(ereg('\&',$word)) { $word = str_replace('&','&amp;',$word); }
    if(ereg('<',$word)) { $word = str_replace('<','&lt;',$word); }
    if(ereg('>',$word)) { $word = str_replace('>','&gt;',$word); }
    if(ereg('"',$word)) { $word = str_replace('"','&quot;',$word); }
    Return $word;
  // funciton - End
  }


  function NHNViewOrder() {

    // --------------------------------------------------------
    // �Ķ��Ÿ ���� 
    @preg_match_all( '/(.*)=(.*)&/Umsi' , $_SERVER['QUERY_STRING'].='&' , $re );
    $order_id = $_GET['ORDER_ID'];
    $item_ids = array();
    foreach($re[1] as $key=>$val) {
      if( $val == 'ITEM_ID' && trim($re[2][$key])!='' ) $item_ids[] = trim($re[2][$key]);
    }

    // --------------------------------------------------------
    // �⺻����
    $Mini_Info = $this->inst_NcSelect->MiniInfoSelect();
    $com_name = trim($Mini_Info[com_name]);
    $address = trim($Mini_Info[address1])." ".trim($Mini_Info[address2]);

    // --------------------------------------------------------
    // üũ�ƿ� �⺻ ���� 
    $Checkout_Info = $this->inst_NcSelect->NCInfoSelect();
      
    // --------------------------------------------------------
    // �ֹ� ����
    $Order_Match_Info = $this->inst_NcSelect->SelectOrder($_GET['ORDER_ID'],$_GET['ITEM_ID']);

    // --------------------------------------------------------
    // ��������

    // ��ǰ�� ����
    $re_addr = trim($Checkout_Info[re_deliv_addr1][0])." ".trim($Checkout_Info[re_deliv_addr2][0]);
    if(trim($re_addr) == "") { // ��ǰ�� �ּҰ� ������ �⺻ ���� ���
      $re_addr = $address;
    }

    $enckey = urlencode(base64_encode(time()));
    
    if(trim($Order_Match_Info[number]) != "") {
      $xml_header = '<?xml version="1.0" encoding="euc-kr" ?> <deliveries> ';
      $str = "";
      

      // �ݺ� xml 
      foreach($Order_Match_Info[number] as $key=>$val) {

        $itemID = trim($Order_Match_Info['goods_code'][$key]);
        $companyName = trim($Order_Match_Info['pa_sender'][$key]);
        $companyNameEncode = urlencode(trim($Order_Match_Info['pa_sender'][$key])); // ���̹����� utf-8 ���� ������ �Ǿ� urlencode cjflgka 2011-01-27 �����
        $trackingNumber = trim($Order_Match_Info['sendno'][$key]);
        $status = trim($Order_Match_Info['state'][$key]);


// �̴�� ���� ���� ���� ���� ���
$str.= <<<xmlstr

<delivery>
<itemID><![CDATA[$itemID]]></itemID>
<companyName><![CDATA[$companyName]]></companyName>
<trackingNumber><![CDATA[$trackingNumber]]></trackingNumber>
<status><![CDATA[$status]]></status>
<sellerName><![CDATA[$com_name]]></sellerName>
<senderAddress><![CDATA[$address]]></senderAddress>
<senderName><![CDATA[$com_name]]></senderName>
<returnAddress><![CDATA[$re_addr]]></returnAddress>
<returnName><![CDATA[$com_name]]></returnName>
<statusURL type="new">
<![CDATA[http://www.playauto.co.kr/nhn/tracking.html?sn=$companyNameEncode&so=$trackingNumber&enckey=$enckey]]>
</statusURL>
</delivery>

xmlstr;
      }
      $xml_footer = '</deliveries> ';
      echo $xml_header.$str.$xml_footer;
    }
    exit;
  // funciton - End
  }

  //-----------------------------------------------------------------------------
  //  ���̹� ���� ���� �޾� ��� �����ϱ�
  //-----------------------------------------------------------------------------
  function NhnCouponInfo() {
        
    require "xml_parser_coupon.php";

    if($_GET[uisl] == "uisl" ) {
      require_once 'HTTP/Request.php';
      //http post������� ��û ����
      $rq = new HTTP_Request("http://dd.playauto.co.kr/uisl/nhn_cou3.xml");
      $rq->addHeader("Content-Type", "text/xml;charset=UTF-8");
      $rq->setBody($request_body);

      $result = $rq->sendRequest();
      if (PEAR::isError($result)) {
         echo "ERROR[NHN0001]". $result->toString(). "\n";
         return;
      }

      //����޽��� Ȯ��
      $rcode = $rq->getResponseCode();
      if($rcode!='200'){
         echo "ERROR[NHN0002]: http response code=". $rcode. "\n";
         return;
      }
      $xml_parse =& new xml_parse();
      $dataList = $xml_parse->parse($rq->getResponseBody());
    }else{
      $xml_parse =& new xml_parse();
      $dataList = $xml_parse->parse($_REQUEST['xml_data']);
    }

    $insert_array = array();
    $insert_array['wdate'] = date('Y-m-d H:i:s');
    $insert_array['templateId'] = $dataList['templateId']; //�������ø�������Ű
    $insert_array['type'] = $dataList['productId'] == "" && $dataList['categoryId'] == "" ? 0:1; // rate=>����, fix=>������
    $insert_array['dc_type'] = $dataList['type']; // rate=>����, fix=>������
    $insert_array['dc'] = $dataList['dc']; //���ΰ� �� ������
    $insert_array['maxPrice'] = $dataList['maxPrice']; // ����������ִ�ݾ�
    $insert_array['issueMain'] = $dataList['issueMain']; // ������üNBP/FRAN(������)
    $insert_array['beginDttm'] = $dataList['beginDttm']; // ����������۽ð�
    $insert_array['endDttm'] = $dataList['endDttm']; // ������������ð�
    $insert_array['status'] = $dataList['status']; // PUBLS(����)/PAUSE(�Ͻ�����)/TERM(�ߴ�)
    $insert_array['mbrIsueQty'] = $dataList['mbrIsueQty']; // �ִ����ΰ��� 0�̸鹫��/ 0 �̾ƴϸ鰳���ִ����ΰ���

    if($dataList['templateId'] != "") {
	    $goreal = "not";
      $pa_templateId = $this->select_Sock->fetch_key("select templateId from Coupon_Basic_Nhn where templateId='".$dataList['templateId']."'");
      if(trim($pa_templateId[templateid][0]) != ""){
  	    if($insert_array['status'] != "READY"){
          $sql = "update ".$this->DataBase.".Coupon_Range_Nhn set cancel=1 where templateId='".$dataList['templateId']."'" ;
          $this->select_Sock->execute($sql);
        }
        $update_array = array();
        $result = $this->select_Sock->Array_update($this->DataBase.".Coupon_Basic_Nhn",$insert_array,"templateId='".$dataList['templateId']."'");
        if($result == "Success") {
          $re_msg = $_REQUEST['xml_data'];

          // ���� ������ �����ϸ� adb�� �ֱ� - ���� ����� ���̹��� �����ϱ����� ��ȸ��
          $update_aa = array();
          if($insert_array['status'] != "READY"){
            $update_aa[state] = 5;
						$update_aa[cdate] = date('Y-m-d H:i:s');
          }else{
            $update_aa[state] = 2;
          }
          $result = $this->main_Sock->Array_update("ECHost.coupon_edate_info",$update_aa,"templateId='".$dataList['templateId']."' and cp_code='".$this->CP_CODE_VALUE."'");

		    $goreal = $insert_array['status'] == "READY" ?"go":$goreal;
        }else{
          $re_msg = "N";
        }
      }
	  if(trim($pa_templateId[templateid][0]) == "" || $goreal == "go"){
        $result = $this->select_Sock->Array_insert($this->DataBase.".Coupon_Basic_Nhn",$insert_array);
        if($result == "Success" || $goreal == "go") {

          if($dataList['templateId'] != "") {

            //��ǰ �ڵ庰 �϶�
            if(is_array($dataList['productId'])) {
              foreach($dataList['productId'] as $key=>$val) {
                // ��ǰ�� ó�� ���� �ֱ�
                $insert_array2 = array();
                $insert_array2['wdate'] = $insert_array['wdate'];
                $insert_array2['templateId'] = $insert_array['templateId']; //�������ø�������Ű
                $insert_array2['v_type'] = 1;
                $insert_array2['productId'] = $val;
                $this->select_Sock->Array_insert($this->DataBase.".Coupon_Range_Nhn",$insert_array2);                
              }
            }else{
              // ��ǰ�� ó�� ���� �ֱ�
              $insert_array2 = array();
              $insert_array2['wdate'] = $insert_array['wdate'];
              $insert_array2['templateId'] = $insert_array['templateId']; //�������ø�������Ű
              $insert_array2['v_type'] = 1;
              $insert_array2['productId'] = $dataList['productId']; // ��ǰ�ڵ�
              $this->select_Sock->Array_insert($this->DataBase.".Coupon_Range_Nhn",$insert_array2);
            }

            //ī�װ��� �ϴ�
            if(is_array($dataList['categoryId'])) {
              foreach($dataList['categoryId'] as $key=>$val) {
                // ��ǰ�� ó�� ���� �ֱ�
                $insert_array2 = array();
                $insert_array2['wdate'] = $insert_array['wdate'];
                $insert_array2['templateId'] = $insert_array['templateId']; //�������ø�������Ű
                $insert_array2['v_type'] = 2;
                $insert_array2['categoryId'] = $val;
                $this->select_Sock->Array_insert($this->DataBase.".Coupon_Range_Nhn",$insert_array2);
              }

            }else{
              // ��ǰ�� ó�� ���� �ֱ�
              $insert_array2 = array();
              $insert_array2['wdate'] = $insert_array['wdate'];
              $insert_array2['templateId'] = $insert_array['templateId']; //�������ø�������Ű
              $insert_array2['v_type'] = 2;
              $insert_array2['categoryId'] = $dataList['categoryId'];
              $this->select_Sock->Array_insert($this->DataBase.".Coupon_Range_Nhn",$insert_array2);
            }
          }
          $re_msg =  $_REQUEST['xml_data'];
        }else{
          $re_msg =  "N";
        }
        
        // ���� ������ �����ϸ� adb�� �ֱ� - ���� ����� ���̹��� �����ϱ����� ��ȸ��
        $insert_aa = array();
        $insert_aa['wdate'] = $insert_array['wdate'];
        $insert_aa['cp_code'] = $this->CP_CODE_VALUE;
        $insert_aa['templateId'] = $insert_array['templateId']; //�������ø�������Ű
        $insert_aa['sdate'] = $dataList['beginDttm'];
        $insert_aa['edate'] = $dataList['endDttm'];
        $this->main_Sock->Array_insert("ECHost.coupon_edate_info",$insert_aa);
      }
     }else{
      $re_msg =  "N";
    }

    echo $re_msg;
    
  // funciton - End
  }

  //-----------------------------------------------------------------------------
  //  ���̹� ������ ���� ����� ��ǰ �α׳����� ��ǰ�� ���� ���� ������Ʈ �ϱ� 
  //-----------------------------------------------------------------------------
  function NhnGoodsEditUpdate() {
    // ������Ʈ ����
    $up_goods_dt = array();
    $up_goods_dt['edit_prg'] = "1";
    $up_log_dt = array();
    $up_log_dt['state'] = "1";

    $goods_update_all = $this->select_Sock->fetch("select number from ".$this->DataBase.".Coupon_Goods_Update_Log where state=0 and goods_no = 'ALL' and wdate > DATE_ADD(now(), interval -1 day) ");
    if($goods_update_all[0]['number'] != "") {
      // ��ü ������ ��� ����ǰ �����̶� ��ü �����ϸ��
			$ugel = array();
			$ugel[Goods_no] = '__all__';
			$ugel[Goods_edit_type] = '1';
			$ugel[Goods_edit_date] = "now()";
			$this->select_Sock->Array_insert_dupl("$this->DataBase.Goods_Edit_Log",$ugel,"Success",false);
    }else{

      // ������Ʈ ��� ��ǰ ����
      $goods_update_list = $this->select_Sock->fetch("select number,goods_no from ".$this->DataBase.".Coupon_Goods_Update_Log where state=0 and wdate > DATE_ADD(now(), interval -1 day) ");
      $goods_update_list2 = @array_chunk($goods_update_list,"500");
      
      if(is_array($goods_update_list2)) {
        // 500�Ǿ� ��� ��ǰ�� ���濩�� ������Ʈ 
        foreach($goods_update_list2 as $key=>$val) {
          $dataList = array();
          foreach($val as $key2=>$val2) {
            if($val2['goods_no'] != "") {
							$ugel = array();
							$ugel[Goods_no] = $val2['goods_no'];
							$ugel[Goods_edit_type] = '1';
							$ugel[Goods_edit_date] = "now()";
							$result = $this->select_Sock->Array_insert_dupl("$this->DataBase.Goods_Edit_Log",$ugel,"Success",false);
              if($result == "Success") {
                $dataList[] = $val2['number'];
              }
            }
          }
          // ������Ʈ Ȯ�� �Ȱ͸� ���� �Ϸ� ������ 1 �� ����
          $result = $this->select_Sock->Array_update($this->DataBase.".Coupon_Goods_Update_Log",$up_log_dt,"number in ('".implode("','",$dataList)."')");
          if($result == "Success") {
            echo "<li> ".$key." : 500�� ���ε� �Ϸ�";
          }
        }
      }
    }
    echo "<li> ��� ���ε� �Ϸ�";
  }

  //-----------------------------------------------------------------------------
  //  ���̹� ������ ���� ����� ��ǰ ã�� ��� ��ǰ ���� ���� ������Ʈ �ϱ�
  //  �� ���� ����� ���� ���� ��ǰ�� ���� �����ǰ�� �ٸ��� �־� ã�� ���� �α׿� ������ ������
  //-----------------------------------------------------------------------------
  function NhnCouponToGoodsEdit($data) {
    $coupon_Info = array();
    if($data[type] == "MATCHING_CATE") {
      $coupon_Info['MATCHING_CATE'] = "OK";
      $coupon_Info['goods_no_array'] = $data['goods_no_array'];
    }else{
      // �ű� ����
      $templateId = str_replace(",","','",$data['templateId']);
      $where = "and a.templateId in ('$templateId')";
      $where_type = $data[type];
      if($data[type2] == "END") {
        $coupon_Info = $this->inst_SelectShop->GetCouponInfoNhnList("","",$where,$where_type,"1");
      }else{
        $coupon_Info = $this->inst_SelectShop->GetCouponInfoNhnList("","",$where,$where_type);
      }
    }

    echo "<li> �ű����� ������� - ".date('Y-m-d H:i:s');
    // Ÿ�Ժ� ó��
    if( $data[type] == "NEW" ) {
      $this->NhnCouponToGoodsEditUpLog($coupon_Info); // ���� ���� �α׿� ���
      $this->NhnGoodsEditUpdate(); // ���� ���� ��ǰ�� ���
    }elseif( $data[type] == "EDIT" ) {
      // ���� ����
      $up_log_dt = array();
      $up_log_dt['state'] = "0";
      $result = $this->select_Sock->Array_update($this->DataBase.".Coupon_Goods_Update_Log",$up_log_dt,"templateId in ('$templateId')");

      // ��ü ���� ������ ������ ������ ��� ��ǰ ������Ʈ �ؾ� �ϴ� �ٸ� ���� ������ �ʿ� ����
      if(array_search("0",$coupon_Info[type]) === false) {
        $this->NhnCouponToGoodsEditUpLog($coupon_Info); // ���� ���� �α׿� ���
      }
      $this->NhnGoodsEditUpdate(); // ���� ���� ��ǰ�� ���
    }elseif( $data[type] == "END" ) {
      // ���� ����
      $this->NhnCouponToGoodsEditUpLog($coupon_Info); // ���� ���� �α׿� ���
      $this->NhnGoodsEditUpdate(); // ���� ���� ��ǰ�� ���
      if($data[type2] == "END") {
        $this->select_Sock->execute("delete from Coupon_Range_Nhn where cancel=1 and templateId in ('$templateId')"); // ���̹��� ����� ���� ���� �����ϱ� 2011-11-30 �����
      }
      $this->select_Sock->execute("delete from Coupon_Goods_Update_Log where state=1 and templateId in ('$templateId')");
    }elseif( $data[type] == "MATCHING_CATE" ) {
      // ��Ī ī�װ�
      $this->NhnCouponToGoodsEditUpLog($coupon_Info); // ���� ���� �α׿� ���
    }elseif( $data[type] == "reflash" ) { 
      // Coupon_Goods_Update_Log�� �׾Ƴ� ��������� ���������� Goods_Edit_Log �� �ȵ�� ����� ��õ� �Ҷ����� 2011-08-11 �����
			// http://[cp_code].ec.playautocorp.com/navercheckout/goods_edit_coupon_prg.html?type=reflash
      $this->NhnGoodsEditUpdate(); // ���� ���� ��ǰ�� ���
    }
  }
  //-----------------------------------------------------------------------------
  //  ���̹� ������ ���� ����� ��ǰ �α� ���̺� �����ϱ�
  //-----------------------------------------------------------------------------
  function NhnCouponToGoodsEditUpLog($coupon_Info) {
    
    echo "<li> �������� ���� ������ ���� Ÿ�Կ� ���� ���� ���� - ".date('Y-m-d H:i:s');
    
    // ���� ������ ��ü�ϰ�� ��� �Ǹ��� ��ǰ ���� ��� 
    if(@array_search("0",$coupon_Info[type]) !== false) {
      // ��ǰ�� ó�� ���� �ֱ�
      $insert_log = array();
      $insert_log['wdate'] = date('Y-m-d H:i:s');
      $insert_log['templateId'] = $coupon_Info['templateid'];
      $insert_log['goods_no'] = "ALL";
      $insert_log['state'] = 0;
      $result = $this->select_Sock->Array_insertToUpdate($this->DataBase.".Coupon_Goods_Update_Log",$insert_log);
      if($result == "Success") {
        echo "<li> ���� Ÿ�� : ��ü == ����Ϸ� - ".date('Y-m-d H:i:s');
      }else{
        echo "<li><b><font color=red>���� Ÿ�� : ��ü == ������� - ".date('Y-m-d H:i:s')."</font></b>";
      }
    }else{
      if($coupon_Info[MATCHING_CATE] == "OK") {
        // ���̹� ī�װ� ���濡 ���� �������� ���� ������ �����Ͽ� ���� �ҽ�ó����
        $goods_no_array = explode(",",$coupon_Info['goods_no_array']);
        $up_field = array();
        foreach($goods_no_array as $key=>$val) {
          $up_field[productid][] = trim($val);
          $up_field[productid_pid][] = "nhn_cate";  
        }

      }else{
        $up_field = array();
        
        echo "<li> ���� Ÿ�� �� ��ǰ�� ī�װ� �и� ���� - ".date('Y-m-d H:i:s');
        foreach($coupon_Info[number] as $key=>$val) {
          // ��ǰ�ڵ�
          if( trim($coupon_Info[productid][$key]) != "" && trim($coupon_Info[v_type][$key]) == "1") {
            $up_field[productid][] = trim($coupon_Info[productid][$key]);
            $up_field[productid_pid][] = trim($coupon_Info[templateid][$key]);
          }
          //ī�װ�
          if( trim($coupon_Info[categoryid][$key]) != "" && trim($coupon_Info[v_type][$key]) == "2") {
            $up_field[categoryid][] = trim($coupon_Info[categoryid][$key]);
            $up_field[categoryid_pid][] = trim($coupon_Info[templateid][$key]);
          }          
        }
      }

      if(sizeof($up_field[productid]) > 0 ) {
        // ��ǰ �ڵ�
        echo "<li> ���� Ÿ�� : ��ǰ�ڵ� == ������� - ".date('Y-m-d H:i:s');
        foreach($up_field[productid] as $key=>$val) {
          $insert_log = array();
          $insert_log['wdate'] = date('Y-m-d H:i:s');
          $insert_log['templateId'] = $up_field[productid_pid][$key];
          $insert_log['goods_no'] = $val;
          $insert_log['state'] = 0;
          $result = $this->select_Sock->Array_insertToUpdate($this->DataBase.".Coupon_Goods_Update_Log",$insert_log); 
          if($result == "Success") {
            echo "<li> ���� Ÿ�� : ��ǰ�ڵ� == $key �� ����Ϸ� - ".date('Y-m-d H:i:s');
          }else{
            echo "<li><b><font color=red>���� Ÿ�� : ��ǰ�ڵ� == $key �� ������� ( $result )- ".date('Y-m-d H:i:s')."</font></b>";
          }
        }
        echo "<li> ���� Ÿ�� : ��ǰ�ڵ� - ��� ó���Ϸ� - ".date('Y-m-d H:i:s');
      }elseif(sizeof($up_field[categoryid]) > 0 ) {
        // ī�װ�
        echo "<li> ���� Ÿ�� : ī�װ� == ������� - ".date('Y-m-d H:i:s');
        $up_categoryid_array = array();
        foreach($up_field[categoryid] as $key=>$val) {
          $where = array();
          for($i=1; $i<=4; $i++) {
            $where[] = "cate_code".$i." = '".$val."'"; // ī�װ� �з��� where�� ����
          }
          $where = implode(" or ",$where);
          $cate_m_list = $this->select_Sock->fetch_keynm("select site_code from Nhn_Cate_Info where ".$where , "site_code");
          $up_categoryid_array[$up_field[categoryid_pid][$key]] = is_array($up_categoryid_array[$up_field[categoryid_pid][$key]]) ? $up_categoryid_array[$up_field[categoryid_pid][$key]]:array();
          $up_categoryid_array[$up_field[categoryid_pid][$key]] = @array_merge($up_categoryid_array[$up_field[categoryid_pid][$key]],$cate_m_list[site_code]);
          echo "<li> ���� Ÿ�� : ī�װ�( $val ) == ���� ��ǰ ���� �Ϸ� - ".date('Y-m-d H:i:s');
        }
        
        $ii = 0;
        foreach($up_categoryid_array as $key=>$val) {
          foreach($val as $key2=>$val2) {
            $insert_log = array();
            $insert_log['wdate'] = date('Y-m-d H:i:s');
            $insert_log['templateId'] = $key;
            $insert_log['goods_no'] = $val2;
            $insert_log['state'] = 0;
            $result = $this->select_Sock->Array_insertToUpdate($this->DataBase.".Coupon_Goods_Update_Log",$insert_log); 
            if($result == "Success") {
              echo "<li> ���� Ÿ�� : ī�װ� == $ii �� ����Ϸ� - ".date('Y-m-d H:i:s');
            }else{
              echo "<li><b><font color=red>���� Ÿ�� : ī�װ� == $ii �� ������� - ".date('Y-m-d H:i:s')."</font></b>";
            }
            $ii++;
          }
        }
        echo "<li> ���� Ÿ�� : ī�װ� == ��� ó���Ϸ� - ".date('Y-m-d H:i:s');
      }else{
        // �� ó��
        echo "<li><b><font color=red> ������ ī�װ� ���� ���� - ".date('Y-m-d H:i:s')."</font></b>";
      }
    }
  }

}  // class - End

?>