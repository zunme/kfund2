<?
//-------------------------------------------------------------------------
//
//  ���̹� üũ�ƿ� �ֹ� CLASS : 2010-12-07 ������
//
//  �����ּ� : http://minishop.ecadmin.playautocorp.com/execute/Execute_Admin.php?action=NHNOrder&as=OrderMan&NHNaction=PaidOrder
//-------------------------------------------------------------------------
require_once 'nhnapi-simplecryptlib.php';
require_once 'HTTP/Request.php';
require_once "xml_parser_uisl.php";

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

    //$this->test = true;
    if($this->test) {
      //��� ����
      $accessLicense = "01000100000503b9ecabad8d09c6e718ceaefd1f34033dc7d97db450be663f4686105cb068";  // ��� ���Ϸ� �߼۵�  ���̼�ƮŰ �Է�(�׽�Ʈ)
      $this->key= "AQABAAAVG60l7a89ffqtgHdTg1NGzPlANqc5clH/OMXXo8kG/g==";  // ��и��Ϸ� �߼۵� ��ũ��Ű (�׽�Ʈ)
      $this->targetUrl = "http://sandbox.api.naver.com/Checkout/MallService4";
    }else{
      //��� ����
      $accessLicense = "0100010000e5be7c82a4d24f24e836ac9b1ac177eaa62bbd3ec75d51a1f3b3760fa9346805";  // ��� ���Ϸ� �߼۵�  ���̼�ƮŰ �Է�
      $this->key= "AQABAAD/KPnN8qc4ZRzkg4/jOlgdH8AoAar+K0fxhyDk45CIfA==";  // ��и��Ϸ� �߼۵� ��ũ��Ű
      $this->targetUrl = "http://api.naver.com/Checkout/MallService4";
    }

    // �⺻����
    $this->service = "MallService4";
    $detailLevel = "Full";
    $version = "4.0";
    $RequestID="playauto_".date('Ymdhis');


    //NHNAPISCL ��ü����
    $this->scl = new NHNAPISCL();
    //Ÿ�ӽ������� ���˿� �°� ����
    $this->timestamp = $timestamp = $this->scl->getTimestamp();

    //hmac-sha256�������
    $signature = $this->scl->generateSign($timestamp . $this->service . $this->operation, $this->key);

    //soap template�� ������ �� �⺻ ����
    $this->request_head="<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:mall=\"http://mall.checkout.platform.nhncorp.com/\" xmlns:base=\"http://base.checkout.platform.nhncorp.com/\">
    <soapenv:Header/>
    <soapenv:Body>
    <mall:".$this->operation."Request>
    <base:AccessCredentials>
    <base:AccessLicense>". $accessLicense . "</base:AccessLicense>
    <base:Timestamp>" . $timestamp . "</base:Timestamp>
    <base:Signature>" .$signature . "</base:Signature>
    </base:AccessCredentials>
    <base:RequestID>". $RequestID . "</base:RequestID>
    <base:DetailLevel>" . $detailLevel . "</base:DetailLevel>
    <base:Version>" . $version . "</base:Version>
    ";
  }

  //------------------------------------------------------------------------
  // ���̹��� API�� ����
  //------------------------------------------------------------------------
  function sendNHN($request_data,$debug=false) {
    $request_body=$this->request_head.$request_data;
    
    if($debug) {
      echo $request_body;
      exit;
    }
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
  function XmlToText($data,$thiskey='__ALL__',$search_array = array()) {

    $xml_parse =& new xml_parse_uisl($thiskey,$this->action);
    $dataList = $xml_parse->parse($data);
    $dataList2 = $dataList[$this->action];

    if(sizeof($search_array) > 0 && $search_array != "" ) {
      foreach($search_array as $key=>$val) {
        if($dataList[$key] != $val) {
          unset($dataList[$key]);
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
      "ORDERERTEL1" => true,
      "MALLMEMBERID" => true,
      "BASEADDRESS" => true,
      "DETAILEDADDRESS" => true,
      "NAME" => true,
      "TEL1" => true,
      );

    //��ȣȭ �ϱ�
    $decryptKey = $this->scl->generateKey($this->timestamp , $this->key);

    if(is_array($dataList2)) {
      foreach($dataList2 as $k=>$v) {
        
        // $search_array ���� ���� ��� �ش� ������ �����͸� ���� �ǵ��� ������ ���� ó��
        if(sizeof($search_array) > 0 && $search_array != "") {
          $loop_ch = false;
          foreach($search_array as $sa_k=>$sa_v) {
            if($v[$sa_k] != $sa_v) { // Ư����� ������ �ƴϸ� ���� ó��
              unset($dataList2[$k]);
              $loop_ch = true;
            }
          }

          // ������ ������ �Ʒ� ó���� ���ǹ� �ϹǷ� ���� �迭������ �ѱ�
          if($loop_ch) {
            continue;
          }
        }

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
            $dataList2[$k][$k2] = iconv("utf-8","UHC//IGNORE",$v2);
          }
        }
      }
    }
    
    $dataList[$this->action] = $dataList2;
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
  // ��ǰ�ֹ���ȣ�� �ֹ� ���� �˻�
  // data_product_id_list : ��ǰ�ֹ���ȣ �迭
  //------------------------------------------------------------------------
  function NHNPOrderSearch($data_product_id_list,$viewType = false,$action2 = array()) {

    $data_product_id_list = explode(",",$data_product_id_list);
    $this->action = strtoupper("ProductOrderInfoList");
    $this->operation = "GetProductOrderInfoList";

    $data_list = array();
    $data_product_id_list_array = array_chunk($data_product_id_list,1000);
    foreach($data_product_id_list_array as $key=>$val) {
      
      $this->init();
      $request_data = "";
      foreach($val as $key2=>$val2) {
        $request_data.="
          <mall:ProductOrderIDList>$val2</mall:ProductOrderIDList>
        ";
      }

      $request_data.="
      </mall:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";
           
      $data = $this->sendNHN($request_data); // ����
      $data = $this->XmlToText($data,$this->NCkey,$action2); // XML�� �迭�� ��ȯ
      
      $data_list = @array_merge($data_list,$data[$this->action]);
      sleep(0.5);
    }

     // ������ ����
    if($viewType) {
      return $data_list;
    }else{
      echo $this->WebViewData($data_list);
    }
    exit;
  }

  //------------------------------------------------------------------------
  // ������� Ȯ�� ó��
  //------------------------------------------------------------------------
  function NHNPlaceOrder($OrderID) {

    // �⺻����
    $this->action = "PlaceProductOrder";
    $this->operation = $this->action;

    $this->init();

    //���۵����� ���� �� ����
    $request_data="
    <mall:ProductOrderID>$OrderID</mall:ProductOrderID>
    </mall:".$this->operation."Request>
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
  // ���ֹ���ȣ(API 3.0)�� ���� (API 4.0)�ֹ� ���� ����
  //------------------------------------------------------------------------
  function NHNPlaceOrder2($OrderID) {

    // �⺻����
    $this->action = strtoupper("MigratedProductOrderList");
    $this->operation = "GetMigratedProductOrderList";

    $this->init();

    //���۵����� ���� �� ����
    $request_data="
    <mall:OldOrderID>100000001</mall:OldOrderID>
    </mall:".$this->operation."Request>
    </soapenv:Body>
    </soapenv:Envelope>";
    $data = $this->sendNHN($request_data);

    // XML�� TEXT�� ��ȯ
    $data = $this->XmlToText($data);

    return $data;
  }

  //------------------------------------------------------------------------
  // ���º� �ֹ� ����
  //------------------------------------------------------------------------
  function NHNOrder($thiskey,$action,$dday='') {
    
    set_time_limit(0);
    // --------------------------------
    // �Ⱓ����
		if( $dday =='') $dday =-1;

    if( $this->CP_CODE == "street0" ) {
      $dday = 8;
    }
    
    // --------------------------------
    // ������ ����ڹ����� ���� üũ�ƿ�API3.0������ ���� �ڵ尪 �����ؾ��� 
    // �׷��� üũ�ƿ�API3.0�����ڵ�� üũ�ƿ�API4.0���� �ڵ带 ��Ī����
    $action2 = array(); // Ư�� ���ǿ� �ش��ϴ� �����͸� ����� ���
    if($action == "PlacedOrder") {
      $action = "PAYED"; // �������� �Է��� 
      $action2[strtoupper('PlaceOrderStatus')] = "OK"; // ����Ȯ�ε� �ֹ�
    }elseif($action == "PaidOrder") {
      $action = "PAYED"; // �������� �Է��� 
      $action2[strtoupper('PlaceOrderStatus')] = "NOT_YET"; // ����Ȯ�� �ȵ� �ֹ�
    }elseif($action == "ShippedOrder" || $action == "CompletedOrder") {
      $action = "DISPATCHED"; //����� ����Ȯ�� ������
    }elseif($action == "ReturnOrder") {
      $action = "RETURN_REQUESTED"; // ��ǰ��û
    }elseif($action == "ExchangOrder") {
      $action = "EXCHANGE_REQUESTED"; // ��ȯ��û
    }elseif($action == "CancelOrder") {
      $action = "CANCEL_REQUESTED"; // ��ҿ�û
    }elseif($action == "CanceledOrder") {
      $action = "CANCELED"; // ��Ҹ���
    }elseif($action == "ReturnedOrder") {
      $action = "RETURNED"; // ��ǰ����
    }elseif($action == "ExchangedOrder") {
      $action = "EXCHANGED"; // ��ȯ����
    }

    

    $data_list = array();
    $data_product_id_list = array();
    for($i=$dday; $i>=-1; $i--) {

      // ��ȸ�Ⱓ �ִ� 24�ð�(1��)�Դϴ�.
      // ���̹� �ð��� GMT(�ѱ��ð�+9)�ð� �Դϴ�
      $this->test = true;
      if($this->test) {
        $d_date = mktime(0,0,0,02,14,2012);
        $p_date = mktime(0,0,0,02,15,2012);
        $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
        $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ
      }else{
        $this->InquiryTimeFrom = date('Y-m-d',time() - 86400 * ($i+1))."T00:00:00+09:00";
        $this->InquiryTimeTo = date('Y-m-d',time() - 86400 * $i)."T00:00:00+09:00";
      }
      $this->test = false;
      // --------------------------------
      // �Ⱓ�ȿ� �ش��ϴ� �ֹ� ��ȣ ����
      $this->operation = "GetChangedProductOrderList";
      $this->init();

      //���۵����� ����
      $request_data="
      <base:InquiryTimeFrom>" . $this->InquiryTimeFrom . "</base:InquiryTimeFrom>
      <base:InquiryTimeTo>" . $this->InquiryTimeTo . "</base:InquiryTimeTo>
      <base:InquiryExtraData></base:InquiryExtraData>
      <mall:LastChangedStatusCode>$action</mall:LastChangedStatusCode>
     
      
      </mall:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";

      $data = $this->sendNHN($request_data); // ����
      $data = $this->XmlToText($data,$this->NCkey); // XML�� �迭�� ��ȯ

      sleep(0.5);
      $product_id_nm = 'PRODUCTORDERID';

      $data_product_id_list = @array_merge($data_product_id_list,$data[$product_id_nm]);  
      // üũ�ƿ� ��ü ��ȸ����� �ִ� 1000���� �����ϸ� �� �̻��� 1000�����ĺ��� �Ʒ��� ����ó���ؾ���
      if($data['HASMOREDATA'][0] == "true" ) {
        for($ii=0; $ii<20; $ii++) {
          $this->init();
          //���۵����� ����
          $request_data="
          <base:InquiryTimeFrom>".$data['MOREDATATIMEFROM'][0]."</base:InquiryTimeFrom>
          <base:InquiryTimeTo>" . $this->InquiryTimeTo . "</base:InquiryTimeTo>
          <base:InquiryExtraData>".$data['INQUIRYEXTRADATA'][0]."</base:InquiryExtraData>
          </mall:".$this->operation."Request>
          </soapenv:Body>
          </soapenv:Envelope>";
          $data = $this->sendNHN($request_data); // ����
          $data = $this->XmlToText($data,$this->NCkey); // XML�� �迭�� ��ȯ

          sleep(0.5);
          $data_product_id_list = array_merge($data_product_id_list,$data[$product_id_nm]);

          if($data['HASMOREDATA'][0] != "true" ) {
            break;
          }
        }
      }
    }

    /*
    if($action != "ShippedOrder" && $action != "CompletedOrder") {
      // --------------------------------
      // ������ �ֹ� ��ȣ�� �ֹ������� ���� �ϱ�
      if(sizeof($data_product_id_list) > 0 ) {
        $data_product_id_list = implode(",",array_unique($data_product_id_list));
        $data_list = $this->NHNPOrderSearch($data_product_id_list,true,$action2);
        //sleep(0.5);
      }
    }
    */

    if(sizeof($data_product_id_list) > 0 ) {
      $data_product_id_list_old = array();
      $data_product_id_list_new = array();
      // ���ֺ� ��ȣ�� ���ֹ� ��ȣ �����ϱ�
      foreach($data_product_id_list as $key=>$val) {
        if(strlen($val) < 10) {
          $data_product_id_list_old[$key] = $val;
        }else{
          $data_product_id_list_new[$key] = $val;
        }
      }

      if($action != "ShippedOrder" && $action != "CompletedOrder") {
        // --------------------------------
        // ������ �ֹ� ��ȣ�� �ֹ������� ���� �ϱ�
        if(sizeof($data_product_id_list_old) > 0 ) {
          foreach($data_product_id_list_old as $key=>$val) {
            $data_list_old = $this->NHNPOrderSearch2($val);
            $data_list = array_merge($data_list,$data_list_old);
          }
        }


        // ������ �ֹ� ��ȣ�� �ֹ������� ���� �ϱ�
        if(sizeof($data_product_id_list_new) > 0 ) {
          $data_product_id_list_new = implode(",",array_unique($data_product_id_list_new));
          print_r(sizeof($data_product_id_list_new));
          exit;
          $data_list_new = $this->NHNPOrderSearch($data_product_id_list_new,true,$action2);
          $data_list = @array_merge($data_list,$data_list_new);
          //sleep(0.5);
        }
      }
    }

    // --------------------------------
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
  // $ProductOrderID : ��ǰ �ֹ���ȣ
  // $DeliveryMethodCode : ��۹�� �ڵ�
  // $DeliveryCompanyCode : �ù�� �ڵ�
  // TrackingNumber : �����ȣ
  //------------------------------------------------------------------------
  function NHNShipOrder($para) {
    
   // �⺻����
    $this->action = "ShipProductOrder";
    $this->operation = $this->action;
    $this->init();

    $DispatchDate = date("Y-m-d\TH:i:s",strtotime("-9hour"));

    //���۵����� ���� �� ����
    $request_data="
    <mall:ProductOrderID>".$para['OrderID']."</mall:ProductOrderID>
    <mall:DeliveryMethodCode>".$para['DeliveryMethodCode']."</mall:DeliveryMethodCode>
    <mall:DeliveryCompanyCode>".$para['DeliveryCompanyCode']."</mall:DeliveryCompanyCode>
    <mall:TrackingNumber>".$para['TrackingNumber']."</mall:TrackingNumber>
    <mall:DispatchDate>".$DispatchDate."</mall:DispatchDate>
    </mall:".$this->operation."Request>
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

}  // class - End




?>