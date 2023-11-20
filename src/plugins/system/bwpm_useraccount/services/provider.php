<?php
defined('_JEXEC') || die;

use BoldtWebservice\Plugin\System\Bwpm_useraccount\Extension\Bwpm_useraccount;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

return new class implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param Container $container The DI container.
     *
     * @return  void
     *
     * @since   4.2.6
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container)
            {
                $config  = (array)PluginHelper::getPlugin('system', 'bw_libregister');
                $subject = $container->get(DispatcherInterface::class);

                /** @var \Joomla\CMS\Plugin\CMSPlugin $plugin */
                $plugin = new Bwpm_useraccount($subject, $config);
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
