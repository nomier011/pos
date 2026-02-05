<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Coffee POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { color: #654321; position: relative; overflow: hidden; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; position: relative; z-index: 1; }
        .header { background: rgba(101, 67, 33, 0.9); color: #fff; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .user-info { text-align: right; }
        .user-info h1 { font-size: 24px; margin-bottom: 5px; }
        .user-info p { font-size: 14px; opacity: 0.9; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 3px 5px rgba(0,0,0,0.1); border-top: 4px solid #8B4513; }
        .stat-card i { font-size: 30px; color: #8B4513; margin-bottom: 10px; }
        .stat-card h3 { font-size: 28px; margin-bottom: 5px; }
        .stat-card p { color: #666; font-size: 14px; }
        .orders { background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 3px 5px rgba(0,0,0,0.1); }
        .section-title { color: #8B4513; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e6d3b8; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f5e6d3; padding: 12px; text-align: left; color: #654321; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .completed { background: #e8f5e9; color: #2e7d32; }
        .preparing { background: #e3f2fd; color: #1565c0; }
        .pending { background: #fff3e0; color: #ef6c00; }
        .quick-actions { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .action-btn { background: rgba(255, 255, 255, 0.9); border: 2px solid #e6d3b8; border-radius: 8