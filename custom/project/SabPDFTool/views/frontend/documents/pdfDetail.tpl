<style type="text/css">
    body {
        {$PDFCreatorConfig->body}
    }
    h1 {
        {$PDFCreatorConfig->h1}
    }
    h2.light {
        font-weight: normal;
    }
    #logo { 
        {$PDFCreatorConfig->logo_css}
    }
    #content { 
    }
    #content table {
        padding-top: 5px;
        padding-bottom: 5px;
    }
    #content table tr{
        padding-bottom: 20px;
    }
    table, .einleitung {
        margin-bottom: 30px;
        width:750px;
        font-family: verdana;
        font-size:95%;
    }
    table td.articleName, table td.articleSubheadline{
        padding:5px 0;
        color: #000;
        font-weight:bold;
        font-size: 25pt;
        text-align: center;
    }
    table td.articleSubheadline {
        color: #898989;
    }
    table td.supplierSubheadline {
        text-align: center;
        font-weight: normal;
    }
    .image {
        text-align: center;
    }
    table th{
        padding: 5px;
        font-weight: normal;
        font-size: 110%;
        color: #000;
        border: 1px solid #000000;
    }
    table td.headline{
        background-color: #efefef;
        padding:5px;
        font-weight:bold;
        font-size:110%;
        color: #000;
    }
    table td.text {
        padding: 5px;
        font-weight: normal;
        font-size: 11pt;
        line-height: 18pt;
        color: #000;
    }
    table td.text--center{
        padding:5px;
        font-weight:normal;
        font-size:11pt;
        line-height: 18pt;
        text-align: center;
        color: #000;
    }
    table td.head {
        border-bottom: 0.5pt solid #dfdfdf;
        padding:2px;
    }
    table.table_pageheader {
        position: relative;
        float: left;
        width: 100%;
        height: 250px;
        padding-top: 20px;
    }
    .table_foot {
        margin-top: 20px;
    }
    .footer {
        font-size: 8pt;
    }
    td.bordered {
        border: 0.5pt solid #000000;
        line-height: 1.8;
    }
</style>

<body>
<htmlpageheader name="myHTMLHeader1">
    <table width="700px" class="table_pageheader" style="border-bottom:1px solid #000;">
        <tr>
            <td width="60%" align="left" valign="middle">
                <h2 class="light">{$SabPDFToolConfig->pdf_headline}</h2>
                <h2>Bestellfax (0511) 37 07 86 93<br/>www.n-medica.de</h2>
                <!-- <p>{$sHeute}</p> -->
            </td>
            <td width="40%" align="right"><img src="{$SabPDFToolConfig->pdf_logo}" style="wwidth: 91px; height: auto;"></td>
        </tr>
    </table>
</htmlpageheader>
<htmlpagefooter name="myHTMLFooter">
        <table width="700px" class="footer" style="border-top:1px solid #000;">
            <tr>
                <td align="center" colspan="2"><strong>Seite {literal}{PAGENO}{/literal}</strong></td>
            </tr>
            <tr>
                <td align="left"><strong>nmedica &#174; GmbH</strong></td>
                <td align="right">Telefon (0511) 370 786 92</td>
            </tr>
            <tr>
                <td align="left"><strong>Ihr Partner für Medizinprodukte und Systeme</strong></td>
                <td align="right">Fax (0511) 370 786 93</td>
            </tr>
            <tr>
                <td align="left">Scharnhorststrasse 23</td>
                <td align="right">Mail service@n-medica.de</td>
            </tr>
            <tr>
                <td align="left">30175 Hannover</td>
                <td align="right">Web www.n-medica.de</td>
            </tr>
        </table>
</htmlpagefooter>
<sethtmlpageheader name="myHTMLHeader1" page="O" value="on" show-this-page="1" />
<sethtmlpagefooter name="myHTMLFooter" page="0" value="on" />
<div id="content">
    <table cellspacing="0" style="border: 0;" width="700px">
        <tr>
            <td class="articleName">
                <strong>{$sArticle.articleName}</strong>
            </td>
        </tr>
        <tr>
            <td class="articleSubheadline">
                <strong>{$sArticle.attr7}</strong>
            </td>
        </tr>
        <tr>
            <td class="image">
                <img src="{$sArticle.image.source}" style="width: 400px; height: auto; margin: 40px auto 0;">
            </td>
        </tr>
        <tr>
            <td class="thumbnails" style="text-align: center;">
                {foreach $sArticle.images as $thumbnail}
                    <img src="{$thumbnail.thumbnails.0.source}" style="width: 100px; height: auto; margin: 5px 10px 0px 0px;">
                {/foreach}
            </td>
        </tr>
        <tr>
            <td class="supplier_image image">
                <img src="{$sArticle.supplierImg}" style="width: 80px; height: auto; margin: 180px auto 0;">
            </td>
        </tr>
    </table>

<pagebreak />

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="AufeinenBlick" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.attr8}
            </td>
        </tr>
    </table>

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Produktinformationen" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.description_long}
            </td>
        </tr>
    </table>

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Produkteigenschaften" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.attr1}
            </td>
        </tr>
    </table>

<pagebreak />

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Anwendungsbereiche" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.attr2}
            </td>
        </tr>
    </table>

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="ProduktrichtlinienHinweise" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.attr4}
            </td>
        </tr>
    </table>

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Produktzusammensetzung" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.attr3}
            </td>
        </tr>
    </table>

<pagebreak />

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Bestellinformationen" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
    </table>

{if sVariantenListe}
    {$i=0}
    <table cellspacing="0" style="border: 0;">
        <thead>
        <tr>
            <th>{s name="Typ" namespace="plugins/frontend/SabPDFToolv5"}{/s}</th>
            <th>{s name="ArtNr" namespace="plugins/frontend/SabPDFToolv5"}{/s}</th>
            <th>{s name="HerstellerNr" namespace="plugins/frontend/SabPDFToolv5"}HerstellerNr{/s}</th>
            <th>{s name="PZN" namespace="plugins/frontend/SabPDFToolv5"}{/s}</th>
            <th>{s name="VE" namespace="plugins/frontend/SabPDFToolv5"}{/s}</th>
            <th>{s name="Preis" namespace="plugins/frontend/SabPDFToolv5"}{/s}</th>
            <th>{s name="Verordnungshinweis" namespace="plugins/frontend/SabPDFToolv5"}{/s}</th>
            <th>Bestellmenge</th>
        </tr>
        </thead>
        {foreach $sVariantenListe as $variante}
            <tr>
                <td class="text--center bordered">{$variante.additionaltext}</td>
                <td class="text--center bordered">{$variante.ordernumber}</td>
                <td class="text--center bordered">{$variante.suppliernumber}</td>
                <td class="text--center bordered">{$variante.attr11}</td>
                <td class="text--center bordered">{$variante.purchaseunit|string_format:"%d"} {$variante.unit}</td>
                <td class="text--center bordered">{$variante.price|currency}</td>
                <td class="text--center bordered">{$variante.attr12}</td>
                <td class="text--center bordered"></td>
            </tr>
        {/foreach}
    </table>
{/if}
    
    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Publikationen" namespace="plugins/frontend/SabPDFToolv5"}{/s}
            </td>
        </tr>
        <tr>
            <td class="text">
                {$sArticle.attr9}
            </td>
        </tr>
    </table>

<pagebreak />

    <table cellspacing="0" style="border: 0;">
        <tr>
            <td class="headline">
                {s name="Ihre_Bestellinformationen" namespace="plugins/frontend/SabPDFToolv5"}Ihre Bestellinformationen{/s}
            </td>
        </tr>
    </table>
    <table cellspacing="0" width="700px;" style="border: 0 none">
        <tr>
            <td class="text--left bordered" width="50%">Ihre Kundennummer</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
        <tr>
            <td class="text--left bordered" width="50%">Firma / Praxis</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
        <tr>
            <td class="text--left bordered" width="50%">Ansprechpartner</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
        <tr>
            <td class="text--left bordered" width="50%">Straße</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
        <tr>
            <td class="text--left bordered" width="50%">PLZ / Ort</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
        <tr>
            <td class="text--left bordered" width="50%">Telefon / Fax</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
        <tr>
            <td class="text--left bordered" width="50%">Mail</td>
            <td class="text--left bordered" width="50%"></td>
        </tr>
    </table>

    <table cellspacing="0" width="700px;" style="border: 0 none; margin-top: 100px;">
        <tr>
            <td class="text--left" width="40%"><hr /></td>
            <td class="text--left" width="20%"></td>
            <td class="text--left" width="40%"><hr /></td>
        </tr>
        <tr>
            <td class="text--left" width="40%">Stempel</td>
            <td class="text--left" width="20%"></td>
            <td class="text--left" width="40%">Datum / Unterschrift</td>
        </tr>
    </table>

    <table cellspacing="0" width="700px;" style="border: 0 none; margin-top: 100px;">
        <tr>
            <td class="text--left">Anmerkungen: </td>
        </tr>
    </table>



</div>
</body>
