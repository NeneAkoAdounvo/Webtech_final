<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';


// Fetch news articles
$newsQuery = "SELECT n.*, u.username as author_name 
              FROM news n 
              LEFT JOIN users u ON n.author_id = u.user_id";
$newsResult = $conn->query($newsQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="news-section">
            <div class="card">
                <div class="section-header">
                    <h2>News Management</h2>
                    <button class="add-button" onclick="addNews()">Add News Article</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>News ID</th>
                            <th>Title</th>
                            <th>Content Preview</th>
                            <th>Author</th>
                            <th>Published At</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $newsResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['news_id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars(substr($row['content'], 0, 100)) ?>...</td>
                                <td><?= htmlspecialchars($row['author_name']) ?></td>
                                <td><?= $row['published_at'] ?></td>
                                <td>
                                    <?php if ($row['image_link']): ?>
                                        <img src="../../<?= htmlspecialchars($row['image_link']) ?>" 
                                             alt="News Image" style="max-width: 100px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="confirmDeleteNews(<?= $row['news_id'] ?>)">Delete</button>
                                    <button onclick="editNews(<?= $row['news_id'] ?>)">Edit</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
function addNews() {
    window.location.href = "../../functions/add_functions/add_news.php";
}

function confirmDeleteNews(id) {
    if (confirm("Are you sure you want to delete this news article?")) {
        window.location.href = "../../functions/delete_functions/delete_news.php?id=" + id;
    }
}

function editNews(id) {
    window.location.href = "../../functions/edit_functions/edit_news.php?id=" + id;
}
</script>