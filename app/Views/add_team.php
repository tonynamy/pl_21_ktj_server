<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>


<form method="POST">
    <div class="uiframe" style="margin:0 auto; width:800px">

        <div class="uigray" style="margin-top:8px; margin-bottom:16px">
            <p>※엑셀파일 업로드에 앞서 주의해주세요.<br>
            엑셀파일은 반드시 아래의 양식이어야하며 B3행부터 D마지막행까지 받아옵니다.</p>

            <table class="excel" style="width:400px; margin-top:16px; margin-bottom:16px; box-shadow: 0px 3px 8px #BBBBBB">
                <tr bgcolor="#F2F2F2" align="center">
                    <td width="24px"></td> <td width="24px">A</td> <td width="80px">B</td> <td width="80px">C</td> <td width="150px">D</td> <td>E</td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">1</td> <td></td> <td></td> <td></td> <td></td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">2</td> <td></td> <td bgcolor="#DDEEFF">팀이름</td> <td bgcolor="#DDEEFF">팀원</td> <td bgcolor="#DDEEFF">주민번호</td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">3</td> <td></td> <td rowspan="2">홍길동팀</td> <td>홍길동</td> <td>770826-1******</td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">4</td> <td></td>                              <td>차바위</td> <td>921126-1******</td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">5</td> <td></td> <td rowspan="2">심청이팀</td> <td>심청이</td> <td>970109-2******</td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">6</td> <td></td>                               <td>심봉사</td> <td>610228-2******</td> <td></td>
                </tr>
            </table>

            <p>팀이름, 팀원, 주민번호 모두 반드시 입력되어야합니다.<br>
            주민번호는 앞6자리만 불러오기 때문에 따로 편집할 필요없습니다.</p>
        </div>

        <div style="display:flex; align-items:center">
            <label style="width:130px;">엑셀파일 업로드</label>
            <button style="width:150px;" formaction="load_team_excel">파일선택</button>
        </div>

            <div style="display:flex">
                <label style="width:130px; padding-top:8px">엑셀형식 문자열</label>
                <textarea name="excel_string" style="margin-left: 20px" wrap="off"><?=old('excel_string')?></textarea>
            </div>
        
            <div align="right">
                <button class="bluebutton" formaction="parse_team_data" style="width:80px">등록</button>
            </div>
        
    </div>
</form>


<?= $this->endSection() ?>