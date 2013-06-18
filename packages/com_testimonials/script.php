<?php
/**
 * @package     Testimonials
 * @subpackage  com_testimonials
 *
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Script file of Testimonials Component.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class Com_TestimonialsInstallerScript
{
	/**
	 * Called before any type of action.
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install).
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
		// Get the application.
		$app         = JFactory::getApplication();
		$min_version = (string) $adapter->get('manifest')->attributes()->version;
		$jversion    = new JVersion;

		if (!$jversion->isCompatible($min_version))
		{
			$app->enqueueMessage(JText::sprintf('COM_TESTIMONIALS_VERSION_UNSUPPORTED', $min_version), 'error');
			return false;
		}

		if (get_magic_quotes_gpc())
		{
			$app->enqueueMessage(JText::_('COM_TESTIMONIALS_MAGIC_QUOTES'), 'error');
			return false;
		}

		// Storing old release number for process in postflight.
		if ($route == 'update')
		{
			$this->oldRelease = $this->getParam('version');

			// Check if update is allowed (only update from 1.0 and higher).
			if (version_compare($this->oldRelease, '1.0', '<'))
			{
				$app->enqueueMessage(JText::sprintf('COM_VIDEOS_UPDATE_UNSUPPORTED', $this->oldRelease), 'error');
				return false;
			}
		}
	}

	/**
	 * Called after any type of action.
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install).
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		// Adding content_type for testimonials.
		$table = JTable::getInstance('Contenttype', 'JTable');

		if (!$table->load(array('type_alias' => 'com_testimonials.testimonial')))
		{
			$common = new stdClass;
			$common->core_content_item_id = 'id';
			$common->core_name            = 'name';
			$common->core_state           = 'state';
			$common->core_alias           = 'alias';
			$common->core_created_time    = 'created';
			$common->core_modified_time   = 'modified';
			$common->core_body            = 'testimonial';
			$common->core_hits            = 'hits';
			$common->core_publish_up      = 'publish_up';
			$common->core_publish_down    = 'publish_down';
			$common->core_access          = 'access';
			$common->core_params          = 'params';
			$common->core_featured        = 'featured';
			$common->core_metadata        = 'metadata';
			$common->core_language        = 'language';
			$common->core_images          = 'images';
			$common->core_urls            = 'urls';
			$common->core_version         = 'version';
			$common->core_ordering        = 'ordering';
			$common->core_metakey         = 'metakey';
			$common->core_metadesc        = 'metadesc';
			$common->core_catid           = 'catid';
			$common->core_xreference      = 'xreference';
			$common->asset_id             = 'asset_id';

			$field_mappings = new stdClass;
			$field_mappings->common[] = $common;
			$field_mappings->special  = array();

			$special = new stdClass;
			$special->dbtable = '#__testimonials';
			$special->key     = 'id';
			$special->type    = 'Testimonial';
			$special->prefix  = 'TestimonialsTable';
			$special->config  = 'array()';

			$common = new stdClass;
			$common->dbtable  = '#__ucm_content';
			$common->key      = 'ucm_id';
			$common->type     = 'Corecontent';
			$common->prefix   = 'JTable';
			$common->config   = 'array()';

			$table_object = new stdClass;
			$table_object->special = $special;
			$table_object->common  = $common;

			$contenttype['type_title']     = 'Testimonial';
			$contenttype['type_alias']     = 'com_testimonials.testimonial';
			$contenttype['table']          = json_encode($table_object);
			$contenttype['rules']          = '';
			$contenttype['field_mappings'] = json_encode($field_mappings);
			$contenttype['router']         = 'TestimonialsHelperRoute::getTestimonialRoute';

			$table->save($contenttype);
		}
	}

	/**
	 * Called on installation.
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function install(JAdapterInstance $adapter)
	{
		// Set the redirect location.
		$adapter->getParent()->setRedirectURL('index.php?option=com_testimonials');
	}

	/**
	 * Called on update.
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function update(JAdapterInstance $adapter)
	{

	}

	/**
	 * Called on uninstallation.
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		echo '<p>' . JText::_('COM_TESTIMONIALS_UNINSTALL_TEXT') . '</p>';
	}
}
