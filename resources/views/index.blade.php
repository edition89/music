@include('layouts.head')
<div id="app-cover">
    <div id="bg-artwork"></div>
    <div id="bg-layer"></div>
    <div id="player">

        <div id="player-content">
            <div id="album-art">
                <img src="/image/cover.png" class="active" id="album-art-img">
                <div id="buffer-box">Buffering ...</div>
            </div>
            <div id="player-track">
                <div id="track-name"></div>
                <div id="album-name"></div>
                <div id="track-time">
                    <div id="current-time"></div>
                    <div id="track-length"></div>
                </div>
                <div id="s-area">
                    <div id="ins-time"></div>
                    <div id="s-hover"></div>
                    <div id="seek-bar"></div>
                </div>
            </div>
            <div id="player-controls">
                <div class="control">
                    <div class="button" id="play-previous">
                        <i class="fas fa-backward"></i>
                    </div>
                </div>
                <div class="control">
                    <div class="button" id="play-pause-button">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
                <div class="control">
                    <div class="button" id="play-next">
                        <i class="fas fa-forward"></i>
                    </div>
                </div>
            </div>
        </div>
        <div id="play-list"></div>
    </div>
</div>
@include('layouts.scripts')
