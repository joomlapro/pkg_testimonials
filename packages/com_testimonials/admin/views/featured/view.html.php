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
 * View class for a list of featured testimonials.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsViewFeatured extends JViewLegacy
{
	protected $items;

	protected $pagination;

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
		// Initialise variables.
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$this->authors    = $this->get('Authors');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Load the submenu.
		TestimonialsHelper::addSubmenu('featured');

		// Levels filter.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('J1'));
		$options[] = JHtml::_('select.option', '2', JText::_('J2'));
		$options[] = JHtml::_('select.option', '3', JText::_('J3'));
		$options[] = JHtml::_('select.option', '4', JText::_('J4'));
		$options[] = JHtml::_('select.option', '5', JText::_('J5'));
		$options[] = JHtml::_('select.option', '6', JText::_('J6'));
		$options[] = JHtml::_('select.option', '7', JText::_('J7'));
		$options[] = JHtml::_('select.option', '8', JText::_('J8'));
		$options[] = JHtml::_('select.option', '9', JText::_('J9'));
		$options[] = JHtml::_('select.option', '10', JText::_('J10'));

		$this->f_levels = $options;

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

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
		$state = $this->get('State');
		$canDo = TestimonialsHelper::getActions();
		$user  = JFactory::getUser();

		JToolbarHelper::title(JText::_('COM_TESTIMONIALS_MANAGER_FEATURED_TITLE'), 'featured.png');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('testimonial.add');
		}
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('testimonial.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('testimonials.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('testimonials.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::custom('featured.delete', 'remove.png', 'remove_f2.png', 'JTOOLBAR_REMOVE', true);
			JToolbarHelper::archiveList('testimonials.archive');
			JToolbarHelper::checkin('testimonials.checkin');
		}

		if ($state->get('filter.published') == - 2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'testimonials.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('testimonials.trash');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_testimonials');
		}

		JToolBarHelper::help('featured', $com = true);

		JHtmlSidebar::setAction('index.php?option=com_testimonials&view=featured');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_MAX_LEVELS'),
			'filter_level',
			JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_AUTHOR'),
			'filter_author_id',
			JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by.
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value.
	 *
	 * @since   3.1
	 */
	protected function getSortFields()
	{
		return array(
			'fp.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_TESTIMONIALS_HEADING_NAME'),
			'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			'a.created_by' => JText::_('JAUTHOR'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.created' => JText::_('JDATE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
