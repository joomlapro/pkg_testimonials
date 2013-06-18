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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the behavior script.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Get the application.
$app = JFactory::getApplication();
$input = $app->input;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'testimonial.cancel' || document.formvalidator.isValid(document.id('testimonial-form'))) {
			<?php echo $this->form->getField('testimonial')->save(); ?>
			Joomla.submitform(task, document.getElementById('testimonial-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_testimonials&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="testimonial-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Testimonials -->
		<div class="span10 form-horizontal">
			<fieldset>
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_TESTIMONIALS_NEW_TESTIMONIAL', true) : JText::sprintf('COM_TESTIMONIALS_EDIT_TESTIMONIAL', $this->item->id, true)); ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('name'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('name'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('ordering'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('ordering'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('testimonial'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('testimonial'); ?>
							</div>
						</div>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
						<?php if ($this->item->id): ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('id'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('id'); ?>
								</div>
							</div>
						<?php endif; ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_by'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_by'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_by_alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_by_alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_up'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_down'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
						</div>
						<?php if ($this->item->version): ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('version'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('version'); ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($this->item->modified_by): ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified_by'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('modified_by'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('modified'); ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($this->item->hits): ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('hits'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('hits'); ?>
								</div>
							</div>
						<?php endif; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<?php $fieldSets = $this->form->getFieldsets('params'); ?>
					<?php foreach ($fieldSets as $name => $fieldSet): ?>
						<?php $paramstabs = 'params-' . $name; ?>
						<?php echo JHtml::_('bootstrap.addTab', 'myTab', $paramstabs, JText::_($fieldSet->label, true)); ?>
							<?php echo $this->loadTemplate('params'); ?>
						<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endforeach; ?>

					<?php $fieldSets = $this->form->getFieldsets('metadata'); ?>
					<?php foreach ($fieldSets as $name => $fieldSet): ?>
						<?php $metadatatabs = 'metadata-' . $name; ?>
						<?php echo JHtml::_('bootstrap.addTab', 'myTab', $metadatatabs, JText::_($fieldSet->label, true)); ?>
						<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
						<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endforeach; ?>

					<?php if ($this->canDo->get('core.admin')): ?>
						<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_TESTIMONIALS_FIELDSET_RULES', true)); ?>
							<fieldset>
								<?php echo $this->form->getInput('rules'); ?>
							</fieldset>
						<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endif; ?>

					<div>
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
				<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			</fieldset>
		</div>
		<!-- End Testimonials -->
		<!-- Begin Sidebar -->
		<?php echo JLayoutHelper::render('joomla.edit.details', $this); ?>
		<!-- End Sidebar -->
	</div>
</form>
