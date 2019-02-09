<?
//-------------------------------------------------------------------------
//
//  ���̹� üũ�ƿ� ��ǰ�� CLASS : 2011-03-15 ������
//
//  �����ּ� : http://test.ecadmin.playautocorp.com/execute/Execute_Admin.php?as=OrderMan&action=NHNAfter_All
//-------------------------------------------------------------------------
require_once 'nhnapi-simplecryptlib.php';
require_once 'HTTP/Request.php';
require_once "xml_parser_after.php";

class NaverCheckOutAfter_All {
  //-------------------------------------------------
  //  ������
  //-------------------------------------------------
  function NaverCheckOutAfter_All($select_sock,$DBNAME,$MainDataBase,$main_sock,$execute_Sock){

	$this->sock = $select_sock;
    $this->main_sock = $main_sock;
	$this->execute_sock = $execute_Sock;
    $this->SMS_sock = $sms_sock;
	$this->DataBase=$DBNAME; //  ������ ���̽��� ����ȭ
    $this->MainDataBase=$MainDataBase; //  ���� DB


  // function - End
  }

  //------------------------------------------------------------------------
  // ���̹� API �⺻ ����
  //------------------------------------------------------------------------
  function init() {
    if($this->stype == "AMP") {
      //$this->test = true;
    }

    $this->test = true;
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
  function XmlToText($data) {

	$query = "select CP_CODE,NcShopkey,NcButtonkey from $this->MainDataBase.Minishop_Nckey WHERE NcButtonkey != '' ";
    $this->main_sock->reset_rownum();
	$com = $this->main_sock->fetch_key($query);
    $rows = $this->main_sock->rownum;

	$data_size = sizeof($data);

	for($i=0; $i<$rows; $i++) {

		for($j=0; $j<$data_size; $j++) {

			$xml_parse =& new xml_parse2($com[ncbuttonkey][$i],$this->action);
			$dataList = $xml_parse->parse($data[$j]);

			if(!$dataList)
				continue;

			//��ȣȭ ��� �ʵ�
			$chdata = array(
			"ProductName" => true,
			"ProductOption" => true,
			"PurchaseEvaluationContent" => true,
      "WriterId" => true,
			);

			//��ȣȭ �ϱ�
			$decryptKey = $this->scl->generateKey($this->timestamp , $this->key);

			foreach($dataList as $k=>$v) {
				foreach($chdata as $k2=>$v2) {
					if($v[$k2] != "") { // ������ ���� Ȯ��
						$dataList[$k][$k2] = $this->scl->decrypt($decryptKey, $v[$k2]);
					}
				}

				foreach($dataList[$k] as $k2=>$v2) {
					$dataList[$k][$k2] =iconv("utf-8","UHC",$v2);
				}

			}

				if($dataList) {
				//xmp($dataList);
				$limit = sizeof($dataList);

				for($k=0; $k<$limit; $k++) {

					// ��¥ �߶� ���̱�.
					$newdate = explode("T",$dataList[$k][WRITEDATETIME]);
					$newdate2 = explode(".00Z",$newdate[1]);
					$regi_date = $newdate[0]." ".$newdate2[0];
					// 9�ð� ���ϱ� �߰� 2011-07-13 ������
					$regi_date =  date("Y-m-d H:i:s", strtotime('+9 HOUR',strtotime($regi_date)));

					$sel_query = "select * from EC_".$com[cp_code][$i].".Board_goods_review WHERE orderId ='".$dataList[$k][ORDERID]."' AND  goods_code='".$dataList[$k][PRODUCTID]."' AND regi_date ='$regi_date' ";
					$sel_sql   =  $this->sock->fetch_key($sel_query);

					// �ߺ��� �������
					if($sel_sql == '') {

						$options	=  $dataList[$k][PRODUCTOPTION] =='null' ? "" : $dataList[$k][PRODUCTOPTION];
						$contents = str_replace("'","`",$dataList[$k][PURCHASEEVALUATIONCONTENT]);
						$goods_name = str_replace("'","`",$dataList[$k][PRODUCTNAME]);

						$insert_query = "insert into EC_".$com[cp_code][$i].".Board_goods_review set regi_date = '$regi_date' , orderId = '".$dataList[$k][ORDERID]."' , options='".$options."' , contents='".$contents."' , writer='".$dataList[$k][WRITER]."' , rate= '".$dataList[$k][PURCHASESATISFACTIONDEGREE]."' , goods_code ='".$dataList[$k][PRODUCTID]."' , goods_name='".$goods_name."' ";
						$result = $this->execute_sock->execute($insert_query);

						if($result == "Success") {
							echo "<li>��� ���� (�����ڵ� : ".$com[cp_code][$i]." �ֹ���ȣ : ".$dataList[$k][ORDERID]." ��ǰ�ڵ� : ".$dataList[$k][PRODUCTID]." ��Ͻð� : ".$regi_date.")</li>" ;
						}else{
							echo "<li>��� ���� (�����ڵ� : ".$com[cp_code][$i]." �ֹ���ȣ : ".$dataList[$k][ORDERID]." ��ǰ�ڵ� : ".$dataList[$k][PRODUCTID]." ��Ͻð� : ".$regi_date.")</li>" ;
						}

						flush();
					} else{
						echo "<li>�ߺ� ��ǰ�ı� �Դϴ�.(�����ڵ� : ".$com[cp_code][$i]." �ֹ���ȣ : ".$dataList[$k][ORDERID]." ��ǰ�ڵ� : ".$dataList[$k][PRODUCTID]." ��Ͻð� : ".$regi_date.")</li>" ;
						flush();
					}

				// loop - End
				}

			}

		}


	}


  }


  //------------------------------------------------------------------------
  // ��ǰ�� ����
  //------------------------------------------------------------------------
  function NHNAfter_All() {

	  set_time_limit(0);
  	$data_list = array();
	  $data =array();

	  $lists = 0;
    for($i=0; $i>=-1; $i--) {
      $s_timestamp = time() - 86400 * ($i+1);
      $e_timestamp = time() - 86400 * $i;
      $d_date = mktime(0,0,0,date('m',$s_timestamp),date('d',$s_timestamp),date('Y',$s_timestamp));
      $p_date = mktime(0,0,0,date('m',$e_timestamp),date('d',$e_timestamp),date('Y',$e_timestamp));
      $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
      $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ
      
      $d_date = mktime(0,0,0,01,14,2012);
      $p_date = mktime(0,0,0,01,15,2012);
      $this->InquiryTimeFrom = date('Y-m-d',$d_date - 32400)."T".date('H:i:s',$d_date - 32400);  //InquiryTimeFrom ��ȸ�Ⱓ
      $this->InquiryTimeTo = date('Y-m-d',$p_date - 32400)."T".date('H:i:s',$p_date - 32400); //InquiryTimeTo ��ȸ�Ⱓ


      // �⺻����
      $this->action = "PurchaseReview";
      $this->operation = "GetPurchaseReviewList";
      $this->init();

      //���۵����� ����
      $request_data="
      <base:InquiryTimeFrom>" . $this->InquiryTimeFrom . "</base:InquiryTimeFrom>
      <base:InquiryTimeTo>" . $this->InquiryTimeTo . "</base:InquiryTimeTo>
      <base:InquiryExtraData></base:InquiryExtraData>
      </mall:".$this->operation."Request>
      </soapenv:Body>
      </soapenv:Envelope>";

      // ����
      $data = $this->sendNHN($request_data);

			sleep(0.5);
	    $lists++;

      // XML�� TEXT�� ��ȯ
      //$data = $this->XmlToText($data,$this->NCkey);
      //$data_list = array_merge($data_list,$data);
	  }

	  $this->XmlToText(&$data);
  }

}  // class - End

?>