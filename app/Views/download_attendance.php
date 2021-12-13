<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:1450px">
            
                <div style="padding:16px;">

                    <form >
                        <i class="arrow left icon" onclick="history.back()" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                        <label>출퇴근기록 다운로드</label>

                        <div style="align-items:center; margin-top:32px">

                            <select id="team_select" class="ui dropdown" name="team_id">

                                <?php foreach($teams as $team) : ?>

                                    <option value="<?=$team['id']?>" <?= $team['id'] == $old_team_id ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                                <?php endforeach ?>

                            </select>
                            <span>의 출퇴근기록을 엑셀파일로 저장하시겠습니까?</span>

                        </div>

                        
                        <div style="display:flex; align-items:center; margin-top:16px">
                            <span>기간: 최근&nbsp;</span>
                            <input type="number" min="1" name="weeks" value="1" style="width:90px;">
                            <span>&nbsp;주</span>
                        </div>
                        <div style="text-align:right;">
                            <button class="bluebutton" type="submit" style="width:150px;">다운로드</button>
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
