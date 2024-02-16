<?php
namespace Vico\Controller;

use Vico\Response;


class ContactController extends Controller
{
    public function index():Response
    {
        return $this->render('contact.php', [
                'title' => 'Nous contacter'
            ]);
    }
}