<?php
// http://mysql.atw.hu
$response = array();

$mysqli = new mysqli("localhost", "expense", "Start123", "expense");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (isset($_GET['token'], $_GET['top'])) {
    $token = $_GET['token'];
    $top = $_GET['top'];
    $query = "SELECT amount,category,description,timestamp
    FROM transactions t, users u
    WHERE u.token = '$token'
    AND t.user_id = u.id
    ORDER BY timestamp DESC
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
/*
http://localhost/expense/api.php?name=istvan.szalontai12@gmail.com&password=kaki
 */
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
            $sql = "INSERT INTO transactions (user_id, category, amount, currency, description) VALUES ($id, '$category', '$amount','HUF','$description')";
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

if (isset($_POST['name'], $_POST['password'], $_POST['email'])) {
    $name = $_POST['name'];
    $password = substr(md5($_POST['password']), 0, 10);
    $email = $_POST['email'];
    $count = 0;

    //check name and email
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
