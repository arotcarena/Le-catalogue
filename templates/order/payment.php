
<div class="m-4">

    <h4>Adresse de livraison</h4>
    <p>
        <?= $delivery_address->toHtml() ?>
    </p>

    <h4 class>Adresse de facturation</h4>
    <p>
        <?= $invoice_address->toHtml() ?>
    </p>

</div>


<?= $invoice->toHtml() ?>

<br>
<div class="m-4">
    <h4>Entrez vos numéros de carte bancaire : </h4>
<img src="../../img/cb_logo.jpg" alt="logos-cb"/>
    <p>numéros de carte : <input type="text"></p>
    date de validité : <textarea rows="1" cols="5" ></textarea>
    cryptogramme : <textarea rows="1" cols="5" ></textarea>
</div>

<div>
<a href="<?= $router->url('order_address_check') ?>" class="btn btn-secondary m-4">Revenir à l'adresse</a>
<a href="<?= $router->url('order_processing') ?>" class="btn btn-primary">Payer</a>

</div>