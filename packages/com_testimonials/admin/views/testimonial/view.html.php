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
 * View to edit a testimonial.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsViewTestimonial extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

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
		// Initialiase variables.
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = TestimonialsHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		// Initialiase variables.
		$user       = JFactory::getUser();
		$userId     = $user->get('id');
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Since we don't track these assets at the item level.
		$canDo      = TestimonialsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_TESTIMONIALS_PAGE_' . ($checkedOut ? 'VIEW_TESTIMONIAL' : ($isNew ? 'ADD_TESTIMONIAL' : 'EDIT_TESTIMONIAL'))), 'testimonial-add.png');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit'))
		{
			JToolbarHelper::apply('testimonial.apply');
			JToolbarHelper::save('testimonial.save');
		}

		if (!$checkedOut && $canDo->get('core.create'))
		{
			JToolbarHelper::save2new('testimonial.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('testimonial.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('testimonial.cancel');
		}
		else
		{
			JToolbarHelper::cancel('testimonial.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolBarHelper::help('testimonial', $com = true);
	}
}
