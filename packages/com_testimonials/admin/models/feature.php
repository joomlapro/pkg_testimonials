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

// Load dependent classes.
require_once __DIR__ . '/testimonial.php';

/**
 * Feature model.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsModelFeature extends TestimonialsModelTestimonial
{
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate.
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object.
	 *
	 * @since   3.1
	 */
	public function getTable($type = 'Featured', $prefix = 'TestimonialsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   3.1
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();

		return $condition;
	}
}
