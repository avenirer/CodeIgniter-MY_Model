<?php
/** how to extend MY_Model:
 *	class User_model extends MY_Model
 *	{
 * 		public function __construct()
 * 		{
 * 			$this->_table ='users'; // OPTIONAL (default: plural of model name) - if the table name is the plural of the model name (model name: User_model; table: users) this line is optional
 * 			$this->_primary = 'id'; // OPTIONAL (default: id) - the primary key
 * 			$this->_timestamps = TRUE; // OPTIONAL (default: TRUE) - allow for the use of timestamps for creation and update
 * 			$this->_timestamp_format = 'datetime'; // OPTIONAL (default: datetime) - format can be 'datetime','date','timestamp'
 * 			$this->_created_col = 'created_at'; // OPTIONAL (default: created_at) - the name of the column for creation time
 * 			$this->_updated_col = 'updated_at'; // OPTIONAL (default: updated_at) - the name of the column for update time
 * 			$this->_soft_delete = TRUE; // OPTIONAL (default: TRUE) - allow for soft deletes?
 * 			$this->_soft_delete_col = 'status'; //OPTIONAL (default:status) - what column will be used to allow for soft delete
 * 			$this->_soft_delete_values = array(0=>'inactive', 1=>'active'); // OPTIONAL - what values will the column take for soft delete (0) and removal of soft delete(1)
 * 			parent::__construct();
 * 		}
 * 	}
 * 
 **/
class MY_Model extends CI_Model
{
	protected $_table;
	protected $_primary = 'id';
	protected $_timestamps = TRUE;
	protected $_timestamp_format = 'datetime'; // format can be 'datetime','date','timestamp'
	protected $_created_col = 'created_at';
	protected $_updated_col = 'updated_at';
	private $_time;
	protected $_soft_delete = TRUE;
	protected $_soft_delete_col = 'status';
	protected $_soft_delete_values = array(0=>'inactive',1=>'active');
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->_fetch_table();
		switch ($this->_timestamp_format)
		{
			case 'datetime':
				$this->_time = date('Y-m-d H:i:s');			
			break;
			case 'date':
				$this->_time = date('Y-m-d');			
			break;
			case 'timestamp':
				$this->_time = time();
			break;
		}
	}
	/** retrieve all records from DB
	 * @param array $where_arr
	 * @param var|array $order_by_var_arr
	 * @param var $select
	 * @return object
	**/
	public function get_all($where_arr = NULL, $order_by_var_arr = NULL, $select = NULL)
	{
		$this->_where($where_arr);
		if(isset($order_by_var_arr))
		{
			if(!is_array($order_by_var_arr))
			{
				$order_by[0] = $order_by_var_arr;
				$order_by[1] = 'asc';
			}
			else
			{
				$order_by[0] = $order_by_var_arr[0];
				$order_by[1] = $order_by_var_arr[1];
			}
			$this->db->order_by($order_by[0],$order_by[1]);
		}
		if(isset($select))
		{
			$this->db->select($select);
		}
		$query = $this->db->get($this->_table);
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
	}
	
	/** retrieve number of records from DB
	 * @param array $where_arr
	 * @return int
	**/
	public function get_count($where_arr = NULL)
	{
		$this->db->where($where_arr);
		return $this->db->count_all_results($this->_table);
	}
	
	/**
	 * Retrieve one record from DB
	 * @param int|array $where_arr_var
	 * @param str $select_str
	 * @return object
	*/
	public function get($where_arr_var = NULL,$select = NULL)
	{
		$this->_where($where_arr_var);
		if(isset($select))
		{
			$this->db->select($select);
		}
		$this->db->limit(1);
		$query = $this->db->get($this->_table);
		if($query->num_rows()>0)
		{
			return $query->row();
		}
		else
		{
			return FALSE;
		}
	}
	/**
	 * Insert a record into DB
	 * @param type $columns_arr
	 * @return int insert id
	 */
	public function insert($columns_arr)
	{
		if(is_array($columns_arr))
		{
			if($this->_timestamps==TRUE && !array_key_exists($this->_created_col, $columns_arr))
			{
				$columns_arr[$this->_created_col]= $this->_time;
			}
			if($this->db->insert($this->_table,$columns_arr))
			{
				return $this->db->insert_id();
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Update record
	 * @param array $columns_arr
	 * @param array/var $where_arr_var
	 * @return integer affected rows
	 */
	public function update($columns_arr, $where_arr_var = NULL)
	{
		$this->_where($where_arr_var);
		if($this->_timestamps && !array_key_exists($this->_updated_col, $columns_arr))
		{
			$columns_arr[$this->_updated_col]= $this->_time;
		}
		$this->db->update($this->_table,$columns_arr);
		if($this->db->affected_rows()>0)
		{
			return $this->db->affected_rows();
		}
		else
		{
			return FALSE;
		}
	}
	/**
	 * Delete row(s) - it does a soft delete or a raw delete depending on the $_soft_delete values
	 * @param array $where_arr
	 * @return integer affected rows
	 */
	public function delete($where_arr_var = NULL)
	{
		if(isset($where_arr_var))
		{
			$this->_where($where_arr_var);
			if($this->_soft_delete===TRUE)
			{
				$this->db->update($this->_table, array($this->_soft_delete_col=>$this->_soft_delete_values[0]));
			}
			else
			{
				$this->db->delete($this->_table);
			}
			return $this->db->affected_rows();
		}
		else
		{
			return FALSE;
		}

	}
	/**
	 * Undelete row(s) - it does a soft undelete or a raw delete depending on the $_soft_delete values
	 * @param array $where_arr_var
	 * @return integer affected rows
	 */
	public function undelete($where_arr_var = NULL)
	{
		if($this->_soft_delete && isset($where_arr_var))
		{
			$this->_where($where_arr_var);
			$this->db->update($this->_table, array($this->_soft_delete_col=>$this->_soft_delete_values[1]));
			return $this->db->affected_rows();
			
		}
		else
		{
			return FALSE;
		}
	}
	
	
	
	/**
	 * hash a string
	 * @param string $string
	 * @return string 128 characters long hash of $string
	 */
	public function hash($string)
	{
		return hash('sha512', $string, config_item('encryption_key'));
	}
	private function _fetch_table()
	{
		if ($this->_table == NULL)
	        {
	        	$this->load->helper('inflector');
	        	$this->_table = plural(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
	        }
	}
	private function _where($where_arr_var = NULL)
	{
		if(isset($where_arr_var))
		{
			if(is_array($where_arr_var))
			{
				 return $this->db->where($where_arr_var);
			}
			else
			{
				return $this->db->where(array($this->_primary => $where_arr_var));
			}
		}
		return;
	}

}
