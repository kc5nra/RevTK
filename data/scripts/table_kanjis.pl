#!/usr/bin/perl
###############################################################################
#
#	table_kanjis.pl
#
#	Create tab-delimited data to load into the database KANJIS table.
#	For the website 'Reviewing the Kanji'.
#
#	keyword
#	kanji
#	onyomi
#	framenum
#	lessonnum
#	strokecount
#
#	Takes input from:
#	- "Heisig Kanji Index" (http://ziggr.com)
#	For Volume 3 kanji (frames 2043-3007) :
#	- RTK 3 keywords index compiled by 'kurojohn' (John Vold)
#	- kanjidic2 in XML format from Jim Breen's site
#
# Usage:
#   
#   $ cd data/scripts
#   $ perl table_kanjis.pl > ../generated/table_kanjis.utf8
#
# Then in MySQL console execute:
# 
#   mysql> LOAD DATA LOCAL INFILE 'table_kanjis.utf8' INTO TABLE kanjis;
#
# @author    Fabrice Denis
#	
###############################################################################

use strict;

# thanks to Zig (http://ziggr.com) for compiling the Heisig Kanji Index
my $ZIGFILE = '../datafiles/rtk1_data.utf8';
# rtk3 keywords compiled by John 'kurojohn' Vold
my $RTK3KEYWORDSFILE = '../datafiles/rtk3_keywords.txt';
# kanjidic2 XML format file from Jim Breen
my $KANJIDICFILE = '../datafiles/download/kanjidic2.xml.utf8';
# lesson number for the kanji in RTK Volume 3.
my $RTK3LESSON = 57;
# first frame number in RTK Volume 3
my $RTK3_FRAME_START = 2043;

# #
my %heisig;
my %rtk3kw;
my %onyomi;


	#	get RtK keywords from Ziggr and kurojohn's files
	get_rtk1(\%heisig, $ZIGFILE);
	get_rtk3_keywords(\%rtk3kw, $RTK3KEYWORDSFILE);
	check_missing_rtk (\%heisig);

	get_kanjidic(\%heisig, \%rtk3kw, \%onyomi, $KANJIDICFILE);

	
	#
	#	Output
	#
	foreach (sort keys %heisig)
	{
		my $framenum = $_;

		#heisignumber:kanji:keyword:strokecount:indexordinal:lessonnumber
		my @kdata = split /:/, $heisig{$_};

		my $on = '\N'; # NULL value for LOAD DATA
		if (exists $onyomi{$kdata[0]}) {
			$on = $onyomi{$kdata[0]};
		}

		# 	output tab-delimited data
		# 	KEYWORD KANJI ONYOMI1 FRAMENUM LESSONNUM STROKECOUNT
		print "$kdata[2]\t$kdata[1]\t$on\t$kdata[0]\t$kdata[5]\t$kdata[3]\n";
	}
	

#
#	Ziggr's file contains the RTK1 keywords and RTK lesson numbers
#
sub get_rtk1
{
	my ($rtk, $zigfile) = @_;
    open ZIGGR, $zigfile or die "Cannot open file '$zigfile' : $!";
	while (<ZIGGR>)
	{
		chomp;
		#	Ignore comment lines
		if (/^#/) {
			next;
		}
		
		# heisignumber:kanji:keyword:strokecount:indexordinal:lessonnumber
		my @data = split /:/, $_;
		
		# build array to be sorted
		my $framenumber = $data[0];
		$framenumber = sprintf('%05d', $framenumber);

		# error check
		if (exists $rtk->{$framenumber}){
			print STDERR "Error: frame number $framenumber duplicate\n";
			exit;
		}

		$rtk->{$framenumber} = $_;
	}
	close (ZIGGR);
}

#	kurojohn's file contains the RTK3 keywords
#
sub get_rtk3_keywords
{
	my ($rtk3kw, $kwfile) = @_;
    open KWFILE, $kwfile or die "Cannot open file '$kwfile' : $!";
	while (<KWFILE>)
	{	
		chomp;
		# sample line
		# nnnn.  keyword
		# regex: capture keyword with optional spaces in-between, but strip spacing at end-of-line
		if (/(\d{4})\. +(\S.+\S|\S+)\s*$/)
		{
			$rtk3kw{'0'.$1} = $2;
		#	check for special characters
		#	my $f = $1;
		#	my $k = $2;
		#	if ($k =~ /[^a-zA-Z -\[\]]/)
		#	{
		#		print "lalala $f : '$k'\n";
		#	}
		}
		else
		{
			print STDERR "Oops: could not parse line '$_'\n";
		}
	}
	close (KWFILE);
}

#	
#	fill up the missing data from kanjidic2
#
sub get_kanjidic
{
	my ($rtk, $rtk3kw, $onyomi, $kdicfile) = @_;
	my %K = ();

	my $fh = openit ($kdicfile);
	while (fParseKanji($fh, \%K))
	{
		# framenum = heisig rtk number OR unicode (starts at 0x3000)
		my $framenum;
		my $framenumkey;
		
		$framenum = $K{'heisignum'} || sprintf('%d', eval('0x'.$K{'ucs'})); 
		$framenumkey = sprintf('%05d', $framenum);
		
		if ($framenum<0) {
			print STDERR 'framenum problem';
			exit(0);
		}

		#	rtk3 and beyond kanji, complete data
		if ($framenum >= $RTK3_FRAME_START)
		{
			# RTK3 -OR- CJK unifed ideographs - Common and uncommon kanji (4e00 - 9faf)
			if ($K{'heisignum'} || ($framenum >= 0x4e00 && $framenum <= 0x9fa5))
			{
			
				my $keyword = $rtk3kw{$framenumkey} || ('Unicode-0x'.$K{'ucs'});
				my $rtklesson = $K{'heisignum'} ? $RTK3LESSON : 0;
				my $s_framenum = $K{'heisignum'} || '0x'.$K{'ucs'};
				
				#heisignumber:kanji:keyword:strokecount:indexordinal:lessonnumber
				$rtk->{$framenumkey} = sprintf(
					'%s:%s:%s:%s::%d',
					$framenum, $K{'utf8'}, $keyword, $K{'strokec'}, $rtklesson
					);
			}
		}

		#onyomi
		if ($K{'oncount'} > 0)
		{
			$onyomi->{$K{'heisignum'}} = $K{'onyomi'};
		}
	}
	close ($fh);
}


sub openit {
	my ($filename) = @_;
	local *FH;
    open (FH, $filename) or die "Cannot open file '$filename': $!";
    return *FH;
}


#	parse one kanji from kanjidic2 XML format (see Jim Breen's site)
sub fParseKanji {
	my ($fh, $K) = @_;
	$K->{'found'} = 0;
	while (<$fh>) {
		if ( /<character>/ )
		{
			$K->{'found'} = 1;
			$K->{'utf8'} = '';
			$K->{'strokec'} = 0;
			$K->{'heisignum'} = 0;
			$K->{'ucs'} = 0;
		#	$K->{'klearners'} = 0;
			$K->{'onyomi'} = '';
			$K->{'oncount'} = 0; # count how many readings were found
		#	$K->{'kunyomi'} = '';
		#	$K->{'radical'} = 0;
		}
		elsif ( /<literal>(.*)<\/literal>/ )
		{
			$K->{'utf8'} = $1;
		}

		#	unicode hexdecimal
		elsif ( /<cp_value cp_type="ucs">([^<]+)<\// )
		{
			$K->{'ucs'} = $1;
		}

	#	elsif ( /<rad_value rad_type="classical">(.*)<\/rad_value>/ ) {
	#		$K->{'radical'} = $1;
	#	}
		elsif ( /<stroke_count>(.*)<\// ) {
			$K->{'strokec'} = $1;
		}
		elsif ( /<dic_ref dr_type="heisig">(.*)<\// ) {
			$K->{'heisignum'} = $1;
		}
	#	elsif ( /<dic_ref dr_type="halpern_kkld">(.*)<\/dic_ref>/ ) {
	#		$K->{'klearners'} = $1;
	#	}
		elsif ( /<reading r_type="ja_on"[^>]*>(.*)<\// ) {	
			
			# limit to one main reading for database atm. (may 2007)
			if (!$K->{'oncount'}) {
				$K->{'onyomi'} .= $1.' ';
				$K->{'oncount'}++;
			}
		}
	#	elsif ( /<reading r_type="ja_kun">(.*)<\// ) {
	#		# maybe more than one line
	#		# xx.yy  ->  xx(yy)   the dot separates verb endings?
	#		$K->{'kunyomi'} .= $1;
	#	}
		elsif ( /<\/character>/ )
		{
			if ($K->{'found'}) {
				return 1;
			}
			else {
				print "BUG 12641263";
				return 0;
			}
		}
	}
	return 0;
}


#	Check for missing frame numbers
sub check_missing_rtk
{
	my ($rtk) = @_;
	my $n = 1;
	foreach (sort keys %$rtk)
	{
		my $n05d = sprintf('%05d', $n);
		if ($_ ne $n05d)
		{
			print STDERR "Missing frame number $n05d ($_) ?\n";
			last;
		}
		$n++;
	}
}
