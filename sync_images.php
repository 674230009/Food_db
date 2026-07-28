<?php
require_once 'db.php';

$folder_name = 'images'; 

if (!is_dir($folder_name)) {
    mkdir($folder_name, 0777, true);
}

// ล้างข้อมูลเก่า
$pdo->query("TRUNCATE TABLE food_images");

// ดึงรายการอาหาร
$foods = $pdo->query("SELECT id, name_th FROM foods")->fetchAll();

echo "<h3>🔄 กำลังซิงค์รูปภาพจากโฟลเดอร์ '$folder_name' ...</h3><hr>";

$success_count = 0;
$extensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'PNG'];

foreach ($foods as $food) {
    $food_id = $food['id'];
    $food_name = trim($food['name_th']);
    $found_image = false;
    
    foreach ($extensions as $ext) {
        $image_file_name = $food_name . '.' . $ext;
        $full_path = $folder_name . '/' . $image_file_name;
        $windows_path = iconv('UTF-8', 'TIS-620//IGNORE', $full_path);
        
        if (file_exists($full_path) || (@file_exists($windows_path))) {
            $stmt = $pdo->prepare("INSERT INTO food_images (food_id, image_name) VALUES (?, ?)");
            $stmt->execute([$food_id, $image_file_name]);
            
            echo "<span style='color: green;'>✅ จับคู่สำเร็จ:</span> <b>$food_name</b> -> <i>$image_file_name</i><br>";
            $found_image = true;
            $success_count++;
            break; 
        }
    }
    
    if (!$found_image) {
        echo "<span style='color: red;'>❌ ไม่พบรูปภาพ:</span> <b>$food_name</b> (ต้องการไฟล์ $food_name.jpg หรือ .png)<br>";
    }
}

echo "<hr><b>🎉 ซิงค์รูปภาพสำเร็จทั้งหมด $success_count เมนู</b><br><br>";
echo "<a href='index.php' style='display:inline-block; padding:8px 15px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:4px;'>👉 กลับไปหน้าหลัก (index.php)</a>";
?>