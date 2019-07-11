<?php

declare(strict_types=1);

namespace Deployer;

interface CommandInterface {
    public function runCommand(string $command, $verbose = true) : string;
}
