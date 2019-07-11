## Deployer 🚀

A simple package to connect via SSH and run commands, built with PHP 💙

## ⚠️ WARNING ⚠️

**This repository is in active development, use at your own risk.**

### Requirements

- PHP >=7.2
- ssh2 (install with `pecl install ssh2-1.1.2` latest version at the time of writing)

### Installation

Run composer require

```
composer require patoui/deployer
```

### Usage

```php
$ssh = new Deployer\EasySSH('127.0.0.1', 22);
(new Deployer\LaravelDeployer($ssh, '/var/www/example.ca'))
    ->deploy();
```

Behind the scenes the above runs a few commands

```bash
cd /var/www/example.ca
git pull origin master
composer install --no-dev
sudo service php7.2 reload
sudo service nginx reload
```
 
### Advance Usage

```php
$ssh = new Deployer\EasySSH('127.0.0.1', 22); // connect to SSH
(new Deployer\LaravelDeployer($ssh, '/var/www/example.ca')) // pass SSH connection and project directory on remote
    ->setPhpVersion('7.3')      // set the php version
    ->setWebServer('nginx')     // set the web server
    ->shouldCompileAssets()     // tell the deployer to compile assets
    ->setPackageManager('yarn') // tell the deployer which package manager to use
    ->deploy();                 // execute deployment
```

The above will run:

```bash
cd /var/www/example.ca
git pull origin master
composer install --no-dev
yarn
sudo service php7.3 reload
sudo service nginx reload
```
