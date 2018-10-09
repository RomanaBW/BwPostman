<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates form template for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

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
	// insert placeholder
	jQuery(function($)
	{
		$.fn.EnableInsertAtCaret = function()
		{
			$(this).on("focus", function()
			{
				$(".insertatcaretactive").removeClass("insertatcaretactive");
				$(this).addClass("insertatcaretactive");
			});
		};
		$("#jform_intro_intro_text,#jform_intro_intro_headline").EnableInsertAtCaret();
	});

	function InsertAtCaret(myValue)
	{
		return jQuery(".insertatcaretactive").each(function(i)
		{
			if (document.selection)
			{
				//For browsers like Internet Explorer
				this.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			}
			else if (this.selectionStart || this.selectionStart == '0')
			{
				//For browsers like Firefox and Webkit based
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
				this.focus();
				this.selectionStart = startPos + myValue.length;
				this.selectionEnd = startPos + myValue.length;
				this.scrollTop = scrollTop;
			}
			else
			{
				this.value += myValue;
				this.focus();
			}
		})
	}

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
			confirmCancel = confirm("<?php echo JText::_('COM_BWPOSTMAN_TPL_CONFIRM_CANCEL', true); ?>");
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

	// check form field values
	function checkValues(turn)
	{
		var inputs = '';
		var elements = document.adminForm.elements;
		for (var i=0; i<elements.length; i++)
		{
			var fieldValue = elements[i].value;
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
	function readStore(item) {
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

	window.onload = function()
	{
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
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=template&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<fieldset class="adminform">
			<legend>
				<?php
				$title = JText::_('COM_BWPOSTMAN_NEW_TPL_HTML');
				if ($this->item->id)
				{
					$title = JText::sprintf('COM_BWPOSTMAN_EDIT_TPL_HTML', $this->item->id);
				}

				echo $title
				?>
			</legend>
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
								<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
								<?php echo $this->loadTemplate('basics'); ?>
							</ul>
						</div>
					</fieldset>
					<?php
					echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_HEADER_LABEL'), 'panel2');
					echo $this->loadTemplate('header');
					echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_INTRO_LABEL'), 'panel3');
					echo $this->loadTemplate('intro');
					echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_ARTICLE_LABEL'), 'panel4');
					echo $this->loadTemplate('article');
					echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_FOOTER_LABEL'), 'panel5');
					echo $this->loadTemplate('footer');
					if ($this->permissions['com']['admin'] || $this->permissions['admin']['template'])
					{
						echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_FIELDSET_RULES'), 'panel6');
						?>
						<div class="well well-small">
							<fieldset class="adminform">
								<?php echo $this->form->getInput('rules'); ?>
							</fieldset>
						</div>
						<?php
					}

					echo JHtml::_('tabs.end');
					?>
					<div class="clr clearfix"></div>
					<div class="well-note well-small"><?php echo JText::_('COM_BWPOSTMAN_TPL_USER_NOTE'); ?></div>
				</div>
				<div id="email_preview" class="fltlft span7">
					<p>
						<button class="btn btn-large btn-block btn-primary" type="submit">
							<?php echo JText::_('COM_BWPOSTMAN_TPL_REFRESH_PREVIEW'); ?>
						</button>
					</p>
					<iframe id="myIframe" name="myIframeHtml"
						src="index.php?option=com_bwpostman&amp;view=template&amp;layout=template_preview&amp;format=raw&amp;id=<?php echo $this->item->id; ?>"
						height="800" width="100%" style="border: 1px solid #c2c2c2;">
					</iframe>
				</div>
				<div class="clr clearfix"></div>
			</div>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $this->item->created_by; ?>" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('tpl_id'); ?>
		<?php echo $this->form->getInput('checked_out'); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo JHtml::_('form.token'); ?>
		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
	</form>
</div>
