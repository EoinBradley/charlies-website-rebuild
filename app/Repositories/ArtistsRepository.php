<?php

namespace App\Repositories;

use App\Models\Artists\Artist;
use App\Models\Artists\Exceptions\ArtistNotFound;
use App\Models\Users\User;
use Illuminate\Support\Collection;
use PDO;

class ArtistsRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function getArtists(): Collection
    {
        $stmt = $this->db->query("
            SELECT * FROM artists
            WHERE deleted_at IS NULL
        ");

        return collect($stmt->fetchAll(PDO::FETCH_OBJ))->map(function (object $data) {
            return new Artist(
                (int) $data->id,
                $data->name,
                $data->description
            );
        });
    }

    /** @throws ArtistNotFound */
    public function getArtistById(int $id): Artist
    {
        $stmt = $this->db->prepare("
            SELECT * FROM artists
            WHERE id = :id
                AND deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw ArtistNotFound::withId($id);
        }

        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return new Artist(
            (int) $data->id,
            $data->name,
            $data->description
        );
    }

    public function saveArtist(Artist $artist, User $user): void
    {
        $stmt = $this->db->prepare("
            UPDATE artists
            SET name = :name,
                description = :description,
                updated_at = NOW(),
                actor_id = :actorId
            WHERE id = :id
        ");

        $stmt->execute([
            'name' => $artist->name,
            'description' => $artist->description,
            'actorId' => $user->id,
            'id' => $artist->id,
        ]);
    }
}
