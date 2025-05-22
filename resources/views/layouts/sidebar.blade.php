<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <!-- Sidebar Ad Slot - Top -->
    <x-ad-slot code="sidebar_top" />

    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">@lang('translation.Menu')</li>

                @if(auth()->user()->role === 'admin')
                    <!-- Admin Menu -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i data-feather="grid" class="icon-sm"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.authentication-logs') }}">
                            <i data-feather="shield" class="icon-sm"></i>
                            <span>Login History</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i data-feather="users" class="icon-sm"></i>
                            <span>User Management</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.hosting.index') }}">
                            <i data-feather="server" class="icon-sm"></i>
                            <span>Hosting Accounts</span>
                        </a>
                    </li>

                    <!-- Notifications menu -->
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="bell" class="icon-sm"></i>
                            <span>Notifications</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.notifications.announcements') }}"><i data-feather="radio" class="icon-sm"></i> Announcements</a></li>
                            <li><a href="{{ route('admin.notifications.popups') }}"><i data-feather="alert-circle" class="icon-sm"></i> Popup Notifications</a></li>
                            <li><a href="{{ route('admin.notifications.bulk-email') }}"><i data-feather="send" class="icon-sm"></i> Email Notifications</a></li>
                        </ul>
                    </li>

                    <!-- Advertisement Management Menu -->
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="image" class="icon-sm"></i>
                            <span>Advertisement</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.ad-slots.index') }}">Ad Placement Areas</a></li>
                            <li><a href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                            <li><a href="{{ route('admin.advertisements.statistics') }}">Statistics</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="message-square" class="icon-sm"></i>
                            <span>Support Tickets</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.tickets.index') }}">All Tickets</a></li>
                            <li><a href="{{ route('admin.tickets.ratings') }}">Staff Ratings</a></li>
                            <li><a href="{{ route('admin.tickets.categories.index') }}">Categories</a></li>
                        </ul>
                    </li>
                    
                    <!-- Knowledge Base for Admin -->
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="book-open" class="icon-sm"></i>
                            <span>Knowledge Base</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.knowledge.categories.index') }}">Categories</a></li>
                            <li><a href="{{ route('admin.knowledge.articles.index') }}">Articles</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="sliders" class="icon-sm"></i>
                            <span>Settings</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.settings.index') }}"><i data-feather="sliders" class="icon-sm"></i> General Settings</a></li>
                            <li><a href="{{ route('admin.settings.oauth') }}"><i data-feather="key" class="icon-sm"></i> OAuth Settings</a></li>
                            <li><a href="{{ route('admin.captcha.index') }}"><i data-feather="shield" class="icon-sm"></i> Captcha Settings</a></li>
                            <li><a href="{{ route('admin.settings.smtp') }}"><i data-feather="mail" class="icon-sm"></i> SMTP Settings</a></li>
                            <li><a href="{{ route('admin.cloudflare.index') }}"><i data-feather="cloud" class="icon-sm"></i> Cloudflare Settings</a></li>
                            <li><a href="{{ route('admin.sitepro.index') }}"><i data-feather="layout" class="icon-sm"></i> SitePro Settings</a></li>
                            <li><a href="{{ route('admin.mofh.settings') }}"><i data-feather="server" class="icon-sm"></i> MOFH Settings</a></li>
                            <li><a href="{{ route('admin.webftp.settings') }}"><i data-feather="folder" class="icon-sm"></i> WebFTP Settings</a></li>
                            <li><a href="{{ route('admin.auth-log-settings') }}"><i data-feather="user-check" class="icon-sm"></i> Auth Log Settings</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('admin.email.index') }}">
                            <i data-feather="mail" class="icon-sm"></i>
                            <span>Email Templates</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.domains.index') }}">
                            <i data-feather="globe" class="icon-sm"></i>
                            <span>Domain Extensions</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.migration.index') }}">
                            <i data-feather="database" class="icon-sm"></i>
                            <span>Data Migration</span>
                        </a>
                    </li>

                @elseif(auth()->user()->role === 'support')
                    <!-- Support Staff Menu -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i data-feather="grid" class="icon-sm"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="message-square" class="icon-sm"></i>
                            <span>Support Tickets</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.tickets.index') }}">All Tickets</a></li>
                            <li><a href="{{ route('admin.tickets.ratings') }}">Staff Ratings</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('admin.hosting.index') }}">
                            <i data-feather="server" class="icon-sm"></i>
                            <span>Hosting Accounts</span>
                        </a>
                    </li>
                    
                    <!-- Knowledge Base for Support -->
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="book-open" class="icon-sm"></i>
                            <span>Knowledge Base</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.knowledge.categories.index') }}">Categories</a></li>
                            <li><a href="{{ route('admin.knowledge.articles.index') }}">Articles</a></li>
                        </ul>
                    </li>

                    <!-- Notifications menu for Support Staff -->
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="bell" class="icon-sm"></i>
                            <span>Notifications</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.notifications.announcements') }}"><i data-feather="radio" class="icon-sm"></i> Announcements</a></li>
                            <li><a href="{{ route('admin.notifications.popups') }}"><i data-feather="alert-circle" class="icon-sm"></i> Popup Notifications</a></li>
                            <li><a href="{{ route('admin.notifications.bulk-email') }}"><i data-feather="send" class="icon-sm"></i> Email Notifications</a></li>
                        </ul>
                    </li>

                    <!-- Advertisement Management for Support -->
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="image" class="icon-sm"></i>
                            <span>Advertisement</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.ad-slots.index') }}">Ad Placement Areas</a></li>
                            <li><a href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                            <li><a href="{{ route('admin.advertisements.statistics') }}">Statistics</a></li>
                        </ul>
                    </li>

                  @else
                    <!-- User Menu -->
                    <li>
                        <a href="{{ route('user.dashboard') }}">
                            <i data-feather="grid" class="icon-sm"></i>
                            <span>@lang('translation.Dashboard')</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('hosting.index') }}">
                            <i data-feather="server" class="icon-sm"></i>
                            <span>@lang('translation.Hosting')</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('ssl.index') }}">
                            <i data-feather="shield-off" class="icon-sm"></i>
                            <span>@lang('translation.SSL_Certificates')</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.tickets.index') }}">
                            <i data-feather="message-square" class="icon-sm"></i>
                            <span>@lang('translation.Support_Tickets')</span>
                        </a>
                    </li>
                    
                    <!-- User Notifications Menu -->
                    <li>
                        <a href="{{ route('notifications.index') }}">
                            <i data-feather="bell" class="icon-sm"></i>
                            <span>@lang('translation.Notifications')</span>
                        </a>
                    </li>
                    
                    <!-- Knowledge Base for User -->
                    <li>
                        <a href="{{ route('knowledge.index') }}">
                            <i data-feather="book-open" class="icon-sm"></i>
                            <span>@lang('translation.Knowledge_Base')</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('whois.index') }}">
                            <i data-feather="search" class="icon-sm"></i>
                            <span>@lang('translation.WHOIS_Domain')</span>
                        </a>
                    </li>
                    
                   <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="tool" class="icon-sm"></i>
                            <span>@lang('translation.Tools')</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('tools.case-converter') }}"><i data-feather="type" class="icon-sm"></i> @lang('translation.Case_Converter')</a></li>
                            <li><a href="{{ route('tools.code-beautifier') }}"><i data-feather="code" class="icon-sm"></i> @lang('translation.Code_Beautifier')</a></li>
                            <li><a href="{{ route('tools.color-tools') }}"><i data-feather="droplet" class="icon-sm"></i> @lang('translation.Color_Tools')</a></li>
                            <li><a href="{{ route('tools.base64') }}"><i data-feather="lock" class="icon-sm"></i> @lang('translation.Base64_Encoder')</a></li>
                            <li><a href="{{ route('tools.sql-formatter') }}"><i data-feather="database" class="icon-sm"></i> @lang('translation.SQL_Formatter')</a></li>
                            <li><a href="{{ route('tools.cdn-search') }}"><i data-feather="link" class="icon-sm"></i> @lang('translation.CDN_Search')</a></li>
                            <li><a href="{{ route('tools.website-speed-test') }}"><i data-feather="globe" class="icon-sm"></i> @lang('translation.Website_Speed_Test')</a></li>
                            <li><a href="{{ route('tools.css-grid-generator') }}"><i data-feather="grid" class="icon-sm"></i> @lang('translation.CSS_Grid_Generator')</a></li>
                            <li><a href="{{ route('tools.froala-license') }}"><i data-feather="edit-3" class="icon-sm"></i> @lang('translation.Froala_License')</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
        
        <!-- Sidebar Ad Slot - Middle -->
        <x-ad-slot code="sidebar_middle" />
    </div>
    
    <!-- Sidebar Ad Slot - Bottom -->
    <x-ad-slot code="sidebar_bottom" />
</div>
<!-- Left Sidebar End -->