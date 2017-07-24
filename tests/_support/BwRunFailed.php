<?php
namespace Codeception\Extension;

use Codeception\Event\PrintResultEvent;
use Codeception\Events;
use Codeception\Extension;
use Codeception\Test\Descriptor;

/**
 * Saves failed tests into tests/log/failed in order to rerun failed tests.
 *
 * To rerun failed tests just run the `failed` group:
 *
 * ```
 * php codecept run -g failed
 * ```
 *
 * Starting from Codeception 2.1 **this extension is enabled by default**.
 *
 * ``` yaml
 * extensions:
 *     enabled: [Codeception\Extension\RunFailed]
 * ```
 *
 * On each execution failed tests are logged and saved into `tests/_output/failed` file.
 *
 * @since 2.0.0
 */
class BwRunFailed extends \Codeception\Extension
{
    public static $events = [
        Events::RESULT_PRINT_AFTER => 'saveFailed'
    ];

    protected $config = ['file' => 'failed'];

    public function _initialize()
    {
        $logPath = str_replace($this->getRootDir(), '', $this->getLogDir()); // get local path to logs
        $this->_reconfigure(['groups' => ['failed' => $logPath . $this->config['file']]]);
    }

	/**
	 * @param PrintResultEvent $e
	 *
	 *
	 * @since version
	 */
	public function saveFailed(PrintResultEvent $e)
    {
    	$new_test_run   = getenv('BW_NEW_TEST_RUN');

        $file = $this->getLogDir() . $this->config['file'];
        $result = $e->getResult();

        if ($new_test_run == 'true') {
            if (is_file($file)) {
                rename($file, $file . '_previous');
            }
        }

        putenv('BW_NEW_TEST_RUN=false');

	    if ($result->wasSuccessful()) {
		    return;
	    }

        $output = [];
        foreach ($result->failures() as $fail) {
            $output[] = $this->localizePath(Descriptor::getTestFullName($fail->failedTest()));
        }
        foreach ($result->errors() as $fail) {
            $output[] = $this->localizePath(Descriptor::getTestFullName($fail->failedTest()));
        }

        file_put_contents($file, implode("\n", $output), FILE_APPEND);
    }

	/**
	 * @param $path
	 *
	 * @return bool|string
	 *
	 * @since version
	 */
	protected function localizePath($path)
    {
        $root = realpath($this->getRootDir()) . DIRECTORY_SEPARATOR;
        if (substr($path, 0, strlen($root)) == $root) {
            return substr($path, strlen($root));
        }
        return $path;
    }
}
