<?php
// http://mysql.atw.hu
$con = mysqli_connect("127.0.0.1", "expense", "Start123", "expense");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
} else {
    echo "<p>Success</p>";
}

if (isset($name, $password, $email)) {
    $password = md5($password);
    $sql = "INSERT INTO users (username,password, email) VALUES ('$name', '$password', '$email')";
    $success = mysqli_query($con, $sql);
    if ($success) {
        echo "<p>user inserted</p>";
    } else {
        echo "<p>user not inserted</p>";
    }
}

if (isset($_POST['category'], $_POST['amount'], $_POST['description'])) {
    $sql = "INSERT INTO transactions (user_id, category, amount, currency, description) VALUES (1, '$category', '$amount','HUF','$description')";
    $success = mysqli_query($con, $sql);
    if ($success) {
        echo "<p>expense inserted</p>";
    } else {
        echo "<p>expense not inserted</p>";
    }
}

mysqli_close($con);
