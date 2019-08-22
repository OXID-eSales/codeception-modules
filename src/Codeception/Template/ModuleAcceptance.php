<?php

namespace Codeception\Template;

use Codeception\InitTemplate;
use Codeception\Util\Template;
use Symfony\Component\Yaml\Yaml;

class ModuleAcceptance extends InitTemplate
{
    const DIRECTORY_OUTPUT = '_output';
    const DIRECTORY_DATA = '_data';
    const DIRECTORY_SUPPORT = '_support';
    const DIRECTORY_GENERATED = '_support' . DIRECTORY_SEPARATOR . '_generated';
    const DIRECTORY_ACCEPTANCE = 'Acceptance';
    const DIRECTORY_CONFIG = 'Config';
    const DIRECTORY_PAGE_OBJECTS = 'Page';
    const DIRECTORY_MODULES = 'Module';

    protected $configTemplate = "codeception.yml";
    protected $acceptanceConfigTemplate = "acceptance.suite.yml";
    protected $paramsTemplate = "params.php";
    protected $firstTestTemplate = "ExampleCest.php";

    public function setup()
    {
        $this->checkInstalled();
        $this->printHelloMessage();

        $testsDirectory = $this->ask("Where tests will be stored?", 'Codeception');
        $this->namespace = $this->ask("Add namespace", 'MyVendor\MyModule\Tests\Codeception');
        $browser = $this->askForBrowser();

        $this->createStructureDirectories($testsDirectory);

        $configFile = (new Template($this->getTemplateFromFile($this->configTemplate)))
            ->place('browser', $browser)
            ->place('baseDir', $testsDirectory)
            ->place('configDir', self::DIRECTORY_CONFIG)
            ->place('outputDir', self::DIRECTORY_OUTPUT)
            ->place('dataDir', self::DIRECTORY_DATA)
            ->place('supportDir', self::DIRECTORY_SUPPORT)
            ->produce();

        if ($this->namespace) {
            $namespace = rtrim($this->namespace, '\\');
            $configFile = "namespace: $namespace\n" . $configFile;
        }

        $this->createFile($this->configTemplate, $configFile);
        $this->sayInfo("Created global config {$this->configTemplate} inside the root directory");

        $this->createFile(
            $testsDirectory . DIRECTORY_SEPARATOR . $this->acceptanceConfigTemplate,
            $this->getTemplateFromFile($this->acceptanceConfigTemplate)
        );
        $this->sayInfo("Created suite config {$this->acceptanceConfigTemplate} inside $testsDirectory directory");

        $this->createFile(
            $testsDirectory . DIRECTORY_SEPARATOR . self::DIRECTORY_DATA . DIRECTORY_SEPARATOR . 'dump.sql',
            ''
        );
        $this->sayInfo("Created a dump.sql file inside $testsDirectory/" . self::DIRECTORY_DATA);

        $this->createFile(
            $testsDirectory . DIRECTORY_SEPARATOR . self::DIRECTORY_DATA . DIRECTORY_SEPARATOR . 'fixtures.php',
            $this->getTemplateFromFile('fixtures.php')
        );
        $this->sayInfo("Created a fixtures.php file inside $testsDirectory/" . self::DIRECTORY_DATA);

        $acceptanceDirectoryPath = $testsDirectory . DIRECTORY_SEPARATOR . self::DIRECTORY_ACCEPTANCE;

        $exampleTestFile = (new Template($this->getTemplateFromFile($this->firstTestTemplate)))
            ->place('namespace', $this->namespace)
            ->produce();
        $this->createFile(
            $acceptanceDirectoryPath . DIRECTORY_SEPARATOR . $this->firstTestTemplate,
            $exampleTestFile
        );
        $this->sayInfo("Created a demo test {$this->firstTestTemplate} inside $acceptanceDirectoryPath directory");

        $this->createFile(
            $acceptanceDirectoryPath . DIRECTORY_SEPARATOR . '_bootstrap.php',
            $this->getTemplateFromFile('_bootstrap.php')
        );
        $this->sayInfo("Created the _bootstrap.php inside $acceptanceDirectoryPath directory");

        $paramsFile = (new Template($this->getTemplateFromFile($this->paramsTemplate)))
            ->place('testsDir', $testsDirectory)
            ->place('dataDir', self::DIRECTORY_DATA)
            ->produce();
        $this->createFile(
            $testsDirectory . DIRECTORY_SEPARATOR . self::DIRECTORY_CONFIG . DIRECTORY_SEPARATOR . 'params.php',
            $paramsFile
        );
        $this->sayInfo("Created {$this->paramsTemplate} inside $testsDirectory/" . self::DIRECTORY_CONFIG . " directory");

        $this->createHelper(
            'Acceptance',
            $testsDirectory . DIRECTORY_SEPARATOR . self::DIRECTORY_SUPPORT
        );

        $this->createActor(
            'AcceptanceTester',
            $testsDirectory . DIRECTORY_SEPARATOR . self::DIRECTORY_SUPPORT,
            Yaml::parse($this->getTemplateFromFile($this->acceptanceConfigTemplate))
        );

        $this->printGoodByeMessage($acceptanceDirectoryPath);
    }

    private function getTemplateFromFile($fileName)
    {
        $filePath = implode(
            DIRECTORY_SEPARATOR,
            [
            dirname(realpath(__FILE__)),
            $this->getShortClassName(self::class),
            $fileName
            ]
        );

        return file_get_contents($filePath);
    }

    /**
     * @return string
     */
    private function askForBrowser()
    {
        $browser = $this->ask("Select a browser for testing", ['firefox', 'chrome', 'phantomjs']);

        if ($browser === 'phantomjs') {
            $this->sayInfo("Ensure that you have Phantomjs running before starting tests");
        }
        if ($browser === 'chrome') {
            $this->sayInfo("Ensure that you have Selenium Server and ChromeDriver installed before running tests");
        }
        if ($browser === 'firefox') {
            $this->sayInfo("Ensure that you have Selenium Server and GeckoDriver installed before running tests");
        }

        return $browser;
    }

    /**
     * @param string $testsDirectory
     */
    private function createStructureDirectories(string $testsDirectory): void
    {
        $structureDirectories = [
            self::DIRECTORY_OUTPUT,
            self::DIRECTORY_DATA,
            self::DIRECTORY_SUPPORT,
            self::DIRECTORY_GENERATED,
            self::DIRECTORY_ACCEPTANCE,
            self::DIRECTORY_CONFIG,
            self::DIRECTORY_MODULES,
            self::DIRECTORY_PAGE_OBJECTS
        ];

        foreach ($structureDirectories as $directoryToCreate) {
            $this->createEmptyDirectory($testsDirectory . DIRECTORY_SEPARATOR . $directoryToCreate);
        }

        $this->sayInfo("Created test directories inside at $testsDirectory");
    }

    private function printHelloMessage(): void
    {
        $this->say("Let's prepare Codeception for module acceptance testing");
        $this->say("Create your tests and run them in real browser");
        $this->say("");
    }

    /**
     * @param string $acceptanceDirectoryPath
     */
    private function printGoodByeMessage(string $acceptanceDirectoryPath): void
    {
        $this->say();
        $this->saySuccess("INSTALLATION COMPLETE");

        $this->say();
        $this->say("<bold>Next steps:</bold>");
        $this->say('1. Launch Selenium Server or PhantomJS and webserver');
        $this->say("2. Edit <bold>$acceptanceDirectoryPath/LoginCest.php</bold> to test login of your application");
        $this->say("3. Run tests using: <comment>PARTIAL_MODULE_PATHS=<vendor_name>/<module_name> RUN_TESTS_FOR_SHOP=0 RUN_TESTS_FOR_MODULES=1 ACTIVATE_ALL_MODULES=1 vendor/bin/runtests-codeception</comment>");
        $this->say();
        $this->say("<bold>Happy testing!</bold>");
    }
}
