<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>


<form method="POST">

    <div class="uiframe" style="margin:0 auto; width:400px">

        <div style="display:flex; align-items:center">
            <label style="width:130px">현장</label>
            <select class="ui fluid dropdown" name="place">
                <option value="-1">현장명</option>

                <?php $old_place = old('place') ?>
                <?php foreach($places as $place) : ?>

                <option value="<?=$place['id']?>" <?= $place['id'] == $old_place ? "SELECTED" : "" ?> > <?= $place['name'] ?> </option>

                <?php endforeach ?>

            </select>
        </div>
        
        <div style="display:flex; align-items:center">
            <label style="width:130px">이름</label>
            <input type="text" name="name" value="<?= old('name'); ?>">
        </div>
        

        <div style="margin-top:8px">
            <label style="width:130px">생년월일</label>
            <div class="ui calendar" id="standard_calendar" data-type="date" data-date="1971-01-01">
            </div>
        </div>

        <div align="right">
            <button class="bluebutton" type="submit" style="width:80px">생성</button>
        </div>

    </div>
    
</form>

<?= $this->endSection() ?>