{* $Id: atracking_pageviews.tpl,v 1.13 2005/12/23 12:03:39 max Exp $ *}
{if $statistics}

<table cellspacing="1" class="DataSheet">
<tr class="DataSheet">
	<th width="70%" align="left">{$lng.lbl_page_url}</th>
	<th width="10%">{$lng.lbl_average_time}</th>
	<th width="10%">{$lng.lbl_visits}</th>
	<th width="10%">%</th>
</tr>
{section name=num loop=$statistics}
<tr>
	<td>{math equation="x+1" x=$smarty.section.num.index}. <a href="{$statistics[num].page|amp}">{$statistics[num].page|truncate:70:"..."|amp}</a></td>
	<td align="center">{$statistics[num].time_avg|formatprice:false:false:0}</td>
	<td align="center">{$statistics[num].views}</td>
	<td align="center">{math equation="x/y*100" x=$statistics[num].views y=$all_views assign="value"}{$value|formatprice}</td>
</tr>
{/section}
</table>

<br /><br />

{$lng.txt_top_pages_views_note}

{else}

<br />
<div align="center">{$lng.txt_no_statistics}</div>

{/if}

