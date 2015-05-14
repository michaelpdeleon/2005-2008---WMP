{* $Id: ps_paypal_pro.tpl,v 1.8.2.2 2006/07/22 08:09:11 max Exp $ *}
<table cellspacing="10">
<tr>
<td>{$lng.lbl_paypal_api_access_username}:</td>
<td><input type="text" name="{$conf_prefix}[param01]" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_paypal_api_access_password}:</td>
<td><input type="text" name="{$conf_prefix}[param02]" size="24" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td valign="top">{$lng.lbl_paypal_api_use_method}:</td>
<td>
<table>
<tr>
	<td><input type="radio" id="APIS" name="{$conf_prefix}[param07]" value="S"{if $module_data.param07 ne 'C'} checked="checked"{/if} /></td>
	<td><label for="APIS">{$lng.lbl_paypal_api_signature_type}</label></td>
</tr>
<tr>
	<td><input type="radio" id="APIC" name="{$conf_prefix}[param07]" value="C"{if $module_data.param07 eq 'C'} checked="checked"{/if} /></td>
	<td><label for="APIC">{$lng.lbl_paypal_api_certificate_type}</label></td>
</tr>
</table>
</td>
</tr>

<tr>
<td>{$lng.lbl_paypal_api_certificate_file}:</td>
<td>
xcart_dir/payment/certs/<input type="text" name="{$conf_prefix}[param04]" size="24" value="{$module_data.param04|escape}" />
</td>
</tr>

<tr>
<td>{$lng.lbl_paypal_api_access_signature}:</td>
<td><input type="text" name="{$conf_prefix}[param05]" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="{$conf_prefix}[param03]">
<option value="AFA"{if $module_data.param03 eq "AFA"} selected="selected"{/if}>Afghani (Afghanistan)
<option value="DZD"{if $module_data.param03 eq "DZD"} selected="selected"{/if}>Algerian Dinar (Algeria)
<option value="ADP"{if $module_data.param03 eq "ADP"} selected="selected"{/if}>Andorran Peseta (Andorra)
<option value="ARS"{if $module_data.param03 eq "ARS"} selected="selected"{/if}>Argentine Peso (Argentina)
<option value="AMD"{if $module_data.param03 eq "AMD"} selected="selected"{/if}>Armenian Dram (Armenia)
<option value="AWG"{if $module_data.param03 eq "AWG"} selected="selected"{/if}>Aruban Guilder (Aruba)
<option value="AUD"{if $module_data.param03 eq "AUD"} selected="selected"{/if}>Australian Dollar (Australia)
<option value="AZM"{if $module_data.param03 eq "AZM"} selected="selected"{/if}>Azerbaijanian Manat (Azerbaijan)
<option value="BSD"{if $module_data.param03 eq "BSD"} selected="selected"{/if}>Bahamian Dollar (Bahamas)
<option value="BHD"{if $module_data.param03 eq "BHD"} selected="selected"{/if}>Bahraini Dinar (Bahrain)
<option value="THB"{if $module_data.param03 eq "THB"} selected="selected"{/if}>Baht (Thailand)
<option value="PAB"{if $module_data.param03 eq "PAB"} selected="selected"{/if}>Balboa (Panama)
<option value="BBD"{if $module_data.param03 eq "BBD"} selected="selected"{/if}>Barbados Dollar (Barbados)
<option value="BYR"{if $module_data.param03 eq "BYB"} selected="selected"{/if}>Belarussian Ruble (Belarus)
<option value="BZD"{if $module_data.param03 eq "BZD"} selected="selected"{/if}>Belize Dollar (Belize)
<option value="BMD"{if $module_data.param03 eq "BMD"} selected="selected"{/if}>Bermudian Dollar (Bermuda)
<option value="VEB"{if $module_data.param03 eq "VEB"} selected="selected"{/if}>Bolivar (Venezuela)
<option value="BOB"{if $module_data.param03 eq "BOB"} selected="selected"{/if}>Boliviano (Bolivia)
<option value="BRL"{if $module_data.param03 eq "BRL"} selected="selected"{/if}>Brazilian Real (Brazil)
<option value="BND"{if $module_data.param03 eq "BND"} selected="selected"{/if}>Brunei Dollar (Brunei Darussalam)
<option value="BGN"{if $module_data.param03 eq "BGN"} selected="selected"{/if}>Bulgarian Lev (Bulgaria)
<option value="BIF"{if $module_data.param03 eq "BIF"} selected="selected"{/if}>Burundi Franc (Burundi)
<option value="CAD"{if $module_data.param03 eq "CAD"} selected="selected"{/if}>Canadian Dollar (Canada)
<option value="CVE"{if $module_data.param03 eq "CVE"} selected="selected"{/if}>Cape Verde Escudo (Cape Verde)
<option value="KYD"{if $module_data.param03 eq "KYD"} selected="selected"{/if}>Cayman Islands Dollar (Cayman Islands)
<option value="GHC"{if $module_data.param03 eq "GHC"} selected="selected"{/if}>Cedi (Ghana)
<option value="XOF"{if $module_data.param03 eq "XOF"} selected="selected"{/if}>CFA Franc BCEAO (Guinea-Bissau)
<option value="XAF"{if $module_data.param03 eq "XAF"} selected="selected"{/if}>CFA Franc BEAC (Central African Republic)
<option value="XPF"{if $module_data.param03 eq "XPF"} selected="selected"{/if}>CFP Franc (New Caledonia)
<option value="CLP"{if $module_data.param03 eq "CLP"} selected="selected"{/if}>Chilean Peso (Chile)
<option value="COP"{if $module_data.param03 eq "COP"} selected="selected"{/if}>Colombian Peso (Colombia)
<option value="KMF"{if $module_data.param03 eq "KMF"} selected="selected"{/if}>Comoro Franc (Comoros)
<option value="BAM"{if $module_data.param03 eq "BAM"} selected="selected"{/if}>Convertible Marks (Bosnia And Herzegovina)
<option value="NIO"{if $module_data.param03 eq "NIO"} selected="selected"{/if}>Cordoba Oro (Nicaragua)
<option value="CRC"{if $module_data.param03 eq "CRC"} selected="selected"{/if}>Costa Rican Colon (Costa Rica)
<option value="CUP"{if $module_data.param03 eq "CUP"} selected="selected"{/if}>Cuban Peso (Cuba)
<option value="CYP"{if $module_data.param03 eq "CYP"} selected="selected"{/if}>Cyprus Pound (Cyprus)
<option value="CZK"{if $module_data.param03 eq "CZK"} selected="selected"{/if}>Czech Koruna (Czech Republic)
<option value="GMD"{if $module_data.param03 eq "GMD"} selected="selected"{/if}>Dalasi (Gambia)
<option value="DKK"{if $module_data.param03 eq "DKK"} selected="selected"{/if}>Danish Krone (Denmark)
<option value="MKD"{if $module_data.param03 eq "MKD"} selected="selected"{/if}>Denar (The Former Yugoslav Republic Of Macedonia)
<option value="AED"{if $module_data.param03 eq "AED"} selected="selected"{/if}>Dirham (United Arab Emirates)
<option value="DJF"{if $module_data.param03 eq "DJF"} selected="selected"{/if}>Djibouti Franc (Djibouti)
<option value="STD"{if $module_data.param03 eq "STD"} selected="selected"{/if}>Dobra (Sao Tome And Principe)
<option value="DOP"{if $module_data.param03 eq "DOP"} selected="selected"{/if}>Dominican Peso (Dominican Republic)
<option value="VND"{if $module_data.param03 eq "VND"} selected="selected"{/if}>Dong (Vietnam)
<option value="XCD"{if $module_data.param03 eq "XCD"} selected="selected"{/if}>East Caribbean Dollar (Grenada)
<option value="EGP"{if $module_data.param03 eq "EGP"} selected="selected"{/if}>Egyptian Pound (Egypt)
<option value="SVC"{if $module_data.param03 eq "SVC"} selected="selected"{/if}>El Salvador Colon (El Salvador)
<option value="ETB"{if $module_data.param03 eq "ETB"} selected="selected"{/if}>Ethiopian Birr (Ethiopia)
<option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro (Europe)
<option value="FKP"{if $module_data.param03 eq "FKP"} selected="selected"{/if}>Falkland Islands Pound (Falkland Islands)
<option value="FJD"{if $module_data.param03 eq "FJD"} selected="selected"{/if}>Fiji Dollar (Fiji)
<option value="HUF"{if $module_data.param03 eq "HUF"} selected="selected"{/if}>Forint (Hungary)
<option value="CDF"{if $module_data.param03 eq "CDF"} selected="selected"{/if}>Franc Congolais (The Democratic Republic Of Congo)
<option value="GIP"{if $module_data.param03 eq "GIP"} selected="selected"{/if}>Gibraltar Pound (Gibraltar)
<option value="HTG"{if $module_data.param03 eq "HTG"} selected="selected"{/if}>Gourde (Haiti)
<option value="PYG"{if $module_data.param03 eq "PYG"} selected="selected"{/if}>Guarani (Paraguay)
<option value="GNF"{if $module_data.param03 eq "GNF"} selected="selected"{/if}>Guinea Franc (Guinea)
<option value="GWP"{if $module_data.param03 eq "GWP"} selected="selected"{/if}>Guinea-Bissau Peso (Guinea-Bissau)
<option value="GYD"{if $module_data.param03 eq "GYD"} selected="selected"{/if}>Guyana Dollar (Guyana)
<option value="HKD"{if $module_data.param03 eq "HKD"} selected="selected"{/if}>Hong Kong Dollar (Hong Kong)
<option value="UAH"{if $module_data.param03 eq "UAH"} selected="selected"{/if}>Hryvnia (Ukraine)
<option value="ISK"{if $module_data.param03 eq "ISK"} selected="selected"{/if}>Iceland Krona (Iceland)
<option value="INR"{if $module_data.param03 eq "INR"} selected="selected"{/if}>Indian Rupee (India)
<option value="IRR"{if $module_data.param03 eq "IRR"} selected="selected"{/if}>Iranian Rial (Islamic Republic Of Iran)
<option value="IQD"{if $module_data.param03 eq "IQD"} selected="selected"{/if}>Iraqi Dinar (Iraq)
<option value="JMD"{if $module_data.param03 eq "JMD"} selected="selected"{/if}>Jamaican Dollar (Jamaica)
<option value="JOD"{if $module_data.param03 eq "JOD"} selected="selected"{/if}>Jordanian Dinar (Jordan)
<option value="KES"{if $module_data.param03 eq "KES"} selected="selected"{/if}>Kenyan Shilling (Kenya)
<option value="PGK"{if $module_data.param03 eq "PGK"} selected="selected"{/if}>Kina (Papua New Guinea)
<option value="LAK"{if $module_data.param03 eq "LAK"} selected="selected"{/if}>Kip (Lao People's Democratic Republic)
<option value="EEK"{if $module_data.param03 eq "EEK"} selected="selected"{/if}>Kroon (Estonia)
<option value="HRK"{if $module_data.param03 eq "HRK"} selected="selected"{/if}>Kuna (Croatia)
<option value="KWD"{if $module_data.param03 eq "KWD"} selected="selected"{/if}>Kuwaiti Dinar (Kuwait)
<option value="MWK"{if $module_data.param03 eq "MWK"} selected="selected"{/if}>Kwacha (Malawi)
<option value="ZMK"{if $module_data.param03 eq "ZMK"} selected="selected"{/if}>Kwacha (Zambia)
<option value="AOA"{if $module_data.param03 eq "AOA"} selected="selected"{/if}>Kwanza (Angola)
<option value="MMK"{if $module_data.param03 eq "MMK"} selected="selected"{/if}>Kyat (Myanmar)
<option value="GEL"{if $module_data.param03 eq "GEL"} selected="selected"{/if}>Lari (Georgia)
<option value="LVL"{if $module_data.param03 eq "LVL"} selected="selected"{/if}>Latvian Lats (Latvia)
<option value="LBP"{if $module_data.param03 eq "LBP"} selected="selected"{/if}>Lebanese Pound (Lebanon)
<option value="ALL"{if $module_data.param03 eq "ALL"} selected="selected"{/if}>Lek (Albania)
<option value="HNL"{if $module_data.param03 eq "HNL"} selected="selected"{/if}>Lempira (Honduras)
<option value="SLL"{if $module_data.param03 eq "SLL"} selected="selected"{/if}>Leone (Sierra Leone)
<option value="ROL"{if $module_data.param03 eq "ROL"} selected="selected"{/if}>Leu (Romania)
<option value="BGL"{if $module_data.param03 eq "BGL"} selected="selected"{/if}>Lev (Bulgaria)
<option value="LRD"{if $module_data.param03 eq "LRD"} selected="selected"{/if}>Liberian Dollar (Liberia)
<option value="LYD"{if $module_data.param03 eq "LYD"} selected="selected"{/if}>Libyan Dinar (Libyan Arab Jamahiriya)
<option value="SZL"{if $module_data.param03 eq "SZL"} selected="selected"{/if}>Lilangeni (Swaziland)
<option value="LTL"{if $module_data.param03 eq "LTL"} selected="selected"{/if}>Lithuanian Litas (Lithuania)
<option value="LSL"{if $module_data.param03 eq "LSL"} selected="selected"{/if}>Loti (Lesotho)
<option value="MGF"{if $module_data.param03 eq "MGF"} selected="selected"{/if}>Malagasy Franc (Madagascar)
<option value="MYR"{if $module_data.param03 eq "MYR"} selected="selected"{/if}>Malaysian Ringgit (Malaysia)
<option value="MTL"{if $module_data.param03 eq "MTL"} selected="selected"{/if}>Maltese Lira (Malta)
<option value="TMM"{if $module_data.param03 eq "TMM"} selected="selected"{/if}>Manat (Turkmenistan)
<option value="MUR"{if $module_data.param03 eq "MUR"} selected="selected"{/if}>Mauritius Rupee (Mauritius)
<option value="MZM"{if $module_data.param03 eq "MZM"} selected="selected"{/if}>Metical (Mozambique)
<option value="MXN"{if $module_data.param03 eq "MXN"} selected="selected"{/if}>Mexican Peso (Mexico)
<option value="MXV"{if $module_data.param03 eq "MXV"} selected="selected"{/if}>Mexican Unidad de Inversion (Mexico)
<option value="MDL"{if $module_data.param03 eq "MDL"} selected="selected"{/if}>Moldovan Leu (Republic Of Moldova)
<option value="MAD"{if $module_data.param03 eq "MAD"} selected="selected"{/if}>Moroccan Dirham (Morocco)
<option value="BOV"{if $module_data.param03 eq "BOV"} selected="selected"{/if}>Mvdol (Bolivia)
<option value="NGN"{if $module_data.param03 eq "NGN"} selected="selected"{/if}>Naira (Nigeria)
<option value="ERN"{if $module_data.param03 eq "ERN"} selected="selected"{/if}>Nakfa (Eritrea)
<option value="NAD"{if $module_data.param03 eq "NAD"} selected="selected"{/if}>Namibia Dollar (Namibia)
<option value="NPR"{if $module_data.param03 eq "NPR"} selected="selected"{/if}>Nepalese Rupee (Nepal)
<option value="ANG"{if $module_data.param03 eq "ANG"} selected="selected"{/if}>Netherlands (Netherlands)
<option value="YUM"{if $module_data.param03 eq "YUM"} selected="selected"{/if}>New Dinar (Yugoslavia)
<option value="ILS"{if $module_data.param03 eq "ILS"} selected="selected"{/if}>New Israeli Sheqel (Israel)
<option value="TWD"{if $module_data.param03 eq "TWD"} selected="selected"{/if}>New Taiwan Dollar (Province Of China Taiwan)
<option value="NZD"{if $module_data.param03 eq "NZD"} selected="selected"{/if}>New Zealand Dollar (New Zealand)
<option value="BTN"{if $module_data.param03 eq "BTN"} selected="selected"{/if}>Ngultrum (Bhutan)
<option value="KPW"{if $module_data.param03 eq "KPW"} selected="selected"{/if}>North Korean Won (Democratic People's Republic Of Korea)
<option value="NOK"{if $module_data.param03 eq "NOK"} selected="selected"{/if}>Norwegian Krone (Norway)
<option value="PEN"{if $module_data.param03 eq "PEN"} selected="selected"{/if}>Nuevo Sol (Peru)
<option value="MRO"{if $module_data.param03 eq "MRO"} selected="selected"{/if}>Ouguiya (Mauritania)
<option value="TOP"{if $module_data.param03 eq "TOP"} selected="selected"{/if}>Pa'anga (Tonga)
<option value="PKR"{if $module_data.param03 eq "PKR"} selected="selected"{/if}>Pakistan Rupee (Pakistan)
<option value="MOP"{if $module_data.param03 eq "MOP"} selected="selected"{/if}>Pataca (Macau)
<option value="UYU"{if $module_data.param03 eq "UYU"} selected="selected"{/if}>Peso Uruguayo (Uruguay)
<option value="PHP"{if $module_data.param03 eq "PHP"} selected="selected"{/if}>Philippine Peso (Philippines)
<option value="GBP"{if $module_data.param03 eq "GBP"} selected="selected"{/if}>Pound Sterling (United Kingdom)
<option value="BWP"{if $module_data.param03 eq "BWP"} selected="selected"{/if}>Pula (Botswana)
<option value="QAR"{if $module_data.param03 eq "QAR"} selected="selected"{/if}>Qatari Rial (Qatar)
<option value="GTQ"{if $module_data.param03 eq "GTQ"} selected="selected"{/if}>Quetzal (Guatemala)
<option value="ZAR"{if $module_data.param03 eq "ZAR"} selected="selected"{/if}>Rand (South Africa)
<option value="OMR"{if $module_data.param03 eq "OMR"} selected="selected"{/if}>Rial Omani (Oman)
<option value="KHR"{if $module_data.param03 eq "KHR"} selected="selected"{/if}>Riel (Cambodia)
<option value="MVR"{if $module_data.param03 eq "MVR"} selected="selected"{/if}>Rufiyaa (Maldives)
<option value="IDR"{if $module_data.param03 eq "IDR"} selected="selected"{/if}>Rupiah (Indonesia)
<option value="RUB"{if $module_data.param03 eq "RUB"} selected="selected"{/if}>Russian Ruble (Russian Federation)
<option value="RUR"{if $module_data.param03 eq "RUR"} selected="selected"{/if}>Russian Ruble (Russian Federation)
<option value="RWF"{if $module_data.param03 eq "RWF"} selected="selected"{/if}>Rwanda Franc (Rwanda)
<option value="SAR"{if $module_data.param03 eq "SAR"} selected="selected"{/if}>Saudi Riyal (Saudi Arabia)
<option value="SCR"{if $module_data.param03 eq "SCR"} selected="selected"{/if}>Seychelles Rupee (Seychelles)
<option value="SGD"{if $module_data.param03 eq "SGD"} selected="selected"{/if}>Singapore Dollar (Singapore)
<option value="SKK"{if $module_data.param03 eq "SKK"} selected="selected"{/if}>Slovak Koruna (Slovakia)
<option value="SBD"{if $module_data.param03 eq "SBD"} selected="selected"{/if}>Solomon Islands Dollar (Solomon Islands)
<option value="KGS"{if $module_data.param03 eq "KGS"} selected="selected"{/if}>Som (Kyrgyzstan)
<option value="SOS"{if $module_data.param03 eq "SOS"} selected="selected"{/if}>Somali Shilling (Somalia)
<option value="LKR"{if $module_data.param03 eq "LKR"} selected="selected"{/if}>Sri Lanka Rupee (Sri Lanka)
<option value="SHP"{if $module_data.param03 eq "SHP"} selected="selected"{/if}>St Helena Pound (St Helena)
<option value="ECS"{if $module_data.param03 eq "ECS"} selected="selected"{/if}>Sucre (Ecuador)
<option value="SDD"{if $module_data.param03 eq "SDD"} selected="selected"{/if}>Sudanese Dinar (Sudan)
<option value="SRG"{if $module_data.param03 eq "SRG"} selected="selected"{/if}>Surinam Guilder (Suriname)
<option value="SEK"{if $module_data.param03 eq "SEK"} selected="selected"{/if}>Swedish Krona (Sweden)
<option value="CHF"{if $module_data.param03 eq "CHF"} selected="selected"{/if}>Swiss Franc (Switzerland)
<option value="SYP"{if $module_data.param03 eq "SYP"} selected="selected"{/if}>Syrian Pound (Syrian Arab Republic)
<option value="TJS"{if $module_data.param03 eq "TJR"} selected="selected"{/if}>Tajikistani somoni (Tajikistan)
<option value="BDT"{if $module_data.param03 eq "BDT"} selected="selected"{/if}>Taka (Bangladesh)
<option value="WST"{if $module_data.param03 eq "WST"} selected="selected"{/if}>Tala (Samoa)
<option value="TZS"{if $module_data.param03 eq "TZS"} selected="selected"{/if}>Tanzanian Shilling (United Republic Of Tanzania)
<option value="KZT"{if $module_data.param03 eq "KZT"} selected="selected"{/if}>Tenge (Kazakhstan)
<option value="TPE"{if $module_data.param03 eq "TPE"} selected="selected"{/if}>Timor Escudo (East Timor)
<option value="SIT"{if $module_data.param03 eq "SIT"} selected="selected"{/if}>Tolar (Slovenia)
<option value="TTD"{if $module_data.param03 eq "TTD"} selected="selected"{/if}>Trinidad and Tobago Dollar (Trinidad And Tobago)
<option value="MNT"{if $module_data.param03 eq "MNT"} selected="selected"{/if}>Tugrik (Mongolia)
<option value="TND"{if $module_data.param03 eq "TND"} selected="selected"{/if}>Tunisian Dinar (Tunisia)
<option value="TRL"{if $module_data.param03 eq "TRL"} selected="selected"{/if}>Turkish Lira (Turkey)
<option value="UGX"{if $module_data.param03 eq "UGX"} selected="selected"{/if}>Uganda Shilling (Uganda)
<option value="ECV"{if $module_data.param03 eq "ECV"} selected="selected"{/if}>Unidad de Valor Constante (Ecuador)
<option value="CLF"{if $module_data.param03 eq "CLF"} selected="selected"{/if}>Unidades de fomento (Chile)
<option value="USN"{if $module_data.param03 eq "USN"} selected="selected"{/if}>US Dollar (Next day) (United States)
<option value="USS"{if $module_data.param03 eq "USS"} selected="selected"{/if}>US Dollar (Same day) (United States)
<option value="USD"{if $module_data.param03 eq "USD"} selected="selected"{/if}>US Dollar (United States)
<option value="UZS"{if $module_data.param03 eq "UZS"} selected="selected"{/if}>Uzbekistan Sum (Uzbekistan)
<option value="VUV"{if $module_data.param03 eq "VUV"} selected="selected"{/if}>Vatu (Vanuatu)
<option value="KRW"{if $module_data.param03 eq "KRW"} selected="selected"{/if}>Won (Republic Of Korea)
<option value="YER"{if $module_data.param03 eq "YER"} selected="selected"{/if}>Yemeni Rial (Yemen)
<option value="JPY"{if $module_data.param03 eq "JPY"} selected="selected"{/if}>Yen (Japan)
<option value="CNY"{if $module_data.param03 eq "CNY"} selected="selected"{/if}>Yuan Renminbi (China)
<option value="ZWD"{if $module_data.param03 eq "ZWD"} selected="selected"{/if}>Zimbabwe Dollar (Zimbabwe)
<option value="PLN"{if $module_data.param03 eq "PLN"} selected="selected"{/if}>Zloty (Poland)
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="{$conf_prefix}[testmode]">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
<br /><font class="SmallText">{$lng.lbl_paypal_test_mode_note}</font>
</td>
</tr>

<!--
<tr>
<td>{$lng.lbl_paypal_transaction_type}:</td>
<td>
<select name="{$conf_prefix}[param05]">
<option value="S"{if $module_data.param05 eq "S"} selected="selected"{/if}>Sale</option>
<option value="A"{if $module_data.param05 eq "A"} selected="selected"{/if}>Authorization and Capture</option>
</select>
</td>
</tr>
-->

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="{$conf_prefix}[param06]" size="36" value="{$module_data.param06|escape}" /></td>
</tr>

</table>
