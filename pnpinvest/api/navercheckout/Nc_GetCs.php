<?
//-------------------------------------------------------------------------
//
//  ���̹� üũ�ƿ� �ֹ� CLASS : 2010-12-07 ������
//
//  �����ּ� : http://minishopnc.ecadmin.playautocorp.com/execute/Execute_Admin.php?as=OrderMan&action=NHNCs&NHNaction=Inquiry
//  �����ּ� : http://test.ecadmin.playautocorp.com/execute/Execute_Admin.php?as=OrderMan
//-------------------------------------------------------------------------
require_once 'nhnapi-simplecryptlib.php';
require_once 'HTTP/Request.php';
require_once "xml_parser.php";

class NaverCheckOutCs {
  //-------------------------------------------------
  //  ������
  //-------------------------------------------------
  function NaverCheckOutCs(){

  // function - End
  }

  //------------------------------------------------------------------------
  // ���̹� API �⺻ ����
  //------------------------------------------------------------------------
  function init() {

    //$test = true;

    if($test) {
      //��� ����
      $accessLicense = "01000100000503b9ecabad8d09c6e718ceaefd1f34033dc7d97db450be663f46105cb068";  // ��� ���Ϸ� �߼۵�  ���̼�ƮŰ �Է�(�׽�Ʈ)
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

    //�̴ϼ��� �ʴ� ������ ���� �������� �̻��̸� �Ʒ��� ���� ���� ���� �������̶�� �˸��� ���� ����
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

	//��ȣȭ ��� �ʵ�
    $chdata = array(
      "EMAIL" => true,
      "CUSTOMERID" => true,
      "MOBILEPHONENUMBER" => true,
      "ORDERERNAME" => true,
      );

    //��ȣȭ �ϱ�
    $decryptKey = $this->scl->generateKey($this->timestamp , $this->key);

    foreach($dataList as $k=>$v) {
      foreach($chdata as $k2=>$v2) {
        if($v[$k2] != "") { // ������ ���� Ȯ��
          $dataList[$k][$k2] = $this->scl->decrypt($decryptKey, $v[$k2]);
        }
      }

	  if($this->stype != "AMP") {
		  foreach($dataList[$k] as $k2=>$v2) {
			if(is_array($v2)) {
				foreach($v2 as $k3=>$v3) {
					$dataList[$k][$k2][$k3] = iconv("utf-8","UHC",$v3);
				}
			} else{
				$dataList[$k][$k2] =iconv("utf-8","UHC",$v2);
			}

		  }
	  }
    }


	return $dataList;

  }


  //------------------------------------------------------------------------
  // 1:1 ���� ����.
  //------------------------------------------------------------------------
  function NHNCs($thiskey,$action) {

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
	$this->stype =  $_GET['stype'] != ""  ? $_GET['stype'] : "" ;

	$data_list = array();

    for($i=35; $i>=-1; $i--) {

		$s_timestamp = time() - 86400 * ($i+1);
		$e_timestamp = time() - 86400 * $i;
		$d_date = mktime(0,0,0,date('m',$s_timestamp),date('d',$s_timestamp),date('Y',$s_timestamp));
		$p_date = mktime(0,0,0,date('m',$e_timestamp),date('d',$e_timestamp),date('Y',$e_timestamp));
		$this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
		$this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ

		// �⺻����
		$this->action = $action;
		$this->operation = "GetCustomer".$this->action."List";
		//$this->action = "GetCustomerInquiryList";
		//$this->operation =$this->action;
		$this->init();

		//���۵����� ����
		$request_data="
		<InquiryTimeFrom>" . $this->InquiryTimeFrom . "</InquiryTimeFrom>
		<InquiryTimeTo>" . $this->InquiryTimeTo . "</InquiryTimeTo>
		<InquiryExtraData></InquiryExtraData>
		<IsAnswered>false</IsAnswered>
		</ns2:".$this->operation."Request>
		</soapenv:Body>
		</soapenv:Envelope>";

		// ����
		$data = $this->sendNHN($request_data);

		// XML�� TEXT�� ��ȯ
		$data = $this->XmlToText($data,$this->NCkey);

		// üũ�ƿ� 1:1 ����api  ��ǰ�ڵ�� �÷��̿��� ��ǰ �ڵ� �߰����ֱ�. 2011-06-24 ������ ����----------------------------
		$counts =  sizeof($data);

		for($j =0; $j<$counts; $j++) {

			// �迭�� ���������� ������ �����ϴ� ��.
			$increment =0;

			// �ش� �ֹ��� ��ǰ �ڵ�� ��������
			$check_match = "SELECT goods_code from $this->DataBase.Order_Match WHERE nhn_ocode = '".$data[$j][ORDERID]."' ";
			$this->sock->reset_rownum();
			$order_array = $this->sock->fetch($check_match);
			$matchRow = $this->sock->rownum;

			// ��ǰ ������ ��ŭ roof
			for($ii=0; $ii<$matchRow; $ii++) {

				// ��ǰ DB ���� ���̹� ��ǰ�ڵ带 ��ȸ �� ������ ���̹� ��ǰ �ڵ�� �÷��̿��� ��ǰ�ڵ带 �����´�.
				$query_goods = "SELECT Goods_no ,PlayAutoGoodsCode from $this->DataBase.Goods_Basic WHERE Goods_no = '".$order_array[$ii][goods_code]."'";
				$goodsInfo = $this->sock->fetch($query_goods);

				// ������ �迭�� �߰�.
				if($goodsInfo) {

					$data[$j][GOODS][$increment][CODE]		= $goodsInfo[0][goods_no];
					$data[$j][GOODS][$increment][PACODE]	= $goodsInfo[0][playautogoodscode];
					$increment++;
				}
			}
		}
		// �߰� ��-------------------------------------------------------------------------------------------------------------------

		$data_list = array_merge($data_list,$data);
	}

	if($this->stype == "AMP") {
      $ViewDataEncode = base64_encode(json_encode($data_list));
    }else{
      $ViewDataEncode = base64_encode(serialize($data_list));
    }

    // ������ ����
    echo('<?xml version="1.0" encoding="utf-8"?>
    <PLAYAUTO_RESPONSE>
      <RESULT>OK</RESULT>
      <MSG></MSG>
      <DATA><![CDATA['.$ViewDataEncode.']]></DATA>
    </PLAYAUTO_RESPONSE>
    ');
    exit;
  }

  //------------------------------------------------------------------------
  // 1:1 ���Ǵ亯
  //------------------------------------------------------------------------
  function NHNShipCs() {

    // �⺻����
    $this->action = "AnswerCustomerInquiry";
    $this->operation = $this->action;
    $this->init();

	$str_utf8 = iconv("EUC-KR","UTF-8",$_GET['AContent']);

	//���۵����� ���� �� ����
    $request_data="
    <InquiryID>".$_GET['CsID']."</InquiryID>
    <AnswerContent>".$str_utf8."</AnswerContent>
    </ns2:".$this->operation."Request>
    </soapenv:Body>
    </soapenv:Envelope>";

    $data = $this->sendNHN($request_data);

    // XML�� TEXT�� ��ȯ
    $data = $this->XmlToText($data,$this->NCkey);

    // ������ ����
    echo('<?xml version="1.0" encoding="utf-8"?>
    <PLAYAUTO_RESPONSE>
      <RESULT>OK</RESULT>
      <MSG></MSG>
      <DATA><![CDATA['.base64_encode(serialize($data)).']]></DATA>
    </PLAYAUTO_RESPONSE>
    ');
    exit;
  }

}  // class - End

?>