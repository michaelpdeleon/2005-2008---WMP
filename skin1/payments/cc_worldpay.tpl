{* $Id: cc_worldpay.tpl,v 1.7.2.3 2006/07/31 10:37:06 max Exp $ *}
<h3>WorldPay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_worlpay_note|substitute:"current_location":$current_location:"processor":$module_data.processor}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_worldpay_return_url}:</td>
<td>{$current_location}/payment/{$module_data.processor}</td>
</tr>
<tr>
<td>{$lng.lbl_cc_worldpay_instanceid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option {if $module_data.param02 eq "AFA"}selected{/if} value="AFA">Afghani</option>
<option {if $module_data.param02 eq "ALL"}selected{/if} value="ALL">Lek</option>
<option {if $module_data.param02 eq "DZD"}selected{/if} value="DZD">Algerian Dinar</option>
<option {if $module_data.param02 eq "AON"}selected{/if} value="AON">New Kwanza</option>
<option {if $module_data.param02 eq "ARS"}selected{/if} value="ARS">Argentine Peso</option>
<option {if $module_data.param02 eq "AWG"}selected{/if} value="AWG">Aruban Guilder</option>
<option {if $module_data.param02 eq "AUD"}selected{/if} value="AUD">Australian Dollar</option>
<option {if $module_data.param02 eq "ATS"}selected{/if} value="ATS">Schilling</option>
<option {if $module_data.param02 eq "BSD"}selected{/if} value="BSD">Bahamian Dollar</option>
<option {if $module_data.param02 eq "BHD"}selected{/if} value="BHD">Bahraini Dinar</option>
<option {if $module_data.param02 eq "BDT"}selected{/if} value="BDT">Taka</option>
<option {if $module_data.param02 eq "BBD"}selected{/if} value="BBD">Barbados Dollar</option>
<option {if $module_data.param02 eq "BEF"}selected{/if} value="BEF">Belgian Franc</option>
<option {if $module_data.param02 eq "BZD"}selected{/if} value="BZD">Belize Dollar</option>
<option {if $module_data.param02 eq "BMD"}selected{/if} value="BMD">Bermudian Dollar</option>
<option {if $module_data.param02 eq "BOB"}selected{/if} value="BOB">Boliviano</option>
<option {if $module_data.param02 eq "BAD"}selected{/if} value="BAD">Bosnian Dinar</option>
<option {if $module_data.param02 eq "BWP"}selected{/if} value="BWP">Pula</option>
<option {if $module_data.param02 eq "BRL"}selected{/if} value="BRL">Real</option>
<option {if $module_data.param02 eq "BND"}selected{/if} value="BND">Brunei Dollar</option>
<option {if $module_data.param02 eq "BGL"}selected{/if} value="BGL">Lev</option>
<option {if $module_data.param02 eq "XOF"}selected{/if} value="XOF">CFA Franc BCEAO</option>
<option {if $module_data.param02 eq "BIF"}selected{/if} value="BIF">Burundi Franc</option>
<option {if $module_data.param02 eq "KHR"}selected{/if} value="KHR">Cambodia Riel</option>
<option {if $module_data.param02 eq "XAF"}selected{/if} value="XAF">CFA Franc BEAC</option>
<option {if $module_data.param02 eq "CAD"}selected{/if} value="CAD">Canadian Dollar</option>
<option {if $module_data.param02 eq "CVE"}selected{/if} value="CVE">Cape Verde Escudo</option>
<option {if $module_data.param02 eq "KYD"}selected{/if} value="KYD">Cayman Islands Dollar</option>
<option {if $module_data.param02 eq "CLP"}selected{/if} value="CLP">Chilean Peso</option>
<option {if $module_data.param02 eq "CNY"}selected{/if} value="CNY">Yuan Renminbi</option>
<option {if $module_data.param02 eq "COP"}selected{/if} value="COP">Colombian Peso</option>
<option {if $module_data.param02 eq "KMF"}selected{/if} value="KMF">Comoro Franc</option>
<option {if $module_data.param02 eq "CRC"}selected{/if} value="CRC">Costa Rican Colon</option>
<option {if $module_data.param02 eq "HRK"}selected{/if} value="HRK">Croatian Kuna</option>
<option {if $module_data.param02 eq "CUP"}selected{/if} value="CUP">Cuban Peso</option>
<option {if $module_data.param02 eq "CYP"}selected{/if} value="CYP">Cyprus Pound</option>
<option {if $module_data.param02 eq "CZK"}selected{/if} value="CZK">Czech Koruna</option>
<option {if $module_data.param02 eq "DKK"}selected{/if} value="DKK">Danish Krone</option>
<option {if $module_data.param02 eq "DJF"}selected{/if} value="DJF">Djibouti Franc</option>
<option {if $module_data.param02 eq "XCD"}selected{/if} value="XCD">East Caribbean Dollar</option>
<option {if $module_data.param02 eq "DOP"}selected{/if} value="DOP">Dominican Peso</option>
<option {if $module_data.param02 eq "TPE"}selected{/if} value="TPE">Timor Escudo</option>
<option {if $module_data.param02 eq "ECS"}selected{/if} value="ECS">Ecuador Sucre</option>
<option {if $module_data.param02 eq "EGP"}selected{/if} value="EGP">Egyptian Pound</option>
<option {if $module_data.param02 eq "SVC"}selected{/if} value="SVC">El Salvador Colon</option>
<option {if $module_data.param02 eq "EEK"}selected{/if} value="EEK">Kroon</option>
<option {if $module_data.param02 eq "ETB"}selected{/if} value="ETB">Ethiopian Birr</option>
<option {if $module_data.param02 eq "XEU"}selected{/if} value="XEU">ECU</option>
<option {if $module_data.param02 eq "EUR"}selected{/if} value="EUR">European EURO</option>
<option {if $module_data.param02 eq "FKP"}selected{/if} value="FKP">Falkland Islands Pound</option>
<option {if $module_data.param02 eq "FJD"}selected{/if} value="FJD">Fiji Dollar</option>
<option {if $module_data.param02 eq "FIM"}selected{/if} value="FIM">Markka</option>
<option {if $module_data.param02 eq "FRF"}selected{/if} value="FRF">French Franc</option>
<option {if $module_data.param02 eq "XPF"}selected{/if} value="XPF">CFP Franc</option>
<option {if $module_data.param02 eq "GMD"}selected{/if} value="GMD">Dalasi</option>
<option {if $module_data.param02 eq "DEM"}selected{/if} value="DEM">Deutsche Mark</option>
<option {if $module_data.param02 eq "GHC"}selected{/if} value="GHC">Cedi</option>
<option {if $module_data.param02 eq "GIP"}selected{/if} value="GIP">Gibraltar Pound</option>
<option {if $module_data.param02 eq "GRD"}selected{/if} value="GRD">Drachma</option>
<option {if $module_data.param02 eq "GTQ"}selected{/if} value="GTQ">Quetzal</option>
<option {if $module_data.param02 eq "GNF"}selected{/if} value="GNF">Guinea Franc</option>
<option {if $module_data.param02 eq "GWP"}selected{/if} value="GWP">Guinea - Bissau Peso</option>
<option {if $module_data.param02 eq "GYD"}selected{/if} value="GYD">Guyana Dollar</option>
<option {if $module_data.param02 eq "HTG"}selected{/if} value="HTG">Gourde</option>
<option {if $module_data.param02 eq "HNL"}selected{/if} value="HNL">Lempira</option>
<option {if $module_data.param02 eq "HKD"}selected{/if} value="HKD">Hong Kong Dollar</option>
<option {if $module_data.param02 eq "HUF"}selected{/if} value="HUF">Forint</option>
<option {if $module_data.param02 eq "ISK"}selected{/if} value="ISK">Iceland Krona</option>
<option {if $module_data.param02 eq "INR"}selected{/if} value="INR">Indian Rupee</option>
<option {if $module_data.param02 eq "IDR"}selected{/if} value="IDR">Rupiah</option>
<option {if $module_data.param02 eq "IRR"}selected{/if} value="IRR">Iranian Rial</option>
<option {if $module_data.param02 eq "IQD"}selected{/if} value="IQD">Iraqi Dinar</option>
<option {if $module_data.param02 eq "IEP"}selected{/if} value="IEP">Irish Pound</option>
<option {if $module_data.param02 eq "ILS"}selected{/if} value="ILS">Shekel</option>
<option {if $module_data.param02 eq "ITL"}selected{/if} value="ITL">Italian Lira</option>
<option {if $module_data.param02 eq "JMD"}selected{/if} value="JMD">Jamaican Dollar</option>
<option {if $module_data.param02 eq "JPY"}selected{/if} value="JPY">Yen</option>
<option {if $module_data.param02 eq "JOD"}selected{/if} value="JOD">Jordanian Dinar</option>
<option {if $module_data.param02 eq "KZT"}selected{/if} value="KZT">Tenge</option>
<option {if $module_data.param02 eq "KES"}selected{/if} value="KES">Kenyan Shilling</option>
<option {if $module_data.param02 eq "KRW"}selected{/if} value="KRW">Won</option>
<option {if $module_data.param02 eq "KPW"}selected{/if} value="KPW">North Korean Won</option>
<option {if $module_data.param02 eq "KWD"}selected{/if} value="KWD">Kuwaiti Dinar</option>
<option {if $module_data.param02 eq "KGS"}selected{/if} value="KGS">Som</option>
<option {if $module_data.param02 eq "LAK"}selected{/if} value="LAK">Kip</option>
<option {if $module_data.param02 eq "LVL"}selected{/if} value="LVL">Latvian Lats</option>
<option {if $module_data.param02 eq "LBP"}selected{/if} value="LBP">Lebanese Pound</option>
<option {if $module_data.param02 eq "LSL"}selected{/if} value="LSL">Loti</option>
<option {if $module_data.param02 eq "LRD"}selected{/if} value="LRD">Liberian Dollar</option>
<option {if $module_data.param02 eq "LYD"}selected{/if} value="LYD">Libyan Dinar</option>
<option {if $module_data.param02 eq "LTL"}selected{/if} value="LTL">Lithuanian Litas</option>
<option {if $module_data.param02 eq "LUF"}selected{/if} value="LUF">Luxembourg Franc</option>
<option {if $module_data.param02 eq "MOP"}selected{/if} value="MOP">Pataca</option>
<option {if $module_data.param02 eq "MKD"}selected{/if} value="MKD">Denar</option>
<option {if $module_data.param02 eq "MGF"}selected{/if} value="MGF">Malagasy Franc</option>
<option {if $module_data.param02 eq "MWK"}selected{/if} value="MWK">Kwacha</option>
<option {if $module_data.param02 eq "MYR"}selected{/if} value="MYR">Malaysian Ringitt</option>
<option {if $module_data.param02 eq "MVR"}selected{/if} value="MVR">Rufiyaa</option>
<option {if $module_data.param02 eq "MTL"}selected{/if} value="MTL">Maltese Lira</option>
<option {if $module_data.param02 eq "MRO"}selected{/if} value="MRO">Ouguiya</option>
<option {if $module_data.param02 eq "MUR"}selected{/if} value="MUR">Mauritius Rupee</option>
<option {if $module_data.param02 eq "MXN"}selected{/if} value="MXN">Mexico Peso</option>
<option {if $module_data.param02 eq "MNT"}selected{/if} value="MNT">Mongolia Tugrik</option>
<option {if $module_data.param02 eq "MAD"}selected{/if} value="MAD">Moroccan Dirham</option>
<option {if $module_data.param02 eq "MZM"}selected{/if} value="MZM">Metical</option>
<option {if $module_data.param02 eq "MMK"}selected{/if} value="MMK">Myanmar Kyat</option>
<option {if $module_data.param02 eq "NAD"}selected{/if} value="NAD">Namibian Dollar</option>
<option {if $module_data.param02 eq "NPR"}selected{/if} value="NPR">Nepalese Rupee</option>
<option {if $module_data.param02 eq "ANG"}selected{/if} value="ANG">Netherlands Antilles Guilder</option>
<option {if $module_data.param02 eq "NLG"}selected{/if} value="NLG">Netherlands Guilder</option>
<option {if $module_data.param02 eq "NZD"}selected{/if} value="NZD">New Zealand Dollar</option>
<option {if $module_data.param02 eq "NIO"}selected{/if} value="NIO">Cordoba Oro</option>
<option {if $module_data.param02 eq "NGN"}selected{/if} value="NGN">Naira</option>
<option {if $module_data.param02 eq "NOK"}selected{/if} value="NOK">Norwegian Krone</option>
<option {if $module_data.param02 eq "OMR"}selected{/if} value="OMR">Rial Omani </option>
<option {if $module_data.param02 eq "PKR"}selected{/if} value="PKR">Pakistan Rupee</option>
<option {if $module_data.param02 eq "PAB"}selected{/if} value="PAB">Balboa</option>
<option {if $module_data.param02 eq "PGK"}selected{/if} value="PGK">New Guinea Kina</option>
<option {if $module_data.param02 eq "PYG"}selected{/if} value="PYG">Guarani</option>
<option {if $module_data.param02 eq "PEN"}selected{/if} value="PEN">Nuevo Sol</option>
<option {if $module_data.param02 eq "PHP"}selected{/if} value="PHP">Philippine Peso</option>
<option {if $module_data.param02 eq "PLN"}selected{/if} value="PLN">New Zloty</option>
<option {if $module_data.param02 eq "PTE"}selected{/if} value="PTE">Portugese Escudo</option>
<option {if $module_data.param02 eq "QAR"}selected{/if} value="QAR">Qatari Rial</option>
<option {if $module_data.param02 eq "ROL"}selected{/if} value="ROL">Leu</option>
<option {if $module_data.param02 eq "RUR"}selected{/if} value="RUR">Russian Ruble</option>
<option {if $module_data.param02 eq "RWF"}selected{/if} value="RWF">Rwanda Franc</option>
<option {if $module_data.param02 eq "WST"}selected{/if} value="WST">Tala</option>
<option {if $module_data.param02 eq "STD"}selected{/if} value="STD">Dobra</option>
<option {if $module_data.param02 eq "SAR"}selected{/if} value="SAR">Saudi Riyal</option>
<option {if $module_data.param02 eq "SCR"}selected{/if} value="SCR">Seychelles Rupee</option>
<option {if $module_data.param02 eq "SLL"}selected{/if} value="SLL">Leone</option>
<option {if $module_data.param02 eq "SGD"}selected{/if} value="SGD">Singapore Dollar</option>
<option {if $module_data.param02 eq "SKK"}selected{/if} value="SKK">Slovak Koruna</option>
<option {if $module_data.param02 eq "SIT"}selected{/if} value="SIT">Tolar</option>
<option {if $module_data.param02 eq "SBD"}selected{/if} value="SBD">Solomon Islands Dollar</option>
<option {if $module_data.param02 eq "SOS"}selected{/if} value="SOS">Somalia Shilling</option>
<option {if $module_data.param02 eq "ZAR"}selected{/if} value="ZAR">Rand</option>
<option {if $module_data.param02 eq "ESP"}selected{/if} value="ESP">Spanish Peseta</option>
<option {if $module_data.param02 eq "LKR"}selected{/if} value="LKR">Sri Lanka Rupee</option>
<option {if $module_data.param02 eq "SHP"}selected{/if} value="SHP">St Helena Pound</option>
<option {if $module_data.param02 eq "SDP"}selected{/if} value="SDP">Sudanese Pound</option>
<option {if $module_data.param02 eq "SRG"}selected{/if} value="SRG">Suriname Guilder</option>
<option {if $module_data.param02 eq "SZL"}selected{/if} value="SZL">Swaziland Lilangeni</option>
<option {if $module_data.param02 eq "SEK"}selected{/if} value="SEK">Sweden Krona</option>
<option {if $module_data.param02 eq "CHF"}selected{/if} value="CHF">Swiss Franc</option>
<option {if $module_data.param02 eq "SYP"}selected{/if} value="SYP">Syrian Pound</option>
<option {if $module_data.param02 eq "TWD"}selected{/if} value="TWD">New Taiwan Dollar</option>
<option {if $module_data.param02 eq "TJR"}selected{/if} value="TJR">Tajik Ruble</option>
<option {if $module_data.param02 eq "TZS"}selected{/if} value="TZS">Tanzanian Shilling</option>
<option {if $module_data.param02 eq "THB"}selected{/if} value="THB">Baht</option>
<option {if $module_data.param02 eq "TOP"}selected{/if} value="TOP">Tonga Pa'anga</option>
<option {if $module_data.param02 eq "TTD"}selected{/if} value="TTD">Trinidad &amp; Tobago Dollar</option>
<option {if $module_data.param02 eq "TND"}selected{/if} value="TND">Tunisian Dinar</option>
<option {if $module_data.param02 eq "TRL"}selected{/if} value="TRL">Turkish Lira</option>
<option {if $module_data.param02 eq "UGX"}selected{/if} value="UGX">Uganda Shilling</option>
<option {if $module_data.param02 eq "UAH"}selected{/if} value="UAH">Ukrainian Hryvnia</option>
<option {if $module_data.param02 eq "AED"}selected{/if} value="AED">United Arab Emirates Dirham</option>
<option {if $module_data.param02 eq "GBP"}selected{/if} value="GBP">Pounds Sterling</option>
<option {if $module_data.param02 eq "USD"}selected{/if} value="USD">US Dollar</option>
<option {if $module_data.param02 eq "UYU"}selected{/if} value="UYU">Uruguayan Peso</option>
<option {if $module_data.param02 eq "VUV"}selected{/if} value="VUV">Vanuatu Vatu</option>
<option {if $module_data.param02 eq "VEB"}selected{/if} value="VEB">Venezuela Bolivar</option>
<option {if $module_data.param02 eq "VND"}selected{/if} value="VND">Viet Nam Dong</option>
<option {if $module_data.param02 eq "YER"}selected{/if} value="YER">Yemeni Rial</option>
<option {if $module_data.param02 eq "YUM"}selected{/if} value="YUM">Yugoslavian New Dinar</option>
<option {if $module_data.param02 eq "ZRN"}selected{/if} value="ZRN">New Zaire</option>
<option {if $module_data.param02 eq "ZMK"}selected{/if} value="ZMK">Zambian Kwacha</option>
<option {if $module_data.param02 eq "ZWD"}selected{/if} value="ZWD">Zimbabwe Dollar</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="A" {if $module_data.testmode eq "A"}selected{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D" {if $module_data.testmode eq "D"}selected{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N" {if $module_data.testmode eq "N"}selected{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
