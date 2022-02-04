<div class="card">
    <div class="card-header">
      <?= $title ?>
    </div>
    <div class="card-body">
        <p class="card-text"><?= $text ?></p>
      <?= $grid ?>
        <pre class="code collapse" id="<?= $codeId ?>"><?= $code ?></pre>
    </div>
    <div class="card-footer bg-transparent border-light">
        <a href="#" class="btn btn-primary">Resolve ...</a>
        <a class="btn btn-primary" data-toggle="collapse" href="#<?= $codeId ?>" role="button"
           aria-expanded="false" aria-controls="collapseExample">
            <?=$showButtonLabel?>
        </a>
    </div>
</div>