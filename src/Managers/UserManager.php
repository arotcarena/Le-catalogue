<?php
namespace Vico\Managers;

use Vico\Tools;
use Vico\Config;
use Vico\UrlHelper;
use Vico\Pagination;
use Vico\Models\User;
use Vico\Notification;
use Vico\QueryBuilder;
use Vico\Models\Address;
use Vico\Managers\Manager;
use Vico\Models\UserFilter;
use Vico\Managers\AddressManager;

class UserManager extends Manager
{
    protected $fields = [
        'id', 'email', 'first_name', 'last_name', 'password', 'role', 'invoice_address_id', 'delivery_address_id', 'confirmation_token', 'confirmation_token_expire', 'remember_token', 'choice_2FA', 'code_2FA', 'code_2FA_expire', 'confirmed_at', 'last_login', 'inactive'
    ];
    protected $to_hash = ['password'];
    public $q_search_fields = ['email', 'first_name', 'last_name'];

    protected $table = 'user';
    protected $class = User::class;


    /**
     * @return string message pour l'utilisateur
     */
    public function sendCode2FA(User $user):string
    {
        $code = Tools::numeric_code(6);
        $user->setCode_2FA($code)
            ->setCode_2FA_expire(time() + Config::CODE_2FA_EXPIRE);
        $this->persist($user);
        (new Notification())->send2FA($user->getEmail(), $code);
        return 'Entrez le code reçu à l\'adresse '.$user->getEmail().'. Valable 5 min';
    }

    public function sendWelcomeToken(User $user, $router)
    {
        $token = Tools::token(60);
        $user->setConfirmation_token($token)
            ->setConfirmation_token_expire((new \DateTime())->add(new \DateInterval('P2D'))->format('Y-m-d H:i:s'));
        $this->persist($user);
        $link = $router->url('confirm_account').'?id='.$user->getId().'&token='.$token;

        (new Notification())->welcomeEmail($user->getEmail(), $link);
    }

    public function remember(User $user):void
    {
        $remember_token = Tools::token(250);
        $user->setRemember_token($remember_token);
        $this->persist($user);
        \setcookie('remember', $user->getId().'=='.$remember_token.'-secret-code-0000', time() + 3600 * 48);
    }

    public function verify_token(array $data):bool
    {
        if(isset($data['id']) AND isset($data['token']))
        {
            $user = $this->findOneOrNull(['id' => $_GET['id']]);
            if($user AND $user->getConfirmation_token() === $data['token'])
            {
                if((new \DateTime())->format('Y-m-d H:i:s') < $user->getConfirmation_token_expire())
                {
                    return true;
                }
            }
        }
        return false;
    }
    public function findOneOrNull(array $filters, ?string $key_word = null):?User
    {
        $user = parent::findOneOrNull($filters, $key_word); 
        if($user)
        {
            $addresses = (new AddressManager())->findAll(['user_id' => $user->getId()]);
            $addressesById = [];
            foreach($addresses as $address)
            {
                $addressesById[$address->getId()] = $address;
            }
            return $user->setAddresses($addresses)
                        ->setDelivery_address($addressesById[$user->getDelivery_address_id()] ?? null)
                        ->setInvoice_address($addressesById[$user->getInvoice_address_id()] ?? null);
        }
        return null;
    }




    public function findPaginated(?array $filters = null, ?string $key_word = 'and'):Pagination
    {
        if(empty($filters))
        {
            $filters['last_login_order'] = 'desc';
        }
        if(!empty($filters['last_login_order']) AND !empty($filters['confirmed_at_order']))
        {
            header('Location: '.(new UrlHelper())->modif_get($_SERVER['REQUEST_URI'], null, ['confirmed_at_order']));
            exit();
        }
        $per_page = 5;
        if(!empty($filters['per_page']))
        {
            $per_page = $filters['per_page'];
        }

        $queryBuilder = $this->createQueryBuilder()
                            ->select('DISTINCT u.*')
                            ->from('user u')
                            ->join('address a', 'LEFT JOIN')
                            ->on('u.invoice_address_id = a.id')
                            ->on('u.delivery_address_id = a.id')
                            ->on_keyWord('or');
        if(!empty($filters))
        {
            $queryBuilder->filters($filters, $this->fields, 'u')
                            ->filters($filters, $this->get_fields(AddressManager::class), 'a')
                            ->where_keyWord($key_word);
            if(!empty($filters['q']))
            {
                $queryBuilder->qSearch($filters['q'], $this->q_search_fields, 'u')
                            ->qSearch($filters['q'], $this->getQ_search_fields(AddressManager::class), 'a');
            }
        }                   

        $pagination = $this->createPagination($queryBuilder->getQuery(), $per_page)
                            ->fetchClass($this->class);

        if(!empty($pagination->getItems()))
        {  
            AddressManager::hydrateUsers($pagination->getItems());
        }
        return $pagination;
    }

    public function delete(int $id):void 
    {
        //on supprime les adresses qui correspondent a cet utilisateur
        ($this->helper->getManager('AddressManager'))->deleteAll(['user_id' => $_SESSION['id']]);
        //puis l'utilisateur lui-même
        parent::delete($id);
    }



}