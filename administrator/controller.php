<?php
/**
 * @version     3.0.0
 * @package     com_einsatzkomponente
 * @copyright   Copyright (C) 2013 by Ralf Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ralf Meyer <webmaster@feuerwehr-veenhusen.de> - http://einsatzkomponente.de
 */
 
// No direct access
defined('_JEXEC') or die;

class EinsatzkomponenteController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_SITE.'/administrator/components/com_einsatzkomponente/helpers/einsatzkomponente.php'; // Helper-class laden
		
		$params = JComponentHelper::getParams('com_einsatzkomponente');
		
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_einsatzkomponente"');
		$parameter = json_decode( $db->loadResult(), true );
        $version = $parameter['version'];

		// Version auf BETA überprüfen, und gegebenenfalls eine Warnung ausgeben
        if($version!=str_replace("beta","",$version)):
		?>
		<table>
		<tr>
		<div class="alert alert-info j-toggle-main " style="">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<h4>Hinweis :</h4>Achtung Beta-Version <?php echo $parameter['version'];?> !!! Es wird nicht empfohlen, diese Version der Einsatzkomponente auf einer öffentlichen Live-Webseite zu betreiben. 
		</div>        
		</tr>
		</table>
		<?php endif; ?>
		

<?php
		//------------------------------------------------------------------------
        if($version!=str_replace("Premium","",$version)):
		$params->set('eiko', '1');
		endif;  ?>

<?php	// Catch Sites 

		$j_version = new JVersion;
		$response = @file("http://einsatzkomponente.de/gateway/validation.php?validation=".$params->get('validation_key','0')."&domain=".$_SERVER['SERVER_NAME']."&version=".$j_version->getShortVersion()."&eikoversion=".$version); // Request absetzen

?>

<?php	

		$view		= JFactory::getApplication()->input->getCmd('view', 'kontrollcenter');
        JFactory::getApplication()->input->set('view', $view);
		parent::display($cachable, $urlparams);
		return $this;
	}
}
