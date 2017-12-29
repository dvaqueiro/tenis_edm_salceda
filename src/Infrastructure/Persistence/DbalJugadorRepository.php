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
        $sql = "INSERT INTO usuarios (dni, nombre, telefono, email, password, foto) VALUES (?,?,?,?,?,?)";
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

    public function update(Jugador $jugador)
    {
        $sql = "UPDATE usuarios set nombre=?, telefono=?, email=?, foto=?, password=? WHERE id = ?";
        return $this->dbal->executeUpdate($sql, [
            $jugador->getNombre(),
            $jugador->getTelefono(),
            $jugador->getEmail(),
            $jugador->getFoto(),
            $jugador->getPassword(),
            $jugador->getId(),
        ]);
    }

    public function findRivales($idLiga, $idJugador)
    {
        $sql = 'SELECT ul.*
                FROM divisiones d
                INNER JOIN ud ON ud.`iddivision` = d.`id`
                INNER JOIN ud ud2 ON ud2.`iddivision` = d.`id`
                INNER JOIN usuarios ul ON ul.`id` = ud2.`idusuario` AND ul.id <> ?
                WHERE  d.`idliga` = ? AND ud.`idusuario` = ?;';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $idJugador);
        $stmt->bindValue(2, $idLiga);
        $stmt->bindValue(3, $idJugador);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }

    public function findAll()
    {
        $sql = 'SELECT u.* FROM usuarios u ';
        $stmt = $this->dbal->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }

    public function findAllWithRoles($roles)
    {
        if(empty($roles)) {
            return $this->findAll();
        }

        $sql = 'SELECT u.* FROM usuarios u WHERE u.roles IN (?)';
        $stmt = $this->dbal->executeQuery($sql,
            array($roles),
            array(Connection::PARAM_INT_ARRAY)
        );
        $data = $stmt->fetchAll();

        return $this->factory->makeAll($data);
    }
}