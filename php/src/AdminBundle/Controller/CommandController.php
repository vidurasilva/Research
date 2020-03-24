<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Command controller
 *
 * @Route("/command")
 */
class CommandController extends Controller
{
    protected static $commands = [
        'commands' => [
            [
                'id'      => 0,
                'name'    => 'Find goals to mark as failed or succeeded',
                'command' => 'app:goal:markgoals'
            ],
            [
                'id'      => 1,
                'name'    => 'Handle-failed-goals',
                'command' => 'app:goal:fail'
            ],
            [
                'id'      => 2,
                'name'    => 'Handle checkins-mails supervisor',
                'command' => 'app:mail:supervisor'
            ],
			[
				'id' 	  => 3,
				'name'    => 'Add a new clientId and clientSecret',
				'command' => 'app:add:client'
			],
			[
				'id' 	  => 4,
				'name'    => 'Mark milestones: create checkins for failed tasks',
				'command' => 'app:goal:markmilestones'
			]
        ]
    ];

    /**
     * Command overview
     *
     * @Route("/", name="command_overview")
     * @Method("GET")
     */
    public function overviewAction()
    {
        return $this->render('AdminBundle:Command:overview.html.twig', self::$commands);
    }

    /**
     * Command execution
     * @Route("/execute", name="command_execute")
     * @Method("GET")
     */
    public function executeCommandAction(Request $request)
    {
        $commandId   = $request->query->getInt('id');
        $kernel      = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        if (!isset(self::$commands['commands'][$commandId])) {
            throw new BadRequestHttpException('Command does not exist');
        }

        $command = self::$commands['commands'][$commandId];

        $input = new ArrayInput(array(
            'command' => $command['command'],
            '--env'   => ENVIRONMENT,
        ));
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        return $this->render('AdminBundle:Command:process.html.twig', ['content' => $content]);
    }
}
