<?php

namespace Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Domain\Model\JugadorFactory;
use Domain\Model\JugadorRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalJugadorRepository implements JugadorRepository
{
    private $dbal;
    private $factory;

    function __construct(Connection $dbal, JugadorFactory $factory)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
    }

    public function findByDivision($idDivision)
    {
        $sql = 'SELECT u.* FROM usuarios u '
            . 'INNER JOIN ud on ud.idusuario = u.id '
            . 'INNER JOIN divisiones d on d.id = ud.iddivision WHERE d.id = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idDivision);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return $this->factory->makeAll($data);
    }
}