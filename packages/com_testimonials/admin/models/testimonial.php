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
 * Item Model for an Testimonial.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsModelTestimonial extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var     string
	 * @since   3.1
	 */
	protected $text_prefix = 'COM_TESTIMONIALS_TESTIMONIAL';

	/**
	 * Batch copy items to a new testimonial.
	 *
	 * @param   integer  $category  The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   3.1
	 */
	protected function batchCopy($category, $pks, $contexts)
	{
		// Initialiase variables.
		$table = $this->getTable();
		$i     = 0;

		// Parent exists so we let's proceed.
		while (!empty($pks))
		{
			// Pop the first ID off the stack.
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists.
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error.
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the name & alias.
			$data = $this->generateNewTitle(null, $table->alias, $table->name);
			$table->name = $data['0'];
			$table->alias = $data['1'];

			// Reset the ID because we are making a copy.
			$table->id = 0;

			// Get the featured state.
			$featured = $table->featured;

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Get the new item ID.
			$newId = $table->get('id');

			// Add the new ID to the array.
			$newIds[$i] = $newId;
			$i++;

			// Check if the testimonial was featured and update the #__testimonials_frontpage table.
			if ($featured == 1)
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__testimonials_frontpage'))
					->values($newId . ', 0');
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Clean the cache.
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   3.1
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return;
			}

			// Get the current user object.
			$user = JFactory::getUser();

			return $user->authorise('core.delete', $this->option . '.testimonial.' . (int) $record->id);
		}
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   3.1
	 */
	protected function canEditState($record)
	{
		// Get the current user object.
		$user = JFactory::getUser();

		// Check for existing testimonial.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', $this->option . '.testimonial.' . (int) $record->id);
		}
		// Default to component settings if testimonial not known.
		else
		{
			return parent::canEditState($this->option);
		}
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function prepareTable($table)
	{
		// Set the publish date to now.
		$db = $this->getDbo();

		if ($table->state == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = JFactory::getDate()->toSql();
		}

		if ($table->state == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $db->getNullDate();
		}

		// Increment the content version number.
		$table->version++;
	}

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
	public function getTable($type = 'Testimonial', $prefix = 'TestimonialsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the metadata field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, $this->option . '.testimonial');
				$item->metadata['tags'] = $item->tags;
			}
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm($this->option . '.testimonial', 'testimonial', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}

		// Get the current user object.
		$user = JFactory::getUser();

		// Check for existing testimonial.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', $this->option . '.testimonial.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', $this->option)))
		{
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('featured', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an testimonial you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('featured', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   3.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState($this->option . '.edit.testimonial.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->option . '.testimonial', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function save($data)
	{
		// Get the application.
		$app = JFactory::getApplication();

		// Alter the name for save as copy.
		if ($app->input->get('task') == 'save2copy')
		{
			list($name, $alias) = $this->generateNewTitle(null, $data['alias'], $data['name']);

			$data['name'] = $name;
			$data['alias'] = $alias;
			$data['state'] = 0;
		}

		if (parent::save($data))
		{
			if (isset($data['featured']))
			{
				$this->featured($this->getState($this->getName() . '.id'), $data['featured']);
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to toggle the featured setting of testimonials.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;

		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_TESTIMONIALS_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable('Featured', 'TestimonialsTable');

		try
		{
			// Initialiase variables.
			$db = $this->getDbo();

			// Create the base update statement.
			$query = $db->getQuery(true)
				->update($db->quoteName('#__testimonials'))
				->set('featured = ' . (int) $value)
				->where('id IN (' . implode(',', $pks) . ')');

			// Set the query and execute the update.
			$db->setQuery($query);
			$db->execute();

			if ((int) $value == 0)
			{
				// Adjust the mapping table.
				// Clear the existing features settings.
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__testimonials_frontpage'))
					->where('testimonial_id IN (' . implode(',', $pks) . ')');

				// Set the query and execute the update.
				$db->setQuery($query);
				$db->execute();
			}
			else
			{
				// First, we find out which of our new featured testimonials are already featured.
				$query = $db->getQuery(true)
					->select('f.testimonial_id')
					->from('#__testimonials_frontpage AS f')
					->where('testimonial_id IN (' . implode(',', $pks) . ')');

				// Set the query and execute the update.
				$db->setQuery($query);

				$old_featured = $db->loadColumn();

				// We diff the arrays to get a list of the testimonials that are newly featured.
				$new_featured = array_diff($pks, $old_featured);

				// Featuring.
				$tuples = array();

				foreach ($new_featured as $pk)
				{
					$tuples[] = $pk . ', 0';
				}

				if (count($tuples))
				{
					// Initialiase variables.
					$db = $this->getDbo();
					$columns = array('testimonial_id', 'ordering');

					// Create the base insert statement.
					$query = $db->getQuery(true)
						->insert($db->quoteName('#__testimonials_frontpage'))
						->columns($db->quoteName($columns))
						->values($tuples);

					// Set the query and execute the insert.
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();
		$this->cleanCache();

		return true;
	}
}
