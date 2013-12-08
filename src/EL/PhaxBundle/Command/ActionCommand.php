<?php

namespace EL\PhaxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Phax command line.
 * 
 * You can call your phax controller here,
 * using command :
 * 
 *      phax:action controller action -p key:value -p key2:value2
 * 
 * If your controller returns a metaMessage,
 * this message will be displayed in console.
 * Else a json string will be displayed.
 */
class ActionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('phax:action')
            ->setDescription('Execute Phax action using command line')
            ->addArgument('controller', InputArgument::OPTIONAL, 'Controller name', 'help')
            ->addArgument('action', InputArgument::OPTIONAL, 'Action name', 'default')
            ->addOption('parameters', 'p', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Parameters to send to the controller')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller_name    = $input->getArgument('controller');
        $action_name        = $input->getArgument('action');
        $params             = array();
        
        foreach ($input->getOption('parameters') as $value) {
            $tokens = explode(':', $value);
            
            if (count($tokens) === 1) {
                $key = $tokens[0];
                
                if (strlen($key) === 0) {
                    continue;
                }
                
                $params[$key] = true;
            }
            
            if (count($tokens) === 2) {
                $key    = $tokens[0];
                $value  = $tokens[1];
                
                if (0 === (strlen($key) * strlen($value))) {
                    continue;
                }
                
                $params[$tokens[0]] = $tokens[1];
            }
        }
        
        $params['phax_metadata'] = array(
            'mode_cli'      => true,
        );
        
        $phax_reaction = $this
                ->getContainer()
                ->get('phax_core')
                ->action($controller_name, $action_name, $params)
        ;
        
        if ($phax_reaction->hasMetaMessage()) {
            $output->writeln($phax_reaction->getMetaMessage());
        } else {
            $output->writeln(json_encode($phax_reaction->getJsonData()));
        }
    }
}
