<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Command;

use App\V1\AbstractCacheTrait;
use App\V1\BuildCacheTrait;
use App\V1\VersionCacheTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddBuildCommand extends Command {
    use AbstractCacheTrait;
    use BuildCacheTrait;
    use VersionCacheTrait;

    protected static $defaultName = 'app:add-build';

    protected function configure() {
        $this
            ->setDescription('Adds a build')
            ->addArgument('project', InputArgument::REQUIRED, 'Project identifier')
            ->addArgument('version', InputArgument::REQUIRED, 'Version identifier')
            ->addArgument('build', InputArgument::REQUIRED, 'Build number');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $project = $input->getArgument('project');
        $version = $input->getArgument('version');
        $build = $input->getArgument('build');

        $io = new SymfonyStyle($input, $output);
        $io->comment('Adding build ' . $build . ' to version ' . $version . ' in project ' . $project);

        $kernel = $this->getApplication()->getKernel();
        $container = $kernel->getContainer()->getParameterBag();
        $cache = $this->getCache($container);

        $versions = $this->addVersion($container, $cache, $project, $version);
        if(!in_array($version, $versions)) {
            $io->error('Could not add version ' . $version . ' to cache for project ' . $project . '.');
            return;
        }

        $builds = $this->addBuild($container, $cache, $project, $version, $build);
        if(!in_array($build, $builds)) {
            $io->error('Could not add build ' . $build . ' to version ' . $version . ' in project ' . $project . '.');
            return;
        }

        $io->success('Build was successfully added.');
    }
}
