<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';

// Fetch community events
$communityEventsQuery = "SELECT ce.*, u.username as organizer_name 
                        FROM communityevents ce 
                        LEFT JOIN users u ON ce.organizer_id = u.user_id";
$communityEventsResult = $conn->query($communityEventsQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="community-events-section">
            <div class="card">
                <div class="section-header">
                    <h2>Community Events Management</h2>
                    <button class="add-button" onclick="addCommunityEvent()">Add New Event</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Organizer</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $communityEventsResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['event_id'] ?></td>
                                <td><?= htmlspecialchars($row['event_name']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= $row['event_date'] ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['organizer_name']) ?></td>
                                <td>
                                    <?php if ($row['image_link']): ?>
                                        <img src="../../<?= htmlspecialchars($row['image_link']) ?>" 
                                             alt="Event Image" style="max-width: 100px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="confirmDeleteEvent(<?= $row['event_id'] ?>)">Delete</button>
                                    <button onclick="editEvent(<?= $row['event_id'] ?>)">Edit</button>
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
function addCommunityEvent() {
    window.location.href = "../../functions/add_functions/add_community_event.php";
}

function confirmDeleteEvent(id) {
    if (confirm("Are you sure you want to delete this event?")) {
        window.location.href = "../../functions/delete_functions/delete_community_event.php?id=" + id;
    }
}

function editEvent(id) {
    window.location.href = "../../functions/edit_functions/edit_community_event.php?id=" + id;
}
</script>