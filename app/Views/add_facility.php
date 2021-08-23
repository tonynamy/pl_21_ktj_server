<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>


<form method="POST">
    <div class="uiframe" style="margin:0 auto; width:1400px">

        <div class="uigray" style="margin-top:8px; margin-bottom:16px; overflow-x:scroll;">
            <p>※엑셀파일 업로드에 앞서 주의해주세요.<br>
            엑셀파일은 반드시 아래의 양식이어야하며 B3행부터 V마지막행까지 받아옵니다.</p>

            <table class="excel" style="font-size: 9pt; margin-top:16px; margin-bottom:16px; table-layout:fixed; box-shadow: 0px 3px 8px #BBBBBB">
                <tr bgcolor="#F2F2F2" align="center">
                    <td width="16px"></td> <td width="16px">A</td> <td width="65px">B</td> <td width="35px">C</td> <td width="75px">D</td> <td width="45px">E</td>
                    <td width="60px">F</td> <td width="28px">G</td> <td width="74px">H</td> <td width="55px">I</td> <td width="85px">J</td> <td width="80px">K</td>
                    <td width="50px">L</td> <td width="200px">M</td> <td width="50px">N</td> <td width="70px">O</td> <td width="70px">P</td> <td width="70px">Q</td>
                    <td width="70px">R</td> <td width="70px">S</td> <td width="70px">T</td> <td width="70px">U</td> <td width="150px">V</td> <td width="50px">W</td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">1</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>
                    <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>
                </tr>
                <tr bgcolor="#DDEEFF" align="center">
                    <td bgcolor="#F2F2F2">2</td> <td bgcolor="#FFFFFF"></td> <td>승인번호</td> <td>공종</td> <td>담당자</td> <td>업체</td> <td>설치동</td> <td>층</td> <td>설치위치</td>
                    <td>설치구간</td> <td>설치목적</td> <td>루베산출식</td> <td>루베</td> <td>헤베산출식</td> <td>헤베</td>
                    <td>도면등록일</td> <td>설치시작일</td> <td>승인완료일</td> <td>수정시작일</td> <td>수정완료일</td> <td>해체시작일</td> <td>해체완료일</td> <td>비고</td> <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">3</td> <td></td> <td>SAM-001</td> <td>설비</td> <td>조용필 과장</td> <td>화이자</td> <td>GALAXY</td> <td>3F</td> <td>C-F/13-21열</td>
                    <td></td> <td>코골이 방지용</td> <td>5.55*7.5*7.1</td> <td>295.5</td> <td>(5.55*6)+(5.55*2.25)+(5.55*1.5)</td> <td>54.1</td> <td>2021-08-01</td>
                    <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>돌아와요 부산항에 잘부름</td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">4</td> <td></td> <td>SAM-002</td> <td>전기</td> <td>서태지 대리</td> <td>모더나</td> <td>GALAXY</td> <td>PH</td> <td>I-J/23-24열</td>
                    <td>사내</td> <td>화장실 청소용</td> <td>5.55*7.86*5</td> <td></td> <td>(5.55*7.33)+(5.55*1.86)+(1.85*1.86)</td> <td></td> <td></td>
                    <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>난알아요 잘부름</td> <td></td>
                </tr>
                <tr align="center">
                    <td bgcolor="#F2F2F2">5</td> <td></td> <td>SAM-002</td> <td>건축</td> <td>장범준 주임</td> <td>얀센</td> <td>GALAXY</td> <td>PH</td> <td>I-J/23-24열</td>
                    <td>사외</td> <td>벚꽃 축제용</td> <td>5.55*7.86*5</td> <td></td> <td>(5.55*7.33)+(5.55*1.86)+(1.85*1.86)</td> <td></td>
                    <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>벚꽃엔딩 잘부름</td> <td></td>
                </tr>
            </table>

            <p>승인번호, 설치동, 층, 설치위치는 반드시 입력되어야합니다.<br>
            공종은 설비, 전기, 건축이 아닌건 모두 기타로 입력됩니다.<br>
            날짜는 꼭 2021-08-01형식으로 입력할 것, 도면등록일은 비워두면 오늘날짜로 등록되고 나머지 날짜는 비워둬도 관계없습니다.<br>
            문자열로 등록시 승인번호부터 넣으면 복사하여 붙혀넣으면 되고, 한 행안에 띄어쓰기가 있어선 안됩니다.</p>
        </div>

        <div style="display:flex; align-items:center">
            <label style="width:130px;">엑셀파일 업로드</label>
            <button style="width:150px;" formaction="load_facility_excel">파일선택</button>
        </div>

            <div style="display:flex">
                <label style="width:130px; padding-top:8px">엑셀형식 문자열</label>
                <textarea name="excel_string" style="margin-left: 14px" wrap="off"><?=old('excel_string')?></textarea>
            </div>
        
            <div align="right">
                <button class="bluebutton" formaction="parse_facility_data" style="width:80px">등록</button>
            </div>
        
    </div>
</form>


<?= $this->endSection() ?>