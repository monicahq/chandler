<?php

namespace App\Domains\Contact\Dav\Services;

use App\Domains\Contact\Dav\ExportVCardResource;
use App\Domains\Contact\Dav\Order;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Services\BaseService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\ParseException;
use Sabre\VObject\Reader;
use Symfony\Component\Finder\Finder;

class ExportVCard extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'contact_id' => 'required|integer|exists:contacts,id',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_in_vault',
            'contact_must_belong_to_vault',
        ];
    }

    public function __construct(private Application $app)
    {
    }

    /**
     * Export one VCard.
     *
     * @param  array  $data
     * @return VCard
     */
    public function execute(array $data): VCard
    {
        $this->validateRules($data);

        $vcard = $this->export($this->contact);

        $this->contact->timestamps = false;
        $this->contact->vcard = $vcard->serialize();
        $this->contact->save();

        return $vcard;
    }

    /**
     * @param  Contact  $contact
     * @return VCard
     */
    private function export(Contact $contact): VCard
    {
        // The standard for most of these fields can be found on https://datatracker.ietf.org/doc/html/rfc6350
        if ($contact->vcard) {
            try {
                /** @var VCard */
                $vcard = Reader::read($contact->vcard, Reader::OPTION_FORGIVING + Reader::OPTION_IGNORE_INVALID_LINES);
                if (! $vcard->UID) {
                    $vcard->UID = $contact->uuid;
                }
            } catch (ParseException $e) {
                // Ignore error
            }
        }
        if (! isset($vcard)) {
            // Basic information
            $vcard = new VCard([
                'UID' => $contact->uuid,
                'SOURCE' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'VERSION' => '4.0',
            ]);
        }

        $exporters = collect($this->exporters());
        foreach ($exporters->sortBy('order')->pluck('exporter') as $exporter) {
            /** @var ExportVCardResource */
            $exporter = $exporter->newInstance();
            $exporter->export($contact, $vcard);
        }

        return $vcard;
    }

    private function exporters()
    {
        $namespace = $this->app->getNamespace();
        $appPath = app_path();

        foreach ((new Finder)->files()->in($appPath)->name('*.php') as $file) {
            $file = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getRealPath(), realpath($appPath).DIRECTORY_SEPARATOR)
            );

            $class = new ReflectionClass($file);
            if ($class->isSubclassOf(ExportVCardResource::class) && ! $class->isAbstract()) {
                $attributes = $class->getAttributes(Order::class, ReflectionAttribute::IS_INSTANCEOF);

                $order = count($attributes) > 0
                    ? $attributes[0]->newInstance()->order
                    : 0;

                yield [
                    'order' => $order,
                    'exporter' => $class,
                ];
            }
        }
    }
}
