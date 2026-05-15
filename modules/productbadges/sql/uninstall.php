<?php
$sql = [
    'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'productbadges_product`',
    'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'productbadges_badge_lang`',
    'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'productbadges_badge`',
];

foreach ($sql as $query) {
    Db::getInstance()->execute($query);
}

return true;