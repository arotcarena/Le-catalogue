<?php
use Vico\Nav;
use Vico\Auth;
use Vico\Form;
use Vico\Managers\UserManager;
use Vico\Managers\InvoiceManager;
use Vico\Validators\InvoiceValidator;


Auth::check('admin');

$invoiceManager = new InvoiceManager();


$errors = [];
$success = false;

if(!empty($_POST))
{
    $v = new InvoiceValidator($_POST);

    if($v->validate())
    { 
        if(isset($_GET['invoice_id']))
        {
            $invoiceManager->insert($_POST, ['id' => $_GET['invoice_id']]);
            $invoice = $invoiceManager->findOneOrNull(['id' => $_GET['invoice_id']]);
            $user = (new UserManager())->findOneOrNull(['id' => $invoice->getUser_id()]);
            mail($user->getEmail(), $invoice->getEmailObject(), $invoice->toMail());
            $success = true;
        }
        else
        {
            throw new Exception("Impossible de mettre a jour la facture car pas d'identifiant précisé", 1);
        }
    }
    else
    {
        $errors = $v->errors();
    }

}

$pagination = $invoiceManager->findPaginated($_GET);

$current_status = (int)($_GET['status'] ?? 0);
?>






<?php if($success): ?>
    <div class="alert alert-success">Les informations d'expédition ont bien été modifiées</div>
<?php endif ?>



<nav class="navbar navbar-expand mb-4">
    <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <?php $style = $current_status === 0 ? 'font-weight: bold;': '' ?>
                <?= Nav::link($router->url('admin_orders'), 'Toutes les commandes ('.$invoiceManager->count().')', 'match_3', '" style="'.$style.'"') ?>
            </li>
        <?php foreach([1 => 'en préparation', 2 => 'expédiées', 3 => 'livrées'] as $status => $label): ?>
            <?php $style = $current_status === $status ? 'font-weight: bold;': '' ?>
            <li class="nav-item">
                <?= Nav::link($router->url('admin_orders').'?status='.$status, 'Commandes '.$label.' ('.$invoiceManager->count(['status' => $status]).')', 'match_3', '" style="'.$style.'"') ?>
            </li>
        <?php endforeach ?>
    </ul>
</nav>



<?= $pagination->links() ?>

<?php foreach($invoices as $invoice): ?>

<?php $form = new Form($invoice, $errors) ?>

    <div class="row align-items-center">
        <div class="col">
            <?= $invoice->toHtml() ?>
            <form class="form m-4" action="?invoice_id=<?= $invoice->getId() ?>" method="post" onsubmit="return confirm('Voulez-vous vraiment mettre a jour les informations ? Un email sera envoyé au client')">
                <div class="form-group">Livraison : 
                    <?= $form->text('delivery_date', '') ?>
                </div>
                <div class="form-group">Status : 
                    <?= $form->number('status', '') ?>
                </div>
                <button class="btn btn-primary" type="submit">Modifier</button>
            </form>
        </div>
        <div class="col">
            <a href="<?= $router->url('admin_users_single', ['id' => $invoice->getUser_id(), 'status' => 0]) ?>" class="btn btn-outline-primary">Voir le client <br>#id <?= $invoice->getUser_id() ?></a>
        </div>
    </div>
    <br>
<?php endforeach ?>
    

<?= $pagination->links() ?>

