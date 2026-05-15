<div class="panel">
    <div class="panel-heading">
        <i class="icon-tag"></i> Product Badges
    </div>
    <div class="panel-body">
        {if $badges}
            <input type="hidden" name="productbadges_submitted" value="1">
            <table class="table">
                <thead>
                    <tr>
                        <th>Asignar</th>
                        <th>Badge</th>
                        <th>Vista previa</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$badges item=badge}
                    <tr>
                        <td>
                            <input type="checkbox"
                                name="badges[]"
                                value="{$badge.id_badge|intval}"
                                {if in_array($badge.id_badge, $assigned_ids)}checked{/if}>
                        </td>
                        <td>{$badge.text}</td>
                        <td>
                            <span style="background-color:{$badge.background_color};color:{$badge.text_color};padding:3px 10px;border-radius:3px;font-weight:bold;">
                                {$badge.text}
                            </span>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            <p class="help-block">Las badges se guardan al guardar el producto.</p>
            <button type="button" id="productbadges-save" class="btn btn-primary">Guardar badges ahora</button>
            <script>
                (function(){
                    var btn = document.getElementById('productbadges-save');
                    if (!btn) return;
                    btn.addEventListener('click', function(){
                        var checkboxes = document.querySelectorAll('input[name="badges[]"]');
                        var selected = [];
                        checkboxes.forEach(function(cb){ if(cb.checked) selected.push(cb.value); });
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '{$badges_admin_url|escape:'html':'UTF-8'}');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        var params = 'ajax=1&action=saveBadges&id_product=' + encodeURIComponent({$id_product|intval}) + '&badges=' + encodeURIComponent(selected.join(','));
                        xhr.send(params);
                    });
                })();
            </script>
        {else}
            <p>No hay badges activas. <a href="{$badges_admin_url|escape:'html':'UTF-8'}">Crear badges</a></p>
        {/if}
    </div>
</div>