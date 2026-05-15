<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminProductBadgesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'productbadges_badge';
        $this->className = 'ProductBadge';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bootstrap = true;
        $this->identifier = 'id_badge';

        parent::__construct();

        $this->fields_list = [
            'id_badge' => [
                'title' => $this->l('ID'),
                'width' => 50,
            ],
            'text' => [
                'title' => $this->l('Texto'),
                'lang' => true,
            ],
            'background_color' => [
                'title' => $this->l('Color de fondo'),
            ],
            'text_color' => [
                'title' => $this->l('Color de texto'),
            ],
            'position' => [
                'title' => $this->l('Posición'),
            ],
            'active' => [
                'title' => $this->l('Activo'),
                'active' => 'status',
                'type' => 'bool',
            ],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Etiqueta'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Texto'),
                    'name' => 'text',
                    'lang' => true,
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Color de fondo'),
                    'name' => 'background_color',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Color de texto'),
                    'name' => 'text_color',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Posición'),
                    'name' => 'position',
                    'required' => true,
                    'options' => [
                        'query' => [
                            ['id' => 'top-left', 'name' => $this->l('Superior izquierda')],
                            ['id' => 'top-right', 'name' => $this->l('Superior derecha')],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Activo'),
                    'name' => 'active',
                    'values' => [
                        ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Sí')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Guardar'),
            ],
        ];
    }
    public function ajaxProcessSaveBadges()
{
    $id_product = (int) Tools::getValue('id_product');

    if (!$id_product) {
        die(json_encode(['success' => false, 'error' => 'Invalid product']));
    }

    $badges_str = pSQL(Tools::getValue('badges', ''));
    
    Db::getInstance()->delete('productbadges_product', 'id_product = ' . $id_product);
    
    if ($badges_str) {
        $badge_ids = explode(',', $badges_str);
        foreach ($badge_ids as $id_badge) {
            Db::getInstance()->insert('productbadges_product', [
                'id_badge' => (int) $id_badge,
                'id_product' => $id_product,
            ]);
        }
    }
    
    die(json_encode(['success' => true]));
}
}