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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
	public static function getHeadTag(): string
	{
		$head_tag  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
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
	public static function getBodyTag(): string
	{
		return ' <body bgcolor="#ffffff" emb-default-bgcolor="#ffffff">' . "\n";
	}

	/**
	 * Configure the beginning of article-tag.
	 *
	 * @return   string $article_tag define the standard article-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getArticleTagBegin(): string
	{
		return ' <div class="article">' . "\n";
	}

	/**
	 * Configure the end of article-tag.
	 *
	 * @return   string $article_tag define the standard article-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getArticleTagEnd(): string
	{
		return ' </div>' . "\n";
	}

	/**
	 * Configure the readon-tag.
	 *
	 * @return   string $readon_tag define the standard readon-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getReadonTag(): string
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
	 * Configure the beginning of legal-tag.
	 *
	 * @return   string $legal_tag define the standard legal-tag.
	 *
	 * @since    2.0.0
	 */
	public static function getLegalTagBegin(): string
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
	public static function getLegalTagEnd(): string
	{
		$legal_tag_end  = '           </td>' . "\n";
		$legal_tag_end .= '          </tr>' . "\n";
		$legal_tag_end .= '         </tbody></table>' . "\n";
		$legal_tag_end .= '       </td>' . "\n";
		$legal_tag_end .= '     </tr>' . "\n";
		$legal_tag_end .= '   </tbody></table>' . "\n";

		return $legal_tag_end;
	}
}
