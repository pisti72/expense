<?php
// http://mysql.atw.hu
$response = array();

$mysqli = new mysqli("localhost", "expense", "Start123", "expense");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (isset($_GET['id'], $_GET['top'])) {
    $id = $_GET['id'];
    $top = $_GET['top'];
    $query = "SELECT amount,category,description,timestamp
    FROM transactions
    WHERE user_id = $id
    ORDER BY timestamp DESC
    LIMIT $top";
    $rows = array();
    if ($result = $mysqli->query($query)) {

        /* fetch object array */
        while ($row = $result->fetch_row()) {
            $rows[] = $row;
            //printf("%s (%s)\n", $row[0], $row[1]);
        }

        /* free result set */
        $response = $rows;
        $result->close();
    }

}

if (isset($_POST['category'], $_POST['amount'], $_POST['description'])) {
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $sql = "INSERT INTO transactions (user_id, category, amount, currency, description) VALUES (1, '$category', '$amount','HUF','$description')";
    $success = $mysqli->query($sql);
    if ($success) {
        $response['result'] = 'success';
    } else {
        $response['result'] = 'failed';
    }
}

if (isset($_POST['name'], $_POST['password'], $_POST['email'])) {
    $name = $_POST['name'];
    $password = md5($_POST['password']);
    $email = $_POST['email'];

    $sql = "INSERT INTO users (username, password, email) VALUES ('$name', '$password','$email')";
    $success = $mysqli->query($sql);
    if ($success) {
        $response['result'] = 'success';
    } else {
        $response['result'] = 'failed';
    }
}

echo json_encode($response);

/* close connection */
$mysqli->close();
