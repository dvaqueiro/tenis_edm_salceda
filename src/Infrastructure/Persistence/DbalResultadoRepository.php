<?php

namespace Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Domain\Model\ResultadoFactory;
use Domain\Model\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalResultadoRepository implements ResultadoRepository
{
    private $dbal;
    private $factory;

    function __construct(Connection $dbal, ResultadoFactory $factory)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
    }

    public function findByDivision($idDivision)
    {
        $sql = 'SELECT r.* , ul.nombre AS nombre_local, uv.nombre AS nombre_visitante
                FROM resultados r 
                INNER JOIN usuarios ul ON ul.`id` = r.idu1
                INNER JOIN usuarios uv ON uv.`id` = r.idu2
                WHERE division = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idDivision);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->factory->makeAll($data);
    }

    public function findByLigaAndJugador($idLiga, $idJugador)
    {
        $sql = 'SELECT r.* , ul.nombre AS nombre_local, uv.nombre AS nombre_visitante
                FROM divisiones d
                INNER JOIN ud ON ud.`iddivision` = d.`id`
                INNER JOIN resultados r ON r.`division` = d.id
                INNER JOIN usuarios ul ON ul.`id` = r.idu1
                INNER JOIN usuarios uv ON uv.`id` = r.idu2
                WHERE d.`idliga` = ? AND ud.`idusuario` = ?
                AND (r.`idu1` = ? OR r.`idu2` = ?);';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idLiga);
        $stmt->bindValue(2, $idJugador);
        $stmt->bindValue(3, $idJugador);
        $stmt->bindValue(4, $idJugador);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->factory->makeAll($data);
    }
}