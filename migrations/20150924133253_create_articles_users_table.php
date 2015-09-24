<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

// You can find dbforge usage examples here: http://ellislab.com/codeigniter/user-guide/database/forge.html


class Migration_Create_articles_users_table extends CI_Migration
{
    public function __construct()
	{
	    parent::__construct();
		$this->load->dbforge();
	}

	public function up()
	{
	    $fields = array(
            'article_id' => array(
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>TRUE
            ),
            'user_id' => array(
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>TRUE
            )
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('articles_users', TRUE);
    }

	public function down()
	{
	    $this->dbforge->drop_table('articles_users', TRUE);
    }
}
/* End of file '20150924133253_create_articles_users_table' */
/* Location: ./D:\Dropbox\server\mymodel\application\migrations/20150924133253_create_articles_users_table.php */
