<?php
/**
 * BwPostman NewsletterContent Plugin
 *
 * BwPostman NewsletterContent Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman NewsletterContent Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwSiteApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Menu\MenuFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Profiler\Profiler;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\DI\Container;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;
use Joomla\Session\SessionInterface;
use Psr\Log\LoggerInterface;

define('JPATH_THEMES_SITE', JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates');

if (!ComponentHelper::isEnabled('com_bwpostman')) {
	Factory::getApplication()->enqueueMessage(
		Text::_('PLG_BWPOSTMAN_PLUGIN_NEWSLETTERCONTENT_ERROR') . ', ' . Text::_('PLG_BWPOSTMAN_PLUGIN_NEWSLETTERCONTENT_COMPONENT_NOT_INSTALLED'),
		'error'
	);
	return false;
}

/**
 * Class plgBwPostmanNewslettercontent
 *
 * @since       4.2.0
 */
class PlgBwPostmanNewslettercontent extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 *
	 * @since       4.2.0
	 */
	protected $db;

	/**
	 * Method to process content plugins on an article, when it is added to the newsletter content
	 *
	 * @param string $context    The context working in
	 * @param object $article    The complete content of the newsletter up to now as html content and text content
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since       4.2.0
	 */
	public function onBwpmRenderNewsletterArticle(string $context, object &$article): bool
	{
		// Some plugins don't make sense in context of newsletters, so exclude them from processing
		$excludedPlugins = array(
			'confirmconsent',
			'emailcloak',
			'finder',
			'joomla',
			'pagebreak',
			'pagenavigation',
			'vote',
			'jce',
		);

		$includedPlugins = array(
			'fields',
			'phocamenu',
		);

		// Prepare the applications
		$container = Factory::getContainer();
		$user = Factory::getApplication()->getIdentity();

		if (!$container->has('BwApplicationSite'))
		{
			$container->alias(BwSiteApplication::class, 'BwApplicationSite')
				->share(
					'BwApplicationSite',
					function (Container $container) {
						$app = new BwSiteApplication(null, $container->get('config'), null, $container);

						// The session service provider needs Factory::$application, set it if still null
						if (Factory::$application === null) {
							Factory::$application = $app;
						}

						$app->setDispatcher($container->get(DispatcherInterface::class));
						$app->setLogger($container->get(LoggerInterface::class));
						$app->setSession($container->get(SessionInterface::class));
						$app->setUserFactory($container->get(UserFactoryInterface::class));
						$app->setCacheControllerFactory($container->get(CacheControllerFactoryInterface::class));
						$app->setMenuFactory($container->get(MenuFactoryInterface::class));

						return $app;
					},
					true
				);
		}


		$siteApp = $container->get(BwSiteApplication::class);
		$siteApp->scope = 'com_bwpostman';
		$siteApp->initialiseApp();
		$siteApp->loadDocument();
		$siteApp->loadIdentity($user);

		$adminApp = Factory::getApplication();

		// Get list of all content plugins
		$availablePlugins = PluginHelper::getPlugin('content');

		// Only process not excluded plugins, one by one to be able to process special handling, if plugin needs it
		foreach ($availablePlugins as $availablePlugin)
		{
//			if (!in_array($availablePlugin->name, $excludedPlugins))
			if (in_array($availablePlugin->name, $includedPlugins))
			{
				$currentPlugin = $adminApp->bootPlugin($availablePlugin->name, 'content');

				if (method_exists($currentPlugin, 'onContentPrepare'))
				{
					Factory::$application = $siteApp;

					// Handle plugin loadmodule different
					if ($availablePlugin->name == 'loadmodule')
					{
						$article->text = $article->introtext;
//						$article = $this->processLoadModule($article, $currentPlugin);
					}
//					else
//					{
						$currentPlugin->onContentPrepare('com_content.article', $article, $article->attribs, 0);
//					}
					Factory::$application = $adminApp;
				}
			}
		}

		return true;
	}

	/**
	 * Get the path to a layout from a Plugin
	 *
	 * @param string $type   Plugin type
	 * @param string $name   Plugin name
	 * @param string $layout Layout name
	 *
	 * @return  string  Layout path
	 *
	 * @throws Exception
	 * @since   3.0
	 */
	public function getLayoutPath($type, $name, $layout = 'default')
	{
		$app = Factory::getApplication();

		if ($app->isClient('site') || $app->isClient('administrator')) {
			$templateObj = $app->getTemplate(true);
		} else {
			$templateObj = (object) [
				'template' => '',
				'parent'   => '',
			];
		}

		$defaultLayout = $layout;
		$template      = $templateObj->template;

		if (strpos($layout, ':') !== false) {
			// Get the template and file name from the string
			$temp          = explode(':', $layout);
			$template      = $temp[0] === '_' ? $templateObj->template : $temp[0];
			$layout        = $temp[1];
			$defaultLayout = $temp[1] ?: 'default';
		}

		// Build the template and base path for the layout
		$layoutPaths = [];

		if ($template) {
			$layoutPaths[] = JPATH_THEMES_SITE . '/' . $template . '/html/plg_' . $type . '_' . $name . '/' . $layout . '.php';
		}

		if ($templateObj->parent) {
			$layoutPaths[] = JPATH_THEMES_SITE . '/' . $templateObj->parent . '/html/plg_' . $type . '_' . $name . '/' . $layout . '.php';
		}

		$layoutPaths[] = JPATH_PLUGINS . '/' . $type . '/' . $name . '/tmpl/' . $defaultLayout . '.php';
		$layoutPaths[] = JPATH_PLUGINS . '/' . $type . '/' . $name . '/tmpl/default.php';

		foreach ($layoutPaths as $path) {
			if (is_file($path)) {
				return $path;
			}
		}

		return end($layoutPaths);
	}







	/**
	 * Gets the name of the current template.
	 *
	 * @param   boolean  $params  True to return the template parameters
	 *
	 * @return  string|\stdClass  The name of the template if the params argument is false. The template object if the params argument is true.
	 *
	 * @throws  \InvalidArgumentException
	 *
	 * @since   4.2.0
	 */
	public function getTemplate($siteApp, $params = false)
	{
		if (\is_object($siteApp->template)) {
			if ($siteApp->template->parent) {
				if (!is_file(JPATH_THEMES . '/' . $siteApp->template->template . '/index.php')) {
					if (!is_file(JPATH_THEMES . '/' . $siteApp->template->parent . '/index.php')) {
						throw new \InvalidArgumentException(Text::sprintf('JERROR_COULD_NOT_FIND_TEMPLATE', $siteApp->template->template));
					}
				}
			} elseif (!is_file(JPATH_THEMES . '/' . $siteApp->template->template . '/index.php')) {
				throw new \InvalidArgumentException(Text::sprintf('JERROR_COULD_NOT_FIND_TEMPLATE', $siteApp->template->template));
			}

			if ($params) {
				return $siteApp->template;
			}

			return $siteApp->template->template;
		}

		// Get the id of the active menu item
		$menu = $siteApp->getMenu();
		$item = $menu->getActive();

		if (!$item) {
			$item = $menu->getItem($siteApp->input->getInt('Itemid', null));
		}

		$id = 0;

		if (\is_object($item)) {
			// Valid item retrieved
			$id = $item->template_style_id;
		}

		$tid = $siteApp->input->getUint('templateStyle', 0);

		if (is_numeric($tid) && (int) $tid > 0) {
			$id = (int) $tid;
		}

		/** @var OutputController $cache */
//		$cache = $siteApp->getCacheControllerFactory()->createCacheController('output', ['defaultgroup' => 'com_templates']);

		if ($siteApp->getLanguageFilter()) {
			$tag = $siteApp->getLanguage()->getTag();
		} else {
			$tag = '';
		}

		$templates = $siteApp->bootComponent('templates')->getMVCFactory()
			->createModel('Style', 'Administrator')->getSiteTemplates();

		foreach ($templates as &$template) {
			// Create home element
			if ($template->home == 1 && !isset($template_home) || $siteApp->getLanguageFilter() && $template->home == $tag) {
				$template_home = clone $template;
			}

			$template->params = new Registry($template->params);
		}

		// Unset the $template reference to the last $templates[n] item cycled in the foreach above to avoid editing it later
		unset($template);

		// Add home element, after loop to avoid double execution
		if (isset($template_home)) {
			$template_home->params = new Registry($template_home->params);
			$templates[0]          = $template_home;
		}

		if (isset($templates[$id]))
		{
			$template = $templates[$id];
		} else
		{
			$template = $templates[0];
		}

		// Allows for overriding the active template from the request
		$template_override = $siteApp->input->getCmd('template', '');

		// Only set template override if it is a valid template (= it exists and is enabled)
		if (!empty($template_override)) {
			if (is_file(JPATH_THEMES . '/' . $template_override . '/index.php')) {
				foreach ($templates as $tmpl) {
					if ($tmpl->template === $template_override) {
						$template = $tmpl;
						break;
					}
				}
			}
		}

		// Need to filter the default value as well
		$template->template = InputFilter::getInstance()->clean($template->template, 'cmd');

		// Fallback template
		if (!empty($template->parent)) {
			if (!is_file(JPATH_THEMES . '/' . $template->template . '/index.php')) {
				if (!is_file(JPATH_THEMES . '/' . $template->parent . '/index.php')) {
					$siteApp->enqueueMessage(Text::_('JERROR_ALERTNOTEMPLATE'), 'error');

					// Try to find data for 'cassiopeia' template
					$original_tmpl = $template->template;

					foreach ($templates as $tmpl) {
						if ($tmpl->template === 'cassiopeia') {
							$template = $tmpl;
							break;
						}
					}

					// Check, the data were found and if template really exists
					if (!is_file(JPATH_THEMES . '/' . $template->template . '/index.php')) {
						throw new \InvalidArgumentException(Text::sprintf('JERROR_COULD_NOT_FIND_TEMPLATE', $original_tmpl));
					}
				}
			}
		} elseif (!is_file(JPATH_THEMES . '/' . $template->template . '/index.php')) {
			$siteApp->enqueueMessage(Text::_('JERROR_ALERTNOTEMPLATE'), 'error');

			// Try to find data for 'cassiopeia' template
			$original_tmpl = $template->template;

			foreach ($templates as $tmpl) {
				if ($tmpl->template === 'cassiopeia') {
					$template = $tmpl;
					break;
				}
			}

			// Check, the data were found and if template really exists
			if (!is_file(JPATH_THEMES . '/' . $template->template . '/index.php')) {
				throw new \InvalidArgumentException(Text::sprintf('JERROR_COULD_NOT_FIND_TEMPLATE', $original_tmpl));
			}
		}

		// Cache the result
//		$siteApp->template = $template;

		if ($params) {
			return $template;
		}
		$siteApp->set('template', $template);

		return $template->template;
	}




	/**
	 * Method to process loadmodule plugin on an article
	 *
	 * The original loadmodule plugin only works correctly from frontend, but the content for the newsletter is created
	 * in backend. So nearly all methods to get the content of a module fails because wrong $app.
	 *
	 * @param object $article    The complete content of the newsletter up to now as html content and text content
	 * @param object $plugin     The loadmodule plugin
	 *
	 * @return object
	 *
	 * @throws Exception
	 *
	 * @since       4.2.0
	 */
	private function processLoadModule(object $article, object $plugin): object
	{
		$defaultStyle = $plugin->params->get('style', 'none');

		// Fallback xhtml (used in Joomla 3) to html5
		if ($defaultStyle === 'xhtml')
		{
			$defaultStyle = 'html5';
		}

		// Expression to search for (positions)
		$regex = '/{loadposition\s(.*?)}/i';

		// Expression to search for(modules)
		$regexmod = '/{loadmodule\s(.*?)}/i';

		// Expression to search for(id)
		$regexmodid = '/{loadmoduleid\s([1-9][0-9]*)}/i';

		if (str_contains($article->text, '{loadposition '))
		{
			// Find all instances of plugin and put in $matches for loadposition
			// $matches[0] is full pattern match, $matches[1] is the position
			preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

			// No matches, skip this
			if ($matches)
			{
				foreach ($matches as $match)
				{
					$matcheslist = explode(',', $match[1]);

					// We may not have a module style so fall back to the plugin default.
					if (!array_key_exists(1, $matcheslist))
					{
						$matcheslist[1] = $defaultStyle;
					}

					$position = trim($matcheslist[0]);
					$style    = trim($matcheslist[1]);

					$output = $this->_load($position, $style);

					// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
					$start = strpos($article->text, $match[0]);

					if ($start !== false)
					{
						$article->text = substr_replace($article->text, $output, $start, strlen($match[0]));
					}
				}
			}
		}

		if (str_contains($article->text, '{loadmodule '))
		{
			// Find all instances of plugin and put in $matchesmod for loadmodule
			preg_match_all($regexmod, $article->text, $matchesmod, PREG_SET_ORDER);

			// If no matches, skip this
			if ($matchesmod)
			{
				foreach ($matchesmod as $matchmod)
				{
					$matchesmodlist = explode(',', $matchmod[1]);

					// First parameter is the module, will be prefixed with mod_ later
					$module = trim($matchesmodlist[0]);

					// Second parameter is the title
					$title = '';

					if (array_key_exists(1, $matchesmodlist))
					{
						$title = htmlspecialchars_decode(trim($matchesmodlist[1]));
					}

					// Third parameter is the module style, (fallback is the plugin default set earlier).
					$stylemod = $defaultStyle;

					if (array_key_exists(2, $matchesmodlist))
					{
						$stylemod = trim($matchesmodlist[2]);
					}

					$output = $this->_loadmod($module, $title, $stylemod);

					// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
					$start = strpos($article->text, $matchmod[0]);

					if ($start !== false)
					{
						$article->text = substr_replace($article->text, $output, $start, strlen($matchmod[0]));
					}
				}
			}
		}

		if (str_contains($article->text, '{loadmoduleid '))
		{
			// Find all instances of plugin and put in $matchesmodid for loadmoduleid
			preg_match_all($regexmodid, $article->text, $matchesmodid, PREG_SET_ORDER);

			// If no matches, skip this
			if ($matchesmodid)
			{
				foreach ($matchesmodid as $match)
				{
					$id     = trim($match[1]);
					$output = $this->_loadid($id);

					// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
					$start = strpos($article->text, $match[0]);

					if ($start !== false)
					{
						$article->text = substr_replace($article->text, $output, $start, strlen($match[0]));
					}
				}
			}
		}

		return $article;
	}

	/**
	 * Loads and renders the module
	 *
	 * @param string $position The position assigned to the module
	 * @param string $style    The style assigned to the module
	 *
	 * @return  false|string
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	protected function _load(string $position, string $style = 'none')
	{
		$modules  = $this->getModuleByPosition($position);
		$params   = ['style' => $style];
		ob_start();

		foreach ($modules as $module) {
			echo $this->render($module, $params);
		}

		return ob_get_clean();
	}

	/**
	 * This is always going to get the first instance of the module type unless
	 * there is a title.
	 *
	 * @param string $module The module title
	 * @param string $title  The title of the module
	 * @param string $style  The style of the module
	 *
	 * @return  false|string
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	protected function _loadmod(string $module, string $title, string $style = 'none')
	{
		$mod      = $this->getModuleByName($module, $title);

		// If the module without the mod_ isn't found, try it with mod_.
		// This allows people to enter it either way in the content
		if (!isset($mod)) {
			$name = 'mod_' . $module;
			$mod  = $this->getModuleByName($name, $title);
		}

		$params = ['style' => $style];
		ob_start();

		if ($mod->id) {
			echo $this->render($mod, $params);
		}

		return ob_get_clean();
	}

	/**
	 * Loads and renders the module
	 *
	 * @param string $id The id of the module
	 *
	 * @return  false|string
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	protected function _loadid(string $id)
	{
		$modules  = $this->getModuleById($id);
		$params   = ['style' => 'none'];
		ob_start();

		if ($modules->id > 0) {
			echo $this->render($modules, $params);
		}

		return ob_get_clean();
	}

	/**
	 * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'mod_breadcrumbs')
	 *
	 * @param string      $name  The name of the module
	 * @param string|null $title The title of the module, optional
	 *
	 * @return  stdClass  The Module object
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	private function getModuleByName(string $name, string $title = null)
	{
		$result  = null;
		$modules = $this->getModules();
		$total   = count($modules);

		for ($i = 0; $i < $total; $i++) {
			// Match the name of the module
			if ($modules[$i]->name === $name || $modules[$i]->module === $name) {
				// Match the title if we're looking for a specific instance of the module
				if (!$title || $modules[$i]->title === $title) {
					// Found it
					$result = $modules[$i];
					break;
				}
			}
		}

		// If we didn't find it, and the name is mod_something, create a dummy object
		if ($result === null && strpos($name, 'mod_') === 0) {
			$result         = $this->createDummyModule();
			$result->module = $name;
		}

		return $result;
	}

	/**
	 * Get modules by position
	 *
	 * @param string $position The position of the module
	 *
	 * @return  array  An array of module objects
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	private function getModuleByPosition(string $position)
	{
		$position = strtolower($position);
		$result   = [];
		$input    = Factory::getApplication()->getInput();
		$modules  = $this->getModules();
		$total    = count($modules);

		for ($i = 0; $i < $total; $i++) {
			if ($modules[$i]->position === $position) {
				$result[] = $modules[$i];
			}
		}

		// Prepend a dummy module for template preview if no module is published in the position
		if (empty($result) && $input->getBool('tp') && ComponentHelper::getParams('com_templates')->get('template_positions_display')) {
			$dummy                  = $this->createDummyModule();
			$dummy->title           = $position;
			$dummy->position        = $position;
			$dummy->content         = $position;
			$dummy->contentRendered = true;

			array_unshift($result, $dummy);
		}

		return $result;
	}

	/**
	 * Get module by id
	 *
	 * @param string $id The id of the module
	 *
	 * @return  stdClass  The Module object
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	private function getModuleById($id)
	{
		$modules = $this->getModules();

		$total = count($modules);

		for ($i = 0; $i < $total; $i++) {
			// Match the id of the module
			if ((string) $modules[$i]->id === $id) {
				// Found it
				return $modules[$i];
			}
		}

		// If we didn't find it, create a dummy object
		$result = $this->createDummyModule();

		return $result;
	}

	/**
	 * Method to create a dummy module.
	 *
	 * @return  stdClass  The Module object
	 *
	 * @since   4.2.0
	 */
	private function createDummyModule(): stdClass
	{
		$module            = new stdClass();
		$module->id        = 0;
		$module->title     = '';
		$module->module    = '';
		$module->position  = '';
		$module->content   = '';
		$module->showtitle = 0;
		$module->control   = '';
		$module->params    = '';

		return $module;
	}

	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @param object      $module  The name of the module to render
	 * @param array       $attribs Associative array of values
	 * @param string|null $content If present, module information from the buffer will be used
	 *
	 * @return  string  The output of the script
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	private function render(object $module, array $attribs = [], string $content = null)
	{
		if (!is_object($module)) {
			$title = $attribs['title'] ?? null;

			$module = $this->getModuleByName($module, $title);

			if (!is_object($module)) {
				if (is_null($content)) {
					return '';
				}

				/**
				 * If module isn't found in the database but data has been pushed in the buffer
				 * we want to render it
				 */
				$tmp            = $module;
				$module         = new stdClass();
				$module->params = null;
				$module->module = $tmp;
				$module->id     = 0;
				$module->user   = 0;
			}
		}

		// Set the module content
		if (!is_null($content)) {
			$module->content = $content;
		}

		// Get module parameters
		$params = new Registry($module->params);

		// Use parameters from template
		if (isset($attribs['params'])) {
			$template_params = new Registry(html_entity_decode($attribs['params'], ENT_COMPAT, 'UTF-8'));
			$params->merge($template_params);
			$module         = clone $module;
			$module->params = (string) $params;
		}

		// Set cachemode parameter or use JModuleHelper::moduleCache from within the module instead
		$cachemode = $params->get('cachemode', 'static');

		if ($params->get('cache', 0) == 1 && Factory::getApplication()->get('caching') >= 1 && $cachemode !== 'id' && $cachemode !== 'safeuri') {
			// Default to itemid creating method and workarounds on
			$cacheparams               = new stdClass();
			$cacheparams->cachemode    = $cachemode;
			$cacheparams->class        = ModuleHelper::class;
			$cacheparams->method       = 'renderModule';
			$cacheparams->methodparams = [$module, $attribs];
			$cacheparams->cachesuffix  = $attribs['contentOnly'] ?? false;

			// It needs to be done here because the cache controller does not keep reference to the module object
			$module->content         = ModuleHelper::moduleCache($module, $params, $cacheparams);
			$module->contentRendered = true;

			return $module->content;
		}

		return $this->renderModule($module, $attribs);
	}

	/**
	 * Render the module.
	 *
	 * @param object $module  A module object.
	 * @param array  $attribs An array of attributes for the module (probably from the XML).
	 *
	 * @return  string  The HTML content of the module output.
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	private function renderModule(object $module, array $attribs = [])
	{
		$app = Factory::getApplication();

		// Check that $module is a valid module object
		if (!\is_object($module) || !isset($module->module) || !isset($module->params)) {
			if (JDEBUG) {
				Log::addLogger(['text_file' => 'jmodulehelper.log.php'], Log::ALL, ['modulehelper']);
				$app->getLogger()->debug(
					__METHOD__ . '() - The $module parameter should be a module object.',
					['category' => 'modulehelper']
				);
			}

			return '';
		}

		// Get module parameters
		$params = new Registry($module->params);

		// Render the module content
		$this->renderRawModule($module, $params, $attribs);

		// Return early if only the content is required
		if (!empty($attribs['contentOnly'])) {
			return $module->content;
		}

		if (JDEBUG) {
			Profiler::getInstance('Application')->mark('beforeRenderModule ' . $module->module . ' (' . $module->title . ')');
		}

		// Record the scope.
		$scope = $app->scope;

		// Set scope to component name
		$app->scope = $module->module;

		// Get the template
		$template = $app->getTemplate();

		// Check if the current module has a style param to override template module style
		$paramsChromeStyle = $params->get('style');
		$basePath          = '';

		if ($paramsChromeStyle) {
			$paramsChromeStyle   = explode('-', $paramsChromeStyle, 2);
			$ChromeStyleTemplate = strtolower($paramsChromeStyle[0]);
			$attribs['style']    = $paramsChromeStyle[1];

			// Only set $basePath if the specified template isn't the current or system one.
			if ($ChromeStyleTemplate !== $template && $ChromeStyleTemplate !== 'system') {
				$basePath = JPATH_THEMES . '/' . $ChromeStyleTemplate . '/html/layouts';
			}
		}

		// Make sure a style is set
		if (!isset($attribs['style'])) {
			$attribs['style'] = 'none';
		}

		// Dynamically add outline style
		if ($app->getInput()->getBool('tp') && ComponentHelper::getParams('com_templates')->get('template_positions_display')) {
			$attribs['style'] .= ' outline';
		}

		$module->style = $attribs['style'];

		// If the $module is nulled it will return an empty content, otherwise it will render the module normally.
		$app->triggerEvent('onRenderModule', [&$module, &$attribs]);

		if ($module === null || !isset($module->content)) {
			return '';
		}

		// Prevent double modification of the module content by chrome style
		$module = clone $module;

		$displayData = [
			'module'  => $module,
			'params'  => $params,
			'attribs' => $attribs,
		];

		foreach (explode(' ', $attribs['style']) as $style) {
			$moduleContent = LayoutHelper::render('chromes.' . $style, $displayData, $basePath);

			if ($moduleContent) {
				$module->content = $moduleContent;
			}
		}

		// Revert the scope
		$app->scope = $scope;

		$app->triggerEvent('onAfterRenderModule', [&$module, &$attribs]);

		if (JDEBUG) {
			Profiler::getInstance('Application')->mark('afterRenderModule ' . $module->module . ' (' . $module->title . ')');
		}

		return $module->content;
	}

	/**
	 * Render the module content.
	 *
	 * @param object   $module  A module object
	 * @param Registry $params  A module parameters
	 * @param array    $attribs An array of attributes for the module (probably from the XML).
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   4.2.0
	 */
	private function renderRawModule($module, Registry $params, $attribs = [])
	{
		if (!empty($module->contentRendered)) {
			return $module->content;
		}

		if (JDEBUG) {
			Profiler::getInstance('Application')->mark('beforeRenderRawModule ' . $module->module . ' (' . $module->title . ')');
		}

		// Get the site application
//		$app = Factory::getApplication();
		$container = \Joomla\CMS\Factory::getContainer();
		$siteApp = $container->get(\Joomla\CMS\Application\SiteApplication::class);
//		$siteApp->execute();

		// Record the scope.
		$scope = $siteApp->scope;

		// Set scope to component name
		$siteApp->scope = $module->module;

		// Get module path
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
		$bootedModule = $siteApp->bootModule($module->module, $siteApp->getName());

		$dispatcher = $bootedModule->getDispatcher($module, $siteApp);

		// Check if we have a dispatcher
		if ($dispatcher) {
			ob_start();
			$this->dispatch($module, $siteApp, null);
//			$dispatcher->dispatch();
			$module->content = ob_get_clean();
		}

		// Add the flag that the module content has been rendered
		$module->contentRendered = true;

		// Revert the scope
		$siteApp->scope = $scope;

		if (JDEBUG) {
			Profiler::getInstance('Application')->mark('afterRenderRawModule ' . $module->module . ' (' . $module->title . ')');
		}

		return $module->content;
	}


	/**
	 * Dispatches the dispatcher.
	 *
	 * @return  void
	 *
	 * @since   4.2.0
	 */
	private function dispatch($module, $siteApp, $input)
	{
		$path = JPATH_ROOT . '/modules/' . $module->module . '/' . $module->module . '.php';

		if (!is_file($path)) {
			return;
		}

//		$template = $siteApp->getTemplate();
		$template = null;
		$params = new Registry($module->params);

		$displayData = array(
		'module'   => $module,
		'app'      => $siteApp,
		'input'    => $input,
		'params'   => $params,
		'template' => $template,
		);


//		$this->loadLanguage();

		// Execute the layout without the module context
		$loader = static function ($path, array $displayData) {
			// If $displayData doesn't exist in extracted data, unset the variable.
			if (!\array_key_exists('displayData', $displayData)) {
				extract($displayData);
				unset($displayData);
			} else {
				extract($displayData);
			}

			include $path;
		};

		$loader($path, $displayData);
	}




	/**
	 * Method to get the modules list for frontend
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since       4.2.0
	 */
	public function getModules(): array
	{
		$app      = Factory::getApplication();
		$itemId   = $app->getInput()->getInt('Itemid', 0);
		$groups   = $app->getIdentity()->getAuthorisedViewLevels();

		$clientId = 0;

		// Build a cache ID for the resulting data object
		$cacheId = implode(',', $groups) . '.' . $clientId . '.' . $itemId;

		$db      = Factory::getContainer()->get(DatabaseInterface::class);
		$query   = $db->getQuery(true);
		$nowDate = Factory::getDate()->toSql();

		$query->select($db->quoteName(['m.id', 'm.title', 'm.module', 'm.position', 'm.content', 'm.showtitle', 'm.params', 'mm.menuid']))
			->from($db->quoteName('#__modules', 'm'))
			->join(
				'LEFT',
				$db->quoteName('#__modules_menu', 'mm'),
				$db->quoteName('mm.moduleid') . ' = ' . $db->quoteName('m.id')
			)
			->join(
				'LEFT',
				$db->quoteName('#__extensions', 'e'),
				$db->quoteName('e.element') . ' = ' . $db->quoteName('m.module')
				. ' AND ' . $db->quoteName('e.client_id') . ' = ' . $db->quoteName('m.client_id')
			)
			->where(
				[
					$db->quoteName('m.published') . ' = 1',
					$db->quoteName('e.enabled') . ' = 1',
					$db->quoteName('m.client_id') . ' = :clientId',
				]
			)
			->bind(':clientId', $clientId, ParameterType::INTEGER)
//			->whereIn($db->quoteName('m.client_id'), $clientId)
			->whereIn($db->quoteName('m.access'), $groups)
			->extendWhere(
				'AND',
				[
					$db->quoteName('m.publish_up') . ' IS NULL',
					$db->quoteName('m.publish_up') . ' <= :publishUp',
				],
				'OR'
			)
			->bind(':publishUp', $nowDate)
			->extendWhere(
				'AND',
				[
					$db->quoteName('m.publish_down') . ' IS NULL',
					$db->quoteName('m.publish_down') . ' >= :publishDown',
				],
				'OR'
			)
			->bind(':publishDown', $nowDate)
			->extendWhere(
				'AND',
				[
					$db->quoteName('mm.menuid') . ' = :itemId',
					$db->quoteName('mm.menuid') . ' <= 0',
				],
				'OR'
			)
			->bind(':itemId', $itemId, ParameterType::INTEGER);

		$query->order($db->quoteName(['m.position', 'm.ordering']));

		// Set the query
		$db->setQuery($query);

		try {
			/** @var CallbackController $cache */
			$cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
				->createCacheController('callback', ['defaultgroup' => 'com_modules']);

			$modules = $cache->get([$db, 'loadObjectList'], [], md5($cacheId), false);
		} catch (RuntimeException $e) {
			$app->getLogger()->warning(
				Text::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()),
				['category' => 'jerror']
			);

			return array();
		}


		return $modules;
	}
}
