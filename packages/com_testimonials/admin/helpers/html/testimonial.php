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
 * Utility class working with testimonial.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
abstract class JHtmlTestimonial
{
	/**
	 * Displays a batch widget for moving or copying items.
	 *
	 * @param   string  $extension  The extension.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   3.1
	 */
	public static function item($extension)
	{
		// Create the copy/move options.
		$options = array(JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
			JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE')));

		// Create the batch selector to move or copy.
		$lines = array('<div id="batch-move-copy" class="control-group radio">',
			JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'), '</div><hr />');

		return implode("\n", $lines);
	}

	/**
	 * Show the feature/unfeature links.
	 *
	 * @param   int      $value      The state value.
	 * @param   int      $i          Row number.
	 * @param   boolean  $canChange  Is user allowed to change?
	 *
	 * @return  string  HTML code.
	 */
	public static function featured($value = 0, $i = 0, $canChange = true)
	{
		// Load the tooltip bootstrap script.
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action.
		$states = array(
			0 => array('unfeatured', 'testimonials.featured', 'COM_TESTIMONIALS_UNFEATURED', 'COM_TESTIMONIALS_TOGGLE_TO_FEATURE'),
			1 => array('featured', 'testimonials.unfeatured', 'COM_TESTIMONIALS_FEATURED', 'COM_TESTIMONIALS_TOGGLE_TO_UNFEATURE'),
		);
		$state  = JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon   = $state[0];

		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JText::_($state[3]) . '"><i class="icon-' . $icon . '"></i></a>';
		}
		else
		{
			$html = '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JText::_($state[2]) . '"><i class="icon-' . $icon . '"></i></a>';
		}

		return $html;
	}
}
