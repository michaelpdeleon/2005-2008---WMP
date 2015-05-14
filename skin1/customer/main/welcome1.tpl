{* $Id: welcome.tpl,v 1.28.2.1 2006/07/12 04:51:17 svowl Exp $ *}
<!-- Start of addition by Michael de Leon 09.14.06 -->
<script type="text/javascript" src="ieupdate.js"></script>
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
  	<td class="welcome_box1">
	<script type="text/javascript">startIeFix();</script><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="632" height="160" id="bioexcell_flash_intro" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="{$ImagesDir}/bioexcell_flash_intro.swf" />
<param name="menu" value="false" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<param name="wmode" value="transparent" />
<embed src="{$ImagesDir}/bioexcell_flash_intro.swf" menu="false" quality="high" bgcolor="#ffffff" wmode="transparent" width="632" height="160" name="bioexcell_flash_intro" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object><!-- --><script type="text/javascript">endIeFix();</script><br>
	</td>
  </tr>
  <tr>
  	<td class="welcome_box2">
	<img src="{$ImagesDir}/wwmp_bldg10.25.06.jpg"><br>
	</td>
  </tr>
  <tr>
  	<td class="welcome_box4" align="center">
	<!--
		<img class="wwmp_whatsnewtitle" src="{* $ImagesDir *}/wwmp_whatsnew_title10.27.06.jpg"><br>
	-->
		<img src="{ $ImagesDir }/wwmp_whatsnew1_01.11.07.jpg"><img class="wwmp_whatsnewpics" src="{ $ImagesDir }/wwmp_whatsnew2_01.11.07.jpg"><img src="{ $ImagesDir }/wwmp_whatsnew3_01.11.07.jpg">
	</td>
  </tr>
  <tr>
  	<td class="welcome_box3" align="center">
	<!--
		<img class="wwmp_whatsnewtitle" src="{* $ImagesDir *}/wwmp_whatsnew_title10.27.06.jpg"><br>
	-->
		<img src="{ $ImagesDir }/wwmp_whatsnew1_01.11.07.jpg"><img class="wwmp_whatsnewpics" src="{ $ImagesDir }/wwmp_whatsnew2_01.11.07.jpg"><img src="{ $ImagesDir }/wwmp_whatsnew3_01.11.07.jpg">
	</td>
  </tr>
  <!--
  <tr>
  	<td width="634" height="178" background="{*$ImagesDir*}/wwmp_mission01.10.07.jpg" align="left" class="welcome_box3"><br /><font class="wwmp_mission_title">WorldWide Medical Products, Inc.</font><br />
	  <img class="wwmp_mission_logo" src="{*$ImagesDir*}/wwmp_logo10.25.06.jpg" width="100" height="71" align="left">
	  <p class="wwmp_mission_p1"><strong>WorldWide Medical Products, Inc. (WWMP)</strong> - a technologically advanced, quality-driven provider of essential laboratory wares and services to the scientific community, is dedicated to exceeding the wants and needs of our clients. Our mission is to utilize our unique comprehension and understanding of the research and development market, while maintaining the ability to respond and adapt to our clients needs. The personal attention and custom-tailored solutions our clients deserve are ultimately achieved through the collaboration of our team of technical and professional consultative representatives.</p>
  	  </td>
  </tr>
  -->
</table>
</div>
<br>
<!-- End of addition by Michael de Leon 09.14.06 -->
<!-- Deleted by Michael de Leon 09.14.06
{* if ($active_modules.Greet_Visitor ne "") and ($smarty.cookies.GreetingCookie ne "") and $logout_user eq ''*}
{* assign var="_name" value=$smarty.cookies.GreetingCookie|replace:"\'":"'" *}
<h3>{* $lng.lbl_welcome_back|substitute:"name":$_name *} </h3> 
{* elseif $lng.lbl_site_title *}
<h3>{* $lng.lbl_welcome_to|substitute:"company":$lng.lbl_site_title *}</h3>
{* else *}
<h3>{* $lng.lbl_welcome_to|substitute:"company":$config.Company.company_name *}</h3>
{* /if *}
{* $lng.txt_welcome *}
<br />
-->
<!-- Start of edit by Michael de Leon 09.14.06 -->
{* if $active_modules.Bestsellers ne "" and $config.Bestsellers.bestsellers_menu eq "Y" *}
{* include file="modules/Bestsellers/bestsellers.tpl" *}
{* /if *}
<!-- End of edit by Michael de Leon 09.14.06 -->
<!-- Deleted by Michael de Leon 09.14.06
<br />
{* include file="customer/main/featured.tpl" f_products=$f_products *}
-->