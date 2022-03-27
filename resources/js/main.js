$(function () {
    var playerTrack = $("#player-track"),
        bgArtwork = $("#bg-artwork"),
        bgArtworkUrl,
        albumName = $("#album-name"),
        trackName = $("#track-name"),
        albumArt = $("#album-art"),
        sArea = $("#s-area"),
        seekBar = $("#seek-bar"),
        trackTime = $("#track-time"),
        insTime = $("#ins-time"),
        sHover = $("#s-hover"),
        playPauseButton = $("#play-pause-button"),
        i = playPauseButton.find("i"),
        tProgress = $("#current-time"),
        tTime = $("#track-length"),
        seekT,
        seekLoc,
        seekBarPos,
        cM,
        ctMinutes,
        ctSeconds,
        curMinutes,
        curSeconds,
        durMinutes,
        durSeconds,
        playProgress,
        bTime,
        nTime = 0,
        buffInterval = null,
        tFlag = false,
        playPreviousTrackButton = $("#play-previous"),
        playNextTrackButton = $("#play-next"),
        trackNowIndex = 0,
        offsetSong = 0,
        songList = [],
        flagInit = true,
        flagPlay = false,
        flagTable = false,
        tableParams = {
            "album": {"name": "Альбом"},
            "artist": {"name": "Артист"},
            "title": {"name": "Название"},
            "year": {"name": "Год", "size": "1"},
            "genre": {"name": "Жанр", "size": "5"}
        };

    function playPause() {
        setTimeout(function () {
            if (audio.paused) {
                let pause = $('#play-list').find(`#${trackNowIndex}`);
                console.log(`#${trackNowIndex}`);
                pause.removeClass('fa-play');
                pause.addClass('fa-pause');
                albumArt.addClass("active");
                checkBuffering();
                i.attr("class", "fa fa-pause");
                audio.play();
            } else {
                let play = $('#play-list').find('.fa-pause');
                play.removeClass('fa-pause');
                play.addClass('fa-play');
                albumArt.removeClass("active");
                clearInterval(buffInterval);
                albumArt.removeClass("buffering");
                i.attr("class", "fa fa-play");
                audio.pause();
            }
        }, 300);
    }

    function showHover(event) {
        seekBarPos = sArea.offset();
        seekT = event.clientX - seekBarPos.left;
        seekLoc = audio.duration * (seekT / sArea.outerWidth());

        sHover.width(seekT);

        cM = seekLoc / 60;

        ctMinutes = Math.floor(cM);
        ctSeconds = Math.floor(seekLoc - ctMinutes * 60);

        if (ctMinutes < 0 || ctSeconds < 0) return;

        if (ctMinutes < 0 || ctSeconds < 0) return;

        if (ctMinutes < 10) ctMinutes = "0" + ctMinutes;
        if (ctSeconds < 10) ctSeconds = "0" + ctSeconds;

        if (isNaN(ctMinutes) || isNaN(ctSeconds)) insTime.text("--:--");
        else insTime.text(ctMinutes + ":" + ctSeconds);

        insTime.css({left: seekT, "margin-left": "-21px"}).fadeIn(0);
    }

    function hideHover() {
        sHover.width(0);
        insTime.text("00:00").css({left: "0px", "margin-left": "0px"}).fadeOut(0);
    }

    function playFromClickedPos() {
        audio.currentTime = seekLoc;
        seekBar.width(seekT);
        hideHover();
    }

    function updateCurrTime() {
        nTime = new Date();
        nTime = nTime.getTime();

        if (!tFlag) {
            tFlag = true;
            trackTime.addClass("active");
        }

        curMinutes = Math.floor(audio.currentTime / 60);
        curSeconds = Math.floor(audio.currentTime - curMinutes * 60);

        durMinutes = Math.floor(audio.duration / 60);
        durSeconds = Math.floor(audio.duration - durMinutes * 60);

        playProgress = (audio.currentTime / audio.duration) * 100;

        if (curMinutes < 10) curMinutes = "0" + curMinutes;
        if (curSeconds < 10) curSeconds = "0" + curSeconds;

        if (durMinutes < 10) durMinutes = "0" + durMinutes;
        if (durSeconds < 10) durSeconds = "0" + durSeconds;

        if (isNaN(curMinutes) || isNaN(curSeconds)) tProgress.text("00:00");
        else tProgress.text(curMinutes + ":" + curSeconds);

        if (isNaN(durMinutes) || isNaN(durSeconds)) tTime.text("00:00");
        else tTime.text(durMinutes + ":" + durSeconds);

        if (
            isNaN(curMinutes) ||
            isNaN(curSeconds) ||
            isNaN(durMinutes) ||
            isNaN(durSeconds)
        )
            trackTime.removeClass("active");
        else trackTime.addClass("active");

        seekBar.width(playProgress + "%");

        if (playProgress == 100) {
            i.attr("class", "fa fa-play");
            seekBar.width(0);
            tProgress.text("00:00");
            albumArt.removeClass("buffering").removeClass("active");
            clearInterval(buffInterval);
        }
    }

    function checkBuffering() {
        clearInterval(buffInterval);
        buffInterval = setInterval(function () {
            if (nTime == 0 || bTime - nTime > 1000) albumArt.addClass("buffering");
            else albumArt.removeClass("buffering");

            bTime = new Date();
            bTime = bTime.getTime();
        }, 100);
    }

    function selectTrack(trackNowIndex) {

        //if (trackNowIndex == 0) i.attr("class", "fa fa-play");
        //else {
        albumArt.removeClass("buffering");
        i.attr("class", "fa fa-pause");
        //}

        seekBar.width(0);
        trackTime.removeClass("active");
        tProgress.text("00:00");
        tTime.text("00:00");
        currAlbum = songList[trackNowIndex].album;
        currTrackName = songList[trackNowIndex].artist + ' - ' + songList[trackNowIndex].title;
        currArtwork = songList[trackNowIndex].cover_path;
        audio.src = songList[trackNowIndex].path + songList[trackNowIndex].file_name;

        nTime = 0;
        bTime = new Date();
        bTime = bTime.getTime();

        if (flagPlay) {
            audio.play();
        } else {
            flagPlay = true;
        }

        albumArt.addClass("active");

        clearInterval(buffInterval);
        checkBuffering();

        albumName.text(currAlbum);
        trackName.text(currTrackName);

        $("#album-art-img").attr("src", currArtwork);

        bgArtworkUrl = $("#album-art-img").attr("src");

        bgArtwork.css({"background-image": "url(" + bgArtworkUrl + ")"});
    }

    function initPlayer() {
        audio = new Audio();

        selectTrack(trackNowIndex);

        audio.loop = false;

        playPauseButton.on("click", playPause);

        sArea.mousemove(function (event) {
            showHover(event);
        });

        sArea.mouseout(hideHover);

        sArea.on("click", playFromClickedPos);

        $(audio).on("timeupdate", updateCurrTime);

        $(audio).on("ended", function () {
            nextSong();
            playCount(trackNowIndex);
        });

        playPreviousTrackButton.on("click", function () {
            prevSong();
        });
        playNextTrackButton.on("click", function () {
            nextSong();
        });
    }

    function nextSong() {
        trackNowIndex++;
        if (trackNowIndex > songList.length - 1) {
            offsetSong++;
            getPlayer(offsetSong);
        }
        selectTrack(trackNowIndex);
    }

    function prevSong() {
        trackNowIndex--;
        if (trackNowIndex < 0) {
            trackNowIndex = songList.length - 1;
        }
        selectTrack(trackNowIndex);
    }

    function getTableParameters() {
        $.ajax({
            url: '/api/get-table-parameters',
            async: false,
            method: 'post',
            dataType: 'html',
            success: function (data) {
                tableParams = JSON.parse(data).data;
            },
        });
    }

    function getPlayer(offsetSong) {
        $.ajax({
            url: '/api/get-song-list',
            async: false,
            method: 'post',
            dataType: 'html',
            data: {offsetSong: offsetSong},
            success: function (data) {
                let jsonData = JSON.parse(data).data;
                for (let item of jsonData) {
                    addDataTable(item);
                    songList.push(item);
                }
            },
            error: function (data) {
                offsetSong--;
            }
        });

        $(".js-play-track").on("click", function () {
            let pause = $('#play-list').find('.fa-pause');
            pause.removeClass('fa-pause');
            pause.addClass('fa-play');
            $(this).removeClass('fa-play');
            $(this).addClass('fa-pause');
            trackNowIndex = $(this).attr("data-id") - 1
            selectTrack(trackNowIndex);
        });

        if (flagInit) {
            initPlayer();
            flagInit = false;
        }
    }

    function playCount(id) {
        $.ajax({
            url: '/api/play-count',
            method: 'post',
            dataType: 'html',
            data: {id: id + 1},
            success: function (data) {
            },
        });
    }

    createTable();
    getPlayer(offsetSong);

    function createTable() {
        let table = document.createElement("table");
        let header = table.createTHead();
        let tr = header.insertRow(-1);
        let th = document.createElement("th");
        th.innerHTML = '';
        tr.appendChild(th);

        Object.keys(tableParams).forEach(key => {
            let th = document.createElement("th");
            th.innerHTML = tableParams[key].name;
            tr.appendChild(th);
        });

        tr = header.insertRow(-1);
        let td = document.createElement("td");
        tr.appendChild(td);

        Object.keys(tableParams).forEach(key => {
            let td = document.createElement("td");
            let size = tableParams[key].size ? tableParams[key].size : '';
            td.innerHTML = `<input type="text" id="js-table-${key}" size="${size}">`;
            tr.appendChild(td);
        });

        table.createTBody();

        let divShowData = $('#play-list');

        divShowData.innerHTML = "";
        divShowData.append(table);
    }

    function addDataTable(data) {
        let tbody = $('#play-list > table > tbody');
        let table_body = '';

        table_body += `<td><i data-id="${data.id}" class="fa fa-play js-play-track button-table"></i></td>`;
        Object.keys(data).forEach(key => {
            if (Object.keys(tableParams).indexOf(key) != -1) {
                table_body += `<td>${data[key]}</td>`;
            }
        });
        tbody.append('<tr>' + table_body + '</tr>');
    }

    function clearDataTable(data) {
        console.log(data);

    }

    $("#play-list").on("scroll", function () {
        if ($(this).scrollTop() == ($(this)[0].scrollHeight - $(this)[0].clientHeight)) {
            offsetSong++;
            getPlayer(offsetSong);
        }
    });

});
