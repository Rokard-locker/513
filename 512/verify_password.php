<?php
/**
 * 验证管理员密码的工具
 * 测试数据库中存储的密码哈希值
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
    <title>Verify Admin Password</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; font-weight: bold; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .sql-box { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; font-family: monospace; overflow-x: auto; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verify Admin Password</h1>
        
        <?php
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                echo '<div class="error">Database Connection Failed: ' . $conn->connect_error . '</div>';
            } else {
                echo '<div class="success">✓ Database Connected</div>';
                
                // 获取管理员账户
                $stmt = $conn->prepare("SELECT id, username, email, password, role, status, email_verified FROM users WHERE id = 1");
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo '<div class="error">✗ Admin account (ID=1) not found!</div>';
                } else {
                    $admin = $result->fetch_assoc();
                    
                    echo '<div class="info">';
                    echo '<h3>Current Admin Account (ID: ' . $admin['id'] . '):</h3>';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Value</th></tr>';
                    echo '<tr><td>Username</td><td>' . htmlspecialchars($admin['username']) . '</td></tr>';
                    echo '<tr><td>Email</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
                    echo '<tr><td>Role</td><td>' . htmlspecialchars($admin['role']) . '</td></tr>';
                    echo '<tr><td>Status</td><td>' . htmlspecialchars($admin['status']) . '</td></tr>';
                    echo '<tr><td>Email Verified</td><td>' . ($admin['email_verified'] ? 'Yes (1)' : 'No (0)') . '</td></tr>';
                    echo '<tr><td>Password Hash</td><td style="word-break: break-all; font-size: 11px;">' . htmlspecialchars($admin['password']) . '</td></tr>';
                    echo '</table>';
                    echo '</div>';
                    
                    // 测试密码
                    echo '<h3>Password Tests:</h3>';
                    
                    $test_passwords = ['admin123', 'Admin@2024'];
                    $found_match = false;
                    
                    foreach ($test_passwords as $pwd) {
                        if (password_verify($pwd, $admin['password'])) {
                            echo '<div class="success">';
                            echo '✓ Password "' . htmlspecialchars($pwd) . '" is CORRECT!';
                            echo '</div>';
                            $found_match = true;
                            break;
                        } else {
                            echo '<div class="error">';
                            echo '✗ Password "' . htmlspecialchars($pwd) . '" is WRONG';
                            echo '</div>';
                        }
                    }
                    
                    if (!$found_match) {
                        echo '<div class="error">';
                        echo '<h3>⚠️ None of the test passwords matched!</h3>';
                        echo '<p>The password hash in database does not match any known password.</p>';
                        echo '</div>';
                        
                        // 生成新的正确哈希值
                        $correct_hash = password_hash('admin123', PASSWORD_DEFAULT);
                        
                        echo '<h3>🔧 Fix SQL:</h3>';
                        echo '<p>Execute this SQL to reset password to "admin123":</p>';
                        echo '<div class="sql-box">';
                        echo "-- Reset password to: admin123\n";
                        echo "UPDATE users SET \n";
                        echo "    password = '" . $correct_hash . "',\n";
                        echo "    status = 'active',\n";
                        echo "    email_verified = 1,\n";
                        echo "    updated_at = NOW()\n";
                        echo "WHERE id = 1;\n\n";
                        echo "-- Or use this (by username/email):\n";
                        echo "UPDATE users SET \n";
                        echo "    password = '" . $correct_hash . "',\n";
                        echo "    status = 'active',\n";
                        echo "    email_verified = 1,\n";
                        echo "    updated_at = NOW()\n";
                        echo "WHERE username = 'admin' OR email = 'admin@rokardsport.com';";
                        echo '</div>';
                        
                        echo '<div class="info">';
                        echo '<h3>📋 Steps:</h3>';
                        echo '<ol>';
                        echo '<li>Copy the SQL statement above</li>';
                        echo '<li>Go to phpMyAdmin</li>';
                        echo '<li>Select database: <strong>if0_37528964_sport</strong></li>';
                        echo '<li>Click "SQL" tab</li>';
                        echo '<li>Paste and execute the SQL</li>';
                        echo '<li>Try logging in with: <strong>admin@rokardsport.com</strong> / <strong>admin123</strong></li>';
                        echo '</ol>';
                        echo '</div>';
                    } else {
                        echo '<div class="success">';
                        echo '<h3>✓ Password is correct!</h3>';
                        echo '<p>If you still cannot login, the issue might be in the login page code.</p>';
                        echo '</div>';
                    }
                }
                
                $conn->close();
            }
        } catch (Exception $e) {
            echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>
</html>
