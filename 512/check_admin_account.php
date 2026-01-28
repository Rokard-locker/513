<?php
/**
 * 检查管理员账户和密码的工具
 * 访问此文件可以诊断管理员登录问题
 */

// 数据库配置
define('DB_HOST', 'sql102.infinityfree.com');
define('DB_USER', 'if0_37528964');
define('DB_PASS', 'Huoying1');
define('DB_NAME', 'if0_37528964_sport');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Admin Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
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
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info-box {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-left: 4px solid #2196F3;
        }
        .error-box {
            background: #ffe7e7;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        .success-box {
            background: #e7f7e7;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .sql-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Admin Account Diagnostic Tool</h1>

        <?php
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                echo '<div class="error-box">';
                echo '<strong>Database Connection Failed:</strong> ' . $conn->connect_error;
                echo '</div>';
            } else {
                echo '<div class="success-box">';
                echo '<strong>✓ Database Connected Successfully</strong>';
                echo '</div>';

                // 检查管理员账户
                $result = $conn->query("
                    SELECT id, username, email, password, full_name, role, status, email_verified, created_at, updated_at 
                    FROM users 
                    WHERE username = 'admin' OR email = 'admin@rokardsport.com'
                ");
                
                if ($result && $result->num_rows > 0) {
                    $admin = $result->fetch_assoc();
                    
                    echo '<div class="info-box">';
                    echo '<h2>✓ Admin Account Found</h2>';
                    echo '</div>';
                    
                    echo '<h3>Account Information:</h3>';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Value</th></tr>';
                    echo '<tr><td>ID</td><td>' . htmlspecialchars($admin['id']) . '</td></tr>';
                    echo '<tr><td>Username</td><td>' . htmlspecialchars($admin['username']) . '</td></tr>';
                    echo '<tr><td>Email</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
                    echo '<tr><td>Full Name</td><td>' . htmlspecialchars($admin['full_name']) . '</td></tr>';
                    echo '<tr><td>Role</td><td>' . htmlspecialchars($admin['role']) . '</td></tr>';
                    echo '<tr><td>Status</td><td>' . htmlspecialchars($admin['status']) . '</td></tr>';
                    echo '<tr><td>Email Verified</td><td>' . ($admin['email_verified'] ? 'Yes' : 'No') . '</td></tr>';
                    echo '<tr><td>Created At</td><td>' . htmlspecialchars($admin['created_at']) . '</td></tr>';
                    echo '<tr><td>Updated At</td><td>' . htmlspecialchars($admin['updated_at'] ?? 'NULL') . '</td></tr>';
                    echo '<tr><td>Password Hash</td><td style="word-break: break-all; font-size: 11px;">' . htmlspecialchars($admin['password']) . '</td></tr>';
                    echo '</table>';

                    // 检查状态
                    $issues = [];
                    if ($admin['status'] !== 'active') {
                        $issues[] = "Account status is '" . $admin['status'] . "' (should be 'active')";
                    }
                    if (!$admin['email_verified']) {
                        $issues[] = "Email is not verified";
                    }

                    // 测试密码
                    echo '<h3>Password Tests:</h3>';
                    $test_passwords = ['admin123', 'Admin@2024'];
                    foreach ($test_passwords as $test_pwd) {
                        if (password_verify($test_pwd, $admin['password'])) {
                            echo '<div class="success-box">';
                            echo '<strong>✓ Password "' . htmlspecialchars($test_pwd) . '" is CORRECT!</strong>';
                            echo '</div>';
                        } else {
                            echo '<div class="error-box">';
                            echo '<strong>✗ Password "' . htmlspecialchars($test_pwd) . '" is INCORRECT</strong>';
                            echo '</div>';
                        }
                    }

                    if (!empty($issues)) {
                        echo '<h3>⚠️ Issues Found:</h3>';
                        foreach ($issues as $issue) {
                            echo '<div class="warning">' . htmlspecialchars($issue) . '</div>';
                        }
                    }

                    // 生成修复SQL
                    echo '<h3>🔧 Fix SQL:</h3>';
                    echo '<p>If password tests failed, execute this SQL to reset password to "admin123":</p>';
                    
                    $correct_hash = password_hash('admin123', PASSWORD_DEFAULT);
                    echo '<div class="sql-box">';
                    echo "-- Reset password to: admin123\n";
                    echo "UPDATE users SET \n";
                    echo "    password = '" . $correct_hash . "',\n";
                    echo "    status = 'active',\n";
                    echo "    email_verified = 1,\n";
                    echo "    updated_at = NOW()\n";
                    echo "WHERE id = " . $admin['id'] . ";\n\n";
                    echo "-- Or use username/email:\n";
                    echo "UPDATE users SET \n";
                    echo "    password = '" . $correct_hash . "',\n";
                    echo "    status = 'active',\n";
                    echo "    email_verified = 1,\n";
                    echo "    updated_at = NOW()\n";
                    echo "WHERE username = 'admin' OR email = 'admin@rokardsport.com';";
                    echo '</div>';

                } else {
                    echo '<div class="error-box">';
                    echo '<h2>✗ Admin Account NOT Found</h2>';
                    echo '<p>The admin account does not exist in the database. You need to create it.</p>';
                    echo '</div>';

                    echo '<h3>🔧 Create Admin Account SQL:</h3>';
                    $correct_hash = password_hash('admin123', PASSWORD_DEFAULT);
                    echo '<div class="sql-box">';
                    echo "-- Create admin account with password: admin123\n";
                    echo "INSERT INTO users (username, email, password, full_name, role, status, email_verified, created_at) VALUES\n";
                    echo "('admin', 'admin@rokardsport.com', '" . $correct_hash . "', 'System Administrator', 'admin', 'active', 1, NOW());";
                    echo '</div>';
                }
                
                $conn->close();
            }
        } catch (Exception $e) {
            echo '<div class="error-box">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>📝 Next Steps:</h3>
            <ol>
                <li>Copy the SQL statement above (if needed)</li>
                <li>Login to phpMyAdmin</li>
                <li>Select database: <strong>if0_37528964_sport</strong></li>
                <li>Execute the SQL statement</li>
                <li>Try logging in again with:
                    <ul>
                        <li>Email: <strong>admin@rokardsport.com</strong></li>
                        <li>Password: <strong>admin123</strong></li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>
</body>
</html>
