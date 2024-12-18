<?php
//require_once '../utils/session_check.php';
session_start();
require_once '../db/config.php';

// Fetch featured (latest) news
$featured_sql = "SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.user_id 
                ORDER BY published_at DESC 
                LIMIT 1";
$featured_result = $conn->query($featured_sql);

// Fetch other news
$news_sql = "SELECT n.*, u.username as author_name 
            FROM news n 
            LEFT JOIN users u ON n.author_id = u.user_id 
            ORDER BY published_at DESC 
            LIMIT 10";
$news_result = $conn->query($news_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Latest News - Highlanders FC</title>
   <link rel="stylesheet" href="../assets/css/LatestNewsstyles.css">
   <style>
       .container {
           padding: 40px 20px;
           max-width: 1200px;
           margin: 0 auto;
       }

       h1 {
           text-align: center;
           color: #f5c518;
           font-size: 2.5em;
           margin-bottom: 30px;
           text-transform: uppercase;
       }

       .featured-news {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 15px;
           overflow: hidden;
           margin-bottom: 40px;
           box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
       }

       .featured-image-container {
           position: relative;
           width: 100%;
           padding-top: 40%;
           overflow: hidden;
       }

       .featured-image {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           object-fit: cover;
       }

       .featured-content {
           padding: 30px;
       }

       .featured-title {
           color: #f5c518;
           font-size: 2em;
           margin-bottom: 15px;
       }

       .news-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
           gap: 30px;
           padding: 20px 0;
       }

       .news-card {
           background: rgba(0, 0, 0, 0.8);
           border: 2px solid #f5c518;
           border-radius: 15px;
           overflow: hidden;
           transition: transform 0.3s ease;
       }

       .news-card:hover {
           transform: translateY(-5px);
       }

       .news-image-container {
           position: relative;
           width: 100%;
           padding-top: 56.25%;
           overflow: hidden;
       }

       .news-image {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           object-fit: cover;
       }

       .news-content {
           padding: 20px;
       }

       .news-title {
           color: #f5c518;
           font-size: 1.5em;
           margin-bottom: 10px;
       }

       .news-meta {
           color: #888;
           font-size: 0.9em;
           margin-bottom: 15px;
       }

       .news-excerpt {
           color: #fff;
           margin-bottom: 15px;
           line-height: 1.6;
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
   </style>
</head>
<body>
   <!-- Header -->
   <div class="header">
       <div class="logo">Highlanders FC</div>
       <div class="tabs">
           <a href="userHome.php" class="tab-link">Home</a>
           <a href="matchday.php" class="tab-link">Matchday</a>
           <a href="latest.php" class="tab-link active">Latest News</a>
           <a href="players.php" class="tab-link">Player Profiles</a>
           <a href="community.php" class="tab-link">Community</a>
           <a href="history.php" class="tab-link">History</a>
           <a href="shop.php" class="tab-link">Shop</a>
           <a href="myorders.php" class="tab-link">My orders</a>

       </div>
   </div>

   <!-- Content -->
   <div class="container">
       <h1>Latest News</h1>

       <!-- Featured News -->
       <?php if ($featured_result && $featured_result->num_rows > 0): 
           $featured = $featured_result->fetch_assoc(); ?>
           <div class="featured-news">
               <div class="featured-image-container">
                   <?php if (!empty($featured['image_link'])): ?>
                       <img src="<?php echo htmlspecialchars('../' . $featured['image_link']); ?>" 
                            alt="<?php echo htmlspecialchars($featured['title']); ?>" 
                            class="featured-image">
                   <?php else: ?>
                       <div class="default-image">
                           <span>Highlanders FC News</span>
                       </div>
                   <?php endif; ?>
               </div>
               <div class="featured-content">
                   <h2 class="featured-title"><?php echo htmlspecialchars($featured['title']); ?></h2>
                   <p class="news-meta">
                       By <?php echo htmlspecialchars($featured['author_name']); ?> | 
                       <?php echo date('F j, Y', strtotime($featured['published_at'])); ?>
                   </p>
                   <div class="news-excerpt">
                       <?php echo nl2br(htmlspecialchars($featured['content'])); ?>
                   </div>
               </div>
           </div>
       <?php endif; ?>

       <!-- News Grid -->
       <h2 style="color: #f5c518; margin: 30px 0;">Recent Articles</h2>
       <div class="news-grid">
           <?php
           if ($news_result && $news_result->num_rows > 0) {
               while($news = $news_result->fetch_assoc()) {
                   if (isset($featured) && $news['news_id'] == $featured['news_id']) continue;
                   ?>
                   <div class="news-card">
                       <div class="news-image-container">
                           <?php if (!empty($news['image_link'])): ?>
                               <img src="<?php echo htmlspecialchars('../' . $news['image_link']); ?>" 
                                    alt="<?php echo htmlspecialchars($news['title']); ?>" 
                                    class="news-image">
                           <?php else: ?>
                               <div class="default-image">
                                   <span>Highlanders FC News</span>
                               </div>
                           <?php endif; ?>
                       </div>
                       <div class="news-content">
                           <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                           <p class="news-meta">
                               By <?php echo htmlspecialchars($news['author_name']); ?> | 
                               <?php echo date('F j, Y', strtotime($news['published_at'])); ?>
                           </p>
                           <div class="news-excerpt">
                               <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                           </div>
                       </div>
                   </div>
                   <?php
               }
           } else {
               echo "<p style='text-align: center; color: #f5c518;'>No news articles available at the moment.</p>";
           }
           ?>
       </div>
   </div>
</body>
</html>