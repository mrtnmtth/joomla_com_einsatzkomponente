<?php
/**
 * @version     3.15.0
 * @package     com_einsatzkomponente
 * @copyright   Copyright (C) 2017 by Ralf Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ralf Meyer <ralf.meyer@mail.de> - https://einsatzkomponente.de
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
/**
 * Supports an HTML select list of categories
 */
class JFormFieldEinsatzfuehrer extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'einsatzfuehrer';
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$address = array();
        
$id = JFactory::getApplication()->input->getVar('id', 0);

$params = JComponentHelper::getParams('com_einsatzkomponente');

$db = JFactory::getDBO();
$query = 'SELECT id, boss2 as title FROM #__eiko_einsatzberichte WHERE state="1" GROUP BY boss2 ASC';
$db->setQuery($query);
$arrayDb = $db->loadObjectList();

$html[]='<input class="control-label" type="text"  name="'. $this->name.'"  id="'.$this->id.'"  value="'.$this->value.'" size="'.$this->size.'" />';

if (count($arrayDb)):
$array[] = JHTML::_('select.option', '', 'Einsatzführer auswählen', 'title', 'title');
$array = array_merge($array, $arrayDb);
$html[].= '<br/><br/>'.JHTML::_('select.genericlist', $array, "einsatzfuehrer", 'onchange="changeText_einsatzfuehrer()" ', 'title', 'title', '0');

$html[].='<script type="text/javascript">
function changeText_einsatzfuehrer(){
    var userInput1 = document.getElementById("einsatzfuehrer").value;
	document.getElementById("'.$this->id.'").value = userInput1;
}
</script>';
endif;
        
		return implode($html);
	}
}