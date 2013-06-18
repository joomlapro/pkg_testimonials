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
 * Testimonial Table class.
 *
 * @package     Testimonials
 * @subpackage  com_testimonials
 * @since       3.1
 */
class TestimonialsTableTestimonial extends JTable
{
	/**
	 * Helper object for storing and deleting tag information.
	 *
	 * @var     JHelperTags
	 * @since   3.1
	 */
	protected $tagsHelper = null;

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  Driver A database connector object.
	 *
	 * @since   3.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__testimonials', 'id', $db);

		$this->tagsHelper = new JHelperTags;
		$this->tagsHelper->typeAlias = 'com_testimonials.testimonial';
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   3.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_testimonials.testimonial.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   3.1
	 */
	protected function _getAssetTitle()
	{
		return $this->name;
	}

	/**
	 * Method to get the parent asset id for the record.
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent.
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   3.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$assetId = null;

		// Build the query to get the asset id for the parent testimonial.
		$query = $this->_db->getQuery(true)
			->select($this->_db->quoteName('id'))
			->from($this->_db->quoteName('#__assets'))
			->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_testimonials'));

		// Get the asset id from the database.
		$this->_db->setQuery($query);

		if ($result = $this->_db->loadResult())
		{
			$assetId = (int) $result;
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 *                          to ignore while binding. [optional]
	 *
	 * @return  null|string  Null is operation was satisfactory, otherwise returns an error.
	 *
	 * @see     JTable:bind
	 * @since   3.1
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JTable::check
	 * @since   3.1
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_TESTIMONIALS_ERR_TABLES_NAME'));
			return false;
		}

		// Set alias.
		if (empty($this->alias))
		{
			$this->alias = $this->name;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string.
		if (!empty($this->metakey))
		{
			// Only process if not empty.
			// Array of characters to remove.
			$bad_characters = array("\n", "\r", "\"", "<", ">");

			// Remove bad characters.
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);

			// Create array using commas as delimiter.
			$keys = explode(', ', $after_clean);
			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key))
				{
					// Ignore blank keywords.
					$clean_keys[] = trim($key);
				}
			}

			// Put array back together delimited by ", ".
			$this->metakey = implode(", ", $clean_keys);
		}

		return true;
	}

	/**
	 * Override parent delete method to delete tags information.
	 *
	 * @param   integer  $pk  Primary key to delete.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   3.1
	 * @throws  UnexpectedValueException
	 */
	public function delete($pk = null)
	{
		$result = parent::delete($pk);

		$this->tagsHelper->typeAlias = 'com_testimonials.testimonial';

		return $result && $this->tagsHelper->deleteTagData($this, $pk);
	}

	/**
	 * Overload the store method for the Testimonials table.
	 *
	 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function store($updateNulls = false)
	{
		// Initialiase variables.
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing item.
			$this->modified    = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New testimonial. A testimonial created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		// Set publish_up to null date if not set.
		if (!$this->publish_up)
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		// Set publish_down to null date if not set.
		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		// Verify that the alias is unique.
		$table = JTable::getInstance('Testimonial', 'TestimonialsTable');

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_TESTIMONIALS_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		$this->tagsHelper->typeAlias = 'com_testimonials.testimonial';
		$this->tagsHelper->preStoreProcess($this);

		// Attempt to store the user data.
		$result = parent::store($updateNulls);

		return $result && $this->tagsHelper->postStoreProcess($this);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not
	 *                            set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialiase variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Get the JDatabaseQuery object.
		$query = $this->_db->getQuery(true);

		// Update the publishing state for rows with the given primary keys.
		$query->update($this->_db->quoteName($this->_tbl))
			->set($this->_db->quoteName('state') . ' = ' . (int) $state)
			->where('(' . $where . ')' . $checkin);
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}
}
