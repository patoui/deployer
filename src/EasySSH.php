<?php

declare(strict_types=1);

namespace Deployer;

class EasySSH implements CommandInterface
{
    private $connection;

    public function __construct(
        string $host,
        int $port = 22,
        string $user = 'root',
        string $public_key = null,
        string $private_key = null,
        string $passphrase = null
    ) {
        $this->connection = ssh2_connect($host, $port, ['hostkey' => 'ssh-rsa']);

        if ($this->connection === false) {
            die('SSH Connection Failed'.PHP_EOL);
        }

        $home_directory = rtrim($_ENV['HOME'] ?? $_SERVER['HOME'], '/');

        if (is_null($public_key)) {
            $public_key = "$home_directory/.ssh/id_rsa.pub";
        }

        if (is_null($private_key)) {
            $private_key = "$home_directory/.ssh/id_rsa";
        }

        $params = array_filter([$user, $public_key, $private_key, $passphrase]);

        if (!ssh2_auth_pubkey_file($this->connection, ...$params)) {
            die('Public Key Authentication Failed'.PHP_EOL);
        }

        echo 'Public Key Authentication Successful'.PHP_EOL;
    }

    public function runCommand(string $command, $verbose = true) : string
    {
        if ($verbose) {
            echo PHP_EOL . "Running command: $command" . PHP_EOL;
        }

        $stream = ssh2_exec($this->connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        // $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        $output = stream_get_contents($stream_out);
        fclose($stream);

        if ($verbose) {
            echo "OUTPUT:" . PHP_EOL . $output . PHP_EOL;
        }

        return $output;
    }

    public function __destruct()
    {
        if ($this->connection) {
            ssh2_disconnect($this->connection);
        }
    }
}
