<?php

namespace App\Repositories;

use App\Models\Events\Event;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PDO;

class EventsRepository
{
    public function __construct(
        private readonly PDO $db,
        private readonly ArtistsRepository $artistsRepository
    ) {
    }

    public function getUpcomingEvents(): Collection
    {
        $stmt = $this->db->query("
            SELECT events.* FROM events
            JOIN artists
                ON events.artist_id = artists.id
                    AND artists.deleted_at IS NULL
            WHERE DATE(events.start_at) >= CURDATE()
                AND events.deleted_at IS NULL
        ");

        return collect($stmt->fetchAll(PDO::FETCH_OBJ))->map(function (object $data) {
            return new Event(
                (int) $data->id,
                $this->artistsRepository->getArtistById((int) $data->artist_id),
                Carbon::parse($data->start_at)
            );
        });
    }

    public function saveEvent(Event $event, User $user): Event
    {
        if ($event->id === null) {
            $stmt = $this->db->prepare("
                INSERT INTO events (artist_id, start_at, created_at, actor_id)
                VALUE (:artistId, :startAt, NOW(), :actorId);
            ");

            $stmt->execute([
                'artistId' => $event->artist->id,
                'startAt' => $event->startAt->format('Y-m-d H:i:s'),
                'actorId' => $user->id,
            ]);

            $event->id = (int) $this->db->lastInsertId();

            return $event;
        }

        return $event;
    }
}
