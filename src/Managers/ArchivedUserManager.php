<?php
namespace Vico\Managers;

use Vico\Tools;
use Vico\Models\User;
use Vico\Managers\Manager;


class ArchivedUserManager extends Manager
{
    protected $fields = [
        'id', 'email', 'first_name', 'last_name', 'role', 'confirmed_at', 'delete_at'
    ];
    protected $table = 'archived_user';
    protected $class = User::class;

    
}