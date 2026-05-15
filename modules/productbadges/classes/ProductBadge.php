<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductBadge extends ObjectModel
{
    public $background_color;
    public $text_color;
    public $position;
    public $active;
    public $text;

    public static $definition = [
        'table' => 'productbadges_badge',
        'primary' => 'id_badge',
        'multilang' => true,
        'fields' => [
            'background_color' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isColor',
                'required' => true,
                'size' => 7,
            ],
            'text_color' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isColor',
                'required' => true,
                'size' => 7,
            ],
            'position' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'text' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'required' => true,
                'size' => 50,
            ],
        ],
    ];
}