<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form class="ui form" action="" method="POST">

    <div class="field">
        <label>엑셀 형식 문자열</label>
        <textarea name="excel_string"><?=old('excel_string')?></textarea>
    </div>

    <button class="ui button" type="submit">팀 등록</button>

</form>


<?= $this->endSection() ?>