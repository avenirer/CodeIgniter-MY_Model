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
        $this->output->enable_profiler(TRUE);
        $this->load->model('user_model');
        $this->load->model('article_model');
        $data['user'] = $this->user_model->get(1);
        $data['user_with'] = $this->user_model->with_details('fields:first_name,last_name')->get(1);
        $data['user_with_count'] = $this->user_model->with_details('fields:*count*')->get(1);
        $data['user_where'] = $this->user_model->where('username','avenirer')->get();
        $data['user_where_pass'] = $this->user_model->where(array('username'=>'administrator','password'=>'mypass'))->get();
        $data['user_as_array'] = $this->user_model->as_array()->get(1);
        $data['users'] = $this->user_model->get_all();
        $data['users_with'] = $this->user_model->with_details('fields:first_name,last_name,address')->get_all();
        $data['users_with_count'] = $this->user_model->with_details('fields:*count*')->get_all();
        $data['users_with_count_many'] = $this->user_model->with_posts('fields:*count*')->get_all();
        $data['users_with_and_where'] = $this->user_model->with_details('fields:first_name,last_name,address','where:`user_details`.`first_name`=\'Admin\'')->get_all();
        $data['users_with_and_non_exclusive_where'] = $this->user_model->with_details('fields:first_name,last_name,address|non_exclusive_where:`user_details`.`first_name`=\'Admin\'')->get_all();
        $data['users_where_pass'] = $this->user_model->where(array('password'=>'nopass'))->get_all();
        $data['users_as_array'] = $this->user_model->as_array()->get_all();
        $data['users_as_dropdown'] = $this->user_model->as_dropdown('username')->get_all();
        $data['articles_with_authors'] = $this->article_model->with_authors('fields:username')->get_all();
		$this->load->view('test_view',$data);
	}
}
