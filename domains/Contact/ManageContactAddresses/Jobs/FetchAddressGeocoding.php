<?php

namespace App\Contact\ManageContactAddresses\Jobs;

use App\Contact\ManageContactAddresses\Services\GetGPSCoordinate;
use App\Models\Address;
use App\Models\Company\Place;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchAddressGeocoding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The place instance.
     *
     * @var Place
     */
    public Address $address;

    /**
     * Create a new job instance.
     *
     * @param  Place  $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new GetGPSCoordinate)->execute([
            'address_id' => $this->address->id,
        ]);
    }
}
