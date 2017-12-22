<footer class="@yield('footer-class')">
    @if(sizeof($footerMenus) != 0)
        <ul>
            @foreach($footerMenus as $menu)
                <li>
                    <a href="{{$menu->link}}">
                        {{$menu->text}}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
    <div class="copyright">
        &copy; {{ date("Y") == '2017' ? '2017' : "2017 - ".date('Y') }} FOMO
    </div>
    <div class="design-by">
        Website by: <a href="http://www.peiyingtang.com" target="_blank">PY</a> + <a href="http://www.thelittletroop.com" target="_blank">LOOI</a>
    </div>
</footer>
