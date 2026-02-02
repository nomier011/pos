<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Coffee POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f8f5f0;
            color: #5c3a21;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: #8b4513;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(139, 69, 19, 0.2);
        }

        .user-info {
            text-align: right;
        }

        .user-info h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .user-info p {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
            border-top: 4px solid #8b4513;
        }

        .stat-card i {
            font-size: 30px;
            color: #8b4513;
            margin-bottom: 10px;
        }

        .stat-card h3 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #666;
            font-size: 14px;
        }

        /* Orders */
        .orders {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
        }

        .section-title {
            color: #8b4513;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e6d3b8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f5e6d3;
            padding: 12px;
            text-align: left;
            color: #5c3a21;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .completed { background: #e8f5e9; color: #2e7d32; }
        .preparing { background: #e3f2fd; color: #1565c0; }
        .pending { background: #fff3e0; color: #ef6c00; }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-btn {
            background: white;
            border: 2px solid #e6d3b8;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #5c3a21;
        }

        .action-btn:hover {
            background: #8b4513;
            color: white;
            border-color: #8b4513;
            transform: translateY(-3px);
        }

        .action-btn i {
            font-size: 24px;
            display: block;
            margin-bottom: 8px;
        }

        .action-btn span {
            font-weight: bold;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 14px;
            border-top: 1px solid #e6d3b8;
            margin-top: 30px;
        }

        .logout {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #8b4513;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            text-decoration: none;
        }

        .logout:hover {
            background: #5c3a21;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats, .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .user-info {
                text-align: center;
                margin-top: 10px;
            }
        }

        @media (max-width: 480px) {
            .stats, .quick-actions {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1><i class="fas fa-coffee"></i> Coffee Shop POS</h1>
                <p>Point of Sale System</p>
            </div>
            <div class="user-info">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p><i class="fas fa-user"></i> Barista | <?php echo date('l, F j, Y'); ?></p>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>$0</h3>
                <p>Today's Sales</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-receipt"></i>
                <h3>0</h3>
                <p>Orders Today</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>0</h3>
                <p>Customers Served</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <h3>$0</h3>
                <p>Monthly Revenue</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders">
            <div class="section-title">
                <h2><i class="fas fa-history"></i> Recent Orders</h2>
                <button style="background: #8b4513; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                    View All
                </button>
            </div>
            
            <table>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #888;">
                        <i class="fas fa-coffee" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                        No orders yet today
                    </td>
                </tr>
            </table>
        </div>

        <!-- Quick Actions -->
        <div class="section-title">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
        </div>
        <div class="quick-actions">
            <div class="action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>New Order</span>
            </div>
            <div class="action-btn">
                <i class="fas fa-print"></i>
                <span>Print Receipt</span>
            </div>
            <div class="action-btn">
                <i class="fas fa-utensils"></i>
                <span>Manage Menu</span>
            </div>
            <div class="action-btn">
                <i class="fas fa-chart-pie"></i>
                <span>View Reports</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© <?php echo date('Y'); ?> Coffee Shop POS System</p>
            <p>All rights reserved | System Status: <span style="color: green; font-weight: bold;">Online</span></p>
        </div>
    </div>

    <!-- Logout Button -->
    <a href="logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i>
    </a>

    <script>
        // Simple time update
        function updateTime() {
            const now = new Date();
            const timeElement = document.querySelector('.user-info p');
            const dateStr = now.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            
            document.querySelector('.user-info p').innerHTML = 
                `<i class="fas fa-user"></i> Barista | ${dateStr} ${timeStr}`;
        }

        // Update time every minute
        updateTime();
        setInterval(updateTime, 60000);

        // Make quick action buttons clickable
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.querySelector('span').textContent;
                alert(`Opening: ${action}`);
            });
        });
    </script>
</body>
</html>