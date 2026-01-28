-- 将账户2（Rokard）设置为管理员
-- 账户信息：ID=2, Username=Rokard, Email=2456355219@qq.com

-- 方法1：使用ID更新（推荐）
UPDATE users 
SET role = 'admin',
    updated_at = NOW()
WHERE id = 2;

-- 方法2：使用邮箱更新
UPDATE users 
SET role = 'admin',
    updated_at = NOW()
WHERE email = '2456355219@qq.com';

-- 方法3：使用用户名更新
UPDATE users 
SET role = 'admin',
    updated_at = NOW()
WHERE username = 'Rokard';

-- 验证更新结果
SELECT id, username, email, role, status, created_at, updated_at 
FROM users 
WHERE id = 2;

-- ============================================
-- 说明：
-- 执行后，账户2（Rokard）将拥有管理员权限
-- 可以使用该账户的邮箱和密码登录管理员面板
-- 
-- 登录信息：
-- 邮箱：2456355219@qq.com
-- 密码：你注册时设置的密码
-- ============================================
