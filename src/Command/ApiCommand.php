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
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class ApiCommand extends GeneratorCommand
{

    public function __construct()
    {
        parent::__construct('gen:api');
        $this->setDescription('Create a new controller class');
        $this->addArgument('t', InputArgument::OPTIONAL, '控制器名称');
        $this->addArgument('s', InputArgument::OPTIONAL, "对应服务名");
        $this->addArgument('d', InputArgument::OPTIONAL, '控制器介绍');
    }

    protected function getServiceName()
    {
        return str_replace($this->getNamespace($this->getName()) . '\\', '', $this->getName());
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
        $stub = str_replace('%SERVICE%', ucfirst($this->input->getArgument('s') ?? $class) . "Service", $stub);
        $stub = str_replace('%SERVICENAME%', lcfirst($this->input->getArgument('s') ?? $class) . "Service", $stub);
        $stub = str_replace('%TITLE%', $this->input->getArgument('t') ?? $class, $stub);
        $stub = str_replace('%DESC%', $this->input->getArgument('d'), $stub);
        return $stub;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/controller.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Controller';
    }
}