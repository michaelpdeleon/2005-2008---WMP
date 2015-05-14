{* $Id: rectangle_bottom.tpl,v 1.12 2006/02/10 14:27:31 svowl Exp $ *}
	</td>
</tr>
{if $active_modules.Users_online ne ""}
<tr>
	<td>{include file="modules/Users_online/menu_users_online.tpl"}</td>
</tr>
{else}
<tr>
	<td><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{/if}
<tr>
	<td class="BottomRow">
{if $printable ne ''}
<hr size="1" noshade="noshade" />
{/if}
{ include file="bottom.tpl" }
	</td>
</tr>
</table>
