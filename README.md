# CodeIgniter-MY_Model

This **CodeIgniter MY_Model** is the result of a lengthy tutorial about constructing a **MY_Model** in CodeIgniter (http://avenir.ro/revisiting-my_model-copying-jamie-rumbelow-looking-eloquent/). It's based on **Jamie Rumbelow's Base Model** (https://github.com/jamierumbelow/codeigniter-base-model), but with some changed/added methods. It provides a full CRUD base for database interactions, as well as an event-based observer system, intelligent table name guessing and soft delete.

##Synopsis
```php
class User_model extends MY_Model { }

$this->load->model('user_model');

$this->user_model->get(1)

$this->user_model->get_all();

$this->user_model->where('username','avenirer')->get();

$this->user_model->insert(array('username' => 'avenirer','email' => 'avenir.ro@gmail.com'));

$this->user_model->update(array('status' => '0'), 1);

$this->user_model->delete(1);
```

##Installation/Usage

Download and drag the **MY_Model.php** file into your **application/core** directory. CodeIgniter will load and initialise this class automatically.

Extend your model classes from MY_Model and all the functionality will be baked in automatically.
```php
class User_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct()
	}
}
```
If extended like that, MY_Model makes the following assumptions:

* **the table name** is the plural of the model name without the **_model** or **_m** extension: if a model is called **User_model**, the table is assumed to be called **users**. That means that you have to call your models like **Table_model** or **Table_m**
* **the primary key** is named "id". That means that every table must have "id" as primary key.
* there are **at least a "created_at" and "updated_at" columns**.

If you want, you can be original by changing the settings before the `parent::__construct();`
```php
class User_model extends MY_Model
{
	public function __construct()
	{
		
		// you can set the database connection that you want to use for this particular model, by passing the group connection name or a config array. By default will use the default connection
		$this->_database_connection  = 'special_connection';
		
		// you can set a table name, if your model doesn't respect the assumptions made by MY_Model. If you don't set a table name, the name will be the plural of the model name without the "_model" or "_m" string (model name: User_model; table: users).
		$this->table = 'tha_hood_table';
		
		// you can set a different primary key
		$this->primary = 'my_key_is_more_unique';
		
		// you can disable the use of timestamps. This way, MY_Model won't try to set a created_at and updated_at value on create methods. Also, if you pass it an array as calue, it tells MY_Model, that the first element is a created_at field type, the second element is a updated_at field type (and the third element is a deleted_at field type if $this->soft_deletes is set to TRUE)
		$this->timestamps = TRUE
		
		// you can enable (TRUE) or disable (FALSE) the "soft delete" on records. Default is FALSE, which means that when you delete a row, that one is gone forever
        $this->soft_deletes = FALSE
              
        // you can set how the model returns you the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'object' | 'array'
  		
		parent::__construct();
 	}
}
```
##Relationships

When you extend MY_Model, you can also setup relationships between the model and other models (as long as they are created and extend MY_Model). So, just before `parent::__construct();` you can also add:
```php
$this->has_one['phone'] = 'Phone_model'
// if the Phone_model doesn't extend the MY_Model, you can manually define the relationship by using an array
$this->has_one['phone'] = array('Phone_model','foreign_key','local_key');

$this->has_one['address'] = 'Address_model'

$this->has_many['posts'] = array('Posts_model','foreign_key','another_local_key');
```
There are times when you'll need to alter your model data before or after it's inserted or returned. This could be adding timestamps, pulling in relationships or deleting dependent rows. The MVC pattern states that these sorts of operations need to go in the model. In order to facilitate this, MY_Model contains a series of callbacks/observers -- methods that will be called at certain points.

The full list of observers are as follows:
```php
$before_create = array();
$after_create = array();
$before_update = array();
$after_update = array();
$before_get = array();
$after_get = array();
$before_delete = array();
$after_delete = array();
$before_soft_delete = array();
$after_soft_delete = array();
```
These are instance variables usually defined at the class level. They are arrays of methods on this class to be called at certain points. An example:
```php
class User_model extends MY_Model
{
	function __construct()
	{
		$this->before_create[] = 'hash_password';
		parent::__construct();
	}
    public $before_create = array( 'hash_password' );

	protected function hash_password($data)
    {
        $book['password'] = 'whateverpasswordcreationresultyoumaythinkof';
        return $data;
    }
}
```
Each observer overwrites its predecessor's data, sequentially, in the order the observers are defined. In order to work with relationships, the MY_Model already has an `after_get` trigger which will be called last.

##Working with relationships

Every table has a way to interact with other tables. So if your model has relationships with other models, you can define those relationships:
```php
class User_model extends MY_Model
{

    function __construct()
    {
        $this->has_one['phone'] = 'Phone_model';
        $this->has_one['address'] = array('Address_model','user_id','id');
        $this->has_many['posts'] = 'Post_model';
        parent::__construct();
    }
}
```
You can then access your related data using the `with()` method:

`php $user = $this->user_model->with('phone')->with('posts')->get(1);`

You can also call the related data in one single string, by separating the relations with pipe:

$user = $this->user_model->with('phone|address|posts')->get(1);

The related data will be embedded in the returned value having "phone", "address" and "posts" as keys.

echo $user->phone->phone_number;

foreach ($user->posts as $post)
{
    echo $post->title;
}

If you have a one on one relationship, you can get the related data by joining tables so that no separate query is made. This is made by passing "FALSE" as second parameter for with() Also, you can make sure you don't have conflicting column names by chaining a fields() method to the query:

$users = $this->user_model->with('phone',FALSE)->fields('users.id,users.username,phones.id as phone_id, phones.phone_number')->get_all();

You must take into consideration that if you don't do a join or have a many_to_many relationships, for each relation there is one separate query.

Arrays vs Objects

By default, MY_Model is setup to return objects. If you'd like to return results as array you can:

- either define $this->return_as = 'array' in the constructor
- or add as_array() into the query chain: $users = $this->user_model->as_array()->get_all(); $posts = $this->post_model->as_object()->get_all();

If you'd like all your calls to use the array methods, you can set the $return_type variable to array.

Soft Deletes

By default, the delete mechanism works with an SQL DELETE statement. However, you might not want to destroy the data, you might instead want to perform a 'soft delete'.

If you enable soft deleting, the deleted_at row will be filled with the current date and time, rather than actually being removed from the database.

You can enable soft delete in the constructor: $this->soft_deletes = TRUE;

Once you've enabled it whenever you do, for example, a $this->user_model->delete(3); the delete() method will only create a datetime in the deleted_at column of the user with id 3.

If you really want to delete a row you can use force_delete() method: $this->user_model->force_delete(6);

You can also restore or "un-delete" a row by using the restore() method: $this->user_model->restore(3). This will set to NULL the deleted_at value.

Once you soft delete a row, that row won't appear in read results unless expressely asked to:

For this, you have the following methods:

* with_trashed() - will show all rows, including those that were soft deleted
* only_trashed() - will show only the rows that were soft deleted

You can also check if a row is soft_deleted by using trashed() method:

$this->user_model->trashed(3); // will return TRUE or FALSE

Database Connection

The class will automatically use the default database connection, and even load it for you if you haven't yet.

You can specify a database connection on a per-model basis by declaring the $_db_group instance variable. This is equivalent to calling $this->db->database($this->_db_group, TRUE).

You can also change the database connection on a per request basis. For example, if you want to use a different database connection for writing data you can do this:

$this->user_model->on('write_conn')->delete(3);

After this, I would advise you to do a $this->user_model->reset(); in order to reset the database connection to the model's (or application's) default.
