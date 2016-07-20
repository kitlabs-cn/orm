<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Tests\Models\Quote\User;
use Doctrine\Tests\Models\Quote\Address;

/**
 * @group DDC-1845
 * @group DDC-142
 */
class DDC142Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    protected function setUp() : void
    {
        parent::setUp();
    }

    public function testCreateRetrieveUpdateDelete()
    {

        $user           = new User;
        $user->name     = 'FabioBatSilva';
        $this->_em->persist($user);

        $address        = new Address;
        $address->zip   = '12345';
        $this->_em->persist($address);

        $this->_em->flush();

        $addressRef = $this->_em->getReference(Address::class, $address->getId());

        $user->setAddress($addressRef);

        $this->_em->flush();
        $this->_em->clear();

        $id = $user->id;
        self::assertNotNull($id);


        $user       = $this->_em->find(User::class, $id);
        $address    = $user->getAddress();

        self::assertInstanceOf(User::class, $user);
        self::assertInstanceOf(Address::class, $user->getAddress());

        self::assertEquals('FabioBatSilva', $user->name);
        self::assertEquals('12345', $address->zip);


        $user->name     = 'FabioBatSilva1';
        $user->address  = null;

        $this->_em->persist($user);
        $this->_em->remove($address);
        $this->_em->flush();
        $this->_em->clear();


        $user = $this->_em->find(User::class, $id);
        self::assertInstanceOf(User::class, $user);
        self::assertNull($user->getAddress());

        self::assertEquals('FabioBatSilva1', $user->name);

        $this->_em->remove($user);
        $this->_em->flush();
        $this->_em->clear();

        self::assertNull($this->_em->find(User::class, $id));
    }

}
