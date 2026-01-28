# 修复管理员登录问题

## 问题分析

既然你可以注册其他账户并登录，说明登录功能本身是正常的。问题可能出在：

1. **管理员账户的密码哈希值不正确**
2. **管理员账户的状态不是 'active'**
3. **管理员账户不存在**

## 快速诊断

### 步骤1：访问诊断工具

访问这个文件来诊断问题：
```
http://rokard.ct.ws/512/check_admin_account.php
```
或
```
https://rokard.ct.ws/512/check_admin_account.php
```

或者访问：
```
http://rokard.ct.ws/512/test_login.php
```

这些工具会：
- 检查管理员账户是否存在
- 检查账户状态
- 测试常见密码
- 生成修复SQL

---

### 步骤2：检查数据库

在phpMyAdmin中执行：

```sql
-- 检查管理员账户
SELECT id, username, email, role, status, password 
FROM users 
WHERE username = 'admin' OR email = 'admin@rokardsport.com';
```

查看结果：
- 如果查询结果为空 → 账户不存在，需要创建
- 如果 `status` 不是 `'active'` → 需要更新状态
- 如果 `password` 字段为空或看起来不对 → 需要重置密码

---

### 步骤3：修复方案

#### 方案A：重置密码为 admin123（推荐）

```sql
-- 使用新生成的哈希值（这会正确匹配密码 'admin123'）
UPDATE users 
SET password = '$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO',
    status = 'active',
    email_verified = 1,
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';
```

**登录信息：**
- 邮箱：`admin@rokardsport.com`
- 密码：`admin123`

---

#### 方案B：创建管理员账户（如果不存在）

```sql
-- 创建管理员账户，密码：admin123
INSERT INTO users (username, email, password, full_name, role, status, email_verified, created_at) 
VALUES (
    'admin', 
    'admin@rokardsport.com', 
    '$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO',
    'System Administrator', 
    'admin', 
    'active', 
    1, 
    NOW()
);
```

---

#### 方案C：确保账户状态正确

```sql
-- 如果账户存在但状态不对，修复状态
UPDATE users 
SET status = 'active',
    email_verified = 1,
    updated_at = NOW()
WHERE username = 'admin' OR email = 'admin@rokardsport.com';
```

---

## 完整的修复SQL（推荐）

执行这个SQL可以一次性修复所有问题：

```sql
-- 完整修复：创建或更新管理员账户
INSERT INTO users (username, email, password, full_name, role, status, email_verified, created_at) 
VALUES (
    'admin', 
    'admin@rokardsport.com', 
    '$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO',
    'System Administrator', 
    'admin', 
    'active', 
    1, 
    NOW()
)
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO',
    status = 'active',
    email_verified = 1,
    updated_at = NOW();
```

---

## 验证

执行SQL后，访问诊断工具验证：
```
http://rokard.ct.ws/512/test_login.php
```

应该看到：
- ✓ Admin account found
- ✓ Password "admin123" is CORRECT!

然后尝试登录：
- 邮箱：`admin@rokardsport.com`
- 密码：`admin123`

---

## 如果还是不行

1. **检查登录页面的代码**：确保密码验证逻辑正确
2. **清除浏览器缓存和Cookie**：可能有旧的会话数据
3. **检查PHP错误日志**：查看是否有错误信息
4. **确认数据库连接**：确保登录时使用的数据库配置正确

---

## 密码哈希值说明

上面使用的哈希值 `$2y$10$6glfdXPXsxI60Xdp3UWt/08YPii.rpApOjTcic/rBk6ZrThBvoHKO` 
对应密码：`admin123`

这个哈希值是通过 `password_hash('admin123', PASSWORD_DEFAULT)` 生成的。
