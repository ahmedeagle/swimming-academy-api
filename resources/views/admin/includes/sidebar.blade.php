<div class="main-menu menu-fixed menu-light menu-accordion    menu-shadow " data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class="nav-item @if(Route::current()->getName() == 'admin.dashboard') active @endif"><a
                    href="{{route('admin.dashboard')}}"><i class="la la-mouse-pointer"></i><span
                        class="menu-title" data-i18n="nav.add_on_drag_drop.main">الرئيسية </span></a>
            </li>

            <li class="nav-item has-sub @if(Request::is('admin/categories*')) open @endif">
                <a href="{{route('admin.categories.all')}}"><i class="la la-home"></i>
                    <span class="menu-title" data-i18n="nav.dash.main"> الأقسام </span>
                    <span
                        class="badge badge badge-info badge-pill float-right mr-2">{{\App\Models\Category::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.categories.all') active @endif"><a
                            class="menu-item" href="{{route('admin.categories.all')}}"
                            data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.categories.create') active @endif"
                           href="{{route('admin.categories.create')}}" data-i18n="nav.dash.crypto">أضافة
                            قسم جديد </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-sub @if(Request::is('admin/academies*')) open @endif">
                <a href="{{route('admin.academies.all')}}"><i class="la la-home"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">الأكاديميات </span>
                    <span
                        class="badge badge badge-info badge-pill float-right mr-2">{{\App\Models\Academy::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.academies.all') active @endif"><a
                            class="menu-item" href="{{route('admin.academies.all')}}"
                            data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.academies.create') active @endif"
                           href="{{route('admin.academies.create')}}" data-i18n="nav.dash.crypto">أضافة
                            أكاديمية </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item @if(Request::is('admin/coaches*') ) open @endif"><a
                    href="{{route('admin.coaches.all')}}"><i class="la la-male"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">الكاباتن  </span>
                    <span
                        class="badge badge badge-success badge-pill float-right mr-2">{{\App\Models\Coach::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.coaches.all') active @endif"><a
                            class="menu-item" href="{{route('admin.coaches.all')}}"
                            data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.coaches.create') active @endif "
                           href="{{route('admin.coaches.create')}}" data-i18n="nav.dash.crypto">أضافة
                            كابتن جديد </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item @if(Request::is('admin/teams*')) open @endif"><a href="{{route('admin.teams.all')}}"><i
                        class="la la-group"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">الفرق </span>
                    <span
                        class="badge badge badge-danger badge-pill float-right mr-2">{{\App\Models\Team::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.teams.all') active @endif"><a class="menu-item"
                                                                                                       href="{{route('admin.teams.all')}}"
                                                                                                       data-i18n="nav.dash.ecommerce">
                            عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.teams.create') active @endif"
                           href="{{route('admin.teams.create')}}" data-i18n="nav.dash.crypto">أضافة
                            فريق </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item @if(Request::is('admin/users*') ) open @endif"><a href="{{route('admin.users.all')}}"><i
                        class="la la-child"></i>
                    <span class="menu-title" data-i18n="nav.dash.main"> الاعبين  </span>
                    <span
                        class="badge badge badge-warning  badge-pill float-right mr-2">{{\App\Models\User::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.users.all') active @endif"><a class="menu-item"
                                                                                                       href="{{route('admin.users.all')}}"
                                                                                                       data-i18n="nav.dash.ecommerce">
                            عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.coaches.create') active @endif"
                           href="{{route('admin.users.create')}}" data-i18n="nav.dash.crypto">أضافة
                            لاعب جديد </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item @if(Request::is('admin/events*')) open @endif"><a
                    href="{{route('admin.events.all')}}"><i class="la la-picture-o"></i>
                    <span class="menu-title" data-i18n="nav.dash.main"> فعاليات الاكاديمية  </span>
                    <span
                        class="badge badge badge-danger  badge-pill float-right mr-2">{{\App\Models\Event::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.events.all') active @endif"><a
                            class="menu-item" href="{{route('admin.events.all')}}"
                            data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.events.create') active @endif"
                           href="{{route('admin.events.create')}}" data-i18n="nav.dash.crypto">أضافة
                            فاعليات </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item @if(Request::is('admin/activities*')) open @endif"><a
                    href="{{route('admin.activities.all')}}"><i class="la la-video-camera"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">  أنشطة الاكاديمية  </span>
                    <span
                        class="badge badge badge-success  badge-pill float-right mr-2">{{\App\Models\Activity::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.activities.all') active @endif"><a
                            class="menu-item" href="{{route('admin.activities.all')}}"
                            data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.activities.create') active @endif"
                           href="{{route('admin.activities.create')}}" data-i18n="nav.dash.crypto">أضافة
                            نشاط </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item @if(Request::is('admin/heroes*')) open @endif"><a
                    href="{{route('admin.heroes.all')}}"><i class="la la-star"></i>
                    <span class="menu-title" data-i18n="nav.dash.main"> ألابطال   </span>
                    <span
                        class="badge badge badge-warning  badge-pill float-right mr-2">{{\App\Models\Hero::count()}}</span>
                </a>
                <ul class="menu-content">
                    <li class="@if(Route::current()->getName() == 'admin.heroes.all') active @endif"><a
                            class="menu-item" href="{{route('admin.heroes.all')}}"
                            data-i18n="nav.dash.ecommerce"> عرض الكل </a>
                    <li class="@if(Route::current()->getName() == 'admin.heroes.currentWeek') active @endif"><a
                            class="menu-item" href="{{route('admin.heroes.currentWeek')}}"
                            data-i18n="nav.dash.ecommerce">أبطال الاسبوع الحالي </a>
                    </li>
                    <li><a class="menu-item @if(Route::current()->getName() == 'admin.heroes.create') active @endif"
                           href="{{route('admin.heroes.create')}}" data-i18n="nav.dash.crypto">أضافة
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if(Request::is('admin/users/tickets*') ) open @endif">
                <a href="{{route('admin.users.tickets.all')}}"><i class="la la-envelope-o"></i>
                    <span class="menu-title" data-i18n="nav.dash.main">تذاكر المراسلات   </span>
                </a>
                <ul class="menu-content">
                    <li class=" @if(Route::current()->getName() == 'admin.users.tickets.all') active @endif"><a
                            class="menu-item" href="{{route('admin.users.tickets.all')}}"
                            data-i18n="nav.dash.ecommerce"> تذاكر الاعبين </a>
                    </li>
                </ul>
            </li>


        </ul>
    </div>
</div>
