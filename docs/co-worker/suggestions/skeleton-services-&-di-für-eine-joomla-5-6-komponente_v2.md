# Skeleton: Services & Dependency Injection für eine Joomla-5/6-Komponente

Kommentierte Beispielstruktur zum direkten Übernehmen ins eigene Projekt.
Platzhalter umbenennen: `Mycomp` → Komponentenname, `Acme` → Vendor-Namespace,
`com_mycomp` → Komponentenname klein geschrieben.

## DRY: ein Service-Ordner statt zwei

Komponenten haben zwei getrennte MVC-Welten (`...\Site` und `...\Administrator`),
Services sind aber **normale, überall autoladbare Klassen**. Deshalb:
Services NUR im Administrator-Teil anlegen (dort liegt auch das einzige
`services/provider.php`, das beim Booten für Site UND Admin läuft – so macht es
auch com_content). Site-Modelle importieren die Services einfach:

```php
use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceInterface;
```

**Security-Hinweis:** In geteilten Services dürfen nie ACL-Prüfungen *allein*
liegen – die gehören in den jeweils zuständigen Controller der Client-Seite.

## Dateistruktur

```text
administrator/components/com_bwpostman/          ← Administrator-Teil
├── services/
│   └── provider.php                          ← der EINE Provider (für beide Clients)
└── src/
    ├── MVC/
    │   └── BwPostmanMVCFactory.php               ← injiziert für Site UND Admin
    ├── Model/
    │   └── ExportModel.php
    └── Service/                               ← EIN Service-Ordner für alles
        ├── FilesystemService.php
        ├── FilesystemServiceInterface.php
        ├── FilesystemServiceAwareInterface.php
        ├── FilesystemServiceAwareTrait.php
        ├── UserStateService.php
        ├── UserStateServiceInterface.php
        ├── UserStateServiceAwareInterface.php
        ├── UserStateServiceAwareTrait.php
        ├── LoggerService.php
        ├── LoggerServiceInterface.php
        ├── LoggerServiceAwareInterface.php
        └── LoggerServiceAwareTrait.php

components/com_bwpostman/                         ← Site-Teil (kein Service-Ordner!)
└── src/
    └── Model/                                 ← nutzt Administrator\Service\...
```

**Wichtig vor dem Übernehmen (zwei Prüfpunkte):**

1. Konstruktor-Signatur von `Joomla\CMS\MVC\Factory\MVCFactory` in der eigenen
   Joomla-Version prüfen (erwartet evtl. Namespace UND DatabaseInterface).
2. Namespaces/Pfade an die eigene Struktur anpassen (Joomla-Konvention:
   `BoldtWebservice\Component\BwPostman\Administrator\...` bzw. `\Site\...`).

---

## 1. `src/Service/FilesystemServiceInterface.php`

```php
<?php
/**
 * Vertrag für Dateisystemzugriffe.
 * Das Model kennt nur dieses Interface – nie eine konkrete Joomla-Klasse.
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

interface FilesystemServiceInterface
{
    public function exists(string $path): bool;

    public function write(string $path, string $content): bool;

    public function delete(string $path): bool;
}
```

## 2. `src/Service/FilesystemService.php`

```php
<?php
/**
 * Die EINZIGE Klasse der Komponente, die Joomlas Filesystem-Klassen berührt.
 * Genau deshalb: Diese Klasse wird nicht unit-getestet (Integrationstest!),
 * alles andere wird gegen das Interface gemockt.
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

use Joomla\CMS\Filesystem\File;

class FilesystemService implements FilesystemServiceInterface
{
    public function exists(string $path): bool
    {
        return File::exists($path);
    }

    public function write(string $path, string $content): bool
    {
        return File::write($path, $content) !== false;
    }

    public function delete(string $path): bool
    {
        return File::delete($path);
    }
}
```

## 3. `src/Service/FilesystemServiceAwareInterface.php`

```php
<?php
/**
 * Klassen, die einen Filesystem-Service erhalten können, implementieren
 * dieses Interface. Die MVCFactory erkennt darüber, was zu injizieren ist.
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

interface FilesystemServiceAwareInterface
{
    public function setFilesystem(FilesystemServiceInterface $service): void;
}
```

## 4. `src/Service/FilesystemServiceAwareTrait.php`

```php
<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

trait FilesystemServiceAwareTrait
{
    protected FilesystemServiceInterface $filesystemService;

    public function setFilesystem(FilesystemServiceInterface $service): void
    {
        $this->filesystemService = $service;
    }
}
```

---

## 5. `src/Service/UserStateServiceInterface.php`

```php
<?php
/**
 * Kapselt den Zugriff auf Session-/UserState-Werte (Scoped auf die Komponente,
 * damit es keine Namenskollisionen im Session-Store gibt).
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

interface UserStateServiceInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): mixed;
}
```

## 6. `src/Service/UserStateService.php`

```php
<?php
/**
 * Achtung: In Joomla 5/6 ist Factory::getSession() deprecated.
 * Der Weg führt über Factory::getApplication()->getSession().
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

use Joomla\CMS\Factory;

class UserStateService implements UserStateServiceInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Factory::getApplication()
            ->getSession()
            ->get('com_bwpostman.' . $key, $default);
    }

    public function set(string $key, mixed $value): mixed
    {
        return Factory::getApplication()
            ->getSession()
            ->set('com_bwpostman.' . $key, $value);
    }
}
```

## 7. `src/Service/UserStateServiceAwareInterface.php`

```php
<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

interface UserStateServiceAwareInterface
{
    public function setUserState(UserStateServiceInterface $service): void;
}
```

## 8. `src/Service/UserStateServiceAwareTrait.php`

```php
<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

trait UserStateServiceAwareTrait
{
    protected UserStateServiceInterface $userStateService;

    public function setUserState(UserStateServiceInterface $service): void
    {
        $this->userStateService = $service;
    }
}
```

---

## 9. `src/Service/LoggerServiceInterface.php`

```php
<?php
/**
 * Bewusst minimal (3 Methoden statt der vollen Joomla-Log-API):
 * Ein kleines, stabiles Interface überlebt Umbauten im Core.
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

interface LoggerServiceInterface
{
    public function debug(string $message, array $context = []): void;

    public function info(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;
}
```

## 10. `src/Service/LoggerService.php`

```php
<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

use Joomla\CMS\Log\Log;

class LoggerService implements LoggerServiceInterface
{
    public function __construct(private readonly string $context = 'com_bwpostman')
    {
    }

    public function debug(string $message, array $context = []): void
    {
        Log::add($message, Log::DEBUG, $this->context);
    }

    public function info(string $message, array $context = []): void
    {
        Log::add($message, Log::INFO, $this->context);
    }

    public function error(string $message, array $context = []): void
    {
        Log::add($message, Log::ERROR, $this->context);
    }
}
```

## 11. `src/Service/LoggerServiceAwareInterface.php`

```php
<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

interface LoggerServiceAwareInterface
{
    public function setLogger(LoggerServiceInterface $service): void;
}
```

## 12. `src/Service/LoggerServiceAwareTrait.php`

```php
<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

trait LoggerServiceAwareTrait
{
    protected LoggerServiceInterface $loggerService;

    public function setLogger(LoggerServiceInterface $service): void
    {
        $this->loggerService = $service;
    }
}
```

---

## 13. `services/provider.php`

```php
<?php
/**
 * Der Service Provider ist das "Rezeptbuch" der Komponente:
 * Er definiert, WIE jeder Service erzeugt wird – nicht WHO ihn benutzt.
 * Joomla legt beim Booten der Erweiterung einen eigenen Child-Container an
 * und führt dieses register() einmal aus.
 */
defined('_JEXEC') or die;

use BoldtWebservice\Component\BwPostman\Administrator\MVC\BwPostmanMVCFactory;
use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemService;
use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerService;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerServiceInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateService;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Services registrieren – immer gegen das Interface als Schlüssel!
        // (Dann kann die Implementierung später getauscht werden, ohne
        // dass irgendein Consumer angefasst werden muss.)

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

        // Eigene MVCFactory statt der Core-Factory registrieren,
        // damit Models beim Erzeugen ihre Services injiziert bekommen.
        $container->set(
            MVCFactoryInterface::class,
            static fn (Container $c) => new BwPostmanMVCFactory(
                '\\BoldtWebservice\\Component\\BwPostman',
                $c
            )
        );
    }
};
```

---

## 14. `src/MVC/BwPostmanMVCFactory.php`

```php
<?php
/**
 * Erweiterte MVCFactory: prüft nach dem Erzeugen eines MVC-Objekts,
 * welche Aware-Interfaces es implementiert, und injiziert die passenden
 * Services aus dem Container der Komponente.
 *
 * Neue Services später ergänzen = eine weitere if-Zeile in injectServices().
 *
 * PRÜFPUNKT: Konstruktor der Elternklasse Joomla\CMS\MVC\Factory\MVCFactory
 * in der eigenen Joomla-Version ansehen (nimmt evtl. zusätzlich ein
 * DatabaseInterface). Ggf. Signatur anpassen.
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\MVC;

use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerServiceInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\DI\Container;

class BwPostmanMVCFactory extends MVCFactory
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

    // Analog erweiterbar, falls auch Views/Tables Services brauchen:
    // public function createView(string $name, string $client = null): ?object
    // public function createTable(string $name, string $client = null): ?object

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
```

---

## 15. `src/Model/ExportModel.php` (Beispiel-Consumer)

```php
<?php
/**
 * Beispielmodell, das alle drei Services nutzt.
 * Kein Factory::-Aufruf, kein Container-Zugriff, kein statisches File::exists() –
 * alle Abhängigkeiten kommen per Setter (injiziert von BwPostmanMVCFactory).
 * Im Unit-Test werden einfach Mocks über dieselben Setter übergeben.
 */
namespace BoldtWebservice\Component\BwPostman\Administrator\Model;

use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceAwareTrait;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerServiceAwareTrait;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateServiceAwareTrait;
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
                'Export fehlgeschlagen: Zielverzeichnis fehlt',
                ['file' => $file, 'last_run' => $lastRun]
            );

            return false;
        }

        // ... tatsächliche Exportlogik ...

        $this->userStateService->set('last_export', time());

        return true;
    }
}
```

---

## 16. `tests/ExportModelTest.php`

```php
<?php
/**
 * Unit-Test ohne Joomla-Bootstrap, ohne Dateisystem, ohne Session.
 * Alle Abhängigkeiten sind Mocks des jeweiligen Interfaces.
 */
namespace BoldtWebservice\Component\BwPostman\Tests;

use BoldtWebservice\Component\BwPostman\Administrator\Model\ExportModel;
use BoldtWebservice\Component\BwPostman\Administrator\Service\FilesystemServiceInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\LoggerServiceInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\UserStateServiceInterface;
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
```

---

## Anleitung: Einen vierten Service ergänzen (z. B. XmlParser)

Immer dieselben fünf Schritte – nirgends muss bestehender Code angefasst werden:

1. **Interface**: `XmlParserServiceInterface` mit der gewünschten Methode
   (z. B. `parse(string $xml): \SimpleXMLElement`).
2. **Implementierung**: `XmlParserService` kapselt SimpleXML/DOMDocument.
3. **Aware-Interface + Trait**: `setXmlParser(...)` wie oben.
4. **Provider**: eine weitere `$container->set()`-Zeile in `provider.php`.
5. **Factory**: eine weitere `if`-Zeile in `injectServices()`.

Danach kann jedes Model/View/Table den Dienst nutzen, indem es Interface +
Trait einbindet – die MVCFactory injiziert ihn automatisch.
