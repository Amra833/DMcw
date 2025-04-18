<?php
require '../vendor/autoload.php';  // Adjust path if needed

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->urbanfood->feedbacks;

$feedbacks = $collection->find([], ['sort' => ['date' => -1]]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Feedback - UrbanFood</title>
  <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    h1 {
      text-align: center;
      color: #2d3e50;
      margin: 50px 0;
      font-size: 2.5rem;
    }

    .feedback-list {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
    }

    .feedback {
      background-color: #ffffff;
      padding: 20px;
      margin: 15px 0;
      width: 80%;
      max-width: 800px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .feedback:hover {
      transform: scale(1.02);
      box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    }

    .feedback p {
      margin: 10px 0;
      color: #333;
    }

    .feedback strong {
      color: #2d3e50;
    }

    .feedback hr {
      margin-top: 20px;
      border: 0;
      border-top: 1px solid #eee;
    }

    .back-btn {
      display: block;
      text-align: center;
      margin: 30px;
      padding: 10px 10px;
      background-color: #2d3e50;
      color: white;
      font-size: 1.2rem;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #3b4c63;
    }
  </style>
</head>
<body>
  <h1>Customer Feedback</h1>
  <div class="feedback-list">
    <?php foreach ($feedbacks as $feedback): ?>
      <div class="feedback">
        <p><strong>Name:</strong> <?= htmlspecialchars($feedback['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($feedback['email']) ?></p>
        <p><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($feedback['message'])) ?></p>
        <hr>
      </div>
    <?php endforeach; ?>
  </div>

  <a href="dashboard.php" class="back-btn">Back to Home</a>
</body>
</html>
