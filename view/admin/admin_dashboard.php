<?php include 'admin_header.php'; ?>

<?php// require_once '../../utils/session_check.php'; ?>

<?php
// Initialize variables to default values
$totalUsers = 0;
$totalOrders = 0;
$usersByRole = [];
$ordersByMonth = [];

try {
    // Fetch total users
    $result = $conn->query("SELECT COUNT(*) as total_users FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        $totalUsers = $row['total_users'];
    }

    // Fetch total orders
    $result = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
    if ($result) {
        $row = $result->fetch_assoc();
        $totalOrders = $row['total_orders'];
    }

    // Fetch users by role
    $result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    if ($result) {
        $usersByRole = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch monthly orders
    $result = $conn->query("
        SELECT 
            MONTH(order_date) as month, 
            YEAR(order_date) as year, 
            COUNT(*) as order_count, 
            SUM(total_amount) as total_revenue 
        FROM orders 
        GROUP BY year, month
        ORDER BY year, month
    ");
    if ($result) {
        $ordersByMonth = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    // Log the error or handle it appropriately
    error_log("Dashboard data fetch error: " . $e->getMessage());
    // You might want to set an error message to display to the admin
    $errorMessage = "Unable to fetch dashboard data. Please try again later.";
}
?>

<div class="dashboard-container">
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        
        <?php if (isset($errorMessage)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <section class="user-analytics-section">
            <div class="card full-width">
                <h2>User Analytics</h2>
                
                <div class="analytics-summary">
                    <div class="summary-cards">
                        <div class="summary-card">
                            <h3>Total Users</h3>
                            <p class="stat-number"><?php echo htmlspecialchars($totalUsers); ?></p>
                        </div>
                        <div class="summary-card">
                            <h3>Total Orders</h3>
                            <p class="stat-number"><?php echo htmlspecialchars($totalOrders); ?></p>
                        </div>
                    </div>
                </div>

                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h3>Users by Role</h3>
                        <canvas id="userRolesChart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <h3>Monthly Orders</h3>
                        <canvas id="monthlyOrdersChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userAnalyticsData = {
    totalUsers: <?php echo json_encode($totalUsers); ?>,
    totalOrders: <?php echo json_encode($totalOrders); ?>,
    usersByRole: <?php echo json_encode($usersByRole); ?>,
    ordersByMonth: <?php echo json_encode($ordersByMonth); ?>
};

document.addEventListener('DOMContentLoaded', function() {
    // Only create charts if we have data
    <?php if (!empty($usersByRole)): ?>
    // User Roles Pie Chart
    new Chart(document.getElementById('userRolesChart'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($usersByRole, 'role')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($usersByRole, 'count')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)', 
                    'rgba(54, 162, 235, 0.8)', 
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
    <?php endif; ?>

    <?php if (!empty($ordersByMonth)): ?>
    // Monthly Orders Bar Chart
    new Chart(document.getElementById('monthlyOrdersChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_map(function($order) {
                return $order['month'] . '/' . $order['year'];
            }, $ordersByMonth)); ?>,
            datasets: [
                {
                    label: 'Order Count',
                    data: <?php echo json_encode(array_column($ordersByMonth, 'order_count')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                },
                {
                    label: 'Total Revenue ($)',
                    data: <?php echo json_encode(array_column($ordersByMonth, 'total_revenue')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<style>
    <style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .main-content h1 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .card.full-width {
        width: 100%;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        padding: 30px;
    }

    .analytics-summary {
        margin-bottom: 30px;
    }

    .summary-cards {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .summary-card {
        flex: 1;
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-5px);
    }

    .summary-card h3 {
        margin-bottom: 10px;
        color: #666;
        font-size: 1.1em;
    }

    .stat-number {
        font-size: 2.5em;
        font-weight: bold;
        color: #333;
    }

    .charts-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .chart-wrapper {
        flex: 1;
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .chart-wrapper h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    @media (max-width: 1024px) {
        .summary-cards,
        .charts-container {
            flex-direction: column;
        }

        .summary-card,
        .chart-wrapper {
            width: 100%;
            margin-bottom: 20px;
        }
    }
    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        text-align: center;
    }
</style>