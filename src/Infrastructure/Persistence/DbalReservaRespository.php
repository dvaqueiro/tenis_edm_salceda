<?php

namespace Infrastructure\Persistence;

use DateTime;
use Doctrine\DBAL\Driver\Connection;
use Domain\Model\Reserva;
use Domain\Model\ReservaFactory;
use Domain\Model\ReservaRespository;
use PDO;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalReservaRespository implements ReservaRespository
{
    private $dbal;
    private $factory;

    function __construct(Connection $dbal, ReservaFactory $factory)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
    }

    public function findByPistaYFecha($pista, DateTime $fecha)
    {
        $sql = 'SELECT *
                FROM pabellon
                where fecha = ? and pista = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $fecha->format('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(2, $pista, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }

    public function add(Reserva $newReserva)
    {
        $sql = "INSERT INTO pabellon (idusuario, hora, fecha, pista, aprobado) VALUES (?,?,?,?,?)";
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $newReserva->getIdJugador());
        $stmt->bindValue(2, $newReserva->getHora());
        $stmt->bindValue(3, $newReserva->getFecha()->format('Y-m-d'));
        $stmt->bindValue(4, $newReserva->getPista());
        $stmt->bindValue(5, $newReserva->getAprobado());
        $stmt->execute();

        $id = $this->dbal->lastInsertId();
        $newReserva->setId($id);

        return $newReserva;
    }

    public function findById($reservaId)
    {
        $sql = 'SELECT *
                FROM pabellon p
                where id = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $reservaId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch();

        return $this->factory->make($data);
    }
}