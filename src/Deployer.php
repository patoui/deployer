<?php

declare(strict_types=1);

namespace Deployer;

class Deployer
{
    protected $ssh;
    protected $commands;

    public function __construct(CommandInterface $ssh, array $commands = [])
    {
        $this->ssh = $ssh;
        $this->commands = $commands;
    }

    public function addCommand(string $command) : void
    {
        $this->commands[] = $command;
    }

    public function addCommands(array $commands) : void
    {
        $this->commands = array_merge($this->commands, $commands);
    }

    public function deploy() : bool
    {
        $this->ssh->runCommand(implode(' && ', $this->commands));

        return true;
    }
}
