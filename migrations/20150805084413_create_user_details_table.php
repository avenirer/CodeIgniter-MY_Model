<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

// You can find dbforge usage examples here: http://ellislab.com/codeigniter/user-guide/database/forge.html


class Migration_Create_user_details_table extends CI_Migration
{
    public function __construct()
	{
	    parent::__construct();
		$this->load->dbforge();
	}

	public function up()
	{
	    $fields = array(
            'user_id' => array(
                'type' => 'INT',
                'constraint'=>11,
                'unsigned'=>TRUE
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
            'address' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
            'created_at' => array(
                'type'=>'DATETIME',
                'NULL'=>TRUE,
            ),
            'created_by'=> array(
                'type'=>'INT',
                'unsigned'=>TRUE,
                'NULL'=>TRUE,
            ),
            'updated_at' => array(
                'type'=>'DATETIME',
                'NULL'=>TRUE,
            ),
            'updated_by'=> array(
                'type'=>'INT',
                'unsigned'=>TRUE,
                'NULL'=>TRUE,
            ),
            'deleted_at' => array(
                'type'=>'DATETIME',
                'NULL'=>TRUE,
            ),
            'deleted_by'=> array(
                'type'=>'INT',
                'unsigned'=>TRUE,
                'NULL'=>TRUE,
            )
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('user_id',TRUE);
        $this->dbforge->create_table('user_details', TRUE);
        $this->load->model('user_details_model');
        $this->user_details_model->insert_dummy();
    }

	public function down()
	{
	    $this->dbforge->drop_table('user_details', TRUE);
    }
}
/* End of file '20150805084413_create_user_details_table' */
/* Location: ./D:\Dropbox\server\mymodel\application\migrations/20150805084413_create_user_details_table.php */
