<header>
    <div class="logo menu-tab"></div>
    <div class="pull-right nav-right">
        <ul>
            <li>
                <a href="/cart" class="cart">
                    <span class="item-count">
                        {{Session::has('cart.item') ? sizeof(Session::get('cart.item')) : 0}}
                    </span>
                </a>
            </li>
        </ul>
    </div>
</header>


<a href="/">
    <div id="logo" class="@yield('logo-class')">
        @include('layouts.partials.logo')
    </div>
</a>
