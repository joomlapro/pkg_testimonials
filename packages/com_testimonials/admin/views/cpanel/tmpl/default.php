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

// Get the current user object.
$user = JFactory::getUser();
?>
<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav">
			<ul class="nav nav-list">
				<li class="nav-header"><?php echo JText::_('COM_TESTIMONIALS_HEADER_SUBMENU'); ?></li>
				<li class="active"><a href="<?php echo $this->baseurl; ?>/index.php?option=com_testimonials"><?php echo JText::_('COM_TESTIMONIALS_LINK_DASHBOARD'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_testimonials&amp;view=testimonials"><?php echo JText::_('COM_TESTIMONIALS_LINK_TESTIMONIALS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_testimonials&amp;view=featured"><?php echo JText::_('COM_TESTIMONIALS_LINK_FEATURED'); ?></a></li>
			</ul>
		</div>
	</div>
	<div class="span6">
		<?php
		foreach ($this->modules as $module)
		{
			$output = JModuleHelper::renderModule($module, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($module->params);
			echo $output;
		}
		?>
	</div>
	<div class="span4">
		<?php
		foreach ($this->iconmodules as $iconmodule)
		{
			$output = JModuleHelper::renderModule($iconmodule, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($iconmodule->params);
			echo $output;
		}
		?>
	</div>
</div>
