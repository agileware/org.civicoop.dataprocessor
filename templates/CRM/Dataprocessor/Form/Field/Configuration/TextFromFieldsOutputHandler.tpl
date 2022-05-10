{crmScope extensionKey='dataprocessor'}
    <div class="crm-section">
        <div class="label">{$form.data_field_1.label}</div>
        <div class="content">{$form.data_field_1.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_2.label}</div>
        <div class="content">{$form.data_field_2.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_3.label}</div>
        <div class="content">{$form.data_field_3.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_4.label}</div>
        <div class="content">{$form.data_field_4.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_5.label}</div>
        <div class="content">{$form.data_field_5.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_6.label}</div>
        <div class="content">{$form.data_field_6.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_7.label}</div>
        <div class="content">{$form.data_field_7.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_8.label}</div>
        <div class="content">{$form.data_field_8.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.data_field_9.label}</div>
        <div class="content">{$form.data_field_9.html}</div>
        <div class="clear"></div>
    </div>
    <p class="help">{ts}Use %1, %2 … %9 as placeholders in the text template, they will be replaced by the value of the corresponding data field. Add an exclamation mark to the placeholders (%!1 etc.) to make the corresponding data field mandatory: If all data fields are empty or if one mandatory data field is empty, the whole result will be empty.{/ts}</p>
    <div class="crm-section">
        <div class="label">{$form.text_template.label}</div>
        <div class="content">{$form.text_template.html}</div>
        <div class="clear"></div>
    </div>
{/crmScope}
