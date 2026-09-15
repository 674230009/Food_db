-- 1. สร้างฐานข้อมูล food_db
CREATE DATABASE IF NOT EXISTS `food_db` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `food_db`;

-- 2. สร้างตารางเก็บข้อมูลเมนูอาหาร (foods)
CREATE TABLE IF NOT EXISTS `foods` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name_th` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `image_name` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. สร้างตารางเก็บวัตถุดิบและส่วนผสม (recipes)
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `food_id` INT(11) NOT NULL,
  `recipe_name` VARCHAR(255) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `food_id` (`food_id`),
  CONSTRAINT `fk_recipes_foods` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;