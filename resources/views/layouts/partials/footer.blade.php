<footer class="@yield('footer-class')">
    @if(sizeof($footerMenus) != 0)
        <ul>
            @foreach($footerMenus as $menu)
                <li>
                    <a href="{{$menu->link}}" {{$menu->text == 'Facebook' || $menu->text == 'Instagram' ? 'target="_blank"' : ''}}>
                        {{$menu->text}}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
    <div class="copyright">
        &copy; {{ date("Y") == '2018' ? '2018' : "2018 - ".date('Y') }} FOMO
    </div>
    <div class="design-by">
        {{-- Website by: <a href="http://www.peiyingtang.com" target="_blank">PY</a> + <a href="http://www.thelittletroop.com" target="_blank">LOOI</a> --}}
    </div>
</footer>
