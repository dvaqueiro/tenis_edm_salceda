<?php

namespace Infrastructure\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
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
        $sql = 'SELECT p.*, u.nombre
                FROM pabellon p
                INNER JOIN usuarios u on u.id = p.idusuario
                where p.fecha = ? and p.pista = ? ORDER BY p.hora ASC';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $fecha->format('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(2, $pista, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }

    public function add(Reserva $newReserva)
    {
        $sql = "INSERT INTO pabellon (idusuario, hora, fecha, pista, aprobado, token) VALUES (?,?,?,?,?,?)";
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $newReserva->getIdJugador());
        $stmt->bindValue(2, $newReserva->getHora());
        $stmt->bindValue(3, $newReserva->getFecha()->format('Y-m-d'));
        $stmt->bindValue(4, $newReserva->getPista());
        $stmt->bindValue(5, $newReserva->getAprobado());
        $stmt->bindValue(6, $newReserva->getToken());
        $stmt->execute();

        $id = $this->dbal->lastInsertId();
        $newReserva->setId($id);

        return $newReserva;
    }

    public function findById($reservaId)
    {
        $sql = 'SELECT p.*, u.nombre
                FROM pabellon p
                INNER JOIN usuarios u on u.id = p.idusuario
                where p.id = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $reservaId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch();

        return $this->factory->make($data);
    }

    public function update(Reserva $reserva)
    {
        $sql = "UPDATE pabellon set idusuario=?, hora=?, fecha=?, pista=?, aprobado=? WHERE id = ?";
        return $this->dbal->executeUpdate($sql, [
            $reserva->getIdJugador(),
            $reserva->getHora(),
            $reserva->getFecha()->format('Y-m-d'),
            $reserva->getPista(),
            $reserva->getAprobado(),
            $reserva->getId()
        ]);
    }

    public function findAll($limit, $orderBy)
    {
        $first = true;
        $sql = 'SELECT p.*, u.nombre
                FROM pabellon p
                INNER JOIN usuarios u on u.id = p.idusuario';
        foreach ($orderBy as $key => $value) {
            if($first) {
                $sql .= " ORDER BY {$key} {$value}";
                $first = false;
            } else {
                $sql .= ",{$key} {$value}";
            }
        }
        if($limit) {
            $sql .= " LIMIT {$limit}";
        }
        $stmt = $this->dbal->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }
}