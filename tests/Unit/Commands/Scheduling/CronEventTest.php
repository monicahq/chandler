<?php

namespace Tests\Unit\Commands\Scheduling;

use App\Console\Scheduling\CronEvent;
use App\Models\Instance\Cron;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CronEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_get_the_right_command()
    {
        $cron = Cron::factory()->create();

        $event = CronEvent::command($cron->command);

        $this->assertEquals($event->cron()->id, $cron->id);
    }

    /** @test */
    public function now_not_due()
    {
        $cron = Cron::factory()->create();
        $event = new CronEvent($cron);

        $this->assertFalse($event->isDue());
    }

    /** @test */
    public function next_minute_is_due()
    {
        Carbon::setTestNow(Carbon::create(2019, 5, 1, 7, 0, 0));

        $cron = Cron::factory()->create();
        $event = new CronEvent($cron);

        $this->assertFalse($event->isDue());

        Carbon::setTestNow(Carbon::create(2019, 5, 1, 7, 1, 0));

        $this->assertTrue($event->isDue());

        $this->assertDatabaseHas('crons', [
            'command' => $cron->command,
            'last_run_at' => '2019-05-01 07:01:00',
        ]);
    }

    /** @test */
    public function hourly_cron()
    {
        Carbon::setTestNow(Carbon::create(2019, 5, 1, 7, 0, 0));

        $cron = Cron::factory()->create();
        $event = new CronEvent($cron);
        $event->hourly();

        $this->assertFalse($event->isDue());

        Carbon::setTestNow(Carbon::create(2019, 5, 1, 8, 22, 0));

        $this->assertTrue($event->isDue());

        $this->assertDatabaseHas('crons', [
            'command' => $cron->command,
            'last_run_at' => '2019-05-01 08:22:00',
        ]);

        Carbon::setTestNow(Carbon::create(2019, 5, 1, 8, 59, 0));

        $this->assertFalse($event->isDue());

        Carbon::setTestNow(Carbon::create(2019, 5, 1, 9, 01, 0));

        $this->assertTrue($event->isDue());

        $this->assertDatabaseHas('crons', [
            'command' => $cron->command,
            'last_run_at' => '2019-05-01 09:01:00',
        ]);
    }

    /** @test */
    public function daily_cron()
    {
        Carbon::setTestNow(Carbon::create(2019, 5, 1, 7, 0, 0));

        $cron = Cron::factory()->create();
        $event = new CronEvent($cron);
        $event->daily();

        Carbon::setTestNow(Carbon::create(2019, 5, 2, 8, 10, 0));

        $this->assertTrue($event->isDue());

        $this->assertDatabaseHas('crons', [
            'command' => $cron->command,
            'last_run_at' => '2019-05-02 08:10:00',
        ]);

        Carbon::setTestNow(Carbon::create(2019, 5, 2, 10, 0, 0));

        $this->assertFalse($event->isDue());

        Carbon::setTestNow(Carbon::create(2019, 5, 3, 0, 0, 0));

        $this->assertTrue($event->isDue());

        $this->assertDatabaseHas('crons', [
            'command' => $cron->command,
            'last_run_at' => '2019-05-03 00:00:00',
        ]);
    }
}
