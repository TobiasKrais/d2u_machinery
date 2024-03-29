<?php

namespace D2U_Machinery;

use Machine;
use rex;
use rex_config;
use rex_sql;

/**
 * @api
 * Job contact class
 */
class Contact
{
    /** @var int Database ID */
    public int $contact_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Picture */
    public string $picture = '';

    /** @var string Phone number */
    public string $phone = '';

    /** @var string E-Mail address */
    public string $email = '';

    /**
     * Constructor.
     * @param int $contact_id contact ID
     */
    public function __construct($contact_id = 0)
    {
        if ($contact_id > 0) {
            $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_machinery_contacts '
                    .'WHERE contact_id = '. $contact_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
            $num_rows = $result->getRows();

            if ($num_rows > 0) {
                $this->contact_id = (int) $result->getValue('contact_id');
                $this->name = stripslashes((string) $result->getValue('name'));
                $this->picture = (string) $result->getValue('picture');
                $this->phone = (string) $result->getValue('phone');
                $this->email = (string) $result->getValue('email');
            }
        }
    }

    /**
     * Deletes the object.
     */
    public function delete(): void
    {
        $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_machinery_contacts '
                .'WHERE contact_id = '. $this->contact_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
    }

     /**
      * Create an empty object instance.
      * @return Contact empty new object
      */
     public static function factory()
     {
         return new self();
     }

    /**
     * Get all contacts.
     * @return Contact[] array with Contact objects
     */
    public static function getAll()
    {
        $query = 'SELECT contact_id FROM '. rex::getTablePrefix() .'d2u_machinery_contacts ORDER BY name';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $contacts = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $contacts[(int) $result->getValue('contact_id')] = new self((int) $result->getValue('contact_id'));
            $result->next();
        }
        return $contacts;
    }

    /**
     * Get contact by e-mail address.
     * @param string $email E-Mail address
     * @return Contact|bool contact object or false
     */
    public static function getByMail($email)
    {
        $query = 'SELECT contact_id FROM '. rex::getTablePrefix() .'d2u_machinery_contacts '
                ."WHERE email = '". $email ."'";
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            return new self((int) $result->getValue('contact_id'));
        }

        return false;

    }

    /**
     * Gets the machines of the contact.
     * @return Machine[] Machines
     */
    public function getMachines()
    {
        $clang_id = (int) rex_config::get('d2u_helper', 'default_lang');
        $query = 'SELECT machine_id FROM '. rex::getTablePrefix() .'d2u_machinery_machines '
            .'WHERE contact_id = '. $this->contact_id .' ';
        $query .= 'ORDER BY name ASC';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machines[] = new Machine((int) $result->getValue('machine_id'), $clang_id);
            $result->next();
        }
        return $machines;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if no error occurs
     */
    public function save()
    {
        $saved = true;

        $pre_save_contact = new self($this->contact_id);

        if (0 === $this->contact_id || $pre_save_contact !== $this) {
            $query = rex::getTablePrefix() .'d2u_machinery_contacts SET '
                    ."email = '". $this->email ."', "
                    ."name = '". addslashes($this->name) ."', "
                    ."phone = '". $this->phone ."', "
                    ."picture = '". (str_contains($this->picture, 'noavatar.jpg') ? '' : $this->picture) ."' ";

            if (0 === $this->contact_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE contact_id = '. $this->contact_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->contact_id) {
                $this->contact_id = (int) $result->getLastId();
                $saved = !$result->hasError();
            }
        }

        return $saved;
    }
}
