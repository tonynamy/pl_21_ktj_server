<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST" action="/fm/delete_place">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:400px; padding:16px;"">

                <div>
                    <i class="arrow left icon" onclick="history.back()" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                </div>

                <div style="margin-top:16px; margin-bottom:16px">
                    <?= $place['name'] ?> 현장에는 아래와 같은 정보가 있습니다.
                </div>

                <?php if(in_array("facility", $exist_data)) : ?>
                    <div>ㆍ도면정보</div>
                <?php endif ?>

                <?php if(in_array("task", $exist_data)) : ?>
                    <div>ㆍ작업기록</div>
                <?php endif ?>
                
                <?php if(in_array("team", $exist_data)) : ?>
                    <div>ㆍ팀정보</div>
                <?php endif ?>
                
                <?php if(in_array("user", $exist_data)) : ?>
                    <div>ㆍ직원정보</div>
                <?php endif ?>

                <div style="margin-top:16px; margin-bottom:16px">
                    ※삭제하면 모든 기록이 삭제되며 복구가 불가합니다.
                </div>


                <div align="right">
                    <button id="place_add" class="bluebutton" type="submit" style="width:100px">현장 삭제</button>
                </div>

                <input type="hidden" name="place_id" value="<?= $place['id'] ?>">

            </div>
        </div>
        
    </form>
    
<?= $this->endSection() ?>