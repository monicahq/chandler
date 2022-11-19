<?php

namespace App\Domains\Contact\Dav\Services;

use App\Domains\Contact\ManageContact\Services\CreateContact;
use App\Domains\Contact\ManageContact\Services\UpdateContact;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\Gender;
use App\Services\BaseService;
use App\Traits\DAVFormat;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use ReturnTypeWillChange;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\ParseException;
use Sabre\VObject\Reader;

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
        self::BEHAVIOUR_ADD, self::BEHAVIOUR_REPLACE,
    ];

    /**
     * The Account id.
     *
     * @var int
     */
    public int $accountId = 0;

    /**
     * The genders that will be associated with imported contacts.
     *
     * @var array<Gender>
     */
    protected array $genders = [];

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
        $this->genders = [];
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
        ['entry' => $entry, 'vcard' => $vcard] = $this->getEntry($data);

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

        $contact = Arr::get($data, 'contact_id') === null ? $this->getExistingContact($entry) : $this->contact;

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
            'entry' => $entry,
            'vcard' => $vcard,
        ];
    }

    /**
     * Get or create the gender called "Vcard" that is associated with all
     * imported contacts.
     *
     * @param  string  $genderCode
     * @return Gender
     */
    private function getGender(string $genderCode): Gender
    {
        if (! Arr::has($this->genders, $genderCode)) {
            $gender = $this->getGenderByType($genderCode);
            if (! $gender) {
                switch ($genderCode) {
                    case 'M':
                        $gender = $this->getGenderByName(trans('account.gender_male')) ?? $this->getGenderByName(config('dav.default_gender'));
                        break;
                    case 'F':
                        $gender = $this->getGenderByName(trans('account.gender_female')) ?? $this->getGenderByName(config('dav.default_gender'));
                        break;
                    default:
                        $gender = $this->getGenderByName(config('dav.default_gender'));
                        break;
                }
            }

            if (! $gender) {
                $gender = new Gender;
                $gender->account_id = $this->accountId;
                $gender->name = config('dav.default_gender');
                // $gender->type = Gender::UNKNOWN;
                $gender->save();
            }

            Arr::set($this->genders, $genderCode, $gender);
        }

        return Arr::get($this->genders, $genderCode);
    }

    /**
     * Get the gender by name.
     *
     * @param  string  $name
     * @return Gender|null
     */
    private function getGenderByName(string $name): ?Gender
    {
        return Gender::where([
            'account_id' => $this->accountId,
            'name' => $name,
        ])->first();
    }

    /**
     * Get the gender by type.
     *
     * @param  string  $type
     * @return Gender|null
     */
    private function getGenderByType(string $type): ?Gender
    {
        return Gender::where([
            'account_id' => $this->accountId,
            'type' => $type,
        ])->first();
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
    private function hasNICKNAME(VCard $entry): bool
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

        if (! $contact) {
            $contact = $this->existingContactWithEmail($entry);
        }

        if (! $contact) {
            $contact = $this->existingContactWithName($entry);
        }

        if ($contact) {
            $contact->timestamps = false;
        }

        return $contact;
    }

    /**
     * Search with email field.
     *
     * @param  VCard  $entry
     * @return null
     */
    private function existingContactWithEmail(VCard $entry)
    {
        if (empty($entry->EMAIL)) {
            return null;
        }

        return null;
    }

    /**
     * Search with names fields.
     *
     * @param  VCard  $entry
     * @return Contact|null
     */
    private function existingContactWithName(VCard $entry): ?Contact
    {
        $contact = [];
        $this->importNames($contact, $entry);

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
        $contact = $this->importGeneralInformation($contact, $entry);

        $this->importPhoto($contact, $entry);
        $this->importWorkInformation($contact, $entry);
        $this->importAddress($contact, $entry);
        $this->importEmail($contact, $entry);
        $this->importTel($contact, $entry);
        $this->importSocialProfile($contact, $entry);
        $this->importCategories($contact, $entry);
        $this->importNote($contact, $entry);

        // Save vcard content
        $contact->vcard = $vcard;
        $contact->distant_etag = $etag;

        $contact->save();

        return $contact;
    }

    /**
     * Import general contact information.
     *
     * @param  Contact|null  $contact
     * @param  VCard  $entry
     * @return Contact
     */
    private function importGeneralInformation(?Contact $contact, VCard $entry): Contact
    {
        $contactData = $this->getContactData($contact);
        $original = $contactData;

        $contactData = $this->importUid($contactData, $entry);
        $contactData = $this->importNames($contactData, $entry);
        $contactData = $this->importGender($contactData, $entry);
        $contactData = $this->importBirthday($contactData, $entry);

        if ($contact !== null && $contactData !== $original) {
            $contact = app(UpdateContact::class)->execute($contactData);
        } else {
            $contactData['listed'] = true;
            $contact = app(CreateContact::class)->execute($contactData);
        }

        return $contact;
    }

    /**
     * Get contact data.
     *
     * @param  Contact|null  $contact
     * @return array
     */
    private function getContactData(?Contact $contact): array
    {
        $result = [
            'account_id' => $this->accountId,
            'vault_id' => $this->vault->id,
            'author_id' => $this->author->id,
            'first_name' => $contact ? $contact->first_name : null,
            'last_name' => $contact ? $contact->last_name : null,
            'middle_name' => $contact ? $contact->middle_name : null,
            'nickname' => $contact ? $contact->nickname : null,
            'gender_id' => $contact ? $contact->gender_id : $this->getGender('O')->id,
        ];

        if ($contact) {
            $result['contact_id'] = $contact->id;
        }

        return $result;
    }

    /**
     * Import names of the contact.
     *
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importNames(array $contactData, VCard $entry): array
    {
        if ($this->hasFirstnameInN($entry)) {
            $contactData = $this->importFromN($contactData, $entry);
        } elseif ($this->hasFN($entry)) {
            $contactData = $this->importFromFN($contactData, $entry);
        } elseif ($this->hasNICKNAME($entry)) {
            $contactData = $this->importFromNICKNAME($contactData, $entry);
        } else {
            throw new \LogicException('Check if you can import entry!');
        }

        return $contactData;
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
        } elseif ($this->hasNICKNAME($entry)) {
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
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importFromN(array $contactData, VCard $entry): array
    {
        $parts = $entry->N->getParts();

        $contactData['last_name'] = $this->formatValue(Arr::get($parts, '0'));
        $contactData['first_name'] = $this->formatValue(Arr::get($parts, '1'));
        $contactData['middle_name'] = $this->formatValue(Arr::get($parts, '2'));
        // prefix [3]
        // suffix [4]

        if (! empty($entry->NICKNAME)) {
            $contactData['nickname'] = $this->formatValue($entry->NICKNAME);
        }

        return $contactData;
    }

    /**
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importFromNICKNAME(array $contactData, VCard $entry): array
    {
        $contactData['first_name'] = $this->formatValue($entry->NICKNAME);

        return $contactData;
    }

    /**
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importFromFN(array $contactData, VCard $entry): array
    {
        $fullnameParts = preg_split('/\s+/', $entry->FN, 2);

        if (Str::of($this->author->name_order)->startsWith('%first_name% %last_name%')) {
            $contactData['first_name'] = $this->formatValue($fullnameParts[0]);
            if (count($fullnameParts) > 1) {
                $contactData['last_name'] = $this->formatValue($fullnameParts[1]);
            }
        } elseif (Str::of($this->author->name_order)->startsWith('%last_name% %first_name%')) {
            $contactData['last_name'] = $this->formatValue($fullnameParts[0]);
            if (count($fullnameParts) > 1) {
                $contactData['first_name'] = $this->formatValue($fullnameParts[1]);
            }
        } else {
            $contactData['first_name'] = $this->formatValue($fullnameParts[0]);
        }

        if (! empty($entry->NICKNAME)) {
            $contactData['nickname'] = $this->formatValue($entry->NICKNAME);
        }

        return $contactData;
    }

    /**
     * Import uid of the contact.
     *
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importUid(array $contactData, VCard $entry): array
    {
        if (! empty($uuid = (string) $entry->UID) && Uuid::isValid($uuid)) {
            $contactData['uuid'] = $uuid;
        }

        return $contactData;
    }

    /**
     * Import gender of the contact.
     *
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importGender(array $contactData, VCard $entry): array
    {
        if ($entry->GENDER) {
            $contactData['gender_id'] = $this->getGender((string) $entry->GENDER)->id;
        }

        return $contactData;
    }

    /**
     * Import photo of the contact.
     *
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importPhoto(Contact $contact, VCard $entry): void
    {
        if ($entry->PHOTO) {
        }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importWorkInformation(Contact $contact, VCard $entry): void
    {
        if ($entry->ORG) {
        }

        if ($entry->ROLE) {
        }

        if ($entry->TITLE) {
        }
    }

    /**
     * @param  array  $contactData
     * @param  VCard  $entry
     * @return array
     */
    private function importBirthday(array $contactData, VCard $entry): array
    {
        if ($entry->BDAY && ! empty((string) $entry->BDAY)) {
        }

        return $contactData;
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importAddress(Contact $contact, VCard $entry): void
    {
        if (! $entry->ADR) {
            return;
        }

        foreach ($entry->ADR as $adr) {
        }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importEmail(Contact $contact, VCard $entry): void
    {
        if (is_null($entry->EMAIL)) {
            return;
        }

        foreach ($entry->EMAIL as $email) {
        }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importNote(Contact $contact, VCard $entry): void
    {
        if (is_null($entry->NOTE)) {
            return;
        }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importTel(Contact $contact, VCard $entry): void
    {
        if (is_null($entry->TEL)) {
            return;
        }

        foreach ($entry->TEL as $tel) {
        }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importSocialProfile(Contact $contact, VCard $entry): void
    {
        if (is_null($entry->socialProfile)) {
            return;
        }

        foreach ($entry->socialProfile as $socialProfile) {
        }
    }

    /**
     * Import the categories as tags.
     *
     * @param  Contact  $contact
     * @param  VCard  $entry
     * @return void
     */
    private function importCategories(Contact $contact, VCard $entry): void
    {
        if (! is_null($entry->CATEGORIES)) {
        }
    }
}
