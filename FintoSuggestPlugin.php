<?php
/**
 * Finto Vocabulary Suggest
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @copyright Copyright 2015 Matti Lassila
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Finto Vocabulary Suggest plugin.
 * 
 * @package FintoSuggest
 */
class FintoSuggestPlugin extends Omeka_Plugin_AbstractPlugin
{

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install', 
        'uninstall', 
        'initialize', 
        'define_acl', 
        'admin_head'
    );
    
    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main', 
    );

    protected $_options = array(
        'fintoLimit'=>'10'
    );
    
    /**
     * Install the plugin.
     *
     * @return void
     */
    public function hookInstall()
    {
        $this->_installOptions();
        $sql1 = "
        CREATE TABLE `{$this->_db->FintoSuggest}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `element_id` int(10) unsigned NOT NULL,
            `suggest_endpoint` tinytext COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $this->_db->query($sql1);
    }
    
    /**
     * Uninstall the plugin.
     *
     * @return void
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
        $sql1 = "DROP TABLE IF EXISTS `{$this->_db->FintoSuggest}`";
        $this->_db->query($sql1);
    }
    
    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function hookInitialize()
    {
        // Register the SelectFilter controller plugin.
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new FintoSuggest_Controller_Plugin_Autosuggest);
        
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    /**
     * Define the plugin's access control list.
   *
   * @param array $args This array contains a reference to
   * the zend ACL under it's 'acl' key.
   * @return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('FintoSuggest_Index');
    }
    
    /**
     * Add the FintoSuggest link to the admin main navigation.
     * 
     * @param array $nav Array of links for admin nav section
     * @return array $nav Updated array of links for admin nav section
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Finto Suggest'), 
            'uri' => url('finto-suggest'), 
            'resource' => 'FintoSuggest_Index', 
            'privilege' => 'index', 
        );
        return $nav;
    }

    public function markSuggestField($components, $args) {
        $components['description'] = $components['description']." (This element has autosuggest activated using the FintoSuggest plugin)";
        return($components);
    }

    public function hookAdminHead() {
        $suggests = get_db()->getTable('FintoSuggest')->findAll();
        foreach($suggests as $suggest) {
            $element = get_db()->getTable('Element')->find($suggest->element_id);
            add_filter(array('ElementForm', 'Item', $element->getElementSet()->name, $element->name),array($this,'markSuggestField'));
        }
    }

    
}
