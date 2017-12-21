<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman helper class for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
defined ('_JEXEC') or die ('Restricted access');


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
	 * @param    string $head_tag define the standard head-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getHeadTag()
	{
		$head_tag  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$head_tag .= '<html>'."\n";
		$head_tag .= '	<head>'."\n";
		$head_tag .= '		<title>Newsletter</title>'."\n";
		$head_tag .= '		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
		$head_tag .= '		<meta name="robots" content="noindex,nofollow" />'."\n";
		$head_tag .= '		<meta property="og:title" content="HTML Newsletter" />'."\n";

		return $head_tag;
	}

	/**
	 * Configure the body-tag.
	 *
	 * @param    string $body_tag define the standard body-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getBodyTag()
	{
        $body_tag = ' <body bgcolor="#ffffff" emb-default-bgcolor="#ffffff">'."\n";

		return $body_tag;
	}

	/**
	 * Configure the begin of article-tag.
	 *
	 * @param    string $article_tag define the standard article-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getArticleTagBegin()
	{
        $article_tag_begin = ' <div class="article">'."\n";

		return $article_tag_begin;
	}

	/**
	 * Configure the end of article-tag.
	 *
	 * @param    string $article_tag define the standard article-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getArticleTagEnd()
	{
        $article_tag_end = ' </div>'."\n";

		return $article_tag_end;
	}

	/**
	 * Configure the readon-tag.
	 *
	 * @param    string $readon_tag define the standard readon-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getReadonTag()
	{
        $readon_tag  = '<div class="read_on">'."\n";
        $readon_tag .= '	<p>'."\n";
        $readon_tag .= '		<a href="[%readon_href%]" class="readon">[%readon_text%]</a>'."\n";
        $readon_tag .= '		<br/><br/>'."\n";
        $readon_tag .= '	</p>'."\n";
        $readon_tag .= '</div>'."\n";

		return $readon_tag;
	}

	/**
	 * Configure the begin of legal-tag.
	 *
	 * @param    string $legal_tag define the standard legal-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getLegalTagBegin()
	{
		$legal_tag_begin  = '   <table id="legal" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed; width: 100%;"><tbody>';
		$legal_tag_begin .= '     <tr>'."\n";
		$legal_tag_begin .= '       <td id="legal_td">'."\n";
		$legal_tag_begin .= '         <table class="one-col legal" style="border-collapse: collapse;border-spacing: 0;"><tbody>'."\n";
		$legal_tag_begin .= '          <tr>'."\n";
		$legal_tag_begin .= '           <td class="legal_td">'."\n";

		return $legal_tag_begin;
	}

	/**
	 * Configure the end of legal-tag.
	 *
	 * @param    string $legal_tag define the standard legal-tag.
	 *
	 * @return    void
	 *
	 * @since    2.0.0
	 */
	public static function getLegalTagEnd()
	{
		$legal_tag_end  = '           </td>'."\n";
		$legal_tag_end .= '          </tr>'."\n";
		$legal_tag_end .= '         </tbody></table>'."\n";
		$legal_tag_end .= '       </td>'."\n";
		$legal_tag_end .= '     </tr>'."\n";
		$legal_tag_end .= '   </tbody></table>'."\n";

		return $legal_tag_end;
	}


}
