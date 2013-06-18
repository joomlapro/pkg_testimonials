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
 * Featured Table class.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsTableFeatured extends JTable
{
	/**
	 * Whether the testimonial is or is not checked out.
	 *
	 * @var     boolean
	 * @since   3.1
	 */
	public $checked_out = 0;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  Driver A database connector object.
	 *
	 * @since   3.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__testimonials_frontpage', 'testimonial_id', $db);
	}
}
