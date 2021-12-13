<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe"  style="width:800px">
            
                <div style="padding:16px;">

                    <form >
                        <i class="arrow left icon" onclick="history.back()" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                        <label>종합보고서 다운로드</label>

                        <div style="margin-top:32px"><?= $place_name ?> 현장의 생산성과 안전점수기록을 엑셀파일로 저장하시겠습니까?</div>
                        <div style="display:flex; align-items:center; margin-top:16px">
                            <span>기간: 최근&nbsp;</span>
                            <input type="number" min="1" name="period" value="1" style="width:90px;">
                            <span>&nbsp;달</span>
                        </div>
                        <div style="text-align:right;">
                            <button class="bluebutton" type="submit" style="width:150px;" onclick="location.href='/fm/add_team'">다운로드</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
        
    </form>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        

    });

</script>

<?= $this->endSection() ?>
