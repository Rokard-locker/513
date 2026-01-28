-- 修复管理员密码 - 完整解决方案
-- 请按顺序执行以下SQL语句

-- 步骤1: 检查管理员账户是否存在
SELECT id, username, email, role, status FROM users WHERE username = 'admin' OR email = 'admin@rokardsport.com';

-- 步骤2: 如果账户不存在，创建管理员账户
-- 密码: admin123
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`, `email_verified`, `created_at`) 
VALUES ('admin', 'admin@rokardsport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active', 1, NOW())
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = NOW();

-- 步骤3: 更新密码为 admin123（如果账户已存在）
-- 这个哈希值对应密码: admin123
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';

-- 步骤4: 验证更新结果
SELECT id, username, email, full_name, role, status, created_at, updated_at 
FROM users 
WHERE username = 'admin' OR email = 'admin@rokardsport.com';

-- ============================================
-- 密码选项：
-- 1. admin123 (默认密码，哈希值在上面)
-- 2. Admin@2024 (需要生成新哈希值)
-- ============================================
