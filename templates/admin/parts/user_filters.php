<?php

use Vico\Form;
use Vico\Managers\AddressManager;



$form = new Form($_GET);

$addressManager = new AddressManager();

$week = (new DateTime())->sub(new DateInterval('P1W'))->format('Y-m-d');
$month = (new DateTime())->sub(new DateInterval('P1M'))->format('Y-m-d');
$year = (new DateTime())->sub(new DateInterval('P1Y'))->format('Y-m-d');
?>



<div class="mt-4">


    <form action="" method="get">
        <div class="row align-items-start">
            <div class="col">
                Filtrer par dernière connexion :
                <?= $form->select('last_login_order', 'Choisissez', ['asc', 'desc'], ['du - récent au + récent', 'du + récent au - récent']) ?>
                OU <br>
                Filtrer par date d'inscription :
                <?= $form->select('confirmed_at_order', 'Choisissez', ['asc', 'desc'], ['du - récent au + récent', 'du + récent au - récent']) ?>
            </div>
            <div class="col">
                Ne pas afficher les comptes inactifs :
                <?= $form->select('last_login_min', 'Choisissez', [$week, $month, $year], ['Depuis + d\'1 semaine', 'Depuis + d\'1 mois', 'Depuis + d\'1 année']) ?>
            </div>
            <div class="col">
                Résultats par page :
                <?= $form->select('per_page', 'Choisissez', [1, 2, 3, 4, 5], ['1', '2', '3', '4', '5']) ?>
            </div>
            
        </div>
    

        <table class="table"><tbody><td></td></tbody></table>

        <div class="row mt-4">

                <div class="col">
                    Recherche :
                    <?= $form->text('q', 'par adresse e-mail, nom, adresse, etc...') ?>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Lancer la recherche</button>
                </div>
                <div class="col">

                </div>
        </div>
    </form>


</div>
<p style="color: white;"> <!-- POUR EFFACER LE 1 QUI SORT DE NULLE PART -->