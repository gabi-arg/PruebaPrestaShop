<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/ProductBadge.php';

class Productbadges extends Module
{
    const CONFIG_ENABLED = 'PRODUCTBADGES_ENABLED';
    const CONFIG_SHOW_LISTINGS = 'PRODUCTBADGES_SHOW_LISTINGS';
    const CONFIG_SHOW_PRODUCT_PAGE = 'PRODUCTBADGES_SHOW_PRODUCT_PAGE';
    const CONFIG_MAX_VISIBLE = 'PRODUCTBADGES_MAX_VISIBLE';

    public function __construct()
    {
        $this->name = 'productbadges';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Gabriela Duran';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Badges');
        $this->description = $this->l('Gestión de etiquetas visuales para productos del catálogo.');
        $this->confirmUninstall = $this->l('¿Estás segura de que quieres desinstalar este módulo?');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install() &&
            $this->installDb() &&
            $this->installConfig() &&
            $this->installTab() &&
            $this->registerHook('displayProductListReviews') &&
            $this->registerHook('displayProductBadgesListing') &&
            $this->registerHook('displayAfterProductThumbs') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('actionProductAdd');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->uninstallDb() &&
            $this->uninstallConfig() &&
            $this->uninstallTab();
    }
    private function installDb()
    {
        return include dirname(__FILE__) . '/sql/install.php';
    }
    private function uninstallDb()
{
    return include dirname(__FILE__) . '/sql/uninstall.php';
}
    private function installConfig()
    {
        return Configuration::updateValue(self::CONFIG_ENABLED, 1) &&
            Configuration::updateValue(self::CONFIG_SHOW_LISTINGS, 1) &&
            Configuration::updateValue(self::CONFIG_SHOW_PRODUCT_PAGE, 1) &&
            Configuration::updateValue(self::CONFIG_MAX_VISIBLE, 3);
    }

    private function uninstallConfig()
    {
        return Configuration::deleteByName(self::CONFIG_ENABLED) &&
            Configuration::deleteByName(self::CONFIG_SHOW_LISTINGS) &&
            Configuration::deleteByName(self::CONFIG_SHOW_PRODUCT_PAGE) &&
            Configuration::deleteByName(self::CONFIG_MAX_VISIBLE);
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitProductbadgesModule')) {
            $this->postProcess();
            $output .= $this->displayConfirmation($this->l('Configuración actualizada.'));
        }

        $output .= $this->renderForm();

        return $output;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_cancel_button = false;
        $helper->module = $this;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitProductbadgesModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->languages = $this->context->controller->getLanguages();
        $helper->allow_employee_form_lang = Configuration::get('PS_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->fields_value = $this->getConfigFormValues();

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuración del módulo'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activar módulo'),
                        'name' => self::CONFIG_ENABLED,
                        'values' => [
                            ['id' => 'enabled_on', 'value' => 1, 'label' => $this->l('Sí')],
                            ['id' => 'enabled_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Mostrar en listados'),
                        'name' => self::CONFIG_SHOW_LISTINGS,
                        'values' => [
                            ['id' => 'show_listings_on', 'value' => 1, 'label' => $this->l('Sí')],
                            ['id' => 'show_listings_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                        'desc' => $this->l('Activa las badges en vistas de categoría, búsqueda y home si el tema soporta el hook.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Mostrar en ficha de producto'),
                        'name' => self::CONFIG_SHOW_PRODUCT_PAGE,
                        'values' => [
                            ['id' => 'show_product_page_on', 'value' => 1, 'label' => $this->l('Sí')],
                            ['id' => 'show_product_page_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Número máximo de badges visibles por producto'),
                        'name' => self::CONFIG_MAX_VISIBLE,
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Limita la cantidad de etiquetas mostradas por producto.'),
                        'validation' => 'isUnsignedInt',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Guardar'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        return [
            self::CONFIG_ENABLED => Tools::getValue(self::CONFIG_ENABLED, Configuration::get(self::CONFIG_ENABLED)),
            self::CONFIG_SHOW_LISTINGS => Tools::getValue(self::CONFIG_SHOW_LISTINGS, Configuration::get(self::CONFIG_SHOW_LISTINGS)),
            self::CONFIG_SHOW_PRODUCT_PAGE => Tools::getValue(self::CONFIG_SHOW_PRODUCT_PAGE, Configuration::get(self::CONFIG_SHOW_PRODUCT_PAGE)),
            self::CONFIG_MAX_VISIBLE => Tools::getValue(self::CONFIG_MAX_VISIBLE, Configuration::get(self::CONFIG_MAX_VISIBLE)),
        ];
    }

    protected function postProcess()
    {
        Configuration::updateValue(self::CONFIG_ENABLED, (int) Tools::getValue(self::CONFIG_ENABLED));
        Configuration::updateValue(self::CONFIG_SHOW_LISTINGS, (int) Tools::getValue(self::CONFIG_SHOW_LISTINGS));
        Configuration::updateValue(self::CONFIG_SHOW_PRODUCT_PAGE, (int) Tools::getValue(self::CONFIG_SHOW_PRODUCT_PAGE));
        Configuration::updateValue(self::CONFIG_MAX_VISIBLE, max(1, (int) Tools::getValue(self::CONFIG_MAX_VISIBLE)));
    }

    protected function isEnabledModule()
    {
        return (bool) Configuration::get(self::CONFIG_ENABLED);
    }

    protected function isListingsEnabled()
    {
        return (bool) Configuration::get(self::CONFIG_SHOW_LISTINGS);
    }

    protected function isProductPageEnabled()
    {
        return (bool) Configuration::get(self::CONFIG_SHOW_PRODUCT_PAGE);
    }

    protected function getMaxVisibleBadges()
    {
        return max(1, (int) Configuration::get(self::CONFIG_MAX_VISIBLE));
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminProductBadges';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Product Badges';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;
        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminProductBadges');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function hookDisplayHeader()
    {
        if (!$this->isEnabledModule()) {
            return;
        }

        $this->context->controller->addCSS($this->_path . 'views/css/productbadges.css');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = isset($params['id_product']) ? (int) $params['id_product'] : (int) Tools::getValue('id_product');

        $badges = Db::getInstance()->executeS('
            SELECT b.id_badge, COALESCE(bl.text, bdl.text) AS text, b.background_color, b.text_color
            FROM `' . _DB_PREFIX_ . 'productbadges_badge` b
            LEFT JOIN `' . _DB_PREFIX_ . 'productbadges_badge_lang` bl 
                ON (b.id_badge = bl.id_badge AND bl.id_lang = ' . (int) $this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'productbadges_badge_lang` bdl 
                ON (b.id_badge = bdl.id_badge AND bdl.id_lang = ' . (int) Configuration::get('PS_LANG_DEFAULT') . ')
            WHERE b.active = 1
        ');

        $assigned_ids = [];
        if ($id_product > 0) {
            $assigned = Db::getInstance()->executeS('
                SELECT id_badge FROM `' . _DB_PREFIX_ . 'productbadges_product`
                WHERE id_product = ' . $id_product
            );
            $assigned_ids = array_column($assigned, 'id_badge');
        }

        $this->context->smarty->assign([
            'badges' => $badges,
            'assigned_ids' => $assigned_ids,
            'badges_admin_url' => $this->context->link->getAdminLink('AdminProductBadges'),
            'id_product' => $id_product,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/badges_tab.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $this->saveProductBadges((int) $params['id_product']);
    }

    public function hookActionProductAdd($params)
    {
        $this->saveProductBadges((int) $params['id_product']);
    }

    private function saveProductBadges($id_product)
    {
        if (!$id_product || !Tools::getValue('productbadges_submitted')) {
            return;
        }

        Db::getInstance()->delete('productbadges_product', 'id_product = ' . $id_product);

        $badges = Tools::getValue('badges', []);
        if (!is_array($badges)) {
            return;
        }

        foreach ($badges as $id_badge) {
            Db::getInstance()->insert('productbadges_product', [
                'id_badge' => (int) $id_badge,
                'id_product' => $id_product,
            ]);
        }
    }

    private function getBadgesForProduct($id_product)
    {
        $badges = Db::getInstance()->executeS('
            SELECT b.id_badge, COALESCE(bl.text, bdl.text) AS text, b.background_color, b.text_color, b.position
            FROM `' . _DB_PREFIX_ . 'productbadges_badge` b
            LEFT JOIN `' . _DB_PREFIX_ . 'productbadges_badge_lang` bl 
                ON (b.id_badge = bl.id_badge AND bl.id_lang = ' . (int) $this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'productbadges_badge_lang` bdl 
                ON (b.id_badge = bdl.id_badge AND bdl.id_lang = ' . (int) Configuration::get('PS_LANG_DEFAULT') . ')
            INNER JOIN `' . _DB_PREFIX_ . 'productbadges_product` bp 
                ON (b.id_badge = bp.id_badge AND bp.id_product = ' . (int) $id_product . ')
            WHERE b.active = 1
        ');

        if (!is_array($badges)) {
            return [];
        }

        return array_slice($badges, 0, $this->getMaxVisibleBadges());
    }

    public function hookDisplayProductListReviews($params)
    {
        if (!$this->isEnabledModule() || !$this->isListingsEnabled()) {
            return '';
        }

        return $this->renderProductBadges($params);
    }

    public function hookDisplayProductBadgesListing($params)
    {
        if (!$this->isEnabledModule() || !$this->isListingsEnabled()) {
            return '';
        }

        return $this->renderProductBadges($params);
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        if (!$this->isEnabledModule() || !$this->isListingsEnabled()) {
            return '';
        }

        return $this->renderProductBadges($params);
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        if (!$this->isEnabledModule() || !$this->isProductPageEnabled()) {
            return '';
        }

        return $this->renderProductBadges($params);
    }

    private function getProductIdFromParams(array $params)
    {
        if (!isset($params['product']) || !is_array($params['product'])) {
            return 0;
        }

        $product = $params['product'];

        if (!empty($product['id_product'])) {
            return (int) $product['id_product'];
        }

        if (!empty($product['id'])) {
            return (int) $product['id'];
        }

        return 0;
    }

    private function renderProductBadges(array $params)
    {
        $id_product = $this->getProductIdFromParams($params);

        if (!$id_product) {
            return '';
        }

        $badges = $this->getBadgesForProduct($id_product);

        if (!$badges) {
            return '';
        }

        $this->context->smarty->assign('badges', $badges);

        return $this->display(__FILE__, 'views/templates/hook/displayProductBadges.tpl');
    }
}