<?php

namespace Infrastructure\Model\Resultado;

use Doctrine\DBAL\Connection;
use Domain\Model\Resultado\Resultado;
use Domain\Model\Resultado\ResultadoFactory;
use Domain\Model\Resultado\ResultadoRepository;

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

    public function add(Resultado $resultado)
    {
        $sql = "INSERT INTO resultados (idu1, idu2, division, j11, j12, j21, j22, j31, j32, ganador) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $resultado->getIdJugadorLocal());
        $stmt->bindValue(2, $resultado->getIdJugadorVisitante());
        $stmt->bindValue(3, $resultado->getIdDivision());
        $stmt->bindValue(4, $resultado->getSet(0)->getJuegosLocal());
        $stmt->bindValue(5, $resultado->getSet(0)->getJuegosVisitante());
        $stmt->bindValue(6, $resultado->getSet(1)->getJuegosLocal());
        $stmt->bindValue(7, $resultado->getSet(1)->getJuegosVisitante());
        $stmt->bindValue(8, $resultado->getSet(2)->getJuegosLocal());
        $stmt->bindValue(9, $resultado->getSet(2)->getJuegosVisitante());
        $stmt->bindValue(10, $resultado->getGanador());
        $stmt->execute();

        $id = $this->dbal->lastInsertId();
        $resultado->setIdResultado($id);

        return $resultado;
    }
}