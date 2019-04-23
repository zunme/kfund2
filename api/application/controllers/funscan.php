<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class funscan extends CI_Controller {
  function index() {
    $idx = $this->input->get('i_id');
    $sql ="
      select * from funscan where productcode = ?
    ";
    $qry = $this->db->query($sql, array($idx) );
    if ( $qry->num_rows() < 1){
      $sql = "
      SELECT
       a.i_subject AS productname
       , a.i_id AS productcode
       , CONCAT(\"https://www.kfunding.co.kr/pnpinvest/?mode=invest_view&loan_id=\",a.i_id) AS url
       , CONCAT(\"https://www.kfunding.co.kr/pnpinvest/data/photoreviewers/\",a.i_id,'/', b.i_creditratingviews) AS image
       , a.i_year_plus as returnrate
       , b.i_invest_sday AS startat
       , a.i_loan_day AS period
       , a.i_loan_pay as amount
       , if(a.i_repay = '원금균등상환' ,3 , if(a.i_repay = '원리금균등상환' ,2 , 1 ) ) repaymenttype
       , case
       	when b.i_look = 'N' then 1
      	when b.i_look = 'Y' then 2
      	when b.i_look = 'C' then 3
      	when b.i_look = 'D' then 4
       	when b.i_look = 'F' then 7
       END AS productstep
       , b.i_view AS publish
      FROM mari_loan a
      JOIN mari_invest_progress b ON a.i_id = b.loan_id
      WHERE a.i_id = ".(int)$idx."
      LIMIT 1
      ";
      $data = $this->db->query($sql )->row_array();
      $data['mode']="ready";
      $this->db->insert('funscan', $data);
      $data['mode']="regist";
      $data['reward']="";
    }else {
      $data = $qry->row_array();
      $data['mode']=($data['mode']=="ready")?"regist":"modify";
    }
    $this->load->view('funscan', array("data"=>$data));
  }
  function fncall()
  {
    $url = "http://dev.funscan.co.kr/api/api_product.php";
    $result = $this->callAPI($url,"GET", $_POST);
    $result = preg_replace('/\r\n|\r|\n/','',$result);
    $temp = explode(":", $result);
    if( $temp[0]=="SUCCESS"){
      $data = $_POST;
      $data['mode']="regist";
      $idx = (int)$data['productcode'];
      unset($data['productcode']);unset($data['apikey']);
      $this->db->where('productcode', $idx )->update('funscan', $data);
      echo json_encode(array("code"=>200));
    }else json_encode(array("code"=>500, "msg"=>$result));
  }
  function callAPI($url,$method="GET", $data=""){
    $curl = curl_init();
    #curl_setopt( $curl, CURLOPT_USERAGENT, $userAgent );
    #curl_setopt( $curl, CURLOPT_REFERER,$referer);
    switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if (is_array($data) || $data!="" )
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if (is_array($data) || $data!="" )
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      default:
         if (is_array($data) || $data!="" ) $url = sprintf("%s?%s", $url,http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, array('APIKEY: 111111111111111111111','Content-Type: application/json',));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){return false;}
    curl_close($curl);
    return $result;
  }
}
