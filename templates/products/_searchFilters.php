









<?= $filter_form->start() ?>

    <div class="row">
        <div class="col">
            <?= $filter_form->row('category_id') ?>
        </div>
        <div class="col">
            <?= $filter_form->row('brand') ?>
        </div>
        <div class="col">
            <?= $filter_form->row('price_order') ?>
        </div>
        <div class="col">
            <?= $filter_form->row('price_max') ?>
            <?= $filter_form->row('price_min') ?>
        </div>
        <div class="col">
            <?= $filter_form->row('per_page') ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <?= $filter_form->row('q') ?>
        </div>
        <div class="col">
            <?= $filter_form->btn_row() ?>
        </div>
        <div class="col">
        </div>
    </div>
    
<?= $filter_form->end() ?>