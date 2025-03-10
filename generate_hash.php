<?php
// Generar hash para 'admin123'
$admin_password = 'admin123';
$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
echo "Hash para 'admin123': " . $admin_hash . "<br>";

// Generar hash para 'user123'
$user_password = 'user123';
$user_hash = password_hash($user_password, PASSWORD_DEFAULT);
echo "Hash para 'user123': " . $user_hash . "<br>";
?>



Hash para 'admin123': $2y$10$boWAiv/VfLpstXML8ISUMONeHGRGxFkY9Plr.rgvdyPXnnhsGppWS
Hash para 'user123': $2y$10$JuEmhPcWFEd4DHyYG0M3TOKaQvsciDAKzKBvNvkghqMMx5YcE5Bpi


INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$boWAiv/VfLpstXML8ISUMONeHGRGxFkY9Plr.rgvdyPXnnhsGppWS', 'admin'),
('user1', '$2y$10$JuEmhPcWFEd4DHyYG0M3TOKaQvsciDAKzKBvNvkghqMMx5YcE5Bpi', 'user');