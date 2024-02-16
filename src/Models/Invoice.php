<?php
namespace Vico\Models;

use Vico\Tools;
use Vico\Models\Address;
use Vico\Models\Product;

class Invoice
{
    private $id;

    private $user_id;

    private $details;

    private $total_price;

    private $invoice_date;

    private $delivery_date;

    private $status;

    private $delivery_address;

    private $invoice_address;

    private $mail_labels = [
        1 => ['object' => 'Commande en préparation', 'title' => 'Votre commande est en préparation. '],
        2 => ['object' => 'Commande expédiée', 'title' => 'Votre commande est expédiée. '],
        3 => ['object' => 'Commande livrée', 'title' => 'Votre commande a été livrée. ']
    ];

    public function setId(string $id):self
    {
        $this->id = $id;
        return $this;         
    }
    public function setUser_id(int $user_id):self
    {
        $this->user_id = $user_id;
        return $this;         
    }
    public function setInvoice_date(string $invoice_date):self
    {
        $this->invoice_date = $invoice_date;
        return $this;         
    }

    public function setDetails(string $details):self
    {
        $this->details = $details;
        return $this;         
    }
    public function setTotal_price(string $total_price):self
    {
        $this->total_price = $total_price; 
        return $this;
    }        
    public function setDelivery_date(string $delivery_date):self
    {
        $this->delivery_date = $delivery_date;
        return $this;
    }
    public function setDelivery_address(string $delivery_address):self
    {
        $this->delivery_address = $delivery_address;
        return $this;
    }
    public function setInvoice_address(string $invoice_address):self
    {
        $this->invoice_address = $invoice_address;
        return $this;
    }
    public function setStatus(string $status):self
    {
        $this->status = $status;
        return $this;
    }

    public function getId():string
    {
        return $this->id;
    }
    public function getUser_id():string
    {
        return $this->user_id;
    }
    public function getDelivery_address():string
    {
        return $this->delivery_address;
    }
    public function getInvoice_address():string
    {
        return $this->invoice_address;
    }
    public function getDetails():string
    {
        return $this->details;
    }
    public function getTotal_price():string
    {
        return $this->total_price;
    }
    public function getInvoice_date():string 
    {
        return $this->invoice_date;
    }
    public function getDelivery_date():string 
    {
        return $this->delivery_date;
    }
    public function getStatus():int
    {
        return $this->status;
    }
    public function getStatusFormated():string 
    {
        if((int)$this->status === 1)
        {
            return 'en préparation';
        }
        elseif((int)$this->status === 2)
        {
            return 'expédiée';
        }
        elseif((int) $this->status === 3)
        {
            return 'livrée';
        }
    }
    public function getTotal_price_formated():string
    {
        return \number_format($this->total_price, 0, '', ' '). ' €';
    }
    public function getInvoice_date_formated():string 
    {
        return (new \DateTime($this->invoice_date))->format('d/m/Y à H\hi');
    }
    public function getDelivery_date_formated():string 
    {
        if((int)$this->status === 3)
        {
            return (new \DateTime($this->delivery_date))->format('d/m/Y');
        }
        else
        {
            return 'Prévue le '.(new \DateTime($this->delivery_date))->format('d/m/Y');
        }
    }

    
    public function toHtml():string
    {

        if($this->id === null)//alors il s'agit d'une preview
        {
            return <<<HTML
                <div class="m-4">
                    <h5>Récapitulatif de la commande : </h5>
                        <table class="table table-striped">
                        {$this->getDetails_formated()}
                        </table>
                    <h5>Prix total : {$this->getTotal_price_formated()}</h5>
                    <h5>Livraison : {$this->getDelivery_date_formated()}</h5>
                </div>
                HTML;
        }
        //sinon on veut la facture complète

        $delivery_address = new Address();
        $invoice_address = new Address();
        Tools::hydrate($delivery_address, Tools::decode($this->delivery_address)[0]);
        Tools::hydrate($invoice_address, Tools::decode($this->invoice_address)[0]);

        return <<<HTML
                <div class="m-4">
                    <h4>Commande n° {$this->id}</h4> 
                    <h5>Date : {$this->getInvoice_date_formated()}</h5>
                    Détail de la commande :
                    {$this->getDetails_formated()}
                    <h5>Prix total : {$this->getTotal_price_formated()}</h5>
                    <div class="row align-items-start">
                        <div class="col">
                        <h6>Adresse de livraison</h6>
                            {$delivery_address->toHtml()}
                        </div>
                        <div class="col">
                        <h6>Adresse de facturation</h6>
                            {$invoice_address->toHtml()}
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    <h5 class="mt-2">Livraison : {$this->getDelivery_date_formated()}<br>
                        Status  : {$this->getStatusFormated()}</h5>
                </div>
                HTML;

    }

    public function toMail()
    {
        $delivery_address = new Address();
        $invoice_address = new Address();
        Tools::hydrate($delivery_address, Tools::decode($this->delivery_address)[0]);
        Tools::hydrate($invoice_address, Tools::decode($this->invoice_address)[0]);

        
        return <<<HTML
                    {$this->mail_labels[$this->status]['title']}  Veuillez trouver les détails ci-dessous : \n\n\n

                    N° de commande :  {$this->id} \n
                    Date : {$this->getInvoice_date_formated()} \n\n

                    Détail de la commande :    \n
                    on verra plus tard  \n\n\n

                    Prix total : {$this->getTotal_price_formated()}   \n\n\n
                    
                        Adresse de livraison \n\n
                        {$delivery_address->getCivility()} {$delivery_address->getFirst_name()} {$delivery_address->getLast_name()}   \n
                        {$delivery_address->getNumber()} {$delivery_address->getWay()} \n
                        {$delivery_address->getPostal_code()} {$delivery_address->getCity()} \n
                        {$delivery_address->getCountry()} \n\n

                        Adresse de facturation \n\n
                        {$invoice_address->getCivility()} {$invoice_address->getFirst_name()} {$delivery_address->getLast_name()}   \n
                        {$invoice_address->getNumber()} {$invoice_address->getWay()} \n
                        {$invoice_address->getPostal_code()} {$invoice_address->getCity()} \n
                        {$invoice_address->getCountry()} \n\n\n
                        
                        Livraison : {$this->getDelivery_date_formated()} \n
                HTML;
    }
    public function getEmailObject()
    {
        return $this->mail_labels[$this->status]['object'];
    }
    public function getEmailTitle()
    {
        return $this->mail_labels[$this->status]['title'];
    }
    

    private function getDetails_formated():string 
    {
        $html = <<<HTML
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Marque / Modèle</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                HTML;
        foreach(Tools::decode($this->details) as $detail)
        {
            $html .= <<<HTML
                    <tr>
                        <td>{$detail['brand']} {$detail['model']}</td>
                        <td>{$detail['priceFormated']}</td>
                        <td>{$detail['quantity']}</td>
                        <td>{$detail['totalFormated']}</td>
                    </tr>
                    HTML;
        }
        return $html .= '</tbody></table>';
    }

    
}