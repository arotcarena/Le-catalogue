

<?php if(isset($_GET['update'])): ?>
    <div class="alert alert-success m-4">Vos informations personnelles ont bien été modifiées.</div>
<?php elseif(isset($_GET['update_password'])): ?>
    <div class="alert alert-success m-4">Votre mot de passe a bien été modifié.</div>
<?php endif ?>

<div class="m-4">
    <p>Nom : <strong><?= $user->getLast_name() ?></strong></p>
    <p>Prénom : <strong><?= $user->getFirst_name() ?></strong></p>
    <p>Adresse e-mail : <strong><?= $user->getEmail() ?></strong></p>
    <p>Date d'inscription : <strong>Le <?= $user->getConfirmed_at_formated() ?></strong></p>
</div>

<div class="m-4">
    <a href="<?= $router->url('update_user') ?>" class="btn btn-primary">Modifier les informations</a>
    <a href="<?= $router->url('update_password') ?>" class="btn btn-primary">Changer le mot de passe</a>
    <form action="<?= $router->url('delete_account') ?>" method="post" class="mt-2">
        <button type="submit" class="btn btn-secondary">Fermer mon compte</button>
    </form>
</div>


