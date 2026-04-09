<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('dashboard.index') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ $settings->mini_logo ? asset($settings->mini_logo) : asset('assets/images/logo-sm.png') }}"
                    alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ $settings->logo ? asset($settings->logo) : asset('assets/images/logo-dark.png') }}"
                    alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('dashboard.index') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ $settings->mini_logo ? asset($settings->mini_logo) : asset('assets/images/logo-sm.png') }}"
                    alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ $settings->logo ? asset($settings->logo) : asset('assets/images/logo-light.png') }}"
                    alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Dashboard</span></li>
                <li class="nav-item">
                    <a href="{{ route('dashboard.index') }}"
                        class="nav-link {{ getPageStatus('dashboard.index') }}" data-key="t-ecommerce">
                        <i class="ri-home-4-line"></i> <span data-key="t-dashboards">Dashboard</span>
                    </a>
                </li>
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('system-user.*') ? 'active' : '' }}"
                        href="{{ route('system-user.index') }}">
                        <i class="ri-user-line"></i> <span>List of Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('preference.*') ? 'active' : '' }}"
                        href="{{ route('preference.index') }}">
                        <i class="ri-list-check"></i> <span>Preferences</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('grocery-types.*') ? 'active' : '' }}"
                        href="{{ route('grocery-types.index') }}">
                        <i class="ri-shopping-basket-2-line"></i> <span>Grocery Types</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('meals.*') ? 'active' : '' }}"
                        href="{{ route('meals.index') }}">
                        <i class="ri-restaurant-line"></i> <span>Meals</span>
                    </a>
                </li>


                <!-- end Dashboard Menu -->

                {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">CMS Pages</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ getPageStatus(['page.*', 'dynamic.*', 'faq.*'], 'collapsed active') }}"
                        href="#sidebarPages" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="sidebarPages">
                        <i class="ri-pages-line"></i> <span data-key="t-pages">Pages</span>
                    </a>
                    <div class="collapse menu-dropdown {{ getPageStatus(['page.*', 'dynamic.*', 'faq.*'], 'show') }}"
                        id="sidebarPages">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('dynamic.index') }}"
                                    class="nav-link {{ getPageStatus('dynamic.*') }}" data-key="t-starter">
                                    Dynamic Pages
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('faq.index') }}"
                                    class="nav-link {{ getPageStatus('faq.*') }}" data-key="t-starter">
                                    FAQ
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> --}}


                <li class="nav-item">
                    <a class="nav-link menu-link {{ getPageStatus('settings.*') }}" href="#sidebarMultilevel"
                        data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="sidebarMultilevel">
                        <i class="ri-share-line"></i> <span data-key="t-multi-level">Settings</span>
                    </a>
                    <div class="collapse menu-dropdown {{ getPageStatus('settings.*', 'show') }}"
                        id="sidebarMultilevel">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('settings.profile.index') }}"
                                    class="nav-link {{ getPageStatus('settings.profile.*') }}"
                                    data-key="t-level-1.1"> Profile Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('settings.system.index') }}"
                                    class="nav-link {{ getPageStatus('settings.system.*') }}"
                                    data-key="t-level-1.1"> System Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('settings.mail.index') }}"
                                    class="nav-link {{ getPageStatus('settings.mail.*') }}"
                                    data-key="t-level-1.1"> Mail Settings</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('settings.third-party-api.index') }}"
                                    class="nav-link {{ getPageStatus('settings.third-party-api.*') }}"
                                    data-key="t-level-1.1">Bible API Settings</a>
                            </li> --}}
                           {{--  <li class="nav-item">
                                <a href="{{ route('settings.payments.stripe.index') }}"
                                    class="nav-link {{ getPageStatus('settings.payments.*') }}"
                                    data-key="t-level-1.1"> Payment Settings</a>
                            </li> --}}
                        </ul>
                    </div>
                </li>


                {{-- <li class="nav-item" style="margin-top: 10em">
                    <a class="nav-link menu-link {{ request()->routeIs('auth.logout.post') ? 'active' : '' }}"
                        href="{{ route('auth.logout.post') }}">
                        <i class="ri-user-line"></i> <span>Logout</span>
                    </a>
                </li> --}}
                <li class="nav-item" style="margin-top: 25em; margin-left: 15px; ">
                    <form action="{{ route('auth.logout.post') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit"
                            class="nav-link menu-link {{ request()->routeIs('auth.logout.post') ? 'active' : '' }}"
                            style="border: none; background: none; padding: 10px;">
                            <i class="ri-logout-box-r-line"></i> <span>Logout</span>
                        </button>
                    </form>
                </li>


            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    {{-- <div class="sidebar-background"></div> --}}


</div>
