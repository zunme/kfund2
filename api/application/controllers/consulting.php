<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//define("PluginPath",'/home/pnpinvest/www/pnpinvest/plugin/pg/seyfert/aes.class.php');
//define("PluginPath",'/var/www/html/pnpinvest/plugin/pg/seyfert/aes.class.php');
define("PluginPath",'../pnpinvest/plugin/pg/seyfert/aes.class.php');

class Consulting extends CI_Controller {
  function index(){
    if ( ! session_id() ) @ session_start();
    date_default_timezone_set('Asia/Seoul');
    if(isset($_SESSION['ss_m_id'])) $m_id = $_SESSION['ss_m_id'];
    else $m_id ='';
    $this->load->view("consulting");
  }
  function save(){
    if($this->input->post("agreement") != 'Y'){
      return;
      echo json_encode(array('code'=>500, "msg"=>"개인정보 수집 및 이용에 동의해주세요"));
    }
    $this->load->helper('security');
    $data ['company'] = xss_clean($this->input->post("company_name"));
    $data ['name'] = xss_clean($this->input->post("manager_name"));
    $data ['tel'] = xss_clean($this->input->post("manager_tel"));
    $data ['exp'] =xss_clean($this->input->post("inv_exp"));

    if( $this->db->insert('z_counsult', $data) ) {
      echo json_encode(array('code'=>200, "msg"=>""));
      $to = array('filter'=>array( 'key'=>'consult', 'value'=>'on'));
      $msg= array("send_type"=>"SMS", "to"=>$to, "msg_type"=>"투자상담", "msg"=>"투자상담 신청이 들어왔습니다.");
      $this->load->helper('rabbit');
      sendrabbit($msg);
    }else {
      echo json_encode(array('code'=>500, "msg"=>"오류가 발생하였습니다. 잠시후에 시도해주세요"));
    }

  }
  function admviewlist() {
    $list = $this->db->query("select * from z_counsult order by idx desc")->result_array();
    $this->load->view("consulting_list", array("list"=>$list));
  }
  function toplist() {
    $sql = "select b.m_id, a.total, m_name, m_hp from
    (select m_id , sum(i_pay) as total
    from mari_invest
    group by m_id
    order by sum(i_pay) desc) a
    join mari_member b on a.m_id = b.m_id;";
    $list = $this->db->query($sql)->result_array();
    $this->load->view("top_list", array("list"=>$list));
  }
}
