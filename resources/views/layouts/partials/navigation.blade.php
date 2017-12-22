<nav class="nav-menu">
    <div class="header-position">
        <div class="menu-tab close-nav">Close</div>

        <ul>
            @if(sizeof($navMenus) != 0)
                @foreach($navMenus as $menu)
                    <li>
                        <a href="{{$menu->link}}">
                            {{$menu->text}}
                        </a>
                    </li>
                @endforeach
            @endif
            @if(Auth::check())
                <li><a href="/account">Account</a></li>
                <li><a href="/logout">logout</a></li>
            @else
                <li><a class="login-tab">Login</a></li>
            @endif
        </ul>

        <div class="ship-to">
            Currency
            <br>
            <select id="currency-selection">
              <option value="MYR" id="my-flag" {{Session::get('currency') == 'MYR' ? 'selected' : '' }}>MYR</option>
              <option value="SGD" id="sg-flag" {{Session::get('currency') == 'SGD' ? 'selected' : '' }}>SGD</option>
              <option value="EUR" id="eu-flag" {{Session::get('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
              <option value="USD" id="us-flag" {{Session::get('currency') == 'USD' ? 'selected' : '' }}>USD</option>
            </select>
            {{-- <a id="country">{{$geo->country}}</a> --}}
        </div>
    </div>
</nav>
