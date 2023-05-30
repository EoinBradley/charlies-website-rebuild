<?php

use Carbon\Carbon;
use Phinx\Seed\AbstractSeed;

class DatabaseSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $this->execute("DELETE FROM users WHERE id = 1");
        $this->table('users')
            ->insert([
                [
                    'id' => 1,
                    'username' => 'eoinbradley1994@gmail.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'email' => 'eoinbradley1994@gmail.com',
                    'first_name' => 'Eoin',
                    'last_name' => 'Bradley',
                    'created_at' => Carbon::now()->format('Y-m-d'),
                    'activated_at' => Carbon::now()->format('Y-m-d'),
                    'actor_id' => 1,
                ],
            ])->save();

        $this->execute("DELETE FROM configs WHERE id = 'homepage-description'");
        $this->table('configs')
            ->insert([
                [
                    'id' => 'homepage-description',
                    'value' => "Charlies is a famous early morning Cork bar, full of character and characters. Opening every morning, except Sundays, at 7am this is the closest youll get to round-the-clock craic in a traditional Irish bar and its welcoming coal fire.

And if you thought it was fun before the sun goes down wait until the guitarists, fiddle players, drummers and singers turn up! We host some of the best local, national and international acts from rock n roll and blues at night to traditional Irish music and folk acts on Sunday afternoons.

Due to covid-19 restrictions we will be reopening under phase 3 government guidelines serving food. Until further notice we will be running at reduced opening hours and will be following all government guidelines to ensure the safety of our customers and staff. Hopefully our live music will return in the near future. We are looking forward to welcoming back our loyal regulars and maybe some new customers as well to enjoy Charlies bar and our new riverside seating area. (Now accepting all major credit and debit cards)",
                    'created_at' => Carbon::now()->format('Y-m-d'),
                    'actor_id' => 1,
                ],
            ])->save();

        $this->execute("UPDATE opening_hours SET deleted_at = NOW(), actor_id = 1");
        $this->table('opening_hours')
            ->insert([
                [
                    'day_of_week' => 1,
                    'opened_at' => '12:30',
                    'closed_at' => '23:00',
                    'actor_id' => 1,
                ],
                [
                    'day_of_week' => 2,
                    'opened_at' => '7:00',
                    'closed_at' => '23:30',
                    'actor_id' => 1,
                ],
                [
                    'day_of_week' => 3,
                    'opened_at' => '7:00',
                    'closed_at' => '23:30',
                    'actor_id' => 1,
                ],
                [
                    'day_of_week' => 4,
                    'opened_at' => '7:00',
                    'closed_at' => '23:30',
                    'actor_id' => 1,
                ],
                [
                    'day_of_week' => 5,
                    'opened_at' => '7:00',
                    'closed_at' => '23:30',
                    'actor_id' => 1,
                ],
                [
                    'day_of_week' => 6,
                    'opened_at' => '7:00',
                    'closed_at' => '00:30',
                    'actor_id' => 1,
                ],
                [
                    'day_of_week' => 7,
                    'opened_at' => '7:00',
                    'closed_at' => '00:30',
                    'actor_id' => 1,
                ],
            ])->save();
    }
}
