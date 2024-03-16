<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, email, password FROM register WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row["password"];
        
        //Debugging: Output hashed password from the database
        echo "Stored Password: ". $stored_password . "<br>";

        // Verify the entered password against the stored hash
        if (password_verify($password, $stored_password)) {
            // Password is correct, create a session for the user
            session_start();
            $_SESSION["user_id"] = $row["id"];
            header("Location: index.php"); // Redirect to a dashboard page upon successful login
            exit();
        } else {
            echo "Incorrect password";
        }
    } else {
        // echo "Email not found";
        header("Location: index.php"); // Redirect to a dashboard page upon successful login
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
