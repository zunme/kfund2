<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Alimlist extends CI_Controller {
  function index(){
    $data['list'] = $this->db->query("select * from z_alimitok where code='J0001' order by regdate desc")->result_array();
    $this->load->view('alim',$data);
  }
}
