{if $menu->getMenuItems()|count}
	{assign var='__activeMenuItems' value=$menu->getActiveMenuItems()}
	
	{assign var='__openFieldset' value=false}
	{foreach from=$menu->getMenuItems() item='__primaryMenuItem'}
		{if $menu->getMenuItems($__primaryMenuItem->menuItem)|count}
			{if $__openFieldset}
				</ul></nav></fieldset>
				
				{assign var='__openFieldset' value=false}
			{/if}
			
			<fieldset>
				{if $menu->getMenuItems($__primaryMenuItem->menuItem)|count}
					<legend class="menuHeader" id="{'.'|str_replace:'_':$__primaryMenuItem->menuItem}Content">
						{if $__primaryMenuItem->getProcessor()->getLink()}
							<a href="{$__primaryMenuItem->getProcessor()->getLink()}">{@$__primaryMenuItem->menuItem|language}</a>
						{else}
							{@$__primaryMenuItem->menuItem|language}
						{/if}
						
						{if $__primaryMenuItem->getProcessor()->getNotifications()}
							<span class="badge">{#$__primaryMenuItem->getProcessor()->getNotifications()}</span>
						{/if}
					</legend>
				{/if}
				
				<nav>
					<ul>
						{foreach from=$menu->getMenuItems($__primaryMenuItem->menuItem) item='__menuItem'}
							<li{if $__menuItem->menuItem|in_array:$__activeMenuItems} class="active"{/if}>
								<a href="{$__menuItem->getProcessor()->getLink()}">
									{$__menuItem->menuItem|language}
									
									{if $__menuItem->getProcessor()->getNotifications()}
										<span class="badge">{#$__menuItem->getProcessor()->getNotifications()}</span>
									{/if}
								</a>
							</li>
						{/foreach}
					</ul>
				</nav>
			</fieldset>
			
			{assign var='__openFieldset' value=false}
		{else}
			{if !$__openFieldset}
				<fieldset><nav><ul>
				
				{assign var='__openFieldset' value=true}
			{/if}
			
			<li{if $__primaryMenuItem->menuItem|in_array:$__activeMenuItems} class="active"{/if}>
				<a href="{$__primaryMenuItem->getProcessor()->getLink()}">{$__primaryMenuItem->menuItem|language}</a>
				
				{if $__primaryMenuItem->getProcessor()->getNotifications()}
					<span class="badge">{#$__primaryMenuItem->getProcessor()->getNotifications()}</span>
				{/if}
			</li>
		{/if}
	{/foreach}
	
	{if $__openFieldset}
		</ul></nav></fieldset>
	{/if}
{/if}
