<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
class User_details_model extends MY_Model
{
	public function __construct()
	{
        $this->table = 'user_details';
        $this->primary_key = 'user_id';
        $this->has_one['user'] = 'User_model';
		parent::__construct();
	}

    public function insert_dummy()
    {
        $insert_data = array(
            array(
                'user_id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'address' => 'no stable address'
            ),
            array(
                'user_id' => 2,
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'address' => 'same as John Doe\'s'
            ),
            array(
                'user_id' => 3,
                'first_name' => 'Adrian',
                'last_name' => 'Voicu',
                'address' => 'Bucharest, Romania'
            ),
            array(
                'user_id' => 4,
                'first_name' => 'Admin',
                'last_name' => 'Istrator',
                'address' => 'over us'
            ),
            array(
                'user_id' => 5,
                'first_name' => 'Whoever',
                'last_name' => 'You want',
                'address' => 'Wherever'
            ),
        );
        $this->db->insert_batch($this->table, $insert_data);
    }
	

}
/* End of file '/User_model.php' */
/* Location: ./application/models//User_model.php */