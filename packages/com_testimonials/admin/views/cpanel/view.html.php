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
 * HTML View class for the Cpanel component.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsViewCpanel extends JViewLegacy
{
	protected $modules = null;

	protected $iconmodules = null;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   3.1
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$input = JFactory::getApplication()->input;

		/*
		 * Set the template - this will display cpanel.php
		 * from the selected admin template.
		 */
		$input->set('tmpl', 'cpanel');

		// Display the cpanel modules.
		$this->modules = JModuleHelper::getModules('testimonials-cpanel');

		// Display the submenu position modules.
		$this->iconmodules = JModuleHelper::getModules('testimonials-icon');

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$canDo = TestimonialsHelper::getActions();

		// Set toolbar items for the page.
		JToolbarHelper::title(JText::_('COM_TESTIMONIALS_MANAGER_CPANEL_TITLE'), 'cpanel.png');

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_testimonials');
		}

		JToolBarHelper::help('cpanel', $com = true);
	}
}
