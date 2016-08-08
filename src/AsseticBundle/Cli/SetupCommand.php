<?php

namespace AsseticBundle\Cli;

use AsseticBundle\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
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
        parent::__construct('setup');
        $this->assetic = $assetic;
        $this->setDescription('Create cache and assets directories with valid permissions.');
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
        $config      = $this->assetic->getConfiguration();
        $mode        = (null !== ($mode = $config->getUmask())) ? $mode : 0775;
        $displayMode = decoct($mode);

        $cachePath   = $config->getCachePath();
        $pathExists  = is_dir($cachePath);
        if ($cachePath && !$pathExists) {
            mkdir($cachePath, $mode, true);
            $output->writeln('Cache path created "' . $cachePath . '" with mode "' . $displayMode . '"');
        } elseif ($pathExists) {
            $output->writeln('Creation of cache path "' . $cachePath . '" skipped - path exists');
        } else {
            $output->writeln('Creation of cache path "' . $cachePath . '" skipped - no path provided');
        }

        $webPath    = $config->getWebPath();
        $pathExists = is_dir($webPath);
        if ($webPath && !$pathExists) {
            mkdir($webPath, $mode, true);
            $output->writeln('Web path created "' . $webPath . '" with mode "' . $displayMode . '"');
        } elseif ($pathExists) {
            $output->writeln('Creation of web path "' . $webPath . '" skipped - path exists');
        } else {
            $output->writeln('Creation of web path "' . $webPath . '" skipped - no path provided');
        }
    }
}
