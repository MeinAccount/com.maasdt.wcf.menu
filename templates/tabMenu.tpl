<nav class="tabMenu">
	<ul>
		{foreach from=$menu->getMenuItems() item='__menuItem'}
			<li{if $__menuItem->menuItem == $menu->getActiveMenuItem()} class="ui-state-active"{/if}>
				<a href="{$__menuItem->getProcessor()->getLink()}">{$__menuItem->menuItem|language}{if $__menuItem->getProcessor()->getNotifications()}<span class="badge">{#$__menuItem->getProcessor()->getNotifications()}</span>{/if}</a>
			</li>
		{/foreach}
	</ul>
</nav>
