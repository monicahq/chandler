<?php

namespace App\Domains\Contact\Dav\Web\Controllers\Backend\CardDAV;

use Sabre\CardDAV\AddressBook as BaseAddressBook;

class AddressBook extends BaseAddressBook
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
            [
                'privilege' => '{DAV:}write-content',
                'principal' => '{DAV:}owner',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}bind',
                'principal' => '{DAV:}owner',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}unbind',
                'principal' => '{DAV:}owner',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}write-properties',
                'principal' => '{DAV:}owner',
                'protected' => true,
            ],
        ];
    }

    /**
     * This method returns the ACL's for card nodes in this address book.
     * The result of this method automatically gets passed to the
     * card nodes in this address book.
     *
     * @return array
     */
    public function getChildACL()
    {
        return $this->getACL();
    }

    /**
     * Returns the last modification date.
     *
     * @return int|null
     */
    public function getLastModified(): ?int
    {
        $carddavBackend = $this->carddavBackend;
        if ($carddavBackend instanceof CardDAVBackend) {
            $date = $carddavBackend->getLastModified($this->addressBookInfo['id']);
            if (! is_null($date)) {
                return (int) $date->timestamp;
            }
        }

        return null;
    }

    /**
     * This method returns the current sync-token for this collection.
     * This can be any string.
     *
     * If null is returned from this function, the plugin assumes there's no
     * sync information available.
     *
     * @return string|null
     */
    public function getSyncToken(): ?string
    {
        if ($this->carddavBackend instanceof CardDAVBackend) {
            return (string) $this->carddavBackend->refreshSyncToken($this->addressBookInfo['id'])->id;
        }

        return null;
    }
}
