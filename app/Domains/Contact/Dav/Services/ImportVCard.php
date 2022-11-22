<?php

namespace App\Domains\Contact\Dav\Services;

use App\Domains\Contact\Dav\ImportVCardResource;
use App\Domains\Contact\Dav\Order;
use App\Domains\Contact\ManageContact\Dav\ImportContact;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Services\BaseService;
use App\Traits\DAVFormat;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use ReturnTypeWillChange;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\ParseException;
use Sabre\VObject\Reader;
use Symfony\Component\Finder\Finder;

class ImportVCard extends BaseService implements ServiceInterface
{
    use DAVFormat;

    public const BEHAVIOUR_ADD = 'behaviour_add';

    public const BEHAVIOUR_REPLACE = 'behaviour_replace';

    /** @var array<string,string> */
    protected array $errorResults = [
        'ERROR_PARSER' => 'import_vcard_parse_error',
        'ERROR_CONTACT_EXIST' => 'import_vcard_contact_exist',
        'ERROR_CONTACT_DOESNT_HAVE_FIRSTNAME' => 'import_vcard_contact_no_firstname',
    ];

    /**
     * Valids value for frequency type.
     *
     * @var array
     */
    public static array $behaviourTypes = [
        self::BEHAVIOUR_ADD,
        self::BEHAVIOUR_REPLACE,
    ];

    /**
     * The Account id.
     *
     * @var int
     */
    public int $accountId = 0;

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
            'contact_id' => 'nullable|integer|exists:contacts,id',
            'entry' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (! is_string($value) && ! is_resource($value) && ! $value instanceof VCard) {
                        $fail($attribute.' must be a string, a resource, or a VCard object.');
                    }
                },
            ],
            'behaviour' => [
                'required',
                Rule::in(self::$behaviourTypes),
            ],
            'etag' => 'nullable|string',
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
        ];
    }

    public function __construct(private Application $app)
    {
    }

    /**
     * Import one VCard.
     *
     * @param  array  $data
     * @return array
     */
    public function execute(array $data): array
    {
        $this->validateRules($data);

        if (Arr::get($data, 'contact_id') !== null) {
            $this->validateContactBelongsToVault($data);
        }

        return $this->process($data);
    }

    private function clear()
    {
        $this->accountId = 0;
    }

    /**
     * Process data importation.
     *
     * @param  array  $data
     * @return array
     */
    private function process(array $data): array
    {
        if ($this->accountId !== $data['account_id']) {
            $this->clear();
            $this->accountId = $data['account_id'];
        }

        /**
         * @var VCard|null $entry
         * @var string $vcard
         */
        [$entry, $vcard] = $this->getEntry($data);

        if ($entry === null) {
            return [
                'error' => 'ERROR_PARSER',
                'reason' => $this->errorResults['ERROR_PARSER'],
                'name' => '(unknow)',
            ];
        }

        return $this->processEntry($data, $entry, $vcard);
    }

    /**
     * Process entry importation.
     *
     * @param  array  $data
     * @param  VCard  $entry
     * @param  string  $vcard
     * @return array
     */
    private function processEntry(array $data, VCard $entry, string $vcard): array
    {
        if (! $this->canImportCurrentEntry($entry)) {
            return [
                'error' => 'ERROR_CONTACT_DOESNT_HAVE_FIRSTNAME',
                'reason' => $this->errorResults['ERROR_CONTACT_DOESNT_HAVE_FIRSTNAME'],
                'name' => $this->name($entry),
            ];
        }

        $contact = Arr::get($data, 'contact_id') === null
            ? $this->getExistingContact($entry)
            : $this->contact;

        return $this->processEntryContact($data, $entry, $vcard, $contact);
    }

    /**
     * Process entry importation.
     *
     * @param  array  $data
     * @param  VCard  $entry
     * @param  string  $vcard
     * @param  Contact|null  $contact
     * @return array
     */
    private function processEntryContact(array $data, VCard $entry, string $vcard, ?Contact $contact): array
    {
        $behaviour = $data['behaviour'] ?: self::BEHAVIOUR_ADD;
        if ($contact && $behaviour === self::BEHAVIOUR_ADD) {
            return [
                'contact_id' => $contact->id,
                'error' => 'ERROR_CONTACT_EXIST',
                'reason' => $this->errorResults['ERROR_CONTACT_EXIST'],
                'name' => $this->name($entry),
            ];
        }

        if ($contact) {
            $timestamps = $contact->timestamps;
            $contact->timestamps = false;
        }

        $contact = $this->importEntry($contact, $entry, $vcard, Arr::get($data, 'etag'));

        if (isset($timestamps)) {
            $contact->timestamps = $timestamps;
        }

        return [
            'contact_id' => $contact->id,
            'name' => $this->name($entry),
        ];
    }

    /**
     * Return the name and email address of the current entry.
     * John Doe Johnny john@doe.com.
     * Only used for report display.
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     *
     * @param  VCard  $entry
     * @return array|string|null|\Illuminate\Contracts\Translation\Translator
     */
    #[ReturnTypeWillChange]
    private function name($entry)
    {
        if ($this->hasFirstnameInN($entry)) {
            $parts = $entry->N->getParts();

            $name = '';
            if (! empty(Arr::get($parts, '1'))) {
                $name .= $this->formatValue($parts[1]);
            }
            if (! empty(Arr::get($parts, '2'))) {
                $name .= ' '.$this->formatValue($parts[2]);
            }
            if (! empty(Arr::get($parts, '0'))) {
                $name .= ' '.$this->formatValue($parts[0]);
            }
            $name .= ' '.$this->formatValue($entry->EMAIL);
        } elseif ($this->hasNickname($entry)) {
            $name = $this->formatValue($entry->NICKNAME);
            $name .= ' '.$this->formatValue($entry->EMAIL);
        } elseif ($this->hasFN($entry)) {
            $name = $this->formatValue($entry->FN);
            $name .= ' '.$this->formatValue($entry->EMAIL);
        } else {
            $name = trans('settings.import_vcard_unknown_entry');
        }

        return $name;
    }

    /**
     * @param  array  $data
     * @return array
     */
    private function getEntry(array $data): array
    {
        $entry = $vcard = $data['entry'];

        if (! $entry instanceof VCard) {
            try {
                $entry = Reader::read($entry, Reader::OPTION_FORGIVING + Reader::OPTION_IGNORE_INVALID_LINES);
            } catch (ParseException $e) {
                return [
                    'entry' => null,
                    'vcard' => $vcard,
                ];
            }
        }

        if ($vcard instanceof VCard) {
            $vcard = $entry->serialize();
        }

        return [
            $entry,
            $vcard,
        ];
    }

    /**
     * Check whether a contact has a first name or a nickname. If not, contact
     * can not be imported.
     *
     * @param  VCard  $entry
     * @return bool
     */
    private function canImportCurrentEntry(VCard $entry): bool
    {
        return
            $this->hasFirstnameInN($entry) ||
            $this->hasNickname($entry) ||
            $this->hasFN($entry);
    }

    /**
     * @param  VCard  $entry
     * @return bool
     */
    private function hasFirstnameInN(VCard $entry): bool
    {
        return $entry->N !== null && ! empty(Arr::get($entry->N->getParts(), '1'));
    }

    /**
     * @param  VCard  $entry
     * @return bool
     */
    private function hasNickname(VCard $entry): bool
    {
        return ! empty((string) $entry->NICKNAME);
    }

    /**
     * @param  VCard  $entry
     * @return bool
     */
    private function hasFN(VCard $entry): bool
    {
        return ! empty((string) $entry->FN);
    }

    /**
     * Check whether the contact already exists in the database.
     *
     * @param  VCard  $entry
     * @return Contact|null
     */
    private function getExistingContact(VCard $entry): ?Contact
    {
        $contact = $this->existingUuid($entry);

        // if (! $contact) {
        //     $contact = $this->existingContactWithEmail($entry);
        // }

        if (! $contact) {
            $contact = $this->existingContactWithName($entry);
        }

        if ($contact) {
            $contact->timestamps = false;
        }

        return $contact;
    }

    // /**
    //  * Search with email field.
    //  *
    //  * @param  VCard  $entry
    //  * @return null
    //  */
    // private function existingContactWithEmail(VCard $entry)
    // {
    //     if (empty($entry->EMAIL)) {
    //         return null;
    //     }

    //     return null;
    // }

    /**
     * Search with names fields.
     *
     * @param  VCard  $entry
     * @return Contact|null
     */
    private function existingContactWithName(VCard $entry): ?Contact
    {
        $contact = [];
        app(ImportContact::class)->importNames($contact, $entry);

        return Contact::where([
            'vault_id' => $this->vault->id,
            'first_name' => Arr::get($contact, 'first_name'),
            'middle_name' => Arr::get($contact, 'middle_name'),
            'last_name' => Arr::get($contact, 'last_name'),
        ])->first();
    }

    /**
     * Search with uuid.
     *
     * @param  VCard  $entry
     * @return Contact|null
     */
    private function existingUuid(VCard $entry): ?Contact
    {
        return ! empty($uuid = (string) $entry->UID) && Uuid::isValid($uuid)
            ? Contact::where([
                'vault_id' => $this->vault->id,
                'uuid' => $uuid,
            ])->first()
            : null;
    }

    /**
     * Create the Contact object matching the current entry.
     *
     * @param  Contact|null  $contact
     * @param  VCard  $entry
     * @param  string  $vcard
     * @param  string|null  $etag
     * @return Contact
     */
    private function importEntry(?Contact $contact, VCard $entry, string $vcard, ?string $etag): Contact
    {
        /**
         * @var \Illuminate\Support\Collection<int, ImportVCardResource> $importers
         */
        $importers = collect($this->importers())
            ->sortBy(fn (ReflectionClass $importer) => Order::get($importer))
            ->map(fn (ReflectionClass $importer): ImportVCardResource => $importer->newInstance()->setContext($this)
            );

        foreach ($importers as $importer) {
            $contact = $importer->import($contact, $entry);
        }

        // Save vcard content
        $contact->vcard = $vcard;
        $contact->distant_etag = $etag;

        $contact->save();

        return $contact;
    }

    private function importers()
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
            if ($class->isSubclassOf(ImportVCardResource::class) && ! $class->isAbstract()) {
                yield $class;
            }
        }
    }
}
