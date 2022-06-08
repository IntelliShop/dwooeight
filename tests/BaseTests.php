<?php

namespace Dwoo\Tests {

    use Dwoo\Compiler;
    use Dwoo\Core;
    use PHPUnit\Framework\Constraint\Constraint;
    use PHPUnit\Framework\TestCase;

    /**
     * Class BaseTests
     *
     * @package Dwoo\Tests
     */
    abstract class BaseTests extends TestCase
    {

        protected $compileDir;
        protected $cacheDir;
        protected $compiler;
        protected $dwoo;

        /**
         * Constructs a test case with the given name.
         *
         * @param string $name
         * @param array $data
         * @param string $dataName
         */
        public function __construct($name = null, array $data = array(), $dataName = '')
        {
            $this->compileDir = __DIR__ . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'cache';
            $this->cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'compiled';

            // extend this class and override this in your constructor to test a modded compiler
            $this->compiler = new Compiler();
            $this->dwoo = new Core($this->compileDir, $this->cacheDir);

            parent::__construct($name, $data, $dataName);
        }

        /**
         * Clear cache and compiled directories
         */
        public function __destruct()
        {
            $this->clearDir($this->cacheDir, true);
            $this->clearDir($this->compileDir, true);
        }

        /**
         * @param string $path
         * @param bool $emptyOnly
         */
        protected function clearDir($path, $emptyOnly = false)
        {
            if ($path !== null) {
                if (is_dir($path)) {
                    foreach (glob($path . '/*') as $f) {
                        $this->clearDir($f);
                    }
                    if (!$emptyOnly) {
                        rmdir($path);
                    }
                } elseif (is_file($path)) {
                    unlink($path);
                }
            }
        }
    }

    // Evaluates two strings and ignores differences in line endings (\r\n == \n == \r)
    class DwooConstraintStringEquals extends Constraint
    {
        protected $target;

        public function __construct($target)
        {
            $this->target = preg_replace('#(\r\n|\r)#', "\n", $target);
        }

        public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
        {
            $this->other = preg_replace('#(\r\n|\r)#', "\n", $other);

            return $this->target == $this->other;
        }

        public function toString(): string
        {
            return 'equals expected value.' . PHP_EOL . 'Expected:' . PHP_EOL . $this->target . PHP_EOL . 'Received:' . PHP_EOL . $this->other . PHP_EOL;
        }
    }

    class DwooConstraintPathEquals extends Constraint
    {
        protected $target;

        public function __construct($target)
        {
            $this->target = preg_replace('#([\\\\/]{1,2})#', '/', $target);
        }

        public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
        {
            $this->other = preg_replace('#([\\\\/]{1,2})#', '/', $other);

            return $this->target == $this->other;
        }

        public function toString(): string
        {
            return 'equals expected value.' . PHP_EOL . 'Expected:' . PHP_EOL . $this->target . PHP_EOL . 'Received:' . PHP_EOL . $this->other . PHP_EOL;
        }
    }
}