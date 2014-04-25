<?php

class MY_Model extends CI_Model
{
	protected $_table;
	protected $_primary = 'id';
	protected $_timestamps = TRUE;
	protected $_timestamp_format = 'datetime'; // format can be 'datetime','date','timestamp'
	protected $_created_col = 'created_at';
	protected $_updated_col = 'updated_at';
	private $_time;
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
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
		if(isset($where_arr))
		{
			$this->db->where($where_arr);
		}
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
	
	/**
	 * Retrieve one record from DB
	 * @param int|array $where_arr_var
	 * @param str $select_str
	 * @return object
	*/
	public function get($where_arr_var = NULL,$select = NULL)
	{
		if(isset($where_arr_var))
		{
			if(is_array($where_arr_var))
			{
				$this->db->where($where_arr);
			}
			else
			{
				$this->db->where(array($this->_primary => $where_arr_var));
			}
		}
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
	 * Update record(s)
	 * @param array $columns_arr
	 * @param array $where_arr
	 * @return integer affected rows
	 */
	public function update($columns_arr, $where_arr = NULL)
	{
		if(isset($where_arr))
		{
			$this->db->where($where_arr);
		}
		if($this->_timestamps==TRUE && !array_key_exists($this->_updated_col, $columns_arr))
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
	 * Delete row(s)
	 * @param array $where_arr
	 * @return integer affected rows
	 */
	public function delete($where_arr_var = NULL)
	{
		if(isset($where_arr))
		{
			if(is_array($where_arr_var))
			{
				$this->db->where($where_arr);
			}
			else
			{
				$this->db->where(array($this->_primary => $where_arr_var));
			}
			$this->db->delete($this->_table);
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

}
