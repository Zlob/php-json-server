#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JsonServer\DataBase;
use JsonServer\Config;
use Faker\Factory;

$console = new Application();

$console
    ->register('faker')
    ->setDefinition(array(
        new InputArgument('table', InputArgument::REQUIRED, 'Table name'),
        new InputArgument('columns', InputArgument::REQUIRED, 'columns names separated with a space with faker type in dot ("name.name birthday.dateTime")'),
        new InputOption('num', null, InputOption::VALUE_REQUIRED, 'Number of rows in table', 1)
    ))
    ->setDescription('Fill table with fake data')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $num = $input->getOption('num');
        $columns = explode(" ",$input->getArgument('columns'));
        $faker = Faker\Factory::create();
        $db = new DataBase(__DIR__ . Config::get('pathToDb'));
        $table = $db->{$input->getArgument('table')};
        for($i = 1; $i <= $num; $i++){
            $data = [];
            foreach($columns as $column){
                error_reporting(E_ERROR | E_PARSE);
                list($name, $type, $param) = explode(".", $column);
                if($param){
                    $data[$name] = $faker->$type($param);
                }
                else{
                    $data[$name] = $faker->$type();
                }
            }
            $table->insert($data);
        }
        $db->save();
    })
;

$console->run();

exit(0);
