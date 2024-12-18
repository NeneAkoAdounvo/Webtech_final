<?php
//require_once '../utils/session_check.php';
session_start();
require_once '../db/config.php';

// Fetch the most recent/upcoming match
$current_match_sql = "SELECT m.*, 
                           ht.team_name as home_team_name,
                           at.team_name as away_team_name
                    FROM matches m
                    LEFT JOIN teams ht ON m.home_team = ht.team_id 
                    LEFT JOIN teams at ON m.away_team = at.team_id
                    WHERE m.match_date >= CURDATE()
                    ORDER BY m.match_date ASC, m.match_time ASC
                    LIMIT 1";
$current_match_result = $conn->query($current_match_sql);

// Fetch upcoming matches
$upcoming_sql = "SELECT m.*, 
                       ht.team_name as home_team_name,
                       at.team_name as away_team_name
                FROM matches m
                LEFT JOIN teams ht ON m.home_team = ht.team_id 
                LEFT JOIN teams at ON m.away_team = at.team_id
                WHERE m.match_date >= CURDATE()
                ORDER BY m.match_date ASC, m.match_time ASC
                LIMIT 5";
$upcoming_result = $conn->query($upcoming_sql);

// Fetch recent matches with highlights
$recent_sql = "SELECT m.*, 
                     ht.team_name as home_team_name,
                     at.team_name as away_team_name,
                     mh.highlight_description,
                     mh.video_link
              FROM matches m
              LEFT JOIN teams ht ON m.home_team = ht.team_id 
              LEFT JOIN teams at ON m.away_team = at.team_id
              LEFT JOIN matchhighlights mh ON m.match_id = mh.match_id
              WHERE m.match_date < CURDATE()
              ORDER BY m.match_date DESC
              LIMIT 5";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Matchday - Highlanders FC</title>
   <link rel="stylesheet" href="../assets/css/Matchdaystyles.css">
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

       /* Featured Match Styling */
       .featured-match {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 15px;
           padding: 30px;
           margin-bottom: 40px;
           text-align: center;
       }

       .match-teams {
           display: flex;
           justify-content: center;
           align-items: center;
           gap: 20px;
           margin-bottom: 20px;
       }

       .team {
           font-size: 2em;
           color: #fff;
       }

       .vs {
           font-size: 1.5em;
           color: #f5c518;
       }

       .match-info {
           color: #fff;
           margin-bottom: 20px;
       }

       .match-score {
           font-size: 2.5em;
           color: #f5c518;
           margin: 20px 0;
       }

       /* Match Cards Grid */
       .matches-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
           gap: 20px;
           margin-top: 30px;
       }

       .match-card {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 10px;
           padding: 20px;
           transition: transform 0.3s ease;
       }

       .match-card:hover {
           transform: translateY(-5px);
       }

       .match-card-header {
           color: #f5c518;
           font-size: 1.2em;
           margin-bottom: 10px;
           text-transform: uppercase;
       }

       .match-card-teams {
           color: #fff;
           margin-bottom: 10px;
           font-size: 1.1em;
       }

       .match-card-info {
           color: #ccc;
           font-size: 0.9em;
       }

       /* Section Headers */
       .section-header {
           color: #f5c518;
           font-size: 1.8em;
           margin: 40px 0 20px;
           text-transform: uppercase;
           border-bottom: 2px solid #f5c518;
           padding-bottom: 10px;
       }

       /* Highlights Section */
       .highlights {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 15px;
           padding: 20px;
       }

       .highlight-item {
           border-bottom: 1px solid #333;
           padding: 15px 0;
       }

       .highlight-item:last-child {
           border-bottom: none;
       }

       .highlight-link {
           color: #f5c518;
           text-decoration: none;
           display: block;
           transition: color 0.3s ease;
       }

       .highlight-link:hover {
           color: #ffd700;
       }

       .highlight-description {
           color: #ccc;
           font-size: 0.9em;
           margin-top: 5px;
       }
   </style>
</head>
<body>
   <!-- Header -->
   <div class="header">
       <div class="logo">Highlanders FC</div>
       <div class="tabs">
           <a href="userHome.php" class="tab-link">Home</a>
           <a href="matchday.php" class="tab-link active">Matchday</a>
           <a href="latest.php" class="tab-link">Latest News</a>
           <a href="players.php" class="tab-link">Player Profiles</a>
           <a href="community.php" class="tab-link">Community</a>
           <a href="history.php" class="tab-link">History</a>
           <a href="shop.php" class="tab-link">Shop</a>
           <a href="myorders.php" class="tab-link">My orders</a>


       </div>
   </div>

   <!-- Content -->
   <div class="container">
       <h1>Matchday Center</h1>

       <!-- Featured/Current Match -->
       <?php if ($current_match_result && $current_match_result->num_rows > 0):
           $current_match = $current_match_result->fetch_assoc(); ?>
           <div class="featured-match">
               <div class="match-teams">
                   <span class="team"><?php echo htmlspecialchars($current_match['home_team_name']); ?></span>
                   <span class="vs">vs</span>
                   <span class="team"><?php echo htmlspecialchars($current_match['away_team_name']); ?></span>
               </div>
               <div class="match-info">
                   <?php echo date('F j, Y', strtotime($current_match['match_date'])); ?> at
                   <?php echo date('g:i A', strtotime($current_match['match_time'])); ?>
               </div>
               <?php if ($current_match['score']): ?>
                   <div class="match-score"><?php echo htmlspecialchars($current_match['score']); ?></div>
               <?php endif; ?>
               <div class="match-info">
                   Venue: <?php echo htmlspecialchars($current_match['stadium']); ?>
               </div>
           </div>
       <?php endif; ?>

       <!-- Upcoming Matches -->
       <h2 class="section-header">Upcoming Matches</h2>
       <div class="matches-grid">
           <?php
           if ($upcoming_result && $upcoming_result->num_rows > 0) {
               while($match = $upcoming_result->fetch_assoc()) {
                   if (isset($current_match) && $match['match_id'] == $current_match['match_id']) continue;
                   ?>
                   <div class="match-card">
                       <div class="match-card-header">Match #<?php echo $match['match_id']; ?></div>
                       <div class="match-card-teams">
                           <?php echo htmlspecialchars($match['home_team_name']); ?> vs 
                           <?php echo htmlspecialchars($match['away_team_name']); ?>
                       </div>
                       <div class="match-card-info">
                           Date: <?php echo date('F j, Y', strtotime($match['match_date'])); ?><br>
                           Time: <?php echo date('g:i A', strtotime($match['match_time'])); ?><br>
                           Venue: <?php echo htmlspecialchars($match['stadium']); ?>
                       </div>
                   </div>
                   <?php
               }
           } else {
               echo "<p style='text-align: center; color: #f5c518;'>No upcoming matches scheduled.</p>";
           }
           ?>
       </div>

       <!-- Recent Matches & Highlights -->
       <h2 class="section-header">Recent Matches & Highlights</h2>
       <div class="highlights">
           <?php
           if ($recent_result && $recent_result->num_rows > 0) {
               while($match = $recent_result->fetch_assoc()) {
                   ?>
                   <div class="highlight-item">
                       <a href="<?php echo htmlspecialchars($match['highlights_link']); ?>" class="highlight-link">
                           <?php echo htmlspecialchars($match['home_team_name']); ?> vs 
                           <?php echo htmlspecialchars($match['away_team_name']); ?>
                           (<?php echo htmlspecialchars($match['score']); ?>)
                       </a>
                       <div class="highlight-description">
                           <?php echo date('F j, Y', strtotime($match['match_date'])); ?> - 
                           <?php echo htmlspecialchars($match['highlight_description'] ?? 'Match Highlights'); ?>
                       </div>
                   </div>
                   <?php
               }
           } else {
               echo "<p style='text-align: center; color: #f5c518;'>No recent matches to display.</p>";
           }
           ?>
       </div>
   </div>
</body>
</html>