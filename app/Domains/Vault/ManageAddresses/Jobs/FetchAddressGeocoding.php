<?php

namespace App\Domains\Vault\ManageAddresses\Jobs;

use App\Domains\Vault\ManageAddresses\Services\GetGPSCoordinate;
use App\Models\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchAddressGeocoding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The address instance.
     */
    public Address $address;

    /**
     * Create a new job instance.
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
