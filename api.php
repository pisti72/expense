<?php

/** 
 * 
 * http://mysql.atw.hu
 */

$response = array();

//$mysqli = new mysqli("localhost", "expense", "Start123", "expense");
global $mysqli;
$mysqli = new mysqli("c236um.forpsi.com", "b12516", "V9pebnA","b12516","3306");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/**
 *  create the database
 */

if (isset($_GET['db'])) {
    $db = $_GET['db'];
    if ($db == 'create') {
        $sql = "drop table if exists users";
        $mysqli->query($sql);
        $sql = "drop table if exists transactions";
        $mysqli->query($sql);
        $sql = "
CREATE TABLE transactions (
  id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  category varchar(50) NOT NULL,
  amount decimal(10,0) NOT NULL,
  balance decimal(10,0) NOT NULL,
  description varchar(100) NOT NULL,
  createdAt timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1";
$mysqli->query($sql);
$sql = "ALTER TABLE transactions ADD PRIMARY KEY (id)";
        $mysqli->query($sql);
        $sql = "
CREATE TABLE users (
  id int(11) NOT NULL,
  username varchar(100) NOT NULL,
  password varchar(10) NOT NULL,
  email varchar(100) NOT NULL,
  token varchar(100) NOT NULL,
  createdAt timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1";
$mysqli->query($sql);
$sql = "ALTER TABLE users ADD PRIMARY KEY (id)";

        if ($result = $mysqli->query($sql)) {
            $response['result'] = 'success';
        } else {
            $response['result'] = 'failed';
        }
    }
}

/**
 *  lists last transactions
 */
if (isset($_GET['token'], $_GET['top'])) {
    $token = $_GET['token'];
    $top = $_GET['top'];
    $query = "SELECT amount,category,description,createdAt,balance
    FROM transactions t, users u
    WHERE u.token = '$token'
    AND t.user_id = u.id
    ORDER BY createdAt DESC
    LIMIT $top";
    $rows = array();
    if ($result = $mysqli->query($query)) {

        /* fetch object array */
        while ($row = $result->fetch_row()) {
            $rows[] = $row;
        }

        $response = $rows;
        $result->close();
    }
}

/* login and return the user */
if (isset($_GET['name'], $_GET['password'])) {
    $name = $_GET['name'];
    $password = substr(md5($_GET['password']), 0, 10);
    $query = "SELECT username, token FROM users WHERE (username = '$name' OR email = '$name') AND password = '$password'";
    if ($result = $mysqli->query($query)) {
        /* fetch object array */
        if ($row = $result->fetch_row()) {
            $response['result'] = 'success';
            $response['name'] = $row[0];
            $response['token'] = $row[1];
            $result->close();
        } else {
            $response['result'] = 'failed';
        }
    }
}

/**
 * counts users
 */

if (isset($_GET['count'])) {
    $count = $_GET['count'];
    if ($count == "users") {
        $query = "SELECT COUNT(*) FROM users WHERE 1";
        if ($result = $mysqli->query($query)) {
            /* fetch object array */
            if ($row = $result->fetch_row()) {
                $response['result'] = 'success';
                $response['count'] = $row[0];
                $result->close();
            } else {
                $response['result'] = 'failed';
            }
        }
    }
}

/**
 * Insert a new expense
 */

if (isset($_POST['category'], $_POST['amount'], $_POST['description'], $_POST['token'])) {
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $token = $_POST['token'];
    $id = 0;

    //findUserByToken
    $query = "SELECT id FROM users WHERE token = '$token'";
    if ($result = $mysqli->query($query)) {
        /* fetch object array */
        if ($row = $result->fetch_row()) {
            $id = $row[0];
            $result->close();
            $balance = getLastBalance($id) - $amount;
            //$balance = 23;
            $sql = "INSERT INTO transactions (user_id, category, amount, description, balance) VALUES ($id, '$category', $amount, '$description', $balance)";
            $success = $mysqli->query($sql);
            if ($success) {
                $response['result'] = 'success';
            } else {
                $response['result'] = 'failed';
            }
        } else {
            $response['result'] = 'failed';
        }
    } else {
        $response['result'] = 'failed';
    }
}

function getLastBalance($id) {
    $query = "SELECT balance FROM transactions WHERE user_id=$id ORDER BY createdAt DESC LIMIT 1";
    echo $query;
    if ($result = $mysqli->query($query)) {
        /* fetch object array */
        if ($row = $result->fetch_row()) {
            return $row[0];
        }
    }
    return 0;
}

/**
 * Return list of categories 
 */

if(isset($_GET['categories'])){
    $token = $_GET['categories'];
    $query = "SELECT t.category 
    FROM transactions t, users u 
    WHERE u.token = '$token' 
    GROUP BY t.category";
    $rows = array();
    if ($result = $mysqli->query($query)) {
        while ($row = $result->fetch_row()) {
            $rows[] = $row[0];
        }
        $response = $rows;
        $result->close();
    }
}

/**
 * Sign up a new user
 */
if (isset($_POST['name'], $_POST['password'], $_POST['email'])) {
    $name = $_POST['name'];
    $password = substr(md5($_POST['password']), 0, 10);
    $email = $_POST['email'];
    $count = 0;

    //check name and email existence
    $query = "SELECT COUNT(*) FROM users WHERE username = '$name' OR email = '$email'";
    if ($result = $mysqli->query($query)) {
        /* fetch object array */
        if ($row = $result->fetch_row()) {
            $count = $row[0];
            $result->close();
        } else {
            $response['result'] = 'failed';
            die();
        }
    }

    //if there is no user with this name and email
    if ($count == 0) {
        $token = substr(md5($password . $name . $email), 0, 32);
        $sql = "INSERT INTO users (username, password, email, token) VALUES ('$name', '$password','$email','$token')";
        $success = $mysqli->query($sql);
        if ($success) {
            $response['result'] = 'success';
            $response['name'] = $name;
            $response['token'] = $token;
        } else {
            $response['result'] = 'failed';
        }
    } else {
        $response['result'] = 'exists';
    }
}

echo json_encode($response);

/* close connection */
$mysqli->close();
