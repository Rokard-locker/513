<?php
/**
 * 生成密码哈希值的工具脚本
 * 使用方法：在浏览器中访问此文件，或通过命令行运行：php generate_password_hash.php
 */

// 设置新密码（请修改为你想要的密码）
$new_password = 'Admin@2024';

// 生成密码哈希
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "=== 密码哈希生成工具 ===\n\n";
echo "新密码: " . $new_password . "\n";
echo "哈希值: " . $hashed_password . "\n\n";

echo "=== SQL 更新语句 ===\n\n";
echo "UPDATE users SET password = '" . $hashed_password . "', updated_at = NOW() WHERE username = 'admin';\n\n";

echo "=== 使用方法 ===\n";
echo "1. 将上面的SQL语句复制到phpMyAdmin\n";
echo "2. 执行SQL语句\n";
echo "3. 使用新密码登录\n";

// 验证哈希值（可选）
echo "\n=== 验证 ===\n";
if (password_verify($new_password, $hashed_password)) {
    echo "✓ 密码哈希验证成功！\n";
} else {
    echo "✗ 密码哈希验证失败！\n";
}

?>
