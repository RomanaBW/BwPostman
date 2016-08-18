<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template html for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
 * @license GNU/GPL, see LICENSE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

// Load the tooltip behavior for the notes
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.keepalive');


$image = '<i class="icon-info"></i>';

$options = array(
		'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
		'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
	'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
	'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.submitbutton = function (pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'template.save')
		{
			writeStore("inputs", 0);
			writeStore("jpanetabs_template_tabs", 0);
			writeStore("jpanetabs_buttons" ,0);
		}

		if (pressbutton == 'template.apply')
		{
			writeStore("inputs", 0);
		}

		if (pressbutton == 'template.save2copy')
		{
			writeStore("inputs", 0);
		}

		if (pressbutton == 'template.cancel')
		{
			// check if form field values has changed
			var inputs_old = readStore("inputs");
			inputs = checkValues(1);
			if (inputs_old === inputs)
			{
			}
			else
			{
			// confirm if cancel or not
				confirmCancel =confirm("<?php echo JText::_('COM_BWPOSTMAN_TPL_CONFIRM_CANCEL', true); ?>");
	 			if (confirmCancel == false)
			    {
	 				return;
	 			}
			}
			writeStore("inputs", 0);
			writeStore("jpanetabs_template_tabs", 0);
			writeStore("jpanetabs_buttons", 0);
 			 			submitform(pressbutton);
			return;
		}

		// Validate input fields
		if (form.jform_title.value == "")
		{
			alert("<?php echo JText::_('COM_BWPOSTMAN_TPL_ERROR_TITLE', true); ?>");
		}
		else if (form.jform_description.value== "")
		{
			alert("<?php echo JText::_('COM_BWPOSTMAN_TPL_ERROR_DESCRIPTION', true); ?>");
		}
		else
		{
			submitform(pressbutton);
		}
	};

		// insert placeholder
		function buttonClick(text, editor)
		{
			jInsertEditorText(text, editor);
		}

		// check form field values
		function checkValues(turn)
		{
			var inputs = '';
			var elements = document.adminForm.elements;
			for (var i=0; i<elements.length; i++)
			{
				if (elements[i].getAttribute('id') == 'jform_tpl_css') {var fieldValue = elements[i].value.length;} else if (elements[i].getAttribute('id') == 'jform_tpl_html') {} else {var fieldValue = elements[i].value;}
				if (elements[i].getAttribute('checked') != false) {var fieldChecked = elements[i].getAttribute('checked');}
				inputs += fieldValue + fieldChecked;
			}
			if (turn == 0)
			{
				writeStore("inputs", inputs);
			}
			else
			{
				return inputs;
			}
		}

		// write to storage
		function writeStore(item, value)
		{
			if (Browser.Features.localstorage)
			{
				localStorage[item] = value;
			}
			else
			{
				Cookie.write(item, value);
			}
		}

		// read storage
		function readStore(item)
		{
			if (Browser.Features.localstorage)
			{
				itemValue = localStorage[item];
			}
			else
			{
				itemValue = Cookie.read(item);
			}
			return itemValue;
		}

		window.onload = function() {
		var framefenster = document.getElementById("myIframe");

		if(framefenster.contentWindow.document.body)
		{
			var framefenster_size = framefenster.contentWindow.document.body.offsetHeight;
			if(document.all && !window.opera)
			{
				framefenster_size = framefenster.contentWindow.document.body.scrollHeight;
			}
			framefenster.style.height = framefenster_size + 'px';
		}
		// check if store is empty or 0
		var store = readStore("inputs");
		if (store == 0 || store === undefined || store === null)
		{
			checkValues(0);
		}
	};
/* ]]> */
</script>

<div id="bwp_view_lists">
	<?php
		if ($this->queueEntries)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
		}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=template&layout=default&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_BWPOSTMAN_TPL_TEMPLATE'); ?></legend>
			<div class="well well-small">
				<div class="fltlft width-40 span5 control-group">
					<?php
						echo JHtml::_('tabs.start', 'template_tabs', $options);
						echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'), 'panel1');
					?>
					<fieldset class="panelform">
						<legend><?php echo JText::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'); ?></legend>
						<div class="well well-small">
							<ul class="adminformlist unstyled">
								<li>
									<?php echo $this->form->getLabel('title'); ?>
									<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
								</li>
								<li>
									<?php echo $this->form->getLabel('description'); ?>
									<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
								</li>
								<li>
									<?php echo $this->form->getLabel('thumbnail'); ?>
									<div class="controls"><?php echo $this->form->getInput('thumbnail'); ?></div>
								</li>
							</ul>
						</div>
					</fieldset>

					<fieldset class="panelform">
						<legend><?php echo JText::_('COM_BWPOSTMAN_TPL_ARTICLE_LABEL'); ?></legend>
						<div class="well well-small">
							<ul class="adminformlist unstyled">
								<?php
								foreach ($this->form->getFieldset('jarticle') as $field) :
									$show = array("jform[article][show_author]", "jform[article][show_createdate]", "jform[article][show_readon]");
									if (in_array($field->name, $show)) : ?>
										<li><?php echo $field->label; ?>
											<div class="controls clearfix"><?php echo $field->input; ?></div>
										</li>
										<?php
									endif;
								endforeach; ?>
							</ul>
						</div>
					</fieldset>
					<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
					<?php
						echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_CSS_LABEL') , 'panel2');
					?>
					<fieldset class="panelform">
						<ul class="adminformlist unstyled">
							<li>
								<div><?php echo JText::_('COM_BWPOSTMAN_TPL_CSS_DESC'); ?></div>
								<div class="well well-small">
									<?php echo $this->form->getInput('tpl_css'); ?>
								</div>
							</li>
						</ul>
					</fieldset>
					<?php
						echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_HTML_LABEL') , 'panel3');
					?>
					<fieldset class="panelform">
						<ul class="adminformlist unstyled">
							<li>
								<div><?php echo JText::_('COM_BWPOSTMAN_TPL_HTML_DESC'); ?></div>
									<div class="well well-small">
										<?php echo $this->form->getInput('tpl_html'); ?>
										<?php
										$link = JUri::base() . '#';
										if(JPluginHelper::isEnabled('bwpostman', 'personalize')) {
											$button_text = JText::_('COM_BWPOSTMAN_TPL_HTML_PERS_BUTTON');
											$linktexts = array('PERS' => $button_text, '[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]', '[%content%]', '[%unsubscribe_link%]', '[%edit_link%]', '[%impressum%]');
										}
										else {
										$linktexts = array('[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]', '[%content%]', '[%unsubscribe_link%]', '[%edit_link%]', '[%impressum%]');
										}
										foreach ($linktexts as $key => $linktext) {
											echo "                    <a class=\"btn btn-small pull-left\" onclick=\"buttonClick('". $linktext ."','jform_tpl_html');return false;\" href=\"" . $link . "\">" . $linktext . "</a>";
											echo '                     <p>&nbsp;'.JText::_('COM_BWPOSTMAN_TPL_HTML_DESC'.$key).'</p>';
										}
										if(JPluginHelper::isEnabled('bwpostman', 'personalize')) echo JText::_('COM_BWPOSTMAN_TPL_HTML_DESC_PERSONALIZE');
										?>
									</div>
								<div class="clr clearfix"></div>
							</li>
						</ul>
					</fieldset>
					<?php
						echo JHtml::_('tabs.end');
					?>
					<div class="clr clearfix"></div>
					<div class="well-note well-small"><?php echo JText::_('COM_BWPOSTMAN_TPL_USER_NOTE'); ?></div>
				</div>
				<div id="email_preview" class="fltlft span7">
					<p><button class="btn btn-large btn-block btn-primary" type="submit"><?php echo JText::_('COM_BWPOSTMAN_TPL_REFRESH_PREVIEW'); ?></button>&nbsp;</p>
					<iframe id="myIframe" name="myIframeHtml" src="index.php?option=com_bwpostman&amp;view=template&amp;layout=template_preview&amp;format=raw&amp;id=<?php echo $this->item->id; ?>" height="800" width="100%" style="border: 1px solid #c2c2c2;"></iframe>
				</div>
				<div class="clr clearfix"></div>
			</div>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('tpl_id', null, 0); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo JHtml::_('form.token'); ?>
		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
	</form>

</div>
