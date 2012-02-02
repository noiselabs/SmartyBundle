{* Widgets *}

{block form_widget}
	<div {block 'widget_container_attributes'}{/block}>
		{block 'field_rows'}{/block}
		{$form|form_rest}
    </div>
{/block}

{block "collection_widget"}
	{*if isset($prototype)}
		{assign var="attr" value=array_merge($attr, array('data-prototype' =>  {$prototype|form_row})}
    {/if*}
    {block 'form_widget'}{/block}
{/block}

{block "textarea_widget"}
    <textarea {block 'widget_attributes'}{/block}>{$value}</textarea>
{/block}

{*block widget_choice_options}
	{for choice, label in options}
        {if _form_is_choice_group(label)}
            <optgroup label="{{ choice|trans }}">
                {for nestedChoice, nestedLabel in label}
                    <option value="{{ nestedChoice }}"{if _form_is_choice_selected(form, nestedChoice)} selected="selected"{/if}>{{ nestedLabel|trans }}</option>
                {/foreach}
            </optgroup>
        {else}
            <option value="{{ choice }}"{if _form_is_choice_selected(form, choice)} selected="selected"{/if}>{{ label|trans }}</option>
        {/if}
    {/foreach}
{/block widget_choice_options*}

{block "choice_widget"}
	{if $expanded}
		<div {block 'widget_container_attributes'}{/block}>
		{foreach $form as $child}
			{$child|form_widget}
			{$child|form_label}
		{/foreach}
		</div>
	{else}
	<select {block 'widget_attributes'}{/block}{if $multiple} multiple="multiple"{/if}>
		{if $empty_value != null}
			<option value="">{$empty_value|trans}</option>
		{/if}
		{if count($preferred_choices) > 0}
			{assign var="options" value=$preferred_choices}
			{block 'widget_choice_options'}{/block}
			{if count($choices) > 0 and $separator != null}
                <option disabled="disabled">{$separator}</option>
            {/if}
        {/if}
        {assign var="options" value=$choices}
        {block 'widget_choice_options'}{/block}
    </select>
    {/if}
{/block}

{block checkbox_widget}
	<input type="checkbox" {block 'widget_attributes'}{/block}{if isset($value)} value="{$value}"{/if}{if $checked} checked="checked"{/if} />
{/block}

{block 'radio_widget'}
	<input type="radio" {block 'widget_attributes'}{/block}{if isset($value)} value="{$value}"{/if}{if $checked} checked="checked"{/if} />
{/block}

{block 'datetime_widget'}
	{if $widget == 'single_text'}
		{block 'field_widget'}{/block}
	{else}
		<div {block 'widget_container_attributes'}{/block}>
			{$form.date|form_errors}
			{$form.time|form_errors}
			{$form.date|form_widget}
			{$form.time|form_widget}
		</div>
	{/if}
{/block}

{***block "date_widget"}
	{if $widget == 'single_text'}
		{block 'field_widget'}{/block}
	{else}
        <div {block 'widget_container_attributes'}{/block}>
            {{ date_pattern|replace({
                '{{ year }}':  form_widget(form.year),
                '{{ month }}': form_widget(form.month),
                '{{ day }}':   form_widget(form.day),
            })|raw }}
        </div>
    {/if}
{/block date_widget***}

{***block time_widget}
    {if widget == 'single_text'}
        {block 'field_widget'}{/block}
    {else}
        <div {block 'widget_container_attributes'}{/block}>
            {{ form_widget(form.hour, { 'attr': { 'size': '1' } }) }}:{{ form_widget(form.minute, { 'attr': { 'size': '1' } }) }}{if with_seconds}:{{ form_widget(form.second, { 'attr': { 'size': '1' } }) }}{/if}
        </div>
    {/if}
{/block***}

{***block number_widget}
    {# type="number" doesn't work with floats #}
    {set type = type|default('text')}
    {block 'field_widget'}{/block}
{/block***}

{***block integer_widget}
    {set type = type|default('number')}
    {block 'field_widget'}{/block}
{/block***}

{***block money_widget}
    {{ money_pattern|replace({ '{{ widget }}': block('field_widget') })|raw }}
{/block money_widget***}

{***block url_widget}
    {set type = type|default('url')}
    {block 'field_widget'}{/block}
{/block***}

{***block search_widget}
    {set type = type|default('search')}
    {block 'field_widget'}{/block}
{/block search_widget***}

{***block percent_widget}
    {set type = type|default('text')}
    {block 'field_widget'}{/block} %
{/block percent_widget***}

{block "field_widget"}
	<input type="{$type|default:'text'}" {block 'widget_attributes'}{/block} {if !empty($value)}value="{$value}" {/if}/>
{/block}

{block "password_widget"}
	{if !isset($type) || empty($type)}{$type='password'}{/if}
    {block 'field_widget'}{/block}
{/block}

{block "hidden_widget"}
	{if !isset($type) || empty($type)}{$type='hidden'}{/if}
	{block 'field_widget'}{/block}
{/block}

{block "email_widget"}
	{if !isset($type) || empty($type)}{$type='email'}{/if}
	{block 'field_widget'}{/block}
{/block}

{* Labels *}

{***block "generic_label"}
    {if required}
        {set attr = attr|merge({'class': attr.class|default('') ~ ' required'})}
    {/if}
    <label{for attrname,attrvalue in attr} {{attrname}}="{{attrvalue}}"{/foreach}>{{ label|trans }}</label>
{/block***}

{***block "field_label"}
    {set attr = attr|merge({'for': id})}
    {block 'generic_label'}{/block}
{/block***}

{block form_label}
    {block 'generic_label'}{/block}
{/block}

{* Rows *}

{block "repeated_row"}
	{block 'field_rows'}{/block}
{/block}

{***block "field_row"}
    <div>
        {{ form_label(form, label|default(null)) }}
        {{ form_errors(form) }}
        {{ form_widget(form) }}
    </div>
{/block***}

{block "hidden_row"}
	{$form|form_widget}
{/block}

{* Misc *}

{block "field_enctype"}
	{if $multipart}enctype="multipart/form-data"{/if}
{/block}

{***block "field_errors"}
    {if count($errors) > 0}
    <ul>
        {foreach $errors as $error}
            <li>{$error.messageTemplate|trans($error.messageParameters, 'validators'}{/block}</li>
        {/foreach}
    </ul>
    {/if}
{/block***}

{block "field_rest"}
	{foreach $form as $child}
		{if !$child.rendered}
			{$child|form_row}
		{/if}
	{/foreach}
{/block}

{* Support *}

{block "field_rows"}
	{$form|form_errors}
	{foreach $form as $child}
		{$child|form_row}
	{/foreach}
{/block}

{***block "widget_attributes"}
    id="{{ id }}" name="{{ full_name }}"{if read_only} disabled="disabled"{/if}{if required} required="required"{/if}{if max_length} maxlength="{{ max_length }}"{/if}{if pattern} pattern="{{ pattern }}"{/if}
    {for attrname,attrvalue in attr}{{attrname}}="{{attrvalue}}" {/foreach}
{/block***}

{***block "widget_container_attributes"}
    id="{{ id }}"
    {for attrname,attrvalue in attr}{{attrname}}="{{attrvalue}}" {/foreach}
{/block***}
