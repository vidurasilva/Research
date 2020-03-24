<?php
/**
 * Created by PhpStorm.
 * User: Matthijs Overboom
 * Date: 2-1-17
 * Time: 15:46
 */

namespace AppBundle\Command;


use FOS\OAuthServerBundle\Model\ClientManager;
use OAuth2\OAuth2;
use RandomLib\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class AddClientCommand extends ContainerAwareCommand
{
	/**
	 * @var Generator
	 */
	private $generator;

	public function initialize(InputInterface $input, OutputInterface $output)
	{
		$factory = new \RandomLib\Factory();
		$this->generator = $factory->getGenerator(new \SecurityLib\Strength(\SecurityLib\Strength::MEDIUM));
	}

	protected function configure()
	{
		$this
			->setName('app:add:client')
			->setDescription('Add a new clientId and clientSecret');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->write('Generating new clientId and secret', true);
		$clientId = $this->generateClientId();
		$clientSecret = $this->generateClientSecret(25);
		$this->addClient($clientId, $clientSecret);
		$output->writeln('<info>Done, the new clientId is ' . $clientId . ' and the clientSecret is ' . $clientSecret . ' </info>');
	}

	/**
	 * Generate a new clientId
	 *
	 * @return int
	 */
	private function generateClientId()
	{
		$clientId =$this->generator->generateInt(1, 99999999);
		$idIsFree = $this->idIsFree($clientId);

		while(!$idIsFree) {
			$clientId = rand(1, 99999999);
			$idIsFree = $this->idIsFree($clientId);
		}
		return $clientId;
	}

	/**
	 * Generate new clientSecret
	 *
	 * @param $length
	 * @return string
	 */
	private function generateClientSecret($length)
	{
		return $this->generator->generateString($length);
	}

	/**
	 * Check if generated clientId is free
	 *
	 * @param $clientId
	 * @return bool
	 */
	private function idIsFree($clientId)
	{
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$query = $em->getConnection()->prepare('SELECT COUNT(*) FROM client WHERE random_id = :id');
		$query->execute(['id' => $clientId]);
		return $query->fetchAll(\PDO::FETCH_COLUMN)[0] == 0;
	}

	/**
	 * Update clientId and clientSecret in database
	 *
	 * @param $clientId
	 * @param $clientSecret
	 */
	private function addClient($clientId, $clientSecret)
	{
		$grantTypes = [
			OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
			OAuth2::GRANT_TYPE_REFRESH_TOKEN,
			OAuth2::GRANT_TYPE_USER_CREDENTIALS
		];

		/** @var ClientManager $clientManager */
		$clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
		$client = $clientManager->createClient();

		$client->setAllowedGrantTypes($grantTypes);
		$client->setRandomId($clientId);
		$client->setSecret($clientSecret);

		$clientManager->updateClient($client);
	}
}