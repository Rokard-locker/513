# 修改管理员密码指南

## 快速方法：使用提供的SQL

### 方法1：修改为新密码 `Admin@2024`

1. 登录 phpMyAdmin
2. 选择数据库：`if0_37528964_sport`
3. 点击"SQL"标签
4. 复制并执行以下SQL：

```sql
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = NOW()
WHERE username = 'admin';
```

**新密码**: `Admin@2024`

---

### 方法2：生成你自己的密码哈希

如果你想要设置自己的密码：

1. **方法A：使用提供的PHP脚本**
   - 编辑 `database/generate_password_hash.php`
   - 修改 `$new_password = 'YourPassword123';` 为你想要的密码
   - 在浏览器访问：`http://rokard.ct.ws/WP/database/generate_password_hash.php`
   - 或通过命令行运行：`php database/generate_password_hash.php`
   - 复制生成的SQL语句并执行

2. **方法B：在线生成哈希值**
   - 访问：https://bcrypt-generator.com/
   - 输入你想要的密码
   - 复制生成的哈希值
   - 执行SQL：
   ```sql
   UPDATE users 
   SET password = '复制的哈希值',
       updated_at = NOW()
   WHERE username = 'admin';
   ```

3. **方法C：创建临时PHP文件生成**
   - 创建一个文件 `temp_password.php`
   - 内容：
   ```php
   <?php
   echo password_hash('YourNewPassword', PASSWORD_DEFAULT);
   ?>
   ```
   - 访问这个文件获取哈希值
   - 使用获取的哈希值更新数据库

---

## 当前默认密码

- **用户名**: `admin`
- **邮箱**: `admin@rokardsport.com`
- **当前密码**: `admin123`

---

## 验证密码是否更新成功

执行以下SQL查看用户信息：

```sql
SELECT id, username, email, full_name, role, created_at, updated_at 
FROM users 
WHERE username = 'admin';
```

如果 `updated_at` 字段显示了当前时间，说明密码已更新。

---

## 登录测试

1. 访问登录页面：http://rokard.ct.ws/WP/auth/login.php
2. 使用以下任一方式登录：
   - 用户名：`admin` + 新密码
   - 邮箱：`admin@rokardsport.com` + 新密码

---

## 重要提示

⚠️ **安全建议**：
- 使用强密码（至少8位，包含大小写字母、数字和特殊字符）
- 示例强密码：`Admin@2024`, `MyPass123!`, `Secure2024#`
- 不要使用过于简单的密码
- 定期更换密码

---

## 如果忘记新密码

如果忘记了新密码，可以：
1. 重新执行SQL更新为已知密码
2. 或联系数据库管理员重置
