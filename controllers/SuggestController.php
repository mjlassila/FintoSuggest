<?php
/**
 * Finto Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @copyright Copyright 2015 Matti Lassila
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Finto Suggest Assignment controller.
 * 
 * @package FintoSuggest
 */
class FintoSuggest_SuggestController extends Omeka_Controller_AbstractActionController
{

    public function deleteAction()
    {   
        if(version_compare(OMEKA_VERSION,'2.2.1') >= 0)
            $this->_validatePost();
        $suggestId = $this->getRequest()->getParam('suggest_id');
        $FintoSuggest = $this->_helper->db->getTable('FintoSuggest')->find($suggestId);
        $FintoSuggest->delete();
        $this->_helper->flashMessenger(__('Successfully disabled the element\'s suggest feature.'), 'success');
        $this->_helper->redirector('index','index');

    }

    public function editAction()
    {   
      $this->_validatePost();
      $suggestId = $this->getRequest()->getParam('suggest_id');
      $elementId = $this->getRequest()->getParam('element_id');
      $suggestEndpoint = $this->getRequest()->getParam('suggest_endpoint');

        // Don't process an invalid suggest endpoint.
        if (!$this->_suggestEndpointExists($suggestEndpoint)) {
            $this->_helper->flashMessenger(__('Invalid suggest endpoint. No changes have been made.'), 'error');
               
            $this->_helper->redirector('index','index');
        }
        
        $FintoSuggest = $this->_helper->db->getTable('FintoSuggest')->find($suggestId);
        $FintoSuggest->element_id = $elementId;
        $FintoSuggest->suggest_endpoint = $suggestEndpoint;
        $FintoSuggest->save();
        $this->_helper->flashMessenger(__('Successfully edited the element\'s suggest feature.'), 'success');
        $this->_helper->redirector('index','index');
    }

     /**
     * Adds a connection between an element and a vocabulary
     *
     * Overwrites existing connection for that element, if one exists
     *
     * @return void
     */
    public function addAction()
    {
      $this->_validatePost();
      $elementId = $this->getRequest()->getParam('element_id');
      $suggestEndpoint = $this->getRequest()->getParam('suggest_endpoint');
      
      // Don't process empty select options.
      if ('' == $elementId) {
	$this->_helper->flashMessenger(__('Please select an element to assign'), 'success');
	$this->_helper->redirector('index','index');
      }
      
      if (!$this->_suggestEndpointExists($suggestEndpoint)) {
	$this->_helper->flashMessenger(__('Invalid suggest endpoint. No changes have been made.'), 'error');
        
	$this->_helper->redirector('index','index');
      }
      
      $FintoSuggest = new FintoSuggest;
      $FintoSuggest->element_id = $elementId;
      $FintoSuggest->suggest_endpoint = $suggestEndpoint;
      $this->_helper->flashMessenger(__('Successfully enabled the element\'s suggest feature.'), 'success');
      //      }
      
      $FintoSuggest->save();
	
      $this->_helper->redirector('index','index');
    }


    /**
     * Check if the specified suggest endpoint exists.
     * 
     * @param string $suggestEndpoint An endpoint url 
     * which may or may not exist in the database
     * @return bool True if the endpoint exists, false otherwise
     */
    private function _suggestEndpointExists($suggestEndpoint)
    {
        $suggestEndpoints = $this->_helper->db->getTable('FintoSuggest')->getSuggestEndpoints();
        if (!array_key_exists($suggestEndpoint, $suggestEndpoints)) {
            return false;
        }
        return true;
    }
    
   
    private function _validatePost(){
      $csrf = new Omeka_Form_SessionCsrf;
      if(!$csrf->isValid($_POST))
          die("ERROR!");
      return true;
    }
}


