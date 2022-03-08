<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single bootstrap2 template for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/newsletterhelper.php');

JHtml::_('stylesheet', 'com_bwpostman/bwpostman_bs2.css', array('version' => 'auto', 'relative' => true));
$templateName	= Factory::getApplication()->getTemplate();
$css_filename	= 'templates/' . $templateName . '/css/com_bwpostman.css';
JHtml::_('stylesheet', $css_filename, array('version' => 'auto'));
?>
<script type="text/javascript">
/* <![CDATA[ */
	window.onload = function()
	{
		var framefenster = document.getElementById("myIframe");

		if(framefenster.contentWindow.document.body)
		{
			var legal = framefenster.contentWindow.document.getElementById('legal');
			legal.remove();
			var framefenster_size = framefenster.contentWindow.document.body.offsetHeight;
			if(document.all && !window.opera)
			{
				framefenster_size = framefenster.contentWindow.document.body.scrollHeight;
			}
			framefenster.style.height = framefenster_size + 2 +'px';
		}
	};
/* ]]> */
</script>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo Text::_('WARNING'); ?></h4>
			<div>
				<p><?php echo Text::_('COM_BWPOSTMAN_JAVASCRIPTWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="bwpostman">
	<div id="bwp_com_nl_single">
	<?php // if newsletter unpublished - only backlink
	if ($this->newsletter->published != 0)
	{
		if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != '')) { ?>
			<h1 class="contentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
			<?php
			if ($this->page_title)
			{ ?>
				<h2><?php echo $this->newsletter->subject ?></h2><?php
			} ?>
		<?php
		}
		else
		{ ?>
			<?php
			if ($this->page_title)
			{ ?>
				<h1><?php echo $this->newsletter->subject?></h1><?php
			} ?>
		<?php
		} ?>

		<p class="mailingdate<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<?php echo HtmlHelper::date($this->newsletter->mailing_date, Text::_('DATE_FORMAT_LC3'));  ?>
			<?php
			if (!empty($this->newsletter->attachment) && $this->attachment_enabled != 'hide')
			{
				// Convert attachment string or JSON to array, if present
				$attachments = BwPostmanNewsletterHelper::decodeAttachments($this->newsletter->attachment);

				foreach ($attachments as $attachment)
				{
					?>
					<span class="btn" title="<?php echo Text::_('COM_BWPOSTMAN_ATTACHMENT'); ?>">
						<a class="link-attachment" href="<?php echo Uri::base() . $attachment['single_attachment']; ?>" target="_blank">
							<i class="icon_attachment"></i>
						</a>
					</span>
					<?php
				}
			} ?>
		</p>

	<div class="nl_text">
		<iframe id="myIframe" name="myIframeHtml" src="index.php?option=com_bwpostman&amp;view=newsletter&amp;layout=nl_preview&amp;format=raw&amp;id=<?php echo $this->newsletter->id; ?>" height="800" style="width:100%; border: 1px solid #c2c2c2;"></iframe>
	</div>
	<?php
	} ?>
		<p class="back_link btn mt-3"><a href="<?php echo htmlspecialchars($this->backlink); ?>"><?php echo Text::_('JPREV'); ?></a></p>

		<?php
		if ($this->params->get('show_boldt_link', '1') === '1')
		{ ?>
		<p class="bwpm_copyright text-center my-3"><?php echo BwPostman::footer(); ?></p>
		<?php
		} ?>
	</div>
</div>
