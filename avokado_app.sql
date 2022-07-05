/*
 Navicat Premium Data Transfer

 Source Server         : Localhost Mysql
 Source Server Type    : MySQL
 Source Server Version : 100421
 Source Host           : localhost:3306
 Source Schema         : avokado_app

 Target Server Type    : MySQL
 Target Server Version : 100421
 File Encoding         : 65001

 Date: 05/07/2022 13:43:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for alim_evraklari
-- ----------------------------
DROP TABLE IF EXISTS `alim_evraklari`;
CREATE TABLE `alim_evraklari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tarih` date NULL DEFAULT NULL,
  `cari_id` int NULL DEFAULT NULL,
  `evrak_tur` int NULL DEFAULT NULL,
  `evrak_detayi` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `fatura_bilgileri` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `vade_gun` int NULL DEFAULT NULL,
  `vade_tarih` date NULL DEFAULT NULL,
  `unvan` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergino` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergidaire` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergiadres` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_zamani` datetime NULL DEFAULT NULL,
  `odeme_durum` int NULL DEFAULT 0,
  `paket_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `evrak_tutar` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `indirim_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_1` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_8` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_18` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `genel_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of alim_evraklari
-- ----------------------------

-- ----------------------------
-- Table structure for banka_hareket
-- ----------------------------
DROP TABLE IF EXISTS `banka_hareket`;
CREATE TABLE `banka_hareket`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `banka_id` int NOT NULL,
  `banka_hesap_id` int NULL DEFAULT NULL,
  `banka_haraket_tip` int NOT NULL,
  `banka_haraket_cari_id` int NOT NULL DEFAULT 0,
  `banka_haraket_tutar` decimal(14, 4) NOT NULL DEFAULT 0.0000,
  `banka_haraket_tarih` date NOT NULL,
  `banka_haraket_baslik` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `satis_evrak_id` int NULL DEFAULT 0,
  `sabit_gider_id` int NULL DEFAULT 0,
  `p_hatali_id` int NULL DEFAULT 0,
  `alim_evrak_id` int NULL DEFAULT 0,
  `iptal_mesaji` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of banka_hareket
-- ----------------------------

-- ----------------------------
-- Table structure for banka_hesaplari
-- ----------------------------
DROP TABLE IF EXISTS `banka_hesaplari`;
CREATE TABLE `banka_hesaplari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `banka_id` int NULL DEFAULT NULL,
  `hesap_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `hesap_bakiyesi` decimal(14, 4) NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `hesap_iban` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `hesap_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `hesap_sube_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of banka_hesaplari
-- ----------------------------
INSERT INTO `banka_hesaplari` VALUES (1, 1, 'Pos Hesabı', 0.0000, 0, 1, 0, 0, '2019-07-26 00:49:05', '2020-02-23 22:38:13', '0', '0', '0');

-- ----------------------------
-- Table structure for bankalar
-- ----------------------------
DROP TABLE IF EXISTS `bankalar`;
CREATE TABLE `bankalar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `banka_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bankalar
-- ----------------------------
INSERT INTO `bankalar` VALUES (1, 'Genel Banka', 0, 1, 0, 0, '2019-07-26 00:48:54', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for bildirimler
-- ----------------------------
DROP TABLE IF EXISTS `bildirimler`;
CREATE TABLE `bildirimler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `bildirim_mesaj` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `durum` int NULL DEFAULT 0,
  `tarih` date NULL DEFAULT NULL,
  `saat` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `zaman` datetime NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `goruntuleme` int NULL DEFAULT 0,
  `user_id` int NULL DEFAULT 0,
  `favori` int NULL DEFAULT 0,
  `onemli` int NULL DEFAULT 0,
  `islem_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `islem_alt_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `alarm_mod` int NULL DEFAULT 0,
  `mobil_goruntuleme` int NULL DEFAULT 0,
  `status` int NULL DEFAULT 1,
  `bildirim_baslik` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `bildirim_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bildirimler
-- ----------------------------

-- ----------------------------
-- Table structure for cari
-- ----------------------------
DROP TABLE IF EXISTS `cari`;
CREATE TABLE `cari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `cari_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `cari_telefon` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `cari_gsm` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `cari_logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `cari_detay` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `cari_mail` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `cari_adres` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `rfid_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `cari_gsm2` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `cari_yetkili` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `cari_vade_gun` int NULL DEFAULT 0,
  `cari_aktif` int NULL DEFAULT 1,
  `cari_kredi_limit` decimal(14, 4) NULL DEFAULT 0.0000,
  `remove` int NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `owner_id` int NULL DEFAULT NULL,
  `cari_turu` int NULL DEFAULT 1,
  `cari_vergi_no` int NULL DEFAULT 0,
  `cari_vergi_daire` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `cari_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'noimage.jpg',
  `cari_dogum_gunu` date NULL DEFAULT NULL,
  `cari_hesap_turu` int NULL DEFAULT 1,
  `cari_hesap_grubu` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'standart',
  `web_session_kod` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `cari_web_aktif` int NULL DEFAULT 0,
  `web_kullanici_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `web_sifre` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `bayi_user_id` int NULL DEFAULT 0,
  `web_avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'noimage.jpg',
  PRIMARY KEY (`id`, `created_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cari
-- ----------------------------
INSERT INTO `cari` VALUES (1, '1558025271', 'Perakende Satis Hesabi', '', '', '', '', '', '', '', '', '', 0, 1, 0.0000, 0, '2019-05-16 19:47:51', '2019-06-07 23:41:00', '', '', 1, 1, NULL, NULL, 'noimage.jpg', NULL, 1, 'standart', NULL, 0, '0', '0', 0, 'noimage.jpg');

-- ----------------------------
-- Table structure for cari_hesap_gruplari
-- ----------------------------
DROP TABLE IF EXISTS `cari_hesap_gruplari`;
CREATE TABLE `cari_hesap_gruplari`  (
  `id` int NOT NULL,
  `hesap_grup_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `hesap_grup_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cari_hesap_gruplari
-- ----------------------------

-- ----------------------------
-- Table structure for cari_servis_adres
-- ----------------------------
DROP TABLE IF EXISTS `cari_servis_adres`;
CREATE TABLE `cari_servis_adres`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_id` int NULL DEFAULT NULL,
  `adres_takma_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `il` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `ilce` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `semt` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `sokak` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `kapi_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `kat` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `daire` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `posta_kodu` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `yetkili_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `yetkili_telefon_numarasi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `kordinat` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `owner_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `created_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cari_servis_adres
-- ----------------------------

-- ----------------------------
-- Table structure for doviz
-- ----------------------------
DROP TABLE IF EXISTS `doviz`;
CREATE TABLE `doviz`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `doviz_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `doviz_kur` decimal(14, 4) NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `doviz_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `ForexBuying` decimal(14, 4) NULL DEFAULT 0.0000,
  `ForexSelling` decimal(14, 4) NULL DEFAULT 0.0000,
  `BanknoteBuying` decimal(14, 4) NULL DEFAULT 0.0000,
  `BanknoteSelling` decimal(14, 4) NULL DEFAULT 0.0000,
  `last_date` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of doviz
-- ----------------------------
INSERT INTO `doviz` VALUES (1, 'ABD DOLARI', 16.7935, 0, 1, 0, 0, '2019-08-15 11:10:15', '2022-07-05 13:35:07', 'USD', 16.7935, 16.8237, 16.7817, 16.8490, '2022-07-05');
INSERT INTO `doviz` VALUES (2, 'AVUSTRALYA DOLARI', 3.8350, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'AUD', 3.8350, 3.8600, 3.8174, 3.8832, '2019-09-05');
INSERT INTO `doviz` VALUES (3, 'DANİMARKA KRONU', 0.8348, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'DKK', 0.8348, 0.8389, 0.8342, 0.8408, '2019-09-05');
INSERT INTO `doviz` VALUES (4, 'EURO', 17.5322, 0, 1, 0, 0, '2019-08-15 11:10:15', '2022-07-05 13:35:07', 'EUR', 17.5322, 17.5638, 17.5200, 17.5902, '2022-07-05');
INSERT INTO `doviz` VALUES (5, 'İNGİLİZ STERLİNİ', 6.8838, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'GBP', 6.8838, 6.9197, 6.8790, 6.9301, '2019-09-05');
INSERT INTO `doviz` VALUES (6, 'İSVİÇRE FRANGI', 5.7340, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'CHF', 5.7340, 5.7708, 5.7254, 5.7795, '2019-09-05');
INSERT INTO `doviz` VALUES (7, 'İSVEÇ KRONU', 0.5777, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'SEK', 0.5777, 0.5837, 0.5773, 0.5850, '2019-09-05');
INSERT INTO `doviz` VALUES (8, 'KANADA DOLARI', 4.2474, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'CAD', 4.2474, 4.2665, 4.2316, 4.2827, '2019-09-05');
INSERT INTO `doviz` VALUES (9, 'KUVEYT DİNARI', 18.5352, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'KWD', 18.5352, 18.7777, 18.2572, 19.0594, '2019-09-05');
INSERT INTO `doviz` VALUES (10, 'NORVEÇ KRONU', 0.6232, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'NOK', 0.6232, 0.6274, 0.6228, 0.6288, '2019-09-05');
INSERT INTO `doviz` VALUES (11, 'SUUDİ ARABİSTAN RİYALİ', 1.5105, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'SAR', 1.5105, 1.5133, 1.4992, 1.5246, '2019-09-05');
INSERT INTO `doviz` VALUES (12, 'JAPON YENİ', 5.3214, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'JPY', 5.3214, 5.3566, 5.3017, 5.3770, '2019-09-05');
INSERT INTO `doviz` VALUES (13, 'BULGAR LEVASI', 3.1704, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'BGN', 3.1704, 3.2119, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (14, 'RUMEN LEYİ', 1.3113, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'RON', 1.3113, 1.3284, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (15, 'RUS RUBLESİ', 0.0848, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'RUB', 0.0848, 0.0859, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (16, 'İRAN RİYALİ', 0.0134, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'IRR', 0.0134, 0.0136, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (17, 'ÇİN YUANI', 0.7875, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'CNY', 0.7875, 0.7978, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (18, 'PAKİSTAN RUPİSİ', 0.0359, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'PKR', 0.0359, 0.0364, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (19, 'KATAR RİYALİ', 1.5473, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'QAR', 1.5473, 1.5676, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (20, 'ÖZEL ÇEKME HAKKI (SDR)                            ', 7.7516, 0, 1, 0, 0, '2019-08-15 11:10:15', '2019-09-05 07:47:08', 'XDR', 7.7516, 0.0000, 0.0000, 0.0000, '2019-09-05');
INSERT INTO `doviz` VALUES (21, 'TÜRK LİRASI', 1.0000, 0, 1, 0, 0, '2019-08-15 11:10:15', '2022-07-05 13:35:07', 'TL', 1.0000, 1.0000, 1.0000, 1.0000, '2022-07-05');

-- ----------------------------
-- Table structure for duyurular
-- ----------------------------
DROP TABLE IF EXISTS `duyurular`;
CREATE TABLE `duyurular`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `tarih` datetime NULL DEFAULT NULL,
  `baslik` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `duyuru` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of duyurular
-- ----------------------------

-- ----------------------------
-- Table structure for etkinlikler
-- ----------------------------
DROP TABLE IF EXISTS `etkinlikler`;
CREATE TABLE `etkinlikler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NULL DEFAULT NULL,
  `start_date_time` datetime NULL DEFAULT NULL,
  `end_date_time` datetime NULL DEFAULT NULL,
  `start_time` time NULL DEFAULT NULL,
  `end_time` time NULL DEFAULT NULL,
  `baslik` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `mesaj` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `durum` int NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of etkinlikler
-- ----------------------------

-- ----------------------------
-- Table structure for favori_stoklar
-- ----------------------------
DROP TABLE IF EXISTS `favori_stoklar`;
CREATE TABLE `favori_stoklar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_id` int NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of favori_stoklar
-- ----------------------------

-- ----------------------------
-- Table structure for hesap_detaylari
-- ----------------------------
DROP TABLE IF EXISTS `hesap_detaylari`;
CREATE TABLE `hesap_detaylari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `ilk_kayit` datetime NULL DEFAULT NULL,
  `paket_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'Demo Paket',
  `paket_id` int NULL DEFAULT 2,
  `durum` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '1',
  `detay` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `guncel_bakiye` decimal(14, 4) NULL DEFAULT 0.0000,
  `stok_limiti` int NULL DEFAULT 20,
  `cari_limiti` int NULL DEFAULT 5,
  `cihaz_limiti` int NULL DEFAULT 2,
  `kdv_dahil_fiyatlar` int NULL DEFAULT 0,
  `standart_depo_id` int NULL DEFAULT 0,
  `standart_kasa_id` int NULL DEFAULT 0,
  `hesap_cari_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `media_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `hesap_tipi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'ticari',
  `xml_servis` int NULL DEFAULT 0,
  `alarm_bildirim_saati` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '08:00:00',
  `secure_key` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hesap_detaylari
-- ----------------------------
INSERT INTO `hesap_detaylari` VALUES (1, '1', '2019-05-21 11:46:16', 'Sınırsız', 1, '1', ' ', 0.0000, 2147483647, 2147483647, 2147483647, 1, 3, NULL, 'Avokado Yazılım', 'jh43r4t23r42fh4ftgh23f42hg4f3g2h4fgh23f4g32feawdrasvdasgcdfas!!!asdasdMediatepepep', 'ticari', 0, '08:00:00', 'jh43r4t23r42fh4ftgh23f42hg4f3g2h4fgh23f4g32feawdrasvdasgcdfas!!!asdasd');

-- ----------------------------
-- Table structure for hesap_paketleri
-- ----------------------------
DROP TABLE IF EXISTS `hesap_paketleri`;
CREATE TABLE `hesap_paketleri`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `paket_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `paket_tutari` decimal(14, 4) NULL DEFAULT NULL,
  `paket_baslama_tarihi` datetime NULL DEFAULT NULL,
  `paket_bitis_tarihi` datetime NULL DEFAULT NULL,
  `ust_paket_uniq_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `paket_tanimlama_1` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `paket_tanimlama_2` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `paket_tanimlama_3` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `paket_ozellikleri` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `aktif` int NULL DEFAULT 1,
  `paket_aciklamasi` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `hesap_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `paket_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `paket_uniq_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `siparis_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hesap_paketleri
-- ----------------------------
INSERT INTO `hesap_paketleri` VALUES (17, 'Standart', 0.0000, '2019-08-22 00:00:00', '2050-08-22 00:00:00', '0', '100000', '100000', NULL, NULL, 0, 1, NULL, NULL, '', '', 1, NULL, '1', 'standart', '0', '0');
INSERT INTO `hesap_paketleri` VALUES (18, 'Depolama Alanı', 0.0000, '2019-08-22 00:00:00', '2050-08-22 00:00:00', '0', '2147483648', NULL, NULL, NULL, 0, 1, NULL, NULL, '', '', 1, NULL, '1', 'depolama', '0', '0');
INSERT INTO `hesap_paketleri` VALUES (19, 'Web Paketi', 0.0000, '2020-02-16 09:02:25', '2050-02-17 09:02:25', '0', '1024', NULL, NULL, NULL, 0, 1, NULL, NULL, '', '', 1, NULL, '1', 'web', '0', '0');

-- ----------------------------
-- Table structure for indirilen_resimler
-- ----------------------------
DROP TABLE IF EXISTS `indirilen_resimler`;
CREATE TABLE `indirilen_resimler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `resim_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `resim_kayit_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `indirme_durum` int NULL DEFAULT 0,
  `thumbs_folder` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `image_folder` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `resim_dosya_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `indirme_mesaj` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `resim_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of indirilen_resimler
-- ----------------------------

-- ----------------------------
-- Table structure for irsaliye
-- ----------------------------
DROP TABLE IF EXISTS `irsaliye`;
CREATE TABLE `irsaliye`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_id` int NULL DEFAULT NULL,
  `duzenleme_tarihi` datetime NULL DEFAULT NULL,
  `fiili_sevk_tarihi` date NULL DEFAULT NULL,
  `fatura_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tur` int NULL DEFAULT 0,
  `durum` int NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `irsaliye_aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of irsaliye
-- ----------------------------

-- ----------------------------
-- Table structure for is_emir_kalemleri
-- ----------------------------
DROP TABLE IF EXISTS `is_emir_kalemleri`;
CREATE TABLE `is_emir_kalemleri`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cikis_tarih` datetime NOT NULL,
  `cari_id` int NOT NULL DEFAULT 0,
  `cikis_evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `seri_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_id` int NOT NULL,
  `adet` decimal(40, 20) NOT NULL DEFAULT 1.00000000000000000000,
  `satis_fiyati` decimal(40, 20) NOT NULL,
  `kdv_oran` int NOT NULL DEFAULT 0,
  `iskonto` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `indirim_tutari` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `depo` int NOT NULL,
  `raf` int NOT NULL,
  `goz` int NOT NULL,
  `ozel_urun_id` int NOT NULL DEFAULT 0,
  `ic_transfer` int NULL DEFAULT 0,
  `parakende_cikis` int NULL DEFAULT 0,
  `parakende_yazilim_serial` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `satis_evrak_id` int NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL DEFAULT 'TL',
  `doviz_kur` decimal(40, 20) NOT NULL DEFAULT 1.00000000000000000000,
  `anapara` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `stokta_islem` int NULL DEFAULT 1,
  `irsaliye_id` int NULL DEFAULT 0,
  `irsaliye_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `vergi_dahil_gosterim` int NULL DEFAULT 0,
  `vergili_satis_fiyat` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `vergi_durum` int NULL DEFAULT 0,
  `paket_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `adet_etkisiz` int NULL DEFAULT 0,
  `siparis_stok` int NULL DEFAULT 0,
  `satis_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `stok_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `extra_detay` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `extra_sonuc_detay` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of is_emir_kalemleri
-- ----------------------------

-- ----------------------------
-- Table structure for is_emirleri
-- ----------------------------
DROP TABLE IF EXISTS `is_emirleri`;
CREATE TABLE `is_emirleri`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tarih` date NULL DEFAULT NULL,
  `cari_id` int NULL DEFAULT NULL,
  `evrak_tur` int NULL DEFAULT NULL,
  `evrak_detayi` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `vade_gun` int NULL DEFAULT NULL,
  `parakende_satis` int NULL DEFAULT 0,
  `vade_tarih` date NULL DEFAULT NULL,
  `unvan` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergino` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergidaire` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergiadres` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tahsil_durum` int NULL DEFAULT 0,
  `uniq_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `yazilim_serial` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `islem_notu` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_zamani` datetime NULL DEFAULT NULL,
  `paket_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `evrak_tutar` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `indirim_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_1` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_8` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_18` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `genel_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `siparis_durumu` int NULL DEFAULT 0,
  `siparis_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `siparis_fatura_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `kargo_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `kargo_takip_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of is_emirleri
-- ----------------------------

-- ----------------------------
-- Table structure for kasa_haraket
-- ----------------------------
DROP TABLE IF EXISTS `kasa_haraket`;
CREATE TABLE `kasa_haraket`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kasa_id` int NOT NULL,
  `kasa_haraket_tip` int NOT NULL,
  `kasa_haraket_cari_id` int NOT NULL DEFAULT 0,
  `kasa_haraket_tutar` decimal(14, 4) NOT NULL DEFAULT 0.0000,
  `kasa_haraket_tarih` date NOT NULL,
  `kasa_haraket_not` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `satis_evrak_id` int NULL DEFAULT 0,
  `sabit_gider_id` int NULL DEFAULT 0,
  `iptal_mesaji` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kasa_haraket
-- ----------------------------

-- ----------------------------
-- Table structure for kasalar
-- ----------------------------
DROP TABLE IF EXISTS `kasalar`;
CREATE TABLE `kasalar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kasa_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `kasa_toplam_tutar` decimal(14, 4) NOT NULL DEFAULT 0.0000,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kasalar
-- ----------------------------
INSERT INTO `kasalar` VALUES (4, 'Genel Kasa', 0.0000, 0, 1, 0, 0, '2019-05-17 21:43:41', '2020-02-24 13:21:52');

-- ----------------------------
-- Table structure for kiymetli_evraklar
-- ----------------------------
DROP TABLE IF EXISTS `kiymetli_evraklar`;
CREATE TABLE `kiymetli_evraklar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_tur` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_son_odeme_tarihi` date NULL DEFAULT NULL,
  `evrak_tip` int NULL DEFAULT 1,
  `evrak_olusturma_tarihi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_bedeli` decimal(14, 4) NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `evrak_detay` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_cari_id` int NULL DEFAULT NULL,
  `evrak_arsiv_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_muhattap_banka` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `odeme_durum` int NULL DEFAULT 0,
  `kadise_yeri` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_not` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `iptal_mesaj` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kiymetli_evraklar
-- ----------------------------

-- ----------------------------
-- Table structure for markalar
-- ----------------------------
DROP TABLE IF EXISTS `markalar`;
CREATE TABLE `markalar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `marka_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of markalar
-- ----------------------------

-- ----------------------------
-- Table structure for masa_kategorileri
-- ----------------------------
DROP TABLE IF EXISTS `masa_kategorileri`;
CREATE TABLE `masa_kategorileri`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `masa_kategori_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `owner_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of masa_kategorileri
-- ----------------------------

-- ----------------------------
-- Table structure for masalar
-- ----------------------------
DROP TABLE IF EXISTS `masalar`;
CREATE TABLE `masalar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `masa_kategori_id` int NULL DEFAULT 0,
  `masa_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `masa_kapasite` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `remove` int NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `owner_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of masalar
-- ----------------------------

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `modul_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `activate` int NULL DEFAULT 0,
  `url_list` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `owner_id` int NULL DEFAULT NULL,
  `remove` int NULL DEFAULT 0,
  `modul_url_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `modul_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of modules
-- ----------------------------

-- ----------------------------
-- Table structure for odeme_plani
-- ----------------------------
DROP TABLE IF EXISTS `odeme_plani`;
CREATE TABLE `odeme_plani`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_id` int NULL DEFAULT NULL,
  `islem_tip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tutar` decimal(31, 20) NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `islem_tarih` date NULL DEFAULT NULL,
  `islem_tarih_saat` datetime NULL DEFAULT NULL,
  `aciklama` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `evrak_tip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_id` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of odeme_plani
-- ----------------------------

-- ----------------------------
-- Table structure for odemeler
-- ----------------------------
DROP TABLE IF EXISTS `odemeler`;
CREATE TABLE `odemeler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_id` int NULL DEFAULT NULL,
  `odeme_tip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `odeme_tutar` decimal(14, 4) NULL DEFAULT NULL,
  `odeme_islem_id` int NULL DEFAULT NULL,
  `remove` int NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `owner_id` int NULL DEFAULT NULL,
  `odeme_tarih` date NULL DEFAULT NULL,
  `odeme_mesaj` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `alim_evrak_id` int NULL DEFAULT 0,
  `iptal_mesaji` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `etkisiz` int NULL DEFAULT 0,
  `kiymetli_evrak` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of odemeler
-- ----------------------------

-- ----------------------------
-- Table structure for post
-- ----------------------------
DROP TABLE IF EXISTS `post`;
CREATE TABLE `post`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `tip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `mesaj` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `stok_id` int NULL DEFAULT 0,
  `hesap_id` int NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `dataid` int NULL DEFAULT 0,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'noimage.jpg',
  `original_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'noimage.jpg',
  `video` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'novideo',
  `yorum_durum` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of post
-- ----------------------------

-- ----------------------------
-- Table structure for post_comments
-- ----------------------------
DROP TABLE IF EXISTS `post_comments`;
CREATE TABLE `post_comments`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NULL DEFAULT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `comment_id` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of post_comments
-- ----------------------------

-- ----------------------------
-- Table structure for sabit_odemeler
-- ----------------------------
DROP TABLE IF EXISTS `sabit_odemeler`;
CREATE TABLE `sabit_odemeler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_id` int NULL DEFAULT NULL,
  `tutar` decimal(31, 20) NULL DEFAULT 0.00000000000000000000,
  `gun` varchar(11) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '01',
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `aciklama` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `ondecen_bildirim` int NULL DEFAULT 0,
  `ayni_gun_bildirim` int NULL DEFAULT 1,
  `tur` int NULL DEFAULT 0,
  `baslama_tarihi` date NULL DEFAULT NULL,
  `bitis_tarihi` date NULL DEFAULT NULL,
  `baslik` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tutar_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `bildirim_durum` int NULL DEFAULT 1,
  `ertesi_gun_bildirim` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sabit_odemeler
-- ----------------------------

-- ----------------------------
-- Table structure for satis_evraklari
-- ----------------------------
DROP TABLE IF EXISTS `satis_evraklari`;
CREATE TABLE `satis_evraklari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tarih` date NULL DEFAULT NULL,
  `cari_id` int NULL DEFAULT NULL,
  `evrak_tur` int NULL DEFAULT NULL,
  `evrak_detayi` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `fatura_bilgileri` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `vade_gun` int NULL DEFAULT NULL,
  `parakende_satis` int NULL DEFAULT 0,
  `vade_tarih` date NULL DEFAULT NULL,
  `unvan` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergino` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergidaire` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergiadres` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tahsil_durum` int NULL DEFAULT 0,
  `uniq_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `yazilim_serial` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `islem_notu` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `evrak_zamani` datetime NULL DEFAULT NULL,
  `paket_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `evrak_tutar` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `indirim_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_1` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_8` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_18` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `kdv_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `genel_toplam` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `siparis_durumu` int NULL DEFAULT 0,
  `siparis_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `siparis_fatura_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `kargo_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `kargo_takip_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of satis_evraklari
-- ----------------------------

-- ----------------------------
-- Table structure for statik_ip_listesi
-- ----------------------------
DROP TABLE IF EXISTS `statik_ip_listesi`;
CREATE TABLE `statik_ip_listesi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_adress` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of statik_ip_listesi
-- ----------------------------

-- ----------------------------
-- Table structure for stok
-- ----------------------------
DROP TABLE IF EXISTS `stok`;
CREATE TABLE `stok`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_barkod_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_ozel_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_cinsi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_birimi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL DEFAULT '',
  `stok_sinif` int NOT NULL,
  `stok_grup` int NOT NULL,
  `stok_resim` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_adet` int NULL DEFAULT 0,
  `stok_ozel_urun_adet` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `stok_min_seviyesi` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `stok_max_seviyesi` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `stok_alis_fiyati` decimal(40, 20) NOT NULL,
  `stok_satis_fiyati` decimal(40, 20) NOT NULL,
  `stok_max_iskontolu_satis_fiyati` decimal(40, 20) NOT NULL,
  `stok_kdv_oran` bigint NOT NULL DEFAULT 0,
  `stok_kdv_detay` int NOT NULL DEFAULT 0,
  `stok_detayi` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_create_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `last_val` int NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `stok_kdv_dahil_satis_fiyati` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `stok_fiyat_vergi_durum` int NULL DEFAULT 1,
  `aktif` int NULL DEFAULT 1,
  `stok_standart_adet` decimal(40, 20) NULL DEFAULT 1.00000000000000000000,
  `stok_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `stok_satis_iskonto_oran` int NULL DEFAULT 0,
  `stok_alim_iskonto_oran` int NULL DEFAULT 0,
  `stok_seo_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `stok_parent_id` int NULL DEFAULT 0,
  `stok_marka_id` int NULL DEFAULT 0,
  `stok_tipi` int NULL DEFAULT 1,
  `stok_varyant_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `stok_perakende_satis` int NULL DEFAULT 1,
  `stok_web_satis` int NULL DEFAULT 1,
  `stok_portal_satis` int NULL DEFAULT 1,
  `stok_varyant_deger` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `stok_parent_stok_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `sanal_stok` int NULL DEFAULT 0,
  `onemli` int NULL DEFAULT 0,
  `paket_stok` int NULL DEFAULT 0,
  `bayi_alis_fiyati1` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `bayi_alis_fiyati2` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `stok_islem_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `stok_image_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'local',
  `stok_web_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_web_description` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `stok_alim_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `stok_kayit_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `stok_marka` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_resim2` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_resim3` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_satis_fiyati2` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `stok_satis_fiyati3` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `stok_resim4` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `son_xml_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok
-- ----------------------------

-- ----------------------------
-- Table structure for stok_birimler
-- ----------------------------
DROP TABLE IF EXISTS `stok_birimler`;
CREATE TABLE `stok_birimler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_birim_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_birimler
-- ----------------------------
INSERT INTO `stok_birimler` VALUES (12, 'Adet', 0, 1, 0, 0, '2019-05-16 19:48:17', '0000-00-00 00:00:00');
INSERT INTO `stok_birimler` VALUES (13, 'Kg', 0, 1, 0, 0, '2019-08-02 20:29:09', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for stok_change_listener
-- ----------------------------
DROP TABLE IF EXISTS `stok_change_listener`;
CREATE TABLE `stok_change_listener`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `last_id` bigint NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL,
  `updated_user` int NOT NULL,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1066 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_change_listener
-- ----------------------------
INSERT INTO `stok_change_listener` VALUES (1, 1, 0, 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for stok_depolar
-- ----------------------------
DROP TABLE IF EXISTS `stok_depolar`;
CREATE TABLE `stok_depolar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_depo_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `toplam_urun_sayisi` int NOT NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL,
  `updated_user` int NOT NULL,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_depolar
-- ----------------------------
INSERT INTO `stok_depolar` VALUES (3, 'Merkez depo', 0, 0, 1, 1, 0, '2019-05-16 19:49:16', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for stok_etiketler
-- ----------------------------
DROP TABLE IF EXISTS `stok_etiketler`;
CREATE TABLE `stok_etiketler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `tag_id` int NULL DEFAULT NULL,
  `stok_id` int NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_etiketler
-- ----------------------------

-- ----------------------------
-- Table structure for stok_galeri
-- ----------------------------
DROP TABLE IF EXISTS `stok_galeri`;
CREATE TABLE `stok_galeri`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_id` int NULL DEFAULT NULL,
  `resim_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `resim_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `resim_alt` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_galeri
-- ----------------------------

-- ----------------------------
-- Table structure for stok_grup_stoklari
-- ----------------------------
DROP TABLE IF EXISTS `stok_grup_stoklari`;
CREATE TABLE `stok_grup_stoklari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_id` int NULL DEFAULT NULL,
  `grup_stok_id` int NULL DEFAULT NULL,
  `miktar` decimal(10, 4) NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_grup_stoklari
-- ----------------------------

-- ----------------------------
-- Table structure for stok_gruplar
-- ----------------------------
DROP TABLE IF EXISTS `stok_gruplar`;
CREATE TABLE `stok_gruplar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_grup_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `last_val` bigint NULL DEFAULT NULL,
  `stok_group_create_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_gruplar
-- ----------------------------

-- ----------------------------
-- Table structure for stok_haraket_cikis
-- ----------------------------
DROP TABLE IF EXISTS `stok_haraket_cikis`;
CREATE TABLE `stok_haraket_cikis`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cikis_tarih` datetime NOT NULL,
  `cari_id` int NOT NULL DEFAULT 0,
  `cikis_evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `seri_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_id` int NOT NULL,
  `adet` decimal(40, 20) NOT NULL DEFAULT 1.00000000000000000000,
  `satis_fiyati` decimal(40, 20) NOT NULL,
  `kdv_oran` int NOT NULL DEFAULT 0,
  `iskonto` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `indirim_tutari` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `depo` int NOT NULL,
  `raf` int NOT NULL,
  `goz` int NOT NULL,
  `ozel_urun_id` int NOT NULL DEFAULT 0,
  `ic_transfer` int NULL DEFAULT 0,
  `parakende_cikis` int NULL DEFAULT 0,
  `parakende_yazilim_serial` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `satis_evrak_id` int NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL DEFAULT 'TL',
  `doviz_kur` decimal(40, 20) NOT NULL DEFAULT 1.00000000000000000000,
  `anapara` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `stokta_islem` int NULL DEFAULT 1,
  `irsaliye_id` int NULL DEFAULT 0,
  `irsaliye_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `vergi_dahil_gosterim` int NULL DEFAULT 0,
  `vergili_satis_fiyat` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `vergi_durum` int NULL DEFAULT 0,
  `paket_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `adet_etkisiz` int NULL DEFAULT 0,
  `siparis_stok` int NULL DEFAULT 0,
  `satis_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `stok_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_haraket_cikis
-- ----------------------------

-- ----------------------------
-- Table structure for stok_haraket_giris
-- ----------------------------
DROP TABLE IF EXISTS `stok_haraket_giris`;
CREATE TABLE `stok_haraket_giris`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `giris_tarih` datetime NOT NULL,
  `cari_id` int NOT NULL,
  `giris_evrak_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `seri_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `stok_id` int NOT NULL,
  `adet` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `alis_fiyati` decimal(40, 20) NOT NULL,
  `kdv_oran` int NOT NULL DEFAULT 0,
  `vergi_tutari` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `iskonto` decimal(40, 20) NOT NULL DEFAULT 0.00000000000000000000,
  `indirim_tutari` decimal(40, 20) NULL DEFAULT 0.00000000000000000000,
  `depo` int NOT NULL,
  `raf` int NOT NULL,
  `goz` int NOT NULL,
  `ozel_urun` int NOT NULL DEFAULT 0,
  `ozel_urun_durum` int NOT NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `ic_transfer` int NULL DEFAULT 0,
  `alim_evrak_id` int NULL DEFAULT 0,
  `doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `doviz_kur` decimal(40, 20) NULL DEFAULT 1.00000000000000000000,
  `vergi_durum` int NULL DEFAULT 0,
  `aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `paket_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `adet_etkisiz` int NULL DEFAULT 0,
  `alim_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'TL',
  `stok_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_haraket_giris
-- ----------------------------

-- ----------------------------
-- Table structure for stok_kategorileri
-- ----------------------------
DROP TABLE IF EXISTS `stok_kategorileri`;
CREATE TABLE `stok_kategorileri`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kategori_id` int NULL DEFAULT NULL,
  `kategori_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `tip` int NULL DEFAULT 0,
  `ust_kategori_id` int NULL DEFAULT 0,
  `kategori_seo_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_kategorileri
-- ----------------------------

-- ----------------------------
-- Table structure for stok_raflar
-- ----------------------------
DROP TABLE IF EXISTS `stok_raflar`;
CREATE TABLE `stok_raflar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_depo_id` int NOT NULL DEFAULT 0,
  `stok_raf_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL,
  `updated_user` int NOT NULL,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_raflar
-- ----------------------------

-- ----------------------------
-- Table structure for stok_rating
-- ----------------------------
DROP TABLE IF EXISTS `stok_rating`;
CREATE TABLE `stok_rating`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_id` int NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `raiting` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_rating
-- ----------------------------

-- ----------------------------
-- Table structure for stok_resimler
-- ----------------------------
DROP TABLE IF EXISTS `stok_resimler`;
CREATE TABLE `stok_resimler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_id` int NOT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL,
  `updated_user` int NOT NULL,
  `created_date` int NOT NULL,
  `update_date` int NOT NULL,
  `stok_resim_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_resimler
-- ----------------------------

-- ----------------------------
-- Table structure for stok_sayim_kalemler
-- ----------------------------
DROP TABLE IF EXISTS `stok_sayim_kalemler`;
CREATE TABLE `stok_sayim_kalemler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sayim_uniq_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_id` int NULL DEFAULT NULL,
  `stok_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_alim_fiyati` decimal(40, 20) NULL DEFAULT NULL,
  `stok_satis_fiyati` decimal(40, 20) NULL DEFAULT NULL,
  `stok_doviz` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_vergi_oran` int NULL DEFAULT NULL,
  `stok_mevcut_adet` decimal(40, 20) NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_sayim_kalemler
-- ----------------------------

-- ----------------------------
-- Table structure for stok_sayimlar
-- ----------------------------
DROP TABLE IF EXISTS `stok_sayimlar`;
CREATE TABLE `stok_sayimlar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sayim_uniq` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `sayim_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `sayim_baslama_tarih` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `sayim_bitis_tarih` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `sayim_durum` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_sayimlar
-- ----------------------------

-- ----------------------------
-- Table structure for stok_siniflar
-- ----------------------------
DROP TABLE IF EXISTS `stok_siniflar`;
CREATE TABLE `stok_siniflar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_sinif_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL,
  `updated_user` int NOT NULL,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_siniflar
-- ----------------------------
INSERT INTO `stok_siniflar` VALUES (10, '1. Sınıf', 0, 1, 1, 0, '2019-05-16 19:49:05', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for tagler
-- ----------------------------
DROP TABLE IF EXISTS `tagler`;
CREATE TABLE `tagler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `tag_fix` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `tag_create_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `tag_seo_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tagler
-- ----------------------------

-- ----------------------------
-- Table structure for tahsilatlar
-- ----------------------------
DROP TABLE IF EXISTS `tahsilatlar`;
CREATE TABLE `tahsilatlar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cari_id` int NULL DEFAULT NULL,
  `islem_tip` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `islem_id` int NULL DEFAULT NULL,
  `islem_tutar` decimal(14, 4) NULL DEFAULT NULL,
  `remove` int NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `owner_id` int NULL DEFAULT NULL,
  `islem_tarih` date NULL DEFAULT NULL,
  `islem_mesaj` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `satis_evrak_id` int NULL DEFAULT NULL,
  `iptal_mesaji` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `etkisiz` int NULL DEFAULT 0,
  `kiymetli_evrak` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tahsilatlar
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `surname` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `auths` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `admin` int NULL DEFAULT 0,
  `gender` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `owner_id` int NULL DEFAULT NULL,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `api_permission` int NULL DEFAULT 1,
  `api_last_connection` datetime NULL DEFAULT NULL,
  `api_session_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `h_nav` int NULL DEFAULT 1,
  `offline_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `offline_activate` int NULL DEFAULT 0,
  `web` int NULL DEFAULT 1,
  `mobile_api` int NULL DEFAULT 1,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `mobile_api_session_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `mobile_api_last_connection` datetime NULL DEFAULT NULL,
  `template` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT 'default',
  `fatura_detayli_gorunum` int NULL DEFAULT 0,
  `bayi` int NULL DEFAULT 0,
  `cari_id` int NULL DEFAULT 0,
  `kasa_id` int NULL DEFAULT 0,
  `bildirim_session_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `hesap_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `system_user_status` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (2, '81dc9bdb52d04dc20036dbd8313ed055', 'Deneme', 'Kullanıcı', '0', 0, 'admin', NULL, NULL, 'noimage.jpg', 1, 'male', 1, 0, 0, '2019-05-20 01:23:07', '0000-00-00 00:00:00', 1, '2022-07-05 13:41:54', '62c41572b919a', 1, NULL, 0, 1, 1, '', '5d8e2b9f14eea', '2019-09-27 15:32:47', 'fuse', 1, 0, 0, 0, NULL, '0', 0);

-- ----------------------------
-- Table structure for web_ayarlar
-- ----------------------------
DROP TABLE IF EXISTS `web_ayarlar`;
CREATE TABLE `web_ayarlar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `config_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `auto_load` int NULL DEFAULT 0,
  `config_group` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `owner_id` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of web_ayarlar
-- ----------------------------

-- ----------------------------
-- Table structure for web_sayfalar
-- ----------------------------
DROP TABLE IF EXISTS `web_sayfalar`;
CREATE TABLE `web_sayfalar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `page_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NULL DEFAULT 0,
  `meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `owner_id` int NULL DEFAULT 0,
  `fix` int NULL DEFAULT 0,
  `activate` int NULL DEFAULT 1,
  `create_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `page_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `eticaret` int NULL DEFAULT 0,
  `cache_block` int NULL DEFAULT 0,
  `page_html_content` longtext CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of web_sayfalar
-- ----------------------------

-- ----------------------------
-- Table structure for web_slider_items
-- ----------------------------
DROP TABLE IF EXISTS `web_slider_items`;
CREATE TABLE `web_slider_items`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `slider_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `sira` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of web_slider_items
-- ----------------------------

-- ----------------------------
-- Table structure for web_sliders
-- ----------------------------
DROP TABLE IF EXISTS `web_sliders`;
CREATE TABLE `web_sliders`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `slide_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `slide_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `account_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `slide_tur` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `slide_nick_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of web_sliders
-- ----------------------------

-- ----------------------------
-- Table structure for web_stok_detay
-- ----------------------------
DROP TABLE IF EXISTS `web_stok_detay`;
CREATE TABLE `web_stok_detay`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `stok_id` int NULL DEFAULT NULL,
  `stok_meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_aciklama` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `stok_detay` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of web_stok_detay
-- ----------------------------

-- ----------------------------
-- Table structure for web_temalar
-- ----------------------------
DROP TABLE IF EXISTS `web_temalar`;
CREATE TABLE `web_temalar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `tema_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `tema_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of web_temalar
-- ----------------------------

-- ----------------------------
-- Table structure for xml_dosyalari
-- ----------------------------
DROP TABLE IF EXISTS `xml_dosyalari`;
CREATE TABLE `xml_dosyalari`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `xml_dosya_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NULL DEFAULT NULL,
  `update_date` datetime NULL DEFAULT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `ad` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `dosya_url_adresi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `dosya_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `varyant_isimler_degistirme` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `varyant_grup_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `varyant_isimleri_duzenle` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xml_dosyalari
-- ----------------------------

-- ----------------------------
-- Table structure for xml_yuklemeler
-- ----------------------------
DROP TABLE IF EXISTS `xml_yuklemeler`;
CREATE TABLE `xml_yuklemeler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `url_adresi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `firma_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `xml_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `xml_data` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `durum` int NULL DEFAULT 0,
  `result_json` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `favori` int NULL DEFAULT 0,
  `xml_dosyasi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `xml_kod` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xml_yuklemeler
-- ----------------------------

-- ----------------------------
-- Table structure for yapilacaklar
-- ----------------------------
DROP TABLE IF EXISTS `yapilacaklar`;
CREATE TABLE `yapilacaklar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `aciklama` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL,
  `durum` int NULL DEFAULT 0,
  `tarih` date NULL DEFAULT NULL,
  `start` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `end` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `bildirim_id` int NULL DEFAULT 0,
  `baslama` datetime NULL DEFAULT NULL,
  `bitis` datetime NULL DEFAULT NULL,
  `tamgun` int NULL DEFAULT 0,
  `full_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `full_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT 0,
  `takvim` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yapilacaklar
-- ----------------------------
INSERT INTO `yapilacaklar` VALUES (1, 'Deneme', NULL, 1, '2022-06-09', '2022-06-09', '2022-06-10', 0, 1, 0, 0, '2022-06-08 16:57:46', '2022-06-08 16:57:46', 1, '2022-06-09 00:00:00', '2022-06-10 00:00:00', 1, 'Thu Jun 09 2022 00:00:00 GMT+0300 (GMT+03:00)', 'Fri Jun 10 2022 00:00:00 GMT+0300 (GMT+03:00)', 2, 1);

-- ----------------------------
-- Table structure for yazilimlar
-- ----------------------------
DROP TABLE IF EXISTS `yazilimlar`;
CREATE TABLE `yazilimlar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `serial_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `takma_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `master_parakende_cari_hesap_id` int NOT NULL DEFAULT 1,
  `cikis_yapacagi_depo_id` int NOT NULL DEFAULT 1,
  `cihaz_uniq_id` int NULL DEFAULT 0,
  `remove` int NOT NULL DEFAULT 0,
  `kasa_id` int NOT NULL DEFAULT 1,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `created_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `updated_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `device_status` int NULL DEFAULT 1,
  `device_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `device_uniq_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `reload_date` date NULL DEFAULT NULL,
  `pos_hesap_id` int NULL DEFAULT 0,
  `pos_hesap_banka_id` int NULL DEFAULT 0,
  `activate` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yazilimlar
-- ----------------------------
INSERT INTO `yazilimlar` VALUES (1, '1234', 'cihaz1', 1, 3, 5, 0, 4, 1, '0000-00-00 00:00:00', '2022-07-05 13:41:54', '', '', 1, 'barkod', 'd8d095f85f623e5c9ec246b3a406dcba', '2022-07-13', 1, 1, 1);
INSERT INTO `yazilimlar` VALUES (2, '12345', 'cihaz2', 1, 3, 5, 0, 4, 1, '0000-00-00 00:00:00', '2022-06-08 18:47:17', '', '', 1, 'adisyon', 'cf2f78ac8fc5f8e32253a088461680b0', '2022-07-14', 1, 1, 1);

-- ----------------------------
-- Table structure for yedekler
-- ----------------------------
DROP TABLE IF EXISTS `yedekler`;
CREATE TABLE `yedekler`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `yedek_adi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `remove` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_user` int NOT NULL DEFAULT 0,
  `updated_user` int NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `yedek_tipi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '',
  `yedek_dosyasi` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL,
  `key_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yedekler
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
