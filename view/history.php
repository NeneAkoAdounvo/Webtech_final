<?php
session_start();
require_once '../db/config.php';

// Fetch history entries from database
$sql = "SELECT * FROM history ORDER BY history_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/highlanders.css">
    <style>
        .history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .history-card {
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

        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .history-image-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            overflow: hidden;
        }

        .history-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-bottom: 2px solid #f5c518;
        }

        .history-content {
            padding: 20px;
            flex-grow: 1;
        }

        .history-title {
            color: #f5c518;
            font-size: 1.8em;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .history-text {
            color: #cccccc;
            margin-bottom: 15px;
            line-height: 1.6;
            font-size: 1.1em;
        }

        .history-timeline {
            position: relative;
            padding: 20px 0;
            margin-bottom: 30px;
            text-align: center;
        }

        .timeline-marker {
            width: 100%;
            height: 2px;
            background-color: #f5c518;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            z-index: 1;
        }

        .timeline-title {
            background-color: #121212;
            color: #f5c518;
            padding: 10px 20px;
            display: inline-block;
            position: relative;
            z-index: 2;
            border: 2px solid #f5c518;
            border-radius: 30px;
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
            <a href="players.php" class="tab-link">Player Profiles</a>
            <a href="community.php" class="tab-link">Community</a>
            <a href="history.php" class="tab-link active">History</a>
            <a href="shop.php" class="tab-link">Shop</a>
            <a href="myorders.php" class="tab-link">My orders</a>


        </div>
    </div>

    <!-- Content -->
    <div class="container">
        <h1>Club History</h1>
        <div class="history-timeline">
            <div class="timeline-marker"></div>
            <h2 class="timeline-title">Our Journey Through Time</h2>
        </div>

        <div class="history-grid">
            <?php
            if ($result && $result->num_rows > 0) {
                while($history = $result->fetch_assoc()) {
                    ?>
                    <div class="history-card">
                        <div class="history-image-container">
                            <?php if (!empty($history['image_link'])): ?>
                                <img src="<?php echo htmlspecialchars('../' . $history['image_link']); ?>" 
                                     alt="<?php echo htmlspecialchars($history['title']); ?>" 
                                     class="history-image">
                            <?php else: ?>
                                <div class="default-image">
                                    <span>Highlanders FC History</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="history-content">
                            <h2 class="history-title"><?php echo htmlspecialchars($history['title']); ?></h2>
                            <div class="history-text">
                                <?php echo nl2br(htmlspecialchars($history['content'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='text-align: center; color: #f5c518;'>Our history is currently being written. Check back soon!</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>