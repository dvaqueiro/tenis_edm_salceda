<?php

namespace Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Domain\Model\LigaFactory;
use Domain\Model\LigaRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalLigaRepository implements LigaRepository
{
    private $dbal;
    private $factory;

    function __construct(Connection $dbal, LigaFactory $factory)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
    }

    public function findByIdOrLast($idLiga)
    {
        if($idLiga) {
            $sql = 'SELECT * FROM ligas WHERE id = ?';
            $stmt = $this->dbal->prepare($sql);
            $stmt->bindValue(1, $idLiga);
        } else {
            $sql = 'SELECT * FROM ligas ORDER BY id DESC LIMIT 1';
            $stmt = $this->dbal->prepare($sql);
        }
        $stmt->execute();

        $data = $stmt->fetch();

        return $this->factory->make($data);
    }

    public function findLastLimit($limit)
    {
        $sql = 'SELECT * FROM ligas ORDER BY id DESC LIMIT ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }
}