<?php

declare(strict_types=1);

namespace Deployer;

use Exception;

class LaravelDeployer extends Deployer
{
    private $project_directory;
    private $php_version;
    private $package_manager;
    private $web_server;
    private $should_compile_assets = false;

    public function __construct(
        CommandInterface $ssh,
        string $project_directory,
        string $php_version = '7.2',
        string $web_server = null,
        string $package_manager = null
    ) {
        parent::__construct($ssh);

        $this->project_directory = $project_directory;

        $this->checkPhpVersion($php_version);
        $this->php_version = $php_version;

        $package_manager = $package_manager ?? 'npm';
        $this->checkPackageManager($package_manager);
        $this->package_manager = $package_manager;

        $web_server = $web_server ?? 'nginx';
        $this->checkWebServer($web_server);
        $this->web_server = $web_server;
    }

    public function getPhpVersion() : string
    {
        return $this->php_version;
    }

    public function setPhpVersion(string $version) : void
    {
        $this->php_version = $version;
    }

    public function getShouldCompileAssets() : bool
    {
        return $this->should_compile_assets;
    }

    public function setShouldCompileAssets(bool $shouldCompileAssets) : void
    {
        $this->should_compile_assets = $shouldCompileAssets;
    }

    public function shouldCompileAssets() : self
    {
        $this->should_compile_assets = true;

        return $this;
    }

    public function setPackageManager($package_manager) : self
    {
        $this->checkPackageManager($package_manager);

        $this->package_manager = $package_manager;

        return $this;
    }

    public function setWebServer($web_server) : self
    {
        $this->checkWebServer($web_server);
        $this->web_server = $web_server;

        return $this;
    }

    public function deploy() : bool
    {
        $commands = [
            "cd {$this->project_directory}",
            'git pull origin master',
            'composer install --no-dev',
        ];

        if ($this->should_compile_assets) {
            $commands[] = $this->package_manager;
        }

        $commands[] = "sudo service php{$this->php_version}-fpm reload";
        $commands[] = "sudo service {$this->web_server} reload";

        $this->ssh->runCommand(implode(' && ', $commands), true);

        return true;
    }

    private function checkPhpVersion(string $version) : void
    {
        if (!in_array($version, ['7.0', '7.1', '7.2', '7.3', '7.4'])) {
            throw new Exception('Invalid php version, must be one of: 7.0, 7.1, 7.2, 7.3, 7.4');
        }

        echo 'PHP version is valid'.PHP_EOL;
    }

    private function checkWebServer(string $web_server) : void
    {
        if (!in_array($web_server, ['nginx', 'apache'])) {
            throw new Exception("Invalid package manager, must be 'nginx' or 'apache'");
        }

        echo 'Web server is valid'.PHP_EOL;
    }

    private function checkPackageManager(string $package_manager) : void
    {
        if (!in_array($package_manager, ['npm', 'yarn'])) {
            throw new Exception("Invalid package manager, must be 'npm' or 'yarn'");
        }

        echo 'Package manager is valid'.PHP_EOL;
    }
}
