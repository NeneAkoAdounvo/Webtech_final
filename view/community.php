<?php
session_start();
require_once '../db/config.php';
//require_once '../utils/session_check.php';



// Basic query to fetch events
$sql = "SELECT * FROM communityevents ORDER BY event_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Community - Highlanders FC</title>
   <link rel="stylesheet" href="../assets/css/Communitystyles.css">
   <script src="../assets/js/script.js" defer></script>
   <style>
       .events-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
           gap: 30px;
           padding: 20px;
           max-width: 1200px;
           margin: 0 auto;
       }

       .event-card {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 15px;
           overflow: hidden;
           transition: transform 0.3s ease;
           height: 100%;
           display: flex;
           flex-direction: column;
           box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
       }

       .event-card:hover {
           transform: translateY(-5px);
           box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
       }

       .event-image-container {
           position: relative;
           width: 100%;
           padding-top: 56.25%; /* 16:9 Aspect Ratio */
           overflow: hidden;
       }

       .event-image {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           object-fit: cover;
           border-bottom: 2px solid #f5c518;
       }

       .event-content {
           padding: 20px;
           flex-grow: 1;
       }

       .event-title {
           color: #f5c518;
           font-size: 1.8em;
           margin-bottom: 15px;
           font-weight: bold;
           text-transform: uppercase;
       }

       .event-info {
           color: #ffffff;
           margin-bottom: 12px;
           font-size: 1.1em;
       }

       .event-info strong {
           color: #f5c518;
       }

       .event-description {
           color: #cccccc;
           margin-bottom: 15px;
           line-height: 1.6;
           font-size: 1.1em;
       }

       /* Main container styling */
       .container {
           padding: 40px 20px;
           max-width: 1400px;
           margin: 0 auto;
       }

       h1 {
           text-align: center;
           color: #f5c518;
           font-size: 2.5em;
           margin-bottom: 20px;
           text-transform: uppercase;
       }

       .container > p {
           text-align: center;
           color: #f5c518;
           font-size: 1.2em;
           margin-bottom: 40px;
       }

       .default-image {
           background: #2a2a2a;
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           display: flex;
           align-items: center;
           justify-content: center;
           color: #f5c518;
           font-size: 1.2em;
       }

       /* Add Event Button Styling */
       .add-event-button {
           display: inline-block;
           padding: 12px 25px;
           background-color: #f5c518;
           color: #121212;
           text-decoration: none;
           border-radius: 8px;
           font-weight: bold;
           transition: all 0.3s ease;
           text-transform: uppercase;
           letter-spacing: 1px;
           margin-top: 30px;
       }

       .add-event-button:hover {
           background-color: #ffd700;
           transform: translateY(-2px);
           box-shadow: 0 4px 8px rgba(245, 197, 24, 0.3);
       }

       .button-container {
           text-align: center;
           margin-top: 20px;
       }
   </style>
</head>
<body>
   <div class="header">
       <div class="logo">Highlanders FC</div>
       <div class="tabs">
           <a href="userHome.php" class="tab-link">Home</a>
           <a href="matchday.php" class="tab-link">Matchday</a>
           <a href="latest.php" class="tab-link">Latest News</a>
           <a href="players.php" class="tab-link">Player Profiles</a>
           <a href="community.php" class="tab-link active">Community</a>
           <a href="history.php" class="tab-link">History</a>
           <a href="shop.php" class="tab-link">Shop</a>
           <a href="myorders.php" class="tab-link">My orders</a>


       </div>
   </div>

   <div class="container">
       <h1>Community Events</h1>
       <p>Join us at these upcoming community events and be part of the Highlanders family!</p>

       <div class="events-grid">
           <?php
           if ($result && $result->num_rows > 0) {
               while($event = $result->fetch_assoc()) {
                   ?>
                   <div class="event-card">
                       <div class="event-image-container">
                           <?php if (!empty($event['image_link'])): ?>
                               <img src="<?php echo htmlspecialchars('../' . $event['image_link']); ?>" 
                                    alt="<?php echo htmlspecialchars($event['event_name']); ?>" 
                                    class="event-image">
                           <?php else: ?>
                               <div class="default-image">
                                   <span>Highlanders FC Event</span>
                               </div>
                           <?php endif; ?>
                       </div>
                       
                       <div class="event-content">
                           <h2 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h2>
                           <p class="event-info">
                               <strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                           </p>
                           <p class="event-info">
                               <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                           </p>
                           <p class="event-description">
                               <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                           </p>
                       </div>
                   </div>
                   <?php
               }
           } else {
               echo "<p>No community events scheduled at the moment.</p>";
           }
           ?>
       </div>

       <?php if (isset($_SESSION['roleID']) && $_SESSION['roleID'] == 2): ?>
       <div class="button-container">
           <a href="admin/add_community_event.php" class="add-event-button">
               Add New Event
           </a>
       </div>
       <?php endif; ?>
   </div>
</body>
</html>