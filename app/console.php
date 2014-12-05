<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$app = new Application('My Application', 'n/a');

$app->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));

$app->register('hello')->setDefinition([
        new InputArgument('name', InputArgument::OPTIONAL, 'Your name.', 'World'),
    ])
    ->setDescription('Say hello!')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $output->writeln(sprintf('Hello <info>%s</info>!', $input->getArgument('name')));
    });

return $app;
