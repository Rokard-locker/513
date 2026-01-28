<?php
/**
 * 测试登录功能
 * 用于诊断管理员登录问题
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
    <title>Test Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .test-result { padding: 15px; margin: 10px 0; border-radius: 4px; }
        .pass { background: #d4edda; border-left: 4px solid #28a745; }
        .fail { background: #f8d7da; border-left: 4px solid #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Test Admin Login</h1>
        
        <?php
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                echo '<div class="fail">Database Connection Failed: ' . $conn->connect_error . '</div>';
            } else {
                echo '<div class="pass">✓ Database Connected</div>';
                
                // 查找管理员账户
                $email = 'admin@rokardsport.com';
                $stmt = $conn->prepare("SELECT id, username, email, password, full_name, role, status FROM users WHERE email = ? OR username = 'admin'");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo '<div class="fail">✗ Admin account not found!</div>';
                    echo '<p>You need to create the admin account first.</p>';
                } else {
                    $user = $result->fetch_assoc();
                    echo '<div class="pass">✓ Admin account found</div>';
                    
                    echo '<h3>Account Info:</h3>';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Value</th></tr>';
                    echo '<tr><td>ID</td><td>' . $user['id'] . '</td></tr>';
                    echo '<tr><td>Username</td><td>' . htmlspecialchars($user['username']) . '</td></tr>';
                    echo '<tr><td>Email</td><td>' . htmlspecialchars($user['email']) . '</td></tr>';
                    echo '<tr><td>Role</td><td>' . htmlspecialchars($user['role']) . '</td></tr>';
                    echo '<tr><td>Status</td><td>' . htmlspecialchars($user['status']) . '</td></tr>';
                    echo '<tr><td>Password Hash</td><td style="word-break: break-all; font-size: 10px;">' . htmlspecialchars($user['password']) . '</td></tr>';
                    echo '</table>';
                    
                    // 测试密码
                    echo '<h3>Password Verification Tests:</h3>';
                    
                    $test_passwords = [
                        'admin123',
                        'Admin@2024',
                    ];
                    
                    $password_correct = false;
                    $correct_password = '';
                    
                    foreach ($test_passwords as $test_pwd) {
                        if (password_verify($test_pwd, $user['password'])) {
                            echo '<div class="pass">✓ Password "' . htmlspecialchars($test_pwd) . '" is CORRECT!</div>';
                            $password_correct = true;
                            $correct_password = $test_pwd;
                            break;
                        } else {
                            echo '<div class="fail">✗ Password "' . htmlspecialchars($test_pwd) . '" is WRONG</div>';
                        }
                    }
                    
                    // 检查状态问题
                    $issues = [];
                    if ($user['status'] !== 'active') {
                        $issues[] = "Status is '" . $user['status'] . "' (should be 'active')";
                    }
                    
                    if (!empty($issues)) {
                        echo '<h3>⚠️ Issues:</h3>';
                        foreach ($issues as $issue) {
                            echo '<div class="fail">' . htmlspecialchars($issue) . '</div>';
                        }
                    }
                    
                    // 如果密码不对，生成修复SQL
                    if (!$password_correct) {
                        echo '<h3>🔧 Fix Password:</h3>';
                        echo '<p>None of the test passwords worked. Here is SQL to reset password to "admin123":</p>';
                        
                        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
                        echo '<pre style="background: #2d2d2d; color: #fff; padding: 15px; border-radius: 4px; overflow-x: auto;">';
                        echo "-- Reset password to: admin123\n";
                        echo "UPDATE users SET \n";
                        echo "    password = '" . $new_hash . "',\n";
                        echo "    status = 'active',\n";
                        echo "    email_verified = 1,\n";
                        echo "    updated_at = NOW()\n";
                        echo "WHERE id = " . $user['id'] . ";\n";
                        echo '</pre>';
                        
                        echo '<p><strong>Or use username/email:</strong></p>';
                        echo '<pre style="background: #2d2d2d; color: #fff; padding: 15px; border-radius: 4px; overflow-x: auto;">';
                        echo "UPDATE users SET \n";
                        echo "    password = '" . $new_hash . "',\n";
                        echo "    status = 'active',\n";
                        echo "    email_verified = 1,\n";
                        echo "    updated_at = NOW()\n";
                        echo "WHERE username = 'admin' OR email = 'admin@rokardsport.com';";
                        echo '</pre>';
                    } else {
                        echo '<div class="pass">';
                        echo '<h3>✓ Login Credentials:</h3>';
                        echo '<ul>';
                        echo '<li>Email: <strong>admin@rokardsport.com</strong></li>';
                        echo '<li>Password: <strong>' . htmlspecialchars($correct_password) . '</strong></li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                }
                
                $conn->close();
            }
        } catch (Exception $e) {
            echo '<div class="fail">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>📋 Instructions:</h3>
            <ol>
                <li>If password tests failed, copy the SQL above</li>
                <li>Go to phpMyAdmin and select database: <strong>if0_37528964_sport</strong></li>
                <li>Execute the SQL statement</li>
                <li>Try logging in again</li>
            </ol>
        </div>
    </div>
</body>
</html>
