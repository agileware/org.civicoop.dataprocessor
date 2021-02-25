{crmScope extensionKey='dataprocessor'}
    <div class="crm-section">
        <div class="label">{$form.navigation_parent_path.label}</div>
        <div class="content">{$form.navigation_parent_path.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.navigation_weight.label}</div>
      <div class="content">{$form.navigation_weight.html}
        <p class="description">
          {ts 1='https://lab.civicrm.org/dev/core/-/issues/2424'}This will define the order in the navigation menu. A lower value means more at the top.  <br />
            Up to CiviCRM 5.36 this seems to be broken in CiviCRM Core. See this <a href="%1">bug report</a>
          {/ts}</p>
      </div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.permission.label}</div>
        <div class="content">{$form.permission.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.contribution_id_field.label}</div>
        <div class="content">{$form.contribution_id_field.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.hide_id_field.label}</div>
        <div class="content">{$form.hide_id_field.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.hidden_fields.label}</div>
      <div class="content">{$form.hidden_fields.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.expanded_search.label}</div>
      <div class="content">{$form.expanded_search.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.help_text.label}</div>
        <div class="content">{$form.help_text.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.no_result_text.label}</div>
      <div class="content">{$form.no_result_text.html}</div>
      <div class="clear"></div>
    </div>
{/crmScope}
