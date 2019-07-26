<html><head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons|Pinyon+Script|Playball|Jua|Nanum+Gothic">
  <link rel="stylesheet" href="/assets/font-awesome/latest/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

  <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css">
  <link href="/assets/material-kit/assets/css/material-kit2.css?v=1.2.2" rel="stylesheet">
  <link href="/assets/material-kit/assets/css/color.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="/assets/zcast.css">

  <style type="text/css">
  body{
    font-family: 'Nanum Gothic',"Helvetica Neue",Helvetica,Arial,sans-serif;
  }
  .page-header {
    height: 350px;
  }
  .table > thead > tr > th {
    font-size: 1.2em;
    text-align: center;
}
  .c-modal-dialog {
      margin: 30px auto;
      width: 90%;
      max-width: 1000px;
  }

  .float-left {float: left!important;}
  .float-right {float: right!important;}
  .ml-auto, .mx-auto {margin-left: auto!important;float:none;}
  .mr-auto, .mx-auto {margin-right: auto!important;float:none;}
  .btn.btn-fab, .btn.btn-just-icon {font-size: 24px;height: 41px;min-width: 41px;width: 41px;padding: 0;overflow: hidden;position: relative;line-height: 41px;}
  .img-fluid, .img-thumbnail {max-width: 100%;height: auto;}
  .rounded-circle {border-radius: 50%!important;}
  .img-raised {box-shadow: 0 5px 15px -8px rgba(0,0,0,.24), 0 8px 10px -5px rgba(0,0,0,.2);}
  .navbar-dark {color: #fff;background-color: #546e7a;box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 12px -5px rgba(33,33,33,.46);  }
  .bg-dark {color: #fff;background-color: #212121!important;box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 12px -5px rgba(33,33,33,.46);}
  img{max-width: 100%;}
  .form-group.note-group-image-url{display:none}
  .profile-page .profile { text-align: center;}


  .profile-page .profile img {
    max-width: 160px;
    width: 100%;
    margin: 0 auto;
    transform: translate3d(0,-50%,0);
  }
  .profile-page .follow {
    position: absolute;
    top: 0;
    right: 0;
}
.justiconboard{
  font-size: 24px;
  height: 41px;
  min-width: 41px;
  width: 41px;
  padding: 0;
  overflow: hidden;
  position: relative;
  line-height: 41px;
}
  .view_btn {    font-size: 24px;    padding: 0px 10px;    margin-right: 10px;}
footer {    margin-top: 20px;}
footer ul li a  {
  color: inherit;
  padding: .9375rem;
  font-weight: 500;
  font-size: 12px;
  text-transform: uppercase;
  border-radius: 3px;
  position: relative;
  display: block;
  }

  .avatar{
    position: relative;
  }
  .avatar_title{    position: absolute;
    color:#353535;
    bottom: 90px;
    font-size: 20px;
    font-weight: 600;
    left: 50%;
    transform: translate3d(-50%,-50%,0);
}
.modal-backdrop.in {
    filter: alpha(opacity=50);
    opacity: 0;
    width: 0;
    height: 0;
}

.container {
    max-width: 800px;
}
  </style>

  <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/material.min.js"></script>
  <script src="/assets/js/moment.min.js"></script>
  <script src="/assets/js/nouislider.min.js" type="text/javascript"></script>
  <script src="/assets/js/bootstrap-datetimepicker.js" type="text/javascript"></script>
  <script src="/assets/js/bootstrap-selectpicker.js" type="text/javascript"></script>
  <script src="/assets/js/bootstrap-tagsinput.js"></script>
  <script src="/assets/js/jasny-bootstrap.min.js"></script>
  <script src="/assets/js/jquery.flexisel.js"></script>

  <script src="/assets/material-kit/assets/js/material-kit2.js?v=1.2.2" type="text/javascript"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

  <title>캐스트 작성</title>
</head>
<body>

  

<div class="page-header header-filter" style="background-image: url('/assets/img/architecture-buildings-city-311012.jpg');">
	<div class="container">

	</div>
</div>
<div class="main main-raised">
  <div class="container">
        <div class="profile-page">
          <div class="row">
            <div class="col-md-6 ml-auto mr-auto">
                    <div class="profile">
                            <div class="avatar">
                                <img src="/assets/img/castlogo.png" alt="Circle Image" class="img-raised rounded-circle img-fluid" style="background-color: #ff9800;">
                                <span class="avatar_title">캐스트</span>
                            </div>
                            <div class="name">
                                    <h3 class="title">CAST write</h3>
                            </div>
                    </div>

                    <div class="follow">
                            <a href="/api/castboard/paper" class="btn btn-fab btn-rose btn-round" rel="tooltip" title="" data-original-title="Follow this user">
                                    <i class="material-icons">add</i>
                            </a>
                    </div>

            </div>
          </div>

        </div>
      <!-- / top -->
    <div class="board_list_wrap">
<!-- start -->


<form name="lateboard" id="lateboard">
  <input type="hidden" name="castid" value="">
  <div class="form-group is-empty">
    <label for="exampleInputEmail1">제목</label>
    <input type="text" class="form-control" id="casttitle" name="latetitle" placeholder="제목을 입력하세요" value="">
  <span class="material-input"></span><span class="material-input"></span></div>
  
  
  <div class="form-group">
    <label for="exampleInputEmail1">내용</label>
    <textarea id="summernote2" name="late_body"></textarea>
  </div>
  <div>
    <a class="btn btn-primary" href="javascript:;" onclick="save()">저장하기</a>
    <a class="btn btn-success float-right clear-both" href="/api/late">목록으로 가기</a>
  </div>
</form>

<!-- / END -->
    </div>
  </div>
</div>


<script>
var summernote;

(function($) {
  $.extend($.summernote.lang, {
    'ko-KR': {
      font: {
        bold: '굵게',
        italic: '기울임꼴',
        underline: '밑줄',
        clear: '서식 지우기',
        height: '줄 간격',
        name: '글꼴',
        superscript: '위 첨자',
        subscript: '아래 첨자',
        strikethrough: '취소선',
        size: '글자 크기',
      },
      image: {
        image: '그림',
        insert: '그림 삽입',
        resizeFull: '100% 크기로 변경',
        resizeHalf: '50% 크기로 변경',
        resizeQuarter: '25% 크기로 변경',
        resizeNone: '원본 크기',
        floatLeft: '왼쪽 정렬',
        floatRight: '오른쪽 정렬',
        floatNone: '정렬하지 않음',
        shapeRounded: '스타일: 둥근 모서리',
        shapeCircle: '스타일: 원형',
        shapeThumbnail: '스타일: 액자',
        shapeNone: '스타일: 없음',
        dragImageHere: '텍스트 혹은 사진을 이곳으로 끌어오세요',
        dropImage: '텍스트 혹은 사진을 내려놓으세요',
        selectFromFiles: '파일 선택',
        maximumFileSize: '최대 파일 크기',
        maximumFileSizeError: '최대 파일 크기를 초과했습니다.',
        url: '사진 URL',
        remove: '사진 삭제',
        original: '원본',
      },
      video: {
        video: '동영상',
        videoLink: '동영상 링크',
        insert: '동영상 삽입',
        url: '동영상 URL',
        providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion, Youku 사용 가능)',
      },
      link: {
        link: '링크',
        insert: '링크 삽입',
        unlink: '링크 삭제',
        edit: '수정',
        textToDisplay: '링크에 표시할 내용',
        url: '이동할 URL',
        openInNewWindow: '새창으로 열기',
      },
      table: {
        table: '표',
        addRowAbove: '위에 행 삽입',
        addRowBelow: '아래에 행 삽입',
        addColLeft: '왼쪽에 열 삽입',
        addColRight: '오른쪽에 열 삽입',
        delRow: '행 지우기',
        delCol: '열 지우기',
        delTable: '표 삭제',
      },
      hr: {
        insert: '구분선 삽입',
      },
      style: {
        style: '스타일',
        p: '본문',
        blockquote: '인용구',
        pre: '코드',
        h1: '제목 1',
        h2: '제목 2',
        h3: '제목 3',
        h4: '제목 4',
        h5: '제목 5',
        h6: '제목 6',
      },
      lists: {
        unordered: '글머리 기호',
        ordered: '번호 매기기',
      },
      options: {
        help: '도움말',
        fullscreen: '전체 화면',
        codeview: '코드 보기',
      },
      paragraph: {
        paragraph: '문단 정렬',
        outdent: '내어쓰기',
        indent: '들여쓰기',
        left: '왼쪽 정렬',
        center: '가운데 정렬',
        right: '오른쪽 정렬',
        justify: '양쪽 정렬',
      },
      color: {
        recent: '마지막으로 사용한 색',
        more: '다른 색 선택',
        background: '배경색',
        foreground: '글자색',
        transparent: '투명',
        setTransparent: '투명으로 설정',
        reset: '취소',
        resetToDefault: '기본값으로 설정',
        cpSelect: '고르다',
      },
      shortcut: {
        shortcuts: '키보드 단축키',
        close: '닫기',
        textFormatting: '글자 스타일 적용',
        action: '기능',
        paragraphFormatting: '문단 스타일 적용',
        documentStyle: '문서 스타일 적용',
        extraKeys: '추가 키',
      },
      help: {
        'insertParagraph': '문단 삽입',
        'undo': '마지막 명령 취소',
        'redo': '마지막 명령 재실행',
        'tab': '탭',
        'untab': '탭 제거',
        'bold': '굵은 글자로 설정',
        'italic': '기울임꼴 글자로 설정',
        'underline': '밑줄 글자로 설정',
        'strikethrough': '취소선 글자로 설정',
        'removeFormat': '서식 삭제',
        'justifyLeft': '왼쪽 정렬하기',
        'justifyCenter': '가운데 정렬하기',
        'justifyRight': '오른쪽 정렬하기',
        'justifyFull': '좌우채움 정렬하기',
        'insertUnorderedList': '글머리 기호 켜고 끄기',
        'insertOrderedList': '번호 매기기 켜고 끄기',
        'outdent': '현재 문단 내어쓰기',
        'indent': '현재 문단 들여쓰기',
        'formatPara': '현재 블록의 포맷을 문단(P)으로 변경',
        'formatH1': '현재 블록의 포맷을 제목1(H1)로 변경',
        'formatH2': '현재 블록의 포맷을 제목2(H2)로 변경',
        'formatH3': '현재 블록의 포맷을 제목3(H3)로 변경',
        'formatH4': '현재 블록의 포맷을 제목4(H4)로 변경',
        'formatH5': '현재 블록의 포맷을 제목5(H5)로 변경',
        'formatH6': '현재 블록의 포맷을 제목6(H6)로 변경',
        'insertHorizontalRule': '구분선 삽입',
        'linkDialog.show': '링크 대화상자 열기',
      },
      history: {
        undo: '실행 취소',
        redo: '재실행',
      },
      specialChar: {
        specialChar: '특수문자',
        select: '특수문자를 선택하세요',
      },
    },
  });
})(jQuery);

function sendFile(file, editor, welEditable){
    var data = new FormData();
    data.append ("userfile", file);
    $.ajax({
      url : "/api/index.php/castboard/uploadimg",
      data : data,
      cache :false,
      contentType: false,
      processData : false,
      type : "POST",
      success : function (result){
        summernote.summernote('insertImage', result);
        console.log(result);
      },
      error : function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus + " " + errorThrown);
      }
    });
  }
  $(function(){
    summernote = $('#summernote2').summernote({
        height: 500,
        lang: 'ko-KR',
        callbacks: {
          onImageUpload: function (files,editor, $editable){
            sendFile(files[0], $(this), $editable);

          }
        }
    });
  });


function save() {
    if( $("input[name=latetitle]").val()=='') {
      alert("제목을 적어주세요"); return false;
    }
    if( $("#summernote2").val()=='') {
      alert("내용을 적어주세요"); return false;
    }
    console.log ($("#summernote2").val() )
    if ( confirm("저장하시겠습니까?") ){
      $.ajax({
        type : 'POST',
        url : '/api/index.php/late/writedoc',
        dataType : 'json',
        data : $("#lateboard").serialize(),
        success : function(result) {
          if(result.code=='200'){
            alert("저장하였습니다.");
            location.replace("/api/late")
          }else {
            alert( result.msg.escapeSpecialChars() );
          }
        },
        error: function (jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(msg);
        },
      });

    }
  }

</script>

  
  








</body></html>