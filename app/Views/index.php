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
            <label style="width:130px">아이디</label>
            <input type="text" name="name" placeholder="이름" value="<?= old('name'); ?>">
        </div>

        <div style="display:flex; align-items:center">
            <label style="width:130px">패스워드</label>
            <input type="password" name="birthday" placeholder="생년월일 (예 740101)" value="<?= old('birthday'); ?>">
        </div>

        <div style="display:flex; justify-content:flex-end; align-items:flex-end; text-align:bottom;">
            <a href="fm/create_user" style="color:#5599DD; margin-right:24px; margin-bottom:4px">사용자생성</a>
            <button class="bluebutton" type="submit" formaction="fm/login" style="width:80px">로그인</button>
        </div>

    </div>
    
</form>

<?= $this->endSection() ?>