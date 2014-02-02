{if $menu->getActiveMenuItem() && $menu->getMenuItems($menu->getActiveMenuItem())|count}
	<nav class="menu subTabMenu">
		<ul>
			{foreach from=$menu->getMenuItems($menu->getActiveMenuItem()) item='__menuItem'}
				<li{if $__menuItem->menuItem == $menu->getActiveMenuItem(1)} class="ui-state-active"{/if}>
					<a href="{$__menuItem->getProcessor()->getLink()}">{$__menuItem->menuItem|language}{if $__menuItem->getProcessor()->getNotifications()}<span class="badge">{#$__menuItem->getProcessor()->getNotifications()}</span>{/if}</a>
				</li>
			{/foreach}
		</ul>
	</nav>
{/if}
