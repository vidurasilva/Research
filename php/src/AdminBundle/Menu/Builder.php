<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 20/10/16
 * Time: 17:23
 */

namespace AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class Builder
 * @package AdminBundle\Menu
 */
class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'main-menu',
                'id'    => 'main-menu'
            ]
        ]);

        //Dashboard
        $menu->addChild('Dashboard', array('route' => 'dashboard'))->setAttribute('icon', 'fa fa-home fa-3');

        //Users
        $menu->addChild('Users', array('route' => 'user_overview'))->setAttribute('icon', 'fa fa-user fa-3');
        $menu['Users']->addChild('Overview', array('route' => 'user_overview'));

        //Goals
        $menu->addChild('Goals', array('route' => 'goal_overview'))->setAttribute('icon', 'fa fa-dashboard fa-3');
        $menu['Goals']->addChild('Overview', array('route' => 'goal_overview'));
        $menu['Goals']->addChild('Goal categories', array('route' => 'category_overview'));

		//Repetitive Goals
		$menu->addChild('Repetitive Goals', array('route' => 'repetitivegoal_create'))->setAttribute('icon', 'fa fa-repeat fa-3');
		$menu['Repetitive Goals']->addChild('Create', array('route' => 'repetitivegoal_create'));

        //Community
        $menu->addChild('Community', array('route' => 'community_overview'))->setAttribute('icon', 'fa fa-comments-o fa-3');
        $menu['Community']->addChild('Overview', array('route' => 'community_overview'));
        $menu['Community']->addChild('Create community category', array('route' => 'community_create'));

        //Transactions
        $menu->addChild('Transactions', array('route' => 'payment_export'))->setAttribute('icon', 'fa fa-credit-card fa-3');
        $menu['Transactions']->addChild('Export', array('route' => 'payment_export'));

        //Commands
        $menu->addChild('Tasks', array('route' => 'command_overview'))->setAttribute('icon', 'fa fa-play fa-3');
        $menu['Tasks']->addChild('Overview', array('route' => 'command_overview'));

		//Clients
		$menu->addChild('Clients', array('route' => 'client_overview'))->setAttribute('icon', 'fa fa-mobile fa-3');
		$menu['Clients']->addChild('Overview', array('route' => 'client_overview'));

        return $menu;
    }
}