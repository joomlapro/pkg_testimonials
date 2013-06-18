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

// Get the input.
$input = JFactory::getApplication()->input;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_testimonials'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Register dependent classes.
JLoader::register('TestimonialsHelper', __DIR__ . '/helpers/testimonials.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Testimonials');
$controller->execute($input->get('task'));
$controller->redirect();
