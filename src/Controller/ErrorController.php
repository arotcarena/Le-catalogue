<?php 
namespace Vico\Controller;

use Vico\Response;

class ErrorController extends Controller
{
    /**
     * url = '/erreur'  name = 'unknown_error'
     */
    public function index():Response
    {
        return $this->render('errors/unknown_error.php');
    }
}