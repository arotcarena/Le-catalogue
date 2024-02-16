





<?php if(isset($_SESSION['flash'])): ?>
    <?php foreach($_SESSION['flash'] as $class => $message): ?> 
        <div class="alert alert-<?= $class ?>"><?= $message ?></div>
    <?php endforeach ?>
    <?php unset($_SESSION['flash']) ?>
<?php endif ?>


<ul class="container" style="color: green;">
    

</ul>
    





