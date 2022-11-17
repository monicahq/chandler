<?php

namespace App\Domains\Contact\Dav\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\Gender;
use App\Services\BaseService;
// use App\Models\Contact\Tag;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\ParseException;
// use App\Interfaces\LabelInterface;
use Sabre\VObject\Reader;

// use App\Models\Contact\ContactFieldType;
// use App\Models\Contact\ContactFieldLabel;

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

    private function escape($value): string
    {
        return ! empty((string) $value) ? trim((string) $value) : (string) null;
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

        $this->exportNames($contact, $vcard);
        $this->exportGender($contact, $vcard);
        $this->exportPhoto($contact, $vcard);
        $this->exportWorkInformation($contact, $vcard);
        $this->exportBirthday($contact, $vcard);
        $this->exportAddress($contact, $vcard);
        $this->exportContactFields($contact, $vcard);
        $this->exportTimestamp($contact, $vcard);
        $this->exportTags($contact, $vcard);

        return $vcard;
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportNames(Contact $contact, VCard $vcard)
    {
        $vcard->remove('FN');
        $vcard->remove('N');
        $vcard->remove('NICKNAME');

        $vcard->add('FN', $this->escape($contact->name));

        $vcard->add('N', [
            $this->escape($contact->last_name),
            $this->escape($contact->first_name),
            $this->escape($contact->middle_name),
        ]);

        if (! empty($contact->nickname)) {
            $vcard->add('NICKNAME', $this->escape($contact->nickname));
        }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportGender(Contact $contact, VCard $vcard)
    {
        $vcard->remove('GENDER');

        if (is_null($contact->gender)) {
            return;
        }

        $gender = $contact->gender->type;
        if (empty($gender)) {
            switch ($contact->gender->name) {
                case trans('account.gender_male'):
                    $gender = Gender::MALE;
                    break;
                case trans('account.gender_female'):
                    $gender = Gender::FEMALE;
                    break;
                default:
                    $gender = Gender::OTHER;
                    break;
            }
        }
        $vcard->add('GENDER', $gender);
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportPhoto(Contact $contact, VCard $vcard)
    {
        // $vcard->remove('PHOTO');

        // if ($contact->avatar_source == 'photo') {
        //     $photo = $contact->avatarPhoto;

        //     $vcard->add('PHOTO', $photo->dataUrl());
        // } else {
        //     $picture = $contact->getAvatarURL();

        //     if (! empty($picture)) {
        //         $vcard->add('PHOTO', $picture);
        //     }
        // }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportWorkInformation(Contact $contact, VCard $vcard)
    {
        // $vcard->remove('ORG');
        // $vcard->remove('TITLE');

        // if (! empty($contact->company)) {
        //     $vcard->add('ORG', $this->escape($contact->company));
        // }

        // if (! empty($contact->job)) {
        //     $vcard->add('TITLE', $this->escape($contact->job));
        // }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.5
     */
    private function exportBirthday(Contact $contact, VCard $vcard)
    {
        // $vcard->remove('BDAY');

        // if (! is_null($contact->birthdate)) {
        //     if ($contact->birthdate->is_year_unknown) {
        //         $date = $contact->birthdate->date->format('--md');
        //     } else {
        //         $date = $contact->birthdate->date->format('Ymd');
        //     }
        //     $vcard->add('BDAY', $date);
        // }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6350#section-6.3.1
     */
    private function exportAddress(Contact $contact, VCard $vcard)
    {
        // $vcard->remove('ADR');

        // foreach ($contact->addresses as $address) {
        //     $type = $this->getContactFieldLabel($address);
        //     $arguments = [];
        //     if ($type != '') {
        //         $arguments['TYPE'] = $type;
        //     }
        //     $vcard->add('ADR', [
        //         '',
        //         '',
        //         $address->place->street,
        //         $address->place->city,
        //         $address->place->province,
        //         $address->place->postal_code,
        //         $address->place->country,
        //     ],
        //         $arguments
        //     );
        // }
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportContactFields(Contact $contact, VCard $vcard)
    {
        // $vcard->remove('TEL');
        // $vcard->remove('EMAIL');
        // $vcard->remove('socialProfile');
        // $vcard->remove('URL');

        // foreach ($contact->contactFields as $contactField) {
        //     $type = $this->getContactFieldLabel($contactField);
        //     switch ($contactField->contactFieldType->type) {
        //         case ContactFieldType::PHONE:
        //             $vcard->add('TEL', $this->escape($contactField->data), $type);
        //             break;
        //         case ContactFieldType::EMAIL:
        //             $vcard->add('EMAIL', $this->escape($contactField->data), $type);
        //             break;
        //         default:
        //             switch ($contactField->contactFieldType->name) {
        //                 // See https://tools.ietf.org/id/draft-george-vcarddav-vcard-extension-02.html
        //                 case 'Facebook':
        //                     $vcard->add('socialProfile', $this->escape('https://www.facebook.com/'.$contactField->data), ['type' => 'facebook']);
        //                     break;
        //                 case 'Twitter':
        //                     $vcard->add('socialProfile', $this->escape('https://twitter.com/'.$contactField->data), ['type' => 'twitter']);
        //                     break;
        //                 case 'Whatsapp':
        //                     $vcard->add('socialProfile', $this->escape('https://wa.me/'.$contactField->data), ['type' => 'whatsapp']);
        //                     break;
        //                 case 'Telegram':
        //                     $vcard->add('socialProfile', $this->escape('http://t.me/'.$contactField->data), ['type' => 'telegram']);
        //                     break;
        //                 case 'LinkedIn':
        //                     $vcard->add('socialProfile', $this->escape('http://www.linkedin.com/in/'.$contactField->data), ['type' => 'linkedin']);
        //                     break;
        //                 default:
        //                     // If field isn't a supported social profile, but still has a protocol, then export it as a url.
        //                     if (! empty($contactField->contactFieldType->protocol)) {
        //                         $vcard->add('URL', $this->escape($contactField->contactFieldType->protocol.$contactField->data));
        //                     }
        //                     break;
        //             }
        //             break;
        //     }
        // }
    }

    // /**
    //  * @param  LabelInterface  $labelProvider
    //  * @return array|null
    //  */
    // private function getContactFieldLabel(LabelInterface $labelProvider): ?array
    // {
    //     $type = null;
    //     /** @var \Illuminate\Support\Collection<array-key, \App\Models\Contact\ContactFieldLabel> */
    //     $labels = $labelProvider->labels()->get();
    //     if ($labels->count() > 0) {
    //         $type = [];
    //         $type['type'] = $labels->map(function (ContactFieldLabel $label): string {
    //             return mb_strtoupper($label->label_i18n) ?: $label->label;
    //         })->join(',');
    //     }

    //     return $type;
    // }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportTimestamp(Contact $contact, VCard $vcard)
    {
        $vcard->remove('REV');
        $vcard->REV = $contact->updated_at->format('Ymd\\THis\\Z');
    }

    /**
     * @param  Contact  $contact
     * @param  VCard  $vcard
     */
    private function exportTags(Contact $contact, VCard $vcard)
    {
        // $vcard->remove('CATEGORIES');

        // if ($contact->tags->count() > 0) {
        //     $vcard->CATEGORIES = $contact->tags->map(function (Tag $tag): string {
        //         return $tag->name;
        //     })->toArray();
        // }
    }
}
