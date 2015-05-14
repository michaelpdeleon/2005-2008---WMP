#!/usr/bin/perl
#
# $Id: netssleay.pl,v 1.14 2005/04/13 07:17:19 mclap Exp $
#

require Net::SSLeay;
Net::SSLeay->import ( qw(sslcat));
$Net::SSLeay::slowly = 5; # Add sleep so broken servers can keep up

if ($#ARGV<1) {
 	print <<EOF;
 Usage: $0 host port [cert [keycert]] < requestfile
EOF
	exit;
}

($host, $port, $cert, $kcert) = @ARGV;
$request = "";
while(<STDIN>) {
	$request .= $_;
}

($reply) = sslcat($host, $port, $request, $cert, $kcert);
print $reply;
