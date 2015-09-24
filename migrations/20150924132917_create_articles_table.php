<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

// You can find dbforge usage examples here: http://ellislab.com/codeigniter/user-guide/database/forge.html


class Migration_Create_articles_table extends CI_Migration
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
            'title' => array(
                'type'=>'VARCHAR',
                'constraint'=>255
            )
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('articles', TRUE);
        $this->load->model('article_model');
        $this->article_model->insert_dummy();
    }

	public function down()
	{
	    $this->dbforge->drop_table('articles', TRUE);
    }
}
/* End of file '20150924132917_create_comments_table' */
/* Location: ./D:\Dropbox\server\mymodel\application\migrations/20150924132917_create_comments_table.php */
