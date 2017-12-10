<?php

namespace Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Domain\Model\Jugador;
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
            . 'INNER JOIN divisiones d on d.id = ud.iddivision WHERE d.id = ? '
            . 'ORDER BY u.nombre';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idDivision);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return $this->factory->makeAll($data);
    }

    public function add(Jugador $newJugador)
    {
        $sql = "INSERT INTO usuarios (dni, nombre, telefono, email, contrasena, foto) VALUES (?,?,?,?,?,?)";
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $newJugador->getDni());
        $stmt->bindValue(2, $newJugador->getNombre());
        $stmt->bindValue(3, $newJugador->getTelefono());
        $stmt->bindValue(4, $newJugador->getEmail());
        $stmt->bindValue(5, $newJugador->getPassword());
        $stmt->bindValue(6, $newJugador->getFoto());
        $stmt->execute();

        $id = $this->dbal->lastInsertId();
        $newJugador->setId($id);

        return $newJugador;
    }

    public function findByDni($dni)
    {
        $sql = 'SELECT u.* FROM usuarios u where u.dni = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $dni);
        $stmt->execute();
        $data = $stmt->fetch();

        return $this->factory->make($data);
    }

    public function findById($jugadorId)
    {
        $sql = 'SELECT u.* FROM usuarios u where u.id = ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $jugadorId);
        $stmt->execute();
        $data = $stmt->fetch();

        return $this->factory->make($data);
    }
}