<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class funscan extends CI_Controller {
  function index() {
    $idx = $this->input->get('i_id');

    $this->load->view('funscan');
  }
}
