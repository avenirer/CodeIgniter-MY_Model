<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
        $this->load->model('user_model');
        $data['user'] = $this->user_model->get(1);
        $data['user_where'] = $this->user_model->where('username','avenirer')->get();
        $data['user_as_array'] = $this->user_model->as_array()->get(1);
        $data['users'] = $this->user_model->get_all();
        $data['users_as_array'] = $this->user_model->as_array()->get_all();
        $data['users_as_dropdown'] = $this->user_model->as_dropdown('username')->get_all();
		$this->load->view('test_view',$data);
	}
}
