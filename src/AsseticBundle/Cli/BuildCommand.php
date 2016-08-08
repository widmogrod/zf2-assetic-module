<?php

namespace AsseticBundle\Cli;

use AsseticBundle\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    /**
     * The assetic service
     *
     * @var Service
     */
    private $assetic;

    /**
     * Constructor.
     *
     * @param Service $assetic
     */
    public function __construct(Service $assetic)
    {
        parent::__construct('build');
        $this->assetic = $assetic;
        $this->setDescription('Build all assets.');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->assetic->getConfiguration();
        $config->setBuildOnRequest(true);
        $this->assetic->build();
        $this->assetic->getAssetWriter()->writeManagerAssets($this->assetic->getAssetManager());
    }
}
