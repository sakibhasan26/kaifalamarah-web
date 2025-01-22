<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo">
            <a href="{{ setRoute('admin.dashboard') }}" class="sidebar-main-logo">
                <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings,'white') }}"
                data-dark_img="{{ get_logo($basic_settings,'dark') }}" alt="logo">
            </a>
            <button class="sidebar-menu-bar">
                <i class="fas fa-exchange-alt"></i>
            </button>
        </div>
        <div class="sidebar-user-area">
            <div class="sidebar-user-thumb">
                <a href="{{ setRoute('admin.profile.index') }}"><img src="{{ get_image(Auth::user()->image,'admin-profile','profile') }}" alt="user"></a>
            </div>
            <div class="sidebar-user-content">
                <h6 class="title">{{ Auth::user()->fullname }}</h6>
                <span class="sub-title">{{ Auth::user()->getRolesString() }}</span>
            </div>
        </div>
        @php
            $current_route = Route::currentRouteName();
        @endphp
        <div class="sidebar-menu-wrapper">
            <ul class="sidebar-menu">

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.dashboard',
                    'title'     => "Dashboard",
                    'icon'      => "menu-icon las la-rocket",
                ])

                {{-- Section Default --}}
                @include('admin.components.side-nav.link-group',[
                    'group_title'       => "Default",
                    'group_links'       => [
                        [
                            'title'     => "Setup Currency",
                            'route'     => "admin.currency.index",
                            'icon'      => "menu-icon las la-coins",
                        ],
                        [
                            'route'     => 'admin.campaigns.index',
                            'title'     => "Donation",
                            'icon'      => "menu-icon las la-bullhorn",
                        ],
                        [
                            'route'     => 'admin.events.index',
                            'title'     => "Events",
                            'icon'      => "menu-icon las la-calendar",
                        ],
                    ]
                ])

                {{-- Section Transaction & Logs --}}
                @include('admin.components.side-nav.link-group',[
                    'group_title'       => __("Transactions & Logs"),
                    'group_links'       => [
                        'dropdown'      => [
                            [
                                'title'     => __("Add Money Logs"),
                                'icon'      => "menu-icon las la-calculator",
                                'links'     => [
                                    [
                                        'title'     => "Pending Logs",
                                        'route'     => "admin.add.money.pending",
                                    ],
                                    [
                                        'title'     => "Completed Logs",
                                        'route'     => "admin.add.money.complete",
                                    ],
                                    [
                                        'title'     => "Canceled Logs",
                                        'route'     => "admin.add.money.canceled",
                                    ],
                                    [
                                        'title'     => "All Logs",
                                        'route'     => "admin.add.money.index",
                                    ]
                                ],
                            ],
                            [
                                'title'             => "Donation Logs",
                                'icon'              => "menu-icon las la-donate",
                                'links'     => [
                                    [
                                        'title'     => "Pending Logs",
                                        'route'     => "admin.donation.pending",
                                    ],
                                    [
                                        'title'     => "Completed Logs",
                                        'route'     => "admin.donation.complete",
                                    ],
                                    [
                                        'title'     => "Canceled Logs",
                                        'route'     => "admin.donation.canceled",
                                    ],
                                    [
                                        'title'     => "All Logs",
                                        'route'     => "admin.donation.index",
                                    ]
                                ],
                            ],
                        ],

                    ]
                ])
                {{-- Interface Panel --}}
                @include('admin.components.side-nav.link-group',[
                    'group_title'       => __("Interface Panel"),
                    'group_links'       => [
                        'dropdown'      => [
                            [
                                'title'     => "User Care",
                                'icon'      => "menu-icon las la-user-edit",
                                'links'     => [
                                    [
                                        'title'     => "Active Users",
                                        'route'     => "admin.users.active",
                                    ],
                                    [
                                        'title'     => "Email Unverified",
                                        'route'     => "admin.users.email.unverified",
                                    ],
                                    [
                                        'title'     => "All Users",
                                        'route'     => "admin.users.index",
                                    ],
                                    [
                                        'title'     => "Email To Users",
                                        'route'     => "admin.users.email.users",
                                    ],
                                    [
                                        'title'     => "Banned Users",
                                        'route'     => "admin.users.banned",
                                    ]
                                ],
                            ],
                            [
                                'title'             => "Admin Care",
                                'icon'              => "menu-icon las la-user-shield",
                                'links'     => [
                                    [
                                        'title'     => "All Admin",
                                        'route'     => "admin.admins.index",
                                    ],
                                    [
                                        'title'     => "Admin Role",
                                        'route'     => "admin.admins.role.index",
                                    ],
                                    [
                                        'title'     => "Role Permission",
                                        'route'     => "admin.admins.role.permission.index",
                                    ],
                                    [
                                        'title'     => "Email To Admin",
                                        'route'     => "admin.admins.email.admins",
                                    ]
                                ],
                            ],
                        ],
                    ]
                ])
                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.subscriber.index',
                    'title'     => "Subscriber",
                    'icon'      => "menu-icon las la-user-check",
                ])
                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.contact.messages.index',
                    'title'     => "Contact Messages",
                    'icon'      => "menu-icon las la-envelope",
                ])

                {{-- Section Settings --}}
                @include('admin.components.side-nav.link-group',[
                    'group_title'       => __("Settings"),
                    'group_links'       => [
                        'dropdown'      => [
                            [
                                'title'     => "Web Settings",
                                'icon'      => "menu-icon lab la-safari",
                                'links'     => [
                                    [
                                        'title'     => "Basic Settings",
                                        'route'     => "admin.web.settings.basic.settings",
                                    ],
                                    [
                                        'title'     => "Image Assets",
                                        'route'     => "admin.web.settings.image.assets",
                                    ],
                                    [
                                        'title'     => "Setup SEO",
                                        'route'     => "admin.web.settings.setup.seo",
                                    ]
                                ],
                            ],
                            [
                                'title'             => "App Settings",
                                'icon'              => "menu-icon las la-mobile",
                                'links'     => [
                                    [
                                        'title'     => "Splash Screen",
                                        'route'     => "admin.app.settings.splash.screen",
                                    ],
                                    [
                                        'title'     => "Onboard Screen",
                                        'route'     => "admin.app.settings.onboard.screens",
                                    ],
                                    [
                                        'title'     => __('App URLs'),
                                        'route'     => "admin.app.settings.urls",
                                    ],
                                ],
                            ],
                        ],
                    ]
                ])

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.languages.index',
                    'title'     => "Languages",
                    'icon'      => "menu-icon las la-language",
                ])

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.system.maintenance.index',
                    'title'     => __("System Maintenance"),
                    'icon'      => "menu-icon las la-tools",
                ])


                {{-- Verification Center --}}
                @include('admin.components.side-nav.link-group',[
                    'group_title'       => __("Verification Center"),
                    'group_links'       => [
                        'dropdown'      => [
                            [
                                'title'     => "Setup Email",
                                'icon'      => "menu-icon las la-envelope-open-text",
                                'links'     => [
                                    [
                                        'title'     => "Email Method",
                                        'route'     => "admin.setup.email.config",
                                    ],
                                    // [
                                    //     'title'     => "Default Template",
                                    //     'route'     => "admin.setup.email.template.default",
                                    // ]
                                ],
                            ]
                        ],

                    ]
                ])


                @if (admin_permission_by_name("admin.setup.sections.section"))
                    <li class="sidebar-menu-header">{{ __('Setup Web Content') }}</li>
                    @php
                        $current_url = URL::current();

                        $setup_section_childs  = [
                            setRoute('admin.setup.sections.section','breadcrumb-section'),
                            setRoute('admin.setup.sections.section','home_banner'),
                            setRoute('admin.setup.sections.section','service_section'),
                            setRoute('admin.setup.sections.section','about_section'),
                            setRoute('admin.setup.sections.section','overview-section-left'),
                            setRoute('admin.setup.sections.section','overview-section-right'),
                            setRoute('admin.setup.sections.section','partner_section'),
                            setRoute('admin.setup.sections.section','testimonial_section'),
                            setRoute('admin.setup.sections.section','video-section'),
                            setRoute('admin.setup.sections.section','download-app'),
                            setRoute('admin.setup.sections.section','gallery-section'),
                            setRoute('admin.setup.sections.section','contact'),
                            setRoute('admin.setup.sections.section','auth-section'),
                            setRoute('admin.setup.sections.section','footer-section'),
                            setRoute('admin.setup.sections.section','category'),
                            setRoute('admin.setup.sections.section','subscribe-section'),
                            setRoute('admin.setup.sections.section','statistics-section'),
                            setRoute('admin.setup.sections.section','faq-section'),
                        ];
                    @endphp

                    <li class="sidebar-menu-item sidebar-dropdown @if (in_array($current_url,$setup_section_childs)) active @endif">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-terminal"></i>
                            <span class="menu-title">{{ __('Setup Section') }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('admin.setup.sections.section','home_banner') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','home_banner')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Home Banner') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','service_section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','service_section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Service Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','subscribe-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','subscribe-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Subscribe Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','statistics-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','statistics-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Statistics Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','download-app') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','download-app')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('App Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','about_section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','about_section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('About Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','overview-section-left') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','overview-section-left')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Overview Left') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','overview-section-right') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','overview-section-right')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Overview Right') }}</span>
                                </a>

                                {{-- <a href="{{ setRoute('admin.setup.sections.section','testimonial_section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','testimonial_section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Testimonial Section') }}</span>
                                </a> --}}
                                <a href="{{ setRoute('admin.setup.sections.section','video-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','video-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Video Section') }}</span>
                                </a>
                                {{-- <a href="{{ setRoute('admin.setup.sections.section','partner_section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','partner_section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Partner Section') }}</span>
                                </a> --}}
                                <a href="{{ setRoute('admin.setup.sections.section','footer-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','footer-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Footer Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','breadcrumb-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','breadcrumb-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Breadcrumb Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','gallery-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','gallery-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Gallery Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','contact') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','contact')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Contact Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','category') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','category')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Category Type') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','faq-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','faq-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Faq Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','login-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','login-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{__("Login Section")}}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section','register-section') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section','register-section')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{__("Register Section")}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.setup.pages.index',
                    'title'     => "Setup Pages",
                    'icon'      => "menu-icon las la-file-alt",
                ])

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.useful.links.index',
                    'title'     => "Useful Links",
                    'icon'      => "menu-icon las la-link",
                ])

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.extensions.index',
                    'title'     => "Extensions",
                    'icon'      => "menu-icon las la-puzzle-piece",
                ])

                @if (admin_permission_by_name("admin.payment.gateway.view"))
                    <li class="sidebar-menu-header">{{ __('Payment Methods') }}</li>
                    @php
                        $payment_add_money_childs  = [
                            setRoute('admin.payment.gateway.view',['add-money','automatic']),
                            setRoute('admin.payment.gateway.view',['add-money','manual']),
                        ]
                    @endphp
                    <li class="sidebar-menu-item sidebar-dropdown @if (in_array($current_url,$payment_add_money_childs)) active @endif">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-funnel-dollar"></i>
                            <span class="menu-title">{{ __('Add Money') }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('admin.payment.gateway.view',['add-money','automatic']) }}" class="nav-link @if ($current_url == setRoute('admin.payment.gateway.view',['add-money','automatic'])) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Automatic') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.payment.gateway.view',['add-money','manual']) }}" class="nav-link @if ($current_url == setRoute('admin.payment.gateway.view',['add-money','manual'])) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Manual') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Notifications --}}
                {{-- @include('admin.components.side-nav.link-group',[
                    'group_title'       => "Notification",
                    'group_links'       => [
                        'dropdown'      => [
                            [
                                'title'     => "Push Notification",
                                'icon'      => "menu-icon las la-bell",
                                'links'     => [
                                    [
                                        'title'     => "Setup Notification",
                                        'route'     => "admin.push.notification.config",
                                    ],
                                    [
                                        'title'     => "Send Notification",
                                        'route'     => "admin.push.notification.index",
                                    ]
                                ],
                            ]
                        ],

                    ]
                ]) --}}

                @php
                    $bonus_routes = [
                        'admin.cookie.index',
                        'admin.server.info.index',
                        'admin.cache.clear',
                    ];
                @endphp

                @if (admin_permission_by_name_array($bonus_routes))
                    <li class="sidebar-menu-header">{{ __('Bonus') }}</li>
                @endif

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.cookie.index',
                    'title'     => "GDPR Cookie",
                    'icon'      => "menu-icon las la-cookie-bite",
                ])

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.server.info.index',
                    'title'     => "Server Info",
                    'icon'      => "menu-icon las la-sitemap",
                ])

                @include('admin.components.side-nav.link',[
                    'route'     => 'admin.cache.clear',
                    'title'     => "Clear Cache",
                    'icon'      => "menu-icon las la-broom",
                ])
            </ul>
        </div>
    </div>
</div>
