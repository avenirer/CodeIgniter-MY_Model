<?php defined('BASEPATH') OR exit('No direct script access allowed');

/** how to extend MY_Model:
 *	class User_model extends MY_Model
 *	{
 *      public $table = 'users'; // Set the name of the table for this model.
 *      public $primary_key = 'id'; // Set the primary key
 *      public $fillable = array(); // You can set an array with the fields that can be filled by insert/update
 *      public $protected = array(); // ...Or you can set an array with the fields that cannot be filled by insert/update
 * 		public function __construct()
 * 		{
 *          $this->_database_connection  = group_name or array() | OPTIONAL
 *              Sets the connection preferences (group name) set up in the database.php. If not trset, it will use the
 *              'default' (the $active_group) database connection.
 *          $this->timestamps = TRUE | array('made_at','modified_at','removed_at')
 *              If set to TRUE tells MY_Model that the table has 'created_at','updated_at' (and 'deleted_at' if $this->soft_delete is set to TRUE)
 *              If given an array as parameter, it tells MY_Model, that the first element is a created_at field type, the second element is a updated_at field type (and the third element is a deleted_at field type)
 *          $this->soft_deletes = FALSE
 *              Enables (TRUE) or disables (FALSE) the "soft delete" on records. Default is FALSE
 *          $this->return_as = 'object' | 'array'
 *              Allows the model to return the results as object or as array
 *          $this->has_one['phone'] = 'Phone_model' or $this->has_one['phone'] = array('Phone_model','foreign_key','local_key');
 *          $this->has_one['address'] = 'Address_model' or $this->has_one['address'] = array('Address_model','foreign_key','another_local_key');
 *              Allows establishing ONE TO ONE or more ONE TO ONE relationship(s) between models/tables
 *          $this->has_many['posts'] = 'Post_model' or $this->has_many['posts'] = array('Posts_model','foreign_key','another_local_key');
 *              Allows establishing ONE TO MANY or more ONE TO MANY relationship(s) between models/tables
 *          $this->has_many_pivot['posts'] = 'Post_model' or $this->has_many_pivot['posts'] = array('Posts_model','foreign_primary_key','local_primary_key');
 *              Allows establishing MANY TO MANY or more MANY TO MANY relationship(s) between models/tables with the use of a PIVOT TABLE
 *              !ATTENTION: The pivot table name must be composed of the two table names separated by "_" the table names having to to be alphabetically ordered (NOT users_posts, but posts_users).
 *                  Also the pivot table must contain as identifying columns the columns named by convention as follows: table_name_singular + _ + foreign_table_primary_key.
 *                  For example: considering that a post can have multiple authors, a pivot table that connects two tables (users and posts) must be named posts_users and must have post_id and user_id as identifying columns for the posts.id and users.id tables.
 *          $this->cache_driver = 'file'
 *          $this->cache_prefix = 'mm'
 *              If you know you will do some caching of results without the native caching solution, you can at any time use the MY_Model's caching.
 *              By default, MY_Model uses the files to cache result.
 *              If you want to change the way it stores the cache, you can change the $cache_driver property to whatever CodeIgniter cache driver you want to use.
 *              Also, with $cache_prefix, you can prefix the name of the caches. by default any cache made by MY_Model starts with 'mm' + _ + "name chosen for cache"
 *          $this->pagination_delimiters = array('<span>','</span>');
 *              If you know you will use the paginate() method, you can change the delimiters between the pages links
 *          $this->pagination_arrows = array('&lt;','&gt;');
 *              You can also change the way the previous and next arrows look like.
 *
 *
 * 			parent::__construct();
 * 		}
 * 	}
 *
 **/

class MY_Model extends CI_Model
{

    /**
     * Select the database connection from the group names defined inside the database.php configuration file or an
     * array.
     */
    protected $_database_connection = NULL;

    /** @var
     * This one will hold the database connection object
     */
    protected $_database;

    /** @var null
     * Sets table name
     */
    public $table = NULL;

    /**
     * @var null
     * Sets PRIMARY KEY
     */
    public $primary_key = NULL;

    /**
     * @var array
     * You can establish the fields of the table. If you won't these fields will be filled by MY_Model (with one query)
     */
    public $table_fields = array();

    /**
     * @var array
     * Sets fillable fields
     */
    public $fillable = array();

    /**
     * @var array
     * Sets protected fields
     */
    public $protected = array();

    private $_can_be_filled = NULL;


    /** @var bool | array
     * Enables created_at and updated_at fields
     */
    protected $timestamps = TRUE;

    protected $_created_at_field;
    protected $_updated_at_field;
    protected $_deleted_at_field;

    /** @var bool
     * Enables soft_deletes
     */
    protected $soft_deletes = FALSE;

    /** relationships variables */
    private $_relationships = array();
    public $has_one = array();
    public $has_many = array();
    public $has_many_through = array();
    public $has_many_pivot = array();
    public $separate_subqueries = TRUE;
    private $_requested = array();
    /** end relationships variables */

    /*caching*/
    public $cache_driver = 'file';
    public $cache_prefix = 'mm';
    protected $_cache = array();

    /*pagination*/
    public $next_page;
    public $previous_page;
    public $all_pages;
    public $pagination_delimiters;
    public $pagination_arrows;


    /**
     * The various callbacks available to the model. Each are
     * simple lists of method names (methods will be run on $this).
     */
    protected $before_create = array();
    protected $after_create = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_get = array();
    protected $after_get = array();
    protected $before_delete = array();
    protected $after_delete = array();
    protected $before_soft_delete = array();
    protected $after_soft_delete = array();

    protected $callback_parameters = array();

    protected $return_as = 'object';

    private $_trashed = 'without';


    public function __construct()
    {
        parent::__construct();
        $this->load->helper('inflector');
        $this->_set_connection();
        $this->_set_timestamps();
        $this->before_soft_delete[] = 'add_deleted';
        $this->pagination_delimiters = (isset($this->pagination_delimiters)) ? $this->pagination_delimiters : array('<span>','</span>');
        $this->pagination_arrows = (isset($this->pagination_arrows)) ? $this->pagination_arrows : array('&lt;','&gt;');
    }

    public function _get_table_fields()
    {
        if(empty($this->table_fields))
        {
            $this->table_fields = $this->_database->list_fields($this->table);
        }
        return TRUE;
    }

    public function fillable_fields()
    {
        if(!isset($this->_can_be_filled))
        {
            $this->_get_table_fields();
            $no_protection = array();
            foreach ($this->table_fields as $field) {
                if (!in_array($field, $this->protected)) {
                    $no_protection[] = $field;
                }
            }
            if (!empty($this->fillable)) {
                $can_fill = array();
                foreach ($this->fillable as $field) {
                    if (in_array($field, $no_protection)) {
                        $can_fill[] = $field;
                    }
                }
                $this->_can_be_filled = $can_fill;
            } else {
                $this->_can_be_filled = $no_protection;
            }
        }
        return TRUE;
    }

    public function _prep_before_write($data)
    {
        $this->fillable_fields();
        // We make sure we have the fields that can be filled
        $can_fill = $this->_can_be_filled;

        // Let's make sure we receive an array...
        $data_as_array = (is_object($data)) ? (array)$data : $data;

        $new_data = array();
        foreach($data_as_array as $field => $value)
        {
            if(in_array($field,$can_fill))
            {
                $new_data[$field] = $value;
            }
        }
        return $new_data;
    }

    public function _prep_after_read($data, $multi = TRUE)
    {
        if($multi === TRUE && sizeof($data)==1)
        {
            $new_data[] = $data;
        }
        else
        {
            $new_data = $data;
        }
        if(!empty($this->_requested))
        {
            $new_data = $this->_get_requested($new_data);
        }
        if($this->return_as == 'object')
        {
            $object = json_decode(json_encode($new_data), FALSE);
            return $object;
        }
        return $new_data;
        //unset($this);
    }

    protected function _get_requested($data)
    {
        foreach($this->_requested as $relation => $requested)
        {
            echo '<pre>';
            print_r($this->_relationships[$relation]);
            echo '</pre>';
        }

        print_r($data);


        exit;
    }

    /**
     * public function insert($data)
     * Inserts data into table. Can receive an array or a multidimensional array depending on what kind of insert we're talking about.
     * @param $data
     * @return int/array Returns id/ids of inserted rows
     */
    public function insert($data)
    {
        $data = $this->_prep_before_write($data);

        //now let's see if the array is a multidimensional one (multiple rows insert)
        $multi = FALSE;
        foreach($data as $element)
        {
            $multi = (is_array($element)) ? TRUE : FALSE;
        }

        // if the array is not a multidimensional one...
        if($multi === FALSE)
        {
            if($this->timestamps === TRUE || is_array($this->timestamps))
            {
                $data[$this->_created_at_field] = date('Y-m-d H:i:s');
            }
            $data = $this->trigger('before_create',$data);
            if($this->_database->insert($this->table, $data))
            {
                $id = $this->_database->insert_id();
                $return = $this->trigger('after_create',$id);
                return $return;
            }
            return FALSE;
        }
        // else...
        else
        {
            $return = array();
            foreach($data as $row)
            {
                if($this->timestamps === TRUE || is_array($this->timestamps))
                {
                    $row[$this->_created_at_field] = date('Y-m-d H:i:s');
                }
                $row = $this->trigger('before_create',$row);
                if($this->_database->insert($this->table,$row))
                {
                    $return[] = $this->_database->insert_id();
                }
            }
            $after_create = array();
            foreach($return as $id)
            {
                $after_create[] = $this->trigger('after_create', $id);
            }
            return $after_create;
        }
        return FALSE;
    }


    /**
     * public function update($data)
     * Updates data into table. Can receive an array or a multidimensional array depending on what kind of update we're talking about.
     * @param $data
     * @param $column_name_where
     * @return str/array Returns id/ids of inserted rows
     */
    public function update($data, $column_name_where = NULL)
    {
        // Prepare the data...
        $data = $this->_prep_before_write($data);

        //now let's see if the array is a multidimensional one (multiple rows insert)
        $multi = FALSE;
        foreach($data as $element)
        {
            $multi = (is_array($element)) ? TRUE : FALSE;
        }

        // if the array is not a multidimensional one...
        if($multi === FALSE)
        {
            if($this->timestamps === TRUE || is_array($this->timestamps))
            {
                $data[$this->_updated_at_field] = date('Y-m-d H:i:s');
            }
            $data = $this->trigger('before_update',$data);
            if(isset($column_name_where))
            {
                if (is_array($column_name_where))
                {
                    $this->where($column_name_where);
                } elseif (is_numeric($column_name_where)) {
                    $this->_database->where($this->primary_key, $column_name_where);
                } else {
                    $column_value = (is_object($data)) ? $data->{$column_name_where} : $data[$column_name_where];
                    $this->_database->where($column_name_where, $column_value);
                }
            }
            if($this->_database->update($this->table, $data))
            {
                $affected = $this->_database->affected_rows();
                $return = $this->trigger('after_update',$affected);
                return $return;
            }
            return FALSE;
        }
        // else...
        else
        {
            $rows = 0;
            foreach($data as $row)
            {
                if($this->timestamps === TRUE || is_array($this->timestamps))
                {
                    $row[$this->_updated_at_field] = date('Y-m-d H:i:s');
                }
                $row = $this->trigger('before_update',$row);
                if(is_array($column_name_where))
                {
                    $this->_database->where($column_name_where[0], $column_name_where[1]);
                }
                else
                {
                    $column_value = (is_object($row)) ? $row->{$column_name_where} : $row[$column_name_where];
                    $this->_database->where($column_name_where, $column_value);
                }
                if($this->_database->update($this->table,$row))
                {
                    $rows++;
                }
            }
            $affected = $rows;
            $return = $this->trigger('after_update',$affected);
            return $return;
        }
        return FALSE;
    }

    /**
     * public function where($field_or_array = NULL, $operator_or_value = NULL, $value = NULL, $with_or = FALSE, $with_not = FALSE, $custom_string = FALSE)
     * Sets a where method for the $this object
     * @param null $field_or_array - can receive a field name or an array with more wheres...
     * @param null $operator_or_value - can receive a database operator or, if it has a field, the value to equal with
     * @param null $value - a value if it received a field name and an operator
     * @param bool $with_or - if set to true will create a or_where query type pr a or_like query type, depending on the operator
     * @param bool $with_not - if set to true will also add "NOT" in the where
     * @param bool $custom_string - if set to true, will simply assume that $field_or_array is actually a string and pass it to the where query
     * @return $this
     */
    public function where($field_or_array = NULL, $operator_or_value = NULL, $value = NULL, $with_or = FALSE, $with_not = FALSE, $custom_string = FALSE)
    {
        if(is_array($field_or_array))
        {
            $multi = FALSE;
            foreach($field_or_array as $element) {
                $multi = (is_array($element)) ? TRUE : FALSE;
            }
            if($multi === TRUE)
            {
                foreach ($field_or_array as $where)
                {
                    $field = $where[0];
                    $operator_or_value = isset($where[1]) ? $where[1] : NULL;
                    $value = isset($where[2]) ? $where[2] : NULL;
                    $with_or = (isset($where[3])) ? TRUE : FALSE;
                    $with_not = (isset($where[4])) ? TRUE : FALSE;
                    $this->where($field, $operator_or_value, $value, $with_or,$with_not);
                }
            }
        }

        if($with_or == TRUE)
        {
            $where_or = 'or_where';
        }
        else
        {
            $where_or = 'where';
        }

        if($with_not == TRUE)
        {
            $not = '_not';
        }
        else
        {
            $not = '';
        }

        if($custom_string === TRUE)
        {
            $this->_database->{$where_or}($field_or_array, NULL, FALSE);
        }
        elseif(is_numeric($field_or_array))
        {
            $this->_database->{$where_or}(array($this->table.'.'.$this->primary_key => $field_or_array));
        }
        elseif(is_array($field_or_array) && !isset($operator_or_value))
        {
            $this->_database->where($field_or_array);
        }
        elseif(!isset($value) && isset($field_or_array) && isset($operator_or_value) && !is_array($operator_or_value))
        {
            $this->_database->{$where_or}(array($this->table.'.'.$field_or_array => $operator_or_value));
        }
        elseif(!isset($value) && isset($field_or_array) && isset($operator_or_value) && is_array($operator_or_value))
        {
            $this->_database->{$where_or.$not.'_in'}(array($this->table.'.'.$field_or_array => $operator_or_value));
        }
        elseif(isset($field_or_array) && isset($operator_or_value) && isset($value))
        {
            if(strtolower($operator_or_value) == 'like') {
                if($with_not === TRUE)
                {
                    $like = 'not_like';
                }
                else
                {
                    $like = 'like';
                }
                if ($with_or === TRUE)
                {
                    $like = 'or_'.$like;
                }

                $this->_database->{$like}($field_or_array, $value);
            }
            else
            {
                $this->_database->{$where_or}($field_or_array.' '.$operator_or_value, $value);
            }

        }

        if($this->soft_deletes===TRUE)
        {
            $this->_where_trashed();
        }
        return $this;
    }

    /**
     * public function limit($limit, $offset = 0)
     * Sets a rows limit to the query
     * @param $limit
     * @param int $offset
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        $this->_database->limit($limit, $offset);
        return $this;
    }

    /**
     * public function delete($where)
     * Deletes data from table.
     * @param $where
     * @return Returns affected rows or false on failure
     */
    public function delete($where = NULL)
    {
        $this->where($where);
        $affected_rows = 0;
        if($this->soft_deletes === TRUE)
        {
            $query = $this->_database->get($this->table);

            foreach($query->result() as $row)
            {
                $to_update[] = array($this->primary_key => $row->{$this->primary_key});
            }
            if(isset($to_update))
            {
                foreach($to_update as &$row)
                {
                    $row = $this->trigger('before_soft_delete',$row);
                }
                $affected_rows = $this->update($to_update, $this->primary_key);

                $this->trigger('after_soft_delete',$to_update);
            }
            return $affected_rows;
        }
        else
        {
            if($this->_database->delete($this->table))
            {
                return $this->_database->affected_rows();
            }
        }
        return FALSE;
    }

    /**
     * public function force_delete($where = NULL)
     * Forces the delete of a row if soft_deletes is enabled
     * @param null $where
     * @return bool
     */
    public function force_delete($where = NULL)
    {
        $this->where($where);
        if($this->_database->delete($this->table))
        {
            return $this->_database->affected_rows();
        }
        return FALSE;
    }

    /**
     * public function restore($where = NULL)
     * "Un-deletes" a row
     * @param null $where
     * @return bool
     */
    public function restore($where = NULL)
    {
        $this->with_trashed();
        $this->where($where);
        if($affected_rows = $this->_database->update($this->table,array($this->_deleted_at_field=>NULL)))
        {
            return $affected_rows;
        }
        return FALSE;
    }

    /**
     * public function trashed($where = NULL)
     * Verifies if a record (row) is soft_deleted or not
     * @param null $where
     * @return bool
     */
    public function trashed($where = NULL)
    {
        $this->only_trashed();
        $this->where($where);
        $this->limit(1);
        $query = $this->_database->get($this->table);
        if($query->num_rows() == 1)
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * public function get()
     * Retrieves one row from table.
     * @param null $where
     * @return mixed
     */
    public function get($where = NULL)
    {
        if(isset($this->_cache) && !empty($this->_cache))
        {
            $this->load->driver('cache');
            $cache_name = $this->_cache['cache_name'];
            $seconds = $this->_cache['seconds'];
        }
        if(isset($this->_cache) && !empty($this->_cache))
        {
            $data = $this->cache->{$this->cache_driver}->get($cache_name);
        }

        if(isset($data) && $data !== FALSE)
        {
            return $data;
        }
        else
        {
            $this->trigger('before_get');
            $this->where($where);
            $this->limit(1);
            $query = $this->_database->get($this->table);
            if ($query->num_rows() == 1)
            {
                $row = $query->row_array();
                $row = $this->trigger('after_get', $row);
                if(isset($cache_name) && isset($seconds))
                {
                    $this->cache->{$this->cache_driver}->save($cache_name, $data, $seconds);
                    $this->_reset_cache($cache_name);
                }
                return $this->_prep_after_read($row,FALSE);
            }
            return FALSE;
        }
    }

    /**
     * public function get_all()
     * Retrieves rows from table.
     * @param null $where
     * @return mixed
     */
    public function get_all($where = NULL)
    {
        if(isset($this->_cache) && !empty($this->_cache))
        {
            $this->load->driver('cache');
            $cache_name = $this->_cache['cache_name'];
            $seconds = $this->_cache['seconds'];
        }

        if(isset($this->_cache) && !empty($this->_cache))
        {
            $data = $this->cache->{$this->cache_driver}->get($cache_name);
        }

        if(isset($data) && $data !== FALSE)
        {
            return $data;
        }
        else
        {
            $this->trigger('before_get');
            $this->where($where);
            $query = $this->_database->get($this->table);
            if($query->num_rows() > 0)
            {
                $data = $query->result_array();
                $data = $this->trigger('after_get', $data);
                if(isset($cache_name) && isset($seconds))
                {
                    $this->cache->{$this->cache_driver}->save($cache_name, $data, $seconds);
                    $this->_reset_cache($cache_name);
                }
                return $this->_prep_after_read($data,TRUE);
            }
            else
            {
                return FALSE;
            }
        }
    }

    /**
     * public function count()
     * Retrieves number of rows from table.
     * @param null $where
     * @return integer
     */
    public function count($where = NULL)
    {
        $this->where($where);
        $this->_database->from($this->table);
        $number_rows = $this->_database->count_all_results();
        return $number_rows;
    }

    /** RELATIONSHIPS */

    /**
     * public function with($requests)
     * allows the user to retrieve records from other interconnected tables depending on the relations defined before the constructor
     * @param string $requests
     * @param bool $separate_subqueries
     * @return $this
     */
    public function with($relation,$conditions = NULL)
    {
        $this->_set_relationships();
        $this->_requested[$relation] = $conditions;
    }

    /**
     * protected function join_temporary_results($data)
     * Joins the subquery results to the main $data
     * @param $data
     * @return mixed
     */
    protected function join_temporary_results($data)
    {
        $data = json_decode(json_encode($data), TRUE);
        if(array_key_exists($this->primary_key,$data))
        {
            $data = array($data);
        }
        foreach($this->_requested as $requested_key => $request)
        {
            $pivot_table = NULL;
            $relation = $this->_relationships[$request];
            $this->load->model($relation['foreign_model']);
            $foreign_key = $relation['foreign_key'];
            $local_key = $relation['local_key'];
            (isset($relation['pivot_table'])) ? $pivot_table = $relation['pivot_table'] : FALSE;
            $foreign_table = $relation['foreign_table'];
            $type = $relation['relation'];
            $relation_key = $relation['relation_key'];
            $local_key_values = array();
            foreach($data as $key => $element)
            {
                if(isset($element[$local_key]))
                {
                    $id = $element[$local_key];
                    $local_key_values[$key] = $id;
                }
            }
            if(!isset($pivot_table))
            {
                $sub_results = $this->{$relation['foreign_model']}->as_array()->where($foreign_key, $local_key_values)->get_all();
            }
            else
            {
                $this->_database->join($pivot_table, $foreign_table.'.'.$foreign_key.' = '.$pivot_table.'.'.singular($foreign_table).'_'.$foreign_key, 'right');
                $this->_database->join($this->table, $pivot_table.'.'.singular($this->table).'_'.$local_key.' = '.$this->table.'.'.$local_key,'right');
                $this->_database->where_in($this->table.'.'.$local_key,$local_key_values);
                $sub_results = $this->_database->get($foreign_table)->result_array();
            }

            if(isset($sub_results) && !empty($sub_results)) {
                $subs = array();
                echo '<pre>';
                echo print_r($sub_results);
                echo '</pre>';
                foreach ($sub_results as $result)
                {
                    $subs[$result[$foreign_key]][] = $result;
                }
                $sub_results = $subs;
                foreach($local_key_values as $key => $value)
                {
                    if(array_key_exists($value,$sub_results))
                    {
                        if ($type == 'has_one')
                        {
                            $data[$key][$relation_key] = $sub_results[$value][0];
                        }
                        else
                        {
                            $data[$key][$relation_key] = $sub_results[$value];
                        }
                    }
                }
            }
            unset($this->_requested[$requested_key]);
        }
        if(sizeof($data)==1) $data = $data[0];
        return ($this->return_as == 'object') ? json_decode(json_encode($data), FALSE) : $data;
    }

    /**
     * private function _set_relationships()
     *
     * Called by the public method with() it will set the relationships between the current model and other models
     */
    private function _set_relationships()
    {
        if(empty($this->_relationships))
        {
            $options = array('has_one','has_many','has_many_pivot','has_many_through');
            foreach($options as $option)
            {
                if(isset($this->{$option}) && !empty($this->{$option}))
                {
                    foreach($this->{$option} as $key => $relation)
                    {
                        $this->_relationships[$key] = array(
                            'table' => $relation['table'],
                            'local_key' => $relation['local_key'],
                            'foreign_key' => $relation['foreign_key']);
                        if(isset($relation['connector']))
                        {
                            $this->_relationships[$key]['connector'] = $relation['connector'];
                        }
                    }
                }
            }
        }
        return TRUE;
    }

    /** END RELATIONSHIPS */

    /**
     * public function on($connection_group = NULL)
     * Sets a different connection to use for a query
     * @param $connection_group = NULL - connection group in database setup
     * @return obj
     */
    public function on($connection_group = NULL)
    {
        if(isset($connection_group))
        {
            $this->_database->close();
            $this->load->database($connection_group);
            $this->_database = $this->db;
        }
        return $this;
    }

    /**
     * public function reset($connection_group = NULL)
     * Resets the connection to the default used for all the model
     * @return obj
     */
    public function reset()
    {
        if(isset($connection_group))
        {
            $this->_database->close();
            $this->_set_connection();
        }
        return $this;
    }

    /**
     * Trigger an event and call its observers. Pass through the event name
     * (which looks for an instance variable $this->event_name), an array of
     * parameters to pass through and an optional 'last in interation' boolean
     */
    public function trigger($event, $data = array(), $last = TRUE)
    {
        if (isset($this->$event) && is_array($this->$event))
        {
            foreach ($this->$event as $method)
            {
                if (strpos($method, '('))
                {
                    preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);
                    $method = $matches[1];
                    $this->callback_parameters = explode(',', $matches[3]);
                }
                $data = call_user_func_array(array($this, $method), array($data, $last));
            }
        }
        return $data;
    }


    /**
     * public function with_trashed()
     * Sets $_trashed to TRUE
     */
    public function with_trashed()
    {
        $this->_trashed = 'with';
        return $this;
    }

    /**
     * public function with_trashed()
     * Sets $_trashed to TRUE
     */
    public function only_trashed()
    {
        $this->_trashed = 'only';
        return $this;
    }

    private function _where_trashed()
    {
        switch($this->_trashed)
        {
            case 'only' :
                $this->_database->where($this->_deleted_at_field.' IS NOT NULL', NULL, FALSE);
                break;
            case 'without' :
                $this->_database->where($this->_deleted_at_field.' IS NULL', NULL, FALSE);
                break;
            case 'with' :
                break;
        }
        $this->_trashed = 'without';
        return $this;
    }

    /**
     * public funciton fields($fields)
     * does a select() of the $fields
     * @param $fields the fields needed
     * @return $this
     */
    public function fields($fields = NULL)
    {
        if(isset($fields))
        {
            $fields = (is_array($fields)) ? implode(',',$fields) : $fields;
            $this->_database->select($fields);
        }
        return $this;
    }

    /**
     * public function order_by($criteria, $order = 'ASC'
     * A wrapper to $this->_database->order_by()
     * @param $criteria
     * @param string $order
     * @return $this
     */
    public function order_by($criteria, $order = 'ASC')
    {
        if(is_array($criteria))
        {
            foreach ($criteria as $key=>$value)
            {
                $this->_database->order_by($key, $value);
            }
        }
        else
        {
            $this->_database->order_by($criteria, $order);
        }
        return $this;
    }

    /**
     * Return the next call as an array rather than an object
     */
    public function as_array()
    {
        $this->return_as = 'array';
        return $this;
    }

    /**
     * Return the next call as an object rather than an array
     */
    public function as_object()
    {
        $this->return_as = 'object';
        return $this;
    }

    public function set_cache($string, $seconds = 86400)
    {
        $prefix = (strlen($this->cache_prefix)>0) ? $this->cache_prefix.'_' : '';
        $this->_cache = array('cache_name' => $prefix.$string,'seconds'=>$seconds);
        return $this;
    }

    private function _reset_cache($string)
    {
        if(isset($string))
        {
            $this->_cache = array();
        }
        return $this;
    }

    public function delete_cache($string = NULL)
    {
        $this->load->driver('cache');
        $prefix = (strlen($this->cache_prefix)>0) ? $this->cache_prefix.'_' : '';
        if(isset($string) && (strpos($string,'*') === FALSE))
        {
            $this->cache->{$this->cache_driver}->delete($prefix . $string);
        }
        else
        {
            $cached = $this->cache->file->cache_info();
            foreach($cached as $file)
            {
                if(array_key_exists('relative_path',$file))
                {
                    $path = $file['relative_path'];
                    break;
                }
            }
            $mask = (isset($string)) ? $path.$prefix.$string : $path.$prefix.'*';
            array_map('unlink', glob($mask));
        }
        return $this;
    }

    /**
     * private function _set_timestamps()
     *
     * Sets the fields for the created_at, updated_at and deleted_at timestamps
     * @return bool
     */
    private function _set_timestamps()
    {
        if($this->timestamps === TRUE || is_array($this->timestamps))
        {
            $this->_created_at_field = (is_array($this->timestamps) && isset($this->timestamps[0])) ? $this->timestamps[0] : 'created_at';
            $this->_updated_at_field = (is_array($this->timestamps) && isset($this->timestamps[1])) ? $this->timestamps[1] : 'updated_at';
            $this->_deleted_at_field = (is_array($this->timestamps) && isset($this->timestamps[2])) ? $this->timestamps[2] : 'deleted_at';
        }
        return TRUE;
    }

    /**
     *
     * protected function add_deleted($row)
     *
     * Receives a row of data and appends to it a deleted_at field type returning the row
     *
     * @param $row
     * @return mixed
     */
    protected function add_deleted($row)
    {
        if($this->timestamps === TRUE || is_array($this->timestamps))
        {
            if(is_object($row) && !isset($row->{$this->_deleted_at_field}))
            {
                $row->{$this->_deleted_at_field} = date('Y-m-d H:i:s');
            }
            elseif(!isset($row[$this->_deleted_at_field]))
            {
                $row[$this->_deleted_at_field] = date('Y-m-d H:i:s');
            }
        }
        return $row;
    }

    /**
     * private function _set_connection()
     *
     * Sets the connection to database
     */
    private function _set_connection()
    {
        isset($this->_database_connection) ? $this->load->database($this->_database_connection) : $this->load->database();
        $this->_database = $this->db;
    }

    /*
     * HELPER FUNCTIONS
     */

    public function paginate($rows_per_page, $total_rows = NULL, $page_number = 1)
    {
        $this->load->helper('url');
        $segments = $this->uri->total_segments();
        $uri_array = $this->uri->segment_array();
        $page = $this->uri->segment($segments);
        if(is_numeric($page))
        {
            $page_number = $page;
        }
        else
        {
            $page_number = $page_number;
            $uri_array[] = $page_number;
            ++$segments;
        }
        $next_page = $page_number+1;
        $previous_page = $page_number-1;

        if($page_number == 1)
        {
            $this->previous_page = $this->pagination_delimiters[0].$this->pagination_arrows[0].$this->pagination_delimiters[1];
        }
        else
        {
            $uri_array[$segments] = $previous_page;
            $uri_string = implode('/',$uri_array);
            $this->previous_page = anchor($uri_string,$this->pagination_delimiters[0].$this->pagination_arrows[0].$this->pagination_delimiters[1]);
        }
        $uri_array[$segments] = $next_page;
        $uri_string = implode('/',$uri_array);
        if(isset($total_rows) && (ceil($total_rows/$rows_per_page) == $page_number))
        {
            $this->next_page = $this->pagination_delimiters[0].$this->pagination_arrows[1].$this->pagination_delimiters[1];
        }
        else
        {
            $this->next_page = anchor($uri_string, $this->pagination_delimiters[0].$this->pagination_arrows[1].$this->pagination_delimiters[1]);
        }

        $rows_per_page = (is_numeric($rows_per_page)) ? $rows_per_page : 10;

        if(isset($total_rows))
        {
            if($total_rows!=0)
            {
                $number_of_pages = ceil($total_rows / $rows_per_page);
                $links = $this->previous_page;
                for ($i = 1; $i <= $number_of_pages; $i++) {
                    unset($uri_array[$segments]);
                    $uri_string = implode('/', $uri_array);
                    $links .= $this->pagination_delimiters[0];
                    $links .= (($page_number == $i) ? anchor($uri_string, $i) : anchor($uri_string . '/' . $i, $i));
                    $links .= $this->pagination_delimiters[1];
                }
                $links .= $this->next_page;
                $this->all_pages = $links;
            }
            else
            {
                $this->all_pages = $this->pagination_delimiters[0].$this->pagination_delimiters[1];
            }
        }


        if(isset($this->_cache) && !empty($this->_cache))
        {
            $this->load->driver('cache');
            $cache_name = $this->_cache['cache_name'].'_'.$page_number;
            $seconds = $this->_cache['seconds'];
        }

        if(isset($this->_cache) && !empty($this->_cache))
        {
            $data = $this->cache->{$this->cache_driver}->get($cache_name);
        }

        if(isset($data) && $data !== FALSE)
        {
            return $data;
        }
        else
        {
            $this->trigger('before_get');
            $this->where();
            $this->limit($rows_per_page, (($page_number-1)*$rows_per_page));
            $data = $this->get_all();
            if($data)
            {
                if(isset($cache_name) && isset($seconds))
                {
                    $this->cache->{$this->cache_driver}->save($cache_name, $data, $seconds);
                    $this->_reset_cache($cache_name);
                }
                return $data;
            }
            else
            {
                return FALSE;
            }
        }
    }

    public function set_pagination_delimiters($delimiters)
    {
        if(is_array($delimiters) && sizeof($delimiters)==2)
        {
            $this->pagination_delimiters = $delimiters;
        }
        return $this;
    }

    public function set_pagination_arrows($arrows)
    {
        if(is_array($arrows) && sizeof($arrows)==2)
        {
            $this->pagination_arrows = $arrows;
        }
        return $this;
    }

    public function __call($method, $arguments)
    {
        if(substr($method,0,6) == 'where_')
        {
            $column = substr($method,6);
            $this->where($column, $arguments);
            return $this;
        }
        if(substr($method,0,5) == 'with_')
        {
            $relation = substr($method,5);
            $this->with($relation,$arguments);
            return $this;
        }
        else echo 'No method with that name ('.$method.') in MY_Model.';
    }
}
