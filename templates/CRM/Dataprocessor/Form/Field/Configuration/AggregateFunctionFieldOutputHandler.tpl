{crmScope extensionKey='dataprocessor'}
  <div class="crm-section">
    <div class="label">{$form.function.label}</div>
    <div class="content">{$form.function.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
      <div class="label">{$form.field.label}</div>
      <div class="content">{$form.field.html}</div>
      <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.number_of_decimals.label}</div>
    <div class="content">{$form.number_of_decimals.html}
      <p class="description">{ts}Leave empty for no formatting{/ts}</p>
    </div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.decimal_separator.label}</div>
    <div class="content">{$form.decimal_separator.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.thousand_separator.label}</div>
    <div class="content">{$form.thousand_separator.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.prefix.label}</div>
    <div class="content">{$form.prefix.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.suffix.label}</div>
    <div class="content">{$form.suffix.html}</div>
    <div class="clear"></div>
  </div>

{/crmScope}
