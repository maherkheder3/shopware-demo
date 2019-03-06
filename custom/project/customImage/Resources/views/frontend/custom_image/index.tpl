
{extends file='frontend/index/index.tpl'}

{block name="frontend_index_content"}
    <h1>Hello world, this plugin is called {$name} :)</h1>

    {debug}

    <ul>
        {foreach $names as $name}
            <li>{$name}</li>
        {/foreach}
    </ul>
{/block}
