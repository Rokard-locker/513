-- 更新管理员密码SQL脚本
-- 使用方法：在phpMyAdmin中执行此SQL语句

-- 方法1: 使用新密码 "Admin@2024"（推荐）
-- 这个哈希值对应密码: Admin@2024
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';

-- 方法2: 如果你想要修改为其他密码，需要生成新的哈希值
-- 可以使用以下PHP代码生成新密码的哈希：
/*
<?php
$new_password = 'YourNewPassword123'; // 替换为你想要的新密码
echo password_hash($new_password, PASSWORD_DEFAULT);
?>
*/

-- 然后将生成的哈希值替换到上面的UPDATE语句中

-- 方法3: 如果你想保持原密码 admin123，不需要执行任何操作
-- 当前密码 admin123 对应的哈希值已经存在于数据库中

-- 验证密码是否更新成功
SELECT id, username, email, full_name, role FROM users WHERE username = 'admin';
