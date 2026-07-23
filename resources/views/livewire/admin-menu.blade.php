<div class="row mt-4">
    <div class="col-sm-12">
        <h2>Страницы управления</h2>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link{{ request()->is('vk/incoming/log') ? ' active' : '' }}" href="/vk/incoming/log?showadmin=1">VK Incoming Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link{{ request()->is('vk/webhook/log') ? ' active' : '' }}" href="/vk/webhook/log?showadmin=1">VK Webhook Log</a>
            </li>
            <li class="nav-item">
                <a class="nav-link{{ request()->is('vk/groups') ? ' active' : '' }}" href="/vk/groups?showadmin=1">VK Groups</a>
            </li>
            <li class="nav-item">
                <a class="nav-link{{ request()->is('vk/channels') ? ' active' : '' }}" href="/vk/channels?showadmin=1">VK Channels</a>
            </li>
            <li class="nav-item">
                <a class="nav-link{{ request()->is('telegram/log') ? ' active' : '' }}" href="/telegram/log?showadmin=1">Telegram Log</a>
            </li>
            <li class="nav-item">
                <a class="nav-link{{ request()->is('laravel/log') ? ' active' : '' }}" href="/laravel/log?showadmin=1">Laravel Log</a>
            </li>
        </ul>
    </div>
</div>
