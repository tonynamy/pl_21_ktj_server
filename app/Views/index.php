<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form class="ui form" action="/login" method="POST">

    <div class="field">
        <label>현장명</label>
        <select class="ui fluid search dropdown" name="place">
            <option value="-1">현장명</option>

            <?php $old_place = old('place') ?>
            <?php foreach($places as $place) : ?>

            <option value="<?=$place['id']?>" <?= $place['id']==$old_place ? "selected" : "" ?>><?=$place['name']?></option>

            <?php endforeach ?>

        </select>
    </div>

    <div class="field">
        <label>이름</label>
        <input type="text" name="name" placeholder="이름" value="<?=old('name');?>">
    </div>

    <div class="field">
        <label>생년월일</label>
        <input type="text" name="birthday" placeholder="생년월일"  value="<?=old('birthday');?>">
    </div>

    <button class="ui button" type="submit">로그인</button>

</form>

<?= $this->endSection() ?>