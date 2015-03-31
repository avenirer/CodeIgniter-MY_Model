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
		
		// you can set relationships between tables
		
		//$this->has_one['...'] allows establishing ONE TO ONE or more ONE TO ONE relationship(s) between models/tables
		$this->has_one['phone'] = 'Phone_model';
		// or $this->has_one['phone'] = array('Phone_model','foreign_key','local_key');
		$this->has_one['address'] = 'Address_model';
		// or $this->has_one['address'] = array('Address_model','foreign_key','another_local_key');
		
		// $this->has_many[''...] allows establishing ONE TO MANY or more ONE TO MANY relationship(s) between models/tables
		$this->has_many['posts'] = 'Post_model';
		// or $this->has_many['posts'] = array('Posts_model','foreign_key','another_local_key');
		
		// $this->has_many_pivot['...'] allows establishing MANY TO MANY or more MANY TO MANY relationship(s) between models/tables with the use of a PIVOT TABLE
		$this->has_many_pivot['posts'] = 'Post_model';
		// or $this->has_many_pivot['posts'] = array('Posts_model','foreign_primary_key','local_primary_key');
		
		// ATTENTION! The pivot table name must be composed of the two table names separated by "_" the table names having to to be alphabetically ordered (NOT users_posts, but posts_users).
		// Also the pivot table must contain as identifying columns the columns named by convention as follows: table_name_singular + _ + foreign_table_primary_key.
		// For example: considering that a post can have multiple authors, a pivot table that connects two tables (users and posts) must be named posts_users and must have post_id and user_id as identifying columns for the posts.id and users.id tables.
		
		// you can also use caching. If you want to use the set_cache('...') method, but you want to change the way the caching is made you can use the following properties:
		
		$this->cache_driver = 'file';
		//By default, MY_Model uses the files (CodeIgniter's file driver) to cache result. If you want to change the way it stores the cache, you can change the $cache_driver property to whatever CodeIgniter cache driver you want to use.
		
		$this->cache_prefix = 'mm';
		With $cache_prefix, you can prefix the name of the caches. By default any cache made by MY_Model starts with 'mm' + _ + "name chosen for cache"
  		
		parent::__construct();
 	}
}
```

##Caching

If you want to cache the result for faster output, you can at any time use the MY_Model's caching. To do this you simply attach a set_cache('name') inside the query chain:

```php
$this->load->model('user_model');
$users = $this->user_model->as_array()->set_cache('get_all_users')->get_all();
```
The code above will create a cache file named mm_get_all_users. If you want the cache to have a time limit, you can pass a second parameter that represents the number of seconds:

```php
$users = $this->user_model->as_array()->set_cache('get_all_users',3600)->get_all();
```
This file will then be used by the model whenever you call the get_all() method that has a set_cache('get_all_users') method in the chain.

Whenever you want, you can delete the cache "manually" by using the delete_cache() method.

There are three ways you can delete the cache:

* `delete_cache('get_all_users')` deletes a certain cache;
* `delete_cache('users_*')` deletes the caches that start with 'mm_users_' (where 'mm_' is the prefix used by MY_Model);
* `delete_cache()` deletes all cache that start with 'mm_' (where 'mm_' is the prefix used by MY_Model).

Example:
```php
$this->user_model->delete_cache('get_all_users');
```

##Pagination

You can at any time "paginate" the results. You can do this by simply changing `get_all()` method with `paginate()` method. The paginate() method can receive up to three parameters:

* first paramater, which is optional (default is set to 10) is the number of rows per page;
* second parameter, which is optional (default is set to NULL), is the total of rows;
* the third parameter, which is also optional (default is set to 1) is the page you want to get.

If you only passed the first parameter, the page number will be retrieved from the URL (the last segment of the page). If you didn't pass the second parameter you will only be able to output the previous and next page, without links to all the pages.

Examples:

```php
$total_posts = $this->post_model->count(); // retrieve the total number of posts
$posts = $this->post_model->paginate(10,$total_posts); // paginate with 10 rows per page
echo $this->post_model->all_pages; // will output links to all pages like this model: "< 1 2 3 4 5 >". It will put a link if the page number is not the "current page"
echo $this->post_model->previous_page; // will output link to the previous page like this model: "<". It will only put a link if there is a "previous page"
echo $this->post_model->next_page; // will output link to the next page like this model: ">". It will only put a link if there is a "next page"
```
Don't you like how the links look? You can change them by modifing the following properties inside the models that extend the MY_Model():

```php
$this->pagination_delimiters = array('<span>','</span>');
$this->pagination_arrows = array('&lt;','&gt;');
```
Also, you can use the set_pagination_delimiters($delimiters) and set_pagination_arrows($arrows) methods, where $delimiters and $arrows are arrays.

##Relationships

When you extend MY_Model, you can also setup relationships between the model and other models (as long as they are created and extend MY_Model). So, just before `parent::__construct();` you can also add:
```php
$this->has_one['phone'] = 'Phone_model'
// if the Phone_model doesn't extend the MY_Model, you can manually define the relationship by using an array
$this->has_one['phone'] = array('Phone_model','foreign_key','local_key');

$this->has_one['address'] = 'Address_model'

$this->has_many['posts'] = array('Posts_model','foreign_key','another_local_key');
```

###Has One (one to one) relationship (property)

Has One relationship tells our model that ever record in the table has assigned to it a record in another table. It is my opinion that there is no need to do a reverse relation like in Eloquent, where there is a "belongs to" relationship because, the truth be told, being a "one to one" relationship it's an equality between the entities.

We can define a "one to one" relationship by using the has_one property inside the constructor:
```php
class User_model extends MY_Model
{

	function __construct()
	{
		$this->has_one['phone'] = 'Phone_model';
	}
 }
 ```

The reverse of the relationship is defined taking care of the foreign key and local key:

```php
class Phone_model extends MY_Model
{

	function __construct()
	{
		$this->has_one['user'] = array('User_model','id','user_id');
	}
}
```

###Has Many relationship (property)

Has Many relationship tells our model that a record in the table can have many related records in another table. The reverse of this relationship is a has one relation, which translates into a One To Many type of relationship. For a reverse relationship of type Many To Many, we will have another property named Has Many Pivot.

```php
class User_model extends MY_Model
{

	function __construct()
	{
		$this->has_many['posts'] = 'Post_model';
	}
 }
 ```

The reverse of the relationship (which in this case is a one to one) is defined the same:

```php
class Post_model extends MY_Model
{

	function __construct()
	{
		$this->has_one['user'] = array('User_model','id','user_id');
	}
}
```

###Has Many Pivot relationship (property)

Many to many relationship can have one to one as reverse relationship. But there are also many to many relationships that have many to many as reverse relationships. For this we have has_many_pivot key as relation. This one allows establishing MANY TO MANY or more MANY TO MANY relationship(s) between models/tables with the use of a PIVOT TABLE.

**ATTENTION**: The pivot table name must be composed of the two connected table names separated by _ (underscore) the table names having to be alphabetically ordered (NOT users_posts, but posts_users). Also the pivot table must contain as identifying columns the columns named by convention as follows: foreign_table_name_singular + _ (underscore) + foreign_table_primary_key.

For example: considering that a post can have multiple authors, a pivot table that connects the two tables (**users** and **posts**) must be named **posts_users** (**NOT users_posts**) and must have **post_id** and **user_id** as identifying columns for the **posts.id** and **users.id** tables.

Usage example:

```php
class User_model extends MY_Model
{

	function __construct()
	{
		$this->has_many_pivot['posts'] = 'Post_model';
		// or $this->has_many_pivot['posts'] = array('Post_model','id','id'); where the second parameter is the foreign primary key of posts table, and the third parameter is the local primary key.
	}
 }
 ```

The reverse of the relationship (which in this case is also a many to many) is defined the same:

```php
class Post_model extends MY_Model
{

	function __construct()
	{
		$this->has_many_pivot['users'] = 'User_model';
		// or $this->has_many_pivot['users'] = array('User_model','id','id'); where the second parameter is the foreign primary key of users table, and the third parameter is the local primary key.
	}
}
```

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
```php
$user = $this->user_model->with('phone')->with('posts')->get(1);
```
You can also call the related data in one single string, by separating the relations with pipe:
```php
$user = $this->user_model->with('phone|address|posts')->get(1);
```
The related data will be embedded in the returned value having "phone", "address" and "posts" as keys.
```php
echo $user->phone->phone_number;

foreach ($user->posts as $post)
{
    echo $post->title;
}
```
If you have a one on one relationship, you can get the related data by joining tables so that no separate query is made. This is made by passing **FALSE** as second parameter for with() Also, you can make sure you don't have conflicting column names by chaining a `fields()` method to the query:
```php
$users = $this->user_model->with('phone',FALSE)->fields('users.id,users.username,phones.id as phone_id, phones.phone_number')->get_all();
```
You must take into consideration that if you don't do a join or have a many_to_many relationships, for each relation there is one separate query.

##Arrays vs Objects

By default, MY_Model is setup to return objects. If you'd like to return results as array you can:

* either define `$this->return_as = 'array'` in the constructor
* or add `as_array()` into the query chain:
```php
$users = $this->user_model->as_array()->get_all(); $posts = $this->post_model->as_object()->get_all();
```
If you'd like all your calls to use the array methods, you can set the $return_type variable to array.

##Soft Deletes

By default, the delete mechanism works with an SQL DELETE statement. However, you might not want to destroy the data, you might instead want to perform a **'soft delete'**.

If you enable soft deleting, the **deleted_at** row will be filled with the current date and time, rather than actually being removed from the database.

You can enable soft delete in the constructor:
```php
$this->soft_deletes = TRUE;
```
Once you've enabled it whenever you do, for example, a `$this->user_model->delete(3);` the **delete()** method will only create a datetime in the **deleted_at** column of the user with id 3.

If you really want to delete a row you can use `force_delete()` method:
```php
$this->user_model->force_delete(6);
```
You can also restore or "un-delete" a row by using the `restore()` method:
```php
$this->user_model->restore(3)
```
This will set to **NULL** the **deleted_at** value.

Once you soft delete a row, that row won't appear in read results unless expressely asked to:

For this, you have the following methods:

* `with_trashed()` - will show all rows, including those that were soft deleted
* `only_trashed()` - will show only the rows that were soft deleted

You can also check if a row is **soft_deleted** by using `trashed()` method:
```php
$this->user_model->trashed(3); // will return TRUE or FALSE
```
##Database Connection

The class will automatically use the default database connection, and even load it for you if you haven't yet.

You can specify a database connection on a per-model basis by declaring the `$_database_connection` instance variable.

You can also change the database connection on a per request basis. For example, if you want to use a different database connection for writing data you can do this:
```php
$this->user_model->on('write_conn')->delete(3);
```
After this, I would advise you to do a `$this->user_model->reset();` in order to reset the database connection to the model's (or application's) default.

##Observers##

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
        $data['password'] = 'whateverpasswordcreationresultyoumaythinkof';
        return $data;
    }
}
```
Each observer overwrites its predecessor's data, sequentially, in the order the observers are defined. In order to work with relationships, the MY_Model already has an `after_get` trigger which will be called last.

##Available methods

###insert($data)
It inserts one or more rows into table

####Parameters
* $data - data to be inserted.

####Return
* either a integer representing the id of the inserted row;
* or an array with ids.

####Examples
```php
$data = array('username'=>'avenirer','email'=>'avenir.ro@gmail.com');
$this->user_model->insert($data);
```

###update($data, $column_name_where = NULL)
It updates one or more rows from table

####Parameters
* $data - the updated data as object or multidimensional array or multiple array (just like the native $this->db->update())
* $column_name_where - no value if you want to update all rows, an id of the row, an array containing column name and value or, if there are multiple rows, the name of the column that can be found in the $data array.

####Return
Returns the number of affected rows

####Examples
```php
$newdata = array('status'=>'1');
$this->user_model->update($data);

$newdata = array('username'=>'aveniro');
$this->user_model->update($data,1);

$newdata = array('username'=>'aveniro', 'email'=>'avenir.ro@gmail.ro');
$this->user_model->update($data,array('email'=>'avenir.ro@gmail.com'));

$newdata = array('username'=>'aveniro', 'email'=>'avenir.ro@gmail.com');
$this->user_model->update($data,'email');
```

###where($where_col_array, $value = NULL)
It sets a where condition to the query

####Parameters
* $where_col_array, $value = NULL - if you want to look by an id you can simply pass the id; if you want to look for a value of a column, you can pass it as to parameters where('column','value');  if you have multiple columns for identifing a row you can pass it an array where(array('column1'=>'value1','column2'=>'value2')); if you have a "where in" type of query (multiple posible values for a column), you can pass it the name of the column as first parameter and an array of possible values as second parameter;

####Return
Doesn't return anything, being a part of the query chain

####Examples
```php
$this->user_model->where(3)->get();
//you can also do it like this: $this->user_model->get(3);

$this->user_model->where('username','avenirer')->get();

$this->user_model->where('id >=', '3')->get();

$this->user_model->where(array('email'=>'avenir.ro@gmail.com','username'=>'avenirer'))->get();

$this->user_model->where('username',array('avenirer','aveniro')->get();
```

###where_*()
There is also a "dynamic" where. That means that at any time you can write a where method that contains the name of the column:

```php
$this->user_model->where_username('avenirer')->get(); // where the "username" value is avenirer
$this->user->model->where_mail(array('avenir.ro@gmail.com','adrian.voicu@avenir.ro'))->get_all();
```

###limit($limit,$offset=0)
Is a self explaining method...

###order_by($criteria, $order = 'ASC')
Is a wrapper for $this->db->order_by()

###delete(where)
It deletes or soft deletes (depending on your settings) rows, working like the native $this->db->delete().

####Parameters

####Return
It returns affected rows or false, if no delete was done.

###force_delete(where)
It forces the delete of row(s) if soft delete was enabled. Takes same parameters and returns same thing like the method before

###restore($where)
Restores row(s) that were previously soft deleted.
Takes same parameters and returns same thing like the method before

###trashed($where)
Verifies if a row is soft deleted or not

####Return
It returns TRUE or FALSE

####Examples
```php
if($this->user_model->trashed(1))
{
	echo 'the user was deleted';
}
```

###get($where = NULL)
Returns a single row that respects the $where parameter

####Parameters
* where - the $where parameter uses the where($param) method, that means only one parameter

####Return
Returns a row;

####Examples
```php
$user = $this->user_model->get(1);

$user = $this->user_model->get(array('username'=>'avenirer'));
```

###get_all($where = NULL)
Same as the get() method but it can return more than one row

###paginate($rows_per_page = 10, $total_rows = NULL, $page_number = 1)

####Parameters
* rows_per_page = 10 - the number of rows per page
* total_rows = NULL - the total number of rows
* page_number = 1 - current page number

####Return
Returns the results and created the links that can be retrieved by accessing "previous_page", "next_page" and "all_pages" properties

###count($where)
Returns the number of rows.

####Example
```php
$users = $this->user_model->as_array()->count();
```

###as_array()
Sets the option to return the results as an array(), if the model was previously set to return the results as objects.

####Example
```php
$users = $this->user_model->as_array()->get_all();
```

###fields($fields)
Allows the user to select only specific columns

###Examples
```php
$users = $this->user_model->fields('username,password')->get_all();

$users = $this->user_model->fields(array('users.username', 'users.password', 'group.name')->get_all();
```

###as_object()
Sets the option to return the results as object, if the model was previously set to return the results as arrays.

####Example
```php
$users = $this->user_model->as_object()->get_all();
```

###with_trashed()
Sets the option to return in the results the rows that were soft deleted

####Example
```php
$users = $this->user_model->with_trashed()->get_all;
```

###only_trashed()
Sets the option to return in the results only the rows that were soft deleted

####Example
```php
$users = $this->user_model->only_trashed()->get_all;
```

###on($connection_group)
Sets a connection group for the current chain query

###reset()
Resets the connection to the database to the one that is set for the model or the default connection

Enjoy using my MY_Model and please report any issues or try some pull requests. Thank you
