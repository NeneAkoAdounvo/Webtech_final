<?php
//require_once '../utils/session_check.php';
session_start();
require_once '../db/config.php';

// Fetch all players with their team information
$sql = "SELECT p.*, t.team_name 
       FROM players p
       LEFT JOIN teams t ON p.team_id = t.team_id
       ORDER BY p.position, p.name";
$result = $conn->query($sql);

// Group players by position for organized display
$players_by_position = [
   'Goalkeeper' => [],
   'Defender' => [],
   'Midfielder' => [],
   'Forward' => []
];

if ($result && $result->num_rows > 0) {
   while ($player = $result->fetch_assoc()) {
       $players_by_position[$player['position']][] = $player;
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Players - Highlanders FC</title>
   <link rel="stylesheet" href="../assets/css/Playersstyles.css">
   <style>
       .container {
           max-width: 1200px;
           margin: 0 auto;
           padding: 40px 20px;
       }

       h1 {
           text-align: center;
           color: #f5c518;
           font-size: 2.5em;
           margin-bottom: 30px;
           text-transform: uppercase;
       }

       .position-section {
           margin-bottom: 40px;
       }

       .position-title {
           color: #f5c518;
           font-size: 1.8em;
           margin: 20px 0;
           padding-bottom: 10px;
           border-bottom: 2px solid #f5c518;
       }

       .players-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
           gap: 30px;
           padding: 20px 0;
       }

       .player-card {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 15px;
           overflow: hidden;
           transition: transform 0.3s ease;
           text-align: center;
       }

       .player-card:hover {
           transform: translateY(-5px);
           box-shadow: 0 5px 15px rgba(245, 197, 24, 0.3);
       }

       .player-image-container {
           position: relative;
           width: 100%;
           padding-top: 100%; /* Square aspect ratio */
           overflow: hidden;
           background: #2a2a2a;
       }

       .player-image {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           object-fit: cover;
       }

       .default-image {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           display: flex;
           align-items: center;
           justify-content: center;
           color: #f5c518;
           font-size: 3em;
           background: #2a2a2a;
       }

       .player-info {
           padding: 20px;
       }

       .player-name {
           color: #f5c518;
           font-size: 1.5em;
           margin-bottom: 10px;
       }

       .player-details {
           color: #fff;
           margin-bottom: 5px;
       }

       .player-nationality {
           color: #ccc;
           font-style: italic;
           margin-top: 10px;
       }

       .position-indicator {
           background: #f5c518;
           color: #000;
           padding: 5px 10px;
           border-radius: 15px;
           display: inline-block;
           margin-bottom: 10px;
           font-weight: bold;
           text-transform: uppercase;
           font-size: 0.8em;
       }
   </style>
</head>
<body>
   <!-- Header -->
   <div class="header">
       <div class="logo">Highlanders FC</div>
       <div class="tabs">
           <a href="userHome.php" class="tab-link">Home</a>
           <a href="matchday.php" class="tab-link">Matchday</a>
           <a href="latest.php" class="tab-link">Latest News</a>
           <a href="players.php" class="tab-link active">Player Profiles</a>
           <a href="community.php" class="tab-link">Community</a>
           <a href="history.php" class="tab-link">History</a>
           <a href="shop.php" class="tab-link">Shop</a>
           <a href="myorders.php" class="tab-link">My orders</a>


       </div>
   </div>

   <!-- Content -->
   <div class="container">
       <h1>Player Profiles</h1>

       <?php
       foreach ($players_by_position as $position => $players) {
           if (!empty($players)) {
               ?>
               <div class="position-section">
                   <h2 class="position-title"><?php echo $position; ?>s</h2>
                   <div class="players-grid">
                       <?php foreach ($players as $player) { ?>
                           <div class="player-card">
                               <div class="player-image-container">
                                   <?php if (!empty($player['profile_image'])): ?>
                                       <img src="<?php echo htmlspecialchars('../' . $player['profile_image']); ?>" 
                                            alt="<?php echo htmlspecialchars($player['name']); ?>" 
                                            class="player-image">
                                   <?php else: ?>
                                       <div class="default-image">
                                           <span><?php echo substr($player['name'], 0, 1); ?></span>
                                       </div>
                                   <?php endif; ?>
                               </div>
                               <div class="player-info">
                                   <span class="position-indicator"><?php echo $player['position']; ?></span>
                                   <h3 class="player-name"><?php echo htmlspecialchars($player['name']); ?></h3>
                                   <p class="player-details">Age: <?php echo $player['age']; ?></p>
                                   <p class="player-details">Team: <?php echo htmlspecialchars($player['team_name']); ?></p>
                                   <p class="player-nationality">
                                       <?php echo htmlspecialchars($player['nationality']); ?>
                                   </p>
                               </div>
                           </div>
                       <?php } ?>
                   </div>
               </div>
               <?php
           }
       }
       
       if ($result->num_rows === 0) {
           echo "<p style='text-align: center; color: #f5c518;'>No players available to display.</p>";
       }
       ?>
   </div>
</body>
</html>