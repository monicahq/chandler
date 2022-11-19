<?php

namespace App\Domains\Contact\Dav\Web\Backend\CardDAV;

use Sabre\CardDAV\AddressBookHome as BaseAddressBookHome;

class AddressBookHome extends BaseAddressBookHome
{
    /**
     * Returns a list of ACE's for this node.
     *
     * Each ACE has the following properties:
     *   * 'privilege', a string such as {DAV:}read or {DAV:}write. These are
     *     currently the only supported privileges
     *   * 'principal', a url to the principal who owns the node
     *   * 'protected' (optional), indicating that this ACE is not allowed to
     *      be updated.
     *
     * @return array
     */
    public function getACL(): array
    {
        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => '{DAV:}owner',
                'protected' => true,
            ],
        ];
    }

    /**
     * Principal uri.
     *
     * @return string
     */
    private function getPrincipalUri(): string
    {
        /** @var string $principalUri */
        $principalUri = $this->principalUri;

        return $principalUri;
    }

    /**
     * Returns a list of addressbooks.
     *
     * @return array
     */
    public function getChildren(): array
    {
        return collect($this->carddavBackend->getAddressBooksForUser($this->getPrincipalUri()))
            ->map(fn ($addressBookInfo) => new AddressBook($this->carddavBackend, $addressBookInfo))
            ->toArray();
    }
}
