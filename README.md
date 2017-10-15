CommandLockableTrait [![version](http://img.shields.io/badge/release-v1.0.0-brightgreen.svg?style=flat)](https://github.com/DevCreel/MicroBench/archive/master.zip)
======

Symfony console command lockable trait (very simple emulation of multithreading)

Installation
------------

### Use ###

Add this to composer.json

```json
{
    "require": {
        "devcreel/command-lockable-trait": "1.0.*-dev"
    }
}
```

Usage
-----

```php
<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DevCreel\Command\LockableTrait;

class TestCommand extends ContainerAwareCommand
{
    use CommandLockableTrait;
    
    //count of threads
    private $threadsCount = 5;
    
    protected function configure()
    {
        $this->setName('test:run');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //check for free thread
        if (!$this->lock()) {
            $output->writeln('[' . $this->getName() . '] is already running in another process.');
            return 0;
        }
        
        //your code...
        
        //release thread
        $this->release();
    }
    
}

```

License
-------
CommandLockableTrait is licensed under the MIT License