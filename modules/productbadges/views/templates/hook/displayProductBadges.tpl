{if $badges}
    <div class="product-badges">
        {foreach from=$badges item=badge}
            <span class="product-badge product-badge-{$badge.position}" 
                  style="background-color:{$badge.background_color};color:{$badge.text_color}">
                {$badge.text|escape:'html':'UTF-8'}
            </span>
        {/foreach}
    </div>
    
{/if}