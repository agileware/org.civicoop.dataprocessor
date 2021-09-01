{crmScope extensionKey='dataprocessor'}
  {include file="CRM/Dataprocessor/Form/Field/Configuration/SimpleFieldOutputHandler.tpl"}
  <div class="crm-section">
    <div class="label">{$form.label_as_value.label}</div>
    <div class="content">{$form.label_as_value.html}
      <p class="description">{ts}If checked, return option labels.  If unchecked, return option values.{/ts}</p></div>
    <div class="clear"></div>
  </div>
{/crmScope}
