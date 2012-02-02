{* Widgets *}

{block "form_widget"}
	<div {block "widget_container_attributes"}{/block}>
		{block "field_rows"}{/block}
		{$form|form_rest}
	</div>
{/block}

{block "field_enctype"}
	{if $multipart}enctype="multipart/form-data"{/if}
{/block}