<?php

namespace App\Domains\Contact\Dav\Web\Controllers\Backend\CardDAV;

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
    public function getACL()
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
     * Returns a list of addressbooks.
     *
     * @return array
     */
    public function getChildren()
    {
        return collect($this->carddavBackend->getAddressBooksForUser($this->principalUri))
            ->map(fn ($addressBookInfo) => new AddressBook($this->carddavBackend, $addressBookInfo))
            ->toArray();
    }
}
