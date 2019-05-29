{crmScope extensionKey='dataprocessor'}
    <h3>{ts}Exposed Filters{/ts}</h3>
    <div class="crm-block crm-form-block crm-data-processor_source-block">
        <table>
            <tr>
                <th>{ts}Title{/ts}</th>
                <th></th>
                <th></th>
            </tr>
            {foreach from=$filters item=filter}
                <tr>
                    <td>
                        {$filter.title}
                        {if ($filter.is_required)}
                            <span class="crm-marker">*</span>
                        {/if} <br />
                        <span class="description">{$filter.name}</span>
                    </td>
                    <td style="width: 20%">{if ($filter.weight && !is_numeric($filter.weight))}{$filter.weight}{/if}</td>
                    <td style="width: 20%">
                        <a href="{crmURL p="civicrm/dataprocessor/form/filter" q="reset=1&action=update&data_processor_id=`$filter.data_processor_id`&id=`$filter.id`"}">{ts}Edit{/ts}</a>
                        <a href="{crmURL p="civicrm/dataprocessor/form/filter" q="reset=1&action=delete&data_processor_id=`$filter.data_processor_id`&id=`$filter.id`"}">{ts}Remove{/ts}</a>
                    </td>
                </tr>
            {/foreach}
        </table>

        <div class="crm-submit-buttons">
            <a class="add button" title="{ts}Add Filter{/ts}" href="{$addFilterUrl}">
                <i class='crm-i fa-plus-circle'></i> {ts}Add Filter{/ts}</a>
        </div>
    </div>
{/crmScope}