<?php

namespace Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Domain\Model\Comentario;
use Domain\Model\ComentarioFactory;
use Domain\Model\ComentarioRepository;
use PDO;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalComentarioRepository implements ComentarioRepository
{
    private $dbal;
    private $factory;

    function __construct(Connection $dbal, ComentarioFactory $factory)
    {
        $this->dbal = $dbal;
        $this->factory = $factory;
    }

    public function findAll($limit)
    {
        $sql = 'SELECT *
                FROM comentarios
                ORDER BY fecha desc
                LIMIT ?';
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->factory->makeAll($data);
    }

    public function add(Comentario $comentario)
    {
        $sql = "INSERT INTO comentarios (usuario, fecha, comentario) VALUES (?,?,?)";
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $comentario->getUsuario());
        $stmt->bindValue(2, $comentario->getFecha(), 'datetime');
        $stmt->bindValue(3, $comentario->getContenido());
        $stmt->execute();

        $id = $this->dbal->lastInsertId();
        $comentario->setId($id);

        return $comentario;
    }
}