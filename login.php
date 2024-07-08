<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables
    $uname = $_POST['uname'];
    $upswd = $_POST['upswd'];

    // Validate input
    if (empty($uname) || empty($upswd)) {
        echo "All fields are required";
        exit;
    }

    // Sanitize input to prevent SQL Injection
    $uname = htmlspecialchars($uname);

    // Create connection
    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "webpage";

    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
    } else {
        // Prepare SQL statement to check if username exists
        $stmt = $conn->prepare("SELECT * FROM login WHERE uname = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Username already exists";
            $stmt->close();
            $conn->close();
            exit;
        }

        // Prepare SQL statement to insert new user with plaintext password
        $stmt = $conn->prepare("INSERT INTO login (uname, upswd) VALUES (?, ?)");

        if ($stmt === false) {
            echo "Prepare statement failed: " . $conn->error;
            $conn->close();
            exit;
        }

        // Insert plaintext password directly (not recommended)
        $stmt->bind_param("ss", $uname, $upswd);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>
