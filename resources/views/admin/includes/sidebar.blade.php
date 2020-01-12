<div class="main-menu menu-fixed menu-light menu-accordion    menu-shadow " data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class="nav-item active"><a href="{{route('admin.dashboard')}}"><i class="la la-mouse-pointer"></i><span
                        class="menu-title" data-i18n="nav.add_on_drag_drop.main">الرئيسية </span></a>
            </li>

            <li class="nav-item"><a href="{{route('admin.academies.all')}}"><i class="la la-home"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">الأكاديميات </span>
                    <span
                        class="badge badge badge-info badge-pill float-right mr-2">{{\App\Models\Academy::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="active"><a class="menu-item" href="{{route('admin.academies.all')}}"
                                          data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item" href="{{route('admin.academies.create')}}" data-i18n="nav.dash.crypto">أضافة
                            أكاديمية </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item"><a href="{{route('admin.teams.all')}}"><i class="la la-group"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">الفرق </span>
                    <span
                        class="badge badge badge-danger badge-pill float-right mr-2">{{\App\Models\Team::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="active"><a class="menu-item" href="{{route('admin.teams.all')}}"
                                          data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item" href="{{route('admin.teams.create')}}" data-i18n="nav.dash.crypto">أضافة
                            فريق </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item"><a href="{{route('admin.coaches.all')}}"><i class="la la-male"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">المدربين  </span>
                    <span
                        class="badge badge badge-success badge-pill float-right mr-2">{{\App\Models\Coach::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="active"><a class="menu-item" href="{{route('admin.coaches.all')}}"
                                          data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item" href="{{route('admin.coaches.create')}}" data-i18n="nav.dash.crypto">أضافة
                            مدرب </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item"><a href="{{route('admin.users.all')}}"><i class="la la-child"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">الطلاب  </span>
                    <span
                        class="badge badge badge-warning  badge-pill float-right mr-2">{{\App\Models\User::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="active"><a class="menu-item" href="{{route('admin.users.all')}}"
                                          data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item" href="{{route('admin.users.create')}}" data-i18n="nav.dash.crypto">أضافة
                            طالب </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item"><a href="{{route('admin.events.all')}}"><i class="la la-child"></i>
                    <span class="menu-title" data-i18n="nav.dash.main"> فعاليات الاكاديمية  </span>
                    <span
                        class="badge badge badge-danger  badge-pill float-right mr-2">{{\App\Models\Event::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="active"><a class="menu-item" href="{{route('admin.events.all')}}"
                                          data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item" href="{{route('admin.events.create')}}" data-i18n="nav.dash.crypto">أضافة
                             فاعليات  </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item"><a href="{{route('admin.aboutus')}}">
                    <i class="la la-book"></i>
                    <span class="menu-title" data-i18n="nav.dash.main"> من نحن  </span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{route('admin.users.tickets.all')}}"><i class="la la-envelope-o"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">تذاكر المراسلات   </span>
                </a>
                <ul class="menu-content">
                    <li class="active"><a class="menu-item" href="{{route('admin.users.tickets.all')}}"
                                          data-i18n="nav.dash.ecommerce"> تذاكر الطلاب </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</div>
