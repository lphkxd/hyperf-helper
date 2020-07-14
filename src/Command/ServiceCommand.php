<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Mzh\Helper\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Devtool\Generator\GeneratorCommand;
use Hyperf\Utils\CodeGen\Project;

/**
 * @Command
 */
class ServiceCommand extends GeneratorCommand
{

    public function __construct()
    {
        parent::__construct('gen:service');
        $this->setDescription('Create a new service class');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        $stub = str_replace('%CLASS%', $class, $stub);
        return $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $project = new Project();
        return BASE_PATH . '/' . $project->path($name.'Service');
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/service.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Service';
    }
}