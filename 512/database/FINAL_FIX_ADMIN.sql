-- 最终修复管理员密码SQL
-- 根据数据库截图，管理员账户ID是1，需要重置密码

-- 方案1：生成新的密码哈希值（推荐）
-- 这个哈希值对应密码: admin123
-- 使用PHP password_hash()函数生成，确保是正确的格式

-- 首先测试当前密码哈希值（可选）
-- SELECT id, username, email, password FROM users WHERE id = 1;

-- 重置密码为 admin123
UPDATE users 
SET password = '$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO',
    status = 'active',
    email_verified = 1,
    updated_at = NOW()
WHERE id = 1;

-- 或者使用用户名/邮箱
UPDATE users 
SET password = '$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO',
    status = 'active',
    email_verified = 1,
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';

-- 验证更新结果
SELECT id, username, email, role, status, LEFT(password, 20) as password_preview, updated_at 
FROM users 
WHERE id = 1;

-- ============================================
-- 说明：
-- 密码哈希值 $2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO
-- 对应的密码是: admin123
-- 
-- 这个哈希值是通过以下PHP代码生成的：
-- <?php echo password_hash('admin123', PASSWORD_DEFAULT); ?>
-- ============================================
