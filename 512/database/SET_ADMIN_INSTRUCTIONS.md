# 将账户2设置为管理员

## 账户信息

根据数据库截图：
- **ID**: 2
- **用户名**: Rokard
- **邮箱**: 2456355219@qq.com
- **当前角色**: customer
- **状态**: active

## SQL语句

### 快速方法（推荐）

在 phpMyAdmin 中执行：

```sql
UPDATE users 
SET role = 'admin',
    updated_at = NOW()
WHERE id = 2;
```

### 其他方法

**使用邮箱：**
```sql
UPDATE users 
SET role = 'admin',
    updated_at = NOW()
WHERE email = '2456355219@qq.com';
```

**使用用户名：**
```sql
UPDATE users 
SET role = 'admin',
    updated_at = NOW()
WHERE username = 'Rokard';
```

## 验证

执行SQL后，运行以下查询确认：

```sql
SELECT id, username, email, role, status 
FROM users 
WHERE id = 2;
```

应该看到：
- `role` 字段从 `customer` 变为 `admin`
- `updated_at` 字段显示当前时间

## 登录

设置完成后，使用以下信息登录管理员面板：

- **邮箱**: `2456355219@qq.com`
- **密码**: 你注册时设置的密码（不是admin123）
- **管理员面板**: `http://rokard.ct.ws/512/admin/index.php`

## 注意事项

1. 执行SQL后，账户2就拥有管理员权限了
2. 可以使用该账户的原始密码登录（你注册时设置的）
3. 如果忘记密码，可以先登录，然后在用户中心修改密码
4. 现在你有两个管理员账户了：
   - ID 1: admin@rokardsport.com
   - ID 2: 2456355219@qq.com
