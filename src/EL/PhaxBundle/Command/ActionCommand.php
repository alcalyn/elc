<?php

namespace EL\PhaxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ActionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('phax:action')
            ->setDescription('Execute Phax action using command line')
            ->addArgument('controller', InputArgument::OPTIONAL, 'Controller name', 'help')
            ->addArgument('action', InputArgument::OPTIONAL, 'Action name', 'default')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller_name    = $input->getArgument('controller');
        $action_name        = $input->getArgument('action');
        $service_name       = 'phax.'.$controller_name;
        $params             = array();
        
        $params['phax_metadata'] = array(
            'mode_cli'      => true,
        );
        
        $phax_reaction = $this
                ->getContainer()
                ->get($service_name)
                ->{$action_name.'Action'}($params)
        ;
        
        if ($phax_reaction->hasMetaMessage()) {
            $output->writeln($phax_reaction->getMetaMessage());
        } else {
            $output->writeln(json_encode($phax_reaction->getJsonData()));
        }
    }
}
