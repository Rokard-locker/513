<?php
/**
 * 重置管理员密码工具
 * 访问此文件可以生成正确的密码哈希值
 */

// 数据库配置
define('DB_HOST', 'sql102.infinityfree.com');
define('DB_USER', 'if0_37528964');
define('DB_PASS', 'Huoying1');
define('DB_NAME', 'if0_37528964_sport');

// 设置要使用的密码
$password = 'admin123'; // 可以修改为你想要的密码

// 生成密码哈希
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #ff6b35;
            padding-bottom: 10px;
        }
        .password-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #ff6b35;
        }
        .sql-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #e55a2a;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Admin Password Tool</h1>
        
        <div class="info">
            <strong>Current Password:</strong> <span class="success"><?php echo htmlspecialchars($password); ?></span>
        </div>

        <div class="password-box">
            <h3>Generated Password Hash:</h3>
            <p style="word-break: break-all; font-family: monospace;"><?php echo htmlspecialchars($hashed_password); ?></p>
        </div>

        <h3>SQL Update Statement:</h3>
        <div class="sql-box">
UPDATE users 
SET password = '<?php echo $hashed_password; ?>',
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';
        </div>

        <h3>Steps to Reset Password:</h3>
        <ol>
            <li>Copy the SQL statement above</li>
            <li>Login to phpMyAdmin</li>
            <li>Select database: <strong>if0_37528964_sport</strong></li>
            <li>Click "SQL" tab</li>
            <li>Paste and execute the SQL statement</li>
            <li>Login with:
                <ul>
                    <li>Email: <strong>admin@rokardsport.com</strong></li>
                    <li>Password: <strong><?php echo htmlspecialchars($password); ?></strong></li>
                </ul>
            </li>
        </ol>

        <h3>Verify Password Hash:</h3>
        <?php
        // 验证哈希值
        if (password_verify($password, $hashed_password)) {
            echo '<p class="success">✓ Password hash is valid!</p>';
        } else {
            echo '<p style="color: red;">✗ Password hash verification failed!</p>';
        }
        ?>

        <h3>Check Current User:</h3>
        <?php
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                echo '<p style="color: red;">Database connection failed: ' . $conn->connect_error . '</p>';
            } else {
                $result = $conn->query("SELECT id, username, email, role, status FROM users WHERE username = 'admin' OR email = 'admin@rokardsport.com'");
                
                if ($result && $result->num_rows > 0) {
                    echo '<table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">';
                    echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $row['username'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . $row['role'] . '</td>';
                        echo '<td>' . $row['status'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p style="color: orange;">⚠ Admin user not found. You need to create it first.</p>';
                    echo '<div class="sql-box">';
                    echo "INSERT INTO users (username, email, password, full_name, role, status, email_verified, created_at) VALUES ('admin', 'admin@rokardsport.com', '" . $hashed_password . "', 'System Administrator', 'admin', 'active', 1, NOW());";
                    echo '</div>';
                }
                $conn->close();
            }
        } catch (Exception $e) {
            echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
        }
        ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>Change Password:</h3>
            <p>To use a different password, edit this file and change the <code>$password</code> variable, then refresh this page.</p>
        </div>
    </div>
</body>
</html>
