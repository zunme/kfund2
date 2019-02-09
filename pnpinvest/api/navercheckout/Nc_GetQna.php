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
  //-------------------------------------------------
  //  ������
  //-------------------------------------------------
  function NaverCheckOutOrder(){
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
      $this->targetUrl = "http://sandbox.api.naver.com/Checkout/MallService2";
    }else{
      //��� ����
      $accessLicense = "0100010000e5be7c82a4d24f24e836ac9b1ac177eaa62bbd3ec75d51a1f3b3760fa9346805";  // ��� ���Ϸ� �߼۵�  ���̼�ƮŰ �Է�
      $this->key= "AQABAAD/KPnN8qc4ZRzkg4/jOlgdH8AoAar+K0fxhyDk45CIfA==";  // ��и��Ϸ� �߼۵� ��ũ��Ű
      $this->targetUrl = "http://api.naver.com/Checkout/MallService2";
    }

    // �⺻����
    $this->service = "MallService2";
    $detailLevel = "Full";
    $version = "2.0";
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
    <ns2:".$this->operation."Request xmlns=\"http://base.checkout.platform.nhncorp.com/\" xsi:type=\"ns2:GetPaidOrderListRequestType\" xmlns:ns2=\"http://mall.checkout.platform.nhncorp.com/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
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

    return $rq->getResponseBody();
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
          $dataList3['PRODUCTNAME'] = $val['PRODUCTNAME'][$key2];
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
      );


    //��ȣȭ �ϱ�
    $decryptKey = $this->scl->generateKey($this->timestamp , $this->key);
    if(is_array($dataList2)) {
      foreach($dataList2 as $k=>$v) {
        foreach($chdata as $k2=>$v2) {
          if($v[$k2] != "") { // ������ ���� Ȯ��
            $dataList2[$k][$k2] = $this->scl->decrypt($decryptKey, $v[$k2]);
          }
        }

        if($this->stype == "AMP") {
          foreach($dataList2[$k] as $k2=>$v2) {
            $dataList[$k][$k2] = $v2;
          }
        }else{
          unset($dataList[$k]);
          foreach($dataList2[$k] as $k2=>$v2) {
            $dataList[$k][$k2] = iconv("utf-8","UHC",$v2);
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

    // ������ ����
    echo $this->WebViewData($data);
    exit;
  }



  //------------------------------------------------------------------------
  // 1:1 ���� ����.
  //------------------------------------------------------------------------
  function NHNQna($thiskey,$action) {

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

    $data_list = array();
    for($i=15; $i>=-1; $i--) {

    // ��ȸ�Ⱓ �ִ� 24�ð�(1��)�Դϴ�.
    // ���̹� �ð��� GMT(�ѱ��ð�+9)�ð� �Դϴ�
    if($this->test) {
      $d_date = mktime(0,0,0,01,03,2010);
      $p_date = mktime(0,0,0,01,04,2010);
      $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
      $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ
    }else{
      $s_timestamp = time() - 86400 * ($i+1);
      $e_timestamp = time() - 86400 * $i;
      $d_date = mktime(0,0,0,date('m',$s_timestamp),date('d',$s_timestamp),date('Y',$s_timestamp));
      $p_date = mktime(0,0,0,date('m',$e_timestamp),date('d',$e_timestamp),date('Y',$e_timestamp));
      $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
      $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ
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
	  <IsAnswered> boolean </IsAnswered>
      </ns2:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";

      // ����
      $data = $this->sendNHN($request_data);

      // XML�� TEXT�� ��ȯ
      $data = $this->XmlToText($data,$this->NCkey);

      $data_list = array_merge($data_list,$data);
    }

    // �ֹ� ��� ��Ī���� �����ϱ�
    if($this->action == "PlacedOrder") {
      // �ű��ֹ��϶�
      $this->OrderMatchInsert($data_list);
    }else{
      // ����ȭ �϶�
    }

    echo $this->WebViewData($data_list);
    exit;
  }


  //------------------------------------------------------------------------
  // ��������
  // $OrderID : �ֹ� ��ȣ ( * �� �Ʒ��� ��Ģ�� ������� )
  //   - type�� save�� "_"���İ� �� �÷��̿��� �ֹ� ��ȣ
  //   - type�� exec�� "_"���İ� ���ŵ� ���̹� �ֹ� ��ȣ
  //------------------------------------------------------------------------
  function NHNShipOrder($para) {

    $data = array(); // ��� �޼��� �ʱ�ȭ
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


          if( eregi("SUCCESS",$real_data[0]['RESPONSETYPE']) ) {
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
    }else{
      $ViewDataEncode = base64_encode(serialize($data));
    }

    $ViewData = '<?xml version="1.0" encoding="utf-8"?>
    <PLAYAUTO_RESPONSE>
      <RESULT>OK</RESULT>
      <MSG></MSG>
      <DATA><![CDATA['.$ViewDataEncode.']]></DATA>
    </PLAYAUTO_RESPONSE>
    ';

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
      $in_data['wdate'] = date('Y-m-d H:i:s');
      $in_data['state'] = "�߼���";
      $in_data['cp_code'] = $this->CP_CODE;
      $in_data['nhn_ocode'] = $val['NHNORDERID'];
      $in_data['pa_ocode'] = $val['ORDERID'];
      $in_data['goods_code'] = $val['PRODUCTID'];
      $in_data['bundle_count'] = $val['BUNDLECOUNT'];
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
  }

*/
}  // class - End




?>