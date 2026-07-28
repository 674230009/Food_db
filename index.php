<?php
require_once 'db.php';

// ระบบลบข้อมูล
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM foods WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: index.php");
    exit;
}

// ดึงรายการอาหารทั้งหมด
$sql = "SELECT * FROM foods ORDER BY id DESC";
$foods = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการเมนูอาหาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-secondary">📋 รายการเมนูอาหารทั้งหมด</h2>
            <a href="manage.php" class="btn btn-primary">+ เพิ่มเมนูอาหารใหม่</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 15%">รูปภาพ</th>
                            <th style="width: 20%">ชื่ออาหาร</th>
                            <th style="width: 15%">หมวดหมู่</th>
                            <th style="width: 35%">วัตถุดิบและส่วนผสม</th>
                            <th style="width: 15%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($foods)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">ยังไม่มีข้อมูลอาหารในระบบ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($foods as $food):
                                // 1. ดึงส่วนผสม/วัตถุดิบ
                                $stmtRecipe = $pdo->prepare("SELECT * FROM recipes WHERE food_id = ?");
                                $stmtRecipe->execute([$food['id']]);
                                $recipes = $stmtRecipe->fetchAll();

                                // 2. ตรวจสอบไฟล์รูปภาพ (รองรับภาษาไทย และระบบ Windows)
                                $image_file = trim($food['image_name'] ?? '');
                                $image_path = 'images/' . $image_file;
                                
                                // แปลงความยาว/รหัสชื่อภาษาไทยสำหรับ Windows
                                $windows_path = iconv('UTF-8', 'TIS-620//IGNORE', $image_path);

                                // เช็กว่าผู้ใช้เลือกรูปไว้ และมีไฟล์อยู่จริงในโฟลเดอร์ images/
                                $has_image = !empty($image_file) && (file_exists($image_path) || @file_exists($windows_path));
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($has_image): ?>
                                            <!-- เติม ?v=time() เพื่อป้องกัน Browser จำรูปภาพเก่า (Clear Cache) -->
                                            <img src="<?= htmlspecialchars($image_path) ?>?v=<?= time() ?>" 
                                                 alt="<?= htmlspecialchars($food['name_th']) ?>" 
                                                 class="img-thumbnail shadow-sm" 
                                                 style="width: 90px; height: 70px; object-fit: cover; border-radius: 6px;">
                                        <?php else: ?>
                                            <span class="text-muted small">🖼️ ไม่มีรูป</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><strong><?= htmlspecialchars($food['name_th']) ?></strong></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($food['category']) ?></span></td>
                                    <td>
                                        <?php if (!empty($recipes)): ?>
                                            <ul class="mb-0 ps-3 small">
                                                <?php foreach ($recipes as $r): ?>
                                                    <li><?= htmlspecialchars($r['recipe_name']) ?> <?= $r['quantity'] ?> <?= htmlspecialchars($r['unit_name']) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-muted small">- ไม่มีข้อมูล -</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="manage.php?id=<?= $food['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                                        <a href="index.php?delete_id=<?= $food['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบเมนูนี้?');">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>