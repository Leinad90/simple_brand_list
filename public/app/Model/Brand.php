<?php

namespace app\Model;

use Nette\Database\Connection;
use Nette\Database\Row;

class Brand
{

    public function __construct(
        protected readonly Connection $connection
    ) {
    }
    public function getPage(int $offset, int $limit): array
    {
        return $this->connection->fetchAll(
            'SELECT * FROM brands ORDER BY name LIMIT ?,?',
            $offset,
            $limit
        );
    }

    public function getById(int $id) : ?Row
    {
        return $this->connection->fetch(
            'SELECT * FROM brands WHERE id=?',
            $id
        );
    }

    public function getCount(): int
    {
        return $this->connection->fetchField(
            'SELECT count(*) FROM brands'
        );
    }

    public function save(array $data ): int
    {
        if(IsSet($data['id']) && $data['id'])
        {
            $this->connection->query('UPDATE brands SET ? WHERE id = ?',$data,$data['id']);
            return $data['id'];
        }
        unset($data['id']);
        $this->connection->query('INSERT INTO brands ?',$data);
        return $this->connection->getInsertId();
    }

    public function delete(int $id): void
    {
        $this->connection->query('DELETE FROM brands WHERE id = ?',$id);
    }
}