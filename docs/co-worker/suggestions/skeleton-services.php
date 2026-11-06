<?php

/**
 * Skeleton generator: Services & Dependency Injection for a Joomla 5/6 component
 * ============================================================================
 * Run once inside an EMPTY test directory (NOT directly inside your Joomla project):
 *
 *     php create_skeleton.php [target-directory]
 *
 * Generates all PHP files as real files:
 *   - 3 services (Filesystem, UserState, Logger) with interfaces,
 *     Aware interfaces and Aware traits
 *   - Custom MVC factory with service injection
 *   - services/provider.php
 *   - Example models for Administrator AND Site
 *   - PHPUnit test
 *
 * DRY principle: services live ONLY in the Administrator part (that is also
 * where the single services/provider.php lives, which runs on boot for BOTH
 * Site and Admin – com_content does it the same way). Site classes import the
 * services via the Administrator namespace; both prefixes are autoloadable
 * system-wide.
 *
 * Verify before using in production:
 *  1. The constructor signature of Joomla\CMS\MVC\Factory\MVCFactory in your
 *     target Joomla version (may additionally expect a DatabaseInterface).
 *  2. ACL checks belong in the controllers, NEVER solely in shared services.
 */

// ----------------------------- CONFIGURATION ---------------------------------

const VENDOR  = 'BoldtWebservice';       // vendor namespace
const PACKAGE = 'BwPostman';     // component name (CamelCase)
const ELEMENT = 'com_bwpostman'; // lower-cased component name

// ----------------------------- SHORTCUTS -------------------------------------

define('NS_ADMIN',    '\\' . VENDOR . '\\Component\\' . PACKAGE . '\\Administrator');
define('NS_BASE',     '\\' . VENDOR . '\\Component\\' . PACKAGE);
define('NS_ESCAPED',  '\\\\' . VENDOR . '\\\\Component\\\\' . PACKAGE);
define('DIR_ADMIN',   'administrator/components/' . ELEMENT);
define('DIR_SITE',    'components/' . ELEMENT);

$files = [];

// ------------------------- 1. SERVICES (Admin only) ---------------------------
// Per service: interface, implementation, Aware interface, Aware trait.

/**
 * @param string $name      Service name (Filesystem, UserState, ...)
 * @param string $property Property name in consumers (filesystemService, userStateService, ...)
 * @param string $ifaceBody  Interface methods
 * @param string $implBody   Class body including use statements
 */
$addService = static function (string $name, string $property, string $ifaceBody, string $implBody) use (&$files): void {
    $iface = $name . 'Interface';

    $files['src/Service/' . $iface . '.php'] =
        "<?php\n"
        . "namespace " . NS_ADMIN . "\\Service;\n\n"
        . "interface {$iface}\n{\n" . $ifaceBody . "\n}\n";

    $files['src/Service/' . $name . '.php'] =
        "<?php\n"
        . "namespace " . NS_ADMIN . "\\Service;\n\n"
        . $implBody . "\n";

    $files['src/Service/' . $name . 'ServiceAwareInterface.php'] =
        "<?php\n"
        . "namespace " . NS_ADMIN . "\\Service;\n\n"
        . "interface {$name}ServiceAwareInterface\n{\n"
        . "    public function set{$name}({$iface} \$service): void;\n"
        . "}\n";

    $files['src/Service/' . $name . 'ServiceAwareTrait.php'] =
        "<?php\n"
        . "namespace " . NS_ADMIN . "\\Service;\n\n"
        . "trait {$name}ServiceAwareTrait\n{\n"
        . "    protected {$iface} \${$property};\n\n"
        . "    public function set{$name}({$iface} \$service): void\n"
        . "    {\n"
        . "        \$this->{$property} = \$service;\n"
        . "    }\n"
        . "}\n";
};

// --- 1a. FilesystemService -------------------------------------------------
$addService(
    'Filesystem',
    'filesystemService',
    "    public function exists(string \$path): bool;\n\n"
    . "    public function write(string \$path, string \$content): bool;\n\n"
    . "    public function delete(string \$path): bool;",
    "use Joomla\\CMS\\Filesystem\\File;\n\n"
    . "/**\n"
    . " * The ONLY class of the component touching Joomla's filesystem classes.\n"
    . " * That is exactly why: this class is not unit tested (integration test!),\n"
    . " * everything else is mocked against the interface.\n"
    . " */\n"
    . "class FilesystemService implements FilesystemServiceInterface\n{\n"
    . "    public function exists(string \$path): bool\n"
    . "    {\n"
    . "        return File::exists(\$path);\n"
    . "    }\n\n"
    . "    public function write(string \$path, string \$content): bool\n"
    . "    {\n"
    . "        return File::write(\$path, \$content) !== false;\n"
    . "    }\n\n"
    . "    public function delete(string \$path): bool\n"
    . "    {\n"
    . "        return File::delete(\$path);\n"
    . "    }\n"
    . "}"
);

// --- 1b. UserStateService ---------------------------------------------------
$addService(
    'UserState',
    'userStateService',
    "    public function get(string \$key, mixed \$default = null): mixed;\n\n"
    . "    public function set(string \$key, mixed \$value): mixed;",
    "use Joomla\\CMS\\Factory;\n\n"
    . "/**\n"
    . " * Wraps access to session / user state values, scoped to the\n"
    . " * component (prevents name collisions in the session store).\n"
    . " *\n"
    . " * Note: in Joomla 5/6 Factory::getSession() is deprecated.\n"
    . " * The supported way is Factory::getApplication()->getSession().\n"
    . " */\n"
    . "class UserStateService implements UserStateServiceInterface\n{\n"
    . "    public function get(string \$key, mixed \$default = null): mixed\n"
    . "    {\n"
    . "        return Factory::getApplication()\n"
    . "            ->getSession()\n"
    . "            ->get('com_mycomp.' . \$key, \$default);\n"
    . "    }\n\n"
    . "    public function set(string \$key, mixed \$value): mixed\n"
    . "    {\n"
    . "        return Factory::getApplication()\n"
    . "            ->getSession()\n"
    . "            ->set('com_mycomp.' . \$key, \$value);\n"
    . "    }\n"
    . "}"
);

// --- 1c. LoggerService ------------------------------------------------------
$addService(
    'Logger',
    'loggerService',
    "    public function debug(string \$message, array \$context = []): void;\n\n"
    . "    public function info(string \$message, array \$context = []): void;\n\n"
    . "    public function error(string \$message, array \$context = []): void;",
    "use Joomla\\CMS\\Log\\Log;\n\n"
    . "/**\n"
    . " * Deliberately minimal (3 methods instead of the full Joomla log API):\n"
    . " * a small, stable interface survives refactors in the core.\n"
    . " */\n"
    . "class LoggerService implements LoggerServiceInterface\n{\n"
    . "    public function __construct(private readonly string \$context = 'com_mycomp')\n"
    . "    {\n"
    . "    }\n\n"
    . "    public function debug(string \$message, array \$context = []): void\n"
    . "    {\n"
    . "        Log::add(\$message, Log::DEBUG, \$this->context);\n"
    . "    }\n\n"
    . "    public function info(string \$message, array \$context = []): void\n"
    . "    {\n"
    . "        Log::add(\$message, Log::INFO, \$this->context);\n"
    . "    }\n\n"
    . "    public function error(string \$message, array \$context = []): void\n"
    . "    {\n"
    . "        Log::add(\$message, Log::ERROR, \$this->context);\n"
    . "    }\n"
    . "}"
);

// ------------------------- 2. MVC FACTORY -------------------------------------

$files['src/MVC/' . PACKAGE . 'MVCFactory.php'] = <<<'PHP'
<?php
/**
 * Extended MVC factory: after creating an MVC object it checks which Aware
 * interfaces the object implements and injects the matching services from the
 * component's container. Adding a new service = one more if-line in
 * injectServices().
 *
 * CHECKPOINT: inspect the constructor of the parent class
 * Joomla\CMS\MVC\Factory\MVCFactory in your Joomla version (it may also expect
 * a DatabaseInterface). Adjust the signature if necessary.
 */
namespace {NS_ADMIN}\MVC;

use {NS_ADMIN}\Service\FilesystemServiceAwareInterface;
use {NS_ADMIN}\Service\FilesystemServiceInterface;
use {NS_ADMIN}\Service\LoggerServiceAwareInterface;
use {NS_ADMIN}\Service\LoggerServiceInterface;
use {NS_ADMIN}\Service\UserStateServiceAwareInterface;
use {NS_ADMIN}\Service\UserStateServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\DI\Container;

class {PACKAGE}MVCFactory extends MVCFactory
{
    public function __construct(string $namespace, private readonly Container $container)
    {
        parent::__construct($namespace);
    }

    public function createModel(string $name, ?string $client = null): ?object
    {
        $model = parent::createModel($name, $client);

        return $model !== null ? $this->injectServices($model) : null;
    }

    // Extend analogously for views/tables if they need services:
    // public function createView(string $name, ?string $client = null): ?object
    // public function createTable(string $name, ?string $client = null): ?object

    private function injectServices(object $object): object
    {
        if ($object instanceof FilesystemServiceAwareInterface) {
            $object->setFilesystem(
                $this->container->get(FilesystemServiceInterface::class)
            );
        }

        if ($object instanceof UserStateServiceAwareInterface) {
            $object->setUserState(
                $this->container->get(UserStateServiceInterface::class)
            );
        }

        if ($object instanceof LoggerServiceAwareInterface) {
            $object->setLogger(
                $this->container->get(LoggerServiceInterface::class)
            );
        }

        return $object;
    }
}
PHP;

// ------------------------- 3. SERVICE PROVIDER --------------------------------

$files['services/provider.php'] = <<<'PHP'
<?php
/**
 * The service provider is the component's "recipe book":
 * it defines HOW each service is created – not WHO consumes it.
 * On boot Joomla creates a child container and runs this register() once.
 * The provider applies to Site AND Administrator (com_content does the same).
 */
\defined('_JEXEC') or die;

use {NS_ADMIN}\MVC\{PACKAGE}MVCFactory;
use {NS_ADMIN}\Service\FilesystemService;
use {NS_ADMIN}\Service\FilesystemServiceInterface;
use {NS_ADMIN}\Service\LoggerService;
use {NS_ADMIN}\Service\LoggerServiceInterface;
use {NS_ADMIN}\Service\UserStateService;
use {NS_ADMIN}\Service\UserStateServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register services – always keyed by the interface,
        // so the implementation can be swapped later without
        // touching any consumer.

        $container->set(
            FilesystemServiceInterface::class,
            static fn (Container $c) => new FilesystemService()
        );

        $container->set(
            UserStateServiceInterface::class,
            static fn (Container $c) => new UserStateService()
        );

        $container->set(
            LoggerServiceInterface::class,
            static fn (Container $c) => new LoggerService()
        );

        // Register our custom MVC factory instead of the core one,
        // so that models (Site and Admin!) get their services injected.
        $container->set(
            MVCFactoryInterface::class,
            static fn (Container $c) => new {PACKAGE}MVCFactory('{NS_ESCAPED}', $c)
        );
    }
};
PHP;

// ------------------------- 4. EXAMPLE MODEL (Admin) ----------------------------

$files['src/Model/ExportModel.php'] = <<<'PHP'
<?php
/**
 * Example model consuming all three services.
 * No Factory:: calls, no container access, no static File::exists() –
 * all dependencies arrive via setters (injected by the MVC factory).
 * In the unit test, mocks are passed through those very same setters.
 */
namespace {NS_ADMIN}\Model;

use {NS_ADMIN}\Service\FilesystemServiceAwareInterface;
use {NS_ADMIN}\Service\FilesystemServiceAwareTrait;
use {NS_ADMIN}\Service\LoggerServiceAwareInterface;
use {NS_ADMIN}\Service\LoggerServiceAwareTrait;
use {NS_ADMIN}\Service\UserStateServiceAwareInterface;
use {NS_ADMIN}\Service\UserStateServiceAwareTrait;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ExportModel extends BaseDatabaseModel implements
    FilesystemServiceAwareInterface,
    UserStateServiceAwareInterface,
    LoggerServiceAwareInterface
{
    use FilesystemServiceAwareTrait;
    use UserStateServiceAwareTrait;
    use LoggerServiceAwareTrait;

    public function exportTo(string $file): bool
    {
        $lastRun = $this->userStateService->get('last_export');

        if (!$this->filesystemService->exists(\dirname($file))) {
            $this->loggerService->error(
                'Export failed: target directory missing',
                ['file' => $file, 'last_run' => $lastRun]
            );

            return false;
        }

        // ... actual export logic ...

        $this->userStateService->set('last_export', time());

        return true;
    }
}
PHP;

// ------------------------- 5. EXAMPLE MODEL (Site) ----------------------------

$files['site/Model/ExportModel.php'] = <<<'PHP'
<?php
/**
 * Site example model: shows how the Site part imports the services from the
 * Administrator namespace (both prefixes are autoloadable system-wide) –
 * DRY, no second Service folder required.
 *
 * SECURITY: ACL checks belong in the Site controller, NEVER solely in
 * shared services.
 */
namespace {NS_BASE}\Site\Model;

use {NS_ADMIN}\Service\FilesystemServiceAwareInterface;
use {NS_ADMIN}\Service\FilesystemServiceAwareTrait;
use {NS_ADMIN}\Service\LoggerServiceAwareInterface;
use {NS_ADMIN}\Service\LoggerServiceAwareTrait;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ExportModel extends BaseDatabaseModel implements
    FilesystemServiceAwareInterface,
    LoggerServiceAwareInterface
{
    use FilesystemServiceAwareTrait;
    use LoggerServiceAwareTrait;

    public function fileSize(string $file): ?int
    {
        if (!$this->filesystemService->exists($file)) {
            $this->loggerService->error('File not found', ['file' => $file]);

            return null;
        }

        return \filesize($file) ?: null;
    }
}
PHP;

// ------------------------- 6. UNIT TEST ---------------------------------------

$files['tests/ExportModelTest.php'] = <<<'PHP'
<?php
/**
 * Unit test without the Joomla bootstrap, without a filesystem, without a session.
 * All dependencies are mocks of the respective interface.
 */
namespace {NS_ADMIN}\Tests;

use {NS_ADMIN}\Model\ExportModel;
use {NS_ADMIN}\Service\FilesystemServiceInterface;
use {NS_ADMIN}\Service\LoggerServiceInterface;
use {NS_ADMIN}\Service\UserStateServiceInterface;
use PHPUnit\Framework\TestCase;

final class ExportModelTest extends TestCase
{
    public function testExportFailsWhenDirectoryMissing(): void
    {
        $fs = $this->createMock(FilesystemServiceInterface::class);
        $fs->method('exists')->willReturn(false);

        $logger = $this->createMock(LoggerServiceInterface::class);
        $logger->expects($this->once())->method('error');

        $userState = $this->createMock(UserStateServiceInterface::class);
        $userState->method('get')->willReturn(12345);
        $userState->expects($this->never())->method('set');

        $model = new ExportModel();
        $model->setFilesystem($fs);
        $model->setUserState($userState);
        $model->setLogger($logger);

        self::assertFalse($model->exportTo('/tmp/export.csv'));
    }

    public function testExportSucceeds(): void
    {
        $fs = $this->createMock(FilesystemServiceInterface::class);
        $fs->method('exists')->willReturn(true);

        $logger = $this->createMock(LoggerServiceInterface::class);

        $userState = $this->createMock(UserStateServiceInterface::class);
        $userState->expects($this->once())->method('set');

        $model = new ExportModel();
        $model->setFilesystem($fs);
        $model->setUserState($userState);
        $model->setLogger($logger);

        self::assertTrue($model->exportTo('/tmp/export.csv'));
    }
}
PHP;

// ------------------------- 7. README -------------------------------------------

$files['README.md'] = <<<'MD'
# Service skeleton for com_mycomp (Joomla 5/6)

Generated with create_skeleton.php. Structure:

- administrator/components/com_mycomp/src/Service/   Services (ONLY here, DRY)
- administrator/components/com_mycomp/src/MVC/       Custom MVC factory with service injection
- administrator/components/com_mycomp/services/      The ONE provider (boots for Site AND Admin)
- administrator/components/com_mycomp/src/Model/     Admin example model
- administrator/components/com_mycomp/tests/         PHPUnit tests with interface mocks
- components/com_mycomp/src/Model/                   Site example model (imports Admin services)

## Adding a fourth service (e.g. an XmlParser)

1. Interface `XmlParserServiceInterface` (+ method, e.g. parse(string $xml))
2. Implementation `XmlParserService`
3. Aware interface + trait (`setXmlParser(...)`)
4. One `$container->set()` line in services/provider.php
5. One `if` line in MycompMVCFactory::injectServices()

After that any model/view/table can use the service by pulling in interface +
trait – the MVC factory injects it automatically.

## Verify before using in production

1. The constructor signature of `Joomla\CMS\MVC\Factory\MVCFactory` in your
   target Joomla version (may additionally expect a DatabaseInterface).
2. ACL checks always belong in the controllers, never in shared services.
MD;

// ------------------------- GENERATION -----------------------------------------

$target      = $argv[1] ?? getcwd();
$prefixAdmin = $target . '/' . DIR_ADMIN;
$prefixSite  = $target . '/' . DIR_SITE;

foreach ($files as $relative => $content) {
    // The Site example model belongs into the component's Site branch.
    if (str_starts_with($relative, 'site/')) {
        $prefix   = $prefixSite;
        $relative = substr($relative, \strlen('site/'));
    } else {
        $prefix = $prefixAdmin;
    }

    $content = str_replace(
        ['{NS_ADMIN}', '{NS_BASE}', '{PACKAGE}', '{NS_ESCAPED}'],
        [NS_ADMIN, NS_BASE, PACKAGE, NS_ESCAPED],
        $content
    );

    $path = $prefix . '/' . $relative;

    if (!is_dir(\dirname($path)) && !@mkdir(\dirname($path), 0755, true)) {
        fwrite(STDERR, "ERROR: cannot create directory: " . \dirname($path) . "\n");
        exit(1);
    }

    if (file_put_contents($path, $content) === false) {
        fwrite(STDERR, "ERROR: cannot write: $path\n");
        exit(1);
    }

    echo "created: $path\n";
}

echo "\nDone. " . \count($files) . " files generated.\n";
echo "Next steps: read README.md, verify the MVCFactory constructor.\n";
