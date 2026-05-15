<?php
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'productbadges_badge` (
    `id_badge` INT(11) NOT NULL AUTO_INCREMENT,
    `background_color` VARCHAR(7) NOT NULL DEFAULT "#000000",
    `text_color` VARCHAR(7) NOT NULL DEFAULT "#ffffff",
    `position` ENUM("top-left","top-right") NOT NULL DEFAULT "top-left",
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_badge`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'productbadges_badge_lang` (
    `id_badge` INT(11) NOT NULL,
    `id_lang` INT(11) NOT NULL,
    `text` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id_badge`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'productbadges_product` (
    `id_badge` INT(11) NOT NULL,
    `id_product` INT(11) NOT NULL,
    PRIMARY KEY (`id_badge`, `id_product`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}

return true;