<?php
//require_once '../utils/session_check.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">Highlanders FC</div>
        <nav>
            <a href="#home">Home</a>
            <a href="#contact">Contact</a>
            <a href="logout.php" class="logout-link">Logout</a>
        </nav>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="userHome.php" class="tab-link">Home</a>
        <a href="matchday.php" class="tab-link">Matchday</a>
        <a href="latest.php" class="tab-link">Latest news</a>
        <a href="players.php" class="tab-link">Player Profiles</a>
        <a href="community.php" class="tab-link">Community</a>
        <a href="history.php" class="tab-link">History</a>
        <a href="shop.php" class="tab-link">Shop</a>
        <a href="myorders.php" class="tab-link">My orders</a>

    </div>
    
    
    <!-- Matchday Section -->
<div class="content active" id="matchday">
    <h2 class="section-title">Matchday Experience</h2>
    <div class="feature-box">
        <h3>Live Match Updates</h3>
        <p>Stay up-to-date with real-time scores, player stats, and match highlights.</p>
        <a href="matchday.php" class="matchday-button updates-button">
            <i class="icon-updates">🏆</i> Live Updates
        </a>
    </div>
    <div class="feature-box">
        <h3>Fan Engagement</h3>
        <p>See what Highlanders might be planning near you</p>
        <a href="community.php" class="matchday-button chat-button">
            <i class="icon-chat">💬</i> See events
        </a>
    </div>
</div>

<style>
    .matchday-button {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 10px;
        text-decoration: none;
        color: white;
        background-color: #f5c518;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        font-weight: bold;
        text-align: center;
    }

    .matchday-button:hover {
        background-color: #e6b500;
    }

    .matchday-button .icon-updates,
    .matchday-button .icon-chat {
        margin-right: 10px;
    }

    .updates-button {
        background-color: #007bff;
    }

    .updates-button:hover {
        background-color: #0056b3;
    }

    .chat-button {
        background-color: #28a745;
    }

    .chat-button:hover {
        background-color: #218838;
    }
</style>
    <!-- Player Profiles Section -->
    <div class="content" id="players">
        <h2 class="section-title">Player Profiles</h2>
        <div class="cards-container">
            <div class="card">
                <img src="player1.jpg" alt="Player 1">
                <div class="content">
                    <h3>Player 1</h3>
                    <p>Midfielder - 10 Goals this season</p>
                </div>
            </div>
            <div class="card">
                <img src="player2.jpg" alt="Player 2">
                <div class="content">
                    <h3>Player 2</h3>
                    <p>Goalkeeper - 15 Clean Sheets</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Section -->
    <div class="content" id="community">
        <h2 class="section-title">Community Board</h2>
        <div class="feature-box">
            <h3>Events Calendar</h3>
            <p>Find upcoming matches and community events here.</p>
        </div>
        <div class="feature-box">
            <h3>Forum</h3>
            <p>Discuss club-related topics and organize local meetups.</p>
        </div>
    </div>

    <!-- History Section -->
    <div class="content" id="history">
        <h2 class="section-title">Historical Timeline</h2>
        <p class="centered">Explore the history of Highlanders FC with an interactive timeline.</p>
    </div>

    <!-- Shop Section -->
    <div class="content" id="merchandise">
        <h2 class="section-title">Fan Merchandise Shop</h2>
        <div class="feature-box">
            <h3>Official Merchandise</h3>
            <p>Purchase jerseys, scarves, and other memorabilia here.</p>
        </div>
        <div class="feature-box">
            <h3>Custom Merchandise</h3>
            <p>Create your own designs with player names and numbers.</p>
        </div>
    </div>

    <div class="content" id="latest">
        <h2 class="section-title">The Latest Highlanders News</h2>
        <div class="cards-container">
            <div class="card" onclick="openModal('modal1')">
                <img src="news1.jpg" alt="News 1">
                <div class="content">
                    <h3>Highlanders Sign Star Player</h3>
                    <p>Get the details about the newest addition to the Highlanders squad.</p>
                </div>
            </div>
            <div class="card" onclick="openModal('modal2')">
                <img src="news2.jpg" alt="News 2">
                <div class="content">
                    <h3>Upcoming Match Preview</h3>
                    <p>Find out what to expect in the clash against rivals.</p>
                </div>
            </div>
            <div class="card" onclick="openModal('modal3')">
                <img src="news3.jpg" alt="News 3">
                <div class="content">
                    <h3>Community Event Success</h3>
                    <p>Highlights from the recent fan meet-and-greet session.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <div class="modal" id="modal1">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal1')">&times;</span>
            <h3>Highlanders Sign Star Player</h3>
            <p>Full story about the new star player, including stats and comments from the manager.</p>
        </div>
    </div>
    <div class="modal" id="modal2">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal2')">&times;</span>
            <h3>Upcoming Match Preview</h3>
            <p>Insights into the strategies and key players for the upcoming match.</p>
        </div>
    </div>
    <div class="modal" id="modal3">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal3')">&times;</span>
            <h3>Community Event Success</h3>
            <p>Details about the fan meet-and-greet session and its highlights.</p>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.content');

        function switchTab(tabId){
            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`.tab-button[onclick="switchTab('${tabId}')"]`).classList.add('active');
        }

        // Open Modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }
    
        // Close Modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
        // Close modal on outside click
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        }

    function loadContent(tabId, fileName) {
        fetch(fileName)
            .then(response => response.text())
            .then(data => {
                document.getElementById(tabId).innerHTML = data;
            });
    }


    </script>
</body>
</html>