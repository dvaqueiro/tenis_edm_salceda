<?php

namespace Infrastructure\Commands;

use Domain\Model\JugadorRepository;
use Infrastructure\Commands\CustonContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author <dvaqueiro at boardfy dot com>
 */
class FixUsersCommand extends CustonContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fixusers')
            ->setDescription('Fix user data from old system to new system.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $dbal \Doctrine\DBAL\Connection */
        $dbal = $this->app['db'];
        $statement = $dbal->executeQuery('SELECT id, contrasena FROM usuarios u');
        $resultset = $statement->fetchAll();

        foreach ($resultset as $value) {
            $params = [password_hash($value['contrasena'], PASSWORD_BCRYPT, ['cost' => 13]), $value['id']];

            $ok = $dbal->executeUpdate('UPDATE usuarios set password = ? WHERE id = ?',
                $params,
                [\PDO::PARAM_STR, \PDO::PARAM_INT]);

            \Symfony\Component\VarDumper\VarDumper::dump([$params, $ok]);
        }
    }
}