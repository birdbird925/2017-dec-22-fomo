<div class="msg-popup {{session('popup')  ? 'popup' : ''}} ">
    <div class="close-nav {{session('popup')  ? 'popup' : ''}}"></div>
    <div class="popup-inner">
        <div class="title">
            @if(session('popup'))
                {{session('popup.title')}}
            @endif
        </div>
        <div class="caption">
            @if(session('popup'))
                {{session('popup.caption')}}
            @endif
        </div>
    </div>
</div>
