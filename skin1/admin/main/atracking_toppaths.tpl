{* $Id: atracking_toppaths.tpl,v 1.12 2006/01/04 10:58:58 max Exp $ *}
{if $statistics}

<table cellspacing="1" class="DataSheet">
<tr class="DataSheet">
	<th width="10">#</th>
	<th width="80%" align="left">{$lng.lbl_paths_through_site_average_time}</th>
	<th width="10%">{$lng.lbl_visits}</th>
	<th width="10%">%</th>
</tr>
{section name=num loop=$statistics}
<tr>
	<td>{math equation="x+1" x=$smarty.section.num.index}</td>
	<td>
<table cellpadding="0" cellspacing="0">
{section name=cnum loop=$statistics[num].pages}
<tr>
	<td>{math equation="x+1" x=$smarty.section.cnum.index}. <a href="{$statistics[num].pages[cnum].page|amp}">{$statistics[num].pages[cnum].page|truncate:70:"..."}</a> ({$statistics[num].pages[cnum].time_avg|formatprice})</td>
</tr>
{/section}
</table>
	</td>
	<td align="center">{$statistics[num].counter}</td>
	<td align="center">{math equation="x/y*100" x=$statistics[num].counter y=$all_views assign="value"}{$value|formatprice}</td>
</tr>
{/section}
</table>

<br /><br />
{$lng.txt_top_paths_through_site_note}

{else}

<br />
<center>{$lng.txt_no_statistics}</center>

{/if}

