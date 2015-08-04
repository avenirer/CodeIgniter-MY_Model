<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
    <style type="text/css" rel="stylesheet">
        html, body {font-family:sans-serif;}
        h2 a {text-decoration: none; color: #009900; font-size: 14px;}
        pre {font-size: 10px; background-color: #f9f9f9; border:1px solid #4F5155; padding: 10px;}
    </style>
</head>
<body>

<div id="container">
	<h1>Welcome to MY_Model!</h1>

	<div id="body">
		<p>The page you are seeing is just an example of what the models can do with the help of MY_Model.</p>

        <h2><a href="#">$this->user_model->get(1)</a></h2>
        <pre>
            <?php print_r($user);?>
        </pre>

        <h2><a href="#">$this->user_model->as_array()->get(1)</a></h2>
        <pre>
            <?php print_r($user_as_array);?>
        </pre>

        <h2><a href="#">$this->user_model->get_all()</a></h2>
        <pre>
            <?php print_r($users);?>
        </pre>
        <h2><a href="#">$this->user_model->as_array()->get_all()</a></h2>
        <pre>
            <?php print_r($users_as_array);?>
        </pre>
        <h2><a href="#">$this->user_model->as_dropdown('username')->get_all()</a></h2>
        <pre>
            <?php print_r($users_as_dropdown);?>
        </pre>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>