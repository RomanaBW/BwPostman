<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman helper class for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

/**
 * Class BwPostmanHelper
 *
 * @since 2.0.0
 */
abstract class BwPostmanTplHelper
{

	/**
	 * Configure the head-tag.
	 *
	 * @return    string
	 *
	 * @since    2.0.0
	 */
	public static function getHeadTag()
	{
		$head_tag  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$head_tag .= "\n";
		$head_tag .= '<html>' . "\n";
		$head_tag .= '	<head>' . "\n";
		$head_tag .= '		<title>Newsletter</title>' . "\n";
		$head_tag .= '		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
		$head_tag .= '		<meta name="robots" content="noindex,nofollow" />' . "\n";
		$head_tag .= '		<meta property="og:title" content="HTML Newsletter" />' . "\n";

		return $head_tag;
	}

	/**
	 * Configure the body-tag.
	 *
	 * @return    string  $body_tag define the standard body-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getBodyTag()
	{
		$body_tag = ' <body bgcolor="#ffffff" emb-default-bgcolor="#ffffff">' . "\n";

		return $body_tag;
	}

	/**
	 * Configure the begin of article-tag.
	 *
	 * @return   string $article_tag define the standard article-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getArticleTagBegin()
	{
		$article_tag_begin = ' <div class="article">' . "\n";

		return $article_tag_begin;
	}

	/**
	 * Configure the end of article-tag.
	 *
	 * @return   string $article_tag define the standard article-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getArticleTagEnd()
	{
		$article_tag_end = ' </div>' . "\n";

		return $article_tag_end;
	}

	/**
	 * Configure the readon-tag.
	 *
	 * @return   string $readon_tag define the standard readon-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getReadonTag()
	{
		$readon_tag  = '<div class="read_on">' . "\n";
		$readon_tag .= '	<p>' . "\n";
		$readon_tag .= '		<a href="[%readon_href%]" class="readon">[%readon_text%]</a>' . "\n";
		$readon_tag .= '		<br/><br/>' . "\n";
		$readon_tag .= '	</p>' . "\n";
		$readon_tag .= '</div>' . "\n";

		return $readon_tag;
	}

	/**
	 * Configure the begin of legal-tag.
	 *
	 * @return   string $legal_tag define the standard legal-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getLegalTagBegin()
	{
		$legal_tag_begin  = '   <table id="legal" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed; width: 100%;"><tbody>';
		$legal_tag_begin .= '     <tr>' . "\n";
		$legal_tag_begin .= '       <td id="legal_td">' . "\n";
		$legal_tag_begin .= '         <table class="one-col legal" style="border-collapse: collapse;border-spacing: 0;"><tbody>' . "\n";
		$legal_tag_begin .= '          <tr>' . "\n";
		$legal_tag_begin .= '           <td class="legal_td">' . "\n";

		return $legal_tag_begin;
	}

	/**
	 * Configure the end of legal-tag.
	 *
	 * @return   string $legal_tag define the standard legal-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getLegalTagEnd()
	{
		$legal_tag_end  = '           </td>' . "\n";
		$legal_tag_end .= '          </tr>' . "\n";
		$legal_tag_end .= '         </tbody></table>' . "\n";
		$legal_tag_end .= '       </td>' . "\n";
		$legal_tag_end .= '     </tr>' . "\n";
		$legal_tag_end .= '   </tbody></table>' . "\n";

		return $legal_tag_end;
	}

	/**
	 * Method to get the number of standard templates
	 *
	 * @param $cid
	 *
	 * @return int
	 *
	 * @since 2.4.0
	 */
	public static function getNumberOfStdTemplates($cid)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// count selected standard templates
		$query->select($db->quoteName('standard'));
		$query->from($db->quoteName('#__bwpostman_templates'));
		$query->where($db->quoteName('id') . " IN (" . implode(",", $cid) . ")");
		$query->where($db->quoteName('standard') . " = " . $db->quote(1));

		$db->setQuery($query);
		$db->execute();
		$count_std = $db->getNumRows();

		return $count_std;
	}

	/**
	 * Method to get the number of templates depending on provided mode. archive state and title
	 * If title is provided, then archive state is not used
	 *
	 * @param string  $mode
	 * @param boolean $archived
	 * @param string  $title
	 *
	 * @return 	integer|boolean number of templates or false
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	static public function getNbrOfTemplates($mode, $archived, $title = '')
	{
		$archiveFlag = 0;

		if ($archived)
		{
			$archiveFlag = 1;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_templates'));

		if (strtolower($mode) === 'html')
		{
			$query->where($db->quoteName('tpl_id') . ' < ' . $db->quote('998'));
		}
		elseif (strtolower($mode) === 'text')
		{
			$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));
		}

		if ($title !== '')
		{
			$query->where($db->quoteName('title') . ' LIKE ' . $db->quote('%' . $title . '%'));
		}
		else
		{
			$query->where($db->quoteName('archive_flag') . ' = ' . $archiveFlag);
		}

		$db->setQuery($query);

		try
		{
			return $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return false;
	}

	/**
	 * Method to get the title of a template
	 *
	 * @param integer  $id
	 *
	 * @return 	string|boolean title of template or false
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	static public function getTemplateTitle($id)
	{
		$db    = Factory::getDbo();

		// get template title
		$q = $db->getQuery(true)
			->select($db->quoteName('title'))
			->from($db->quoteName('#__bwpostman_templates'))
			->where($db->quoteName('id') . ' = ' .$id);
		$db->setQuery($q);

		try
		{
			$TplTitle = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return $TplTitle;
	}

	/**
	 * Method to set the title of a template
	 *
	 * @param integer  $id
	 * @param string   $title
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	static public function setTemplateTitle($id, $title)
	{
		$db    = Factory::getDbo();

		// get template title
		$q = $db->getQuery(true)
			->update($db->quoteName('#__bwpostman_templates'))
			->set($db->quoteName('title') . ' = ' . $db->quote($title))
			->where($db->quoteName('id') . ' = ' .$id);
		$db->setQuery($q);

		try
		{
			return $db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
	}
}
