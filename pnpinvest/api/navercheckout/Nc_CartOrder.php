<?

//item data�� �����Ѵ�.
class ItemStack {
  var $id;
  var $name;
  var $tprice;
  var $uprice;
  var $option;
  var $count;

	//option�� ���� �������, ���õ� �ɼ��� ������(/)�� �����ؼ� ǥ���ϴ� ���� �����Ѵ�.
  function ItemStack($_id, $_name, $_tprice, $_uprice, $_option, $_count,$_shop_id,$_CouponYn,$_cp_code) {
    $this->id = $_id;
    $this->name = $_name;
    $this->tprice = $_tprice;
    $this->uprice = $_uprice;
    $this->option = $_option;
    $this->count = $_count;
    $this->shop_id = $_shop_id;
		$this->CouponYn = $_CouponYn;
  }

  function makeQueryString($neid) {
    $ret .= 'ITEM_ID=' . urlencode($this->id);
    $ret .= '&EC_MALL_PID=' . urlencode($this->id);

    /*if($this->is_cp_code=='test' || $this->is_cp_code=='minishop') {
      // ���� ������ ���� �ǽð� ��ȸ�� �ش��ǰ�� ī�װ� ���� ����
      $url = "http://dev.etnlapi.shopping.naver.com/nmp05/external/getProductInfo.nhn?mallId=pa_brontibay&mallPid=".$this->id;
    }else{
      //http://etnlapi.shopping.naver.com/nmp05/external/getProductInfo.nhn?mallId=�����̵�(���ļ���ID)&mallPid=��ǰ���̵�
      $url = "http://etnlapi.shopping.naver.com/nmp05/external/getProductInfo.nhn?mallId=".$this->shop_id."&mallPid=".$this->id;
    }*/

    // ���� ������ ���� �ǽð� ��ȸ�� �ش��ǰ�� ī�װ� ���� ����
    $url = "http://etnlapi.shopping.naver.com/nmp05/external/getProductInfo.nhn?mallId=".$neid."&mallPid=".$this->id;

    $match_cate_info = $this->sendNHN($url);
    $match_cate_info = iconv("UTF-8", "euc-kr",$match_cate_info);
    preg_match('/","fullCatId":"([^,]+)","/',$match_cate_info,$match_cate);

    $ret .= '&CAT_ID=' . $match_cate[1];
    $ret .= '&ITEM_NAME=' . urlencode($this->name);
    $ret .= '&ITEM_COUNT=' . $this->count;
    $ret .= '&ITEM_OPTION=' . urlencode($this->option);
    $ret .= '&ITEM_TPRICE=' . $this->tprice;
    $ret .= '&ITEM_UPRICE=' . $this->uprice;

    return $ret;
  }

  //------------------------------------------------------------------------
  // Ư���ּ� ����
  //------------------------------------------------------------------------
  function sendNHN($url) {

    require_once 'HTTP/Request.php';

    //http post������� ��û ����
    $rq = new HTTP_Request($url);
    $rq->addHeader("Content-Type", "text/xml;charset=UTF-8");
    $rq->setBody($request_body);

    $result = $rq->sendRequest();
    if (PEAR::isError($result)) {
       //echo "ERROR[NHN0001]". $result->toString(). "\n";
       return;
    }

    //����޽��� Ȯ��
    $rcode = $rq->getResponseCode();
    if($rcode!='200'){
       //echo "ERROR[NHN0002]: http response code=". $rcode. "\n";
       return;
    }

    return $rq->getResponseBody();
  }

};

$shopId = $ncs[i][0];
$certiKey = $ncs[s][0];
$MALL_MANAGE_CODE = date("ymd").'-'.time().rand(0,9);

$http_host = $_SERVER[HTTP_HOST];
$backUrl = 'http://'.$http_host.'/?action=Cart';
$queryString = 'SHOP_ID='.urlencode($shopId);
$queryString .= '&CERTI_KEY='.urlencode($certiKey);
$queryString .= '&MALL_MANAGE_CODE='.$MALL_MANAGE_CODE;
$queryString .= '&RESERVE1=';
$queryString .= '&RESERVE2=';
$queryString .= '&RESERVE3=';
$queryString .= '&RESERVE4=';
$queryString .= '&RESERVE5=';
$queryString .= '&BACK_URL='.$backUrl;
$totalMoney = 0;
$Cartlist = $_SESSION[CartList];	//	��ٱ�������
$Delivlist = $SaleCostOri = array();

// ���̽�ī���� ���ۿ� ���� 2011-08-17 ������
$GoodsCodes = array();
$pcates = array();
$PAUTOFCIDS = array();

// �α��� ȸ���ΰ�� ��� ����� �ҷ����� 2012-03-06 ������
$__flag = true;
if($_SESSION['UID'] != "") {
	$Cartlist = array();
	$data_array = $this->inst_SelectShop->GetCheckOutData($_POST[OrderID]);

	for($i=0; $i<$data_array[1]; $i++) {
		if($data_array[0][checkout_data][$i] == "") {
			$__flag = false;
			break;
		}
		$checkout_data = unserialize($data_array[0][checkout_data][$i]);
		$Cartlist[$i] = $checkout_data;
	// loop - End
	}
}

if($__flag == false) {
    echo"<script type=\"text/javascript\">
      window.alert(\"���Ž� ������ �߻��Ǿ����ϴ�. ��ٱ��ϸ� ����� �ٽô�� �����Ͽ� �ֽñ� �ٶ��ϴ�.\");
      history.go(-1);
      </script>";
    exit;
}

//DB�� ��ٱ��Ͽ��� ��ǰ ������ ��� �´�.
for($i=0; $i<count($Cartlist); $i++) {

  if($Cartlist[$i][Applycost] != "" || $Cartlist[$i][Applycost] > 0) {
    $Cartlist[$i][SaleCostOri] = $Cartlist[$i][Applycost];
  }

	//	�ɼ�����
  $goodsopt = unserialize($Cartlist[$i][GoodsOpt]);

  if($goodsopt[Size]!='' ) {
    for($po=0; $po<count($goodsopt[Size]); $po++) {
      //  �ɼǰ� ǥ��
      $OptCostView = '';
      $OptCost = $SaleCostOri[$i] = 0;
			//	�ɼǰ� ó��
      if(trim($goodsopt[OptCost][$po]) != '') {
        if($goodsopt[OptCost][$po] > 0 && $goodsopt[OptCost][$po]!='') { $OptCostView = " (+".$goodsopt[OptCost][$po]."��)"; }
				//	- �ݾ��� ��� - �Ѿ���ϱ� �ٲ��� ����. ������ �ʰ� +�� �����ٲ��ž�--^
        elseif($goodsopt[OptCost][$po] < 0 && $goodsopt[OptCost][$po]!='') { $OptCostView = " (".$goodsopt[OptCost][$po]."��)"; }
        else { $OptCostView = ""; }
        $OptCost = $goodsopt[OptCost][$po]/$goodsopt[Amt][0];
      }
      if(trim($goodsopt[Size][$po])==trim($goodsopt[Color][$po]) || trim($goodsopt[Color][$po])=='' || trim($goodsopt[Color][$po])=='0') {
        $post_option = $goodsopt[Size][$po].$OptCostView;
      }
      else {
        //  �ɼǸ� ���� / ���� || �� ���� : 2011-05-16 16:57 ������
        $post_option = $goodsopt[Size][$po].'||'.$goodsopt[Color][$po].$OptCostView;
      }
      //  �ɼǰ� ������ ����
		  $SaleCostOri[$i] = $Cartlist[$i][SaleCostOri] + $OptCost; // �ٷ������ִ� ó�� if($OptCost > 0) { $SaleCostOri[$i] = $Cartlist[$i][SaleCostOri] + $OptCost; }
    }

    $post_count = $goodsopt[Amt][0];

		if($goodsopt[Size2]!='') {
			for($po=0; $po<count($goodsopt[Size2]); $po++) {
				//  �ɼǰ� ǥ��
				$OptCost2 = 0;
				if(trim($goodsopt[OptCost2][$po]) != '') {
					if($goodsopt[OptCost2][$po] > 0 && $goodsopt[OptCost2][$po]!='') { $OptCostView2 = " (+".$goodsopt[OptCost2][$po]."��)"; }
					//	- �ݾ��� ��� - �Ѿ���ϱ� �ٲ��� ����. ������ �ʰ� +�� �����ٲ��ž�--^
					elseif($goodsopt[OptCost2][$po] < 0 && $goodsopt[OptCost2][$po]!='') { $OptCostView2 = " (".$goodsopt[OptCost2][$po]."��)"; }
					else { $OptCostView2 = ""; }
					$OptCost2 = $goodsopt[OptCost2][$po]/$goodsopt[Amt][0];
				}
				if(trim($goodsopt[Size2][$po])==trim($goodsopt[Color2][$po]) || trim($goodsopt[Color2][$po])=='' || trim($goodsopt[Color2][$po])=='0') {
					$post_option .= '____'.$goodsopt[Size2][$po].$OptCostView2;
				}
				else {
					//  / ���� || �� ���� : 2011-05-16 16:57 ������
					$post_option .= '____'.$goodsopt[Size2][$po].'||'.$goodsopt[Color2][$po].$OptCostView2;
				}
				//  �ɼǰ� ������ ����
				$SaleCostOri[$i] = $SaleCostOri[$i] + $OptCost2; // �ٷ������ִ� ó�� if($OptCost > 0) { $SaleCostOri[$i] = $Cartlist[$i][SaleCostOri] + $OptCost; }
			}
			$post_count = $goodsopt[Amt][0];

			if($goodsopt[Size3]!='') {

				for($po=0; $po<count($goodsopt[Size3]); $po++) {
					$OptCost3 = 0;
					if(trim($goodsopt[OptCost3][$po]) != '') {
						if($goodsopt[OptCost3][$po] > 0 && $goodsopt[OptCost3][$po]!='') { $OptCostView3 = " (+".$goodsopt[OptCost3][$po]."��)"; }
						//	- �ݾ��� ��� - �Ѿ���ϱ� �ٲ��� ����. ������ �ʰ� +�� �����ٲ��ž�--^
						elseif($goodsopt[OptCost3][$po] < 0 && $goodsopt[OptCost3][$po]!='') { $OptCostView3 = " (".$goodsopt[OptCost3][$po]."��)"; }
						else { $OptCostView3 = ""; }
						$OptCost3 = $goodsopt[OptCost3][$po]/$goodsopt[Amt][0];
					}
					if(trim($goodsopt[Size3][$po])==trim($goodsopt[Color3][$po]) || trim($goodsopt[Color3][$po])=='' || trim($goodsopt[Color3][$po])=='0') {
						$post_option .= '____'.$goodsopt[Size3][$po].$OptCostView3;
					}
					else {
						//  / ���� || �� ���� : 2011-05-16 16:57 ������
						$post_option .= '____'.$goodsopt[Size3][$po].'||'.$goodsopt[Color3][$po].$OptCostView3;
					}
					//  �ɼǰ� ������ ����
					$SaleCostOri[$i] = $SaleCostOri[$i] + $OptCost3; // �ٷ������ִ� ó�� if($OptCost > 0) { $SaleCostOri[$i] = $Cartlist[$i][SaleCostOri] + $OptCost; }
				}
				$post_count = $goodsopt[Amt][0];
			}
		}
  }
  else {
    $post_option = '';
    $post_count = $goodsopt[Amt];
  } // goodsopt end

  $SaleCostOri[$i] = $SaleCostOri[$i] > 0 ? $SaleCostOri[$i]:$Cartlist[$i][SaleCostOri];

  //�߰����� �ɼ� 2011-01-11 ������
  $Addition = unserialize($Cartlist[$i][Addition]);
  if(is_array($Addition[OptText])){
    $addpt="";
    $addpt= implode(",",$Addition[OptText]);
    $addpt=str_replace("none","",str_replace(",none","",$addpt));
    if(@trim($addpt)!='') {
      $post_option= $post_option ." : [�߰�����] ". $addpt ;
    }
    //if($Addition[OptCostSum] > 0) { $SaleCostOri[$i] = $SaleCostOri[$i] + $Addition[OptCostSum]; }
  }

  if($Addition[OptCostSum] > 0 ) {
    $post_tprice = $SaleCostOri[$i] * $post_count + $Addition[OptCostSum];
  }else{
    $post_tprice = $SaleCostOri[$i] * $post_count;
  }
  $item = new ItemStack($Cartlist[$i][GoodsCode], $Cartlist[$i][Goods_name], $post_tprice, $SaleCostOri[$i], $post_option, $post_count,$shopId,$CouponYn,$this->CP_CODE);
  $totalMoney += $post_tprice;

  $queryString .= '&'.$item->makeQueryString($ncs[neid][0]);

  if($Cartlist[$i][PAUTOFCID] != '') {
		$GoodsCodes[] = $Cartlist[$i][GoodsCode];
		$pcates[] = $Cartlist[$i][pcate];
		$PAUTOFCIDS[] = $Cartlist[$i][PAUTOFCID];
  }

	//	��ü�� ���
	if($_SESSION[CartDelivCostType]=='2') {
		//	��ǰ���� �����Ұ� ���⶧���� ó������. ���ǿ� ��ü���� ���� �Ѿ��.
	}
	//	�Ϲݹ��
	else {
		//  �� ��ǰ�� ��ۺ� ó��
		$Delivlist[goodsdelivtype][$i] = $Cartlist[$i][Delivery_pay];
		$Delivlist[goodsdelivselect][$i] = $Cartlist[$i][DelivTypeSelect] == "" ? '4' : $Cartlist[$i][DelivTypeSelect];

		if($Cartlist[$i][Delivery_pay] > 0) {
			if($Cartlist[$i][Delivery_pay] == 3) { $Delivlist[goodsdelivprice][$i] = trim(str_replace('��','',$Cartlist[$i][Delivery_text])); }
			else { $Delivlist[goodsdelivprice][$i] = $Cartlist[$i][Delivery_cost]; }
		}
		else {
			$Delivlist[goodsdelivprice][$i] = $_POST[deliv_common][Cost][0];
		}
	}

// loop - End
}



//	��ü�� ���
if($_SESSION[CartDelivCostType]=='2') {

	/* ��ü�� ���� ��ۺ� ���� - ��ü�� ������ ����� ���Ҹ� ����
	�������        SHIPPING_TYPE   SHIPPING_PRICE
	����+����       FREE            0��
	����+����       PAYED           0�� �̻�
	����+����       PAYED           0�� �̻�
	*/

	if (!is_array($_SESSION[totalcartdelivprice_sub])){ $_SESSION[totalcartdelivprice_sub] = array(); }
	foreach($_SESSION[totalcartdelivprice_sub] as $tcdp_k=>$tcdp_v) {
		$Delivlist[goodsdelivtype][] = $_SESSION[totalcartdelivprice_sub][$tcdp_k][pay];
		$Delivlist[goodsdelivprice][] = $_SESSION[totalcartdelivprice_sub][$tcdp_k][price];
	}

	$acv = @array_count_values($Delivlist[goodsdelivtype]);
	$delivlist_count = count($Delivlist[goodsdelivtype]);

	//	$acv[1] ����, $acv[2] ������, $acv[3] ����
	//  ���� ������
	if($delivlist_count == $acv[1]) {
		$shippingPrice = 0;
		$shippingType = 'FREE';
	}
	//  ���� ������
	elseif($delivlist_count == $acv[2]) {
		$shippingPrice = array_sum($Delivlist[goodsdelivprice]);
		$shippingType = 'PAYED';
	}
	elseif($delivlist_count == ($acv[1]+$acv[2])) {
		$shippingPrice = array_sum($Delivlist[goodsdelivprice]);
		$shippingType = 'PAYED';
	}
	else {
		echo"<script type=\"text/javascript\">
		  window.alert(\"��ۺ� ó���� ������ �߻��Ͽ����ϴ�. �����ڿ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.\");
		  history.go(-1);
		  </script>";
		exit;
	}
}
//	�Ϲݹ��
else {

	/* �Ϲ� ���� ��ۺ� ����
	�������        SHIPPING_TYPE   SHIPPING_PRICE
	����+����       FREE            0��
	����+����       FREE            0��
	����+����       FREE            0��
	����+����+����  FREE            0��
	����+����       ONDELIVERY      0�� �̻�
	����+����       PAYED           0�� �̻�
	����+����       PAYED           0�� �̻�
	*/

	//  ��ۺ� ó��
	$gds_acv = @array_count_values($Delivlist[goodsdelivselect]);
	$acv = @array_count_values($Delivlist[goodsdelivtype]);
	$delivlist_count = count($Delivlist[goodsdelivtype]);

	//  ���Ǻ� ������
	if($acv[0] > 0) {
		if($totalMoney >= $_POST[deliv_common][Free_line][0]) {
			$shippingPrice = 0;
			$shippingType = 'FREE';

			$flag = 1;
		}
		else {
			for($ii=0; $ii<$delivlist_count; $ii++) {
				if($Delivlist[goodsdelivtype][$ii]=='0') {

					if($Delivlist[goodsdelivselect][$ii] == '3') {
						$Delivlist[goodsdelivtype][$ii] = 3;
						$Delivlist[goodsdelivprice][$ii] = $_POST[deliv_common][Cost][0];
					} else {
						$Delivlist[goodsdelivtype][$ii] = 4;
						$Delivlist[goodsdelivselect][$ii] = 4;
						$Delivlist[goodsdelivprice][$ii] = $_POST[deliv_common][Cost][0];
					}
				}
			}
			$acv = array_count_values($Delivlist[goodsdelivtype]);		//$acv = array_count_values($Delivlist[goodsdelivselect]); // �����ҽ�
		}
	}

	//	$acv[1] ���Ḹ, $acv[2] ������($gds_acv[3] ����, $gds_acv[2] ����), $acv[3] ���Ҹ�, $acv[4] ��������(�⺻����� ��� ���رݾ׿� ������ $acv[4]�� ����)
	//  ���Ǻ� ���ῡ �������� �������
	if($flag < 1) {
		//  ������
		if($delivlist_count == $acv[1] || $acv[1] > 0) {
			$shippingPrice = 0;
			$shippingType = 'FREE';
		}
		//  ���� ����
		elseif($delivlist_count == $acv[3] || ($delivlist_count == $acv[2] && $delivlist_count == $gds_acv[3])) {
			$Delivlist3 = $Delivlist[goodsdelivprice];
			rsort($Delivlist3);
			$shippingPrice = $Delivlist3[0];
			$shippingType = 'ONDELIVERY';
		}
		//  ���� ������
		elseif($delivlist_count == $acv[4] || ($delivlist_count == $acv[2] && $delivlist_count != $gds_acv[3]) || $delivlist_count == (($delivlist_count == $acv[2] && $delivlist_count != $gds_acv[3])+$acv[4])) {
			$Delivlist5 = $Delivlist[goodsdelivprice];
			rsort($Delivlist5);
			$shippingPrice = $Delivlist5[0];
			$shippingType = 'PAYED';
		}
		elseif($delivlist_count == ($acv[2]+$acv[4]+$acv[3])) {
			$Delivlist4 = array();
			//  ������ �������� ��� ���ұݾ� �հ�
			for($iii=0; $iii<$delivlist_count; $iii++) {
				if($Delivlist[goodsdelivselect][$iii]==2 || $Delivlist[goodsdelivselect][$iii]==4) {
					$Delivlist4[] = $Delivlist[goodsdelivprice][$iii];
				}
			}
			rsort($Delivlist4);
			$shippingPrice = $Delivlist4[0];
			$shippingType = 'PAYED';
		}
		else {
			echo"<script type=\"text/javascript\">
			  window.alert(\"��ۺ� ó���� ������ �߻��Ͽ����ϴ�. �����ڿ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.\");
			  history.go(-1);
			  </script>";
			exit;
		}
	}	
}	// �Ϲݹ�� end

$queryString .= '&SHIPPING_TYPE='.$shippingType;
$queryString .= '&SHIPPING_PRICE='.$shippingPrice;

if($shippingType=='ONDELIVERY') {
  $totalPrice = (int)$totalMoney;
}
else {
  $totalPrice = (int)$totalMoney + (int)$shippingPrice;
}
$queryString .= '&TOTAL_PRICE='.$totalPrice;

//	���̹� ���ϸ��� ���� : 2011-07-07 ������
if($_SESSION["MileageCheck"]=='1') {
	$queryString .= '&NMILEAGE_INFLOW_CODE='.$_SESSION['Ncisy'];
}
else {
	if( $_SERVER['REMOTE_ADDR'] == "111.91.139.55" || $_SERVER['REMOTE_ADDR'] == "58.72.239.56" ) {
		$queryString .= '&NMILEAGE_INFLOW_CODE='.$_SESSION['Ncisy'];
	}
}

// ���̹� ���Է�Ʈ üŷ�� ������ : 2011-08-09 ������
$queryString .= '&SALES_CODE='.$_COOKIE['SALES_CODE'];

$req_addr = 'ssl://checkout.naver.com';
//$req_url = 'POST /customer/api/order.nhn HTTP/1.1'; // utf-8
$req_url = 'POST /customer/api/CP949/order.nhn HTTP/1.1'; // euc-kr
$req_host = 'checkout.naver.com';

/*if($shopId == "pa_brontibay") {
  $req_addr = 'ssl://test-checkout.naver.com';
  $req_host = 'test-checkout.naver.com';
}*/

$req_port = 443;
$nc_sock = fsockopen($req_addr, $req_port, $errno, $errstr);

if ($nc_sock) {
  fwrite($nc_sock, $req_url."\r\n" );
  fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
  //fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
  fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded;charset=CP949\r\n");
  fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
  fwrite($nc_sock, "Accept: */*\r\n");
  fwrite($nc_sock, "\r\n");
  fwrite($nc_sock, $queryString."\r\n");
  fwrite($nc_sock, "\r\n");

  // get header
  while(!feof($nc_sock)){
    $header=fgets($nc_sock,4096);
    if($header=="\r\n"){
    break;
    } else {
    $headers .= $header;
    }
  }
  // get body
  while(!feof($nc_sock)){
    $bodys.=fgets($nc_sock,4096);
  }
  fclose($nc_sock);
  $resultCode = substr($headers,9,3);
  if ($resultCode == 200) {
    // success
    $orderId = $bodys;
  } else {
    // fail
    echo"<script type=\"text/javascript\">
      window.alert(\"���Ž� ������ �߻��Ǿ����ϴ�. �����ڿ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.\");
      history.go(-1);
      </script>";
    exit;
    // üũ�ƿ� ���� ����
    echo "<XMP>".iconv("UTF-8","UHC",$bodys);
    exit;
  }
}
else {
    echo"<script type=\"text/javascript\">
      window.alert(\"���Ž� ������ �߻��Ǿ����ϴ�. �����ڿ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.\");
      history.go(-1);
      </script>";
    exit;
    // ���� ó�� 2010-12-09 ������ ����
    echo "$errstr ($errno)";
    exit;
}

// ���̽� ������ ���� DB������-------------------------------------------------
if(sizeof($PAUTOFCIDS) > 0) {
	$act_info = array();
	for($i=0; $i<sizeof($GoodsCodes); $i++) {
		$act_info['wdate'][]	  = "now()";
		$act_info['code'][]		  = $MALL_MANAGE_CODE;
		$act_info['goods_code'][] = $GoodsCodes[$i];
		$act_info['ip'][]		  = $_SERVER['REMOTE_ADDR'];
		$act_info['cate'][]		  = $pcates[$i];
		$act_info['PAUTOFCID'][]  = $PAUTOFCIDS[$i];
	// loop - End
	}
	// ����
	$re = $this->execute_Sock->Array_insert_multi("ECHost.ActInfo",$act_info,"Success",false);
	if($re != 'Success') { // ���н� �ѹ� ��õ�
		sleep(2);
		$re = $this->execute_Sock->Array_insert_multi("ECHost.ActInfo",$act_info,"Success",false);
	}
}
// ���̽� ������ ���� DB������ ��----------------------------------------------


//���Ϲ��� order_id�� �ֹ��� page�� ȣ���Ѵ�.
//echo ($orderId."<br>\n");

// ���̹� �ֹ��� URL ������ JSON ���� ����
$orderUrl = "https://checkout.naver.com/customer/order.nhn?ORDER_ID=".$orderId."&SHOP_ID=".$shopId."&TOTAL_PRICE=".$totalPrice;

/*if($shopId == "pa_brontibay") {
  $orderUrl = "https://test-checkout.naver.com/customer/order.nhn?ORDER_ID=".$orderId."&SHOP_ID=".$shopId."&TOTAL_PRICE=".$totalPrice;
}*/
Header("Location:$orderUrl");
exit;

$ret = array(
  'RESULT' => true,
  'URL' => $orderUrl,
);
echo json_encode($ret);
exit;


/* ���̹� ��û ��ۺ� ó������ - ������� -_-;
�߿䵵��: ����> ����> ����

�������        SHIPPING_TYPE   SHIPPING_PRICE
����+����       FREE            0��
����+����       PAYED           0�� �̻�
����+����       ONDELIVERY      0�� �̻�
����+����       PAYED           0�� �̻�
����+����+����  PAYED           0�� �̻�
����+����       ONDELIVERY      0�� �̻�
����+����+����  ONDELIVERY      0�� �̻�
����+����       PAYED           0�� �̻�
����+����+����  PAYED           0�� �̻�
*/
?>