<?php
namespace Vico\Managers;

use Vico\Models\Address;
use Vico\Managers\UserManager;

class AddressManager extends Manager
{
    protected $class = Address::class;

    protected $table = 'address';

    protected $fields = [
        'id', 'user_id', 'civility', 'last_name', 'first_name', 'number', 'way', 'city', 'postal_code', 'country'
    ];
    protected $q_search_fields = ['first_name', 'last_name', 'way', 'city', 'country', 'postal_code'];


    public function delete(int $id):void 
    {
        $address = $this->findOneOrNull(['id' => $id]);
        $user = ($this->helper->getManager('UserManager'))->findOneOrNull(['id' => $address->getUser_id()]);

        if($user->getDelivery_address_id() === $id)
        {
            $user->setDelivery_address_id(null);
        }
        if($user->getInvoice_address_id() === $id)
        {
            $user->setInvoice_address_id(null);
        }
        (new UserManager())->persist($user);
        parent::delete($id);
    }

    /**
     * @param User[] $users
     */
    public static function hydrateUsers(array $users):void
    {
        $usersById = [];
        foreach($users as $user)
        {
            $usersById[$user->getId()] = $user;
        }
        $addresses = parent::findAll(['user_id' => array_keys($usersById)]);
        foreach($addresses as $address)
        {
            $user = $usersById[$address->getUser_id()];
            if($user->getDelivery_address_id() === $address->getId())
            {
                $user->setDelivery_address($address);
            }
            if($user->getInvoice_address_id() === $address->getId())
            {
                $user->setInvoice_address($address);
            }
        }
    }
    public function getCities():array
    {
        $data = $this->createQueryBuilder()
                        ->select('city')
                        ->from($this->table)
                        ->fetchAllAssoc();
        $cities = [];
        foreach($data as $d)
        {
            if(!\in_array($d['city'], $cities))
            {
                $cities[] = $d['city'];
            }
        }
        return $cities;
    }
}