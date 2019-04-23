<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class funscan extends CI_Controller {
  function index() {
    $idx = $this->input->get('i_id');
    $sql = "
    SELECT
     a.i_subject AS productname
     , a.i_id AS productcode
     , CONCAT('https://www.kfunding.co.kr/pnpinvest/?mode=invest_view&loan_id=',a.i_id) AS url
     , CONCAT('https://www.kfunding.co.kr/pnpinvest/data/photoreviewers/',a.i_id,'/', b.i_creditratingviews) AS image
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
    WHERE a.i_id = ?
    LIMIT 1
    ";
    $data = $this->db->query($sql, array($idx ) )->row_array()
    $this->load->view('funscan', array("data"=>$data));
  }
}
