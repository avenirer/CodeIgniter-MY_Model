<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends MY_Model
{
	public function __construct()
	{
        $this->table = 'users';
        $this->primary_key = 'id';
        //$this->has_one['details'] = 'User_details_model';
        // $this->has_one['details'] = array('User_details_model','user_id','id');
        $this->has_one['details'] = array('local_key'=>'id', 'foreign_key'=>'user_id', 'foreign_model'=>'User_details_model');
        $this->has_many['posts'] = 'Post_model';

		parent::__construct();
	}

    public function insert_dummy()
    {
        $insert_data = array(
            array(
                'username' => 'user1',
                'password' => 'mypass',
                'email' => 'user1@user.com'
            ),
            array(
                'username' => 'user2',
                'password' => 'nopass',
                'email' => 'user2@user.com'
            ),
            array(
                'username' => 'avenirer',
                'password' => 'nopass',
                'email' => 'user3@user.com'
            ),
            array(
                'username' => 'administrator',
                'password' => 'mypass',
                'email' => 'user4@user.com'
            ),
            array(
                'username' => 'user5',
                'password' => 'nopass',
                'email' => 'user5@user.com'
            ),
        );
        $this->db->insert_batch($this->table, $insert_data);
    }
	

}
/* End of file '/User_model.php' */
/* Location: ./application/models//User_model.php */