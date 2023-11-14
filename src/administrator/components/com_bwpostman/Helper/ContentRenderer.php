<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman Content Renderer Class.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use ContentHelperRoute;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;
use RuntimeException;
use stdClass;

/**
* Content Renderer Class
* Provides methods render the selected contents from which the newsletters shall be generated
* --> Referring to BwPostman 1.6 beta and Communicator 2.0.0rc1 (??)
*
* @package		BwPostman-Admin
* @subpackage	Newsletters
*
* @since       2.3.0 (here, moved from newsletter model)
*/
class ContentRenderer
{
	/**
	 * This is the main function to render the content from an ID to HTML
	 *
	 * @param array $nl_content       List of IDs of the selected content
	 * @param int   $template_id      ID of the template used
	 * @param int   $text_template_id ID of the text template used
	 *
	 * @return array    content
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function getContent(array $nl_content, int $template_id, int $text_template_id): array
	{
		PluginHelper::importPlugin('bwpostman');
		$app = Factory::getApplication();

		$content = array();

		$tpl      = $this->getTemplate($template_id);
		$text_tpl = $this->getTemplate($text_template_id);

		// add template assets only for user-made templates
		if ($tpl->tpl_id == '0')
		{
			$tpl_assets = $this->getTemplateAssets($template_id);

			if (!empty($tpl_assets))
			{
				foreach ($tpl_assets as $key => $value)
				{
					$tpl->$key = $value;
				}
			}
		}

		$content['html_version'] = '';
		$content['text_version'] = '';

		$nl_content = ArrayHelper::toInteger($nl_content);

		$app->triggerEvent('onBwpmBeforeRenderNewsletter', array(&$nl_content, &$tpl, &$text_tpl, &$content));

		if ($nl_content == null)
		{
			$content['html_version'] .= '';
			$content['text_version'] .= '';
		}
		else
		{
			foreach ($nl_content as $content_id)
			{
				$app->triggerEvent('onBwpmBeforeRenderNewsletterArticle', array(&$nl_content, &$tpl, &$text_tpl, &$content));

				if ($tpl->tpl_id && $template_id > 0)
				{
					$content['html_version'] .= $this->replaceContentHtmlNew($content_id, $tpl);

					if (($tpl->article['divider'] == 1) && ($content_id != end($nl_content)))
					{
						$content['html_version'] = $content['html_version'] . $tpl->tpl_divider;
					}
				}
				else
				{
					$content['html_version'] .= $this->replaceContentHtml($content_id, $tpl);
				}

				if ($text_tpl->tpl_id && $text_tpl->tpl_id > '999')
				{
					$content['text_version'] .= $this->replaceContentTextNew($content_id, $text_tpl);

					if (($text_tpl->article['divider'] == 1) && ($content_id != end($nl_content)))
					{
						$content['text_version'] = $content['text_version'] . $text_tpl->tpl_divider . "\n\n";
					}
				}
				else
				{
					$content['text_version'] .= $this->replaceContentText($content_id, $text_tpl);
				}

				$app->triggerEvent('onBwpmAfterRenderNewsletterArticle', array(&$nl_content, &$tpl, &$text_tpl, &$content, $content_id, 'bwpostman'));
			}
		}

		$app->triggerEvent('onBwpmAfterRenderNewsletter', array(&$nl_content, &$tpl, &$text_tpl, &$content));

		return $content;
	}

	/**
	 * Method to retrieve content
	 *
	 * @param int $id
	 *
	 * @return object|null
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function  retrieveContent(int $id, int $show_readon)
	{
		$row   = new stdClass();
		$app   = Factory::getApplication();
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('a') . '.*');
		$query->select('ROUND(v.rating_sum/v.rating_count) AS ' . $_db->quoteName('rating'));
		$query->select($_db->quoteName('v') . '.' . $_db->quoteName('rating_count'));
		$query->select($_db->quoteName('u') . '.' . $_db->quoteName('name') . ' AS ' . $_db->quoteName('author'));
		$query->select($_db->quoteName('cc') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('category'));
		$query->select($_db->quoteName('s') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('section'));
		$query->select($_db->quoteName('g') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('groups'));
		$query->select($_db->quoteName('s') . '.' . $_db->quoteName('published') . ' AS ' . $_db->quoteName('sec_pub'));
		$query->select($_db->quoteName('cc') . '.' . $_db->quoteName('published') . ' AS ' . $_db->quoteName('cat_pub'));
		$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('a'));
		$query->join(
			'LEFT',
			$_db->quoteName('#__categories') .
			' AS ' . $_db->quoteName('cc') .
			' ON ' . $_db->quoteName('cc') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('catid')
		);
		$query->join(
			'LEFT',
			$_db->quoteName('#__categories') .
			' AS ' . $_db->quoteName('s') .
			' ON ' . $_db->quoteName('s') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('cc') . '.' . $_db->quoteName('parent_id') .
			' AND ' . $_db->quoteName('s') . '.' . $_db->quoteName('extension') . ' = ' . $_db->quote('com_content')
		);
		$query->join(
			'LEFT',
			$_db->quoteName('#__users') .
			' AS ' . $_db->quoteName('u') .
			' ON ' . $_db->quoteName('u') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by')
		);
		$query->join(
			'LEFT',
			$_db->quoteName('#__content_rating') .
			' AS ' . $_db->quoteName('v') .
			' ON ' . $_db->quoteName('a') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('v') . '.' . $_db->quoteName('content_id')
		);
		$query->join(
			'LEFT',
			$_db->quoteName('#__usergroups') .
			' AS ' . $_db->quoteName('g') .
			' ON ' . $_db->quoteName('a') . '.' . $_db->quoteName('access') . ' = ' . $_db->quoteName('g') . '.' . $_db->quoteName('id')
		);
		$query->where($_db->quoteName('a') . '.' . $_db->quoteName('id') . ' = ' .  $id);

		try
		{
			$_db->setQuery($query);

			$row = $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($row)
		{
			$params = new Registry();
			$params->loadString($row->attribs);

			$row->params = $params;
//			$row->text   = $row->introtext;
      $row->text = ($row->fulltext !== null && trim($row->fulltext) != '' && $show_readon !== 1) ? $row->introtext . ' ' . $row->fulltext : $row->introtext;
		}

		return $row;
	}

	/**
	 * Method to replace HTML content
	 *
	 * @param int    $id    Content ID
	 * @param object $tpl
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function replaceContentHtml(int $id, object $tpl): string
	{
		$app  = Factory::getApplication();
		$lang = $app->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$content = '';

		if ($id !== 0)
		{
			// Editor user type check
			$access          = new stdClass();
			$access->canEdit = $access->canEditOwn = $access->canPublish = 0;

			// $id = -1 if no content is selected
			if ($id === -1)
			{
				$tag_article_begin = BwPostmanTplHelper::getArticleTagBegin();
				$tag_article_end   = BwPostmanTplHelper::getArticleTagEnd();

				// Set special article html if defined at the template
				if (isset($tpl->tpl_tags_article) && $tpl->tpl_tags_article == 0)
				{
					$tag_article_begin = $tpl->tpl_tags_article_advanced_b;
					$tag_article_end = $tpl->tpl_tags_article_advanced_e;
				}

				$content = $tag_article_begin . Text::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_CONTENT') . $tag_article_end;

				return stripslashes($content);
			}

			$row = $this->retrieveContent($id, intval($tpl->article['show_readon']));

			if ($row)
			{
//				$row = $this->processContentPlugins($row);
				$app->triggerEvent('onBwpmRenderNewsletterArticle', array('bwpostman', &$row));


				$params  = $row->params;
				$lang    = self::getArticleLanguage($row->id);
				$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
				$_Itemid = Route::link('site', ContentHelperRoute::getArticleRoute($row->slug, $row->catid, $lang));
				$link    = str_replace(Uri::base(true).'/', '', Uri::base());
				if ($_Itemid)
				{
					$link .= $_Itemid;
				}

				$intro_text = $row->text;

				$html_content = new HtmlContent();

				if (key_exists('show_title', $tpl->article) && $tpl->article['show_title'] != 0)
				{
					ob_start();
					// Displays Item Title
					$html_content->Title($row, $params);

					$content .= ob_get_contents();
					ob_end_clean();
				}

				$content .= '<div class="intro_text">';
				// Displays Category article info

				ob_start();

				if ($tpl->article['show_createdate'] != 0 || $tpl->article['show_author'] != 0)
				{
					$html_content->ArticleInfoBegin();
					// Displays Created Date
					if ($tpl->article['show_createdate'] != 0)
					{
						$html_content->CreateDate($row);
					}

					// Displays Author Name
					if ($tpl->article['show_author'] != 0)
					{
						$html_content->Author($row);
						$html_content->ArticleInfoEnd();
					}
				}

				// Displays Urls
				$content .= ob_get_contents();
				ob_end_clean();

				$content .= $intro_text //(function_exists('ampReplace') ? ampReplace($intro_text) : $intro_text). '</td>'
					. '</div>';

				if ($tpl->article['show_readon'] != 0)
				{
					$tag_readon = isset($tpl->tpl_tags_readon) && $tpl->tpl_tags_readon == 0 ?
						$tpl->tpl_tags_readon_advanced :
						BwPostmanTplHelper::getReadonTag();
					$link = str_replace('administrator/', '', $link);

					// Trigger Plugin "substitutelinks"
					if ($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
					{
						PluginHelper::importPlugin('bwpostman');
						$app->triggerEvent('onBwPostmanSubstituteReadon', array(&$link));
					}

					$tag_readon = str_replace('[%readon_href%]', $link, $tag_readon);
					$content    .= str_replace('[%readon_text%]', Text::_('READ_MORE'), $tag_readon);
				}

				// Set special article html if defined at the template
				$tag_article_begin = isset($tpl->tpl_tags_article) && $tpl->tpl_tags_article == 0 ?
					$tpl->tpl_tags_article_advanced_b :
					BwPostmanTplHelper::getArticleTagBegin();
				$tag_article_end   = isset($tpl->tpl_tags_article) && $tpl->tpl_tags_article == 0 ?
					$tpl->tpl_tags_article_advanced_e :
					BwPostmanTplHelper::getArticleTagEnd();
				$content           = $tag_article_begin . $content . $tag_article_end;

				return stripslashes($content);
			}
		}

		return Text::sprintf('COM_BWPOSTMAN_NL_ERROR_RETRIEVING_CONTENT', $id);
	}

	/**
	 * Method to replace HTML content (new)
	 *
	 * @param int    $id
	 * @param object $tpl
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since       1.1.0
	 */
	public function replaceContentHtmlNew(int $id, object $tpl): string
	{
		$app  = Factory::getApplication();
		$lang = $app->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$content     = '';
		$create_date = '';

		if ($id !== 0)
		{
			// Editor user type check
			$access          = new stdClass();
			$access->canEdit = $access->canEditOwn = $access->canPublish = 0;

			// $id = -1 if no content is selected
			if ($id === -1)
			{
				$content .= $tpl->tpl_article;
				$content = preg_replace("/<table id=\"readon\".*?<\/table>/is", "", $content);
				$content = isset($tpl->article['show_title']) && $tpl->article['show_title'] == 0 ?
					str_replace('[%content_title%]', '', $content) :
					str_replace('[%content_title%]', Text::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_TITLE'), $content);
				$content = str_replace('[%content_text%]', Text::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_CONTENT'), $content);

				return stripslashes($content);
			}

			$row = $this->retrieveContent($id, intval($tpl->article['show_readon']));

			if ($row)
			{
//				$row = $this->processContentPlugins($row);
				$app->triggerEvent('onBwpmRenderNewsletterArticle', array('bwpostman', &$row));

				$lang    = self::getArticleLanguage($row->id);
				$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
				$_Itemid = Route::link('site', ContentHelperRoute::getArticleRoute($row->slug, $row->catid, $lang));
				$link    = str_replace(Uri::base(true).'/', '', Uri::base());

				if ($_Itemid)
				{
					$link .= $_Itemid;
				}

				$intro_text = $row->text;

				if (intval($row->created) != 0)
				{
					$create_date = HtmlHelper::_('date', $row->created);
				}

				$content .= $tpl->tpl_article;
				$content = isset($tpl->article['show_title']) && $tpl->article['show_title'] == 0 ?
					str_replace('[%content_title%]', '', $content) :
					str_replace('[%content_title%]', $row->title, $content);
				$content_text = '';

				if (($tpl->article['show_createdate'] == 1) || ($tpl->article['show_author'] == 1))
				{
					$content_text .= '<p class="article-data">';

					if ($tpl->article['show_createdate'] == 1)
					{
						$content_text .= '<span class="createdate"><small>';
						$content_text .= Text::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
						$content_text .= '&nbsp;&nbsp;&nbsp;&nbsp;</small></span>';
					}

					if ($tpl->article['show_author'] == 1)
					{
						$content_text .= '<span class="created_by"><small>';
						$content_text .= Text::sprintf(
							'COM_CONTENT_WRITTEN_BY',
							($row->created_by_alias ?: $row->author)
						);
						$content_text .= '</small></span>';
					}

					$content_text .= '</p>';
				}

				$content_text .= $intro_text;
				$content      = str_replace('[%content_text%]', $content_text, $content);

				// Trigger Plugin "substitutelinks"
				if ($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
				{
					PluginHelper::importPlugin('bwpostman');
					$app->triggerEvent('onBwPostmanSubstituteReadon', array(&$link));
				}

				$content = str_replace('[%readon_href%]', $link, $content);
				$content = str_replace('[%readon_text%]', Text::_('READ_MORE'), $content);

				return stripslashes($content);
			}
		}

		return Text::sprintf('COM_BWPOSTMAN_NL_ERROR_RETRIEVING_CONTENT', $id);
	}

	/**
	 * Method to replace text content
	 *
	 * @param int    $id
	 * @param object $text_tpl
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since       1.1.0
	 */
	public function replaceContentTextNew(int $id, object $text_tpl): string
	{
		$create_date = '';

		if ($id !== 0)
		{
			$row = $this->retrieveContent($id, intval($text_tpl->article['show_readon']));

			if ($row)
			{
//				$row = $this->processContentPlugins($row);
				Factory::getApplication()->triggerEvent('onBwpmRenderNewsletterArticle', array('bwpostman', &$row));

				list($link, $intro_text) = $this->getIntroText($row);

				if (intval($row->created) != 0)
				{
					$create_date = HtmlHelper::_('date', $row->created);
				}

				$content      = $text_tpl->tpl_article;
				$content      = isset($text_tpl->article['show_title']) && $text_tpl->article['show_title'] == 0 ?
					str_replace('[%content_title%]', '', $content) :
					str_replace('[%content_title%]', $row->title, $content);
				$content_text = "\n";

				if (($text_tpl->article['show_createdate'] == 1) || ($text_tpl->article['show_author'] == 1))
				{
					$content_text = $this->getAuthorAndDate($text_tpl, $create_date, $content_text, $row);
				}

				$content_text .= $intro_text;
				$content      = str_replace('[%content_text%]', $content_text . "\n", $content);

				// Trigger Plugin "substitutelinks"
				if (Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
				{
					PluginHelper::importPlugin('bwpostman');
					Factory::getApplication()->triggerEvent('onBwPostmanSubstituteReadon', array(&$link));
				}

				$content = str_replace('[%readon_href%]', $link . "\n", $content);
				$content = str_replace('[%readon_text%]', Text::_('READ_MORE'), $content);

				return stripslashes($content);
			}
		}

		return '';
	}

	/**
	 * Method to replace text content
	 *
	 * @param int    $id
	 * @param object $text_tpl
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function replaceContentText(int $id, object $text_tpl): string
	{
		$app  = Factory::getApplication();
		$lang = $app->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$create_date = '';

		if ($id !== 0)
		{
			$row = $this->retrieveContent($id, intval($text_tpl->article['show_readon']));

			if ($row)
			{
//				$row = $this->processContentPlugins($row);
				$app->triggerEvent('onBwpmRenderNewsletterArticle', array('bwpostman', &$row));

				list($link, $intro_text) = $this->getIntroText($row);

				if (intval($row->created) != 0)
				{
					$create_date = HtmlHelper::_('date', $row->created);
				}

				$content = isset($text_tpl->article['show_title']) && $text_tpl->article['show_title'] == 0 ? "\n" : "\n" . $row->title;

				$content_text = "";

				if (($text_tpl->article['show_createdate'] == 1) || ($text_tpl->article['show_author'] == 1))
				{
					$content_text = $this->getAuthorAndDate($text_tpl, $create_date, $content_text, $row);
				}

				$intro_text = $content_text . $intro_text;
				$content    .= "\n\n" . $intro_text . "\n\n";

				if ($text_tpl->article['show_readon'] == 1)
				{
					// Trigger Plugin "substitutelinks"
					if ($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
					{
						PluginHelper::importPlugin('bwpostman');
						$app->triggerEvent('onBwPostmanSubstituteReadon', array(&$link));
					}

					$content .= Text::_('READ_MORE') . ": \n" . str_replace('administrator/', '', $link) . "\n\n";
				}

				return stripslashes($content);
			}
		}

		return '';
	}

	/**
	 * Method to get the language of an article
	 *
	 * @param int $id article ID
	 *
	 * @return 	int|mixed|null	language string or 0
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 (here, since 1.0.7 at newsletter model)
	 */
	private function getArticleLanguage(int $id)
	{
		if (Multilanguage::isEnabled())
		{
			$result = '';
			$_db    = Factory::getContainer()->get(DatabaseInterface::class);
			$query  = $_db->getQuery(true);

			$query->select($_db->quoteName('language'));
			$query->from($_db->quoteName('#__content'));
			$query->where($_db->quoteName('id') . ' = ' . $id);

			try
			{
				$_db->setQuery($query);

				$result = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			return $result;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Method to get the template settings which are used to compose a newsletter
	 *
	 * @param int $template_id template id
	 *
	 * @return	object
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 (here, since 1.1.0 at newsletter model)
	 */
	public function getTemplate(int $template_id): object
	{
		$MvcFactory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();
		$tplTable   = $MvcFactory->createTable('Template', 'Administrator');

		$tpl = $tplTable->getTemplate($template_id);

		if (is_string($tpl->basics))
		{
			$registry = new Registry;
			$registry->loadString($tpl->basics);
			$tpl->basics = $registry->toArray();
		}

		if (is_string($tpl->article))
		{
			$registry = new Registry;
			$registry->loadString($tpl->article);
			$tpl->article = $registry->toArray();
		}

		if (is_string($tpl->intro))
		{
			$registry = new Registry;
			$registry->loadString($tpl->intro);
			$tpl->intro = $registry->toArray();
		}

		// only for old templates
		if (empty($tpl->article))
		{
			$tpl->article['show_createdate'] = 0;
			$tpl->article['show_author'] = 0;
			$tpl->article['show_readon'] = 1;
		}

		return $tpl;
	}

	/**
	 * Method to get the template assets which are used to compose a newsletter
	 *
	 * @param int $template_id template id
	 *
	 * @return	array
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 (here, since 2.0.0 at newsletter model)
	 */
	public function getTemplateAssets(int $template_id): array
	{
		$MvcFactory   = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();
		$tplTagsTable = $MvcFactory->createTable('TemplatesTags', 'Administrator');

		return $tplTagsTable->getTemplateAssets($template_id);
	}

	/**
	 * Method to replace edit and unsubscribe link
	 *
	 * @param string $text
	 *
	 * @return    boolean
	 *
	 * @throws Exception
	 *
	 * @since    2.3.0 (here, moved from newsletter model)
	 */
	public function replaceTplLinks(string &$text): bool
	{
		$lang = Factory::getApplication()->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$params          = ComponentHelper::getParams('com_bwpostman');
		$del_sub_1_click = $params->get('del_sub_1_click', '0');

		// replace edit and unsubscribe link
		if ($del_sub_1_click === '0')
		{
			$replace1 = '<a href="[EDIT_HREF]">' . Text::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . '</a>';
		}
		else
		{
			$replace1 = '<a href="[UNSUBSCRIBE_HREF]">' . Text::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . '</a>';
		}
		$text     = str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2 = '<a href="[EDIT_HREF]">' . Text::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . '</a>';
		$text     = str_replace('[%edit_link%]', $replace2, $text);

		return true;
	}

	/**
	 * Method to replace all footer links
	 *
	 * @param string  $body
	 * @param integer $subscriberId
	 * @param integer $mode
	 *
	 * @throws Exception
	 *
	 * @since	3.0.0 (here, moved from newsletter model)
	 */
	public function replaceAllFooterLinks(string &$body, int $subscriberId, int $mode)
	{
		$footerid = 0;

		if ($subscriberId)
		{ // Replace footer links only if it is a real subscriber
			if ($mode === 1)
			{ // HTML newsletter
				$this->replaceTplLinks($body);
				$this->addHTMLFooter($body, $footerid);
			}
			else
			{ // text newsletter
				$this->replaceTextTplLinks($body);
				$this->addTextFooter($body, $footerid);
			}
		}
		else
		{ // If testrecipients remove footer links
			$this->addTestrecipientsFooter($body);
		}
	}

	/**
	 * Method to add the HTML-Tags and the css to the HTML-Newsletter
	 *
	 * @param string $text HTML newsletter
	 * @param int    $id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0 (here, moved from newsletter model)
	 */
	public function addHtmlTags(string &$text, int $id): bool
	{
		$params = ComponentHelper::getParams('com_bwpostman');
		$tpl    = $this->getTemplate($id);

		// add template assets only for user-made templates
		if ($tpl->tpl_id == '0')
		{
			$tpl_assets	= $this->getTemplateAssets($id);

			if (!empty($tpl_assets))
			{
				foreach ($tpl_assets as $key => $value)
				{
					$tpl->$key	= $value;
				}
			}
		}

		$newtext  = isset($tpl->tpl_tags_head) && $tpl->tpl_tags_head == 0 ? $tpl->tpl_tags_head_advanced : BwPostmanTplHelper::getHeadTag();
		$newtext .= '   <style type="text/css">' . "\n";
		$newtext .= '   ' . $tpl->tpl_css . "\n";
		// only for old newsletters with template_id < 1
		if ($id < 1 && $params->get('use_css_for_html_newsletter') == 1)
		{
			$params	= ComponentHelper::getParams('com_bwpostman');
			$css	= $params->get('css_for_html_newsletter');
			$newtext .= '   ' . $css . "\n";
		}

		PluginHelper::importPlugin('bwpostman');
		Factory::getApplication()->triggerEvent('onBwPostmanBeforeCustomCss', array(&$newtext));

		if (isset($tpl->basics['custom_css']))
		{
			$newtext .= $tpl->basics['custom_css'] . "\n";
		}

		$newtext .= '   </style>' . "\n";
		$newtext .= ' </head>' . "\n";

		if (isset($tpl->basics['paper_bg']))
		{
			$newtext .= ' <body bgcolor="' . $tpl->basics['paper_bg'] .
						'" emb-default-bgcolor="' . $tpl->basics['paper_bg'] . '" style="background-color:' . $tpl->basics['paper_bg'] .
						';color:' . $tpl->basics['legal_color'] . ';">' . "\n";
		}
		else
		{
			if (isset($tpl->tpl_tags_body) && $tpl->tpl_tags_body == 0 && trim($tpl->tpl_tags_body_advanced !== ''))
			{
				$newtext .= $tpl->tpl_tags_body_advanced;
			}
			else
			{
				$newtext .= BwPostmanTplHelper::getBodyTag();
			}
		}

		$newtext .= $text . "\n";
		$newtext .= ' </body>' . "\n";
		$newtext .= '</html>' . "\n";

		$text = $newtext;

		return true;
	}

	/**
	 * Method to add the HTML-footer to the HTML-Newsletter
	 *
	 * @param string  $text       HTML newsletter
	 * @param integer $templateId template id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0 (here, moved from newsletter model)
	 */
	public function addHTMLFooter(string &$text, int $templateId): bool
	{
		$app  = Factory::getApplication();
		$lang = $app->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$uri             = Uri::getInstance();
		$params          = ComponentHelper::getParams('com_bwpostman');
		$del_sub_1_click = $params->get('del_sub_1_click', '0');
		$impressum       = Text::_($params->get('legal_information_text', ''));
		$impressum       = nl2br($impressum);
		$sitelink        = $uri->root();

		PluginHelper::importPlugin('bwpostman');
		$app->triggerEvent('onBwPostmanBeforeObligatoryFooterHtml', array(&$text));

		// get template assets if exists
		$tpl_assets	= $this->getTemplateAssets($templateId);

		if (strpos($text, '[%impressum%]') !== false)
		{
			$unsubscribelink = '';
			$editlink        = '';

			// Trigger Plugin "substitutelinks"
			if($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
			{
				PluginHelper::importPlugin('bwpostman');
				$app->triggerEvent('onBwPostmanSubstituteLinks', array(&$unsubscribelink, &$editlink, &$sitelink));
			}

			if ($del_sub_1_click === '0')
			{
				$replace = "<br /><br />" . Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', $sitelink) . "<br /><br />" . $impressum;
			}
			else
			{
				$replace = "<br /><br />" . Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML_ONE_CLICK', $sitelink) . "<br /><br />" . $impressum;
			}

			$replace3  = isset($tpl_assets['tpl_tags_legal']) && $tpl_assets['tpl_tags_legal'] == 0 ?
				$tpl_assets['tpl_tags_legal_advanced_b'] :
				BwPostmanTplHelper::getLegalTagBegin();
			$replace3 .= $replace . "<br /><br />\n";
			$replace3 .= isset($tpl_assets['tpl_tags_legal']) && $tpl_assets['tpl_tags_legal'] == 0 ?
				$tpl_assets['tpl_tags_legal_advanced_e'] :
				BwPostmanTplHelper::getLegalTagEnd();

			$text = str_replace('[%impressum%]', $replace3, $text);
		}

		// only for old newsletters with template_id < 1
		if ($templateId < 1)
		{
			if ($del_sub_1_click === '0')
			{
				$replace = Text::_('COM_BWPOSTMAN_NL_FOOTER_HTML_LINE') . Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', $sitelink) . $impressum;
			}
			else
			{
				$replace = Text::_('COM_BWPOSTMAN_NL_FOOTER_HTML_LINE') . Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML_ONE_CLICK', $sitelink) . $impressum;
			}
			$text = str_replace("[dummy]", "<div class=\"footer-outer\"><p class=\"footer-inner\">$replace</p></div>", $text);
		}

		$app->triggerEvent('onBwPostmanAfterObligatoryFooter', array(&$text, $templateId));

		return true;
	}

	/**
	 * Method to replace edit and unsubscribe link
	 *
	 * @param string $text
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 (here, since 1.1.0 at newsletter model)
	 */
	public function replaceTextTplLinks(string &$text): bool
	{
		$app  = Factory::getApplication();
		$lang = $app->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$uri                = Uri::getInstance();
		$itemid_edit        = BwPostmanSubscriberHelper::getMenuItemid('edit');
		$itemid_unsubscribe = BwPostmanSubscriberHelper::getMenuItemid('register');
		$params             = ComponentHelper::getParams('com_bwpostman');
		$del_sub_1_click    = $params->get('del_sub_1_click', '0');

		if ($del_sub_1_click === '0')
		{
			$unsubscribelink = $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid=' . $itemid_edit .
				'&amp;view=edit&amp;task=unsub&amp;editlink=[EDITLINK]';
		}
		else
		{
			$unsubscribelink = $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid=' . $itemid_unsubscribe .
				'&amp;view=edit&amp;task=unsubscribe&amp;email=[UNSUBSCRIBE_EMAIL]&amp;code=[UNSUBSCRIBE_CODE]';
		}

		$editlink = $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid=' . $itemid_edit . '&amp;view=edit&amp;editlink=[EDITLINK]';
		$sitelink = '';

		// Trigger Plugin "substitutelinks"
		if($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
		{
			PluginHelper::importPlugin('bwpostman');
			$app->triggerEvent('onBwPostmanSubstituteLinks', array(&$unsubscribelink, &$editlink, &$sitelink));
		}

		// replace edit and unsubscribe link
		$replace1 = '+ ' . Text::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . " +\n  " . $unsubscribelink;
		$text     = str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2 = '+ ' . Text::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . " +\n  " . $editlink;
		$text     = str_replace('[%edit_link%]', $replace2, $text);

		return true;
	}

	/**
	 * Method to add the footer Text-Newsletter
	 *
	 * @param string $text Text newsletter
	 * @param int    $id   template id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0 (here, moved from newsletter model)
	 */
	public function addTextFooter(string &$text, int $id): bool
	{
		$app  = Factory::getApplication();
		$lang = $app->getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$uri                = Uri::getInstance();
		$itemid_unsubscribe = BwPostmanSubscriberHelper::getMenuItemid('register');
		$itemid_edit        = BwPostmanSubscriberHelper::getMenuItemid('edit');
		$params             = ComponentHelper::getParams('com_bwpostman');
		$del_sub_1_click    = $params->get('del_sub_1_click', '0');
		$impressum          = "\n\n" . Text::_($params->get('legal_information_text', '')) . "\n\n";

		$unsubscribelink = $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid=' . $itemid_unsubscribe .
			'&amp;view=edit&amp;task=unsubscribe&amp;email=[UNSUBSCRIBE_EMAIL]&amp;code=[UNSUBSCRIBE_CODE]';
		$editlink = $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid=' . $itemid_edit . '&amp;view=edit&amp;editlink=[EDITLINK]';
		$sitelink = $uri->root();

		PluginHelper::importPlugin('bwpostman');
		$app->triggerEvent('onBwPostmanBeforeObligatoryFooterText', array(&$text));

		// Trigger Plugin "substitutelinks"
		if($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') == '1')
		{
			PluginHelper::importPlugin('bwpostman');
			$app->triggerEvent('onBwPostmanSubstituteLinks', array(&$unsubscribelink, &$editlink, &$sitelink));
		}

		if (strpos($text, '[%impressum%]') !== false)
		{
			// replace [%impressum%]
			if ($del_sub_1_click === '0')
			{
				$replace = "\n\n" . Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $sitelink, $editlink) . $impressum;
			}
			else
			{
				$replace = "\n\n" . Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT_ONE_CLICK', $sitelink, $unsubscribelink, $editlink) . $impressum;
			}
			$text = str_replace('[%impressum%]', $replace, $text);
		}

		// only for old newsletters with template_id < 1
		if ($id < 1)
		{
			if ($del_sub_1_click === '0')
			{
				$replace = Text::_('COM_BWPOSTMAN_NL_FOOTER_TEXT_LINE') .
					Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $sitelink, $editlink) . $impressum;
			}
			else
			{
				$replace = Text::_('COM_BWPOSTMAN_NL_FOOTER_TEXT_LINE') .
					Text::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT_ONE_CLICK', $sitelink, $unsubscribelink, $editlink) . $impressum;
			}
			$text = str_replace("[dummy]", $replace, $text);
		}

		return true;
	}

	/**
	 * Method to add the HTML-footer to the HTML-Newsletter
	 *
	 * @param string $body the newsletter content
	 *
	 * @since 3.0.0 (here, moved from newsletter model)
	 */
	public function addTestrecipientsFooter(string &$body)
	{
		$body = str_replace("[%edit_link%]", "", $body);
		$body = str_replace("[%unsubscribe_link%]", "", $body);
		$body = str_replace("[%impressum%]", "", $body);
		$body = str_replace("[dummy]", "", $body);
	}

	/**
	 * Method to add the HTML-footer to the HTML-Newsletter
	 *
	 * @param string      $body the newsletter content
	 * @param object      $tblSendMailQueue
	 * @param string|null $itemid_edit
	 * @param string|null $itemid_unsubscribe
	 * @param string      $editlink
	 * @param integer     $substituteLinks
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 (here, moved from newsletter model)
	 */
	public function replaceContentPlaceholders(string &$body, object $tblSendMailQueue, ?string $itemid_edit, ?string $itemid_unsubscribe, string $editlink, int $substituteLinks)
	{
		$app = Factory::getApplication();
		$uri = Uri::getInstance();

		$fullname = '';
		if ($tblSendMailQueue->firstname != '')
		{
			$fullname = $tblSendMailQueue->firstname . ' ';
		}

		if ($tblSendMailQueue->name != '')
		{
			$fullname .= $tblSendMailQueue->name;
		}

		$fullname = trim($fullname);

		// Replace the dummies
		$body = str_replace("[NAME]", $tblSendMailQueue->name, $body);
		$body = str_replace("[LASTNAME]", $tblSendMailQueue->name, $body);
		$body = str_replace("[FIRSTNAME]", $tblSendMailQueue->firstname, $body);
		$body = str_replace("[FULLNAME]", $fullname, $body);

		// do not replace empty edit link (i.e. for testrecipients)
		if ($editlink !== '')
		{
			// Trigger Plugin "substitutelinks"
			if ((integer)$app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '0') === 1 || $substituteLinks === 1)
			{
				$app->triggerEvent('onBwPostmanSubstituteBody', array(&$body, &$itemid_edit, &$itemid_unsubscribe));
			}
			else
			{
				$body = str_replace(
					"[UNSUBSCRIBE_HREF]",
					Text::sprintf('COM_BWPOSTMAN_NL_UNSUBSCRIBE_HREF', $uri->root(), $itemid_unsubscribe),
					$body
				);
				$body = str_replace(
					"[EDIT_HREF]",
					Text::sprintf('COM_BWPOSTMAN_NL_EDIT_HREF', $uri->root(), $itemid_edit),
					$body
				);
			}

			$body = str_replace("[UNSUBSCRIBE_EMAIL]", $tblSendMailQueue->recipient, $body);
			$body = str_replace("[UNSUBSCRIBE_CODE]", $editlink, $body);
			$body = str_replace("[EDITLINK]", $editlink, $body);
		}
	}

	/**
	 * Provides a URL for one-click unsubscription
	 *
	 * @param string|null $itemid_unsubscribe
	 * @param string  $recipient
	 * @param string  $editlink
	 *
	 * @return string
	 *
	 * @since 3.0.3
	 */
	public function generateUnsubscribeUrl(?string $itemid_unsubscribe, string $recipient, string $editlink): string
	{
		if ($editlink === '')
		{
			return '';
		}

		$link = Text::sprintf('COM_BWPOSTMAN_NL_UNSUBSCRIBE_HREF', Uri::getInstance()->root(), $itemid_unsubscribe);
		$link = str_replace("[UNSUBSCRIBE_EMAIL]", $recipient, $link);

		return str_replace("[UNSUBSCRIBE_CODE]", $editlink, $link);
	}

	/**
	 * Method to add the Template-Tags to the content
	 * Template tags are:
	 * - HTML doctype/header
	 * - HTML body
	 * - newsletter article div (concerns every single article)
	 * - newsletter read more div (concerns every single read mor button)
	 * - newsletter legal info (implemented as table by default)
	 *
	 * @param string $text
	 * @param int    $id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 (here, since 1.1.0 at newsletter model)
	 */
	public function addTplTags(string &$text, int $id): bool
	{
		$tpl = $this->getTemplate($id);

		$newtext = $tpl->tpl_html . "\n";

		// make sure that conditions be usable - some editors add space to conditions
		$text = str_replace('[%content%]', str_replace('<!-- [if', '<!--[if', $text), $newtext);

		return true;
	}

	/**
	 * Method to add the TEXT to the TEXT-Newsletter
	 *
	 * @param string $text Text newsletter
	 * @param int    $id   template id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 (here, since 1.1.0 at newsletter model)
	 */
	public function addTextTpl(string &$text, int $id): bool
	{
		$tpl = $this->getTemplate($id);

		$text = str_replace('[%content%]', "\n" . $text, $tpl->tpl_html);

		return true;
	}

	/**
	 * Method to process special characters
	 *
	 * @param string $text
	 *
	 * @return string
	 *
	 * @since       0.9.1
	 */
	private function unHTMLSpecialCharsAll(string $text): string
	{
		return $this->deHTMLEntities($text);
	}

	/**
	 * convert html special entities to literal characters
	 *
	 * @param string $text
	 *
	 * @return  string  $text
	 *
	 * @since       0.9.1
	 */
	private function deHTMLEntities(string $text): string
	{
		$search  = array(
			"'&(quot|#34);'i",
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(curren|#164);'i",
			"'&(yen|#165);'i",
			"'&(brvbar|#166);'i",
			"'&(sect|#167);'i",
			"'&(uml|#168);'i",
			"'&(copy|#169);'i",
			"'&(ordf|#170);'i",
			"'&(laquo|#171);'i",
			"'&(not|#172);'i",
			"'&(shy|#173);'i",
			"'&(reg|#174);'i",
			"'&(macr|#175);'i",
			"'&(neg|#176);'i",
			"'&(plusmn|#177);'i",
			"'&(sup2|#178);'i",
			"'&(sup3|#179);'i",
			"'&(acute|#180);'i",
			"'&(micro|#181);'i",
			"'&(para|#182);'i",
			"'&(middot|#183);'i",
			"'&(cedil|#184);'i",
			"'&(supl|#185);'i",
			"'&(ordm|#186);'i",
			"'&(raquo|#187);'i",
			"'&(frac14|#188);'i",
			"'&(frac12|#189);'i",
			"'&(frac34|#190);'i",
			"'&(iquest|#191);'i",
			"'&(Agrave|#192);'",
			"'&(Aacute|#193);'",
			"'&(Acirc|#194);'",
			"'&(Atilde|#195);'",
			"'&(Auml|#196);'",
			"'&(Aring|#197);'",
			"'&(AElig|#198);'",
			"'&(Ccedil|#199);'",
			"'&(Egrave|#200);'",
			"'&(Eacute|#201);'",
			"'&(Ecirc|#202);'",
			"'&(Euml|#203);'",
			"'&(Igrave|#204);'",
			"'&(Iacute|#205);'",
			"'&(Icirc|#206);'",
			"'&(Iuml|#207);'",
			"'&(ETH|#208);'",
			"'&(Ntilde|#209);'",
			"'&(Ograve|#210);'",
			"'&(Oacute|#211);'",
			"'&(Ocirc|#212);'",
			"'&(Otilde|#213);'",
			"'&(Ouml|#214);'",
			"'&(times|#215);'i",
			"'&(Oslash|#216);'",
			"'&(Ugrave|#217);'",
			"'&(Uacute|#218);'",
			"'&(Ucirc|#219);'",
			"'&(Uuml|#220);'",
			"'&(Yacute|#221);'",
			"'&(THORN|#222);'",
			"'&(szlig|#223);'",
			"'&(agrave|#224);'",
			"'&(aacute|#225);'",
			"'&(acirc|#226);'",
			"'&(atilde|#227);'",
			"'&(auml|#228);'",
			"'&(aring|#229);'",
			"'&(aelig|#230);'",
			"'&(ccedil|#231);'",
			"'&(egrave|#232);'",
			"'&(eacute|#233);'",
			"'&(ecirc|#234);'",
			"'&(euml|#235);'",
			"'&(igrave|#236);'",
			"'&(iacute|#237);'",
			"'&(icirc|#238);'",
			"'&(iuml|#239);'",
			"'&(eth|#240);'",
			"'&(ntilde|#241);'",
			"'&(ograve|#242);'",
			"'&(oacute|#243);'",
			"'&(ocirc|#244);'",
			"'&(otilde|#245);'",
			"'&(ouml|#246);'",
			"'&(divide|#247);'i",
			"'&(oslash|#248);'",
			"'&(ugrave|#249);'",
			"'&(uacute|#250);'",
			"'&(ucirc|#251);'",
			"'&(uuml|#252);'",
			"'&(yacute|#253);'",
			"'&(thorn|#254);'",
			"'&(yuml|#255);'"
		);
		$replace = array(
			"\"",
			"&",
			"<",
			">",
			" ",
			chr(161),
			chr(162),
			chr(163),
			chr(164),
			chr(165),
			chr(166),
			chr(167),
			chr(168),
			chr(169),
			chr(170),
			chr(171),
			chr(172),
			chr(173),
			chr(174),
			chr(175),
			chr(176),
			chr(177),
			chr(178),
			chr(179),
			chr(180),
			chr(181),
			chr(182),
			chr(183),
			chr(184),
			chr(185),
			chr(186),
			chr(187),
			chr(188),
			chr(189),
			chr(190),
			chr(191),
			chr(192),
			chr(193),
			chr(194),
			chr(195),
			chr(196),
			chr(197),
			chr(198),
			chr(199),
			chr(200),
			chr(201),
			chr(202),
			chr(203),
			chr(204),
			chr(205),
			chr(206),
			chr(207),
			chr(208),
			chr(209),
			chr(210),
			chr(211),
			chr(212),
			chr(213),
			chr(214),
			chr(215),
			chr(216),
			chr(217),
			chr(218),
			chr(219),
			chr(220),
			chr(221),
			chr(222),
			chr(223),
			chr(224),
			chr(225),
			chr(226),
			chr(227),
			chr(228),
			chr(229),
			chr(230),
			chr(231),
			chr(232),
			chr(233),
			chr(234),
			chr(235),
			chr(236),
			chr(237),
			chr(238),
			chr(239),
			chr(240),
			chr(241),
			chr(242),
			chr(243),
			chr(244),
			chr(245),
			chr(246),
			chr(247),
			chr(248),
			chr(249),
			chr(250),
			chr(251),
			chr(252),
			chr(253),
			chr(254),
			chr(255)
		);

		return preg_replace($search, $replace, $text);
	}

	/**
 * @param stdClass $row
 *
 * @return array
 *
 * @throws Exception
 *
 * @since 3.0.0
 */
	private function getIntroText(stdClass $row): array
	{
		$lang    = self::getArticleLanguage($row->id);
		$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
		$_Itemid = Route::link('site', ContentHelperRoute::getArticleRoute($row->slug, $row->catid, $lang));
		$link    = str_replace(Uri::base(true).'/', '', Uri::base());

		if ($_Itemid)
		{
			$link .= $_Itemid;
		}

		$intro_text = $row->text;
		$intro_text = strip_tags($intro_text);

		$intro_text = $this->unHTMLSpecialCharsAll($intro_text);

		return array($link, $intro_text);
	}

	/**
	 * @param object   $text_tpl
	 * @param string   $create_date
	 * @param string   $content_text
	 * @param stdClass $row
	 *
	 * @return string
	 *
	 * @since 3.0.0
	 */
	private function getAuthorAndDate(object $text_tpl, string $create_date, string $content_text, stdClass $row): string
	{
		if ($text_tpl->article['show_createdate'] == 1)
		{
			$content_text .= Text::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
			$content_text .= '    ';
		}

		if ($text_tpl->article['show_author'] == 1)
		{
			$content_text .= Text::sprintf(
				'COM_CONTENT_WRITTEN_BY',
				($row->created_by_alias ?: $row->author)
			);
		}

		$content_text .= "\n\n";

		return $content_text;
	}

	/**
	 * Process content plugins on newsletter content
	 *
	 * @param object $row
	 *
	 * @return mixed|object
	 *
	 * @throws Exception
	 *
	 * @since 4.2.0
	 */
//	protected function processContentPlugins(object $row)
//	{
//		// Some plugins don't make sense in context of newsletters, so exclude them from processing
//		$excludedPlugins = array(
//			'confirmconsent',
//			'emailcloak',
//			'finder',
//			'joomla',
//			'pagebreak',
//			'pagenavigation',
//			'vote',
//			'jce',
//		);
//
//		// Get list of all content plugins
//		$availablePlugins = PluginHelper::getPlugin('content');
//
//		// Only process not excluded plugins, one by one to be able to process special handling, if plugin needs it
//		foreach ($availablePlugins as $availablePlugin)
//		{
//			if (!in_array($availablePlugin->name, $excludedPlugins))
//			{
//				// Prepare row data for loadmodule
//				if ($availablePlugin->name == 'loadmodule')
//				{
//					$row->text = $row->introtext;
//				}
//
//				$currentPlugin = Factory::getApplication()->bootPlugin($availablePlugin->name, 'content');
//
//				if (method_exists($currentPlugin, 'onContentPrepare'))
//				{
//					$currentPlugin->onContentPrepare('com_content.article', $row, $row->attribs, 0);
//				}
//			}
//		}
//
//		return $row;
//	}
}
