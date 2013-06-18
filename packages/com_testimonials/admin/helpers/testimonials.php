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
 * Testimonials helper.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsHelper
{
	/**
	 * The extension name.
	 *
	 * @var     string
	 * @since   3.1
	 */
	public static $extension = 'com_testimonials';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public static function addSubmenu($vName = 'cpanel')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_TESTIMONIALS_SUBMENU_CPANEL'),
			'index.php?option=com_testimonials&view=cpanel',
			$vName == 'cpanel'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TESTIMONIALS_SUBMENU_TESTIMONIALS'),
			'index.php?option=com_testimonials&view=testimonials',
			$vName == 'testimonials'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TESTIMONIALS_SUBMENU_FEATURED'),
			'index.php?option=com_testimonials&view=featured',
			$vName == 'featured'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  $testimonialId  The testimonial ID.
	 *
	 * @return  JObject  A JObject containing the allowed actions.
	 *
	 * @since   3.1
	 */
	public static function getActions($testimonialId = 0)
	{
		// Initialiase variables.
		$user    = JFactory::getUser();
		$result  = new JObject;

		if (empty($testimonialId))
		{
			$assetName = self::$extension;
		}
		else
		{
			$assetName = self::$extension . '.testimonial.' . (int) $testimonialId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
