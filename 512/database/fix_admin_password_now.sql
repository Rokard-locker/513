-- 立即修复管理员密码
-- 这个SQL会重置密码为 admin123，并确保所有设置正确

-- 方法1：直接更新密码（推荐）
-- 这个哈希值对应密码：admin123
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    status = 'active',
    email_verified = 1,
    updated_at = NOW()
WHERE id = 1;

-- 或者使用用户名/邮箱
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    status = 'active',
    email_verified = 1,
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';

-- 验证更新结果
SELECT id, username, email, role, status, email_verified, 
       LEFT(password, 30) as password_preview
FROM users 
WHERE id = 1;
