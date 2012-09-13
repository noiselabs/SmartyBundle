{* Widgets *}

{function form_widget}
    <div {call widget_container_attributes}>
        {call form_rows}
        {form_rest form=$form}
    </div>
{/function}

{function collection_widget}
    {if isset($prototype)}
        {*$attr = array_merge($attr|merge({'data-prototype': form_row($prototype) }) %*}
    {/if}
    {form_widget}
{/function}

{function textarea_widget}
    <textarea {call widget_attributes}>{$value}</textarea>
{/function}

{function widget_choice_options}
    {foreach $options as $choice=>$label}
        {if $label|_form_is_choice_group}
            <optgroup label="{$choice|trans}">
                {foreach $label as $nestedChoice=>$nestedLabel}
                    <option value="{$nestedChoice}"{if $form|_form_is_choice_selected:$nestedChoice} selected="selected"{/if}>{$nestedLabel|trans}</option>
                {/foreach}
            </optgroup>
        {else}
            <option value="{$choice}"{if $form|_form_is_choice_selected:$choice} selected="selected"{/if}>{$label|trans}</option>
        {/if}
    {/foreach}
{/function}

{function choice_widget}
    {if $expanded}
        <div {call widget_container_attributes}>
        {foreach $form as $child}
            {form_widget form=$child}
            {form_label form=$child}
        {/foreach}
        </div>
    {else}
    <select {call widget_attributes}{if $multiple} multiple="multiple"{/if}>
        {if !empty($empty_value)}
            <option value="">{$empty_value|trans}</option>
        {/if}
        {if count($preferred_choices) > 0}
            {$options=$preferred_choices}
            {call widget_choice_options}
            {if count($choices) > 0 and !empty($separator)}
                <option disabled="disabled">{$separator}</option>
            {/if}
        {/if}
        {$options=$choices}
        {call widget_choice_options}
    </select>
    {/if}

{/function}

{function checkbox_widget}
    <input type="checkbox" {call widget_attributes}{if isset($value)} value="{$value}"{/if}{if $checked} checked="checked"{/if} />
{/function}

{function radio_widget}
    <input type="radio" {call widget_attributes}{if isset($value)} value="{$value}"{/if}{if $checked} checked="checked"{/if} />
{/function}

{function datetime_widget}
    {if $widget == 'single_text'}
        {call form_widget}
    {else}
        <div {call widget_container_attributes}>
            {form_errors form=$form.date}
            {form_errors form=$form.time}
            {form_widget form=$form.date}
            {form_widget form=$form.time}
        </div>
    {/if}
{/function}

{function date_widget}
    {if widget == 'single_text'}
        {call form_widget}
    {else}
        {*<div {call widget_container_attributes}>
            {{ date_pattern|replace({
                '{{ year }}':  form_widget(form.year),
                '{{ month }}': form_widget(form.month),
                '{{ day }}':   form_widget(form.day),
            })|raw }}
        </div>*}
    {/if}
{/function}

{function time_widget}
    {if widget == 'single_text'}
        {call form_widget}
    {else}
        {call slacker_notice function="time_widget"}
        {*<div {call widget_container_attributes}>
            {{ form_widget(form.hour, { 'attr': { 'size': '1' } }) }}:{{ form_widget(form.minute, { 'attr': { 'size': '1' } }) }}{if with_seconds}:{{ form_widget(form.second, { 'attr': { 'size': '1' } }) }}{/if}
        </div>*}
    {/if}
{/function}

{function number_widget}
    {* type="number" doesn't work with floats *}
    {$type=$type|default:'text'}
    {call form_widget}
{/function}

{function integer_widget}
    {$type=$type|default:'number'}
    {call form_widget}
{/function}

{function money_widget}
    {call slacker_notice function="money_widget"}
    {*$_money_pattern.widget={call form_widget}}
    {$money_pattern=array_replace($money_pattern, $_money_pattern)*}
    {*{ money_pattern|replace({ '{{ widget }}': {call form_widget})|raw }*}
{/function}

{function url_widget}
    {$type=$type|default:'url'}
    {call form_widget}
{/function}

{function search_widget}
    {$type=$type|default:'search'}
    {call form_widget}
{/function}

{function percent_widget}
    {$type=$type|default:'text'}
    {call form_widget}
{/function}

{function form_widget}
    {$type=$type|default:'text'}
    <input type="{$typ}" {call widget_attributes} {if !empty($value)}value="{$value}" {/if}/>
{/function}

{function password_widget}
    {$type=$type|default:'password'}
    {call form_widget}
{/function}

{function hidden_widget}
    {$type=$type|default:'hidden'}
    {call form_widget}
{/function}

{function email_widget}
    {$type=$type|default:'email'}
    {call form_widget}
{/function}

{* Labels *}

{function generic_label}
    {if $required}
        {if !isset($attr.class) || empty($attr.class)}{$attr.class=''}{/if}
    {/if}
    <label{foreach $attr as $attrname=>$attrvalue} {$attrname}="{$attrvalue}"{/foreach}>{$label|trans}</label>
{/function}

{function form_label}
    {$_attr.for=$id}
    {$attr=array_merge($attr, $_attr)}
    {call generic_label}
{/function}

{function form_label}
    {call generic_label}
{/function}

{* Rows *}

{function repeated_row}
    {call form_rows}
{/function}

{function form_row}
    <div>
        {form_label form=$form label=$label|default:null}
        {form_errors form=$form}
        {form_widget form=$form}
    </div>
{/function}

{function hidden_row}
    {form_widget form=$form}
{/function}

{* Misc *}

{function form_enctype}
    {if $multipart}enctype="multipart/form-data"{/if}
{/function}

{function form_errors}
    {if errors|count > 0}
    <ul>
        {foreach $errors as $error}
            <li>{$error.messageTemplate|trans:$error.messageParameters: 'validators'}</li>
        {/foreach}
    </ul>
    {/if}
{/function}

{function form_rest}
    {foreach $form as $child}
        {if !$child.rendered}
            {form_row form=$child}
        {/if}
    {/foreach}
{/function}

{* Support *}

{function form_rows}
    {form_errors form=$form}
    {foreach $form as $child}
        {form_row form=$child}
    {/foreach}
{/function}

{function widget_attributes}
    id="{$id}" name="{$full_name}"{if $read_only} disabled="disabled"{/if}{if $required} required="required"{/if}{if $max_length} maxlength="{$max_length}"{/if}{if $pattern} pattern="{$pattern}"{/if}
    {foreach $attr as $attrname=>$attrvalue}{$attrname}="{$attrvalue}" {/foreach}
{/function}

{function widget_container_attributes}
    id="{$id}"
    {foreach $attr as $attrname=>$attrvalue}{$attrname}="{$attrvalue}" {/foreach}
{/function}

{function slacker_notice}
    <h1 style="color:#900;">Smarty function <code>$function</code> is unfinished. Sorry!</h1>
{/function}