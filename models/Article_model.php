<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
class Article_model extends MY_Model
{
	public function __construct()
	{
        $this->table = 'articles';
        $this->primary_key = 'id';
        $this->has_many_pivot['authors'] = array(
            'foreign_model'=>'User_model',
            'pivot_table'=>'articles_users',
            'local_key'=>'id',
            'pivot_local_key'=>'article_id',
            'pivot_foreign_key'=>'user_id',
            'foreign_key'=>'id');
        // $this->has_one['details'] = array('User_details_model','user_id','id');
        // $this->has_one['details'] = array('model'=>'User_details_model','foreign_key'=>'user_id','local_key'=>'id');

		parent::__construct();
	}

    public function insert_dummy()
    {
        $insert_data = array(
            array(
                'title' => 'First article title'
            ),
            array(
                'title' => 'Another article title'
            ),
            array(
                'title' => 'One more article title'
            ),
            array(
                'title' => 'This article has a title too'
            ),
            array(
                'title' => 'How about this article title'
            ),
        );
        $this->db->insert_batch($this->table, $insert_data);
    }
	

}
/* End of file '/User_model.php' */
/* Location: ./application/models//User_model.php */