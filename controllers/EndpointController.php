<?php
/**
 * Finto Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @copyright Copyright 2015 Matti Lassila
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Finto Suggest Endpoint controller.
 * 
 * @package FintoSuggest
 */

class FintoSuggest_EndpointController extends Omeka_Controller_AbstractActionController
{

    /**
     * Proxy for the Finto Suggest suggest endpoints, used by the 
     * autosuggest feature.
     *
     * @return void
     */
    public function proxyAction()
    {
      //get the term
      $term = $this->getRequest()->getParam('term');

        // Get the suggest record.
        $elementId = $this->getRequest()->getParam('element-id');
        $FintoSuggests = $this->_helper->db->getTable('FintoSuggest')->findByElementId($elementId);

        $results = array();
        foreach($FintoSuggests as $FintoSuggest) {
            //create the query
            $query = $this->_getSparql($FintoSuggest['suggest_endpoint'],$term,'fi');

            $fullurl = 'http://api.finto.fi/rest/v1/search?'.$query;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$fullurl );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);  
            
            $json = json_decode($response);
            foreach($json->results as $result) {
                $results[] = $result->prefLabel;
            }
        }
	   
        $this->_helper->json($results);
    }

    private function _stream_download($Url) {
        $context_options = array(
            'http' => array(
                'method'=>'GET'
            )
        );
        $context = stream_context_create($context_options);
        //$contents = file_get_contents($Url,NULL,$context);
        //die($Url);
        $contents = file_get_contents($Url);
        return $contents;
    }

    /**
     * Create a  query to search Finto vocabulary service for possible 
     * autocompletions
     * 
     * @param string $vocab The name of the vocabulary to query (e.g.
     * "tgn", "aat", "ulan")
     * @param string $term The first few characters of the term to autosuggest
     * @return string
     */
    private function _getSparql($vocab, $term, $language)  {
            return(
                'vocab='.$vocab.'&'.
                'query='.$term.'*&'.
                'lang='.$language
            );
    }

}