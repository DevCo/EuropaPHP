<?php

namespace Controller;
use Europa\App\App;
use Europa\Controller\ControllerAbstract;
use Europa\Fs\Finder as FsFinder;
use Testes\Coverage\Coverage;
use Testes\Finder\Finder;
use Testes\Suite\Suite;
use Testes\Event\Test as Event;

class Test extends ControllerAbstract
{
    const PADDING = 88;

    public $methodTimer = [];

    public $testTime = 0;

    public $totalTime = 0;

    public function cli()
    {
        return $this->forward('all');
    }

    public function get()
    {
        return $this->forward('all');
    }

    /**
     * Runs all unit tests.
     *
     * @param string $test     The test suite to run. Defaults to "Test".
     * @param bool   $untested Whether or not to display untested LOC.
     * @param string $analyze  The path, relative to the base path, to analyze. If not specified, all modules are analyzed.
     *
     * @return array
     */
    public function all($test = null, $untested = false, $analyze = null)
    {
        $suite  = new Suite;
        $cover  = new Coverage;
        $finder = new FsFinder;

        $finder->is('/\.php$/');
        $finder->in(__DIR__ . '/../../../' . $analyze);
        $cover->start();

        foreach (App::get() as $name => $module) {
            $path  = __DIR__ . '/../../../';
            $path .= $name . '/';
            $path .= App::get()->modules['tests']['path'];

            $suite->addTests(new Finder($path, $test));
        }

        $suite->run($this->getTestEvent());

        $analyzer = $cover->stop();

        foreach ($finder as $file) {
            $analyzer->addFile($file->getRealpath());
        }
        
        return [
            'percent'  => round(number_format($analyzer->getPercentTested(), 2), 2),
            'suite'    => $suite,
            'report'   => $analyzer,
            'untested' => $untested
        ];
    }

    private function getTestEvent() {
        $event = new Event;
        $runner = $this;

        $event->on('preRun', function($test) {
            echo 'Running Tests for: ' . get_class($test) . PHP_EOL;
        });

        $event->on('preMethod', function ($method, $test) use ($runner) {
            $runner->methodTime[$method]['start'] = microtime(true);
        });

        $event->on('postMethod', function ($method, $test) use ($runner) {
            $runner->methodTime[$method]['stop'] = microtime(true);

            $className = get_class($test);

            $start = $runner->methodTime[$method]['start'];
            $stop = $runner->methodTime[$method]['stop'];
            $time = $stop - $start;
            $runner->testTime += $time;
            $number = (string) number_format($time, 3);

            echo "\033[" .($test->isMethodPassed($method) ? '42m[PASS]' : "41m[FAIL]") . "\033[0m" .
                str_pad(' ' . $className . '::' . $method . ' ', self::PADDING - strlen($number)) .
                $number . PHP_EOL;
        });

        $event->on('postRun', function($test) use ($runner) {
            $number = number_format($runner->testTime, 3);
            $runner->totalTime += $runner->testTime;
            $runner->testTime = 0;

            echo str_pad('Total for ' . get_class($test) . ': ' , self::PADDING + 6 - strlen($number), ' ', STR_PAD_LEFT) .
                "\033[1m" . $number . "\033[0m" . PHP_EOL;
        });

        return $event;
    }
}