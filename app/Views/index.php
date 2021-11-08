<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:400px; padding:16px;">

            <div style="height:150px; text-align:center; padding-top:16px;">
                <img src="/static/fmenc_logo.jpg">
            </div>
        
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <label>현장</label>
                <div style="width:270px">
                    <select class="ui fluid dropdown" name="place">

                        <option value="">현장명</option>

                        <?php $old_place = old('place') ?>

                        <?php foreach($places as $place) : ?>

                            <option value="<?=$place['id']?>"    <?= $place['id'] == $old_place ? "SELECTED" : "" ?>    > <?= $place['name'] ?> </option>

                        <?php endforeach ?>

                    </select>
                </div>
            
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <label>아이디</label>
                <input type="text" name="name" placeholder="이름" value="<?= old('name'); ?>" style="width:270px">
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <label>패스워드</label>
                <input type="password" name="birthday" placeholder="생년월일 (예 740101)" value="<?= old('birthday'); ?>" style="width:270px">
            </div>

            <div style="display:flex; justify-content:flex-end; align-items:flex-end; text-align:bottom;">
                <a href="fm/create_user" style="color:#5599DD; margin-right:24px; margin-bottom:4px">사용자생성</a>
                <button class="bluebutton" type="submit" formaction="fm/login" style="width:80px">로그인</button>
            </div>

        </div>
        
    </div>
    <div id="credit"></div>
    
</form>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {

        $('#credit').html('Developed by 푸고소프트');
        $('#credit').width('fit-content');
        $('#credit').css({
            'position': 'absolute',
            'top': $(document).height() - document.getElementById('credit').offsetHeight + 'px',
            'left': $(document).width() - document.getElementById('credit').offsetWidth + 'px',
        });
    });

</script>

<?= $this->endSection() ?>