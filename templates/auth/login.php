


<h3>Connexion</h3>


<?= $login_form->view() ?>



<div class="container mt-4 mb-4">
    <a href="<?= $url_helper->modif_get($router->url('forgot_password'), null, null, ['target']) ?>">Mot de passe oublié ? cliquez ici</a><br>
    <a href="<?= $url_helper->modif_get($router->url('signin'), null, null, ['target']) ?>">Pas encore de compte ? cliquez ici pour vous inscrire</a>
</div>