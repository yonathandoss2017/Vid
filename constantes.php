<?php
if(!defined('CONST')){
    define('CONST',1);
}
// CONSTANTES DE PERMISOS
define('ADMIN', 10);

// TABLAS
define('USERS', 'users');
define('STATS', 'stats');
define('STATS2', 'stats2');
define('STATS_DOMAINS', 'stats_domains');
define('TAGS', 'supplytag');
define('COUNTRIES', 'countries');
define('SITES', 'sites');
define('FORMATS', 'formats');
define('CODES', 'specialcodes');
define('ADS', 'ads');
define('ADSCODES', 'ads_codes');
define('ACC_MANAGERS', 'acc_managers');
define('MOVEMENTS', 'movements');
define('CLOSES', 'closes');
define('EXCEPTIONS', 'exceptions');
define('TINVOICE', 'temp_invoice');
define('ADSTXT', 'adstxt');
define('OBJETIVES', 'objetives');
define('PASSWORDRECOVER', 'recover');
define('APROVE', 'aprove');
define('BIDDERS', 'bidder');
define('PLACEMENTS', 'placements');
define('DSIZES', 'display_sizes');
define('DPOSITIONS', 'display_positions');
define('ADUNITS', 'display_adunits');
define('ADUNITSTIERS', 'display_adunits_tiers');
define('ADUNITSPLACE', 'display_adunits_placements');
define('PREBID_IMPRESION', 'prebid_impresion');
define('PREBID_BIDS', 'prebid_bids');
define('PREBID_REVENUE', 'prebid_revenue');
define('PREBID_REVENUE_HOURS', 'prebid_revenue_hours');
define('PREBID_REVENUE_COUNTRY', 'prebid_revenue_country');
define('PREBID_REVENUE_BIDDER', 'prebid_revenue_bidder');
define('PREBID_FL', 'prebid_formatloads');
define('PREBID_FL_HOURS', 'prebid_formatloads_hours');
define('PREBID_FL_COUNTRY', 'prebid_formatloads_country');

$DisplayBidders[1] = 'AppNexus';
$DisplayBidders[2] = 'Criteo';
$DisplayBidders[3] = 'AOL';
$DisplayBidders[4] = 'PulsePoint';
$DisplayBidders[5] = 'SmartServer';
$DisplayBidders[6] = 'Pubmatic';

$AdType[1] = 'Intext desktop';
$AdType[2] = 'Intext mobile';
$AdType[3] = 'Slider desktop';
$AdType[4] = 'Slider mobile';
$AdType[5] = 'Custom code';

$AdType2[1] = 'Intext desktop';
$AdType2[2] = 'Intext mobile';
$AdType2[3] = 'Slider desktop';
$AdType2[4] = 'Slider mobile';
$AdType2[10] = 'Display desktop';
$AdType2[11] = 'Display MW';
$AdType2[100] = 'Custom code';

$Vis[1] = "De 0 a 1 Millón";
$Vis[2] = "De 1 a 50 Millones";
$Vis[3] = "De 50 a 100 Millones";
$Vis[4] = "+ 100 Millones";

$AdstxtState[0] = "Completo";
$AdstxtState[1] = "Incompleto";
$AdstxtState[2] = "No Encontrado";

$Motivos[2] = "Tráfico insuficiente";
$Motivos[3] = "Categoría ilegal";
$Motivos[4] = "Top Level Domain";

$ApprovedDenied[1]['es'] = 'Aprobado!';
$ApprovedDenied[2]['es'] = 'Denegado';
$ApprovedDenied[1]['en'] = 'Approved!';
$ApprovedDenied[2]['en'] = 'Denied';

$MailMotivos[2]['es'] = "<strong>Tráfico insuficiente:</strong> La web no parece cumplir el volúmen de tráfico de 3 millones de páginas vistas al mes necesarias para poder ser aceptada como publisher de Vidoomy.";
$MailMotivos[3]['es'] = "<strong>Categoría ilegal:</strong> No se aceptan sitios web de contenido adulto, piratería informática, despectivo, falso, violento, ni relacionado con alcohol, drogas, tabaco, armas o ilegal.";
$MailMotivos[4]['es'] = "<strong>Top Level Domain:</strong> Vidoomy no acepta subdominios, solo el dominio principal, tipo: www.vidoomy.com y no subdomain.vidoomy.com.";

$MailMotivos[2]['en'] = "<strong>Insufficient traffic:</strong> The website does not seem to meet the minimum traffic volume of 3 million page views per month necessary to be accepted.";
$MailMotivos[3]['en'] = "<strong>Illegal category:</strong> We do not accept websites with adult, hacking, derogatory, false, violent, related to alcohol, drugs, tobacco, weapons or any illegal content.";
$MailMotivos[4]['en'] = "<strong>Top Level Domain:</strong> Vidoomy does not accept subdomains, only the main domain, for example we accept www.vidoomy.com, but not subdomain.vidoomy.com.";

$MailAprobado[1]['es']['subject'] = "Usuario aprobado en Vidoomy! Empecemos :)";
$MailAprobado[1]['es']['txt'] = "Estimado {User},<br/>
<br/>Queremos indicarle que su cuenta ha sido ¡aprobada en Vidoomy!.<br/>
<br/>El siguiente dominio ha sido validado:<br/>
<br/>{Sites}<br/>
<br/>A continuación uno de nuestros agentes se pondrá en contacto con usted para verificar que toda la publicidad está correctamente integrada y que se active la publicidad en su sitio web.<br/>
<br/>Gracias!";
$MailAprobado[1]['es']['txts'] = "Estimado {User},<br/>
<br/>Queremos indicarle que su cuenta ha sido ¡aprobada en Vidoomy!.<br/>
<br/>Los siguientes dominios han sido validados:<br/>
<br/>{Sites}<br/>
<br/>A continuación uno de nuestros agentes se pondrá en contacto con usted para verificar que toda la publicidad está correctamente integrada y que se active la publicidad en sus sitios web.<br/>
<br/>Gracias!";

$MailAprobado[2]['es']['subject'] = "Usuario denegado en Vidoomy :(";
$MailAprobado[2]['es']['txt'] = "Estimado {User},<br/>
<br/>Queremos indicarle que su cuenta no ha sido aprobada por nuestro equipo de policy en Vidoomy. Le remitimos la url para que pueda revisar la política de aceptación de publishers del programa de Vidoomy:<br/>
<br/><a href=\"https://www.vidoomy.com/politica-aceptacion-vidoomy-publishers\">https://www.vidoomy.com/politica-aceptacion-vidoomy-publishers</a><br/>
<br/><br/>Los detalles por los que no ha sido aceptado su dominio son:<br/>
<br/>{Sites}<br/>
<br/>Si desea que revisemos su caso ya que considera que ha sido denegado de forma incorrecta, puede contactar en respuesta a este mensaje y será respondido por uno de nuestros agentes, o contestar directamente a raquel.fernandez@vidoomy.com para que revise su caso.<br/>
<br/>Gracias!";
$MailAprobado[2]['es']['txts'] = "Estimado {User},<br/>
<br/>Queremos indicarle que su cuenta no ha sido aprobada por nuestro equipo de policy en Vidoomy. Le remitimos la url para que pueda revisar la política de aceptación de publishers del programa de Vidoomy:<br/>
<br/><a href=\"https://www.vidoomy.com/politica-aceptacion-vidoomy-publishers\">https://www.vidoomy.com/politica-aceptacion-vidoomy-publishers</a><br/>
<br/><br/>Los detalles por los que no han sido aceptados sus dominios son:<br/>
<br/>{Sites}<br/>
<br/>Si desea que revisemos su caso ya que considera que ha sido denegado de forma incorrecta, puede contactar en respuesta a este mensaje y será respondido por uno de nuestros agentes, o contestar directamente a raquel.fernandez@vidoomy.com para que revise su caso.<br/>
<br/>Gracias!";

$MailAprobado[3]['es']['subject'] = "Usuario aprobado en Vidoomy! Empecemos :)";
$MailAprobado[3]['es']['txt'] = "Estimado {User},<br/>
<br/>Queremos indicarle que su cuenta ha sido aprobada, pero no todos los dominios han sido aprobados. A continuación le enviamos el detalle:<br/>
<br/>Los detalles de cada dominio son:<br/>
<br/>{Sites}<br/>
<br/>A continuación uno de nuestros agentes se pondrá en contacto con usted para verificar que toda la publicidad está correctamente integrada y que se active la publicidad en su(s) sitio(s) web.
<br/>
<br/>Gracias!";
$MailAprobado[3]['es']['txts'] = "Estimado {User},<br/>
<br/>Queremos indicarle que su cuenta ha sido aprobada, pero no todos los dominios han sido aprobados. A continuación le enviamos el detalle:<br/>
<br/>Los detalles de cada dominio son:<br/>
<br/>{Sites}<br/>
<br/>A continuación uno de nuestros agentes se pondrá en contacto con usted para verificar que toda la publicidad está correctamente integrada y que se active la publicidad en sus sitios web.
<br/>
<br/>Gracias!";

$MailAprobado[1]['en']['subject'] = "Your Vidoomy account was approved! Lets get started :)";
$MailAprobado[1]['en']['txt'] = "Dear {User},<br/>
<br/>We want to tell you that your Vidoomy account has been approved!.<br/>
<br/>The following domain has been validated:<br/>
<br/>{Sites}<br/>
<br/>One of our agents will contact you to verify that all advertising is correctly integrated and that advertising is activated on your website.<br/> 
<br/>Thank you!";
$MailAprobado[1]['en']['txts'] = "Dear {User},<br/>
<br/>We want to tell you that your Vidoomy account has been approved!.<br/>
<br/>The following domains have been validated:<br/>
<br/>{Sites}<br/>
<br/>One of our agents will contact you to verify that all advertising is correctly integrated and that advertising is activated on your websites.<br/>
<br/>Thank you!";

$MailAprobado[2]['en']['subject'] = "Your Vidoomy account was denied :(";
$MailAprobado[2]['en']['txt'] = "Dear {User},<br/>
<br/>We want to inform you that your Vidoomy account has not been approved by our policy team. You can find our Publisher Acceptance Policy on the following link:<br/>
<br/><a href=\"https://www.vidoomy.com/publisher-acceptance-policy\">https://www.vidoomy.com/publisher-acceptance-policy</a><br/>
<br/><br/>The reasons for not approving your site are the following:<br/>
<br/>{Sites}<br/>
<br/>If you want us to review your case as your considers that it has been denied incorrectly, you can contact us in response to this email and it will be answered by one of our agents, or email directly to raquel.fernandez@vidoomy.com to review your case.<br/>
<br/>Thank you!";
$MailAprobado[2]['en']['txts'] = "Dear {User},<br/>
<br/>We want to inform you that your Vidoomy account has not been approved by our policy team. You can find our Publisher Acceptance Policy on the following link:<br/>
<br/><a href=\"https://www.vidoomy.com/publisher-acceptance-policy\">https://www.vidoomy.com/publisher-acceptance-policy</a><br/>
<br/><br/>The reasons for not approving your sites are the following:<br/>
<br/>{Sites}<br/>
<br/>If you want us to review your case as your considers that it has been denied incorrectly, you can contact us in response to this email and it will be answered by one of our agents, or email directly to raquel.fernandez@vidoomy.com to review your case.<br/>
<br/>Thank you!";

$MailAprobado[3]['en']['subject'] = "Your Vidoomy account was approved! Lets get started :)";
$MailAprobado[3]['en']['txt'] = "Dear {User},<br/>
<br/>We want to tell you that your Vidoomy account has been approved, but not all registered domains were validated.<br/>
<br/>Here is the detail:<br/>
<br/>{Sites}<br/>
<br/>One of our agents will contact you to verify that all advertising is correctly integrated and that advertising is activated on your website.<br/>
<br/>Thank you!";
$MailAprobado[3]['en']['txts'] = "Dear {User},<br/>
<br/>We want to tell you that your Vidoomy account has been approved, but not all registered domains were validated.<br/>
<br/>Here is the detail:<br/>
<br/>{Sites}<br/>
<br/>One of our agents will contact you to verify that all advertising is correctly integrated and that advertising is activated on your websites.<br/>
<br/>Thank you!";

$MonthSpanish[1] = 'Enero';
$MonthSpanish[2] = 'Febrero';
$MonthSpanish[3] = 'Marzo';
$MonthSpanish[4] = 'Abril';
$MonthSpanish[5] = 'Mayo';
$MonthSpanish[6] = 'Junio';
$MonthSpanish[7] = 'Julio';
$MonthSpanish[8] = 'Agosto';
$MonthSpanish[9] = 'Septiembre';
$MonthSpanish[10] = 'Octubre';
$MonthSpanish[11] = 'Noviembre';
$MonthSpanish[12] = 'Diciembre';

$DaySpanish[0] = 'Domingo';
$DaySpanish[1] = 'Lunes';
$DaySpanish[2] = 'Martes';
$DaySpanish[3] = 'Miercoles';
$DaySpanish[4] = 'Jueves';
$DaySpanish[5] = 'Viernes';
$DaySpanish[6] = 'Sábado';

?>
