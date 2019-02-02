<?php

namespace App\Command;

use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearCacheCommand extends Command {
    protected static $defaultName = 'app:clear-cache';

    protected function configure() {
        $this
            ->setDescription('Clears the cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->comment('Clearing the cache');

        $kernel = $this->getApplication()->getKernel();
        $cacheDir = $kernel->getContainer()->getParameter('parchment.cache');
        $cache = new FilesystemCache('parchment', 0, $cacheDir);
        if ($cache->clear()) {
            $io->success('Cache was successfully cleared.');
        } else {
            $io->error('Cache could not be cleared.');
        }
    }
}
