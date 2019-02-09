<?
//-------------------------------------------------------------------------
//
//  ���̹� üũ�ƿ� �ֹ� CLASS : 2010-12-07 ������
//
//  �����ּ� : http://minishop.ecadmin.playautocorp.com/execute/Execute_Admin.php?action=NHNOrder&as=OrderMan&NHNaction=PaidOrder
//-------------------------------------------------------------------------
require_once 'nhnapi-simplecryptlib.php';
require_once 'HTTP/Request.php';
require_once "xml_parser.php";

class NaverCheckOutOrder {

	var $inst_main;

  //-------------------------------------------------
  //  ������
  //-------------------------------------------------
  function NaverCheckOutOrder($__inst_main){
		$this->inst_main = $__inst_main;
  // function - End
  }

  //------------------------------------------------------------------------
  // ���̹� API �⺻ ����
  //------------------------------------------------------------------------
  function init() {
    if($this->stype == "AMP") {
      //$this->test = true;
    }

    if($this->test) {
      //��� ����
      $accessLicense = "01000100000503b9ecabad8d09c6e718ceaefd1f34033dc7d97db450be663f4686105cb068";  // ��� ���Ϸ� �߼۵�  ���̼�ƮŰ �Է�(�׽�Ʈ)
      $this->key= "AQABAAAVG60l7a89ffqtgHdTg1NGzPlANqc5clH/OMXXo8kG/g==";  // ��и��Ϸ� �߼۵� ��ũ��Ű (�׽�Ʈ)
      $this->targetUrl = "http://sandbox.api.naver.com/Checkout/MallService3";
    }else{
      //��� ����
      $accessLicense = "0100010000e5be7c82a4d24f24e836ac9b1ac177eaa62bbd3ec75d51a1f3b3760fa9346805";  // ��� ���Ϸ� �߼۵�  ���̼�ƮŰ �Է�
      $this->key= "AQABAAD/KPnN8qc4ZRzkg4/jOlgdH8AoAar+K0fxhyDk45CIfA==";  // ��и��Ϸ� �߼۵� ��ũ��Ű
      $this->targetUrl = "http://api.naver.com/Checkout/MallService3";
    }

    // �⺻����
    $this->service = "MallService3";
    $detailLevel = "Full";
    $version = "3.0";
    $RequestID="playauto_".date('Ymdhis');


    //NHNAPISCL ��ü����
    $this->scl = new NHNAPISCL();
    //Ÿ�ӽ������� ���˿� �°� ����
    $this->timestamp = $timestamp = $this->scl->getTimestamp();

    //hmac-sha256�������
    $signature = $this->scl->generateSign($timestamp . $this->service . $this->operation, $this->key);

    //soap template�� ������ �� �⺻ ����
    $this->request_head="<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">
    <soapenv:Body>
    <ns2:".$this->operation."Request xmlns=\"http://base.checkout.platform.nhncorp.com/\" xsi:type=\"ns2:".$this->operation."RequestType\" xmlns:ns2=\"http://mall.checkout.platform.nhncorp.com/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
    <AccessCredentials>
    <AccessLicense>". $accessLicense . "</AccessLicense>
    <Timestamp>" . $timestamp . "</Timestamp>
    <Signature>" .$signature . "</Signature>
    </AccessCredentials>
    <RequestID>". $RequestID . "</RequestID>
    <DetailLevel xmlns:s1=\"http://base.checkout.platform.nhncorp.com/\">" . $detailLevel . "</DetailLevel>
    <Version xmlns:s2=\"http://base.checkout.platform.nhncorp.com/\">" . $version . "</Version>
    ";

  }

  //------------------------------------------------------------------------
  // ���̹��� API�� ����
  //------------------------------------------------------------------------
  function sendNHN($request_data) {
    $request_body=$this->request_head.$request_data;

    //http post������� ��û ����
    $rq = new HTTP_Request($this->targetUrl);
    $rq->addHeader("Content-Type", "text/xml;charset=UTF-8");
    $rq->addHeader("SOAPAction", $this->service . "#" . $this->operation);
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

    $re_nhn_data = $rq->getResponseBody();
    if(eregi("ERR-COMMON-000302",$re_nhn_data)) {
      $data_error[error] = "ERR-COMMON-000302";
      echo $this->WebViewData($data_error);
      exit;
    }

    return $re_nhn_data;
    // funciton - End
  }

  //------------------------------------------------------------------------
  // ���̹� API�� ���� XML TEXT�� ��ȯ
  //------------------------------------------------------------------------
  function XmlToText($data,$thiskey='__ALL__') {

    $xml_parse =& new xml_parse($thiskey,$this->action);
    $dataList = $xml_parse->parse($data);

    //���� �ֹ� ���� ó��
    $i=0;
    foreach($dataList as $key=>$val) {
      if($val['PRODUCTNAME'] != "") {
        foreach($val['PRODUCTNAME'] as $key2=>$val2) {
          $dataList3 = array();
          $dataList3 = $val;
          $plus_order_id = $key2+1;
          $dataList3['NHNORDERID'] = $val['ORDERID']; // ���̹� �ֹ� ��ȣ ���� ���� �� ����
          $dataList3['BUNDLECOUNT'] = sizeof($val['PRODUCTNAME']); // ���� �ֹ� ���� ���� ���� �� ����
          $dataList3['ORDERID'] = $val['ORDERID']."_".$plus_order_id;
          $dataList3['PRODUCTNAME'] = iconv("euc-kr","utf-8","[üũ�ƿ�]").$val['PRODUCTNAME'][$key2];
          $dataList3['PRODUCTID'] = $val['PRODUCTID'][$key2];
          $dataList3['QUANTITY'] = $val['QUANTITY'][$key2];
          $dataList3['UNITPRICE'] = $val['UNITPRICE'][$key2];
          $dataList3['RETURNREQUESTED'] = $val['RETURNREQUESTED'][$key2];
          $dataList3['ORDERPRODUCT'] = $val['ORDERPRODUCT'][$key2];
          $dataList3['PRODUCTOPTION'] = $val['PRODUCTOPTION'][$key2];
          $dataList2[$i] = $dataList3;
          $i++;
        }
      }
    }

    //��ȣȭ ��� �ʵ�
    $chdata = array(
      "ORDERERNAME" => true,
      "ORDERERID" => true,
      "ORDERERTEL" => true,
      "ORDEREREMAIL" => true,
      "RECIPIENT" => true,
      "SHIPPINGADDRESS1" => true,
      "SHIPPINGADDRESS2" => true,
      "RECIPIENTTEL1" => true,
	  "RECIPIENTTEL2" => true,
      );

    //��ȣȭ �ϱ�
    $decryptKey = $this->scl->generateKey($this->timestamp , $this->key);

    if(is_array($dataList2)) {
      foreach($dataList2 as $k=>$v) {
        foreach($chdata as $k2=>$v2) {
          if($v[$k2] != "") { // ������ ���� Ȯ��
            $dataList2[$k][$k2] = $this->scl->decrypt($decryptKey, $v[$k2]);
						// �ּҿ� " ���� : 2011-12-09 ������
						if($k2=='SHIPPINGADDRESS2') { $dataList2[$k][$k2] = str_replace('"','',$dataList2[$k][$k2]); }
          }
        }

        if($this->stype == "AMP") {
          unset($dataList[$k]);
		  foreach($dataList2[$k] as $k2=>$v2) {
            $dataList[$k][$k2] = $v2;
          }
        }elseif($this->stype == "rese") {
          unset($dataList[$k]);
		  foreach($dataList2[$k] as $k2=>$v2) {
            $dataList[$k][$k2] = base64_encode(iconv("utf-8","UHC//IGNORE",$v2));
          }
        }else{
          unset($dataList[$k]);
          foreach($dataList2[$k] as $k2=>$v2) {
            $dataList[$k][$k2] = iconv("utf-8","UHC//IGNORE",$v2);
          }
        }
      }
    }
    return $dataList;
  }

  //------------------------------------------------------------------------
  // ���̹� API�� ���� XML TEXT�� ��ȯ (���� �ֹ� ���� ó�� ����)
  //------------------------------------------------------------------------
  function XmlToText2($data,$thiskey='__ALL__') {

    $xml_parse =& new xml_parse($thiskey,$this->action);
    $dataList = $xml_parse->parse($data);
	$dataList2 = $dataList;

    //��ȣȭ ��� �ʵ�
    // ���̹����� ���� ���޽� ���Ȱ��� ������ ��ȣȭ �ؼ��� ���� �Ϻ� �����鸸 �ص�ó���ϰ� �ʵ� ������
    $chdata = array(
      "ORDERERNAME" => true,
      "ORDERERID" => true,
      "ORDERERTEL" => true,
      "ORDEREREMAIL" => true,
      "RECIPIENT" => true,
      "SHIPPINGADDRESS1" => true,
      "SHIPPINGADDRESS2" => true,
      "RECIPIENTTEL1" => true,
	  "RECIPIENTTEL2" => true,
      );

    //��ȣȭ ������ ����
    $decryptKey = $this->scl->generateKey($this->timestamp , $this->key);

	if(is_array($dataList2)) {
		foreach($dataList2 as $k=>$v) {
			foreach($chdata as $k2=>$v2) {
				if($v[$k2] != "") { // ������ ���� Ȯ��
					// ����Ű�� �Բ������Ͽ� ��ȣ �ص���
					$dataList2[$k][$k2] = $this->scl->decrypt($decryptKey, $v[$k2]);
				}
			}
			unset($dataList[$k]);
			foreach($dataList2[$k] as $k2=>$v2) {
				if(is_array($v2)) {
					foreach($dataList2[$k][$k2] as $k3=>$v3) {
						$dataList[$k][$k2][$k3] = iconv("utf-8","UHC//IGNORE",$v3);
					}
				}else{
					$dataList[$k][$k2] = iconv("utf-8","UHC//IGNORE",$v2);
				}
			}
		}
	}

    return $dataList;
  }

  //------------------------------------------------------------------------
  // �ֹ���ȣ�� �ֹ� ���� �˻�
  //------------------------------------------------------------------------
  function NHNPOrderSearch($OrderID) {
    
    if( eregi("-",$OrderID) ) {
      $post = array();
      $OrderID_array = explode(" ",$OrderID);
      $post['Cart_id'] = $OrderID_array[1];
      $data2 = $this->inst_main->inst_OrderSelect->OMP_order(&$post,&$this->CP_CODE);

      if($data2[0]['status'] == "19") {
        $data[0]['ORDERSTATUSCODE'] = "OD0008";
      }elseif($data2[0]['status'] == "22") {
        $data[0]['ORDERSTATUSCODE'] = "OD0010";
      }elseif($data2[0]['status'] != "") {
        $data[0]['ORDERSTATUSCODE'] = "dd_order";
      }else{
        $data[0]['ORDERSTATUSCODE'] = "";
      }
    }else{
      // �⺻����

      $this->action = "GetOrderInfo";
      $this->operation = $this->action;

      $this->init();

      //���۵����� ���� �� ����
      $request_data="
      <OrderID>$OrderID</OrderID>
      </ns2:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";
      $data = $this->sendNHN($request_data);

      // XML�� TEXT�� ��ȯ
      $data = $this->XmlToText($data);
    }

    // ������ ����
    echo $this->WebViewData($data);
    exit;
  }

  //------------------------------------------------------------------------
  // ������� Ȯ�� ó��
  //------------------------------------------------------------------------
  function NHNPlaceOrder($OrderID) {
    
    if( eregi("-",$OrderID) ) {
      $OrderID_array = explode(" ",$OrderID);
      $UpdateArray[0][Cart_id] = $OrderID;
      $UpdateArray[0][Status] = "19";
      $UpdateArray[0][Cart_id] = $OrderID_array[1];

      $this->inst_main->inst_OrderExecute->UpdateOrderCartInfo(&$UpdateArray);
      $data[msg] = "Success";

    }else{
      // �⺻����
      $this->action = "PlaceOrder";
      $this->operation = $this->action;

      $this->init();

      //���۵����� ���� �� ����
      $request_data="
      <OrderID> $OrderID </OrderID>
      </ns2:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";
      $data = $this->sendNHN($request_data);

      // XML�� TEXT�� ��ȯ
      $data = $this->XmlToText($data);
    }
    // ������ ����
    echo $this->WebViewData($data);
    exit;
  }

  //------------------------------------------------------------------------
  // ���º� �ֹ� ����
  //------------------------------------------------------------------------
  function NHNOrder($thiskey,$action,$dday='') {

    // -----------------------------------------------------------
    // ���̹� üũ�ƿ�
		// 2011�� 7�� 21�� ����� ������ �߰� ��¥
		if( $dday =='') $dday =10;

    /*
    if( $thiskey == "" ) {
      echo "ERROR[NHN0003]: ��ü�����ڵ� ����";
      exit;
    }

    if( $action == "" ) {
      echo "ERROR[NHN0004]: �ֹ� ���� ��� ����";
      exit;
    }
    */

    if($action == "uislamp") {
      $uisltest = true;
    }

    if( $this->CP_CODE == "street0" ) {
      $dday = 8;
    }

    $data_list = array();
    for($i=$dday; $i>=-1; $i--) {

    // ��ȸ�Ⱓ �ִ� 24�ð�(1��)�Դϴ�.
    // ���̹� �ð��� GMT(�ѱ��ð�+9)�ð� �Դϴ�
    if($this->test) {
      $d_date = mktime(0,0,0,01,03,2010);
      $p_date = mktime(0,0,0,01,04,2010);
      $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
      $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ
    }else{
      /*
      $s_timestamp = time() - 86400 * ($i+1);
      $e_timestamp = time() - 86400 * $i;
      $d_date = mktime(0,0,0,date('m',$s_timestamp),date('d',$s_timestamp),date('Y',$s_timestamp));
      $p_date = mktime(0,0,0,date('m',$e_timestamp),date('d',$e_timestamp),date('Y',$e_timestamp));
      $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
      $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ
      */

      $this->InquiryTimeFrom = date('Y-m-d',time() - 86400 * ($i+1))."T00:00:00+09:00";
      $this->InquiryTimeTo = date('Y-m-d',time() - 86400 * $i)."T00:00:00+09:00";
    }

      // �⺻����
      $this->action = $action;
      $this->operation = "Get".$this->action."List";
      $this->init();

      //���۵����� ����
      $request_data="
      <InquiryTimeFrom>" . $this->InquiryTimeFrom . "</InquiryTimeFrom>
      <InquiryTimeTo>" . $this->InquiryTimeTo . "</InquiryTimeTo>
      <InquiryExtraData></InquiryExtraData>
			<MallUID>".$this->NCkey."</MallUID>
      </ns2:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";

      // ����
      $data = $this->sendNHN($request_data);
sleep(0.5);
      // XML�� TEXT�� ��ȯ
      $data = $this->XmlToText($data,$this->NCkey);

			// 2011-06-21 ������ �߰� �κ�------------- ���� ------------------------
			$counts = sizeof($data);

			if($counts > 0 ) {
				// ���� ����߿� �κ� ���ó�� ���� �Ǵ�

				for($j=0; $j<$counts; $j++) {

					// �ֹ���ȣ�� ���ڰ� �ƴҶ� �о� 2011-08-04 ������
					// �̻��� �ֹ��� �����Ǵ°��� ����.
					if($this->stype != "rese") {
						$check_order = str_replace("_","",$data[$j][ORDERID]);

						if(!is_numeric($check_order)) {	unset($data[$j]); continue;	}
					}

					// ���� ��� �̸鼭 ����ڵ尡 OD0037 ��� ó�� �Ϸ� �϶�
					// DB�� ���ؼ� ���� �߼��� �Ǿ����� Ȯ�� �ؼ� �ƴϸ� OD0008 �̹߼� ���� �ٲ��ش�.
					if($data[$j]['BUNDLECOUNT'] > 1 && $data[$j]['ORDERSTATUSCODE'] =='OD0037' ) {

						$Goods_query = "select * from $this->DataBase.Order_Match where nhn_ocode  = '".$data[$j]['NHNORDERID']."' AND ( nhn_sender != '' OR pa_sender !='' OR  sendno != '') ";
						$this->sock->reset_rownum();
						$this->sock->fetch($Goods_query);
						$Goods_sql = $this->sock->rownum;

						// ���� ���α׷��� ���� �ʰ� ��������� �׳� ����ȭ �ǵ��� �Ѿ��.
						if($Goods_sql == '0') continue;

						$sql = "select * from $this->DataBase.Order_Match where pa_ocode = '".$data[$j]['ORDERID']."' ";
						$re = $this->sock->fetch($sql);

						if( $re[0][state] != "�߼ۿϷ�" ) {
							$data[$j][ORDERSTATUSCODE] = "OD0008";
							$data[$j][ORDERSTATUS] = "�̹߼�";
						}

					}

				// loop - End
				}
			}



			// 2011-06-21 ������ �߰� �κ�------------- �� ------------------------

			$data_list = array_merge($data_list,$data);
    }

    // �ֹ� ��� ��Ī���� �����ϱ�
    if($this->action == "PlacedOrder") {

      if(sizeof($data_list)>0) {
        foreach($data_list as $k=>$v) {
          if($v['MALLMANAGECODE'] != '') {
            // Ư���ֹ� ��ȸ
            $msg = $this->MatchSpecialOrder($v['MALLMANAGECODE']);
            $data_list[$k]['SHIPPINGMESSAGE'] = $data_list[$k]['SHIPPINGMESSAGE'].' '.$msg;
          }
        // loop - End
        }
      }

	  // �ű��ֹ��϶�
      $this->OrderMatchInsert($data_list);
    }else{
      // ����ȭ �϶�
    }
            
    // -----------------------------------------------------------
    // �̴ϼ� ��ü�ֹ�

    if($action == "PlacedOrder") { // ����Ȯ�� ó���� ��� �ֹ� 
      $state_list = "predeliv";
    }elseif($action == "PaidOrder") { //�����ȣ �Է��� �ű��ֹ� 
      $state_list = "accountok";
    }elseif($action == "ShippedOrder") { //�����,����Ȯ��
      $state_list = "deliv,orderget";
    }elseif($action == "CompletedOrder") { //����Ϸ�
      $state_list = "orderok";
    }elseif($action == "ReturnedOrder") { //��ǰ��û , ��ǰ����
      $state_list = "askreturn,okreturn";
    }elseif($action == "ExchangedOrder") { //��ȯ��û , ��ȯ����
      $state_list = "askchange,okchangecross";
    }elseif($action == "CancelOrder") { //��ҿ�û
      $state_list = "askRcancle";
    }elseif($action == "CanceledOrder") { //��Ҹ���
      $state_list = "askRcancleok";
    }

    // �̴ϼ� �ֹ��� ���� ��ü
    if($this->stype == "AMP") {
      $incodig_type = "utf-8";
    }else{
      $incodig_type = "euc-kr";
    }

    $data_list = @array_merge($data_list,$this->getAutoMall($state_list,$incodig_type));
		
    // ���� �Ѹ���
    echo $this->WebViewData($data_list);
    exit;
  }

  //------------------------------------------------------------------------
  // ���̽� ī���� ���ۿ� ���º� �ֹ� ���� 2011-08-16 ������
  //------------------------------------------------------------------------
  function NHNOrder_ACT($action,$Shop_key,$dday='14') {

	$Shop_key = $Shop_key == "" ? "ALL":$Shop_key;
    $data_list = array();
	$dday = 1;
	for($i=0; $i<$dday; $i++) {

		// ��ȸ�Ⱓ �ִ� 24�ð�(1��)�Դϴ�.
		// ���̹� �ð��� GMT(�ѱ��ð�+9)�ð� �Դϴ�
		// $this->InquiryTimeFrom = date('Y-m-d',time() - 86400 * ($i+1))."T".date('H:i:s')."+09:00";
		// $this->InquiryTimeTo = date('Y-m-d',time() - 86400 * $i)."T".date('H:i:s')."+09:00";
		$this->InquiryTimeFrom = date('Y-m-d',time() - 7200)."T".date('H:i:s',time() - 7200)."+09:00";
		$this->InquiryTimeTo = date('Y-m-d',time() - 3000)."T".date('H:i:s',time() - 3000)."+09:00";

		// �⺻����
		$this->action = $action;
		$this->operation = "Get".$this->action."List";
		$this->init();

		//���۵����� ����
		$request_data="
		<InquiryTimeFrom>" . $this->InquiryTimeFrom . "</InquiryTimeFrom>
		<InquiryTimeTo>" . $this->InquiryTimeTo . "</InquiryTimeTo>
		<InquiryExtraData></InquiryExtraData>
		</ns2:".$this->operation."Request>
		</soapenv:Body>
		</soapenv:Envelope>";

		// ����
		$data = $this->sendNHN($request_data);

		// XML�� TEXT�� ��ȯ
		$data = $this->XmlToText2($data);
		$counts = sizeof($data);

		$re_data = array();
		if($counts > 0 ) {
			// ���� ����߿� �κ� ���ó�� ���� �Ǵ�
			$kk = 0;
			for($j=0; $j<$counts; $j++) {
				// �ֹ���ȣ�� ���ڰ� �ƴҶ� �о� 2011-08-04 ������
				// �̻��� �ֹ��� �����Ǵ°��� ����.
				if($this->stype != "rese") {
					$check_order = str_replace("_","",$data[$j][ORDERID]);
					if(!is_numeric($check_order)) {	unset($data[$j]); continue;	}
				}

				if(eregi($data[$j][MALLUID],$Shop_key) || $Shop_key == 'ALL') {
					$re_data[$kk][MALLUID]				= $data[$j][MALLUID];			// ��üŰ
					$re_data[$kk][ORDERID]				= $data[$j][ORDERID];			// �ֹ���ȣ
					$re_data[$kk][MALLMANAGECODE]		= $data[$j][MALLMANAGECODE];	// ������ ��ǰ�ڵ�
					$re_data[$kk][ORDERDATETIME]		= $this->GMTToKTime($data[$j][ORDERDATETIME]);		// �ֹ���
					$re_data[$kk][PAYMENTDATE]			= $this->GMTToKTime($data[$j][PAYMENTDATE]);		// ������
					$re_data[$kk][PRODUCTID]			= $data[$j][PRODUCTID];			// ��ǰ�ڵ�
					$re_data[$kk][PRODUCTNAME]			= $data[$j][PRODUCTNAME];		// ��ǰ��
					$re_data[$kk][QUANTITY]				= $data[$j][QUANTITY];			// ����
					$re_data[$kk][UNITPRICE]			= $data[$j][UNITPRICE];			// ��ǰ�ܰ�
					$re_data[$kk][TOTALORDERAMOUNT]		= $data[$j][TOTALORDERAMOUNT];	// ����������
					$re_data[$kk][ORDERSTATUS]			= $data[$j][ORDERSTATUS];		// �ֹ�����
					$kk++;
				}

			// loop - End
			}
		}

		$data_list = array_merge($data_list,$re_data);
    }

    // �ֹ� ��� ��Ī���� �����ϱ�
    if($this->action == "PlacedOrder") {

    }else{
    // ����ȭ �϶�
    }

    echo $this->WebViewData($data_list);
    exit;
  }

  /*
  //------------------------------------------------------------------------
  // ��������
  //------------------------------------------------------------------------
  function NHNShipOrder($OrderID,$ShippingCompany,$TrackingNumber) {

    // �⺻����
    $this->action = "ShipOrder";
    $this->operation = $this->action;
    $this->init();

    $ShippingCompleteDate = date("Y-m-d\TH:i:s",strtotime("-9hour"));;

    //���۵����� ���� �� ����
    $request_data="
    <OrderID> $OrderID </OrderID>
    <ShippingCompleteDate> $ShippingCompleteDate </ShippingCompleteDate>
    <ShippingCompany> $ShippingCompany </ShippingCompany>
    <TrackingNumber> $TrackingNumber </TrackingNumber>
    </ns2:".$this->operation."Request>
    </soapenv:Body>
    </soapenv:Envelope>";

    $data = $this->sendNHN($request_data);

    // XML�� TEXT�� ��ȯ
    $data = $this->XmlToText($data);

    // ������ ����
    echo $this->WebViewData($data);
    exit;
  }
*/

  //------------------------------------------------------------------------
  // ��������
  // $OrderID : �ֹ� ��ȣ ( * �� �Ʒ��� ��Ģ�� ������� )
  //   - type�� save�� "_"���İ� �� �÷��̿��� �ֹ� ��ȣ
  //   - type�� exec�� "_"���İ� ���ŵ� ���̹� �ֹ� ��ȣ
  //------------------------------------------------------------------------
  function NHNShipOrder($para) {
    $data = array(); // ��� �޼��� �ʱ�ȭ

    if( eregi("-",$para['OrdID']) ) {
      // ----------------------------------------------
      // ����� ����ó��
      
      // ����� ��ۻ� ��ȸ ó��
      $data2 = $this->inst_main->inst_OrderSelect->GetDelivCostInfo(&$this->CP_CODE);

      $sender_code = "";
      for($i=0; $i<sizeof($data2[0][field3]); $i++) {
        if( eregi($para['pa_sender'],$data2[0][field3][$i]) ) {
          $sender_code = $data2[0][field1][$i];
        }
      // loop - End
      }

      $Order_id = explode(" ",$para['OrdID']);
      $InsertOrder_Array = $Order_Array = array();
      $Order_Array[Order_id][0] = $Order_id[0];
      $Order_Array[Cart_id][0] = $Order_id[1];
      $Order_Array[Delivery_code][0] = $sender_code;
      $Order_Array[Delivery_com][0] = $para['sendno'];
      $Order_Array[Regi_date][0] = "now()";
      $Order_Array[Delivery_status][0] = "����";

      $InsertOrder_Array[Cart_id][] = $Order_id[1];
      $InsertOrder_Array[Delivery_com][] = $sender_code;
      $InsertOrder_Array[Delivery_code][] = $para['sendno'];
      $InsertOrder_Array[Regi_date][] = "now()";

      $result = $this->inst_main->inst_OrderExecute->InsertCart_id_Excel(&$Order_Array,&$InsertOrder_Array,array(),&$this->inst_main);

      if($result == "error") {
        $data['msg'] = "Save-False";
      }else{
        $data['msg'] = "Save-Ok";
      }

      echo $this->WebViewData($data);
      exit;

    }else{
      // ----------------------------------------------
      // üũ�ƿ� ����ó��
      
      $up_data = array(); // SET ���� �ʱ�ȭ
      if($para['type'] == "save") {

        $where = "pa_ocode = '".$para['OrdID']."' and cp_code='".$this->CP_CODE."'";
        $up_data['paNumber'] = $para['psNumber']; // �ַ�� ���� �ֹ� �ѹ�
        $up_data['pa_sender'] = $para['pa_sender'];
        $up_data['nhn_sender'] = $para['nhn_sender'];
        $up_data['sendno'] = $para['sendno'];
        $up_data['sendInfoKey'] = $para['sendInfoKey'];
        $result = $this->sock->Array_update("$this->DataBase.Order_Match",$up_data,$where,"Success" ,false);

        if($result == "Success") {
          $data['msg'] = "Save-Ok";
        }else{
          $data['msg'] = "Save-False";
        }

        // ��� ����
        echo $this->WebViewData($data);
        exit;

      }elseif($para['type'] == "exec") {
        // ��������
        // ��Ż ���� ���
        $sql = "select * from $this->DataBase.Order_Match where sendInfoKey = '".$para['sendInfoKey']."' ";
        $re = $this->sock->fetch($sql);

        //�κ� ��ۿ��� Ȯ��
        $partDeliv = array();
        if(is_array($re)) {

          foreach($re as $key=>$val) {
            $partDeliv[$val['nhn_ocode']]++;
          }

          foreach($re as $key=>$val) {
            // �κ� ��� �ֹ� �ù�� ����
            if($partDeliv[$val['nhn_ocode']] != $val['bundle_count']) {
              $val['nhn_sender'] = "z_delegation";
              $val['sendno'] = "";

              //----------------------------------------------------------------------------
              // �ֹ���ȣ�� ��ü�ڵ� ��Ī���� ��ũ��� ����
              $this->OrderMatchShortInsert($val['nhn_ocode']);
              $real_data = $this->NHNShipOrderRe($val['nhn_ocode'],$val['nhn_sender'],$val['sendno']);
              $real_data[0]['RESPONSETYPE'] = "SUCCESS";
            }else{
              //��Ÿ �ù�(z_etc), ������(z_quick), �����(z_direct), �湮 ����(z_visit), ��ü�� ���(z_delegation)�� ��� ������ȣ(TrackingNumber)�� ����־�� �մϴ�.
              if($val['nhn_sender'] == "z_etc" || $val['nhn_sender'] == "z_quick" || $val['nhn_sender'] == "z_direct" || $val['nhn_sender'] == "z_visit" || $val['nhn_sender'] == "z_delegation"){
                $val['sendno']="";
              }
              $real_data = $this->NHNShipOrderRe($val['nhn_ocode'],$val['nhn_sender'],$val['sendno']);
            }


            // �������� ���� �̸� �����
            $re_data_y = false;
            if(eregi("SUCCESS",$real_data[0]['RESPONSETYPE'])) {
              $re_data_y = true;
            }elseif($real_data[0]['CODE'] == "ERR-NC-101002" && eregi("�ֹ� ����: ���ó���Ϸ�", iconv("utf-8","UHC",$real_data[0]['MESSAGE'])) ) {
              $re_data_y = true;
            }

            if($re_data_y) {
              $all_ret[ocode] = $val['pa_ocode']; // ���̹��� Ư�� ����
              $all_ret[result] = true;

              $where = "number='".$val['number']."'";
              $up_data['state'] = "�߼ۿϷ�";

              $result = $this->sock->Array_update("$this->DataBase.Order_Match",$up_data,$where,"Success" ,false);

            } else {
              if($real_data[0]['MESSAGE'] == "") {
                $all_ret[msg] = "[����] ����Ȯ�� �Ұ�";
              }else{
                $all_ret[msg] = "(NHN) ".$real_data[0]['MESSAGE'];
              }
              $all_ret[result] = false;
            }
            $dataView[$val['panumber']] = $all_ret;
  sleep(0.5);
          }
        }

        // ��� ����
        echo $this->WebViewData($dataView);
        exit;

      }else{

        $data['type'] = $type;
        $data['msg'] = "type-error";
        echo $this->WebViewData($data);
        exit;

      }
    }

  }


  //------------------------------------------------------------------------
  // ��������
  // $OrderID : �ֹ� ��ȣ ( * �� �Ʒ��� ��Ģ�� ������� )
  //   - type�� save�� "_"�� �� �÷��̿��� �ֹ� ��ȣ
  //   - type�� exec�� "_"�� ���ŵ� ���̹� �ֹ� ��ȣ
  //------------------------------------------------------------------------
  function NHNShipOrderRe($OrderID,$ShippingCompany,$TrackingNumber) {
    // �⺻����
    $this->action = "ShipOrder";
    $this->operation = $this->action;
    $this->init();

    $ShippingCompleteDate = date("Y-m-d\TH:i:s",strtotime("-9hour"));;

    //���۵����� ���� �� ����
    $request_data="
    <OrderID> $OrderID </OrderID>
    <ShippingCompleteDate> $ShippingCompleteDate </ShippingCompleteDate>
    <ShippingCompany> $ShippingCompany </ShippingCompany>
    <TrackingNumber> $TrackingNumber </TrackingNumber>
    </ns2:".$this->operation."Request>
    </soapenv:Body>
    </soapenv:Envelope>";

    $data = $this->sendNHN($request_data);

    // XML�� TEXT�� ��ȯ
    $data = $this->XmlToText($data);

    return $data;
  }

  //------------------------------------------------------------------------
  // �߼� ���
  // ��� ���� ���� ���� �ֹ� �̹߼����� ó�� �ϱ� ����
  //------------------------------------------------------------------------
  function NHNCancelShipping($OrderID) {

    // �⺻����
    $this->action = "CancelShipping";
    $this->operation = $this->action;
    $this->init();

    $ShippingCompleteDate = date("Y-m-d\TH:i:s",strtotime("-9hour"));;

    //���۵����� ���� �� ����
    $request_data="
    <OrderID> $OrderID </OrderID>
    </ns2:".$this->operation."Request>
    </soapenv:Body>
    </soapenv:Envelope>";

    $data = $this->sendNHN($request_data);

    // XML�� TEXT�� ��ȯ
    $data = $this->XmlToText($data);

    // ������ ����
    echo $this->WebViewData($data);
    exit;
  }



  //------------------------------------------------------------------------
  // ���� ���� ������
  //------------------------------------------------------------------------
  function WebViewData($data) {


    if($this->stype == "AMP") {
      $ViewDataEncode = base64_encode(json_encode($data));
    }elseif($this->stype == "rese") {
      $ViewDataEncode = json_encode($data);
    }else{
      $ViewDataEncode = base64_encode(serialize($data));
    }

    if($this->stype == "rese") {
      $ViewData = $ViewDataEncode;
    }else{
      $ViewData = '<?xml version="1.0" encoding="utf-8"?>
      <PLAYAUTO_RESPONSE>
        <RESULT>OK</RESULT>
        <MSG></MSG>
        <DATA><![CDATA['.$ViewDataEncode.']]></DATA>
      </PLAYAUTO_RESPONSE>
      ';
    }


    return $ViewData;
  }

  //------------------------------------------------------------------------
  // �ֹ� ��Ī���� ����
  //------------------------------------------------------------------------
  function OrderMatchInsert($data) {
    // ������ ������ ����
    $in_data = array();
    $result_re = true;
    foreach($data as $key=>$val) {
      if($this->stype == "rese") {
        $in_data['wdate'] = date('Y-m-d H:i:s');
        $in_data['state'] = "�߼���";
        $in_data['cp_code'] = $this->CP_CODE;
        $in_data['nhn_ocode'] = base64_decode($val['NHNORDERID']);
        $in_data['pa_ocode'] = base64_decode($val['ORDERID']);
        $in_data['goods_code'] = base64_decode($val['PRODUCTID']);
        $in_data['bundle_count'] = base64_decode($val['BUNDLECOUNT']);
      }else{
        $in_data['wdate'] = date('Y-m-d H:i:s');
        $in_data['state'] = "�߼���";
        $in_data['cp_code'] = $this->CP_CODE;
        $in_data['nhn_ocode'] = $val['NHNORDERID'];
        $in_data['pa_ocode'] = $val['ORDERID'];
        $in_data['goods_code'] = $val['PRODUCTID'];
        $in_data['bundle_count'] = $val['BUNDLECOUNT'];
      }
      $result = $this->sock->Array_insert("$this->DataBase.Order_Match",$in_data,"Success" ,false);

      if($result != "Success") {
        for($i=0; $i<10; $i++) {
          $result = $this->sock->Array_insert("$this->DataBase.Order_Match",$in_data,"Success" ,false);
          if($result == "Success") {
            break;
          }
        }
      }
    }

    return $result_re;
  }


  //------------------------------------------------------------------------
  // �ֹ� ��� ��Ī���� ����
  //------------------------------------------------------------------------
  function OrderMatchShortInsert($ORDER_ID) {

    // �������� - �⺻ �̴ϼ��� �ٸ� ��񼭹� �����̶� ���⼭ �ٷ� ���� ó����
    $in_data['nhn_ocode'] = $ORDER_ID;
    $in_data['cp_code'] = $this->CP_CODE;

    $re = $this->sock->Array_insert("ECHost.orderMatch",$in_data,"Success" ,false);

    if($re != "Success") {
      for($i=0; $i<10; $i++) {
        $re = $this->sock->Array_insert("ECHost.orderMatch",$in_data,"Success" ,false);
        if($re == "Success") {
          break;
        }
      }
    }

    /*
    // �������� - �⺻ �̴ϼ��� �ٸ� ��񼭹� �����̶� ���⼭ �ٷ� ���� ó����
    $rank_sock = @mysql_connect('rankdb','playauto','ahffkdy;;');
    if(!$rank_sock) {
     echo("���� ���ῡ �����Ͽ����ϴ�.");
     exit;
    }

    $status=@mysql_select_db("nhn",$rank_sock);
    if(!$status) {
     echo("������ ���̽� ���ῡ �����Ͽ����ϴ�.");
     exit;
    }

    $sql = "INSERT INTO nhn.orderMatch (nhn_ocode, cp_code) VALUES ('".$ORDER_ID."', '".$this->CP_CODE."');";

    if(!mysql_query($sql,$rank_sock)) {
      for($i=0; $i<10; $i++) {
        if(mysql_query($sql,$rank_sock)) {
          break;
        }
      }
    }
    */


  }

  //-----------------------------------------------------------------------------
  //  Ư�� �ֹ� ����Ȱ� ��������
  //-----------------------------------------------------------------------------
  function MatchSpecialOrder($MALLMANAGECODE) {

	$sql = "select * from $this->DataBase.Special_order where Order_id = '".$MALLMANAGECODE."' ";
	$re = $this->sock->fetch($sql);

	$msg = "";
	if(sizeof($re)>0 && $re[0][text1] != '') {
		$data = unserialize($re[0][text1]);
		if($data[type] == 'flower') {
			$msg = "�����޼���:".$data[ordertype]."(".$data[add_msg].")";
		}
	}
	return $msg;

  // funciton - End
  }

  //-----------------------------------------------------------------------------
  //  ��¥ ���ĺ�ȯ
  //-----------------------------------------------------------------------------
  function GMTToKTime($dateData) {
    $dateData = str_replace(".00Z","",$dateData);
    $array_time = explode("T",$dateData);
    $array_time1 = explode("-",$array_time[0]);
    $array_time2 = explode(":",$array_time[1]);

    $array_time3 = mktime($array_time2[0],$array_time2[1],$array_time2[2],$array_time1[1],$array_time1[2],$array_time1[0]);
    $ktime = date('Y-m-d H:i:s',$array_time3 + 32400);
    return $ktime;
  }

  //-----------------------------------------------------------------------------
  //  ��¥ ���ĺ�ȯ
  //-----------------------------------------------------------------------------
  function getAutoMall($state,$incodig_type) {
    $state_array = explode(",",$state);
    $mini_data_list = array();
    foreach($state_array as $key=>$val) {
      $_POST = array();
      $_POST['as'] = "OrderMan";
      $_POST['Cart_lists'] ="OMP";            // OMP�� ������ ����غ� ���� ��ü�ֹ��� �޾ƿ�
      $_POST['Fields_lists'] = "Order_id/Cart_id/Delivery_com/Delivery_code/Order_date/Status/Order_name/Order_mobile/Order_email/Receiver_name/Receiver_zipcode/Receiver_desty/Receiver_tell/Receiver_mobile/Goods_name/Goods_no/Selected_opt/Selected_addition/Amount/Delivery_cost/Account_method/Money/Delivery_date";
      $_POST['omp_state'] = "Y";
      $_POST['mini_state'] = $val;
      $mini_data = $this->inst_main->inst_Order->ExcelDownLoadOMP(&$this->inst_main,$incodig_type);
      if($mini_data != "") {
        $mini_data_list = @array_merge($mini_data_list,$mini_data);  
      }
    }
   
    return $mini_data_list;
  }

}  // class - End




?>