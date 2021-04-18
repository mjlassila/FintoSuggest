<?php
/**
 * Finto Suggest
 * 
 * @copyright Copyright 2007-2012 UCSC Library Digital Initiatives
 * @copyright Copyright 2015 Matti Lassila
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The finto_suggests table.
 * 
 * @package Omeka\Plugins\FintoSuggest
 */
class Table_FintoSuggest extends Omeka_Db_Table
{
    /**
     * @var array $_suggestEndpoints List of suggest endpoints 
     * corresponding to controlled vocabularies
     * and authorities made available by the Finto project
     * 
     */
    private $_suggestEndpoints = array(
        'yso' => 'Yleinen suomalainen ontologia',
        'yso-paikat' => 'YSO-paikat',
        'maotao' => 'MAO/TAO - Museo- ja taideteollisuusalan ontologia', 
        'kauno' => 'Kaunokki',
        'pto' => 'PTO - Paikkatieto-ontologia',
        'cn' => 'Suomalaiset yhteisönimet',
        'liito' => 'LIITO - Liiketoimintaontologia',
        'lapponica' => 'Lapponica',
        'musa' => 'MUSA - Musiikin asiasanasto',
        'valo' => 'VALO - Valokuvausalan ontologia',
        'mts' => 'Metatietosanasto',
        'ysa' => 'Yleinen suomalainen asiasanasto'
				       );
    
    /**
     * Find a suggest record by element ID.
     * 
     * @param int|string $elementId
     * @return FintoSuggest|null
     */
    public function findByElementId($elementId)
    {
        $select = $this->getSelect()->where('element_id = ?', $elementId);
        return $this->fetchObjects($select);
    }
    
    /**
     * Get the suggest endpoints.
     * 
     * @return array
     */
    public function getSuggestEndpoints()
    {
        return $this->_suggestEndpoints;
    }
}
