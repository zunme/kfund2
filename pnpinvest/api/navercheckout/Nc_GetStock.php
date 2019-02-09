<?
//-------------------------------------------------------------------------
//
//  ���̹� üũ�ƿ� ��� CLASS : 2011-05-02 ������
//
//  �׽�Ʈ�����ּ� : http://stock.nhn.playauto.co.kr/NhnStock.php?TYPE=PAY
//  ���������ּ� : http://stockc.nhn.playauto.co.kr/NhnStock.php?TYPE=PAY
//  stock �ּҷ� �Ϸ��Ͽ����� 222�� 229�� �����Ǻ���ð��� �־� stockc�� 229�� ����
//-------------------------------------------------------------------------
require_once 'nhnapi-simplecryptlib.php';
require_once 'HTTP/Request.php';
require_once "xml_parser_stock.php";

class NaverCheckOutStock {
  //-------------------------------------------------
  //  ������
  //-------------------------------------------------
  function NaverCheckOutStock($main_sock,$select_sock,$execute_Sock){

    $this->select_sock = $select_sock;
    $this->main_sock = $main_sock;
    $this->execute_sock = $execute_Sock;

  // function - End
  }

  //------------------------------------------------------------------------
  // ���̹� API �⺻ ����
  //------------------------------------------------------------------------
  function init() {

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
    $dataList2 = array();
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
          $dataList2[$val['MALLUID']][] = $dataList3;
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

    //  �迭�� �����ϴ� �ʵ常 �迭�� ����
    $filed = array('ORDERDATETIME','ORDERID','ORDERSTATUSCODE','ORDERSTATUS','ORDERERNAME','ORDERERID','PAYMENTNUMBER','MALLMANAGECODE','MALLUID','PRODUCTID','PRODUCTOPTION','QUANTITY','NHNORDERID','BUNDLECOUNT');

    if(is_array($dataList2)) {
      foreach($dataList2 as $kv=>$vv) {
        foreach($vv as $k=>$v) {
          foreach($chdata as $k2=>$v2) {
            if($v[$k2] != "") { // ������ ���� Ȯ��
              $dataList2[$kv][$k][$k2] = $this->scl->decrypt($decryptKey, $v[$k2]);
            }
          }
          unset($dataList[$k]);
          foreach($dataList2[$kv][$k] as $k2=>$v2) {
            if(in_array($k2,$filed)) {
              $dataList4[$kv][$k][$k2] = iconv("utf-8","UHC",$v2);
            }
          }
        }
      }
    }
    return $dataList4;
  }

  //------------------------------------------------------------------------
  // ���º� �ֹ� ����
  //------------------------------------------------------------------------
  function NHNOrder($thiskey,$type_action) {

    //  ��ȸ�Ⱓ - ����ð� +- 30��
    $this->InquiryTimeFrom = date('Y-m-d')."T".date('H:i:s',time() - 1800)."+09:00";
    $this->InquiryTimeTo = date('Y-m-d')."T".date('H:i:s',time() + 1800)."+09:00";

    //$this->InquiryTimeFrom = "2011-05-11T00:00:00+09:00";
    //$this->InquiryTimeTo = "2011-05-12T00:00:00+09:00";

    /*  ���̹����� ������ ���� ������ ������ ��ȯ,��ǰ,���� ������
    UNPAY ���Ա� �ֹ�
    PAY ���� �Ϸ�
    CANCEL �ֹ� ���
    EXCHANGE ��ȯ ��û
    RETURN ��ǰ ��û
    INQUIRY �� ���� ���
    */
    switch($type_action) {
      //  ���Ա� �ֹ�
      case "UNPAY" : $action='UnpaidOrder'; break;
      //  �����Ϸ�
      case "PAY" : $action='PaidOrder'; break;

			//case "ORDER" : $action='PlacedOrder'; break;
      //  �ֹ����
      //case "CANCEL" : $action='CanceledOrder'; break;

      default : echo $this->WebViewData(); exit; break;
    }

    echo $this->WebViewData();

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
    $data = $this->XmlToText($data);

		// ��ü�� ������ ����
    $this->UserDateSave($data);

		/*/  �׽�Ʈ�� ��ü�ڵ� �ְ� ����
		$test_data = array();
		//$td_key = 'BABBDD02-B1E4-4B9E-8EBA-0AE048391583';
		$td_key = '031ED885-D85B-45FB-85FA-94E46AA0D44F';
		$test_data[$td_key] = $data[$td_key];
		if($test_data[$td_key][0]['MALLUID']!='') {
			// ��ü�� ������ ����
			xmp($test_data);
			$this->UserDateSave($test_data);
		}

		// ��ü�� ������ ����
		//$this->UserDateSave($test_data); */

    exit;
  }


  //-----------------------------------------------------------------------------
  // ��ü�� ���̺� ����
  //-----------------------------------------------------------------------------
  function UserDateSave($data) {

    //  CP_CODE �˻�
    $query = "select cp_code,ncbuttonkey,DBServerNM from ECHost.Minishop_Nckey order by No asc";
    $user_data = $this->main_sock->fetch($query);

    $user_info = array();
    for($i=0; $i<count($user_data); $i++) {
      $user_info[cp_code][$i] = $user_data[$i][cp_code];
      $user_info[ncbuttonkey][$i] = $user_data[$i][ncbuttonkey];
    }

    if (!is_array($data)){ $data = array(); }

    foreach($data as $k=>$v) {

      $cp_code_key = array_search($k,$user_info[ncbuttonkey]);

      if($cp_code_key!==false) {

        //  �ֹ��� ��ŭ ����
        for($od=0; $od<count($v); $od++) {
          $table = "EC_".$user_info[cp_code][$cp_code_key];

          //  �ߺ� üũ
          $duple_result = $this->DuplOrderid($table,$v[$od][PRODUCTID],$v[$od][ORDERID],$v[$od][NHNORDERID],$v[$od][ORDERSTATUSCODE]);

          if($duple_result!='dupl') {

            $insert_array = array();
            $insert_array[no] = '';
            $insert_array[ORDERDATETIME] = $v[$od][ORDERDATETIME];
            $insert_array[NHNORDERID] = $v[$od][NHNORDERID];
            $insert_array[ORDERID] = $v[$od][ORDERID];
            $insert_array[ORDERSTATUSCODE] = $v[$od][ORDERSTATUSCODE];
            $insert_array[PRODUCTID] = $v[$od][PRODUCTID];
            $insert_array[PRODUCTOPTION] = $v[$od][PRODUCTOPTION];
            $insert_array[QUANTITY] = $v[$od][QUANTITY];
            $insert_array[BUNDLECOUNT] = $v[$od][BUNDLECOUNT];
            $result = $this->execute_sock->Array_insert($table.".Goods_Stock_Log",$insert_array,'Success');

            if($result=='Success') {

              //  ��ǰ�˻�
              $goods_query = "select Goods_no,Volume,Stock from ".$table.".Goods_Basic where Goods_no='".$v[$od][PRODUCTID]."';";
              $goods_data = $this->select_sock->fetch($goods_query);

              //  ': [�߰�����]' �������� �ɼǰ� �߰����� �и�. ���� �߰����� ǥ�� ����� ���ó���κе� �ݵ�� �����ؾ���.
              $user_opt = explode(': [�߰�����]',$v[$od][PRODUCTOPTION]);

              //  ��ǰ�ɼ� �˻�
              $opt_query = "select * from ".$table.".Goods_option where Goods_no='".$v[$od][PRODUCTID]."' and Option_section='1';";
              $opt_data = $this->select_sock->fetch($opt_query);
              $temp = explode("[_Opt_]",$opt_data[0][details]);

              //  ���ɼ��� �ְ� �����ɼ��� ���µ� �Է����� �ƴϰ� �󼼿ɼǵ� �ƴҶ� ó������
              if(trim($user_opt[0])=='' && (trim($temp[0])=='manual' || trim($temp[0])=='manual2' || trim($temp[0])=='manual3' || trim($temp[0])=='insert' || trim($temp[1])!='')) {
                // ó������
              }
              else {
                //  �ɼǾ���
                if(trim($user_opt[0])=='') {
                  $this->UpdateGoodsStock($table,$v[$od][PRODUCTID],$goods_data[0][volume],$v[$od][QUANTITY]);
                }
                else {
                  //  �� �ɼ�
                  if($temp[0]=='manual' || $temp[0]=='manual2' || $temp[0]=='manual3') {

                    $manual_opt_result = $this->UpdateManualoptDetail($v[$od][PRODUCTID],$user_opt,$opt_data,$v[$od][QUANTITY],$table);
                    if($manual_opt_result=='Success') {
                      $this->UpdateGoodsStock($table,$v[$od][PRODUCTID],$goods_data[0][volume],$v[$od][QUANTITY]);
                    }
                  }
                  //  �Է��� �ɼ�
                  else {
                    $this->UpdateGoodsStock($table,$v[$od][PRODUCTID],$goods_data[0][volume],$v[$od][QUANTITY]);
                  }
                }
              }

            // $result=='Success' end
            }
          // $duple_result!='dupl'
          }
        // $od loop end
        }
      //  $cp_code_key
      }
    //  $data end
    }

  // funciton - End
  }


  //-----------------------------------------------------------------------------
  // �ֹ� �ߺ� üũ - �ߺ��� ��� ���� ������Ʈ �Ѵ�
  // $table=�����ͺ��̽���, $goods_no=��ǰ�ڵ�, $playauto_orderid=�÷��̿��� �ֹ���ȣ(unique), $naver_orderid=���̹��ֹ���ȣ, $naver_orderstatus=���̹��ֹ�����
  //-----------------------------------------------------------------------------
  function DuplOrderid($table,$goods_no,$playauto_orderid,$naver_orderid,$naver_orderstatus) {
    //  �ֹ� �αװ˻�
    $log_query = "select PRODUCTID,ORDERID,NHNORDERID from ".$table.".Goods_Stock_Log where PRODUCTID='$goods_no' and ORDERID='$playauto_orderid' and NHNORDERID='$naver_orderid';";

    $log_data = $this->select_sock->fetch($log_query);

    if($log_data[0][orderid]=='') { $log_result = 'ok';}
    else {
      $dupl_update_query = array();
      $dupl_update_query[ORDERSTATUSCODE] = $naver_orderstatus;
      $dupl_update_where = sprintf("PRODUCTID='%s' and ORDERID='%s' and NHNORDERID='%s'",$goods_no,$playauto_orderid,$naver_orderid);
      $dupl_update_data = $this->execute_sock->Array_update($table.".Goods_Stock_Log",$dupl_update_query,$dupl_update_where,"Success",false);

      $log_result = 'dupl';
    }

    return $log_result;

  // funciton - End
  }


  //-----------------------------------------------------------------------------
  // �󼼿ɼ� ���� ����
  // $umd_goods_code=��ǰ�ڵ�, $umd_user_opt=�ֹ��ɼ�, $umd_db_opt=��ǰ�ɼ�, $umd_user_quantity=�ֹ�����, $table=��ü�����ͺ��̽�
  //-----------------------------------------------------------------------------
  function UpdateManualoptDetail($umd_goods_code, $umd_user_opt, $umd_db_opt, $umd_user_quantity, $table) {

		$temp = explode("[_Opt_]",$umd_db_opt[0][details]);

		if(trim($temp[0]) == 'manual' || trim($temp[0]) == 'manual2' || trim($temp[0]) == 'manual3') {

			$opt_temp = explode("[_Opt_]",$umd_db_opt[0][details]);
			$opt_db = unserialize($opt_temp[1]);

			$user_temp = explode("____",$umd_user_opt[0]);

			$user_opt_part = explode('||',$user_temp[0]);

			$user_opt_part[0] = trim(ereg_replace(' \(.*��\)','',$user_opt_part[0])); //  �ɼǰ� ����
			$user_opt_part[1] = trim(ereg_replace(' \(.*��\)','',$user_opt_part[1])); //  �ɼǰ� ����

			foreach($opt_db as $od_k=>$od_v) {

				//  ����1�� ������
				if(trim($od_k) == trim($user_opt_part[0])) {

					//  ����2�� Ȯ��
					$udp_flag = 0;
					//	���� ���и��� �ι���
					if(trim($umd_db_opt[0][opt_name1]!='') && trim($umd_db_opt[0][opt_name2]!='')) {
						$od_v_key = array_keys($od_v);
						if(trim($od_v_key[0])==trim($user_opt_part[0])) {
							$udp_flag = 1;						
						}
					}
					//	���и��� �ѹ��� ��
					elseif(trim($umd_db_opt[0][opt_name1]!='') && trim($umd_db_opt[0][opt_name2]=='')) {
						$udp_flag = 2;
					}

					//  �ɼ��� �ϳ��� �ֹ��� ���
					if(trim($user_opt_part[1])=='' && count($opt_db[$od_k])=='1' && ($udp_flag=='1' || $udp_flag=='2')) {

						if($udp_flag=='2') { 
							$one_opt = explode('|',$opt_db[$od_k][0]);
							$quantity = $one_opt[0] - $umd_user_quantity;
							$opt_db[$od_k][0] = $quantity.'|'.$one_opt[1];
							$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);
						}
						else {
							$one_opt = explode('|',$opt_db[$od_k][$od_k]);
							$quantity = $one_opt[0] - $umd_user_quantity;
							$opt_db[$od_k][$od_k] = $quantity.'|'.$one_opt[1];
							$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);
						}

						//  �ɼǼ��� ���� ������Ʈ
						$opt_update_array = array();
						$opt_update_array[Details] = $opt_update;
						$opt_where = sprintf("Goods_no='%s' and Option_section='1'",$umd_goods_code);
						$opt_result = $this->execute_sock->Array_update($table.".Goods_option",$opt_update_array,$opt_where,"Success",false);
					}
					//  �ɼ��� �ΰ��� �ֹ��� ���
					else {
						//  ����2 �˻�
						foreach($opt_db[$user_opt_part[0]] as $odk_k=>$odk_v) {
							//  ����2�� �������
							if(trim($odk_k)==trim($user_opt_part[1])) {

								$two_opt = explode('|',$odk_v);
								$quantity = $two_opt[0] - $umd_user_quantity;
								$opt_db[$user_opt_part[0]][$odk_k] = $quantity.'|'.$two_opt[1];
								$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);

								//  �ɼǼ��� ���� ������Ʈ
								$opt_update_array = array();
								$opt_update_array[Details] = $opt_update;
								$opt_where = sprintf("Goods_no='%s' and Option_section='1'",$umd_goods_code);
								$opt_result = $this->execute_sock->Array_update($table.".Goods_option",$opt_update_array,$opt_where,"Success",false);

							}
						}
					}
				}
			// foreach end
			}

		}

		if(trim($temp[0]) == 'manual2' || trim($temp[0]) == 'manual3') {

			$opt_temp = explode("[_Opt_]",$umd_db_opt[0][details2]);
			$opt_db = unserialize($opt_temp[1]);

			$user_opt_part = explode('||',$user_temp[1]);
			$user_opt_part[0] = trim(ereg_replace(' \(.*��\)','',$user_opt_part[0])); //  �ɼǰ� ����
			$user_opt_part[1] = trim(ereg_replace(' \(.*��\)','',$user_opt_part[1])); //  �ɼǰ� ����

			foreach($opt_db as $od_k=>$od_v) {
				//  ����1�� ������

				if(trim($od_k) == trim($user_opt_part[0])) {

					//  ����2�� Ȯ��
					$udp_flag = 0;
					//	���� ���и��� �ι���
					if(trim($umd_db_opt[0][opt_name3]!='') && trim($umd_db_opt[0][opt_name4]!='')) {
						$od_v_key = array_keys($od_v);
						if(trim($od_v_key[0])==trim($user_opt_part[0])) {
							$udp_flag = 1;						
						}
					}
					//	���и��� �ѹ��� ��
					elseif(trim($umd_db_opt[0][opt_name3]!='') && trim($umd_db_opt[0][opt_name4]=='')) {
						$udp_flag = 2;
					}

					//  �ɼ��� �ϳ��� �ֹ��� ���
					if(trim($user_opt_part[1])=='' && count($opt_db[$od_k])=='1' && ($udp_flag=='1' || $udp_flag=='2')) {

						if($udp_flag=='2') { 
							$one_opt = explode('|',$opt_db[$od_k][0]);
							$quantity = $one_opt[0] - $umd_user_quantity;
							$opt_db[$od_k][0] = $quantity.'|'.$one_opt[1];
							$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);
						}
						else {
							$one_opt = explode('|',$opt_db[$od_k][$od_k]);
							$quantity = $one_opt[0] - $umd_user_quantity;
							$opt_db[$od_k][$od_k] = $quantity.'|'.$one_opt[1];
							$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);
						}

						//  �ɼǼ��� ���� ������Ʈ
						$opt_update_array = array();
						$opt_update_array[Details2] = $opt_update;
						$opt_where = sprintf("Goods_no='%s' and Option_section='1'",$umd_goods_code);
						$opt_result = $this->execute_sock->Array_update($table.".Goods_option",$opt_update_array,$opt_where,"Success",false);
					}
					//  �ɼ��� �ΰ��� �ֹ��� ���
					else {
						//  ����2 �˻�
						foreach($opt_db[$user_opt_part[0]] as $odk_k=>$odk_v) {
							//  ����2�� �������
							if(trim($odk_k)==trim($user_opt_part[1])) {

								$two_opt = explode('|',$odk_v);
								$quantity = $two_opt[0] - $umd_user_quantity;
								$opt_db[$user_opt_part[0]][$odk_k] = $quantity.'|'.$two_opt[1];
								$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);

								//  �ɼǼ��� ���� ������Ʈ
								$opt_update_array = array();
								$opt_update_array[Details2] = $opt_update;
								$opt_where = sprintf("Goods_no='%s' and Option_section='1'",$umd_goods_code);
								$opt_result = $this->execute_sock->Array_update($table.".Goods_option",$opt_update_array,$opt_where,"Success",false);

							}
						}
					}
				}
			// foreach end
			}

		}

		if(trim($temp[0]) == 'manual3') {

			$opt_temp = explode("[_Opt_]",$umd_db_opt[0][details3]);
			$opt_db = unserialize($opt_temp[1]);

			$user_opt_part = explode('||',$user_temp[2]);
			$user_opt_part[0] = trim(ereg_replace(' \(.*��\)','',$user_opt_part[0])); //  �ɼǰ� ����
			$user_opt_part[1] = trim(ereg_replace(' \(.*��\)','',$user_opt_part[1])); //  �ɼǰ� ����

			foreach($opt_db as $od_k=>$od_v) {
				//  ����1�� ������

				if(trim($od_k) == trim($user_opt_part[0])) {

					//  ����2�� Ȯ��
					$udp_flag = 0;
					//	���� ���и��� �ι���
					if(trim($umd_db_opt[0][opt_name5]!='') && trim($umd_db_opt[0][opt_name6]!='')) {
						$od_v_key = array_keys($od_v);
						if(trim($od_v_key[0])==trim($user_opt_part[0])) {
							$udp_flag = 1;						
						}
					}
					//	���и��� �ѹ��� ��
					elseif(trim($umd_db_opt[0][opt_name5]!='') && trim($umd_db_opt[0][opt_name6]=='')) {
						$udp_flag = 2;
					}

					//  �ɼ��� �ϳ��� �ֹ��� ���
					if(trim($user_opt_part[1])=='' && count($opt_db[$od_k])=='1' && ($udp_flag=='1' || $udp_flag=='2')) {

						if($udp_flag=='2') { 
							$one_opt = explode('|',$opt_db[$od_k][0]);
							$quantity = $one_opt[0] - $umd_user_quantity;
							$opt_db[$od_k][0] = $quantity.'|'.$one_opt[1];
							$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);
						}
						else {
							$one_opt = explode('|',$opt_db[$od_k][$od_k]);
							$quantity = $one_opt[0] - $umd_user_quantity;
							$opt_db[$od_k][$od_k] = $quantity.'|'.$one_opt[1];
							$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);
						}

						//  �ɼǼ��� ���� ������Ʈ
						$opt_update_array = array();
						$opt_update_array[Details3] = $opt_update;
						$opt_where = sprintf("Goods_no='%s' and Option_section='1'",$umd_goods_code);
						$opt_result = $this->execute_sock->Array_update($table.".Goods_option",$opt_update_array,$opt_where,"Success",false);
					}
					//  �ɼ��� �ΰ��� �ֹ��� ���
					else {

						//  ����2 �˻�
						foreach($opt_db[$user_opt_part[0]] as $odk_k=>$odk_v) {
							//  ����2�� �������
							if(trim($odk_k)==trim($user_opt_part[1])) {

								$two_opt = explode('|',$odk_v);
								$quantity = $two_opt[0] - $umd_user_quantity;
								$opt_db[$user_opt_part[0]][$odk_k] = $quantity.'|'.$two_opt[1];
								$opt_update = $temp[0]."[_Opt_]".serialize($opt_db);

								//  �ɼǼ��� ���� ������Ʈ
								$opt_update_array = array();
								$opt_update_array[Details3] = $opt_update;
								$opt_where = sprintf("Goods_no='%s' and Option_section='1'",$umd_goods_code);
								$opt_result = $this->execute_sock->Array_update($table.".Goods_option",$opt_update_array,$opt_where,"Success",false);

							}
						}
					}
				}
			// foreach end
			}

		}

    Return $opt_result;

  // funciton - End
  }

  //-----------------------------------------------------------------------------
  // ��ǰ����� ����
  // $table=�����ͺ��̽�, $ugs_goods_no=��ǰ�ڵ�, $ugs_db_volume=������, $ugs_user_volume=��������
  //-----------------------------------------------------------------------------
  function UpdateGoodsStock($table,$ugs_goods_no,$ugs_db_volume,$ugs_user_volume) {

    //  ��ǰ���� ���� ������Ʈ
    $update_array = array();
    $update_array[Volume] = $ugs_db_volume - $ugs_user_volume;
    if($update_array[Volume] < 1) { $update_array[Stock] = '0'; }
    $where = sprintf("Goods_no='%s'",$ugs_goods_no);
    $goosd_result = $this->execute_sock->Array_update($table.".Goods_Basic",$update_array,$where,"Success",false);

  // funciton - End
  }


  //------------------------------------------------------------------------
  // ���̹� ���� ������
  //------------------------------------------------------------------------
  function WebViewData() {
    $ViewData = 'RESULT=TRUE';
    return $ViewData;
  }


}  // class - End


?>