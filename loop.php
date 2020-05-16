#!/usr/bin/php
<?php

class Loop
{
    private $commandFilePath;

    public function __construct()
    {
        chdir(__DIR__);
        exec('sudo ls');

        print "OK\n";

        $this->commandFilePath = 'command';
    }

    public function run()
    {
        $commandFileMTime = filemtime($this->commandFilePath);

        while (true) {
            clearstatcache(true, $this->commandFilePath);

            if ($commandFileMTime != filemtime($this->commandFilePath)) {
                $commandFileMTime = filemtime($this->commandFilePath);

                $command = trim(file_get_contents($this->commandFilePath));

                if ($command == 'disable') {
                    $this->disable();
                }

                if ($command == 'enable') {
                    $this->enable();
                }
            }

            sleep(1);
        }
    }

    private function disable()
    {
        exec('sudo cp data/disabled.ini /etc/php/7.0/mods-available/xdebug.ini');
        exec('sudo cp data/disabled.ini /etc/php/7.3/mods-available/xdebug.ini');

        $this->restartServices();

        print date('d.m.Y H:i:s') . ' disabled' . PHP_EOL;
    }

    private function enable()
    {
        exec('sudo cp data/enabled.ini /etc/php/7.0/mods-available/xdebug.ini');
        exec('sudo cp data/enabled.ini /etc/php/7.3/mods-available/xdebug.ini');

        $this->restartServices();

        print date('d.m.Y H:i:s') . ' enabled' . PHP_EOL;
    }

    private function restartServices()
    {
        exec('sudo systemctl restart apache2');
        exec('sudo systemctl restart php7.3-fpm');
    }
}

$loop = new Loop();

$loop->run();
