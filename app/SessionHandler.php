<?php

namespace App;

use PDO;
use SessionHandlerInterface;

class SessionHandler implements SessionHandlerInterface
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    public function close(): bool
    {
        return true;
    }

    public function destroy(string $id): bool
    {
        return $this
            ->db
            ->prepare("DELETE FROM sessions WHERE id = :id")
            ->execute(['id' => $id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        return $this
            ->db
            ->prepare("DELETE FROM sessions WHERE updated_at < DATE_SUB(NOW(), INTERVAL :lifetime SECOND)")
            ->execute(['lifetime' => $max_lifetime]);
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() ?: '';
    }

    public function write(string $id, string $data): bool
    {
        return $this
            ->db
            ->prepare("REPLACE INTO sessions (id, updated_at, data) VALUE (:id, NOW(), :data)")
            ->execute([
                'id' => $id,
                'data' => $data,
            ]);
    }
}
