<?php
require '../vendor/autoload.php';  // Adjust path if needed

// MongoDB connection setup
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->urbanfood->feedbacks;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $result = $collection->insertOne([
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'date' => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount() > 0) {
        echo "<script>
            alert('Thank you for your feedback!');
            window.location.href = 'home.html'; 
        </script>";
    } else {
        echo "<script>
            alert('Error submitting feedback. Please try again later.');
            window.location.href = 'feedbacks.html';
        </script>";
    }
}
?>
