<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>SQL Injection Simulation</title>
</head>
<body style="font-family: helvetica, arial, sans;">
<?php

/*
    This was written by Davo Smith ( http://www.davodev.co.uk )
    For what it is worth, it is released under the GPL version 3 or above
*/

$username = false;
$password = false;
$loginok = false;
$realname = false;

$users = array(
               array('username'=>'bob','password'=>'axp149','realname'=>'System Administrator'),
               array('username'=>'dave','password'=>'davepass','realname'=>'David Jones'),
               array('username'=>'sam','password'=>'cheese','realname'=>'Sam Davis')
               );

if (isset($_REQUEST['username'])) { 
    $username = strtolower($_REQUEST['username']);
}

if (isset($_REQUEST['password'])) {
    $password = $_REQUEST['password'];
}

// Remove any 'magic quotes' (if set)
if (get_magic_quotes_gpc()) {
    $username = stripslashes($username);
    $password = stripslashes($password);
}

// Check for 'proper' login
foreach ($users as $user) {
    if ($username == $user['username'] && $password == $user['password']) {
        $loginok = true;
        $realname = $user['realname'];
        $result = "Found 1 result:<br/>username: {$user['username']}, password: {$user['password']}, realname: {$user['realname']}";
    }
}

if (!$loginok) {
    // Split the username by the ' character
    $usersplit = explode('\'', $username);

    if (count($usersplit) == 1) {
        // No 'proper' login and no attempt at SQL injection
        $result = 'No records found';

    } else if (count($usersplit) % 2 == 0) { // Should have odd number of sections, otherwise there is a problem
        $result = 'SQL error - invalid query';
    } else {
        $conn = strtolower(trim($usersplit[1]));
        if ($conn != 'or' && $conn != 'and') {
            $result = 'SQL error - invalid query';
        } else {
            if ($conn == 'or') {
                if (trim($usersplit[3]) == '=') {
                    if (trim($usersplit[2]) == trim($usersplit[4])) {
                        // Correctly inserted: OR 'XX'='XX
                        $loginok = true;
                    }
                }
            }

            if ($loginok) {
                // Display all records - selet the first as the 'logged in' user
                $result = 'Found '.count($users).' results<br/>';
                foreach ($users as $user) {
                    $result .= "username: {$user['username']}, password: {$user['password']}, realname: {$user['realname']}<br/>";
                }
                $realname = $users[0]['realname'];
            } else {
                $result = 'No records found';
            }
        }
    }
}

if ($loginok) {
    echo "<h1>Welcome, $realname</h1>";
    echo '<p>Here is some top secret information, that only you should be able to access</p>';
    echo '<p><a href="sqlinjectsim.php">Logout</a></p>';

} else {
    echo '<h1>Welcome to this site</h1>';
    echo '<p>Please login below:</p>';

    $loginname = '';
    if ($username) {
        echo '<p style="color: red;">Username / password is incorrect</p>';
        $loginname = $username;
    }

    echo '<form method="POST" action="sqlinjectsim.php" style="width:250px; border:solid 1px black; padding: 10px;">';
    echo '<label style="width:80px; display:inline-block; text-align:right;" for="username">Username:</label><input id="username" name="username" type="text" value="'.$loginname.'" /><br />';
    echo '<label style="width:80px; display:inline-block; text-align:right;" for="password">Password:</label><input id="password" name="password" type="password" value="" /><br />';
    echo '<input type="submit" name="login" value="Login" />';
    echo '</form>';
}

if ($username) {
    // Login was attempted, so give a 'behind the scenes' look at the SQL query
    echo '<br />';
    echo '<div style="border:solid 1px black; padding: 10px; background-color: #ffffa0;">';
    echo '<h2>This is what happened on the server</h2>';
    echo '<p>SQL Query:<br />';
    echo "SELECT * from `users` WHERE username='$username' AND password='$password'; <br/>";
    echo $result.'<br/>';
}

?>
</body>
</html>
