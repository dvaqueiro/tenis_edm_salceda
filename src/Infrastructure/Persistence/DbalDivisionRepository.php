<?php

namespace Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Domain\Model\DivisionFactory;
use Domain\Model\DivisionRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalDivisionRepository implements DivisionRepository
{
    private $dbal;
    private $factory;

    function __construct(Connection $dbal, DivisionFactory $factory)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
    }

    public function findByLiga($idLiga)
    {
        $sql = 'SELECT * FROM divisiones WHERE idliga = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idLiga);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->factory->makeAll($data);
    }

    public function findByLigaAndJugador($idLiga, $idJugador)
    {
        $sql = 'SELECT d.*
                FROM divisiones d
                INNER JOIN ud ON ud.`iddivision` = d.`id`
                INNER JOIN ligas l on l.id = d.idliga
                WHERE d.`idliga` = ? AND ud.`idusuario` = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idLiga);
        $stmt->bindValue(2, $idJugador);
        $stmt->execute();
        $data = $stmt->fetch();
        return $this->factory->make($data);
    }
}