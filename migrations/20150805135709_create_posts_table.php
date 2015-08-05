<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

// You can find dbforge usage examples here: http://ellislab.com/codeigniter/user-guide/database/forge.html


class Migration_Create_posts_table extends CI_Migration
{
    public function __construct()
	{
	    parent::__construct();
		$this->load->dbforge();
	}

	public function up()
	{
	    $fields = array(
            'id' => array(
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type'=> 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'title' => array(
                'type'=> 'VARCHAR',
                'constraint' => 255
            ),
            'content' => array(
                'type'=> 'TEXT'
            )
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->create_table('posts', TRUE);
        $this->load->model('post_model');
        $this->post_model->insert_dummy();
    }

	public function down()
	{
	    $this->dbforge->drop_table('posts', TRUE);
    }
}
/* End of file '20150805135709_create_posts_table' */
/* Location: ./D:\Dropbox\server\mymodel\application\migrations/20150805135709_create_posts_table.php */
