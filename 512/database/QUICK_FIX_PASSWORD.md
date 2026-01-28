# 快速修复管理员密码

## 问题诊断

如果登录失败，可能的原因：
1. 数据库中密码哈希值不正确
2. 管理员账户不存在
3. 密码字段格式问题

## 解决方案

### 方法1：使用在线工具（最简单）

1. **访问密码重置工具**：
   ```
   http://rokard.ct.ws/512/database/reset_password.php
   ```
   或
   ```
   https://rokard.ct.ws/512/database/reset_password.php
   ```

2. **复制生成的SQL语句**

3. **在phpMyAdmin中执行**

---

### 方法2：直接执行SQL（推荐）

**登录信息：**
- 邮箱：`admin@rokardsport.com`
- 密码：`admin123`

**执行以下SQL：**

```sql
-- 如果账户不存在，创建它
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`, `email_verified`, `created_at`) 
VALUES ('admin', 'admin@rokardsport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active', 1, NOW())
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = NOW();
```

**或者只更新密码（如果账户已存在）：**

```sql
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';
```

---

### 方法3：检查并修复

**步骤1：检查用户是否存在**
```sql
SELECT id, username, email, role, status, password 
FROM users 
WHERE username = 'admin' OR email = 'admin@rokardsport.com';
```

**步骤2：如果用户不存在，创建它**
```sql
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`, `email_verified`, `created_at`) 
VALUES ('admin', 'admin@rokardsport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active', 1, NOW());
```

**步骤3：如果用户存在但密码不对，更新它**
```sql
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';
```

---

## 密码说明

**默认密码：** `admin123`

这个哈希值 `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi` 对应密码 `admin123`

---

## 登录测试

执行SQL后，使用以下信息登录：

- **邮箱**: `admin@rokardsport.com`
- **密码**: `admin123`

或者：

- **用户名**: `admin`  
- **密码**: `admin123`

---

## 如果还是不行

1. **检查数据库连接**：确认数据库配置正确
2. **检查用户状态**：确保 `status = 'active'`
3. **清除浏览器缓存**：清除cookie和缓存后重试
4. **检查错误日志**：查看PHP错误日志

---

## 生成新密码哈希

如果需要设置新密码，访问：
```
http://rokard.ct.ws/512/database/reset_password.php
```

修改文件中的 `$password` 变量，然后刷新页面获取新的SQL语句。
