<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>


<form method="POST">
    <div class="ui segment" style="margin:0 auto; width:800px">

        <div class="field" style="display:flex; align-items:center">
            <label style="width:130px;">엑셀파일 업로드</label>
            <button id="gray_button" style="width:150px;" formaction="load_excel">파일선택</button>
        </div>

            <div class="field" style="display:flex">
                <label style="width:130px; padding-top:8px">엑셀형식 문자열</label>
                <textarea name="excel_string"><?=old('excel_string')?></textarea>
            </div>
        
            <div class="field" align="right">
                <button class="ui button" formaction="parse_team_data">등록</button>
            </div>
        
    </div>
</form>


<?= $this->endSection() ?>