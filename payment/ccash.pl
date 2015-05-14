#!/usr/bin/perl

#
# $Id: ccash.pl,v 1.5 2003/11/11 13:49:02 mclap Exp $
#

BEGIN
{
# !!! IMPORTANT !!!
# Path to mck-cgi dir. For example: /mystore/merchantid/mck-cgi
$path2mck = "/path-to-mck/login/mck-cgi";
push(@INC,$path2mck);
}

# MCK function
use CCMckLib3_2 qw(InitConfig);
use CCMckDirectLib3_2 qw(SendCC2_1Server doDirectPayment);
use CCMerchantTest qw($ConfigFile);
use CCMerchantCustom qw(GenerateOrderId);


exit if (!($#ARGV+1)); 

foreach $item (@ARGV)
{
	$key="";
	$val="";
	($key,$val)=split('=',$item,2);
	if ($key ne "")
	{
		$val=~ s/\"//; 
		$args{$key}=$val;
	}
}

$status= &InitConfig ($ConfigFile);
if($status)
	{ print "2,,Unable to initialize configuration";exit;}

%result = &SendCC2_1Server ('mauthcapture', %args);

# print all the name-value pairs returned by the message
#foreach (keys (%result))
#{ print (" $_ -> $result{$_}\n"); }

if (($result{'MStatus'} ne "success") && ($result {'MStatus'} ne "success-duplicate"))
	{ print "2,,".$result{'MErrMsg'}." (ErrCode: ".$result{'MErrCode'}.")"; }
else
	{ print "1,".$result{'avs-code'}.",AuthCode: ".$result{'auth-code'}."; ActionCode: ".$result{'action-code'}."; RefCode: ".$result{'ref-code'};}
