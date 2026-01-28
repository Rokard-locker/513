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
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info-box { background: #e7f3ff; padding: 15px; border-radius: 4px; margin: 15px 0; border-left: 4px solid #2196F3; }
        .error-box { background: #ffe7e7; padding: 15px; border-radius: 4px; margin: 15px 0; border-left: 4px solid #dc3545; }
        .success-box { background: #e7f7e7; padding: 15px; border-radius: 4px; margin: 15px 0; border-left: 4px solid #28a745; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .sql-box { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; font-family: 'Courier New', monospace; overflow-x: auto; margin: 15px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verify Admin Password</h1>

        <?php
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                echo '<div class="error-box">Database Connection Failed: ' . $conn->connect_error . '</div>';
            } else {
                echo '<div class="success-box">✓ Database Connected Successfully</div>';
                
                // 获取管理员账户
                $stmt = $conn->prepare("SELECT id, username, email, password, role, status FROM users WHERE username = 'admin' OR email = 'admin@rokardsport.com'");
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo '<div class="error-box">✗ Admin account not found!</div>';
                } else {
                    $admin = $result->fetch_assoc();
                    
                    echo '<div class="info-box">';
                    echo '<h3>Current Admin Account:</h3>';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Value</th></tr>';
                    echo '<tr><td>ID</td><td>' . $admin['id'] . '</td></tr>';
                    echo '<tr><td>Username</td><td>' . htmlspecialchars($admin['username']) . '</td></tr>';
                    echo '<tr><td>Email</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
                    echo '<tr><td>Role</td><td>' . htmlspecialchars($admin['role']) . '</td></tr>';
                    echo '<tr><td>Status</td><td>' . htmlspecialchars($admin['status']) . '</td></tr>';
                    echo '<tr><td>Password Hash</td><td style="word-break: break-all; font-size: 11px; font-family: monospace;">' . htmlspecialchars($admin['password']) . '</td></tr>';
                    echo '</table>';
                    echo '</div>';
                    
                    // 测试多个可能的密码
                    echo '<h3>Password Tests:</h3>';
                    
                    $test_passwords = [
                        'admin123',
                        'Admin@2024',
                        'admin',
                        'password',
                        '12345678',
                    ];
                    
                    $found_password = false;
                    $correct_password = '';
                    
                    foreach ($test_passwords as $test_pwd) {
                        if (password_verify($test_pwd, $admin['password'])) {
                            echo '<div class="success-box">';
                            echo '<strong>✓ CORRECT!</strong> Password is: <code>' . htmlspecialchars($test_pwd) . '</code>';
                            echo '</div>';
                            $found_password = true;
                            $correct_password = $test_pwd;
                            break;
                        } else {
                            echo '<div class="error-box">';
                            echo '✗ Password <code>' . htmlspecialchars($test_pwd) . '</code> is WRONG';
                            echo '</div>';
                        }
                    }
                    
                    if (!$found_password) {
                        echo '<div class="error-box">';
                        echo '<h3>⚠️ None of the tested passwords worked!</h3>';
                        echo '<p>The password hash in the database does not match any common password.</p>';
                        echo '</div>';
                        
                        // 生成新的密码哈希
                        echo '<h3>🔧 Reset Password SQL:</h3>';
                        echo '<p>Execute this SQL to reset password to <code>admin123</code>:</p>';
                        
                        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
                        echo '<div class="sql-box">';
                        echo "-- Reset password to: admin123\n";
                        echo "UPDATE users SET \n";
                        echo "    password = '" . $new_hash . "',\n";
                        echo "    status = 'active',\n";
                        echo "    updated_at = NOW()\n";
                        echo "WHERE id = " . $admin['id'] . ";\n\n";
                        echo "-- Verify the update worked:\n";
                        echo "SELECT id, username, email, role, status FROM users WHERE id = " . $admin['id'] . ";";
                        echo '</div>';
                        
                        echo '<p><strong>After executing this SQL, login with:</strong></p>';
                        echo '<ul>';
                        echo '<li>Email: <code>admin@rokardsport.com</code></li>';
                        echo '<li>Password: <code>admin123</code></li>';
                        echo '</ul>';
                    } else {
                        echo '<div class="success-box">';
                        echo '<h3>✓ Login Credentials:</h3>';
                        echo '<ul>';
                        echo '<li>Email: <strong>' . htmlspecialchars($admin['email']) . '</strong></li>';
                        echo '<li>Password: <strong>' . htmlspecialchars($correct_password) . '</strong></li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                    
                    // 检查其他可能的问题
                    if ($admin['status'] !== 'active') {
                        echo '<div class="error-box">';
                        echo '⚠️ Account status is not "active": ' . htmlspecialchars($admin['status']);
                        echo '</div>';
                    }
                }
                
                $conn->close();
            }
        } catch (Exception $e) {
            echo '<div class="error-box">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>📝 Next Steps:</h3>
            <ol>
                <li>If password test failed, copy the SQL above</li>
                <li>Go to phpMyAdmin → Database: <code>if0_37528964_sport</code></li>
                <li>Click "SQL" tab and paste the SQL</li>
                <li>Execute it</li>
                <li>Try logging in again</li>
            </ol>
        </div>
    </div>
</body>
</html>
