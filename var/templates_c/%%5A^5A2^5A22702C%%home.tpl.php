<?php /* Smarty version 2.6.12, created on 2014-10-13 01:01:12
         compiled from customer/home.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'customer/home.tpl', 6, false),array('modifier', 'strip_tags', 'customer/home.tpl', 12, false),array('modifier', 'escape', 'customer/home.tpl', 12, false),)), $this); ?>
<?php func_load_lang($this, "customer/home.tpl","lbl_events_calendar"); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php if ($this->_tpl_vars['printable'] != ''): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/home_printable.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['skin_config'])), $this);?>

<html>
<head>
<title>
<?php if ($this->_tpl_vars['config']['SEO']['page_title_format'] == 'A'): ?>
<?php unset($this->_sections['position']);
$this->_sections['position']['name'] = 'position';
$this->_sections['position']['loop'] = is_array($_loop=$this->_tpl_vars['location']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['position']['show'] = true;
$this->_sections['position']['max'] = $this->_sections['position']['loop'];
$this->_sections['position']['step'] = 1;
$this->_sections['position']['start'] = $this->_sections['position']['step'] > 0 ? 0 : $this->_sections['position']['loop']-1;
if ($this->_sections['position']['show']) {
    $this->_sections['position']['total'] = $this->_sections['position']['loop'];
    if ($this->_sections['position']['total'] == 0)
        $this->_sections['position']['show'] = false;
} else
    $this->_sections['position']['total'] = 0;
if ($this->_sections['position']['show']):

            for ($this->_sections['position']['index'] = $this->_sections['position']['start'], $this->_sections['position']['iteration'] = 1;
                 $this->_sections['position']['iteration'] <= $this->_sections['position']['total'];
                 $this->_sections['position']['index'] += $this->_sections['position']['step'], $this->_sections['position']['iteration']++):
$this->_sections['position']['rownum'] = $this->_sections['position']['iteration'];
$this->_sections['position']['index_prev'] = $this->_sections['position']['index'] - $this->_sections['position']['step'];
$this->_sections['position']['index_next'] = $this->_sections['position']['index'] + $this->_sections['position']['step'];
$this->_sections['position']['first']      = ($this->_sections['position']['iteration'] == 1);
$this->_sections['position']['last']       = ($this->_sections['position']['iteration'] == $this->_sections['position']['total']);
?>
<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['location'][$this->_sections['position']['index']]['0'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

<?php if (! $this->_sections['position']['last']): ?> - <?php endif; ?>
<?php endfor; endif; ?>
<?php else: ?>
<?php unset($this->_sections['position']);
$this->_sections['position']['name'] = 'position';
$this->_sections['position']['loop'] = is_array($_loop=$this->_tpl_vars['location']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['position']['step'] = ((int)-1) == 0 ? 1 : (int)-1;
$this->_sections['position']['show'] = true;
$this->_sections['position']['max'] = $this->_sections['position']['loop'];
$this->_sections['position']['start'] = $this->_sections['position']['step'] > 0 ? 0 : $this->_sections['position']['loop']-1;
if ($this->_sections['position']['show']) {
    $this->_sections['position']['total'] = min(ceil(($this->_sections['position']['step'] > 0 ? $this->_sections['position']['loop'] - $this->_sections['position']['start'] : $this->_sections['position']['start']+1)/abs($this->_sections['position']['step'])), $this->_sections['position']['max']);
    if ($this->_sections['position']['total'] == 0)
        $this->_sections['position']['show'] = false;
} else
    $this->_sections['position']['total'] = 0;
if ($this->_sections['position']['show']):

            for ($this->_sections['position']['index'] = $this->_sections['position']['start'], $this->_sections['position']['iteration'] = 1;
                 $this->_sections['position']['iteration'] <= $this->_sections['position']['total'];
                 $this->_sections['position']['index'] += $this->_sections['position']['step'], $this->_sections['position']['iteration']++):
$this->_sections['position']['rownum'] = $this->_sections['position']['iteration'];
$this->_sections['position']['index_prev'] = $this->_sections['position']['index'] - $this->_sections['position']['step'];
$this->_sections['position']['index_next'] = $this->_sections['position']['index'] + $this->_sections['position']['step'];
$this->_sections['position']['first']      = ($this->_sections['position']['iteration'] == 1);
$this->_sections['position']['last']       = ($this->_sections['position']['iteration'] == $this->_sections['position']['total']);
?>
<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['location'][$this->_sections['position']['index']]['0'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

<?php if (! $this->_sections['position']['last']): ?> - <?php endif; ?>
<?php endfor; endif; ?>
<?php endif; ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "meta.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" href="<?php echo $this->_tpl_vars['SkinDir']; ?>
/<?php echo $this->_config[0]['vars']['CSSFile']; ?>
" />
<!-- Start addition by Michael de Leon 09.14.06 -->
<?php echo '
<script language="javascript1.2">
<!-- 
/*
 Pleas leave this notice.
 DHTML tip message version 1.5.4 copyright Essam Gamal 2003 
 Home Page: (http://migoicons.tripod.com)
 Email: (migoicons@hotmail.com)
 Updated on :7/30/2003
*/ 

var MI_IE=MI_IE4=MI_NN4=MI_ONN=MI_NN=MI_pSub=MI_sNav=0;mig_dNav()
var Style=[],Text=[],Count=0,move=0,fl=0,isOK=1,hs,e_d,tb,w=window,PX=(MI_pSub)?"px":""
var d_r=(MI_IE&&document.compatMode=="CSS1Compat")? "document.documentElement":"document.body"
var ww=w.innerWidth
var wh=w.innerHeight
var sbw=MI_ONN? 15:0

function mig_hand(){
if(MI_sNav){
w.onresize=mig_re
document.onmousemove=mig_mo
if(MI_NN4) document.captureEvents(Event.MOUSEMOVE)
}}		

function mig_dNav(){
var ua=navigator.userAgent.toLowerCase()
MI_pSub=navigator.productSub
MI_OPR=ua.indexOf("opera")>-1?parseInt(ua.substring(ua.indexOf("opera")+6,ua.length)):0
MI_IE=document.all&&!MI_OPR?parseFloat(ua.substring(ua.indexOf("msie")+5,ua.length)):0
MI_IE4=parseInt(MI_IE)==4
MI_NN4=navigator.appName.toLowerCase()=="netscape"&&!document.getElementById
MI_NN=MI_NN4||document.getElementById&&!document.all
MI_ONN=MI_NN4||MI_pSub<20020823
MI_sNav=MI_NN||MI_IE||MI_OPR>=7
}

function mig_cssf(){
if(MI_IE>=5.5&&FiltersEnabled){fl=1
var d=" progid:DXImageTransform.Microsoft."
mig_layCss().filter="revealTrans()"+d+"Fade(Overlap=1.00 enabled=0)"+d+"Inset(enabled=0)"+d+"Iris(irisstyle=PLUS,motion=in enabled=0)"+d+"Iris(irisstyle=PLUS,motion=out enabled=0)"+d+"Iris(irisstyle=DIAMOND,motion=in enabled=0)"+d+"Iris(irisstyle=DIAMOND,motion=out enabled=0)"+d+"Iris(irisstyle=CROSS,motion=in enabled=0)"+d+"Iris(irisstyle=CROSS,motion=out enabled=0)"+d+"Iris(irisstyle=STAR,motion=in enabled=0)"+d+"Iris(irisstyle=STAR,motion=out enabled=0)"+d+"RadialWipe(wipestyle=CLOCK enabled=0)"+d+"RadialWipe(wipestyle=WEDGE enabled=0)"+d+"RadialWipe(wipestyle=RADIAL enabled=0)"+d+"Pixelate(MaxSquare=35,enabled=0)"+d+"Slide(slidestyle=HIDE,Bands=25 enabled=0)"+d+"Slide(slidestyle=PUSH,Bands=25 enabled=0)"+d+"Slide(slidestyle=SWAP,Bands=25 enabled=0)"+d+"Spiral(GridSizeX=16,GridSizeY=16 enabled=0)"+d+"Stretch(stretchstyle=HIDE enabled=0)"+d+"Stretch(stretchstyle=PUSH enabled=0)"+d+"Stretch(stretchstyle=SPIN enabled=0)"+d+"Wheel(spokes=16 enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=0,motion=forward enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=0,motion=reverse enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=1,motion=forward enabled=0)"+d+"GradientWipe(GradientSize=1.00,wipestyle=1,motion=reverse enabled=0)"+d+"Zigzag(GridSizeX=8,GridSizeY=8 enabled=0)"+d+"Alpha(enabled=0)"+d+"Dropshadow(OffX=3,OffY=3,Positive=true,enabled=0)"+d+"Shadow(strength=3,direction=135,enabled=0)"
}}

function stm(t,s){
if(MI_sNav&&isOK){	
if(document.onmousemove!=mig_mo||w.onresize!=mig_re) mig_hand()
if(fl&&s[17]>-1&&s[18]>0)mig_layCss().visibility="hidden"
var ab="";var ap=""	
var titCol=s[0]?"COLOR=\'"+s[0]+"\'":""
var titBgCol=s[1]&&!s[2]?"BGCOLOR=\'"+s[1]+"\'":""
var titBgImg=s[2]?"BACKGROUND=\'"+s[2]+"\'":""
var titTxtAli=s[3]?"ALIGN=\'"+s[3]+"\'":""
var txtCol=s[6]?"COLOR=\'"+s[6]+"\'":""
var txtBgCol=s[7]&&!s[8]?"BGCOLOR=\'"+s[7]+"\'":""
var txtBgImg=s[8]?"BACKGROUND=\'"+s[8]+"\'":""
var txtTxtAli=s[9]?"ALIGN=\'"+s[9]+"\'":""
var tipHeight=s[13]? "HEIGHT=\'"+s[13]+"\'":""
var brdCol=s[15]? "BGCOLOR=\'"+s[15]+"\'":""
if(!s[4])s[4]="Verdana,Arial,Helvetica" 
if(!s[5])s[5]=1 
if(!s[10])s[10]="Verdana,Arial,Helvetica" 
if(!s[11])s[11]=1
if(!s[12])s[12]=200
if(!s[14])s[14]=0
if(!s[16])s[16]=0
if(!s[24])s[24]=10
if(!s[25])s[25]=10
hs=s[22]
if(MI_pSub==20001108){
if(s[14])ab="STYLE=\'border:"+s[14]+"px solid"+" "+s[15]+"\'";
ap="STYLE=\'padding:"+s[16]+"px "+s[16]+"px "+s[16]+"px "+s[16]+"px\'"}
var closeLink=hs==3?"<TD ALIGN=\'right\'><FONT SIZE=\'"+s[5]+"\' FACE=\'"+s[4]+"\'><A HREF=\'javascript:void(0)\' ONCLICK=\'mig_hide(0)\' STYLE=\'text-decoration:none;color:"+s[0]+"\'><B>Close</B></A></FONT></TD>":""
var title=t[0]||hs==3?"<TABLE WIDTH=\'100%\' BORDER=\'0\' CELLPADDING=\'0\' CELLSPACING=\'0\' "+titBgCol+" "+titBgImg+"><TR><TD "+titTxtAli+"><FONT SIZE=\'"+s[5]+"\' FACE=\'"+s[4]+"\' "+titCol+"><B>"+t[0]+"</B></FONT></TD>"+closeLink+"</TR></TABLE>":"";
var txt="<TABLE "+ab+" WIDTH=\'"+s[12]+"\' BORDER=\'0\' CELLSPACING=\'0\' CELLPADDING=\'"+s[14]+"\' "+brdCol+"><TR><TD>"+title+"<TABLE WIDTH=\'100%\' "+tipHeight+" BORDER=\'0\' CELLPADDING=\'"+s[16]+"\' CELLSPACING=\'0\' "+txtBgCol+" "+txtBgImg+"><TR><TD "+txtTxtAli+" "+ap+" VALIGN=\'top\'><FONT SIZE=\'"+s[11]+"\' FACE=\'"+s[10]+"\' "+txtCol +">"+t[1]+"</FONT></TD></TR></TABLE></TD></TR></TABLE>"
mig_wlay(txt)
tb={trans:s[17],dur:s[18],opac:s[19],st:s[20],sc:s[21],pos:s[23],xpos:s[24],ypos:s[25]}
if(MI_IE4)mig_layCss().width=s[12]
e_d=mig_ed()
Count=0
move=1
}}

function mig_mo(e){
if(move){
var X=0,Y=0,s_d=mig_scd(),w_d=mig_wd()
var mx=MI_NN?e.pageX:MI_IE4?event.x:event.x+s_d[0]
var my=MI_NN?e.pageY:MI_IE4?event.y:event.y+s_d[1]
if(MI_IE4)e_d=mig_ed()
switch(tb.pos){
case 1:X=mx-e_d[0]-tb.xpos+6;Y=my+tb.ypos;break
case 2:X=mx-(e_d[0]/2);Y=my+tb.ypos;break
case 3:X=tb.xpos+s_d[0];Y=tb.ypos+s_d[1];break
case 4:X=tb.xpos;Y=tb.ypos;break		
default:X=mx+tb.xpos;Y=my+tb.ypos}
if(w_d[0]+s_d[0]<e_d[0]+X+sbw)X=w_d[0]+s_d[0]-e_d[0]-sbw
if(w_d[1]+s_d[1]<e_d[1]+Y+sbw){if(tb.pos>2)Y=w_d[1]+s_d[1]-e_d[1]-sbw;else Y=my-e_d[1]}
if(X<s_d[0])X=s_d[0]
with(mig_layCss()){left=X+PX;top=Y+PX}
mig_dis()
}}

function mig_dis(){Count++
if(Count==1){
if(fl){	
if(tb.trans==51)tb.trans=parseInt(Math.random()*50)
var at=tb.trans>-1&&tb.trans<24&&tb.dur>0 
var af=tb.trans>23&&tb.trans<51&&tb.dur>0
var t=mig_lay().filters[af?tb.trans-23:0]
for(var p=28;p<31;p++){mig_lay().filters[p].enabled=0}
for(var s=0;s<28;s++){if(mig_lay().filters[s].status)mig_lay().filters[s].stop()}
for(var e=1;e<3;e++){if(tb.sc&&tb.st==e){with(mig_lay().filters[28+e]){enabled=1;color=tb.sc}}}
if(tb.opac>0&&tb.opac<100){with(mig_lay().filters[28]){enabled=1;opacity=tb.opac}}
if(at||af){if(at)mig_lay().filters[0].transition=tb.trans;t.duration=tb.dur;t.apply()}}
mig_layCss().visibility=MI_NN4?"show":"visible"
if(fl&&(at||af))t.play()
if(hs>0&&hs<4)move=0
}}

function mig_layCss(){return MI_NN4?mig_lay():mig_lay().style}
function mig_lay(){with(document)return MI_NN4?layers[TipId]:MI_IE4?all[TipId]:getElementById(TipId)}
function mig_wlay(txt){if(MI_NN4){with(mig_lay().document){open();write(txt);close()}}else mig_lay().innerHTML=txt}
function mig_hide(C){if(!MI_NN4||MI_NN4&&C)mig_wlay("");with(mig_layCss()){visibility=MI_NN4?"hide":"hidden";left=0;top=-800}}
function mig_scd(){return [parseInt(MI_IE?eval(d_r).scrollLeft:w.pageXOffset),parseInt(MI_IE?eval(d_r).scrollTop:w.pageYOffset)]}
function mig_re(){var w_d=mig_wd();if(MI_NN4&&(w_d[0]-ww||w_d[1]-wh))location.reload();else if(hs==3||hs==2) mig_hide(1)}
function mig_wd(){return [parseInt(MI_ONN?w.innerWidth:eval(d_r).clientWidth),parseInt(MI_ONN?w.innerHeight:eval(d_r).clientHeight)]}
function mig_ed(){return [parseInt(MI_NN4?mig_lay().clip.width:mig_lay().offsetWidth)+3,parseInt(MI_NN4?mig_lay().clip.height:mig_lay().offsetHeight)+5]}
function htm(){if(MI_sNav&&isOK){if(hs!=4){move=0;if(hs!=3&&hs!=2){mig_hide(1)}}}}

function mig_clay(){
if(!mig_lay()){isOK=0  
alert("DHTML TIP MESSAGE VERSION 1.5 ERROR NOTICE.\\n<DIV ID=\\""+TipId+"\\"></DIV> tag missing or its ID has been altered")} 
else{mig_hand();mig_cssf()}}
//-->
</script>
<script language="JavaScript">
<!--
/*
Dynamic Calendar II (By Jason Moon at jasonmoon@usa.net, http://jasonmoon.virtualave.net/)
Permission granted to Dynamicdrive.com to include script in archive
For this and 100\'s more DHTML scripts, visit http://dynamicdrive.com
*/

//Configure Special_Days
//for yearly events, use \'all_Years\' under years.
//use the last column, for links to other sites.  If no links are needed, use \'javascript:void(0);\'
var Special_Days = []
Special_Days[0]=[\'May\',\'7\',\'2008\',\'stm(Text[0],Style[0])\',\'javascript:void(0);\']
Special_Days[1]=[\'May\',\'8\',\'2008\',\'stm(Text[0],Style[0])\',\'javascript:void(0);\']
Special_Days[2]=[\'June\',\'2\',\'2008\',\'stm(Text[1],Style[0])\',\'javascript:void(0);\']
Special_Days[3]=[\'June\',\'3\',\'2008\',\'stm(Text[1],Style[0])\',\'javascript:void(0);\']
Special_Days[4]=[\'June\',\'4\',\'2008\',\'stm(Text[1],Style[0])\',\'javascript:void(0);\']
Special_Days[5]=[\'September\',\'24\',\'2008\',\'stm(Text[2],Style[0])\',\'javascript:void(0);\']
Special_Days[6]=[\'September\',\'25\',\'2008\',\'stm(Text[2],Style[0])\',\'javascript:void(0);\']
Special_Days[7]=[\'October\',\'16\',\'2008\',\'stm(Text[3],Style[0])\',\'javascript:void(0);\']
Special_Days[8]=[\'October\',\'17\',\'2008\',\'stm(Text[3],Style[0])\',\'javascript:void(0);\']

var ns6=document.getElementById&&!document.all
var ie4=document.all

var Selected_Month;
var Selected_Year;
var Current_Date = new Date();
var Current_Month = Current_Date.getMonth();

var Days_in_Month = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var Month_Label = new Array(\'January\', \'February\', \'March\', \'April\', \'May\', \'June\', \'July\', \'August\', \'September\', \'October\', \'November\', \'December\');

var Current_Year = Current_Date.getYear();
if (Current_Year < 1000)
Current_Year+=1900


var Today = Current_Date.getDate();

function Header(Year, Month) {
	if (Month == 1) {
		Days_in_Month[1] = ((Year % 400 == 0) || ((Year % 4 == 0) && (Year % 100 !=0))) ? 29 : 28;
	}
		var Header_String = Month_Label[Month] + \' \' + Year;
		return Header_String;
}

function Make_Calendar(Year, Month) {
	var First_Date = new Date(Year, Month, 1);
	var Heading = Header(Year, Month);
	var First_Day = First_Date.getDay() + 1;
	
	if (((Days_in_Month[Month] == 31) && (First_Day >= 6)) || ((Days_in_Month[Month] == 30) && (First_Day == 7))) {
		var Rows = 6;
	}
	else if ((Days_in_Month[Month] == 28) && (First_Day == 1)) {
		var Rows = 4;
	}
	else {
		var Rows = 5;
	}

	var HTML_String = \'<table width="10"><tr><td class=cal valign="top"><table BORDER=1 CELLSPACING=0 cellpadding=0 FRAME="box" BGCOLOR="#C0C0C0" BORDERCOLORLIGHT="#808080">\';

	HTML_String += \'<tr><td class=weekend ALIGN="CENTER" BGCOLOR="#FFCCCC" BORDERCOLOR="#C0C0C0">&nbsp;S&nbsp;</td><td class=weekday ALIGN="CENTER" BGCOLOR="#FFFFFF" BORDERCOLOR="#C0C0C0">&nbsp;M&nbsp;</td><td class=weekday ALIGN="CENTER" BGCOLOR="#FFFFFF" BORDERCOLOR="#C0C0C0">&nbsp;T&nbsp;</td><td class=weekday ALIGN="CENTER" BGCOLOR="#FFFFFF" BORDERCOLOR="#C0C0C0">&nbsp;W&nbsp;</td>\';

	HTML_String += \'<td class=weekday ALIGN="CENTER" BGCOLOR="#FFFFFF" BORDERCOLOR="#C0C0C0">&nbsp;T&nbsp;</td><td class=weekday ALIGN="CENTER" BGCOLOR="#FFFFFF" BORDERCOLOR="#C0C0C0">&nbsp;F&nbsp;</td><td class=weekend ALIGN="CENTER" BGCOLOR="#FFCCCC" BORDERCOLOR="#C0C0C0">&nbsp;S&nbsp;</td></tr>\';

	var Day_Counter = 1;
	var Loop_Counter = 1;
	for (var j = 1; j <= Rows; j++) {
	HTML_String += \'<tr>\';

	for (var i = 1; i < 8; i++) {
		if ((Loop_Counter >= First_Day) && (Day_Counter <= Days_in_Month[Month])) {
			for (var k = 0; k < Special_Days.length; k++){
				if (Special_Days[k][0]==Month_Label[Month]&&Special_Days[k][1]==Day_Counter&&((Special_Days[k][2]==Year)||(Special_Days[k][2]==\'all_Years\'))) {
					HTML_String += \'<td class=cal BGCOLOR="#FFF9D4" BORDERCOLOR="#C0C0C0" ALIGN="center"><A class="CalendarSpecialDayLink" href="\' + Special_Days[k][4] + \'" onMouseOver="\' + Special_Days[k][3] + \'" onMouseOut="htm()">\' + Day_Counter + \'</a></td>\';
					if (Day_Counter < Days_in_Month[Month]){
						Day_Counter++;
						i++
						if (i==8){
							i=1
							HTML_String += \'</tr>\';
						}
					}
					else {
						HTML_String += \'<td class=cal BORDERCOLOR="#C0C0C0"> </td>\';
						Day_Counter++;
						Loop_Counter++;
						HTML_String += \'</tr>\';
						HTML_String += \'</table></td></tr></table>\';
						cross_el=ns6? document.getElementById("Calendar") : document.all.Calendar
						cross_el.innerHTML = HTML_String;
						return;
					}
				}
			}
			if ((Day_Counter == Today) && (Year == Current_Year) && (Month == Current_Month)) {
				HTML_String += \'<td class=cal BGCOLOR="#FFF9D4" BORDERCOLOR="#C0C0C0" ALIGN="center"><div class="CalendarTodaysDate">\' + Day_Counter + \'</div></td>\';
			}
			else {
				if ((i==1) || (i==7)){
					HTML_String += \'<td class=cal BGCOLOR="#FFCCCC" BORDERCOLOR="#C0C0C0" ALIGN="center">\' + Day_Counter + \'</td>\';
				}
				else {
					HTML_String += \'<td class=cal BGCOLOR="#FFFFFF" BORDERCOLOR="#C0C0C0" ALIGN="center">\' + Day_Counter + \'</td>\';
				}
			}
			Day_Counter++; 
		}
		else {
			HTML_String += \'<td class=cal BORDERCOLOR="#C0C0C0"> </td>\';
		}
		Loop_Counter++;
	}
		HTML_String += \'</tr>\';
	}
	HTML_String += \'</table></td></tr></table>\';
	cross_el=ns6? document.getElementById("Calendar") : document.all.Calendar
	cross_el.innerHTML = HTML_String;
}

function Check_Nums() {
	//if (event!==\'undefined\')
	if ((event.keyCode < 48) || (event.keyCode > 57)) {
		return false;
	}
}

function On_Year() {
	var Year = document.when.year.value;
	if (Year.length == 4) {
		Selected_Month = document.when.month.selectedIndex;
		Selected_Year = Year;
		Make_Calendar(Selected_Year, Selected_Month);
	}
}

function On_Month() {
	var Year = document.when.year.value;
	if (Year.length == 4) {
		Selected_Month = document.when.month.selectedIndex;
		Selected_Year = Year;
		Make_Calendar(Selected_Year, Selected_Month);
	}
	else {
		alert(\'Please enter a valid year.\');
		document.when.year.focus();
	}
}

function Defaults() {
	if (!ie4&&!ns6)
	return
	var Mid_Screen = Math.round(document.body.clientWidth / 2);
	document.when.month.selectedIndex = Current_Month;
	document.when.year.value = Current_Year;
	Selected_Month = Current_Month;
	Selected_Year = Current_Year;
	Make_Calendar(Current_Year, Current_Month);
}

function Skip(Direction) {
	if (Direction == \'+\') {
		if (Selected_Month == 11) {
			Selected_Month = 0;
			Selected_Year++;
		}
		else {
			Selected_Month++;
		}
	}
	else {
		if (Selected_Month == 0) {
			Selected_Month = 11;
			Selected_Year--;
		}
		else {
			Selected_Month--;
		}
	}

	Make_Calendar(Selected_Year, Selected_Month);
	document.when.month.selectedIndex = Selected_Month;
	document.when.year.value = Selected_Year;
}
//-->
</script>
'; ?>

<style type="text/css">
td.cal {
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
}
td.weekend {
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
color:red;
font-weight:bold;
}
td.weekday {
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
font-weight:bold;
}
input.cal {
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
}
select.cal {
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
}
.CalendarSpecialDayLink:link {
color:#0000FF;
font-weight:bold;
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
text-decoration:underline;
}
.CalendarSpecialDayLink:visited {
color:#0000FF;
font-weight:bold;
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
text-decoration:underline;
}
.CalendarTodaysDate {
color:#FF0000;
font-weight:bold;
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
}
.NavigationArrows:link {
color:#0000FF;
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
}
.NavigationArrows:visited {
color:#0000FF;
font-size:8pt;
font-family:Arial, Helvetica, sans-serif;
}
body {
height:100%
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['SkinDir']; ?>
/v4menu/v4menu.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['SkinDir']; ?>
/v4menu/v4menuOD.css">
<link rel="Shortcut Icon" href="/favicon.ico">
<!-- End of addition by Michael de Leon 09.14.06 -->
</head>
<!-- Start of edit by Michael de Leon 09.14.06 -->
<!-- Deleted by Michael de Leon 12.11.06
<body onload="javascript: " onload="Defaults();">
-->
<body <?php echo $this->_tpl_vars['reading_direction_tag'];  if ($this->_tpl_vars['body_onload'] != '' || $GLOBALS['HTTP_GET_VARS']['mode'] == 'order_message'): ?> onload="<?php if ($GLOBALS['HTTP_GET_VARS']['mode'] == 'order_message'): ?>javascript:__utmSetTrans();<?php endif; ?> <?php echo $this->_tpl_vars['body_onload']; ?>
"<?php else: ?> onload="Defaults();"<?php endif; ?>>
<!-- End of edit by Michael de Leon 09.14.06 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "rectangle_top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['active_modules']['SnS_connector']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/SnS_connector/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<!-- main area -->
<table width="634" cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
		<td class="VertMenuLeftColumn">
<!-- Start addition of Michael de Leon 10.31.06 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "v4menu/v4menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "v4menu/v4menu2.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- End addition of Michael de Leon 10.31.06 -->
<!-- Deleted by Michael de Leon 09.22.06
<br />
-->
<!-- Deleted by Michael de Leon 09.14.06
-->
<?php if ($this->_tpl_vars['active_modules']['Survey'] && $this->_tpl_vars['menu_surveys']): ?>
<?php $_from = $this->_tpl_vars['menu_surveys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['menu_survey']):
?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Survey/menu_survey.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
<!-- Deleted by Michael de Leon 09.14.06
-->
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="150" height="1" alt="" />
		</td>
		<td valign="top">
<!-- central space -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "location.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_message.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['active_modules']['Special_Offers'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/new_offers_message.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/home_main.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- /central space -->
&nbsp;
		</td>
		<td class="VertMenuRightColumn">
<?php if ($this->_tpl_vars['active_modules']['SnS_connector'] && $this->_tpl_vars['config']['SnS_connector']['sns_display_button'] == 'Y' && $this->_tpl_vars['sns_collector_path_url'] != ''): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/SnS_connector/button.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Feature_Comparison'] != "" && $this->_tpl_vars['comparison_products'] != ''): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Feature_Comparison/product_list.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<?php endif; ?>
<!-- Deleted by Michael de Leon 10.26.06
<br />
-->
<!-- Start addition by Michael de Leon 10.26.06 -->
<?php if ($this->_tpl_vars['login'] == ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "auth.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "authbox.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/menu_cart.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<DIV id="tip101" style="visibility:hidden;position:absolute;z-index:1000;top:-100"></DIV>
<?php echo '
<script language="JavaScript1.2" type="text/javascript">
/*
Pleas leave this notice.
DHTML tip message version 1.5 copyright Essam Gamal 2003.
Home Pag: http://migoicons.tripod.com
Email migoicons@hotmail.com
Script featured on and can be found at Dynamic Drive (http://www.dynamicdrive.com)
*/ 

//Style format
//Style[0]=["title text color","title bg color","title bg img","title text align","title font face",title font size,"text color","text bg color","text bg img","text align","text font face",
//text font size,tip msg width,tip msg height,tip msg border size,"tip msg border color",tip msg text padding,tip msg popup effect,tip msg popup effect duration,tip msg transparency,
//tip msg shadow type,"tip msg shadow color",tip msg behavior,tip msg position,tip msg x coord,tip msg y coord]
Style[0]=["#ffffff","#006688","","","",1,"#000000","#fff9d4","","","",1,250,,1,"#000000",3,24,0.5,,2,"#666666",,,-3,25]
Text[0]=["&nbsp;2008 NIH (National Institutes of Health) <br />&nbsp;Spring Research Festival Exhibit","May 7-8, 2008<br />Registered - Booth 407<br />Bethesda, MD"]
Text[1]=["&nbsp;ASM (American Society for Microbiology) <br />&nbsp;2008","June 2-4, 2008<br />Boston, MA"]
Text[2]=["&nbsp;2008 Biomedical Research Equipment and <br />&nbsp;Supplies Exhibit at Harvard Medical School","September 24-25, 2008<br />Boston, MA"]
Text[3]=["&nbsp;2008 NIH (National Institutes of Health) <br />&nbsp;Research Festival Exhibit","October 16-17, 2008<br />Bethesda, MD"]

var TipId="tip101"
var FiltersEnabled = 1 // [for IE5.5+] if your not going to use transitions or filters in any of the tips set this to zero.
mig_clay()
</script>
'; ?>

<?php ob_start(); ?>
<table align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td><div id=NavBar></div></td>
	</tr>
<form name="when">
	<tr>
		<td><select class="cal" name="month" onChange="On_Month()">
<?php echo '
<script language="JavaScript1.2">
if (ie4||ns6){
	for (j=0;j<Month_Label.length;j++) {
		document.writeln(\'<option value=\' + j + \'>\' + Month_Label[j]);
	}
}
</script>
'; ?>

</select>
<input class="cal" type="text" name="year" size=4 maxlength=4 onKeyPress="return Check_Nums()" onKeyUp="On_Year()">
		</td>
	</tr>
</form>
</table>
<table align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" align="left"><div align="left"><a class="NavigationArrows" title="previous" href="javascript:void(0);" onClick="Skip('-')">&lt;</a></div></td>
		<td><div id=Calendar></div></td>
		<td valign="top" align="right"><div align="right"><a class="NavigationArrows" title="next" href="javascript:void(0);" onClick="Skip('+')">&gt;</a></div></td>
	</tr>
</table>
<?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.tpl", 'smarty_include_vars' => array('dingbats' => "wwmp_calendar_icon10.26.06.jpg",'menu_title' => $this->_tpl_vars['lng']['lbl_events_calendar'],'menu_content' => $this->_smarty_vars['capture']['menu'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<!-- End addition by Michael de Leon 10.26.06 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "news.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['active_modules']['XAffiliate'] != ""): ?>
<br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "partner/menu_affiliate.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Interneka'] != ""): ?>
<br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Interneka/menu_interneka.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "poweredby.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br />
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="150" height="1" alt="" />
		</td>
	</tr>
</table>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "rectangle_bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- Start addition by Michael de Leon 12.11.06 for Google Analytics -->
<?php if ($GLOBALS['HTTP_SERVER_VARS']['HTTPS'] == 'on'): ?> 
<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript"></script>
<?php else: ?>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript">
_uacct = "UA-1055051-1";
urchinTracker();
</script>
<!-- End addition by Michael de Leon 12.11.06 for Google Analytics -->
</body>
</html>
<?php endif; ?>