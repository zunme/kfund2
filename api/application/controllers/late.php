<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Late extends CI_Controller {
  var $user;
  function _remap($method) {
    if ( ! session_id() ) @ session_start();
    $this->session = $_SESSION;
    $this->member_ck = ( isset($this->session['ss_m_id']) &&  $this->session['ss_m_id'] !='' ) ? true: false;
    if ( $this->member_ck ){
        $this->user = $this->session['ss_m_id'];
    }
    session_write_close();

    if(in_array($method, array('view','write','writedoc','edit','test') ) ) {
        $this->{$method}();
    }else $this->index();
  }
  function test() {
    $this->load->view('testmain');
  }
  function write() {
    if( !$this->member_ck ){
        ?>
        <script>
        location.replace("/pnpinvest/?mode=login")
        </script>
        <?php
    }  
      $this->load->view('late_write');
  }
  function view() {
      $page = (int)$this->input->get('page') > 0 ?(int)$this->input->get('page'):1;
    $row = $this->db->get_where('z_late', array('isview'=>'Y','late_idx'=>(int)$this->input->get('idx') ))->row_array();
    if( !isset($row['late_idx'])) {
      $this->load->view('last_view', array('data'=>array('late_title'=>"없는 페이지 입니다." , 'late_body'=>''),"page"=>$page ));
    }else {
      $this->load->view('late_view', array('data'=>$row,"page"=>$page ));
    }
  }
  function edit() {
    if( !$this->member_ck ){
        ?>
        <script>
        location.replace("/pnpinvest/?mode=login")
        </script>
        <?php
    }  
    $row = $this->db->get_where('z_late', array('isview'=>'Y','late_idx'=>(int)$this->input->get('idx') ))->row_array();
    if( !isset($row['late_idx']) || $row['member_idx'] != $this->user ) {
      ?>
      <script>
      console.log ("<?php echo $row['member_idx']?>");
      console.log ("<?php echo $this->user?>");
      alert("수정권한이 없습니다.");
      location.replace("/");
      </script>
      <?php
    }else {
      $this->load->view('late_edit', array('data'=>$row ));
    }
  }
  function writedoc() {
    if( !$this->member_ck ){
        echo json_encode( array("code"=>500, "msg"=>"로그인 후 사용해주세요" ));//
        return;
    }

    
    $this->load->library('form_validation');

    $this->form_validation->set_rules('latetitle', '제목', 'trim|required|xss_clean');
    $this->form_validation->set_rules('late_body', '내용', 'trim|required');

    $this->form_validation->set_message('required', '%s은(는) 필수입니다.');

    $this->form_validation->set_error_delimiters('', '');
    if ($this->form_validation->run() == FALSE) {
      echo json_encode( array("code"=>500, "msg"=>validation_errors() ));//
      return;
    }

    //preg_match_all("|<p class=\"k-cast-p\">(.*)</p>|U",$this->input->post("cast_body"),$out, PREG_SET_ORDER);
    //$contetnts =  ( isset($out[0][1]) ) ? $out[0][1] : "" ;
    $contetnts =$this->input->post("late_body");
    preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $this->input->post("late_body"), $out);
    $img = ( isset($out[1][0]) ) ? $out[1][0] : "" ;

    $set = array(
        "isview"=> "Y"
        , "late_title"=>$this->input->post("latetitle")
        , "late_img" => $img
        , "late_contents"=>$contetnts
        , "late_body"=>$this->input->post("late_body")
    ) ;

    if( $this->input->post('lateid') > 0  ){
      $qry = $this->db->where('late_idx', $this->input->post('lateid') )->where("member_idx", $this->user) ->get('z_late');
      if ($qry->num_rows() < 1 ){
        echo json_encode( array("code"=>500, "msg"=>"수정권한이 없습니다." ));
        return;
      }else {
        $res = $this->db->where('late_idx', $this->input->post('lateid') )->where("member_idx", $this->user) ->update('z_late', $set);
        $castid = $this->input->post('lateid');
      }
    }else {
      $set["member_idx"] = $this->user;
      $res = $this->db->insert('z_late', $set);
      $castid = $this->db->insert_id();
    }

    if (!$res){
      echo json_encode( array("code"=>500, "msg"=>"저장중 에러발생" ));//
    }else {
      echo json_encode( array("code"=>200, "msg"=>"저장하였습니다.", "castid"=>$castid ));//
    }
  }
  function index() {
    
    $config['base_url'] = '/api/late/';
    $config['uri_segment'] = 2;
    $search = false;

    $config['num_links'] = 2;
    $this->load->library('user_agent');
    if ($this->agent->is_mobile()) {
        $config['per_page'] = 4;
        $perrow = 2;
    }
    else {
        $config['per_page'] = 6;
        $perrow = 3;
    }
    
    
    $config['use_page_numbers'] = TRUE;

    $page = !$this->uri->segment($config['uri_segment']) ? 0 : $this->uri->segment($config['uri_segment']);
    if ($page == 0 ) $config['num_links'] = 4;
    else if( $page < 3) $config['num_links'] = 5 - $page ;
    

    $where['isview'] ='Y';

    $this->db->where($where);
    $config['total_rows'] =  $this->db->count_all_results('z_late') ;
    //$config['total_rows']=30;

    $this->db->where($where);
    
    $list = $this->db->order_by('late_idx desc')->limit($config['per_page'],$page)->get('z_late')->result_array();

    $this->load->library('pagination');


    $config['full_tag_open'] = '';
    $config['full_tag_close'] = '';

    $config['first_link'] = '&lt;&lt;';
    $config['last_link'] = '&gt;&gt;';
    $config['next_link'] = '&gt;';
    $config['prev_link'] = '&lt;';

    $config['cur_tag_open'] = '<li class="active"><a href="javascript">';
    $config['cur_tag_close'] = '</a></li>';

    $config['first_tag_open'] =$config['last_tag_open'] =$config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
    $config['first_tag_close'] =$config['last_tag_close'] =$config['next_tag_close'] = $config['prev_tag_close'] =$config['num_tag_close'] = '</li>';
    $this->pagination->initialize($config);
    $pages =$this->pagination->create_links();


    $this->load->view('late', array("list"=>$list, "pages"=>$pages, "page"=>$page, "perrow"=>$perrow));
  }
}
