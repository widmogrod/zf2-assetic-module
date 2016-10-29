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
        $config = $this->assetic->getConfiguration();
        $mode   = (null !== ($mode = $config->getUmask())) ? $mode : 0775;

        if (!$this->createPath($output, 'Cache', $config->getCachePath(), $mode)) {
            return 1;
        }
        if (!$this->createPath($output, 'Web', $config->getWebPath(), $mode)) {
            return 1;
        }

        return 0;
    }

    /**
     * Creates a path with the needed permissions
     *
     * @param OutputInterface $output The output object
     * @param string          $which  Which path?
     * @param string          $path   The path
     * @param int             $mode   The permissions
     *
     * @return bool                   Success
     */
    private function createPath(OutputInterface $output, $which, $path, $mode)
    {
        $displayMode = decoct($mode);
        $pathExists  = is_dir($path);
        if (!$path) {
            $output->writeln('Creation of ' . $which . ' path skipped - no path provided');

            return true;
        }
        if (!$pathExists) {
            if (mkdir($path, $mode, true)) {
                $output->writeln($which . ' path created "' . $path . '" with mode "' . $displayMode . '"');

                return true;
            } else {
                $output->writeln('<error>' . $which . ' path "' . $path . '" could not be created.</error>');

                return false;
            }
        }

        $readable = is_readable($path);
        $writable = is_writable($path);
        if ($readable && $writable) {
            $output->writeln(
                'Creation of ' . $which . ' path "' . $path . '" skipped - path exists with correct permissions'
            );

            return true;
        } elseif (!$readable && !$writable) {
            $output->writeln(
                '<error>Creation of ' . $which . ' path "' . $path . '" failed - path exists but is neither readable nor writable</error>'
            );
        } elseif (!$readable) {
            $output->writeln(
                '<error>Creation of ' . $which . ' path "' . $path . '" failed - path exists but is not readable</error>'
            );
        } elseif (!$writable) {
            $output->writeln(
                '<error>Creation of ' . $which . ' path "' . $path . '" failed - path exists but is not writable</error>'
            );
        }

        return false;
    }
}
