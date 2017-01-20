<?php

/**
 * @version     3.15.0
 * @package     com_einsatzkomponente
 * @copyright   Copyright (C) 2017 by Ralf Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ralf Meyer <ralf.meyer@mail.de> - https://einsatzkomponente.de
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Einsatzkomponente.
 */
class EinsatzkomponenteViewEinsatzarchiv extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;
    protected $version;

    /**
     * Display the view
     */
    public function display($tpl = null) {
		require_once JPATH_SITE.'/administrator/components/com_einsatzkomponente/helpers/einsatzkomponente.php'; // Helper-class laden
        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params = $app->getParams('com_einsatzkomponente');
        $this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		
		$layout_detail = $this->params->get('layout_detail',''); // Detailbericht Layout
		$this->layout_detail_link = '';  
		if ($layout_detail) : $this->layout_detail_link = '&layout='.$layout_detail;  endif; // Detailbericht Layout 'default' ?

		
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		$this->document->link = JRoute::_('index.php?option=com_einsatzkomponente&view=einsatzarchiv&Itemid='.$menu->id);;
		foreach ( $this->items as $item )
		{
					
			//$item->auswahl_orga = str_replace(",", " +++ ", $this->auswahl_orga);
			$summary = $this->escape( $item->summary );
			$title = html_entity_decode( $summary );
			$desc = strip_tags( $item->desc);
			$desc = (strlen($desc) > $this->params->get('rss_chars','1000')) ? substr($desc,0,strrpos(substr($desc,0,$this->params->get('rss_chars','1000')+1),' ')).' ...' : $desc;
			$nr = EinsatzkomponenteHelper::ermittle_einsatz_nummer($item->date1);
			// url link to article
			// & used instead of &amp; as this is converted by feed creator
			$link = JRoute::_('index.php?option=com_einsatzkomponente&view=einsatzbericht'.$this->layout_detail_link.'&id='.$item->id);

			//$auswahl_orga=  implode(',',$this->auswahl_orga); 
			$item->auswahl_orga = str_replace(",", " +++ ", $item->auswahl_orga);

			// strip html from feed item description text
			/*$description	= ($params->get('feed_summary', 0) ? $item->introtext.$item->fulltext : $item->introtext);
			$author			= $item->created_by_alias ? $item->created_by_alias : $item->author;
*/
			// load individual item creator class
			$rss_item = new JFeedItem();
			$rss_item->title = "+++ Einsatz Nr: " . $nr . " - " . $title . " +++";
			$rss_item->link 		= $link;

			$rss_item->description 	.= '<table>';
			
			if ($item->auswahl_orga) :
			$rss_item->description 	.= '<tr><td><b>Einsatzkräfte</b>: +++ '.$item->auswahl_orga.' +++</td></tr>';
			endif;
			
			if ($desc) :
			$rss_item->description 	.= '<tr><td>'.$desc.'</td></tr>';
			endif;

			if ($this->params->get('display_rss_image','0')) :
			if ($item->image) :
			$rss_item->description 	.= '<tr><td><img src="'.JURI::base().$item->image.'" width="'.$this->params->get('rss_image_width','250px').'" height="'.$this->params->get('rss_image_height','').' "/></td></tr>';
			endif;
			endif;
			
			
		    $rss_item->date = date('d.m.Y H:i', $item->date1);
			
			$rss_item->description 	.= '</table>';
			
			/*$rss_item->category   	= $item->category;
			$rss_item->author		= $author;
			if ($feedEmail == 'site') {
				$rss_item->authorEmail = $siteEmail;
			}
			else {
				$rss_item->authorEmail = $item->author_email;
			}
*/
			// loads item info into rss array
			$this->document->addItem( $rss_item );
		}


		
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
;
            throw new Exception(implode("\n", $errors));
        }

        parent::display($tpl);
    }


}