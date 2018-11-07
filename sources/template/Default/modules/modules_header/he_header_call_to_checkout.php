<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class he_header_call_to_checkout {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function __construct() {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);
      $this->title = CLICSHOPPING::getdef('module_header_call_to_checkout_title');
      $this->description = CLICSHOPPING::getdef('module_header_call_to_checkout_desription');

      $this->title = CLICSHOPPING::getDef('module_header_call_to_checkout_title');
      $this->description = CLICSHOPPING::getDef('module_header_call_to_checkout_description');


      if ( defined('MODULE_HEADER_CALL_TO_CHECKOUT_STATUS') ) {
        $this->sort_order = MODULE_HEADER_CALL_TO_CHECKOUT_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_CALL_TO_CHECKOUT_STATUS == 'True');
        $this->pages = MODULE_HEADER_CALL_TO_CHECKOUT_DISPLAY_PAGES;
      }
    }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      if ($CLICSHOPPING_ShoppingCart->getCountContents() > 0 && !isset($_GET['Checkout'])) {
        $call_to_checkout = '<div class="alert alert-danger text-md-center headerCallToCheckoutAlertDanger" role="alert"><i class="fas fa-flag fa-lg"></i>' . CLICSHOPPING::getDef('text_call_to_checkout', ['count_content' =>$CLICSHOPPING_ShoppingCart->getCountContents()]) . '&nbsp;&nbsp;' . HTML::button(CLICSHOPPING::getDef('button_call_to_checkout'), 'fas fa-thumbs-o-up fa-lg', CLICSHOPPING::link(null, 'Checkout&Shipping'), 'primary', NULL, 'btn-success') . '</div>';
      }else{
        $call_to_checkout = '';
      }

      ob_start();

      require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/header_call_to_checkout'));
      $template = ob_get_clean();

      $CLICSHOPPING_Template->addBlock($template, $this->group);

    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_HEADER_CALL_TO_CHECKOUT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_HEADER_CALL_TO_CHECKOUT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_HEADER_CALL_TO_CHECKOUT_SORT_ORDER',
          'configuration_value' => '10',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer ou la boxe doit s\'afficher',
          'configuration_key' => 'MODULE_HEADER_CALL_TO_CHECKOUT_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Sélectionnez les pages o&ugrave; la boxe doit être présente',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
        ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULE_HEADER_CALL_TO_CHECKOUT_STATUS',
                   'MODULE_HEADER_CALL_TO_CHECKOUT_SORT_ORDER',
                   'MODULE_HEADER_CALL_TO_CHECKOUT_DISPLAY_PAGES'
                  );
    }
  }
