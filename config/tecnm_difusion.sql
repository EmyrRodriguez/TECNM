/*
 Navicat MySQL Data Transfer

 Source Server         : ArjionL
 Source Server Type    : MySQL
 Source Server Version : 50562
 Source Host           : 10.0.8.30:3306
 Source Schema         : tecnm_difusion

 Target Server Type    : MySQL
 Target Server Version : 50562
 File Encoding         : 65001

 Date: 04/06/2021 13:14:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cat_departamentos
-- ----------------------------
DROP TABLE IF EXISTS `cat_departamentos`;
CREATE TABLE `cat_departamentos`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `descripcion` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fecha` datetime NULL DEFAULT NULL,
  `estatus` int(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cat_departamentos
-- ----------------------------
INSERT INTO `cat_departamentos` VALUES (1, 'VN', 'Vinculación ', 'Vinculación Tecnológica', '2021-06-04 09:07:22', 1);
INSERT INTO `cat_departamentos` VALUES (2, 'DN', 'Difusión', 'Difusión', '2021-06-04 09:07:22', 1);
INSERT INTO `cat_departamentos` VALUES (3, 'SE', 'Servicios Escolares', 'Servicios Escolares', '2021-06-04 09:07:22', 1);

-- ----------------------------
-- Table structure for tecnm_usuarios
-- ----------------------------
DROP TABLE IF EXISTS `tecnm_usuarios`;
CREATE TABLE `tecnm_usuarios`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `nombre` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `apellidos` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `correo` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `contrasenia` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `id_departamento` int(11) NULL DEFAULT NULL,
  `estatus` int(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tecnm_usuarios
-- ----------------------------
INSERT INTO `tecnm_usuarios` VALUES (1, 'froebel.ivan', 'Ivan', 'Gutierrez', 'froebel.ivan.g2@gmail.com', '0a027c2315b94790d29bcb9cab8faf5d', 3, 1);
INSERT INTO `tecnm_usuarios` VALUES (2, 'froebel.ivan2', 'Ivan2', 'Gutierrez2', 'froebel.ivan.g2@gmail.com.mx', '0719e46b13dea5e118c4beb3ecd3b104', 2, 1);

SET FOREIGN_KEY_CHECKS = 1;
