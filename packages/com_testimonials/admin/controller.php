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
 * Testimonials Component Controller.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsController extends JControllerLegacy
{
	/**
	 * @var     string  The default view.
	 * @since   3.1
	 */
	protected $default_view = 'cpanel';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  This object to support chaining.
	 *
	 * @since   3.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set the default view name and format from the Request.
		$view   = $this->input->get('view', 'cpanel');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'testimonial' && $layout == 'edit' && !$this->checkEditId('com_testimonials.edit.testimonial', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_testimonials&view=cpanel', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
